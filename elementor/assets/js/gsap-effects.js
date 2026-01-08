/**
 * GSAP Effects Library
 * Version: 1.0
 * Author: Tophive
 * Description: Reusable GSAP animation presets for Elementor & WP widgets
 */

(function($){
  if (typeof gsap === 'undefined') {
    console.error('GSAP not found. Please include gsap.min.js first.');
    return;
  }

  // Register ScrollTrigger if loaded
  if (typeof ScrollTrigger !== 'undefined') gsap.registerPlugin(ScrollTrigger);

  // Global reusable effect function
  window.gsapEffect = function(effect, target, options = {}) {
    if (!target) return;
    const base = { duration: 1, ease: 'power2.out', ...options };

    switch (effect) {
      // ✨ Basic
      case 'fadeIn':        gsap.from(target, { opacity: 0, ...base }); break;
      case 'fadeInUp':      gsap.from(target, { opacity: 0, y: 40, ...base }); break;
      case 'fadeInDown':    gsap.from(target, { opacity: 0, y: -40, ...base }); break;
      case 'fadeInLeft':    gsap.from(target, { opacity: 0, x: -40, ...base }); break;
      case 'fadeInRight':   gsap.from(target, { opacity: 0, x: 40, ...base }); break;
      case 'zoomIn':        gsap.from(target, { opacity: 0, scale: 0.7, ease: 'back.out(1.7)', ...base }); break;
      case 'zoomOut':       gsap.to(target, { opacity: 0, scale: 0.7, ease: 'back.in(1.7)', ...base }); break;
      case 'flipInX':       gsap.from(target, { rotationX: 90, opacity: 0, transformOrigin: 'center', ease: 'back.out(1.7)', ...base }); break;
      case 'flipInY':       gsap.from(target, { rotationY: 90, opacity: 0, transformOrigin: 'center', ease: 'back.out(1.7)', ...base }); break;

      // ⚡ Bounce & Elastic
      case 'bounceIn':      gsap.from(target, { opacity: 0, y: 100, ease: 'bounce.out', ...base }); break;
      case 'elasticPop':    gsap.from(target, { scale: 0, opacity: 0, ease: 'elastic.out(1,0.4)', ...base }); break;

      // 🌊 Wave / Float
      case 'wave':          gsap.to(target, { y: 10, repeat: -1, yoyo: true, ease: 'sine.inOut', duration: 1.5 }); break;
      case 'float':         gsap.to(target, { y: -20, repeat: -1, yoyo: true, ease: 'sine.inOut', duration: 2 }); break;

      // 🌀 Parallax Scroll
      case 'parallax':      
        if (typeof ScrollTrigger === 'undefined') return console.warn('ScrollTrigger missing.');
        gsap.to(target, { yPercent: -20, ease: 'none', scrollTrigger: { trigger: target, scrub: true } });
        break;

      // 💥 Glow / Blur
      case 'glowPulse':
        gsap.to(target, { boxShadow: '0 0 25px rgba(255,255,255,0.8)', repeat: -1, yoyo: true, duration: 1 });
        break;

      case 'blurReveal':
        gsap.fromTo(target,
          { filter: 'blur(10px)', opacity: 0 },
          { filter: 'blur(0px)', opacity: 1, ease: 'power2.out', ...base });
        break;

      // Default
      default:
        console.warn(`Unknown GSAP effect: "${effect}"`);
    }
  };

  // Elementor-aware initializer for GSAP effects
  jQuery(window).on('elementor/frontend/init', () => {
      const applyGsapEffects = ($scope) => {
          $scope.find('[data-gsap]').each(function() {
              const el = jQuery(this);
              const effect = el.data('gsap');
              if (!el.data('gsap-initialized')) {
                  window.gsapEffect(effect, el);
                  el.data('gsap-initialized', true);
              }
          });
      };

      elementorFrontend.hooks.addAction('frontend/element_ready/global', applyGsapEffects);
  });

  // Specific effect for Testimonial Pro slider
  // global gsapEffects module
  window.gsapEffects = (function(){
    // Safety: check gsap exists
    if(typeof gsap==='undefined'){console.warn('GSAP missing.');return{};}
  
    // smooth fade + scale effect
    function slideFadeScale(prev,next){
      if(!prev || !next) return;
      const tl=gsap.timeline({defaults:{duration:0.6,ease:'power2.out'}});
      tl.set(next,{zIndex:3})
        .to(prev,{autoAlpha:0,scale:0.95},0)
        .fromTo(next,{autoAlpha:0,scale:1.05},{autoAlpha:1,scale:1},0)
        .set(prev,{zIndex:1});
    }
  
    // future effects (you can add more)
    function slideFlip(prev,next){
      if(!prev || !next) return;
      const tl=gsap.timeline({defaults:{duration:0.8,ease:'back.out(1.7)'}});
      tl.to(prev,{rotationY:90,autoAlpha:0},0)
        .fromTo(next,{rotationY:-90,autoAlpha:0},{rotationY:0,autoAlpha:1},0)
        .set(prev,{rotationY:0});
    }
  
    return {slideFadeScale,slideFlip};
  })();
})(jQuery);
