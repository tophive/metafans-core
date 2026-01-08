class LiquidSlideshowHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.initSlideshow();
    }

    onDestroy() {
        super.onDestroy();
        if (this.cleanup) {
            this.cleanup();
        }
    }

    initSlideshow() {
        const wrapper = this.$element.find('.lm-slider-wrapper')[0];
        if (!wrapper) return;
        if (typeof THREE === 'undefined' || typeof gsap === 'undefined') {
            console.error('Three.js or GSAP is not loaded for the Liquid Slideshow.');
            return;
        }

        // Settings from data attributes
        const settings = JSON.parse(wrapper.dataset.settings || '{}');
        const SLIDES = settings.slides || [];
        const UID = wrapper.dataset.uid;
        const LOCAL_DISP = settings.localDisp || '';
        const SAFE_DISP = settings.safeDisp || 'https://threejs.org/examples/textures/waterdudv.jpg';
        const EFFECT_TYPE = settings.effectType || 'liquid';
        const EFFECT_INTENSITY = parseFloat(settings.effectIntensity || 0.6);
        const TITLE_ANIMATION_EFFECT = settings.titleAnimationEffect || 'fade_slide';
        let AUTOPLAY_ENABLED = settings.autoplayEnabled === 'true';
        const AUTOPLAY_DELAY = parseInt(settings.autoplayDelay || 5000, 10);
        const PAUSE_ON_HOVER = settings.pauseOnHover === 'true';
        const PAUSE_ON_INTERACTION = settings.pauseOnInteraction === 'true';
        const LOOP_SLIDES = settings.loopSlides === 'true';

        // DOM elements
        const canvas = wrapper.querySelector('.lm-webgl-canvas');
        const navRoot = wrapper.querySelector('.lm-slides-navigation');
        const titleEl = wrapper.querySelector('.lm-title');
        const subtitleEl = wrapper.querySelector('.lm-subtitle');
        const numberEl = wrapper.querySelector('.lm-slide-number');
        const totalEl = wrapper.querySelector('.lm-slide-total');
        totalEl.textContent = String(SLIDES.length).padStart(2, '0');

        // Event tracking
        const _navHandlers = [];
        const _globalHandlers = [];
        const _wrapperHandlers = [];

        // Accessibility: prefers-reduced-motion
        const PREFERS_REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Detect mobile / low power
        const MOBILE = window.innerWidth < 720 || /Mobi|Android/i.test(navigator.userAgent);
        const CLIENT_LOW_POWER = MOBILE || (window.innerWidth < 720 && window.devicePixelRatio > 1.5);
        const PIXEL_RATIO_CAP = CLIENT_LOW_POWER ? 1.0 : Math.min(window.devicePixelRatio, 1.25);
        const RENDER_ANTIALIAS = !CLIENT_LOW_POWER;

        // Auto-downgrade heavy effects
        const LIGHTWEIGHT_EFFECTS = ['fade', 'slide', 'liquid'];
        let EFFECT_TYPE_FINAL = EFFECT_TYPE;
        if (CLIENT_LOW_POWER || MOBILE) {
            if (!LIGHTWEIGHT_EFFECTS.includes(EFFECT_TYPE_FINAL)) {
                EFFECT_TYPE_FINAL = 'fade';
                console.warn(`Heavy effect "${EFFECT_TYPE}" disabled on mobile, using "${EFFECT_TYPE_FINAL}"`);
            }
        }

        // Adjust intensity
        let EFFECT_INTENSITY_FINAL = EFFECT_INTENSITY;
        if (PREFERS_REDUCED) {
            EFFECT_INTENSITY_FINAL = Math.min(EFFECT_INTENSITY, 0.3);
            AUTOPLAY_ENABLED = false;
        } else if (CLIENT_LOW_POWER) {
            EFFECT_INTENSITY_FINAL = Math.min(EFFECT_INTENSITY, 0.5);
        }

        // Event helper
        function addTrackedListener(el, event, handler, list) { el.addEventListener(event, handler); list.push({ el, event, handler }); }

        // Navigation setup
        navRoot.innerHTML = '';
        const navItemsLocal = [];
        SLIDES.forEach((s, i) => {
            const item = document.createElement('div');
            item.id = UID + '-nav-item-' + i;
            item.dataset.index = i;
            item.className = 'lm-slide-nav-item' + (i === 0 ? ' active' : '');
            item.setAttribute('role', 'button');
            item.setAttribute('tabindex', '0');
            item.setAttribute('aria-label', `Go to slide ${i + 1}: ${s.slide_title || ''}`);

            const line = document.createElement('div'); line.className = 'lm-slide-progress-line';
            const fill = document.createElement('div'); fill.className = 'lm-slide-progress-fill';
            line.appendChild(fill);

            const titleDiv = document.createElement('div'); titleDiv.className = 'lm-slide-nav-title';
            titleDiv.textContent = s.slide_title || '';

            item.appendChild(line); item.appendChild(titleDiv);
            navRoot.appendChild(item);

            const navClick = (e) => { e.preventDefault(); e.stopPropagation(); goToSlide(i); };
            addTrackedListener(item, 'click', navClick, _navHandlers);
            addTrackedListener(item, 'keydown', (ev) => { if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); navClick(ev); } }, _navHandlers);
            navItemsLocal.push(item);
        });
        const navItems = navRoot.querySelectorAll('.lm-slide-nav-item');

        // Renderer setup
        let renderer = new THREE.WebGLRenderer({ canvas, antialias: RENDER_ANTIALIAS, alpha: true });
        renderer.setPixelRatio(PIXEL_RATIO_CAP);
        renderer.setSize(wrapper.clientWidth || window.innerWidth, wrapper.clientHeight || window.innerHeight);

        const scene = new THREE.Scene();
        const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

        const vertexShader = `varying vec2 vUv; void main(){ vUv=uv; gl_Position=projectionMatrix*modelViewMatrix*vec4(position,1.0); }`;

        // Fragment Shaders for effects
        function getFragmentShader(effect) {
            const common = `
                precision highp float;
                uniform sampler2D uTexture1; uniform sampler2D uTexture2; uniform sampler2D uDisp;
                uniform vec2 uResolution; uniform vec2 uTexture1Size; uniform vec2 uTexture2Size;
                uniform float uProgress; uniform float uIntensity;
                varying vec2 vUv;
                vec2 getCoverUV(vec2 uv, vec2 textureSize) {
                    vec2 s = uResolution / textureSize; float scale = max(s.x, s.y);
                    vec2 scaled = textureSize * scale; vec2 offset = (uResolution - scaled) * 0.5;
                    return (uv * uResolution - offset) / scaled;
                }`;
            // All Effect implementations
            if (effect === 'fade') return common + `void main() { vec4 t1=texture2D(uTexture1,getCoverUV(vUv,uTexture1Size)); vec4 t2=texture2D(uTexture2,getCoverUV(vUv,uTexture2Size)); gl_FragColor=mix(t1,t2,uProgress); }`;
            if (effect === 'slide') return common + `void main() { vec2 uv1=getCoverUV(vUv,uTexture1Size); vec2 uv2=getCoverUV(vUv,uTexture2Size); vec2 off1=vec2(-uProgress,0.); vec2 off2=vec2(1.-uProgress,0.); gl_FragColor=mix(texture2D(uTexture1,uv1+off1),texture2D(uTexture2,uv2+off2),uProgress); }`;
            if (effect === 'liquid') return common + `void main(){vec2 disp=texture2D(uDisp,vUv).rg;float env=sin(uProgress*3.14159);vec2 offset=(disp-0.5)*2.*.08*uIntensity*env;gl_FragColor=mix(texture2D(uTexture1,getCoverUV(vUv,uTexture1Size)+offset*uProgress),texture2D(uTexture2,getCoverUV(vUv,uTexture2Size)-offset*(1.-uProgress)),uProgress);}`;
            if (effect === 'morph') return common + `float n(vec2 p){return fract(sin(dot(p,vec2(12.9898,78.233)))*43758.5453);}void main(){vec2 uv1=getCoverUV(vUv,uTexture1Size);vec2 uv2=getCoverUV(vUv,uTexture2Size);float a=uProgress;float e=sin(a*3.14159);vec2 d=(texture2D(uDisp,vUv).rg-0.5)*2.*uIntensity*.6*e*(1.-abs(0.5-a));gl_FragColor=mix(texture2D(uTexture1,uv1+d*(1.-a)),texture2D(uTexture2,uv2-d*a),smoothstep(0.,1.,a));}`;
            if (effect === 'ripple') return common + `void main(){vec2 uv1=getCoverUV(vUv,uTexture1Size);vec2 uv2=getCoverUV(vUv,uTexture2Size);vec2 center=vec2(0.5);float d=distance(vUv,center);float freq=12.;float amp=.02*uIntensity;float e=sin(uProgress*3.14159);float r=sin((d-uProgress)*freq)*amp*e;vec2 o=(vUv-center)*r;gl_FragColor=mix(texture2D(uTexture1,uv1+o),texture2D(uTexture2,uv2-o),uProgress);}`;
            if (effect === 'circle') return common + `void main(){vec2 uv1=getCoverUV(vUv,uTexture1Size);vec2 uv2=getCoverUV(vUv,uTexture2Size);vec2 center=vec2(0.5);float d=distance(vUv,center);float maxR=1.2*length(vec2(0.5));float r=uProgress*maxR;float f=.04*(1.+uIntensity*.8);float m=1.-smoothstep(r-f,r+f,d);gl_FragColor=mix(texture2D(uTexture1,uv1),texture2D(uTexture2,uv2),m*smoothstep(0.,1.,uProgress));}`;
            if (effect === 'cinematic') return common + `void main(){vec2 uv1=getCoverUV(vUv,uTexture1Size);vec2 uv2=getCoverUV(vUv,uTexture2Size);vec2 center=vec2(0.5);float z1=1.+.1*(1.-uProgress);float z2=1.+.1*uProgress;uv1=(uv1-center)/z1+center;uv2=(uv2-center)/z2+center;uv1.x+=.05*uProgress;uv2.x-=.05*(1.-uProgress);gl_FragColor=mix(texture2D(uTexture1,uv1),texture2D(uTexture2,uv2),uProgress);}`;
            if (effect === 'glitch_chroma') return common + `void main(){vec2 p=vUv;float d=0.02*sin(p.y*10.+uTime*2.)*uProgress;vec4 t1=texture2D(uTexture1,p+vec2(d,0.));vec4 t2=texture2D(uTexture2,p-vec2(d,0.));gl_FragColor=mix(t1,t2,uProgress);}`;
            if (effect === 'glitch_warp_rgb') return common + `void main(){vec2 p=vUv;float d=0.02*sin(p.y*30.+uTime*4.)*uProgress;float r=texture2D(uTexture1,p+vec2(d,0.)).r;float g=texture2D(uTexture1,p).g;float b=texture2D(uTexture1,p-vec2(d,0.)).b;gl_FragColor=mix(vec4(r,g,b,1.),texture2D(uTexture2,p),uProgress);}`;
            if (effect === 'aurora') return common + `void main(){vec2 uv=getCoverUV(vUv,uTexture1Size);vec3 grad=0.5+0.5*cos(uv.y*10.+uProgress*6.2831+vec3(0.,2.,4.));gl_FragColor=mix(texture2D(uTexture1,uv),texture2D(uTexture2,uv),uProgress)+vec4(grad,0.)*0.2;}`;
            if (effect === 'timewarp') return common + `void main(){vec2 uv=getCoverUV(vUv,uTexture1Size);vec2 center=vec2(0.5);float z1=1.+.5*sin(uProgress*3.1415);vec2 uv1z=(uv-center)/z1+center;vec2 uv2z=(uv-center)/(1.+.5*(1.-uProgress))+center;gl_FragColor=mix(texture2D(uTexture1,uv1z),texture2D(uTexture2,uv2z),uProgress);}`;
            if (effect === 'particle') return common + `void main(){vec2 uv=getCoverUV(vUv,uTexture1Size);vec2 center=vec2(0.5);float angle=length(uv-center)*6.2831*uIntensity;vec2 rot=vec2(cos(angle)*(uv.x-center.x)-sin(angle)*(uv.y-center.y)+center.x,sin(angle)*(uv.x-center.x)+cos(angle)*(uv.y-center.y)+center.y);gl_FragColor=mix(texture2D(uTexture1,rot),texture2D(uTexture2,rot),uProgress);}`;
            if (effect === 'ocean') return common + `void main(){vec2 uv=getCoverUV(vUv,uTexture1Size);float wave=sin(uv.x*20.+uProgress*6.2831)*cos(uv.y*20.+uProgress*6.2831)*.03*uIntensity;vec2 uvOff=uv+vec2(wave,wave);gl_FragColor=mix(texture2D(uTexture1,uvOff),texture2D(uTexture2,uvOff),uProgress);}`;
            if (effect === 'chrome') return common + `void main(){vec2 uv=getCoverUV(vUv,uTexture1Size);float gloss=abs(sin(uv.y*20.+uProgress*6.2831))*.2;vec3 tint=vec3(.9,.95,1.)+gloss*vec3(.1,.15,.2);vec4 blend=mix(texture2D(uTexture1,uv),texture2D(uTexture2,uv),uProgress);gl_FragColor=vec4(blend.rgb*tint,1.);}`;
            if (effect === 'pixel') return common + `void main(){vec2 uv1=getCoverUV(vUv,uTexture1Size);float pixelSize=mix(40.,5.,clamp(uIntensity,0.,1.))*(1.+sin(uProgress*3.1415)*.5);vec2 pUV1=floor(uv1*pixelSize)/pixelSize;float blend=smoothstep(0.,1.,uProgress);gl_FragColor=mix(texture2D(uTexture1,pUV1),texture2D(uTexture2,floor(getCoverUV(vUv,uTexture2Size)*pixelSize)/pixelSize),blend);}`;

            // Default fallback to liquid
            return common + `void main() { vec2 d=texture2D(uDisp,vUv).rg; float e=sin(uProgress*3.14159); vec2 o=(d-.5)*2.*.08*uIntensity*e; gl_FragColor=mix(texture2D(uTexture1,getCoverUV(vUv,uTexture1Size)+o*uProgress),texture2D(uTexture2,getCoverUV(vUv,uTexture2Size)-o*(1.-uProgress)),uProgress); }`;
        }

        // Texture loader
        const loader = new THREE.TextureLoader(); loader.crossOrigin = 'anonymous';
        const loadTexture = url => new Promise(resolve => {
            const finalUrl = url || SAFE_DISP;
            loader.load(finalUrl, t => {
                try { t.minFilter = THREE.LinearFilter; t.magFilter = THREE.LinearFilter; t.wrapS = t.wrapT = THREE.ClampToEdgeWrapping; t.generateMipmaps = false; t.userData = { size: new THREE.Vector2(t.image.width, t.image.height) }; } catch (e) { t.userData = { size: new THREE.Vector2(wrapper.clientWidth || window.innerWidth, wrapper.clientHeight || window.innerHeight) }; }
                resolve(t);
            }, undefined, () => { if (finalUrl !== SAFE_DISP) resolve(loadTexture(SAFE_DISP)); else resolve(null); });
        });

        // State variables
        let textures = [], dispTexture = null, material = null, mesh = null, current = 0, busy = false;

        // Prepare textures
        async function prepare() {
            for (let s of SLIDES) {
                const url = s && s.slide_image && s.slide_image.url ? s.slide_image.url : null;
                const t = await loadTexture(url);
                textures.push(t || new THREE.Texture(document.createElement('canvas')));
            }
            if (textures.length === 1) textures.push(textures[0].clone());
            try { dispTexture = await loadTexture(LOCAL_DISP); }
            catch (e) { try { dispTexture = await loadTexture(SAFE_DISP); } catch (e) { dispTexture = new THREE.Texture(document.createElement('canvas')); } }
            createMaterial();
            const firstTitle = SLIDES[0]?.slide_title || '';
            const firstSubtitle = SLIDES[0]?.slide_subtitle || '';
            animateOverlayText(firstTitle, firstSubtitle);
            gsap.to(navItems[0].querySelector('.lm-slide-progress-fill'), { width: '100%', duration: 1.2 });
            wrapper.classList.add('loaded');
        }

        // Create material and mesh
        function createMaterial() {
            const frag = getFragmentShader(EFFECT_TYPE_FINAL);
            material = new THREE.ShaderMaterial({
                vertexShader, fragmentShader: frag, transparent: true,
                uniforms: {
                    uTexture1: { value: textures[0] }, uTexture2: { value: textures[1] }, uDisp: { value: dispTexture },
                    uResolution: { value: new THREE.Vector2(wrapper.clientWidth || window.innerWidth, wrapper.clientHeight || window.innerHeight) },
                    uTexture1Size: { value: textures[0].userData.size }, uTexture2Size: { value: textures[1].userData.size },
                    uProgress: { value: 0 }, uIntensity: { value: CLIENT_LOW_POWER ? EFFECT_INTENSITY_FINAL * 0.6 : EFFECT_INTENSITY_FINAL }
                }
            });
            mesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material);
            scene.add(mesh);
            animate();
        }

        // Animation loop
        let rafId = null;
        function animate() { rafId = requestAnimationFrame(animate); try { renderer.render(scene, camera); } catch (e) { } }
        function startAnim() { if (!rafId) animate(); }
        function stopAnim() { if (rafId) { cancelAnimationFrame(rafId); rafId = null; } }

        // Overlay text GSAP animation
        function escapeHtml(str) { if (typeof str !== 'string') return ''; return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); }
        function animateOverlayText(title, subtitle) {
            titleEl.textContent = ''; subtitleEl.textContent = '';
            if (CLIENT_LOW_POWER || PREFERS_REDUCED) { titleEl.textContent = title; subtitleEl.textContent = subtitle; return; }
            const titleChars = (title || '').split('').map(l => l === ' ' ? ' ' : l);
            const subtitleChars = (subtitle || '').split('').map(l => l === ' ' ? ' ' : l);
            titleEl.innerHTML = titleChars.map(l => `<span class="char">${escapeHtml(l)}</span>`).join('');
            subtitleEl.innerHTML = subtitleChars.map(l => `<span class="char">${escapeHtml(l)}</span>`).join('');
            const tChars = titleEl.querySelectorAll('.char');
            const sChars = subtitleEl.querySelectorAll('.char');
            const effect = TITLE_ANIMATION_EFFECT;
            
            // GSAP animation text effects
            switch (effect) {
                case 'elastic_pop':
                    if (tChars.length) gsap.from(tChars, { scale: 0, opacity: 0, stagger: 0.05, duration: 0.8, ease: 'elastic.out(1,0.5)' });
                    if (sChars.length) gsap.from(sChars, { scale: 0, opacity: 0, stagger: 0.03, duration: 0.8, delay: 0.2, ease: 'elastic.out(1,0.5)' });
                    break;
                case 'wave':
                    if (tChars.length) gsap.from(tChars, { y: i => Math.sin(i / 2) * 30, opacity: 0, stagger: 0.05, duration: 0.8, ease: 'power2.out' });
                    if (sChars.length) gsap.from(sChars, { y: i => Math.sin(i / 2) * 20, opacity: 0, stagger: 0.03, duration: 0.8, delay: 0.2, ease: 'power2.out' });
                    break;
                case 'flip':
                    if (tChars.length) gsap.from(tChars, { rotationX: -90, opacity: 0, stagger: 0.04, duration: 0.7, transformOrigin: 'top center', ease: 'back.out(1.3)' });
                    if (sChars.length) gsap.from(sChars, { rotationX: -90, opacity: 0, stagger: 0.03, duration: 0.7, delay: 0.2, transformOrigin: 'top center', ease: 'back.out(1.3)' });
                    break;
                case 'scramble':
                    if (tChars.length) gsap.from(tChars, { x: () => Math.random() * 200 - 100, y: () => Math.random() * 200 - 100, rotation: () => Math.random() * 360 - 180, opacity: 0, stagger: 0.03, duration: 0.8, ease: 'power3.out' });
                    if (sChars.length) gsap.from(sChars, { x: () => Math.random() * 100 - 50, y: () => Math.random() * 100 - 50, rotation: () => Math.random() * 180 - 90, opacity: 0, stagger: 0.02, duration: 0.8, delay: 0.2, ease: 'power3.out' });
                    break;
                case 'cascade':
                    const tl = gsap.timeline();
                    if (tChars.length) tl.from(tChars, { y: -50, opacity: 0, stagger: 0.05, scale: 0.8, duration: 0.6, ease: 'power2.out' });
                    if (sChars.length) tl.from(sChars, { y: 50, opacity: 0, stagger: 0.03, scale: 0.9, duration: 0.6, delay: 0.0, ease: 'power2.out' }, "-=0.4");
                    break;
                case 'glitch':
                    if (tChars.length) gsap.to(tChars, { x: () => Math.random() * 6 - 3, y: () => Math.random() * 6 - 3, rotation: () => Math.random() * 5 - 2.5, repeat: 3, yoyo: true, duration: 0.1, stagger: 0.02 });
                    if (sChars.length) gsap.to(sChars, { x: () => Math.random() * 4 - 2, y: () => Math.random() * 4 - 2, rotation: () => Math.random() * 3 - 1.5, repeat: 3, yoyo: true, duration: 0.1, stagger: 0.02 });
                    break;
                default: // fade_slide
                    if (tChars.length) gsap.fromTo(tChars, { y: -50, opacity: 0 }, { y: 0, opacity: 1, stagger: 0.05, duration: 0.8, ease: 'power3.out' });
                    if (sChars.length) gsap.fromTo(sChars, { y: 50, opacity: 0 }, { y: 0, opacity: 1, stagger: 0.03, duration: 0.8, delay: 0.2, ease: 'power3.out' });
                    break;
            }
        }

        // Slide change
        function goToSlide(index) {
            if (busy || index === current) return;
            busy = true;
            navItems[current].classList.remove('active');
            navItems[index].classList.add('active');
            if (!PREFERS_REDUCED) {
                gsap.to(navItems[current].querySelector('.lm-slide-progress-fill'), { width: '0%', duration: 0.3 });
                gsap.to(navItems[index].querySelector('.lm-slide-progress-fill'), { width: '100%', duration: 1.2 });
            }
            material.uniforms.uTexture2.value = textures[index];
            material.uniforms.uTexture2Size.value = textures[index].userData.size;
            animateOverlayText(SLIDES[index]?.slide_title || '', SLIDES[index]?.slide_subtitle || '');
            gsap.to(numberEl, { opacity: 0, duration: 0.18, ease: 'power1.in', onComplete: () => { numberEl.textContent = String(index + 1).padStart(2, '0'); gsap.to(numberEl, { opacity: 1, duration: 0.36, ease: 'power2.out' }); } });
            gsap.to(material.uniforms.uProgress, {
                value: 1, duration: 1.2, ease: 'power2.inOut', onComplete: () => {
                    current = index;
                    material.uniforms.uTexture1.value = textures[current];
                    material.uniforms.uTexture1Size.value = textures[current].userData.size;
                    material.uniforms.uProgress.value = 0;
                    busy = false;
                }
            });
        }

        // Autoplay
        let autoplayTimer = null;
        function startAutoplay() { if (!AUTOPLAY_ENABLED || autoplayTimer) return; autoplayTimer = setInterval(() => { let next = current + 1; if (next >= SLIDES.length) { if (LOOP_SLIDES) next = 0; else return stopAutoplay(); } goToSlide(next); }, AUTOPLAY_DELAY); }
        function stopAutoplay() { if (autoplayTimer) { clearInterval(autoplayTimer); autoplayTimer = null; } }

        // Pause on hover / interaction
        if (PAUSE_ON_HOVER) { addTrackedListener(wrapper, 'mouseenter', () => stopAutoplay(), _wrapperHandlers); addTrackedListener(wrapper, 'mouseleave', () => startAutoplay(), _wrapperHandlers); }
        if (PAUSE_ON_INTERACTION) { navItems.forEach(item => addTrackedListener(item, 'click', () => stopAutoplay(), _navHandlers)); addTrackedListener(window, 'keydown', () => stopAutoplay(), _globalHandlers); }

        // Keyboard navigation
        addTrackedListener(window, 'keydown', (e) => { if (!wrapper.matches(':hover') && !wrapper.contains(document.activeElement)) return; if (e.code === 'ArrowRight' || e.code === 'Space') goToSlide((current + 1) % SLIDES.length); if (e.code === 'ArrowLeft') goToSlide((current - 1 + SLIDES.length) % SLIDES.length); }, _globalHandlers);

        // Click to next slide
        addTrackedListener(wrapper, 'click', (e) => { if (!e.target.closest('.lm-slides-navigation')) goToSlide((current + 1) % SLIDES.length); }, _wrapperHandlers);

        // Resize handling
        function resizeHandler() { const rect = wrapper.getBoundingClientRect(); const w = Math.max(1, Math.round(rect.width || window.innerWidth)); const h = Math.max(1, Math.round(rect.height || window.innerHeight)); renderer.setSize(w, h); if (material?.uniforms?.uResolution) material.uniforms.uResolution.value.set(w, h); }
        addTrackedListener(window, 'resize', resizeHandler, _globalHandlers);

        // IntersectionObserver
        const io = new IntersectionObserver(entries => { entries.forEach(en => { if (en.isIntersecting) startAnim(); else stopAnim(); }); }, { threshold: 0.1 });
        try { io.observe(wrapper); } catch (e) { }

        // Cleanup
        this.cleanup = () => {
            stopAutoplay(); stopAnim();
            io?.disconnect();
            _wrapperHandlers.forEach(h => h.el.removeEventListener(h.event, h.handler));
            _navHandlers.forEach(h => h.el.removeEventListener(h.event, h.handler));
            _globalHandlers.forEach(h => h.el.removeEventListener(h.event, h.handler));
            if (material) { material.dispose(); material = null; }
            if (mesh && mesh.geometry) { mesh.geometry.dispose(); scene.remove(mesh); mesh = null; }
            if (dispTexture) { dispTexture.dispose(); dispTexture = null; }
            textures.forEach(t => t.dispose()); textures = [];
            if (renderer) { try { renderer.forceContextLoss?.(); } catch (e) { } renderer.dispose(); renderer = null; }
        }
        // MutationObserver: scope to wrapper parent to detect removal
        const mutObs = new MutationObserver(() => { if (!document.body.contains(wrapper)) { cleanup(); mutObs.disconnect(); } });
        try { if (wrapper.parentNode) mutObs.observe(wrapper.parentNode, { childList: true }); } catch (e) { }

        // Start
        prepare().then(() => { startAnim(); startAutoplay(); });
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/liquid_morphology_slideshow.default', ($scope) => {
        elementorFrontend.elementsHandler.addHandler(LiquidSlideshowHandler, { $element: $scope });
    });
});