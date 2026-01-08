
class TophiveAdvancedTextWidgetHandler extends elementorModules.frontend.handlers.Base {

	onDestroy() {
		super.onDestroy();
		if (this.rotatorTimeline) this.rotatorTimeline.kill();
		if (this.typerAnimationId) cancelAnimationFrame(this.typerAnimationId);
		if (this.textFillTimeline) this.textFillTimeline.kill();
	}

	getDefaultElements() {
		return {
			$container: this.$element
		};
	}

	startTextRotator($container) {
		const wordList = $container.find(".text-rotate-keywords")[0];
		const edgeElement = $container.find(".text-rotator-spacer");

		if (!wordList || !edgeElement) return;

		const words = Array.from(wordList.children);
		const totalWords = words.length;

		const visibleWords = parseInt(wordList.getAttribute('data-visible-words')) || 3;
		if (totalWords < visibleWords) return;

		const direction = wordList.getAttribute('data-rotation-direction') || 'to-top';
		const isToTop = direction === 'to-top';

		const wordPixelHeight = words[0].offsetHeight;
		let currentIndex = Math.floor((totalWords - visibleWords) / 2);

		function updateEdgeWidth() {
			const currentWords = Array.from(wordList.children);
			const centerOffset = Math.floor(visibleWords / 2);
			const centerIndex = currentIndex + centerOffset;
			const centerWord = currentWords[centerIndex];
			if (!centerWord) return;

			const centerWordWidth = centerWord.getBoundingClientRect().width;

			gsap.to(edgeElement, {
				width: `${centerWordWidth}px`,
				duration: 0.5,
				ease: "expo.out",
			});

			currentWords.forEach((w) => w.classList.remove("active"));
			centerWord.classList.add("active");
		}

		function moveWords() {
			currentIndex += isToTop ? 1 : -1;

			gsap.to(wordList, {
				y: -wordPixelHeight * currentIndex,
				duration: 1.2,
				ease: "elastic.out(1, 0.85)",
				onStart: updateEdgeWidth,
				onComplete: function () {
					if (isToTop && currentIndex >= totalWords - visibleWords) {
						wordList.appendChild(wordList.children[0]);
						currentIndex--;
						gsap.set(wordList, { y: -wordPixelHeight * currentIndex });
					} else if (!isToTop && currentIndex <= 0) {
						wordList.insertBefore(wordList.lastElementChild, wordList.firstElementChild);
						currentIndex++;
						gsap.set(wordList, { y: -wordPixelHeight * currentIndex });
					}
				},
			});
		}

		gsap.set(wordList, { y: -wordPixelHeight * currentIndex });
		updateEdgeWidth();

		gsap.timeline({ repeat: -1, delay: 1 })
			.call(moveWords)
			.to({}, { duration: 2 });
	}

	startTyperEffect($container) {
        const typedEl = $container.find(".th-typed")[0];
        if (!typedEl) return;
    
        const data = typedEl.dataset.typerStrings;
        let words = [];
    
        try {
            words = JSON.parse(data);
        } catch (e) {
            console.warn("Invalid typerStrings JSON");
        }
    
        if (!words || !words.length) return;
    
        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let lastTimestamp = null;
        let delay = 0;
    
        typedEl.classList.add("th-typer-active");
    
        function type(timestamp) {
            if (!lastTimestamp) lastTimestamp = timestamp;
            const elapsed = timestamp - lastTimestamp;
    
            if (elapsed > delay) {
                const fullText = words[wordIndex];
                let typedText = isDeleting
                    ? fullText.substring(0, charIndex--)
                    : fullText.substring(0, charIndex++);
    
                typedEl.textContent = typedText;
    
                if (!isDeleting && charIndex > fullText.length) {
                    isDeleting = true;
                    delay = 1200; // Pause before deleting
                } else if (isDeleting && charIndex === 0) {
                    isDeleting = false;
                    wordIndex = (wordIndex + 1) % words.length;
                    delay = 300; // Pause before typing next
                } else {
                    delay = isDeleting 
                        ? 50 + Math.sin(charIndex) * 15 
                        : 100 + Math.cos(charIndex) * 30;

                }
    
                lastTimestamp = timestamp;
            }
    
            this.typerAnimationId = requestAnimationFrame(type.bind(this));
        }
    
        this.typerAnimationId = requestAnimationFrame(type.bind(this));
    }

