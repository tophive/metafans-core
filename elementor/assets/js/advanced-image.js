class AdvancedImageHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.widget = this.$element.find('.image-pro-widget').get(0);
        if (!this.widget) return;

        this.initAdvancedImage();
    }

    initAdvancedImage() {
        const container = this.widget.querySelector('.ad-image-container');
        if (!container) return;

        const layers = container.querySelectorAll('.ad-image-layer');
        const depth = parseFloat(container.dataset.depth || 1.2);
        const duration = parseFloat(container.dataset.duration || 400) / 1000;

        this.stacked = false;

        this.onMouseMove = (e) => {
            if (this.stacked) return;
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            layers.forEach((l, i) => {
                const offset = i * 5 * depth;
                gsap.to(l, { x: x * 0.01 * offset, y: y * 0.01 * offset, opacity: 1 - i * 0.08, duration: duration, ease: "power2.out" });
            });
        };

        this.onMouseLeave = () => {
            if (this.stacked) return;
            layers.forEach((l, i) => {
                gsap.to(l, { x: 0, y: 0, opacity: i === 0 ? 1 : 0, duration: duration, ease: "power2.out" });
            });
        };

        this.onClick = () => {
            if (!this.stacked) {
                layers.forEach((l, i) => { gsap.to(l, { x: 0, y: 0, scale: 1 - i * 0.05, opacity: 1 - i * 0.08, duration: duration, ease: "power2.out" }); });
            } else {
                layers.forEach((l, i) => { gsap.to(l, { x: 0, y: 0, scale: 1, opacity: i === 0 ? 1 : 0, duration: duration, ease: "power2.out" }); });
            }
            this.stacked = !this.stacked;
        };

        container.addEventListener("mousemove", this.onMouseMove);
        container.addEventListener("mouseleave", this.onMouseLeave);
        container.addEventListener("click", this.onClick);
    }

    onDestroy() {
        super.onDestroy();
        const container = this.widget.querySelector('.ad-image-container');
        if (container) {
            container.removeEventListener("mousemove", this.onMouseMove);
            container.removeEventListener("mouseleave", this.onMouseLeave);
            container.removeEventListener("click", this.onClick);
        }
    }
}

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/Advanced_Image_Widget.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(AdvancedImageHandler, { $element: $scope });
    });
});