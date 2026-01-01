class TestimonialCarouselHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.isInitialized = false;
        this.settings = {};
        this.$wrapper = this.$element.find('.testimonial-carousel');
        this.$slides = null;
        this.$dotsContainer = null;
        this.draggable = null;
        this.autoplayTimer = null;
        this.resizeTimer = null;

        this.current = 0;
        this.slideWidth = 0;
        this.wrapWidth = 0;
        this.centerOffset = 0;
        this.animDuration = 0.6;

        if (this.isInitialized) return;

        this.settings = this.$element.find('.testimonial-carousel-wrapper').data('settings') || {};
        if (!this.$wrapper.length) return;

        this.$slides = this.$wrapper.find('.testimonial-item');
        if (this.$slides.length === 0) return;

        this.slideWidth = this.$slides.first().outerWidth(true);

        this._createDots();
        this._applyCardStyles();
        this._setupDraggable();
        this._bindEvents();
        
        this.goTo(0, false);
        this.startAutoplay();

        this.isInitialized = true;
    }

    onDestroy() {
        super.onDestroy();
        if (!this.isInitialized) return;
        this.stopAutoplay();
        clearTimeout(this.resizeTimer);

        if (this.draggable && this.draggable.length > 0) this.draggable[0].kill();
        if (this.$dotsContainer) this.$dotsContainer.remove();
        
        this.$wrapper.find('.clone').remove();
        gsap.killTweensOf(this.$wrapper);

        jQuery(window).off(`resize.${this.$element.data('id')}`);
        this.isInitialized = false;
    }

    _createDots() {
        if (this.settings.navigation === 'dots') {
            this.$dotsContainer = jQuery('<div class="carousel-dots"></div>');
            this.$slides.each((i) => {
                const $dot = jQuery('<div class="dot"></div>').on('click', () => this.goTo(i));
                this.$dotsContainer.append($dot);
            });
            this.$element.find('.testimonial-carousel-wrapper').append(this.$dotsContainer);
        }
    }

    _applyCardStyles() {
        this.$slides.each((i, slide) => {
            const $slide = jQuery(slide);
            if (this.settings.card_background_type === 'solid' && this.settings.card_solid_color) {
                $slide.css('background', this.settings.card_solid_color);
            } else if (this.settings.card_background_type === 'glass' && this.settings.card_glass_blur?.size) {
                $slide.css({
                    'background': 'rgba(255,255,255,0.06)',
                    'backdrop-filter': `blur(${this.settings.card_glass_blur.size}px)`,
                    '-webkit-backdrop-filter': `blur(${this.settings.card_glass_blur.size}px)`
                });
            }
        });
    }

    _setupDraggable() {
        if (typeof Draggable === 'undefined') return;

        const numSlides = this.$slides.length;
        this.wrapWidth = numSlides * this.slideWidth;

        if (this.settings.start_position === 'center') {
            this.centerOffset = (this.$element.find('.testimonial-carousel-wrapper').outerWidth() / 2) - (this.slideWidth / 2);
        }

        this.$slides.clone().addClass('clone').appendTo(this.$wrapper);

        const wrap = gsap.utils.wrap(this.centerOffset - this.wrapWidth, this.centerOffset);

        gsap.set(this.$wrapper, { x: this.centerOffset });

        this.draggable = Draggable.create(this.$wrapper, {
            type: "x",
            inertia: true,
            edgeResistance: 0.9,
            onDrag: function() {
                gsap.set(this.target, { x: wrap(this.x) });
            },
            onThrowUpdate: function() {
                gsap.set(this.target, { x: wrap(this.x) });
            },
            onDragStart: () => this.stopAutoplay(),
            onThrowComplete: () => this.startAutoplay(),
            snap: {
                x: (endValue) => {
                    const wrappedEnd = wrap(endValue);
                    const closestSlideIndex = -Math.round((wrappedEnd - this.centerOffset) / this.slideWidth);
                    this.goTo(closestSlideIndex, true);
                    return -(this.current * this.slideWidth) + this.centerOffset;
                }
            }
        });
    }

    _bindEvents() {
        const widgetId = this.$element.data('id');
        const uniqueId = `testimonial-carousel-${widgetId}`;
        
        this.$element.on('click', `#${uniqueId}-prev`, () => this.prev());
        this.$element.on('click', `#${uniqueId}-next`, () => this.next());

        if (this.settings.pause_on_hover === 'yes') {
            this.$wrapper.on('mouseenter', () => this.stopAutoplay());
            this.$wrapper.on('mouseleave', () => this.startAutoplay());
        }

        jQuery(window).on(`resize.${widgetId}`, () => this._onResize());
    }

    goTo(index, animate = true) {
        const numSlides = this.$slides.length;
        if (this.settings.loop === 'yes') {
            this.current = (index % numSlides + numSlides) % numSlides;
        } else {
            this.current = Math.max(0, Math.min(index, numSlides - 1));
        }

        const targetX = -(this.current * this.slideWidth) + this.centerOffset;
        
        if (animate) {
            gsap.to(this.$wrapper, { x: targetX, duration: this.animDuration, ease: 'power3.inOut' });
        } else {
            gsap.set(this.$wrapper, { x: targetX });
        }
        this._updateActiveState();
        this.restartAutoplay();
    }
    
    _updateActiveState() {
        const allSlides = this.$wrapper.find('.testimonial-item');
        allSlides.each((i, slide) => {
            const isActive = (i % this.$slides.length) === this.current;
            gsap.to(slide, {
                scale: isActive ? 1 : 0.85,
                opacity: isActive ? 1 : 0.7,
                zIndex: isActive ? 2 : 1,
                duration: this.animDuration,
                ease: 'power3.out'
            });
            jQuery(slide).toggleClass('active', isActive);
        });

        if (this.$dotsContainer) {
            this.$dotsContainer.children().removeClass('active').eq(this.current).addClass('active');
        }
    }

    next() { this.goTo(this.current + 1); }
    prev() { this.goTo(this.current - 1); }

    startAutoplay() { 
        if (this.settings.autoplay === 'yes' && !this.autoplayTimer) {
            this.autoplayTimer = setInterval(() => this.next(), parseInt(this.settings.autoplay_speed, 10) || 5000);
        }
    }

    stopAutoplay() {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
    }

    restartAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }

    _onResize() {
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => {
            this.slideWidth = this.$slides.first().outerWidth(true);
            this.wrapWidth = this.$slides.length * this.slideWidth;
            if (this.draggable && this.draggable.length > 0) {
                this.draggable[0].kill();
                this._setupDraggable();
            }
            this.goTo(this.current, false);
        }, 250);
    }
}

jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/testimonial_carousel.default', function($scope) {
        elementorFrontend.elementsHandler.addHandler(TestimonialCarouselHandler, { $element: $scope });
    });
});