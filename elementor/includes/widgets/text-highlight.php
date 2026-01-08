<?php
// version - 2.8
namespace My_Core_Plugin\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit;

class Text_Highlight extends Widget_Base {

    public function get_name() {
        return 'text_highlight';
    }

    public function get_title() {
        return __('Text Highlight', 'my-core-plugin');
    }

    public function get_icon() {
        return 'eicon-heading';
    }

    public function get_categories() {
        return ['th-general'];
    }

    protected function _register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'my-core-plugin'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'html_tag',
            [
                'label' => __('HTML Tag', 'my-core-plugin'),
                'type' => Controls_Manager::SELECT,
                'default' => 'p',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'p'  => 'Paragraph',
                    'span' => 'Span',
                ],
            ]
        );

        $this->add_control(
            'paragraph',
            [
                'label' => __('Text', 'my-core-plugin'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'This is a sample paragraph. Highlight specific words automatically!',
                'placeholder' => __('Type your text here', 'my-core-plugin'),
            ]
        );

        $this->add_control(
            'words_to_highlight',
            [
                'label' => __('Words to Highlight', 'my-core-plugin'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => 'Separate multiple words with commas. Example: highlight, specific, words',
                'default' => 'highlight, specific, words',
            ]
        );

        $this->add_control(
            'highlight_color',
            [
                'label' => __('Highlight Background', 'my-core-plugin'),
                'type' => Controls_Manager::SELECT,
                'default' => 'yellow',
                'options' => [
                    'yellow' => 'Yellow',
                    'green'  => 'Green',
                    'pink'   => 'Pink',
                    'blue'   => 'Blue',
                    'custom_gradient' => 'Custom Gradient',
                ],
            ]
        );

        $this->add_control(
            'highlight_gradient_colors',
            [
                'label' => __('Gradient Colors', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff6ec4',
                'selectors' => [],
                'condition' => [
                    'highlight_color' => 'custom_gradient',
                ],
                'description' => 'Add multiple gradient colors separated by comma. Example: #ff6ec4,#7873f5,#4ade80',
            ]
        );

        $this->add_control(
            'highlight_font_color',
            [
                'label' => __('Highlight Font Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __('Hover Animation', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'gradient_animation',
            [
                'label' => __('Enable Gradient Animation', 'my-core-plugin'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'my-core-plugin'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'my-core-plugin'),
                'selector' => '{{WRAPPER}} .text-highlight',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Font Color', 'my-core-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .text-highlight' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $html_tag = $settings['html_tag'];
        $paragraph = $settings['paragraph'];
        $words = array_map('trim', explode(',', $settings['words_to_highlight']));
        $color_class = $settings['highlight_color'] . '-highlight';
        $hover_class = ($settings['hover_animation'] === 'yes') ? 'hover-animate' : '';
        $highlight_font_color = $settings['highlight_font_color'];
        $gradient_class = ($settings['gradient_animation'] === 'yes') ? 'gradient-text' : '';

        // Custom gradient handling
        $custom_gradient_css = '';
        if ($settings['highlight_color'] === 'custom_gradient' && !empty($settings['highlight_gradient_colors'])) {
            $colors = explode(',', $settings['highlight_gradient_colors']);
            $gradient_css = implode(',', $colors);
            $custom_gradient_css = 'background: linear-gradient(90deg,' . $gradient_css . '); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% 100%; animation: gradientMove 3s linear infinite;';
            $gradient_class = ''; // disable built-in gradient if custom
        }

        foreach ($words as $word) {
            if (!empty($word)) {
                $paragraph = preg_replace(
                    '/\b' . preg_quote($word, '/') . '\b/i',
                    '<span class="' . esc_attr($color_class . ' ' . $hover_class . ' ' . $gradient_class) . '" style="color:' . esc_attr($highlight_font_color) . ';' . esc_attr($custom_gradient_css) . '">' . $word . '</span>',
                    $paragraph
                );
            }
        }

        // Inline CSS
        echo '<style>
        .text-highlight { margin: 0; } 
        .yellow-highlight { display:inline-block; background: linear-gradient(100deg,#ffffaf00 1%,#ffffaf 2.5%,#ffffaf80 5.7%,#ffffaf1a 93%,#ffffafb4 95%,#ffffaf00 98%), linear-gradient(182deg,#ffffaf00,#ffffaf4d 8%,#ffffaf00 15%); }
        .green-highlight { display:inline-block; background: linear-gradient(100deg,#b8ffaf00 1%,#b8ffaf 2.5%,#b8ffaf80 5.7%,#b8ffaf1a 93%,#b8ffafb4 95%,#b8ffaf00 98%), linear-gradient(182deg,#b8ffaf00,#b8ffaf4d 8%,#b8ffaf00 15%); }
        .pink-highlight { display:inline-block; background: linear-gradient(100deg,#ffafd400 1%,#ffafd4 2.5%,#ffafd480 5.7%,#ffafd41a 93%,#ffafd4b4 95%,#ffafd400 98%), linear-gradient(182deg,#ffafd400,#ffafd44d 8%,#ffafd400 15%); }
        .blue-highlight { display:inline-block; background: linear-gradient(100deg,#afd7ff00 1%,#afd7ff 2.5%,#afd7ff80 5.7%,#afd7ff1a 93%,#afd7ffb4 95%,#afd7ff00 98%), linear-gradient(182deg,#afd7ff00,#afd7ff4d 8%,#afd7ff00 15%); }
        .hover-animate { transition: background-size 0.4s ease; background-size: 200% 100%; }
        .hover-animate:hover { background-size: 100% 100%; }

        .gradient-text {
            background: linear-gradient(90deg, #ff6ec4, #7873f5, #4ade80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% 100%;
            animation: gradientMove 3s linear infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
        </style>';

        echo '<' . esc_html($html_tag) . ' class="text-highlight">' . $paragraph . '</' . esc_html($html_tag) . '>';
    }

    protected function _content_template() {
        ?>
        <#
        var htmlTag = settings.html_tag;
        var paragraph = settings.paragraph;
        var words = settings.words_to_highlight.split(',').map(function(w){ return w.trim(); });
        var colorClass = settings.highlight_color + '-highlight';
        var hoverClass = (settings.hover_animation === 'yes') ? 'hover-animate' : '';
        var highlightFontColor = settings.highlight_font_color;
        var gradientClass = (settings.gradient_animation === 'yes') ? 'gradient-text' : '';
        var customGradientCss = '';

        if(settings.highlight_color === 'custom_gradient' && settings.highlight_gradient_colors){
            var colors = settings.highlight_gradient_colors.split(',');
            customGradientCss = 'background: linear-gradient(90deg,' + colors.join(',') + '); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size:200% 100%; animation: gradientMove 3s linear infinite;';
            gradientClass = '';
        }

        _.each(words, function(word){
            if(word.length > 0){
                var reg = new RegExp('\\b'+ word +'\\b', 'gi');
                paragraph = paragraph.replace(reg, '<span class="'+colorClass+' '+hoverClass+' '+gradientClass+'" style="color:'+highlightFontColor+';'+customGradientCss+'">'+word+'</span>');
            }
        });
        #>
        <{{{ htmlTag }}} class="text-highlight">{{{ paragraph }}}</{{{ htmlTag }}}>
        <?php
    }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Text_Highlight());
