class LiquidImageWidgetHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.initLiquidEffect();
    }

    onDestroy() {
        super.onDestroy();
        if (this.instance && typeof this.instance.cleanup === 'function') {
            this.instance.cleanup();
        }
    }

    initLiquidEffect() {
        const wrapper = this.$element.find('.liquid-image-widget-wrap')[0];
        if (!wrapper) return;
        
        const initEffect = () => {
            const image = wrapper.querySelector('img');
            if (!image) return;

            image.style.opacity = '1';

            const settings = JSON.parse(wrapper.dataset.settings || '{}');
            
            // Store instance data on the handler itself
            this.instance = {};

            const initializeEffect = () => {
                if (typeof THREE === 'undefined') {
                    console.error('Three.js is not loaded.');
                    return;
                }
                const effectMap = {
                    'mouse_displacement': initMouseDisplacement,
                    'scroll_displacement': initScrollDisplacement,
                    'ripple_hover': initRippleHover,
                    'glitch_hover': initGlitchHover,
                    'parallax_3d': initParallax3D,
                    'hue_shift': initHueShift,
                    'blur_focus': initBlurFocus,
                };
                const func = effectMap[settings.effectType] || initMouseDisplacement;
                func.call(this);
            };
            
            function createRendererScene(width, height, usePerspective = false) {
                const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
                renderer.setPixelRatio(window.devicePixelRatio || 1);
                image.parentNode.insertBefore(renderer.domElement, image);
                image.style.opacity = '0';
                const canvas = renderer.domElement;
                canvas.style.display = 'block';
                canvas.style.boxSizing = 'border-box';

                let camera;
                if (usePerspective) {
                    camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 2000);
                    camera.position.z = 500;
                } else {
                    camera = new THREE.OrthographicCamera(width / -2, width / 2, height / 2, height / -2, 0.1, 10);
                    camera.position.z = 1;
                }

                const scene = new THREE.Scene();
                this.instance.renderer = renderer;
                return { renderer, scene, camera, canvas };
            }

            function computeSize() {
                return { width: Math.max(1, wrapper.clientWidth), height: Math.max(1, wrapper.clientHeight) };
            }
            
            let eventListeners = [];
            const addTrackedListener = (el, event, handler, options) => { el.addEventListener(event, handler, options); eventListeners.push({ el, event, handler, options }); };

            function initMouseDisplacement() {
                const size = computeSize();
                const { renderer, scene, camera, canvas } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const material = new THREE.ShaderMaterial({
                    uniforms: { uTexture: { value: texture }, uMouse: { value: new THREE.Vector2(0, 0) }, uIntensity: { value: settings.displacementIntensity }, uSpeed: { value: settings.waveSpeed }, uEnabled: { value: 1.0 } },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: 'uniform sampler2D uTexture; uniform vec2 uMouse; uniform float uIntensity; uniform float uSpeed; uniform float uEnabled; varying vec2 vUv; void main(){ vec2 uv = vUv; float dist = distance(uv, uMouse); uv.y += uEnabled * uIntensity * sin(10.0 * dist); uv.x += uEnabled * uIntensity * cos(10.0 * dist); uv = clamp(uv, 0.0, 1.0); gl_FragColor = texture2D(uTexture, uv); }'
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                let mouse = { x: 0, y: 0 }, targetMouse = { x: 0, y: 0 };
                addTrackedListener(canvas, 'mousemove', function (e) {
                    const rect = canvas.getBoundingClientRect();
                    targetMouse.x = (e.clientX - rect.left) / rect.width; // 0 to 1
                    targetMouse.y = 1.0 - (e.clientY - rect.top) / rect.height; // 0 to 1, inverted
                });
                function animate() {
                    this.instance.rafId = requestAnimationFrame(animate.bind(this));
                    mouse.x += (targetMouse.x - mouse.x) * 0.1;
                    mouse.y += (targetMouse.y - mouse.y) * 0.1;
                    mesh.material.uniforms.uMouse.value.set(mouse.x, mouse.y);
                    renderer.render(scene, camera);
                }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            function initScrollDisplacement() {
                const size = computeSize();
                const { renderer, scene, camera } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const material = new THREE.ShaderMaterial({
                    uniforms: { uTexture: { value: texture }, uScroll: { value: 0.0 }, uIntensity: { value: settings.displacementIntensity } },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: 'uniform sampler2D uTexture; uniform float uScroll; uniform float uIntensity; varying vec2 vUv; void main(){ vec2 uv = vUv; uv.x += sin(uScroll * 10.0) * uIntensity; uv.y += cos(uScroll * 10.0) * uIntensity; gl_FragColor = texture2D(uTexture, uv); }'
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                addTrackedListener(window, 'scroll', function () {
                    const scrollPercent = window.scrollY / (document.body.scrollHeight - window.innerHeight || 1);
                    mesh.material.uniforms.uScroll.value = scrollPercent;
                });
                function animate() { this.instance.rafId = requestAnimationFrame(animate.bind(this)); renderer.render(scene, camera); }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            function initRippleHover() {
                const size = computeSize();
                const { renderer, scene, camera, canvas } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const material = new THREE.ShaderMaterial({
                    uniforms: { uTexture: { value: texture }, uMouse: { value: new THREE.Vector2(0, 0) }, uTime: { value: 0.0 }, uIntensity: { value: settings.displacementIntensity }, uSpeed: { value: settings.waveSpeed } },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: 'uniform sampler2D uTexture; uniform vec2 uMouse; uniform float uTime; uniform float uIntensity; uniform float uSpeed; varying vec2 vUv; void main(){ vec2 uv = vUv; float dist = distance(uv, uMouse); uv.y += sin(uTime * uSpeed - dist * 10.0) * uIntensity; uv.x += cos(uTime * uSpeed - dist * 10.0) * uIntensity; uv = clamp(uv, 0.0, 1.0); gl_FragColor = texture2D(uTexture, uv); }'
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                addTrackedListener(canvas, 'mousemove', function (e) {
                    const rect = canvas.getBoundingClientRect();
                    mesh.material.uniforms.uMouse.value.set((e.clientX - rect.left) / rect.width, 1.0 - (e.clientY - rect.top) / rect.height);
                });
                function animate(time) {
                    this.instance.rafId = requestAnimationFrame(animate.bind(this));
                    mesh.material.uniforms.uTime.value = time * 0.001;
                    renderer.render(scene, camera);
                }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            function initGlitchHover() {
                const size = computeSize();
                const { renderer, scene, camera, canvas } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const fragmentShader = `
                    uniform sampler2D uTexture; 
                    uniform float uTime; 
                    uniform float uGlitchSpeed; 
                    uniform float uGlitchIntensity; 
                    uniform float uHoverState;                    
                    varying vec2 vUv; 
                    void main(){ 
                        float intensity = uGlitchIntensity * uHoverState;
                        vec2 rUV = vUv + vec2(sin(uTime * uGlitchSpeed) * intensity, 0.0); 
                        vec2 gUV = vUv + vec2(0.0, cos(uTime * (uGlitchSpeed * 1.2)) * intensity); 
                        vec2 bUV = vUv; 
                        vec4 r = texture2D(uTexture, rUV); vec4 g = texture2D(uTexture, gUV); vec4 b = texture2D(uTexture, bUV); 
                        gl_FragColor = vec4(r.r, g.g, b.b, 1.0); 
                    }`;
                const material = new THREE.ShaderMaterial({
                    uniforms: { 
                        uTexture: { value: texture }, 
                        uTime: { value: 0.0 }, 
                        uGlitchSpeed: { value: settings.glitchSpeed }, 
                        uGlitchIntensity: { value: settings.glitchIntensity }, 
                        uHoverState: { value: settings.glitchOnHover > 0 ? 0.0 : 1.0 }
                    },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: fragmentShader
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                function animate(time) {
                    this.instance.rafId = requestAnimationFrame(animate.bind(this));
                    mesh.material.uniforms.uTime.value = time * 0.001;
                    renderer.render(scene, camera);
                }
                animate.call(this);

                if (settings.glitchOnHover > 0) {
                    addTrackedListener(canvas, 'mouseenter', function() {
                        gsap.to(material.uniforms.uHoverState, { value: 1.0, duration: 0.3 });
                    });
                    addTrackedListener(canvas, 'mouseleave', function() {
                        gsap.to(material.uniforms.uHoverState, { value: 0.0, duration: 0.3 });
                    });
                }

                this.instance.mesh = mesh;
            }

            function initParallax3D() {
                const size = computeSize();
                const { renderer, scene, camera, canvas } = createRendererScene.call(this, size.width, size.height, true);
                camera.position.z = 500;
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height);
                const material = new THREE.MeshBasicMaterial({ map: texture });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                addTrackedListener(canvas, 'mousemove', function (e) {
                    const rect = canvas.getBoundingClientRect();
                    const x = (e.clientX - rect.left) / rect.width - 0.5;
                    const y = (e.clientY - rect.top) / rect.height - 0.5;
                    mesh.rotation.y = x * 0.5;
                    mesh.rotation.x = -y * 0.5;
                });
                function animate() { this.instance.rafId = requestAnimationFrame(animate.bind(this)); renderer.render(scene, camera); }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            function initHueShift() {
                const size = computeSize();
                const { renderer, scene, camera } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const fragment = 'uniform sampler2D uTexture; uniform float uTime; varying vec2 vUv; vec3 rgb2hsv(vec3 c){ vec4 K = vec4(0.0,-1.0/3.0,2.0/3.0,-1.0); vec4 p = mix(vec4(c.bg, K.wz), vec4(c.gb, K.xy), step(c.b, c.g)); vec4 q = mix(vec4(p.xyw, c.r), vec4(c.r, p.yzx), step(p.x, c.r)); float d = q.x - min(q.w, q.y); float e = 1.0e-10; return vec3(abs((q.z + (q.w - q.y) / (6.0 * d + e))), d / (q.x + e), q.x); } vec3 hsv2rgb(vec3 c){ vec3 rgb = clamp(abs(mod(c.x * 6.0 + vec3(0.0, 4.0, 2.0), 6.0) - 3.0) - 1.0, 0.0, 1.0); return c.z * mix(vec3(1.0), rgb, c.y); } void main(){ vec3 hsv = rgb2hsv(texture2D(uTexture, vUv).rgb); hsv.x += uTime * 0.05; gl_FragColor = vec4(hsv2rgb(hsv), 1.0); }';
                const material = new THREE.ShaderMaterial({
                    uniforms: { uTexture: { value: texture }, uTime: { value: 0.0 } },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: fragment
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                function animate(time) { this.instance.rafId = requestAnimationFrame(animate.bind(this)); mesh.material.uniforms.uTime.value = time * 0.001; renderer.render(scene, camera); }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            function initBlurFocus() {
                const size = computeSize();
                const { renderer, scene, camera, canvas } = createRendererScene.call(this, size.width, size.height, false);
                const texture = new THREE.TextureLoader().load(image.src);
                texture.minFilter = THREE.LinearFilter;
                const geometry = new THREE.PlaneBufferGeometry(size.width, size.height, settings.gridResolution, settings.gridResolution);
                const fragment = 'uniform sampler2D uTexture; uniform vec2 uMouse; uniform float uBlur; varying vec2 vUv; vec4 blur(sampler2D tex, vec2 uv, float amount){ vec4 sum = vec4(0.0); for(int x=-2;x<=2;x++){ for(int y=-2;y<=2;y++){ sum += texture2D(tex, uv + vec2(x, y) * 0.002 * amount ); }} return sum / 25.0; } void main(){ float dist = distance(vUv, uMouse); gl_FragColor = blur(uTexture, vUv, dist * 10.0 * uBlur); }';
                const material = new THREE.ShaderMaterial({
                    uniforms: { uTexture: { value: texture }, uMouse: { value: new THREE.Vector2(0, 0) }, uBlur: { value: 0.0 } },
                    vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
                    fragmentShader: fragment
                });
                const mesh = new THREE.Mesh(geometry, material);
                scene.add(mesh);
                addTrackedListener(canvas, 'mousemove', function (e) {
                    const rect = canvas.getBoundingClientRect();
                    mesh.material.uniforms.uMouse.value.set((e.clientX - rect.left) / rect.width, 1.0 - (e.clientY - rect.top) / rect.height);
                    mesh.material.uniforms.uBlur.value = 1.0;
                });
                addTrackedListener(canvas, 'mouseleave', function () { mesh.material.uniforms.uBlur.value = 0.0; });
                function animate() { this.instance.rafId = requestAnimationFrame(animate.bind(this)); renderer.render(scene, camera); }
                animate.call(this);
                this.instance.mesh = mesh;
            }

            if (image.complete) {
                initializeEffect.call(this);
            } else {
                image.onload = initializeEffect.bind(this);
            }

            this.instance.cleanup = () => {
                if (this.instance.rafId) {
                    cancelAnimationFrame(this.instance.rafId);
                    this.instance.rafId = null;
                }
                eventListeners.forEach(listener => listener.el.removeEventListener(listener.event, listener.handler, listener.options));
                eventListeners = [];

                if (this.instance.mesh) {
                    this.instance.mesh.geometry?.dispose();
                    if (this.instance.mesh.material) {
                        if (Array.isArray(this.instance.mesh.material)) {
                            this.instance.mesh.material.forEach(mat => mat.dispose());
                        } else {
                            this.instance.mesh.material.dispose();
                        }
                    }
                }
                if (this.instance.renderer) {
                    this.instance.renderer.dispose();
                    this.instance.renderer.domElement.remove();
                    this.instance.renderer = null;
                }
                image.style.opacity = '1';
            };
        }
        initEffect.call(this);
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/liquid_image.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(LiquidImageWidgetHandler, { $element: $scope });
    });
});