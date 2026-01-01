<?php

namespace TophiveElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Advance_Url extends Widget_Base
{
  public function get_name()
  {
    return "advance_url";
  }

  public function get_title()
  {
    return esc_html__("Advance url", TH_ELEMENTOR_SLUG);
  }

  public function get_icon()
  {
    return 'eicon-url';
  }

  public function get_categories()
  {
    return ['th-header'];
  }

  public function get_keywords()
  {
    return ['url', 'advance', 'advance-url'];
  }

  protected function register_controls()
  {
    $this->start_controls_section('content', ['label' => esc_html__('Content', TH_ELEMENTOR_SLUG), 'tab' => Controls_Manager::TAB_CONTENT]);

    $this->add_control(
      'title',
      [
        'label' => esc_html__('Title', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__('Payment Links No-code payments', TH_ELEMENTOR_SLUG),
        'placeholder' => esc_html__('Type your title here', TH_ELEMENTOR_SLUG),
      ]
    );

    $this->add_control(
      'sub_title',
      [
        'label' => esc_html__('Subtitle', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__('No-code payments', TH_ELEMENTOR_SLUG),
        'placeholder' => esc_html__('Type your subtitle here', TH_ELEMENTOR_SLUG),
      ]
    );

    $this->add_control(
      'icon',
      [
        'label' => esc_html__('Separator Icon', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::ICONS,
        'default' => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid'],
      ]
    );

    $this->add_control(
      'link',
      [
        'label' => esc_html__('Link', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => ['url' => '', 'is_external' => true, 'nofollow' => true],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section('style', ['label' => esc_html__('Typography', TH_ELEMENTOR_SLUG), 'tab' => Controls_Manager::TAB_STYLE]);
    $this->start_controls_tabs('style_tabs');

    $this->start_controls_tab('style_normal_tab', ['label' => esc_html__('Normal', TH_ELEMENTOR_SLUG)]);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography', 'label' => esc_html__('Title', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url h4']);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'subtitle_typography', 'label' => esc_html__('Subtitle', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url p']);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'separetor_typography', 'label' => esc_html__('Separator', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url span i']);
    $this->end_controls_tab();

    $this->start_controls_tab('style_hover_tab', ['label' => esc_html__('Hover', TH_ELEMENTOR_SLUG)]);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'title_typography_hover', 'label' => esc_html__('Title', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url:hover h4']);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'subtitle_typography_hover', 'label' => esc_html__('Subtitle', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url:hover p']);
    $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'separetor_typography_hover', 'label' => esc_html__('Separator', TH_ELEMENTOR_SLUG), 'selector' => '{{WRAPPER}} .advance-url:hover span i']);
    $this->end_controls_tab();

    $this->end_controls_tabs();
    $this->end_controls_section();

    $this->start_controls_section('spacing', ['label' => esc_html__('Spacing', TH_ELEMENTOR_SLUG), 'tab' => Controls_Manager::TAB_STYLE]);
    $this->add_responsive_control(
      'gap',
      [
        'label' => esc_html__('Gap', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'range' => [
          'px' => ['min' => 0, 'max' => 100, 'step' => 1],
          '%' => ['min' => 0, 'max' => 100],
        ],
        'default' => ['unit' => 'px', 'size' => 10,],
        'selectors' => [
          '{{WRAPPER}} .advance-url' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'padding',
      [
        'label' => esc_html__('Padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'default' => ['top' => 5, 'right' => 10, 'bottom' => 5, 'left' => 10, 'unit' => 'px', 'isLinked' => false,],
        'selectors' => [
          '{{WRAPPER}} .advance-url' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'border',
      [
        'label' => esc_html__('Border', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', 'rem', 'custom'],
        'default' => ['top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px', 'isLinked' => false,],
        'selectors' => [
          '{{WRAPPER}} .advance-url' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    $this->add_control(
      'padding_title',
      [
        'label' => esc_html__('Title padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'em', 'isLinked' => false,],
        'selectors' => [
          '{{WRAPPER}} .advance-url h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    $this->add_control(
      'padding_subtitle',
      [
        'label' => esc_html__('Subtitle padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'em', 'isLinked' => false,],
        'selectors' => [
          '{{WRAPPER}} .advance-url p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    $this->add_control(
      'padding_separetor',
      [
        'label' => esc_html__('Separator padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'em', 'isLinked' => false,],
        'selectors' => [
          '{{WRAPPER}} .advance-url > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section('color', ['label' => esc_html__('Color', TH_ELEMENTOR_SLUG), 'tab' => Controls_Manager::TAB_STYLE]);
    $this->start_controls_tabs('style_tabs_color');

    $this->start_controls_tab('style_normal_tab_color', ['label' => esc_html__('Normal', TH_ELEMENTOR_SLUG)]);
    $this->add_control(
      'bg_color',
      [
        'label' => esc_html__('background Color', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'default' => "#f6f9fc",
        'selectors' => [
          '{{WRAPPER}} .advance-url' => 'background-color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'title_color',
      [
        'label' => esc_html__('Title', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .advance-url h4' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'subtitle_color',
      [
        'label' => esc_html__('Subtitle', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .advance-url p' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'separetor_color',
      [
        'label' => esc_html__('Separator', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'default' => "#000",
        'selectors' => [
          '{{WRAPPER}} .advance-url span i' => 'color: {{VALUE}}',
          '{{WRAPPER}} .advance-url span span' => 'background-color: {{VALUE}}',
        ],
      ]
    );
    $this->end_controls_tab();

    $this->start_controls_tab('style_hover_tab_color', ['label' => esc_html__('Hover', TH_ELEMENTOR_SLUG)]);
    $this->add_control(
      'bg_color_hover',
      [
        'label' => esc_html__('background Color', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .advance-url:hover' => 'background-color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'title_color_hover',
      [
        'label' => esc_html__('Title', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .advance-url:hover h4' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'subtitle_color_hover',
      [
        'label' => esc_html__('Subtitle', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .advance-url:hover p' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'separetor_color_hover',
      [
        'label' => esc_html__('Separator', TH_ELEMENTOR_SLUG),
        'type' => Controls_Manager::COLOR,
        'default' => "#000",
        'selectors' => [
          '{{WRAPPER}} .advance-url:hover span i' => 'color: {{VALUE}}',
          '{{WRAPPER}} .advance-url:hover span span' => 'background-color: {{VALUE}}',
        ],
      ]
    );
    $this->end_controls_tab();

    $this->end_controls_tabs();
    $this->end_controls_section();
  }

  public function render()
  {
    $settings = $this->get_settings_for_display();
    $id = uniqid("th");
?>
    <style>
      #<?php echo $id ?> {
        /* font-size: 16px; */
        display: flex;
        align-items: center;
        transition: all 200ms ease-in;

        &>* {
          margin: 0;
          padding: 0;
          transition: all 200ms ease-in;
        }

        span i {
          opacity: 0;
          transform: translateX(-30%);
          transition: all 200ms ease-in;
          display: block;
        }

        span span {
          transition: all 200ms ease-in;
        }

        &>span {
          position: relative;
        }

        &>span span {
          width: 5px;
          height: 5px;
          border-radius: 50%;
          display: block;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        }

        &:hover span i {
          opacity: 1;
          transform: translateX(0);
        }

        &:hover span span {
          opacity: 0;
        }
      }

      .advance-url {
        padding: 5px;

        h4,
        p,
        i {
          font-size: 12px;
        }
      }
    </style>

    <a href="" class="advance-url" id="<?php echo esc_attr($id) ?>">
      <h4><?php echo $settings["title"]; ?></h4>
      <span class="">
        <i class="<?php echo esc_attr($settings['icon']['value']); ?>" aria-hidden="true"></i>
        <span></span>
      </span>
      <p><?php echo $settings["sub_title"]; ?></p>
    </a>

<?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Advance_Url());
