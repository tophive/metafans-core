console.log(
  "----------------------Mega menu widget script loaded----------------------",
);

function top_label_menu_single_markup({ url, title, repeter_item }) {
  let markup = ` 
     <a 
       href="${url}" 
       class="rootLink ${repeter_item["hasDropdown"] ? "hasDropdown" : ""}" 
       data-dropdown=${repeter_item._id}>
       ${title}
     </a>`;

  return markup;
}

function add_event_listener_on_menu(menu_el) {
  if (!menu_el) return null;

  menu_el.addEventListener("click", (e) => {
    e.preventDefault();

    //add active data-attribute to parent div
    // let parent_el = menu_el.parentElement.parentElement;
    // parent_el.setAttribute("data-active-index", index);
  });
}

function render_top_label_menus({ dom_el, top_label_menus = [] }) {
  if (!dom_el || !top_label_menus.length) return;

  //gen markup
  let menus_markup = top_label_menus
    .map((menu, index) => {
      return top_label_menu_single_markup({
        url: menu.url,
        title: menu.title,
        repeter_item: menu.item,
      });
    })
    .join(" ");

  //clear previous markup
  dom_el.innerHTML = "";

  //insert new html
  dom_el.insertAdjacentHTML("afterbegin", menus_markup);

  //block event listener
  let menus = dom_el.querySelectorAll("a");
  menus.forEach((menu) => add_event_listener_on_menu(menu));
}

function wrap_drop_box_content({ dom_el, repeter_items }) {
  if (!dom_el || !repeter_items.length) return;

  //select all container element
  let container_els = dom_el.querySelectorAll(
    ".dropdownContainer > [data-element_type='container']",
  );

  //create same amount of section as container_els
  let sections = [];
  container_els.forEach((con, index) => {
    let section = document.createElement("div");
    section.classList.add("dropdownSection");
    let content = document.createElement("div");
    content.classList.add("dropdownContent");
    section.appendChild(content);

    if (repeter_items[index]["item"]["hasDropdown"] === "yes") {
      section.setAttribute(
        "data-dropdown",
        repeter_items[index]["item"]["_id"],
      );
    }
    content.appendChild(con);
    dom_el.appendChild(section);
    // sections.push(section);
  });

  //put container element inside sections
}

function menu_data_refector({ pages, repeter_items }) {
  let menus = [];
  repeter_items.forEach((item, index) => {
    if (pages[item.page_id]) {
      menus.push({
        url: pages[item.page_id].permalink,
        title: pages[item.page_id].title,
        item,
      });
    }
  });

  return menus;
}

function collect_dom_element(id = null) {
  let menu_con_el = document.getElementById(id);
  if (!menu_con_el) return null;

  menu_con_el = menu_con_el.querySelector(".globalNav");
  const top_menu_con_el = menu_con_el.querySelector(".navSection.primary");
  const sub_menu_con_el = menu_con_el.querySelector(".dropdownContainer");

  if (!top_menu_con_el || !sub_menu_con_el) return null;
  return {
    menu_con_el,
    top_menu_con_el,
    sub_menu_con_el,
  };
}

let time_out_id = null;

function active_mega_menu({ dom_el, repeter_items }) {
  for (let index = 0; index < repeter_items.length; index++) {
    const element = repeter_items[index];
    if (element["item"]["active"] === "yes") {
      //active this mega menu
      let active_root_link = dom_el.querySelector(
        `.navRoot [data-dropdown="${element["item"]["_id"]}"]`,
      );
      let root_links = dom_el.querySelectorAll(`.navRoot .hasDropdown`);
      let active_dropdown = dom_el.querySelector(
        `.dropdownContainer  [data-dropdown="${element["item"]["_id"]}"]`,
      );
      let dropdown_container = dom_el.querySelector(`.dropdownContainer`);
      let dropdown_arrow = dom_el.querySelector(`.dropdownArrow`);
      let dropdown_bg = dom_el.querySelector(`.dropdownBackground`);
      let dropdown_sections = dom_el.querySelectorAll(
        `.dropdownContainer .dropdownSection`,
      );

      //add class to root container (.globalNav)
      dom_el.classList.add("overlayActive");
      dom_el.classList.add("dropdownActive");

      //remove previous active class form rootLinks
      root_links.forEach((link) => {
        link.classList.remove("active");
      });
      //add active to current active menu
      active_root_link.classList.add("active");

      let r, i, s, o;
      //remove active from dropdown sections and add active to current dropdown
      dropdown_sections.forEach((sec) => {
        sec.classList.remove("active", "left", "right");
        if (active_dropdown == sec) {
          if (time_out_id) clearTimeout(time_out_id);
          time_out_id = window.setTimeout(() => {
            sec.classList.add("active");
            r = "right";
            i = element["item"]["width"];
            s = element["item"]["height"];
            o = active_root_link.content;
            let u = 520,
              a = 400,
              f = i / u,
              l = s / a,
              c = active_root_link.getBoundingClientRect(),
              h = c.left + c.width / 2 - i / 2;
            h = Math.round(Math.max(h, 10));
            p = Math.round(c.left + c.width / 2);

            //
            let droot = dom_el.querySelector(".dropdownRoot");
            let drr = droot.getBoundingClientRect();
            h = c.left - drr.left + c.width / 2 - i / 2;
            p = c.left - drr.left + c.width / 2;
            //

            // this.enableTransitionTimeout = setTimeout(function () {}, 50);
            dom_el.classList.remove("noDropdownTransition");

            dropdown_bg.style.transform =
              "translateX(" + h + "px) scaleX(" + f + ") scaleY(" + l + ")";
            dropdown_container.style.transform = "translateX(" + h + "px)";
            dropdown_container.style.width = i + "px";
            dropdown_container.style.height = s + "px";
            dropdown_arrow.style.transform =
              "translateX(" + p + "px) rotate(45deg)";
          }, 500);
        } else {
          sec.classList.add("left");
        }
      });

      break;
    }
  }
}

