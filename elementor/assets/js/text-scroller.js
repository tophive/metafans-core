class TextScrollerHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        const scroller = this.$element.find(".customizable-scroller p").get(0);
        if (!scroller) return;

        const wrapper = this.$element.find(".customizable-scroller").get(0);
        const speed = wrapper.dataset.animationSpeed || '20s';
        
        scroller.style.animationDuration = speed;
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/horizontal_text_scroller.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(TextScrollerHandler, { $element: $scope });
    });
});