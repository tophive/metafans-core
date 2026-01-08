class StickyHeaderHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.isScrolling = false;
        this.lastScrollTop = 0;
        this.wpPost = this.$element.get(0);

        if (!this.wpPost) return;

        this.initStickyRows();
    }

    onDestroy() {
        super.onDestroy();
        window.removeEventListener("scroll", this.handleScroll);
    }

    initStickyRows() {
        const stickyRows = Array.from(
            this.wpPost.querySelectorAll('[data-header-sticky="true"]')
        );
        if (!stickyRows.length) return;

        const transitionDuration = parseFloat(this.wpPost.getAttribute("data-sticky-transition-duration")) || 0.6;

        const nonStickyRows = Array.from(this.wpPost.children).filter(
            (row) => !stickyRows.includes(row)
        );
        const nonStickyHeight = nonStickyRows.reduce(
            (total, row) => total + row.offsetHeight,
            0
        );
        const wpPostOffsetTop = this.wpPost.offsetTop;

        this.lastScrollTop = window.scrollY || document.documentElement.scrollTop;

        const parseColor = (color) => {
            color = color.trim().toLowerCase();
            let match = color.match(/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\)$/);
            if (match) {
                return [parseFloat(match[1]), parseFloat(match[2]), parseFloat(match[3]), match[4] !== undefined ? parseFloat(match[4]) : 1];
            }
            match = color.match(/^#([\da-f]{3,8})$/i);
            if (match) {
                let hex = match[1];
                if (hex.length <= 4) hex = hex.split("").map((c) => c + c).join("");
                const hasAlpha = hex.length === 8;
                const val = parseInt(hex, 16);
                const r = (val >> (hasAlpha ? 24 : 16)) & 0xff;
                const g = (val >> (hasAlpha ? 16 : 8)) & 0xff;
                const b = (val >> (hasAlpha ? 8 : 0)) & 0xff;
                const a = hasAlpha ? (val & 0xff) / 255 : 1;
                return [r, g, b, a];
            }
            return [0, 0, 0, 1];
        }

        const interpolateColor = (progress, startColor, endColor) => {
            const [sr, sg, sb, sa] = parseColor(startColor);
            const [er, eg, eb, ea] = parseColor(endColor);
            const p = Math.max(0, Math.min(progress, 1));
            const r = Math.round(sr + (er - sr) * p);
            const g = Math.round(sg + (eg - sg) * p);
            const b = Math.round(sb + (eb - sb) * p);
            const a = sa + (ea - sa) * p;
            return `rgba(${r}, ${g}, ${b}, ${a})`;
        }

        stickyRows.forEach((stickyRow) => {
            const bgEndColor = stickyRow.getAttribute("data-sticky-background") || "#000000";
            const blurIntensity = parseFloat(stickyRow.getAttribute("data-sticky-blur")) || 5;

            const initialStyles = window.getComputedStyle(stickyRow);
            const bgStartColor = initialStyles.backgroundColor || "rgb(255, 255, 255)";
            const initialBorder = initialStyles.border || "none";
            const initialBoxShadow = initialStyles.boxShadow || "none";

            const borderValues = stickyRow.getAttribute("data-sticky-border") || "0 0 0 0";
            const borderColor = stickyRow.getAttribute("data-sticky-border_color") || "transparent";
            const boxShadowAttr = stickyRow.getAttribute("data-sticky-box-shadow") || "0 0 0 0 rgba(0, 0, 0, 0)";

            const [borderTop, borderRight, borderBottom, borderLeft] = borderValues.split(" ").map(val => `${val}px`);

            let boxShadowX = 0, boxShadowY = 0, boxShadowBlur = 0, boxShadowSpread = 0, boxShadowColor = "rgba(0, 0, 0, 0)";
            const boxShadowParts = boxShadowAttr.split(" ");
            if (boxShadowParts.length >= 5) {
                boxShadowX = `${boxShadowParts[0]}px`;
                boxShadowY = `${boxShadowParts[1]}px`;
                boxShadowBlur = `${boxShadowParts[2]}px`;
                boxShadowSpread = `${boxShadowParts[3]}px`;
                boxShadowColor = boxShadowParts.slice(4).join(" ");
            }

            stickyRow.style.transition = `all ${transitionDuration}s ease`;

            this.updateTransform = () => {
                const scrollY = window.scrollY || document.documentElement.scrollTop;
                const headerIsTransparent = this.wpPost.classList.contains("tophive-header-transparent");

                const topSpacing = parseFloat(stickyRow.getAttribute("data-sticky-top-spacing")) || 0;
                const maxBorderRadius = parseFloat(stickyRow.getAttribute("data-sticky-max-border-radius")) || 0;
                const minWidthPercent = parseFloat(stickyRow.getAttribute("data-sticky-min-width")) || 1;

                if (!stickyRow.dataset.originalHeight) {
                    stickyRow.dataset.originalHeight = stickyRow.offsetHeight;
                }

                const originalHeight = parseFloat(stickyRow.dataset.originalHeight);
                const targetHeight = parseFloat(stickyRow.getAttribute("data-sticky-height")) || originalHeight;

                const stickyStart = headerIsTransparent ? (wpPostOffsetTop - topSpacing) : wpPostOffsetTop;
                let scrolledAmount = Math.max(0, scrollY - stickyStart);

                const maxTransform = Math.min(nonStickyHeight, window.innerHeight / 2);
                const maxScroll = nonStickyHeight > 0 ? nonStickyHeight : 200;
                let progress = Math.min(scrolledAmount / maxScroll, 1);

                const currentMargin = topSpacing * progress;
                const currentBorderRadius = maxBorderRadius * progress;
                const currentWidth = 100 - (100 - 100 * minWidthPercent) * progress;
                const currentBackgroundColor = interpolateColor(progress, bgStartColor, bgEndColor);
                const currentHeight = originalHeight - (originalHeight - targetHeight) * progress;

                if (nonStickyHeight === 0) {
                    stickyRow.style.top = `${currentMargin}px`;
                } else {
                    stickyRow.style.marginTop = `${currentMargin}px`;
                }

                if (progress > 0) {
                    stickyRow.style.height = `${currentHeight}px`;
                    stickyRow.style.borderTop = `${borderTop} solid ${borderColor}`;
                    stickyRow.style.borderRight = `${borderRight} solid ${borderColor}`;
                    stickyRow.style.borderBottom = `${borderBottom} solid ${borderColor}`;
                    stickyRow.style.borderLeft = `${borderLeft} solid ${borderColor}`;
                    stickyRow.style.boxShadow = `${boxShadowX} ${boxShadowY} ${boxShadowBlur} ${boxShadowSpread} ${boxShadowColor}`;
                } else {
                    stickyRow.style.height = `${originalHeight}px`;
                    stickyRow.style.border = initialBorder;
                    stickyRow.style.boxShadow = initialBoxShadow;
                }

                stickyRow.style.width = `calc(${currentWidth}% - ${currentMargin * 2}px)`;
                stickyRow.style.borderRadius = `${currentBorderRadius}px`;
                stickyRow.style.backgroundColor = currentBackgroundColor;
                stickyRow.style.backdropFilter = `blur(${progress * blurIntensity}px)`;

                if (nonStickyHeight > 0) {
                    this.wpPost.style.transform = `translateY(-${Math.min(scrolledAmount, maxTransform)}px)`;
                } else {
                    this.wpPost.style.transform = "";
                }

                if (scrolledAmount >= maxScroll) {
                    this.wpPost.classList.add("is-sticky");
                    stickyRow.classList.add("only-sticky-visible");
                } else {
                    this.wpPost.classList.remove("is-sticky");
                    stickyRow.classList.remove("only-sticky-visible");
                }
            };

            this.handleScroll = () => {
                if (!this.isScrolling) {
                    this.isScrolling = true;
                    requestAnimationFrame(() => {
                        this.updateTransform();
                        this.isScrolling = false;
                    });
                }
            };

            window.addEventListener("scroll", this.handleScroll);
            this.updateTransform();
        });
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    // This handler should be attached to the element that has the `data-elementor-type="wp-post"` attribute,
    // which is typically a main header container or section.
    elementorFrontend.hooks.addAction('frontend/element_ready/global', ($scope) => {
        if ($scope.is('[data-elementor-type="wp-post"]')) {
             elementorFrontend.elementsHandler.addHandler(StickyHeaderHandler, { $element: $scope });
        }
    });
});