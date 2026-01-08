/**
 * Unified Features File
 * All features in one place
 * Compatible with Node.js and browser
 */

const features = [
    {
        name: 'Slider',
        description: 'Basic slider with autoplay, navigation, and responsive support.',
        init: () => {
            console.log('Initializing Slider...');
        }
    },
    {
        name: 'Parallax',
        description: 'Parallax effect on scroll or mouse move.',
        init: () => {
            console.log('Initializing Parallax...');
        }
    },
    {
        name: 'HoverEffect',
        description: 'Layered hover effect with 2D/3D transformations.',
        init: () => {
            console.log('Initializing HoverEffect...');
        }
    },
    {
        name: 'LiquidMorphology',
        description: 'WebGL liquid morphing effect for slides or images.',
        init: () => {
            console.log('Initializing LiquidMorphology...');
        }
    },
    {
        name: 'TextAnimation',
        description: 'GSAP-based text split and stagger animation.',
        init: () => {
            console.log('Initializing TextAnimation...');
        }
    },
    {
        name: 'ParticleEffect',
        description: 'Interactive particle animation responding to mouse or scroll.',
        init: () => {
            console.log('Initializing ParticleEffect...');
        }
    },
    {
        name: 'ShaderLighting',
        description: 'Shader/lighting highlight effect reacting to cursor or scroll.',
        init: () => {
            console.log('Initializing ShaderLighting...');
        }
    },
    {
        name: 'ClickStackAnimation',
        description: 'Click-to-stack animation for layered elements using GSAP.',
        init: () => {
            console.log('Initializing ClickStackAnimation...');
        }
    },
    {
        name: 'ImageHover3D',
        description: '3D layered hover effect for images with rotation and scale.',
        init: () => {
            console.log('Initializing ImageHover3D...');
        }
    }
];

// Node.js export for scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = features;
}

// Browser export for frontend
if (typeof window !== 'undefined') {
    window.TophiveFeatures = features;
}
