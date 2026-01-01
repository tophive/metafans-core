class OurTeamWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wrapper: '.gsap-team-marquee-wrapper',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wrapper: this.$element.find(selectors.wrapper),
        };
    }

    bindEvents() {
        this.run();
    }

    onDestroy() {
        // Kill the GSAP animation loop when the widget is destroyed
        if (this.loop) {
            this.loop.kill();
        }
        // Also kill any other tweens associated with the widget's elements
        gsap.killTweensOf(this.elements.$wrapper.find('.marquee-item'));
    }

    run() {
        const settings = this.elements.$wrapper.data('settings');
        const marqueeWrapper = this.elements.$wrapper.find('.marquee-wrapper')[0];
        const marquee = this.elements.$wrapper.find('.marquee')[0];
        let items = gsap.utils.toArray(this.elements.$wrapper.find('.marquee-item'));
        const btnPrev = this.elements.$wrapper.find('.arrow-prev')[0];
        const btnNext = this.elements.$wrapper.find('.arrow-next')[0];

        if (!items.length || !marqueeWrapper || !marquee) return;

        const containerWidth = marqueeWrapper.offsetWidth;
        const totalContentWidth = marquee.scrollWidth;

        // The marquee effect is only needed if the content overflows the container.
        const isMarqueeRequired = totalContentWidth > containerWidth;

        if (!isMarqueeRequired) {
            // If not required, hide arrows and stop.
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
            return;
        }

        // --- If we reach here, the marquee effect is required. ---

        const originalItemCount = items.length;

        // Clone items to create a seamless loop.
        items.forEach(item => {
            const clone = item.cloneNode(true);
            marquee.appendChild(clone);
        });
        const allItems = gsap.utils.toArray(marquee.querySelectorAll('.marquee-item'));

        // Create the main animation loop. It starts paused if auto-scroll is off.
        // Store the loop on the class instance so we can access it in onDestroy
        this.loop = gsap.to(allItems, {
            xPercent: -100 * originalItemCount,
            ease: "none",
            duration: settings.speed,
            repeat: -1,
            paused: !settings.enable_scroll, // Start paused if auto-scroll is off
            modifiers: {
                xPercent: gsap.utils.unitize(x => parseFloat(x) % (100 * originalItemCount))
            }
        });

        if (settings.enable_scroll) {
            allItems.forEach(item => {
                item.addEventListener('mouseenter', () => gsap.to(this.loop, { timeScale: 0.1, duration: 0.5 }));
                item.addEventListener('mouseleave', () => gsap.to(this.loop, { timeScale: 1, duration: 0.5 }));
            });
        }

        // Add arrow functionality if they are enabled in settings.
        if (settings.show_arrows && btnNext && btnPrev) {
            const step = 1 / originalItemCount;
            const move = (direction) => {
                // Create a new tween for the progress to avoid conflicts if the main loop is running.
                gsap.to(this.loop, { progress: `${direction}=${step}`, duration: 0.5, ease: 'power2.out' });
            };
            btnNext.addEventListener('click', () => move('+'));
            btnPrev.addEventListener('click', () => move('-'));
        } else {
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
        }
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(OurTeamWidgetHandler, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/gsap_team_marquee.default', addHandler);
});