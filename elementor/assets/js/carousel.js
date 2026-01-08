class TophiveCarouselHandler extends elementorModules.frontend.handlers.Base {

    onInit() {
        super.onInit();
        this.initCarousel();
    }

    onDestroy() {
        super.onDestroy();
        if (this.swiperInstance) {
            this.swiperInstance.destroy();
        }
    }

    initCarousel() {
        const carousel = this.$element[0].querySelector('.tophive-card-carousel');

        if (!carousel) return;

        const settings = JSON.parse(carousel.getAttribute('data-tophive-carousel-settings') || '{}');

        // Use Elementor's internal Swiper utility
        this.swiperInstance = new elementorFrontend.utils.swiper(carousel, settings);
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/tophive-posts-card.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(TophiveCarouselHandler, { $element: $scope });
    });
});
  