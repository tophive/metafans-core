class OffcanvasHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.main = this.$element.get(0);
        if (!this.main) return;

        this.openBtn = this.main.querySelector(".offcanvas_button_open");
        this.closeBtn = this.main.querySelector(".offcanvas_button_close");

        this.handleOpen = this.open.bind(this);
        this.handleClose = this.close.bind(this);

        if (this.openBtn) {
            this.openBtn.addEventListener("click", this.handleOpen);
        }
        if (this.closeBtn) {
            this.closeBtn.addEventListener("click", this.handleClose);
        }
    }

    onDestroy() {
        super.onDestroy();
        if (this.openBtn) {
            this.openBtn.removeEventListener("click", this.handleOpen);
        }
        if (this.closeBtn) {
            this.closeBtn.removeEventListener("click", this.handleClose);
        }
    }

    open() {
        this.main.classList.add("open");
    }

    close() {
        this.main.classList.remove("open");
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/offcanvas.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(OffcanvasHandler, { $element: $scope });
    });
});
