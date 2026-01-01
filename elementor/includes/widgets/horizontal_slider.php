<?php

namespace TophiveElementor\Widgets;

if (! defined("ABSPATH")) exit; // Exit if accessed directly


use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;

class Horizontal_Slider extends Widget_Nested_Base
{

  public function get_name()
  {
    return "horizontal_slider";
  }

  public function get_title()
  {
    return esc_html__("Horizontal Slider", TH_ELEMENTOR_SLUG);
  }

  public function get_icon()
  {
    return "eicon-mega-menu";
  }

  public function get_categories()
  {
    return ["th-header"];
  }

  public function get_keywords()
  {
    return ["horizontal", "slider"];
  }

  protected function register_controls()
  {
    $this->start_controls_section('Style', [
      'label' => __('Style', TH_ELEMENTOR_SLUG),
      'tab' => \Elementor\Controls_Manager::TAB_STYLE,
    ]);

    $this->add_control(
      'demo',
      [
        'label' => __('Demo', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::TEXT,
      ]
    );

    $this->end_controls_section();
  }


  protected function get_default_children_elements()
  {
    return [];
  }

  protected function get_default_repeater_title_setting_key()
  {
    return "";
  }

  protected function get_default_children_title()
  {
    return esc_html__("Slide #%d", TH_ELEMENTOR_SLUG);
  }

  protected function get_default_children_placeholder_selector()
  {
    return ".hslide_container";
  }

  protected function get_html_wrapper_class()
  {
    return "";
  }

  protected function create_container($index)
  {
    return [
      "elType" => "container",
      "settings" => [
        "_title" => sprintf(__("Container #%s", TH_ELEMENTOR_SLUG), $index),
        "content_width" => "full",
      ],
    ];
  }

  protected function render()
  {
    $children = $this->get_children(); ?>

    <style>
      .hslide_container_outer {
        overflow-x: hidden;
      }

      .hslide_container {
        display: flex;
        width: <?php echo count($children) * 100 ?>%;
      }

      .hslide {
        width: 100% !important;
      }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>

    <section class="hslide_container_outer">
      <div class="hslide_container">
        <?php foreach ($children as $index => $child): ?>
          <div class="hslide">
            <?php $child->print_element(); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>


    <script>
      window.addEventListener("DOMContentLoaded", function() {
        gsap.registerPlugin(ScrollTrigger)
        let scroller = document.querySelector(".hslide_container");
        let slides = gsap.utils.toArray(".hslide_container > div");

        let scrollTween = gsap.to(slides, {
          xPercent: -100 * (slides.length - 1),
          ease: "none",
          scrollTrigger: {
            trigger: ".hslide_container",
            pin: true,
            scrub: 1,
            snap: 1 / (slides.length - 1),
            end: "+=" + scroller.offsetWidth
          }
        });
      })
    </script>

  <?php
  }

  protected function content_template()
  {
    $id = uniqid("th");
  ?>
    <section class="hslide_container" id="<?php echo $id ?>">
    </section>
<?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Horizontal_Slider());
