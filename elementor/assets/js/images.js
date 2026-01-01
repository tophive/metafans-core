class TophiveImagesWidgetHandler extends elementorModules.frontend.handlers.Base {

    onInit() {
        super.onInit();
        this.tiltElements = [];
        this.revealScrollTriggers = [];
        this.initTiltEffect();
        this.initRevealEffect();
    }

    onDestroy() {
        super.onDestroy();
        // Cleanup for Tilt Effect
        this.tiltElements.forEach(({ el, handleMove, resetTransform }) => {
            el.removeEventListener('mousemove', handleMove);
            el.removeEventListener('mouseout', resetTransform);
            el.removeEventListener('mousedown', resetTransform);
            el.removeEventListener('mouseup', resetTransform);
        });
        this.tiltElements = [];

        // Cleanup for Reveal Effect
        this.revealScrollTriggers.forEach(st => st.kill());
        this.revealScrollTriggers = [];
    }

    initTiltEffect() {
        const elements = this.$element[0].querySelectorAll('.tophive-hover-tilt');

        elements.forEach((el) => {
            const height = el.clientHeight;
            const width = el.clientWidth;

            if (height === 0 || width === 0) return;

            const settings = JSON.parse(el.dataset.tiltSettings || '{}');
            const maxRotation = settings.max || 20;
            const scaleValue = settings.scale || 1.1;
            const perspective = settings.perspective || 500;

            const handleMove = (e) => {
                const xVal = e.offsetX;
                const yVal = e.offsetY;
                const yRotation = maxRotation * ((xVal - width / 2) / width);
                const xRotation = -maxRotation * ((yVal - height / 2) / height);
                el.style.transform = `perspective(${perspective}px) scale(${scaleValue}) rotateX(${xRotation}deg) rotateY(${yRotation}deg)`;
            };

            const resetTransform = () => {
                el.style.transform = `perspective(${perspective}px) scale(1) rotateX(0) rotateY(0)`;
            };

            const handleMouseDown = () => {
                el.style.transform = `perspective(${perspective}px) scale(0.9) rotateX(0) rotateY(0)`;
            };

            const handleMouseUp = () => {
                el.style.transform = `perspective(${perspective}px) scale(${scaleValue}) rotateX(0) rotateY(0)`;
            };

            el.addEventListener('mousemove', handleMove);
            el.addEventListener('mouseout', resetTransform);
            el.addEventListener('mousedown', handleMouseDown);
            el.addEventListener('mouseup', handleMouseUp);

            this.tiltElements.push({ el, handleMove, resetTransform, handleMouseDown, handleMouseUp });
        });
    }

    initRevealEffect() {
        gsap.utils.toArray(this.$element[0].querySelectorAll(".tophive-reveal-effect")).forEach((elem) => {
            const st = ScrollTrigger.create({
                trigger: elem,
                start: 'top bottom',
                end: 'bottom top',
                toggleClass: 'reveal-applied'
            });
            this.revealScrollTriggers.push(st);
        });
    }
}

window.addEventListener('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/tophive-image.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(TophiveImagesWidgetHandler, { $element: $scope });
    });
});