function listen_mega_menu_event(e) {
  if (e.data.type !== "mega_menu_event") return null;
  const data = e.data.data;

  if (!data.custom_data?.id) return;

  let dom_els = collect_dom_element(data.custom_data.id);
  if (!dom_els) return;

  let { top_menu_con_el, sub_menu_con_el, menu_con_el } = dom_els;

  const top_menus = menu_data_refector({
    pages: data.custom_data.pages,
    repeter_items: data.widget_setting.top_menu,
  });

  //render top menus
  render_top_label_menus({
    dom_el: top_menu_con_el,
    top_label_menus: top_menus,
  });

  //wrap dropbox container for megamenu to work
  wrap_drop_box_content({ dom_el: sub_menu_con_el, repeter_items: top_menus });

  //active a mega menu if active
  active_mega_menu({ dom_el: menu_con_el, repeter_items: top_menus });
}

window.addEventListener("message", listen_mega_menu_event);

class MegaMenuHandler extends elementorModules.frontend.handlers.Base {
  onInit() {
    super.onInit();
    this.container = this.$element.find('.globalNav').get(0);
    if (!this.container) return;

    this.root = this.container.querySelector(".navRoot");
    this.dropdownBackground = this.container.querySelector(".dropdownBackground");
    this.dropdownContainer = this.container.querySelector(".dropdownContainer");
    this.dropdownArrow = this.container.querySelector(".dropdownArrow");
    this.dropdownRoots = Array.from(this.container.querySelectorAll(".hasDropdown"));
    this.dropdownSections = Array.from(this.container.querySelectorAll(".dropdownSection")).map(el => ({
        el: el,
        name: el.getAttribute("data-dropdown"),
        content: el.querySelector(".dropdownContent"),
    }));

    this.activeDropdown = null;
    this.closeDropdownTimeout = null;
    this.enableTransitionTimeout = null;
    this.eventListeners = [];

    this.init();
  }

  onDestroy() {
    super.onDestroy();
    this.eventListeners.forEach(({ element, type, handler }) => {
        element.removeEventListener(type, handler);
    });
    this.eventListeners = [];
    clearTimeout(this.closeDropdownTimeout);
    clearTimeout(this.enableTransitionTimeout);
  }

  init() {
    this.container.classList.add("noDropdownTransition");

    const pointerEvents = window.PointerEvent ? 
      { end: "pointerup", enter: "pointerenter", leave: "pointerleave" } : 
      { end: "touchend", enter: "mouseenter", leave: "mouseleave" };

    this.dropdownRoots.forEach((el) => {
      this.addTrackedListener(el, pointerEvents.end, (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.toggleDropdown(el);
      });
      this.addTrackedListener(el, pointerEvents.enter, (e) => {
        if (e.pointerType === "touch") return;
        this.stopCloseTimeout();
        this.openDropdown(el);
      });
      this.addTrackedListener(el, pointerEvents.leave, (e) => {
        if (e.pointerType === "touch") return;
        this.startCloseTimeout();
      });
    });

    this.addTrackedListener(this.dropdownContainer, pointerEvents.end, (e) => e.stopPropagation());
    this.addTrackedListener(this.dropdownContainer, pointerEvents.enter, (e) => {
      if (e.pointerType === "touch") return;
      this.stopCloseTimeout();
    });
    this.addTrackedListener(this.dropdownContainer, pointerEvents.leave, (e) => {
      if (e.pointerType === "touch") return;
      this.startCloseTimeout();
    });

    // Set dropdownRoot top position
    const link = this.container.querySelector(".navRoot");
    const dropdownRoot = this.container.querySelector(".dropdownRoot");
    if (link && dropdownRoot) {
      dropdownRoot.style.top = `${link.clientHeight}px`;
    }
  }

  addTrackedListener(element, type, handler) {
    element.addEventListener(type, handler);
    this.eventListeners.push({ element, type, handler });
  }

