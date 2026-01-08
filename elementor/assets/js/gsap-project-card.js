class GsapProjectCardHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        super.onInit();
        this.root = this.$element.find(".mi-grid-widget")[0];
        if (!this.root) return;

        this.settings = JSON.parse(this.root.dataset.settings || "{}");
        this.activeWebGLInstances = new Set();
        this.animationFrameId = null;

        this.start();
    }
    
    onDestroy() {
        super.onDestroy();
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
        }
        this.activeWebGLInstances.forEach(instance => {
            if (instance.ren) instance.ren.dispose();
            if (instance.scene) {
                instance.scene.traverse(child => {
                    if (child.isMesh) {
                        child.geometry?.dispose();
                        child.material?.dispose();
                    }
                });
            }
        });
        this.activeWebGLInstances.clear();
    }

    animateGridOverlay() {
        if (!this.settings.show_grid_overlay || !this.settings.grid_overlay_animate || typeof gsap === "undefined") return;
        const cols = this.root.querySelectorAll(".mi-grid-col");
        if (!cols.length) return;
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
            cols.forEach((c) => (c.style.opacity = ".5"));
            return;
        }
        gsap.set(cols, { opacity: 0, scaleY: 0, transformOrigin: "top" });
        gsap.to(cols, {
            opacity: 0.5, scaleY: 1,
            duration: this.settings.grid_overlay_duration || 1.2,
            stagger: this.settings.grid_overlay_stagger || 0.08,
            ease: this.settings.grid_overlay_ease || "power2.out",
        });
    }

    initWebGLEffect() {
        if (!this.settings.enable_effect || typeof gsap === "undefined" || typeof THREE === "undefined") return;
        const containers = Array.from(this.root.querySelectorAll("[data-mi-lens]"));
        if (!containers.length) return;

        const vtx = "varying vec2 v_uv;void main(){v_uv=uv;gl_Position=vec4(position,1.0);}";
        const frg = `precision highp float;varying vec2 v_uv;uniform sampler2D u_texture;uniform vec2 u_mouse;uniform float u_time;uniform vec2 u_resolution;uniform float u_radius,u_speed,u_imageAspect,u_swirl,u_invertStrength,u_brushPulse,u_softness;uniform bool u_reveal;uniform vec3 u_tintA,u_tintB;float H(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453123);}float N(vec2 p){vec2 i=floor(p),f=fract(p);float a=H(i),b=H(i+vec2(1.,0.)),c=H(i+vec2(0.,1.)),d=H(i+vec2(1.,1.));vec2 u=f*f*(3.-2.*f);return mix(a,b,u.x)+(c-a)*u.y*(1.-u.x)+(d-b)*u.x*u.y;}float FBM(vec2 p){float v=0.,a=0.5;mat2 m=mat2(1.6,1.2,-1.2,1.6);for(int i=0;i<5;i++){v+=a*N(p);p=m*p;a*=0.5;}return v;}vec2 SW(vec2 p,vec2 c,float r,float s){vec2 d=p-c;float t=smoothstep(r,0.,length(d))*s;float sn=sin(t),cs=cos(t);return c+mat2(cs,-sn,sn,cs)*d;}void main(){float screenAspect=u_resolution.x/u_resolution.y;float ratio=u_imageAspect/screenAspect;vec2 uvC=v_uv;uvC.x*=screenAspect;vec2 mC=u_mouse;mC.x*=screenAspect;vec2 _p=SW(uvC,mC,u_radius,u_swirl*u_brushPulse);vec2 texC=vec2(mix(0.5-0.5/ratio,0.5+0.5/ratio,_p.x/screenAspect),_p.y);vec4 tex=texture2D(u_texture,texC);float lum=dot(tex.rgb,vec3(0.299,0.587,0.114));vec3 base=mix(tex.rgb,mix(u_tintA,u_tintB,lum),0.12);float marble=FBM(v_uv*2.+u_time*(0.1*u_speed));float dist=distance(uvC,mC)+(marble-0.5)*0.15;float msk=u_radius>0.001?step(dist,u_radius):0.;if(u_softness>0.0001)msk=1.-smoothstep(max(0.,u_radius-u_softness),u_radius,dist);vec3 inv=1.-base;vec3 color=u_reveal?mix(base,inv,msk*u_invertStrength*u_brushPulse):mix(inv,base,msk*u_invertStrength*u_brushPulse);gl_FragColor=vec4(color,1.0);}`;

        let last = 0;
        const tick = (t) => {
            this.animationFrameId = requestAnimationFrame(tick);
            const dt = t - last;
            last = t;
            this.activeWebGLInstances.forEach((s) => {
                if (!s || !s.inView) return;
                s.lerp.lerp(s.target, 0.12);
                s.uniforms.u_mouse.value.copy(s.lerp);
                if (s.hover) s.uniforms.u_time.value += (dt || 16.7) * 0.001;
                s.ren.render(s.scene, s.cam);
            });
        };
        this.animationFrameId = requestAnimationFrame(tick);

        containers.forEach((container) => {
            const img = container.querySelector(".mi-img");
            const src = container.dataset.imgSrc || (img ? img.src : "");
            if (!src) return;

            const siteHost = location.hostname;
            const imgHost = new URL(src, location.origin).hostname;
            if (this.settings.wp_media_only && siteHost !== imgHost) {
                if (img) img.style.display = "block";
                return;
            }

            const loader = new THREE.TextureLoader();
            loader.setCrossOrigin("anonymous");
            loader.load(src, (texture) => {
                try {
                    if (THREE.SRGBColorSpace) texture.colorSpace = THREE.SRGBColorSpace;
                } catch (_) {}
                texture.minFilter = texture.magFilter = THREE.LinearFilter;
                texture.generateMipmaps = false;
                if (img) img.style.display = "none";

                const scene = new THREE.Scene();
                const cam = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
                const ren = new THREE.WebGLRenderer({ alpha: true, antialias: false, powerPreference: "high-performance" });
                const r = container.getBoundingClientRect();
                ren.setPixelRatio(1);
                ren.setSize(r.width, r.height);
                container.appendChild(ren.domElement);

                const uniforms = {
                    u_texture: { value: texture },
                    u_mouse: { value: new THREE.Vector2(0.5, 0.5) },
                    u_time: { value: 0 },
                    u_resolution: { value: new THREE.Vector2(r.width, r.height) },
                    u_radius: { value: this.settings.brush_radius },
                    u_speed: { value: this.settings.marble_speed },
                    u_imageAspect: { value: texture.image ? texture.image.width / texture.image.height : 1 },
                    u_swirl: { value: this.settings.swirl_strength },
                    u_invertStrength: { value: this.settings.invert_strength },
                    u_brushPulse: { value: 0 },
                    u_softness: { value: this.settings.edge_softness },
                    u_reveal: { value: this.settings.effect_mode === "reveal" },
                    u_tintA: { value: new THREE.Color(...this.settings.tintA) },
                    u_tintB: { value: new THREE.Color(...this.settings.tintB) },
                };

                const mesh = new THREE.Mesh(
                    new THREE.PlaneGeometry(2, 2),
                    new THREE.ShaderMaterial({ uniforms, vertexShader: vtx, fragmentShader: frg, depthTest: false, depthWrite: false })
                );
                scene.add(mesh);

                const instanceState = { scene, cam, ren, uniforms, target: new THREE.Vector2(0.5, 0.5), lerp: new THREE.Vector2(0.5, 0.5), inView: true, hover: false };
                this.activeWebGLInstances.add(instanceState);

                if (window.IntersectionObserver) {
                    new IntersectionObserver((es) => {
                        es.forEach((e) => {
                            instanceState.inView = e.isIntersecting;
                            if (!e.isIntersecting) uniforms.u_brushPulse.value = 0;
                        });
                    }, { threshold: 0.1 }).observe(container);
                }
                if (window.ResizeObserver) {
                    new ResizeObserver(() => {
                        const r = container.getBoundingClientRect();
                        ren.setSize(r.width, r.height);
                        uniforms.u_resolution.value.set(r.width, r.height);
                    }).observe(container);
                }

                container.addEventListener("pointerenter", () => {
                    instanceState.hover = true;
                    gsap.to(uniforms.u_brushPulse, { value: 1, duration: 0.35, overwrite: true });
                });
                container.addEventListener("pointerleave", () => {
                    instanceState.hover = false;
                    gsap.to(uniforms.u_brushPulse, { value: 0, duration: 0.5, overwrite: true });
                });
                container.addEventListener("pointermove", (e) => {
                    const r = container.getBoundingClientRect();
                    instanceState.target.set((e.clientX - r.left) / r.width, 1 - (e.clientY - r.top) / r.height);
                });
            }, undefined, () => {
                if (img) img.style.display = "block";
            });
        });
    }

    start() {
        const startAnimations = () => {
            this.animateGridOverlay();
            this.initWebGLEffect();
        };

        if (window.IntersectionObserver) {
            const io = new IntersectionObserver((es) => {
                if (es.some((e) => e.isIntersecting)) {
                    startAnimations();
                    io.disconnect();
                }
            }, { threshold: 0.1 });
            io.observe(this.root);
        } else {
            startAnimations();
        }
    }
}

window.addEventListener("elementor/frontend/init", () => {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/gsap_project_card.default",
        ($scope) => {
            elementorFrontend.elementsHandler.addHandler(GsapProjectCardHandler, { $element: $scope });
        }
    );
});
