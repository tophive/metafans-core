class OsmoSliderHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.sliderElement = this.$element[0];
        if (!this.sliderElement) return;

        this.initSlider();
    }

    onDestroy() {
        super.onDestroy();
        this.stop();
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }
        if (this.prevBtn) this.prevBtn.removeEventListener('click', this.handlePrevClick);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this.handleNextClick);
        if (this.dots) {
            this.dots.forEach(dot => dot.removeEventListener('click', dot.clickHandler));
        }
        if (this.settings.pauseOnHover) {
            this.sliderElement.removeEventListener('mouseenter', this.handleMouseEnter);
            this.sliderElement.removeEventListener('mouseleave', this.handleMouseLeave);
        }
    }

    initSlider() {
        this.list = this.sliderElement.querySelector('[data-slider="list"]');
        if (!this.list) return;

        const orig = Array.from(this.list.querySelectorAll('[data-slider="slide"]'));
        if (orig.length === 0) return;

        this.total = orig.length;
        this.prevBtn = this.sliderElement.querySelector('[data-slider="prev"]');
        this.nextBtn = this.sliderElement.querySelector('[data-slider="next"]');
        this.counter = this.sliderElement.querySelector('.osmo-slider-nav .current');
        const totalCounter = this.sliderElement.querySelector('.osmo-slider-nav .total');
        if (totalCounter) totalCounter.textContent = this.total;

        // Settings from data attributes
        this.settings = {
            autoplay: this.sliderElement.dataset.autoplay === '1',
            speed: Math.max(800, parseInt(this.sliderElement.dataset.speed || '4000', 10)),
            loop: this.sliderElement.dataset.loop === '1',
            pauseOnHover: this.sliderElement.dataset.pauseHover === '1',
            mode: this.sliderElement.dataset.mode || 'slide',
            showDots: this.sliderElement.dataset.dots === '1',
        };

        // Clone for infinite loop in slide mode
        if (this.settings.mode === 'slide' && this.settings.loop) {
            const head = orig.map(s => s.cloneNode(true));
            const tail = orig.map(s => s.cloneNode(true));
            head.forEach(c => this.list.insertBefore(c, this.list.firstChild));
            tail.forEach(c => this.list.appendChild(c));
        }
        this.allSlides = Array.from(this.list.querySelectorAll('[data-slider="slide"]'));

        this.slideW = 0;
        this.index = this.settings.loop && this.settings.mode === 'slide' ? this.total : 0;
        this.timer = null;
        this.isHovering = false;

        this.buildDots();
        this.bindEvents();

        if (this.settings.mode === 'fade') this.setupFadeLayout();
        
        this.measure();
        this.setActive(this.index);
        this.play();
    }

    buildDots() {
        const dotsWrap = this.sliderElement.querySelector('.osmo-slider-dots');
        if (!this.settings.showDots || !dotsWrap) return;
        dotsWrap.innerHTML = '';
        this.dots = [];
        for (let i = 0; i < this.total; i++) {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'osmo-dot';
            b.setAttribute('role', 'tab');
            b.setAttribute('aria-label', `Go to slide ${i + 1}`);
            b.clickHandler = () => this.goToByHuman(i);
            b.addEventListener('click', b.clickHandler);
            dotsWrap.appendChild(b);
            this.dots.push(b);
        }
        this.updateDots(this.index % this.total);
    }

    updateDots(active) {
        if (!this.dots || !this.dots.length) return;
        this.dots.forEach((d, i) => d.setAttribute('aria-selected', i === active ? 'true' : 'false'));
    }

    animateCaption(activeIndex) {
        this.allSlides.forEach((s, i) => {
            const cap = s.querySelector('.slide-caption');
            if (!cap) return;
            gsap.to(cap, {
                opacity: i === activeIndex ? 1 : 0,
                y: i === activeIndex ? 0 : 20,
                duration: i === activeIndex ? 0.5 : 0.4,
                ease: i === activeIndex ? 'power3.out' : 'power3.in'
            });
        });
    }

    normalize(i) {
        if (!this.settings.loop || this.settings.mode !== 'slide') return i;
        if (i >= this.total * 2) i = this.total + (i % this.total);
        if (i < this.total) i = this.total + ((i % this.total + this.total) % this.total);
        return i;
    }

    setActive(i) {
        this.index = this.normalize(i);
        if (this.counter) this.counter.textContent = (this.index % this.total) + 1;
        this.updateDots(this.index % this.total);
        this.animateCaption(this.index);
    }

    goTo(i, dur = 0.6) {
        this.setActive(i);
        gsap.to(this.list, {
            x: -this.index * this.slideW,
            duration: dur,
            ease: 'power3.inOut',
            onComplete: () => {
                const norm = this.normalize(this.index);
                if (norm !== this.index) {
                    this.index = norm;
                    gsap.set(this.list, { x: -this.index * this.slideW });
                    this.animateCaption(this.index);
                }
            }
        });
    }

    setupFadeLayout() {
        const viewport = this.sliderElement.querySelector('.osmo-slider-viewport');
        viewport.style.position = 'relative';
        this.allSlides.forEach((s, i) => {
            s.style.position = 'absolute';
            s.style.inset = '0';
            s.style.willChange = 'opacity, transform';
            gsap.set(s, { opacity: i === this.index ? 1 : 0, zIndex: i === this.index ? 2 : 1 });
        });
    }

    goToFade(nextIdx) {
        const target = (nextIdx % this.total + this.total) % this.total;
        if (target === this.index) return;

        const currEl = this.allSlides[this.index];
        const nextEl = this.allSlides[target];

        gsap.set(nextEl, { opacity: 0, zIndex: 3 });
        gsap.to(currEl, { opacity: 0, duration: 0.6, ease: 'power2.out', onComplete: () => gsap.set(currEl, { zIndex: 1 }) });
        gsap.to(nextEl, {
            opacity: 1, duration: 0.6, ease: 'power2.out', onComplete: () => {
                this.index = target;
                if (this.counter) this.counter.textContent = (this.index % this.total) + 1;
                this.updateDots(this.index % this.total);
                this.animateCaption(this.index);
            }
        });
    }

    measure() {
        const first = this.sliderElement.querySelector('.osmo-slide');
        this.slideW = first ? first.getBoundingClientRect().width : 0;
        if (this.slideW > 0 && this.settings.mode === 'slide') {
            gsap.set(this.list, { x: -this.index * this.slideW });
        }
    }

    goToByHuman(n) {
        this.stop();
        if (this.settings.mode === 'fade') { this.goToFade(n); }
        else { this.goTo(this.settings.loop ? this.total + n : n); }
        this.play();
    }

    bindEvents() {
        this.handlePrevClick = () => this.goToByHuman((this.index % this.total) - 1);
        this.handleNextClick = () => this.goToByHuman((this.index % this.total) + 1);
        if (this.prevBtn) this.prevBtn.addEventListener('click', this.handlePrevClick);
        if (this.nextBtn) this.nextBtn.addEventListener('click', this.handleNextClick);

        this.handleMouseEnter = () => { this.isHovering = true; this.stop(); };
        this.handleMouseLeave = () => { this.isHovering = false; this.play(); };
        if (this.settings.pauseOnHover) {
            this.sliderElement.addEventListener('mouseenter', this.handleMouseEnter);
            this.sliderElement.addEventListener('mouseleave', this.handleMouseLeave);
        }

        this.resizeObserver = new ResizeObserver(() => this.measure());
        this.resizeObserver.observe(this.sliderElement);
    }

    play() {
        this.stop();
        if (!this.settings.autoplay || this.isHovering) return;
        if (this.settings.mode === 'slide' && this.slideW === 0) return;
        this.timer = setInterval(() => {
            this.goToByHuman((this.index % this.total) + 1);
        }, this.settings.speed);
    }

    stop() {
        if (this.timer) { clearInterval(this.timer); this.timer = null; }
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/osmo-slider.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(OsmoSliderHandler, { $element: $scope });
    });
});