  openDropdown(triggerElement) {
    const self = this;

    function measureElement(el) {
      const prevPosition = el.style.position;
      const prevVisibility = el.style.visibility;
      const prevDisplay = el.style.display;

      el.style.position = "relative";
      el.style.visibility = "hidden";
      el.style.display = "block";

      const rect = el.getBoundingClientRect();
      const width = rect.width;
      const height = rect.height;

      el.style.position = prevPosition;
      el.style.visibility = prevVisibility;
      el.style.display = prevDisplay;

      return { width, height };
    }

    if (this.activeDropdown === triggerElement) return;

    this.container.classList.add("overlayActive", "dropdownActive");
    this.activeDropdown = triggerElement;

    this.dropdownRoots.forEach(rootItem => rootItem.classList.remove("active"));
    triggerElement.classList.add("active");

    const targetDropdownName = triggerElement.getAttribute("data-dropdown");
    let alignClass = "left";
    let dropdownWidth, dropdownHeight;

    this.dropdownSections.forEach(section => {
      section.el.classList.remove("active", "left", "right");
      if (section.name === targetDropdownName) {
        section.el.classList.add("active");
        alignClass = "right";
        const { width, height } = measureElement(section.content);
        dropdownWidth = width;
        dropdownHeight = height;
      } else {
        section.el.classList.add(alignClass);
      }
    });

    const baseWidth = 520;
    const baseHeight = 400;
    const scaleX = dropdownWidth / baseWidth;
    const scaleY = dropdownHeight / baseHeight;

    const triggerRect = triggerElement.getBoundingClientRect();
    const dropdownRoot = this.container.querySelector(".dropdownRoot");
    const rootRect = dropdownRoot.getBoundingClientRect();

    let dropdownLeft = triggerRect.left - rootRect.left + triggerRect.width / 2 - dropdownWidth / 2;
    let arrowLeft = triggerRect.left - rootRect.left + triggerRect.width / 2;

    clearTimeout(this.disableTransitionTimeout);
    this.enableTransitionTimeout = setTimeout(() => {
      self.container.classList.remove("noDropdownTransition");
    }, 50);

    this.dropdownBackground.style.transform = `translateX(${dropdownLeft}px) scaleX(${scaleX}) scaleY(${scaleY})`;
    this.dropdownContainer.style.transform = `translateX(${dropdownLeft}px)`;
    this.dropdownContainer.style.width = dropdownWidth + "px";
    this.dropdownContainer.style.height = dropdownHeight + "px";
    this.dropdownArrow.style.transform = `translateX(${arrowLeft}px) rotate(45deg)`;
  }

  closeDropdown() {
    if (!this.activeDropdown) return;
    this.dropdownRoots.forEach(el => el.classList.remove("active"));
    clearTimeout(this.enableTransitionTimeout);

    this.disableTransitionTimeout = setTimeout(() => {
      this.container.classList.add("noDropdownTransition");
    }, 50);

    this.container.classList.remove("overlayActive", "dropdownActive");
    this.activeDropdown = undefined;
  }

  toggleDropdown(triggerElement) {
    this.activeDropdown === triggerElement ? this.closeDropdown() : this.openDropdown(triggerElement);
  }

  startCloseTimeout() {
    this.closeDropdownTimeout = setTimeout(() => this.closeDropdown(), 50);
  }

  stopCloseTimeout() {
    clearTimeout(this.closeDropdownTimeout);
  }
}


jQuery(window).on('elementor/frontend/init', () => {
  elementorFrontend.hooks.addAction('frontend/element_ready/mega-menu.default', ($scope) => {
      elementorFrontend.elementsHandler.addHandler(MegaMenuHandler, { $element: $scope });
  });

  // Mobile Menu Logic
  const self = this;
  const main_con = document.getElementById("mega-menu-mobile");
  const sidebar = document.getElementById("mobile-content-sidebar");
  if (!sidebar) return;

  document.body.appendChild(sidebar);

  //all links
  const links = sidebar.querySelectorAll(".mobile-menu-links a");
  const contents = sidebar.querySelectorAll(".mobile-menu-content .con");
  const back = sidebar.querySelector(".back");
  const close = sidebar.querySelector(".close");
  const open = main_con.querySelector(".open");

  links.forEach((link) => {
    if (!link.classList.contains("hasDropdown")) return;

    link.addEventListener("click", (e) => {
      e.preventDefault();
      let dropdownId = link.getAttribute("data-dropdown");

      links.forEach((_link) => _link.classList.remove("active"));
      link.classList.add("active");

      let content = sidebar.querySelector(
        `.mobile-menu-content [data-dropdown="${dropdownId}"]`,
      );

      contents.forEach((con) => {
        con.classList.remove("active");
      });

      content.classList.add("active");

      sidebar.classList.add("run_transition");
    });
  });

  back.addEventListener("click", function (e) {
    sidebar.classList.remove("run_transition");
  });

  close.addEventListener("click", function (e) {
    sidebar.classList.remove("open", "fade_in");
    sidebar.classList.add("fade_way");

    window.setTimeout(() => {
      sidebar.classList.add("close");
    }, 250);
  });

  open.addEventListener("click", () => {
    sidebar.classList.remove("close", "fade_way");
    sidebar.classList.add("open");
    window.setTimeout(() => {
      sidebar.classList.add("fade_in");
    }, 50);
  });
})
