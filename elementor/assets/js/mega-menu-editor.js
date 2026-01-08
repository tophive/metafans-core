//MAKE A GLOBAL FILE FOR NESTEDELEMENTBASE BUILDER

elementorCommon.elements.$window.on(
  "elementor/nested-element-type-loaded",
  async () => {
    class Mega_menu extends elementor.modules.elements.types.NestedElementBase {
      getType() {
        return "mega_menu";
      }
    }

    elementor.elementsManager.registerElementType(new Mega_menu());
  },
);

elementorCommon.elements.$window.on(
  "elementor/nested-element-type-loaded",
  async () => {
    class Offcanvas extends elementor.modules.elements.types.NestedElementBase {
      getType() {
        return "offcanvas";
      }
    }

    elementor.elementsManager.registerElementType(new Offcanvas());
  },
);

elementorCommon.elements.$window.on(
  "elementor/nested-element-type-loaded",
  async () => {
    class Horizontal_Slider extends elementor.modules.elements.types
      .NestedElementBase {
      getType() {
        return "horizontal_slider";
      }
    }

    elementor.elementsManager.registerElementType(new Horizontal_Slider());
  },
);
