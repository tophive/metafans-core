class WooCartHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.cartWrapper = this.$element.find('.header-cart-wrapper').get(0);
        if (!this.cartWrapper) return;

        this.cartToggle = this.cartWrapper.querySelector('.cart-toggle');
        this.cartSidebar = document.querySelector(".header-cart-sidebar");
        this.cartDropDown = document.querySelector(".header-cart-dropdown");

        this.initWooCart();
    }

    onDestroy() {
        super.onDestroy();
        // Remove all listeners to prevent memory leaks
        if (this.cartToggle) this.cartToggle.removeEventListener("click", this.handleToggleClick);
        if (this.cartSidebar) {
            const cartClose = this.cartSidebar.querySelector(".cart-close");
            if (cartClose) cartClose.removeEventListener("click", this.handleSidebarClose);
            this.cartSidebar.removeEventListener("click", this.handleCartActions);
        }
        if (this.cartDropDown) {
            this.cartDropDown.removeEventListener("click", this.handleCartActions);
        }
        document.removeEventListener("click", this.handleOutsideClick);
        jQuery(document).off("added_to_cart", this.handleAddedToCart);
    }

    initWooCart() {
        const showCartOnAdd = this.cartWrapper.dataset.showCartOnAdd === 'true';

        if (this.cartSidebar) {
            document.body.appendChild(this.cartSidebar);
            const cartClose = this.cartSidebar.querySelector(".cart-close");
            this.handleSidebarClose = () => this.cartSidebar.classList.remove("open");
            this.handleToggleClick = () => this.cartSidebar.classList.add("open");

            if (cartClose) cartClose.addEventListener("click", this.handleSidebarClose);
            if (this.cartToggle) this.cartToggle.addEventListener("click", this.handleToggleClick);
        }

        if (this.cartDropDown) {
            this.handleOutsideClick = (event) => {
                if (!this.cartWrapper.contains(event.target)) {
                    this.cartDropDown.classList.remove("open");
                }
            };
            document.addEventListener("click", this.handleOutsideClick);
        }

        this.handleAddedToCart = () => {
            this.updateWooCart(showCartOnAdd, true);
        };
        jQuery(document).on("added_to_cart", this.handleAddedToCart);

        this.handleCartActions = (event) => {
            const target = event.target;
            const cartItemKey = target.getAttribute("data-cart-item-key");
            if (!cartItemKey) return;

            if (target.classList.contains("cart-plus")) {
                const quantityElement = target.closest(".cart-quantity").querySelector(".cart-qty");
                const newQuantity = parseInt(quantityElement.value) + 1;
                this.updateCartQuantity(cartItemKey, newQuantity);
            } else if (target.classList.contains("cart-minus")) {
                const quantityElement = target.closest(".cart-quantity").querySelector(".cart-qty");
                const newQuantity = Math.max(1, parseInt(quantityElement.value) - 1);
                this.updateCartQuantity(cartItemKey, newQuantity);
            } else if (target.classList.contains("cart-remove")) {
                this.removeCartItem(cartItemKey);
            }
        };

        if (this.cartSidebar) this.cartSidebar.addEventListener("click", this.handleCartActions);
        if (this.cartDropDown) this.cartDropDown.addEventListener("click", this.handleCartActions);
    }

    updateWooCart(showCartOnAdd, forceOpen = false) {
        if (typeof ajax_cart === 'undefined') return;

        fetch(ajax_cart.ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ action: "update_cart_content", nonce: ajax_cart.nonce })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("WooCommerce AJAX Error:", data.message);
                return;
            }

            document.querySelectorAll(".cart-content").forEach(cartContainer => {
                cartContainer.innerHTML = data.data.html;
            });

            let totalQuantity = 0;
            document.querySelectorAll(".cart-qty").forEach(qtyElement => {
                totalQuantity += parseInt(qtyElement.value);
            });

            const countElement = document.querySelector(".cart-count");
            if (countElement) countElement.textContent = totalQuantity;

            const subtotalElement = document.querySelector(".cart-subtotal");
            if (subtotalElement) subtotalElement.innerHTML = data.data.subtotal;

            document.querySelectorAll(".cart-total-price").forEach(el => el.innerHTML = data.data.subtotal);

            if (forceOpen || showCartOnAdd) {
                if (this.cartSidebar) this.cartSidebar.classList.add("open");
                if (this.cartDropDown) this.cartDropDown.classList.add("open");
            }
        })
        .catch(error => console.error("AJAX Cart Update Failed:", error));
    }

    updateCartQuantity(cartItemKey, quantity) {
        if (typeof ajax_cart === 'undefined') return;
        fetch(ajax_cart.ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "update_cart_item_quantity",
                nonce: ajax_cart.nonce,
                cart_item_key: cartItemKey,
                quantity: quantity,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) this.updateWooCart(false, false);
            else console.error("Error updating cart:", data.message);
        })
        .catch(error => console.error("AJAX Cart Update Failed:", error));
    }

    removeCartItem(cartItemKey) {
        if (typeof ajax_cart === 'undefined') return;
        fetch(ajax_cart.ajaxurl, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "remove_cart_item",
                nonce: ajax_cart.nonce,
                cart_item_key: cartItemKey,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) this.updateWooCart(false, false);
            else console.error("Error removing cart item:", data.message);
        })
        .catch(error => console.error("AJAX Remove Cart Item Failed:", error));
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/header-woo-cart.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(WooCartHandler, { $element: $scope });
    });
});