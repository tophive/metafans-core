class WebGLSliderHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.initSlider();
    }

    onDestroy() {
        super.onDestroy();
        this.destroySlider();
    }

    destroySlider() {
        if (this.animationFrameId) cancelAnimationFrame(this.animationFrameId);
        if (this.autoplayInterval) clearInterval(this.autoplayInterval);

        if (this.scene) {
            if (this.object && this.object.geometry) {
                this.object.geometry.dispose();
            }
            if (this.mat) {
                this.mat.dispose();
            }
        }

        if (this.renderer) {
            this.renderer.dispose();
            if (this.renderer.domElement.parentNode) {
                this.renderer.domElement.parentNode.removeChild(this.renderer.domElement);
            }
            this.renderer = null;
        }

        // Remove event listeners if they were added
        window.removeEventListener('resize', this.onResize);
        if (this.sliderEl) {
            this.sliderEl.removeEventListener('mouseenter', this.stopAutoplay);
            this.sliderEl.removeEventListener('mouseleave', this.startAutoplay);
        }
        if (this.prevBtn) this.prevBtn.removeEventListener('click', this.goToPrev);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this.goToNext);
    }

    initSlider() {
        this.sliderEl = this.$element.find('.webgl-slider-container')[0];
        if (!this.sliderEl) return;

        // Store properties on the class instance
        this.renderer = null;
        this.scene = null;
        this.camera = null;
        this.mat = null;
        this.object = null;
        this.autoplayInterval = null;
        this.animationFrameId = null;
        this.currentIndex = 0;
        this.isAnimating = false;

        const images = Array.from(this.sliderEl.querySelectorAll('img'));
        if (!images.length) return;

        const progressLine = this.sliderEl.querySelector('.slider-progress-line');
        const titleEl = this.sliderEl.querySelector('.webgl-slider-title');
        const descEl = this.sliderEl.querySelector('.webgl-slider-description');
        this.prevBtn = this.sliderEl.querySelector('.prev-slide');
        this.nextBtn = this.sliderEl.querySelector('.next-slide');

        const settings = JSON.parse(this.sliderEl.dataset.settings || '{}');
        const autoplayEnable = settings.autoplay_enable === 'yes';
        const autoplaySpeed = parseInt(settings.autoplay_speed, 10) || 5000;
        const pauseOnHover = settings.pause_on_hover === 'yes';
        const webglEffect = settings.webgl_effect || 'displacement';
        const showNavigation = settings.show_navigation === 'yes';
        const showProgress = settings.show_progress === 'yes';

        if (!showNavigation && this.prevBtn && this.nextBtn) {
            this.prevBtn.style.display = 'none';
            this.nextBtn.style.display = 'none';
        }
        if (!showProgress && progressLine) {
            progressLine.parentElement.style.display = 'none';
        }

        const displacementSlider = function (opts) {
            let parent = opts.parent, sliderImages = [];
            this.renderer = new THREE.WebGLRenderer({ antialias: false });
            this.renderer.setPixelRatio(window.devicePixelRatio);
            this.renderer.setSize(parent.offsetWidth, parent.offsetHeight);
            parent.appendChild(this.renderer.domElement);

            let loader = new THREE.TextureLoader();
            loader.crossOrigin = "anonymous";

            images.forEach(img => {
                let texture = loader.load(img.src);
                texture.magFilter = texture.minFilter = THREE.LinearFilter;
                texture.anisotropy = this.renderer.capabilities.getMaxAnisotropy();
                sliderImages.push({ texture, aspect: img.naturalWidth / img.naturalHeight });
            });

            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0x23272A);
            this.camera = new THREE.OrthographicCamera(
                parent.offsetWidth / -2, parent.offsetWidth / 2,
                parent.offsetHeight / 2, parent.offsetHeight / -2, 1, 1000
            );
            this.camera.position.z = 1;

            const getFragmentShader = (effect) => `varying vec2 vUv; uniform sampler2D currentImage; uniform sampler2D nextImage; uniform float dispFactor; void main(){ vec2 uv=vUv; vec4 c1=texture2D(currentImage,vec2(uv.x,uv.y+dispFactor*(texture2D(nextImage,uv).r*0.5))); vec4 c2=texture2D(nextImage,vec2(uv.x,uv.y+(1.0-dispFactor)*(texture2D(currentImage,uv).r*0.3))); gl_FragColor=mix(c1,c2,dispFactor); }`;

            this.mat = new THREE.ShaderMaterial({
                uniforms: { dispFactor: { value: 0 }, currentImage: { value: sliderImages[0].texture }, nextImage: { value: sliderImages[1]?.texture || sliderImages[0].texture } },
                vertexShader: `varying vec2 vUv; void main(){vUv=uv; gl_Position=projectionMatrix*modelViewMatrix*vec4(position,1.0);}`,
                fragmentShader: getFragmentShader(webglEffect),
                transparent: true, opacity: 1
            });

            let geometry = new THREE.PlaneBufferGeometry(parent.offsetWidth, parent.offsetHeight, 1, 1);
            this.object = new THREE.Mesh(geometry, this.mat);
            this.scene.add(this.object);

            const updatePlaneSize = () => { if(!this.object || !sliderImages[this.currentIndex]) return; let planeWidth, planeHeight, imgAspect = sliderImages[this.currentIndex].aspect, sliderAspect = parent.offsetWidth / parent.offsetHeight; if (sliderAspect > imgAspect) { planeWidth = parent.offsetWidth; planeHeight = parent.offsetWidth / imgAspect; } else { planeHeight = parent.offsetHeight; planeWidth = parent.offsetHeight * imgAspect; } this.object.geometry.dispose(); this.object.geometry = new THREE.PlaneBufferGeometry(planeWidth, planeHeight, 1, 1); };
            updatePlaneSize();
            
            this.goToSlide = nextIndex => {
                if (this.isAnimating || nextIndex === this.currentIndex) return;
                this.isAnimating = true;

                this.mat.uniforms.nextImage.value = sliderImages[nextIndex].texture;
                this.mat.uniforms.nextImage.needsUpdate = true;

                gsap.to(this.mat.uniforms.dispFactor, {
                    value: 1, duration: 1, ease: 'expo.inOut',
                    onComplete: () => {
                        this.mat.uniforms.currentImage.value = sliderImages[nextIndex].texture;
                        this.mat.uniforms.currentImage.needsUpdate = true;
                        this.mat.uniforms.dispFactor.value = 0;
                        this.currentIndex = nextIndex;
                        this.isAnimating = false;
                        updatePlaneSize();
                    }
                });

                gsap.killTweensOf([titleEl, descEl]);
                let currentSlide = images[nextIndex];
                let textEffect = currentSlide.dataset.textEffect || 'slide';
                let textDuration = parseFloat(currentSlide.dataset.textDuration) || 0.8;
                let textDelay = parseFloat(currentSlide.dataset.textDelay) || 0;
                let textEase = currentSlide.dataset.textEase || 'power2.inOut';

                titleEl.innerText = currentSlide.dataset.title;
                descEl.innerText = currentSlide.dataset.description;

                switch (textEffect) {
                    case 'fade': gsap.fromTo([titleEl, descEl], { autoAlpha: 0 }, { autoAlpha: 1, duration: textDuration, delay: textDelay, ease: textEase }); break;
                    case 'slide': gsap.fromTo(titleEl, { x: -100, autoAlpha: 0 }, { x: 0, autoAlpha: 1, duration: textDuration, delay: textDelay, ease: textEase }); gsap.fromTo(descEl, { x: 100, autoAlpha: 0 }, { x: 0, autoAlpha: 1, duration: textDuration, delay: textDelay, ease: textEase }); break;
                    case 'zoom': gsap.fromTo([titleEl, descEl], { scale: 0.5, autoAlpha: 0 }, { scale: 1, autoAlpha: 1, duration: textDuration, delay: textDelay, ease: textEase }); break;
                    case 'glitch': gsap.fromTo(titleEl, { x: -5 }, { x: 0, duration: 0.1, repeat: 5, yoyo: true }); gsap.fromTo(descEl, { x: 5 }, { x: 0, duration: 0.1, repeat: 5, yoyo: true }); break;
                }

                if (showProgress && progressLine) {
                    const progressPercent = ((nextIndex + 1) / images.length) * 100;
                    gsap.to(progressLine, { width: progressPercent + '%', duration: 0.8, ease: 'power2.out' });
                }
            };

            this.goToNext = () => this.goToSlide((this.currentIndex + 1) % sliderImages.length);
            this.goToPrev = () => this.goToSlide((this.currentIndex - 1 + sliderImages.length) % sliderImages.length);

            if (showNavigation && this.prevBtn && this.nextBtn) {
                this.prevBtn.addEventListener('click', this.goToPrev);
                this.nextBtn.addEventListener('click', this.goToNext);
            }

            this.startAutoplay = () => { if(autoplayEnable) { this.autoplayInterval = setInterval(this.goToNext, autoplaySpeed); } };
            this.stopAutoplay = () => { if (this.autoplayInterval) clearInterval(this.autoplayInterval); };

            this.startAutoplay();
            if (pauseOnHover) {
                parent.addEventListener('mouseenter', this.stopAutoplay);
                parent.addEventListener('mouseleave', this.startAutoplay);
            }
            
            const animate = () => {
                this.animationFrameId = requestAnimationFrame(animate);
                if (this.renderer) this.renderer.render(this.scene, this.camera);
            };
            animate();

            this.onResize = () => {
                if (!this.renderer) return;
                this.renderer.setSize(parent.offsetWidth, parent.offsetHeight);
                this.camera.left = parent.offsetWidth / -2; this.camera.right = parent.offsetWidth / 2;
                this.camera.top = parent.offsetHeight / 2; this.camera.bottom = parent.offsetHeight / -2;
                this.camera.updateProjectionMatrix();
                updatePlaneSize();
            };
            window.addEventListener('resize', this.onResize);
        }.bind(this);

        displacementSlider({ parent: this.sliderEl });
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/webgl_slider.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(WebGLSliderHandler, { $element: $scope });
    });
});