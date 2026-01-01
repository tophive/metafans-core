class TophiveMarqueeWidgetHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
      super.onInit();
      this.timeline = null;
      this.scrollTrigger = null;
      this.resizeHandler = null;
      this.mouseEnterHandler = null;
      this.mouseLeaveHandler = null;
      this.initMarquee();
    }

    onDestroy() {
        super.onDestroy();
        if (this.timeline) {
            this.timeline.kill();
        }
        if (this.scrollTrigger) {
            this.scrollTrigger.kill();
        }
        if (this.resizeHandler) {
            window.removeEventListener('resize', this.resizeHandler);
        }
        if (this.mouseEnterHandler && this.mouseLeaveHandler) {
            const items = this.$element[0].querySelectorAll('.marquee-item');
            items.forEach(item => {
                item.removeEventListener("mouseenter", this.mouseEnterHandler);
                item.removeEventListener("mouseleave", this.mouseLeaveHandler);
            });
        }
    }

    initScrubMarquee(wrapper) {
        const track = wrapper.querySelector('.marquee-text');
        const direction = wrapper.dataset.direction || 'left';
        const isVertical = direction === 'top' || direction === 'bottom';
        const isReverse = direction === 'right' || direction === 'bottom';
    
        if (!track || !wrapper) return;
    
        this.resizeHandler = () => {
            const wrapperRect = wrapper.getBoundingClientRect();
            const trackRect = track.getBoundingClientRect();
    
            const trackSize = isVertical ? trackRect.height : trackRect.width;
            const wrapperSize = isVertical ? wrapperRect.height : wrapperRect.width;
    
            // Correct edge alignment
            const from = isReverse ? (wrapperSize - trackSize) : 0;
            const to = isReverse ? 0 : (wrapperSize - trackSize);
    
            if (this.scrollTrigger) {
                this.scrollTrigger.kill();
            }
    
            // Create a unique ID and assign to data attribute (optional but useful)
            const scrollId = 'scrub-' + Math.random().toString(36).substr(2, 9);
            track.dataset.scrollId = scrollId;
    
            gsap.fromTo(track, {
                [isVertical ? 'y' : 'x']: from
            }, {
                [isVertical ? 'y' : 'x']: to,
                ease: 'none',
                scrollTrigger: {
                    id: scrollId,
                    trigger: wrapper,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: 1.2,
                    markers: false
                }
            });
            this.scrollTrigger = ScrollTrigger.getById(scrollId);
        };
    
        // Ensure layout is ready
        setTimeout(this.resizeHandler, 100);
        window.addEventListener('resize', this.resizeHandler);
    }
    
      
      
  
    initMarquee() {
        const wrapper = this.$element[0].querySelector('.tophive-marquee-wrapper');

        const isScrub = wrapper.dataset.scrub === 'yes';

        if (isScrub) return this.initScrubMarquee(wrapper);

        const items = wrapper.querySelectorAll('.marquee-item');
        const speed = parseFloat(wrapper.dataset.speed) || 1;
        const direction = wrapper.dataset.direction || 'left';
        const pauseOnHover = wrapper.dataset.pause === 'yes';
      
        let isVertical = direction === 'top' || direction === 'bottom';
        let reversed = direction === 'right' || direction === 'bottom';
      
        let loopFn = isVertical ? this.verticalLoop : this.horizontalLoop;
      
        this.timeline = loopFn.call(this, items, {
          repeat: -1,
          speed: speed,
          draggable: true,
          reversed: reversed,
          paddingRight: parseFloat(gsap.getProperty(items[0], isVertical ? "marginBottom" : "marginRight", "px")),
        });
      
        // Pause on hover
        if (pauseOnHover) {
            this.mouseEnterHandler = () => gsap.to(this.timeline, { timeScale: 0, overwrite: true });
            this.mouseLeaveHandler = () => gsap.to(this.timeline, { timeScale: reversed ? -1 : 1, overwrite: true });
            items.forEach(item => {
                item.addEventListener("mouseenter", this.mouseEnterHandler);
                item.addEventListener("mouseleave", this.mouseLeaveHandler);
            });
        }
      
        // Optional: Scroll toggle (still horizontal scroll)
        let currentScroll = 0;
        let scrollDirection = 1;
        window.addEventListener("scroll", () => {
          let newDirection = (window.pageYOffset > currentScroll) ? 1 : -1;
          if (newDirection !== scrollDirection) {
            gsap.to(this.timeline, { timeScale: newDirection, overwrite: true });
            scrollDirection = newDirection;
          }
          currentScroll = window.pageYOffset;
        });
      }
      

    horizontalLoop(items, config) {
        items = gsap.utils.toArray(items);
        config = config || {};
        let tl = gsap.timeline({repeat: config.repeat, paused: config.paused, defaults: {ease: "none"}, onReverseComplete: () => tl.totalTime(tl.rawTime() + tl.duration() * 100)}),
            length = items.length,
            startX = items[0].offsetLeft,
            times = [],
            widths = [],
            xPercents = [],
            curIndex = 0,
            pixelsPerSecond = (config.speed || 1) * 100,
            snap = config.snap === false ? v => v : gsap.utils.snap(config.snap || 1), // some browsers shift by a pixel to accommodate flex layouts, so for example if width is 20% the first element's width might be 242px, and the next 243px, alternating back and forth. So we snap to 5 percentage points to make things look more natural
            populateWidths = () => items.forEach((el, i) => {
                widths[i] = parseFloat(gsap.getProperty(el, "width", "px"));
                xPercents[i] = snap(parseFloat(gsap.getProperty(el, "x", "px")) / widths[i] * 100 + gsap.getProperty(el, "xPercent"));
            }),
            getTotalWidth = () => items[length-1].offsetLeft + xPercents[length-1] / 100 * widths[length-1] - startX + items[length-1].offsetWidth * gsap.getProperty(items[length-1], "scaleX") + (parseFloat(config.paddingRight) || 0),
                totalWidth, curX, distanceToStart, distanceToLoop, item, i;
            populateWidths();
            gsap.set(items, { // convert "x" to "xPercent" to make things responsive, and populate the widths/xPercents Arrays to make lookups faster.
                xPercent: i => xPercents[i]
            });
            gsap.set(items, {x: 0});
            totalWidth = getTotalWidth();
            for (i = 0; i < length; i++) {
                item = items[i];
                curX = xPercents[i] / 100 * widths[i];
                distanceToStart = item.offsetLeft + curX - startX;
                distanceToLoop = distanceToStart + widths[i] * gsap.getProperty(item, "scaleX");
                tl.to(item, {xPercent: snap((curX - distanceToLoop) / widths[i] * 100), duration: distanceToLoop / pixelsPerSecond}, 0)
                .fromTo(item, {xPercent: snap((curX - distanceToLoop + totalWidth) / widths[i] * 100)}, {xPercent: xPercents[i], duration: (curX - distanceToLoop + totalWidth - curX) / pixelsPerSecond, immediateRender: false}, distanceToLoop / pixelsPerSecond)
                .add("label" + i, distanceToStart / pixelsPerSecond);
                times[i] = distanceToStart / pixelsPerSecond;
            }
            function toIndex(index, vars) {
                vars = vars || {};
                (Math.abs(index - curIndex) > length / 2) && (index += index > curIndex ? -length : length); // always go in the shortest direction
                let newIndex = gsap.utils.wrap(0, length, index),
                    time = times[newIndex];
                if (time > tl.time() !== index > curIndex) { // if we're wrapping the timeline's playhead, make the proper adjustments
                    vars.modifiers = {time: gsap.utils.wrap(0, tl.duration())};
                    time += tl.duration() * (index > curIndex ? 1 : -1);
                }
                curIndex = newIndex;
                vars.overwrite = true;
                return tl.tweenTo(time, vars);
            }
        tl.next = vars => toIndex(curIndex+1, vars);
        tl.previous = vars => toIndex(curIndex-1, vars);
        tl.current = () => curIndex;
        tl.toIndex = (index, vars) => toIndex(index, vars);
        tl.updateIndex = () => curIndex = Math.round(tl.progress() * (items.length - 1));
        tl.times = times;
        tl.progress(1, true).progress(0, true); // pre-render for performance
        if (config.reversed) {
            tl.vars.onReverseComplete();
            tl.reverse();
        }
      if (config.draggable && typeof(Draggable) === "function") {
        let proxy = document.createElement("div"),
            wrap = gsap.utils.wrap(0, 1),
            ratio, startProgress, draggable, dragSnap, roundFactor,
            align = () => tl.progress(wrap(startProgress + (draggable.startX - draggable.x) * ratio)),
            syncIndex = () => tl.updateIndex();
        typeof(InertiaPlugin) === "undefined" && console.warn("InertiaPlugin required for momentum-based scrolling and snapping. https://greensock.com/club");
            draggable = Draggable.create(proxy, {
                trigger: items[0].parentNode,
                type: "x",
                onPress() {
                    startProgress = tl.progress();
                    tl.progress(0);
                    populateWidths();
                    totalWidth = getTotalWidth();
                    ratio = 1 / totalWidth;
                    dragSnap = totalWidth / items.length;
                    roundFactor = Math.pow(10, ((dragSnap + "").split(".")[1] || "").length);
                    tl.progress(startProgress);
                },
                onDrag: align,
                onThrowUpdate: align,
                inertia: true,
                snap: value => {
                    let n = Math.round(parseFloat(value) / dragSnap) * dragSnap * roundFactor;
                    return (n - n % 1) / roundFactor;
                },
                onRelease: syncIndex,
                onThrowComplete: () => gsap.set(proxy, {x: 0}) && syncIndex()
            })[0];
      }
      
        return tl;
    }
    verticalLoop(items, config) {
        items = gsap.utils.toArray(items);
        config = config || {};
      
        let tl = gsap.timeline({
          repeat: config.repeat,
          paused: config.paused,
          defaults: { ease: "none" },
          onReverseComplete: () => tl.totalTime(tl.rawTime() + tl.duration() * 100),
        });
      
        let length = items.length,
            startY = items[0].offsetTop,
            times = [],
            heights = [],
            yPercents = [],
            curIndex = 0,
            pixelsPerSecond = (config.speed || 1) * 100,
            snap = config.snap === false ? v => v : gsap.utils.snap(config.snap || 1),
            populateHeights = () => items.forEach((el, i) => {
              heights[i] = parseFloat(gsap.getProperty(el, "height", "px"));
              yPercents[i] = snap(parseFloat(gsap.getProperty(el, "y", "px")) / heights[i] * 100 + gsap.getProperty(el, "yPercent"));
            }),
            getTotalHeight = () => {
              let last = items[length - 1];
              return last.offsetTop + yPercents[length - 1] / 100 * heights[length - 1] - startY + last.offsetHeight * gsap.getProperty(last, "scaleY") + (parseFloat(config.paddingRight) || 0);
            };
      
        populateHeights();
        gsap.set(items, { yPercent: i => yPercents[i], y: 0 });
      
        let totalHeight = getTotalHeight();
      
        for (let i = 0; i < length; i++) {
          let item = items[i];
          let curY = yPercents[i] / 100 * heights[i];
          let distanceToStart = item.offsetTop + curY - startY;
          let distanceToLoop = distanceToStart + heights[i] * gsap.getProperty(item, "scaleY");
      
          tl.to(item, {
            yPercent: snap((curY - distanceToLoop) / heights[i] * 100),
            duration: distanceToLoop / pixelsPerSecond
          }, 0)
          .fromTo(item, {
            yPercent: snap((curY - distanceToLoop + totalHeight) / heights[i] * 100)
          }, {
            yPercent: yPercents[i],
            duration: (curY - distanceToLoop + totalHeight - curY) / pixelsPerSecond,
            immediateRender: false
          }, distanceToLoop / pixelsPerSecond)
          .add("label" + i, distanceToStart / pixelsPerSecond);
      
          times[i] = distanceToStart / pixelsPerSecond;
        }
      
        tl.progress(1, true).progress(0, true);
      
        if (config.reversed) {
          tl.vars.onReverseComplete();
          tl.reverse();
        }
      
        // ✅ Add Draggable Y Support
        if (config.draggable && typeof(Draggable) === "function") {
          let proxy = document.createElement("div"),
              wrap = gsap.utils.wrap(0, 1),
              ratio, startProgress, draggable, dragSnap, roundFactor,
              align = () => tl.progress(wrap(startProgress + (draggable.startY - draggable.y) * ratio)),
              syncIndex = () => tl.updateIndex();
      
          draggable = Draggable.create(proxy, {
            trigger: items[0].parentNode,
            type: "y",
            onPress() {
              startProgress = tl.progress();
              tl.progress(0);
              populateHeights();
              totalHeight = getTotalHeight();
              ratio = 1 / totalHeight;
              dragSnap = totalHeight / items.length;
              roundFactor = Math.pow(10, ((dragSnap + "").split(".")[1] || "").length);
              tl.progress(startProgress);
            },
            onDrag: align,
            onThrowUpdate: align,
            inertia: true,
            snap: value => {
              let n = Math.round(parseFloat(value) / dragSnap) * dragSnap * roundFactor;
              return (n - n % 1) / roundFactor;
            },
            onRelease: syncIndex,
            onThrowComplete: () => gsap.set(proxy, { y: 0 }) && syncIndex()
          })[0];
        }
      
        return tl;
      }
      
      
  }
  
jQuery(window).on('elementor/frontend/init', () => {
    const addHandler = ($scope) => {
        elementorFrontend.elementsHandler.addHandler(TophiveMarqueeWidgetHandler, { $element: $scope });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/tophive-marquee-text.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/tophive-marquee-image.default', addHandler);
});