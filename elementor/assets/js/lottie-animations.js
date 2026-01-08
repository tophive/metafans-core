class LottieHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.lottieWrapper = this.$element.find('.my-lottie-wrapper')[0];
        this.lottieContainer = this.$element.find('.my-lottie')[0];
        this.animation = null;

        this.loadLottieLibrary().then(() => {
            this.initLottie();
        }).catch(err => {
            console.error("Lottie library failed to load.", err);
        });
    }

    loadLottieLibrary() {
        return new Promise((resolve, reject) => {
            if (window.lottie && window.lottie.loadAnimation) {
                return resolve();
            }
            if (document.getElementById('th-lottie-lib')) {
                const checkInterval = setInterval(() => {
                    if (window.lottie && window.lottie.loadAnimation) {
                        clearInterval(checkInterval);
                        resolve();
                    }
                }, 50);
                return;
            }
            const script = document.createElement('script');
            script.id = 'th-lottie-lib';
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js';
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    initLottie() {
        if (!this.lottieContainer || this.lottieContainer.__thInit) return;
        this.lottieContainer.__thInit = true;

        const settings = {
            container: this.lottieContainer,
            renderer: this.lottieContainer.dataset.renderer || 'svg',
            loop: this.lottieContainer.dataset.loop === 'true',
            autoplay: this.lottieContainer.dataset.autoplay === 'true',
            path: this.lottieContainer.dataset.url
        };

        this.animation = window.lottie.loadAnimation(settings);
        this.animation.setSpeed(parseFloat(this.lottieContainer.dataset.speed || '1'));

        const trigger = this.lottieContainer.dataset.trigger || 'none';
        if (trigger === 'hover') {
            this.lottieContainer.addEventListener('mouseenter', () => this.animation.play());
            this.lottieContainer.addEventListener('mouseleave', () => this.animation.pause());
        } else if (trigger === 'click') {
            this.lottieContainer.addEventListener('click', () => {
                this.animation.isPaused ? this.animation.play() : this.animation.pause();
            });
        }
    }

    onDestroy() {
        super.onDestroy();
        this.animation?.destroy();
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/lottie_animations.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(LottieHandler, { $element: $scope });
    });
});