    applySplitTextEffect($element) {
        const el = $element[0];
    
        const hasSplitAttr = el.hasAttribute('data-split-text') && el.getAttribute('data-split-text') === 'yes';
        const isFillEffect = $element.hasClass('tophive-text-fill-effects');
    
        if (!hasSplitAttr && !isFillEffect) return; // Skip if neither condition met
    
        const splitBy = isFillEffect ? 'chars' : $element.data('split-by') || 'words';
    
        const splitOptions = {
            type: splitBy,
        };
        splitOptions[splitBy + 'Class'] = splitBy;
    
        const $target = $element.find('.elementor-heading-title');
        if (!$target.length) return;
    
        const split = new SplitText($target[0], splitOptions);
    
        if ($element.hasClass('th-split-mask')) {
            $target.find(`.${splitBy}`).wrap('<div class="wrapper-mask">');
        }
    }
    
    
    
    startTextFillEffect($element) {
        
        const settingsAttr = $element[0].querySelector('.tophive-advanced-heading')?.getAttribute('data-fill-settings');
        console.log(settingsAttr);
        if (!settingsAttr) return;
    
        let settings = {};
        try {
            settings = JSON.parse(settingsAttr);
        } catch (e) {
            console.warn('Invalid data-fill-settings JSON');
            return;
        }
    
        const chars = $element.find('.chars');
        if (!chars.length) return;

        this.textFillTimeline = gsap.timeline({
            scrollTrigger: {
                trigger: $element[0],
                start: settings.start || "top 50%",
                end: settings.end || "bottom 50%",
                scrub: settings.scrub ?? 0.75,
                pin: settings.pin === true,
            }
        }).set(chars, {
                color: settings.color || '#FF0000',
                stagger: 0.1
            },0.1
        );
    }
    

	onInit() {
		super.onInit();
		const $container = this.elements.$container;
        
		if ($container.length) {
            this.applySplitTextEffect($container);
            this.startTextRotator($container);
            this.startTyperEffect($container);
            this.startTextFillEffect(this.$element);   
		}

	}
}

// Attach to widget type
window.addEventListener('elementor/frontend/init', () => {
    const addAdvancedTextHandler = ($scope) => {
        elementorFrontend.elementsHandler.addHandler(TophiveAdvancedTextWidgetHandler, { $element: $scope });
    };

	elementorFrontend.hooks.addAction('frontend/element_ready/tophive-advanced-heading.default', addAdvancedTextHandler);
});


class FilteringTabsWidgetHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                tabs: '.tab-item',
                gridItems: '.grid-item',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');

        return {
            $tabs: this.$element.find(selectors.tabs),
            $gridItems: this.$element.find(selectors.gridItems),
        };
    }

    handleTabClick($tab) {
        const filter = $tab.data('filter');

        // Update active tab
        this.elements.$tabs.removeClass('active');
        $tab.addClass('active');

        // Show or hide grid items based on the filter
        this.elements.$gridItems.each((index, item) => {
            const $item = jQuery(item);

            if (filter === '*' || $item.hasClass(filter)) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }

    bindEvents() {
        this.elements.$tabs.on('click', (event) => {
            const $tab = jQuery(event.currentTarget);
            this.handleTabClick($tab);
        });
    }

    onDestroy() {
        super.onDestroy();
        if (this.elements.$tabs) this.elements.$tabs.off('click');
    }

    onInit() {
        super.onInit();

        // Initialize the events for tabs
        this.bindEvents();
    }
}

// Register the handler with Elementor
jQuery(window).on('elementor/frontend/init', () => {
    const addFilteringTabsHandler = ($scope) => {
        elementorFrontend.elementsHandler.addHandler(FilteringTabsWidgetHandler, { $element: $scope });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/filtering-tabs.default', addFilteringTabsHandler);
});