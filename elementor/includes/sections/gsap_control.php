<?php

use Elementor\Element_Base;
use Elementor\Controls_Manager;

// This file contain GSAP control. This control get injected in elementor container widget advance tab

class TH_GSAP_Control_Inject
{
  public function __construct()
  {
    add_action('elementor/element/container/section_layout/after_section_end', [$this, 'add_gsap_control'], 10, 2);
  }

  public function add_gsap_control(Element_Base $widget_instance, $args)
  {
    //start a new section
    $widget_instance->start_controls_section(
      "gsap_control_section",
      [
        "label" => esc_html__("Advance Effect", TH_ELEMENTOR_SLUG),
        "tab" => Controls_Manager::TAB_ADVANCED
      ]
    );

    $widget_instance->add_control(
      'widget_title',
      [
        'label' => esc_html__('Title', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__('Default title', TH_ELEMENTOR_SLUG),
        'placeholder' => esc_html__('Type your title here', TH_ELEMENTOR_SLUG),
      ]
    );

    //end section
    $widget_instance->end_controls_section();
  }
}

new TH_GSAP_Control_Inject();
