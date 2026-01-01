<?php

namespace TophiveElementor\Widgets;

use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;

class Offcanvas extends Widget_Nested_Base
{

  public function get_name()
  {
    return "offcanvas";
  }

  public function get_title()
  {
    return esc_html__("Offcanvas", TH_ELEMENTOR_SLUG);
  }

  public function get_icon()
  {
    return 'eicon-off-canvas';
  }

  public function get_categories()
  {
    return ['th-header'];
  }

  public function get_keywords()
  {
    return ["offcanvas"];
  }

  public function get_script_depends()
  {
    return ["tophive-elementor-bundle"];
  }


  protected function register_controls()
  {
    $this->start_controls_section('content', ['label' => esc_html__('Content', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);
    $this->add_control(
      'open_icon',
      [
        'label' => esc_html__('Open Icon', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::ICONS,
        'default' => ['value' => 'fas fa-moon', 'library' => 'fa-solid'],
      ]
    );

    $this->add_control(
      'close_icon',
      [
        'label' => esc_html__('Colse Icon', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::ICONS,
        'default' => ['value' => 'fas fa-window-close', 'library' => 'fa-solid'],
      ]
    );
    $this->end_controls_section();

    $this->start_controls_section('icon_style_open', ['label' => esc_html__('Open Icon', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
    $this->add_control(
      'open_icon_color',
      [
        'label' => esc_html__("Color", TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_open i' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'open_icon_color_hover',
      [
        'label' => esc_html__("Hover Color", TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_open i:hover' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'open_icon_font',
        'selector' => '{{WRAPPER}} .offcanvas_button_open i',
      ]
    );

    $this->add_control(
      'open_icon_align',
      [
        'label' => esc_html__('Alignment', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'start' => [
            'title' => esc_html__('Left', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__('Center', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-center',
          ],
          'end' => [
            'title' => esc_html__('Right', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'default' => 'start',
        'toggle' => true,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_open' => 'justify-content: {{VALUE}};',
        ],
      ]
    );
    $this->end_controls_section();

    $this->start_controls_section('icon_style_close', ['label' => esc_html__('Close Icon', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
    $this->add_control(
      'close_icon_color',
      [
        'label' => esc_html__("Color", TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_close i' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'close_icon_color_hover',
      [
        'label' => esc_html__("Hover Color", TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_close i:hover' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'close_icon_font',
        'selector' => '{{WRAPPER}} .offcanvas_button_close i',
      ]
    );

    $this->add_control(
      'close_icon_align',
      [
        'label' => esc_html__('Alignment', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'start' => [
            'title' => esc_html__('Left', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__('Center', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-center',
          ],
          'end' => [
            'title' => esc_html__('Right', TH_ELEMENTOR_SLUG),
            'icon' => 'eicon-text-align-right',
          ],
        ],
        'default' => 'start',
        'toggle' => true,
        'selectors' => [
          '{{WRAPPER}} .offcanvas_button_close' => 'justify-content: {{VALUE}};',
        ],
      ]
    );
    $this->end_controls_section();

    $this->start_controls_section('offcanvas', ['label' => esc_html__('Offcanvas', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);

    $this->add_group_control(
      \Elementor\Group_Control_Background::get_type(),
      [
        'name' => 'offcanvas_bg',
        'types' => ['classic', 'gradient', 'video'],
        'selector' => "{{WRAPPER}} .offcanvas_container",
      ]
    );
    $this->add_group_control(
      \Elementor\Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'offcanvas_box_shadow',
        'selector' => "{{WRAPPER}} .offcanvas_container",
      ]
    );
    $this->add_control(
      'offcanvas_padding',
      [
        'label' => esc_html__('Padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'default' => [
          'top' => 10,
          'right' => 10,
          'bottom' => 10,
          'left' => 10,
          'unit' => 'px',
          'isLinked' => false,
        ],
        'selectors' => [
          "{{WRAPPER}} .offcanvas_container" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    $this->add_control(
      'offcanvas_width',
      [
        'label' => esc_html__('Width', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
            'step' => 5,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 300,
        ],
        'selectors' => [
          "{{WRAPPER}} .offcanvas_container" => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );
    $this->end_controls_section();
  }

  protected function get_default_children_elements()
  {
    return [$this->create_container(1)];
  }

  protected function get_default_repeater_title_setting_key()
  {
    return '';
  }

  protected function get_default_children_title()
  {
    return esc_html__('Offcanvas #%d', TH_ELEMENTOR_SLUG);
  }

  protected function get_default_children_placeholder_selector()
  {
    return '.offcanvas_content_area';
  }

  protected function get_html_wrapper_class()
  {
    return '';
  }

  protected function create_container($index)
  {
    return [
      'elType' => 'container',
      'settings' => [
        '_title' => sprintf(__('Container #%s', TH_ELEMENTOR_SLUG), $index),
        'content_width' => 'full',
      ],
    ];
  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
    $id = uniqid("th");
?>
    <style>
      .offcanvas_container {
        position: fixed;
        z-index: 10;
        right: 0;
        background: #fff;
        padding: 10px;
        box-shadow: -10px 0 25px rgba(0, 0, 0, 0.15), -5px 0 10px rgba(0, 0, 0, 0.08);
        width: 300px;
        top: 0px;
        bottom: 0px;
        transition: all 300ms cubic-bezier(0.23, 1, 0.32, 1);
        perspective: 2000px;
        display: none;
        transform: translateX(100px) scale(0.9);
        transform-origin: center right;
        opacity: 0;
        transition-behavior: allow-discrete;
        overflow: hidden;
        overflow-y: auto;
      }

      body:has(#wpadminbar) {
        .offcanvas_container {
          top: 32px;
        }
      }

      .offcanvas_button_open,
      .offcanvas_button_close {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 5px;
        font-size: 20px;

        i {
          transition: all 250ms ease-in;
        }
      }

      .th_offcanvas.open .offcanvas_container {
        display: block;
        transform: translateX(0) scale(1);
        opacity: 1;
      }

      @starting-style {
        .th_offcanvas.open .offcanvas_container {
          transform: translateX(100px) scale(0.9);
          opacity: 0;
        }
      }
    </style>

    <div id="<?php echo $id; ?>" class="th_offcanvas">
      <span class="offcanvas_button_open">
        <i class="<?php echo esc_attr($settings['open_icon']['value']); ?>" aria-hidden="true"></i>
      </span>
      <div class="offcanvas_container" data-id=<?php echo $id; ?>>
        <span class="offcanvas_button_close">
          <i class="<?php echo esc_attr($settings['close_icon']['value']); ?>" aria-hidden="true"></i>
        </span>
        <div class="offcanvas_content_area">
          <?php $children = $this->get_children(); ?>
          <?php foreach ($children as  $child) : ?>
            <?php $child->print_element(); ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <script>
      (() => {
        window.addEventListener("DOMContentLoaded", function() {
          let id = "<?php echo $id; ?>";
          let main = document.getElementById(id);
          if (!main) return;
          let container = main.querySelector(`[data-id=${id}]`);
          if (!container) return;

          let open = main.querySelector(".offcanvas_button_open i");
          let close = main.querySelector(".offcanvas_button_close i");
          open.addEventListener("click", () => main.classList.add("open"));
          close.addEventListener("click", () => main.classList.remove("open"));
        })
      })();
    </script>
  <?php
  }

  protected function content_template()
  {
    $id = uniqid("th");
  ?>
    <style>
      .offcanvas_container {
        position: fixed;
        z-index: 10;
        right: 0;
        background: #fff;
        padding: 10px;
        box-shadow: -10px 0 25px rgba(0, 0, 0, 0.15), -5px 0 10px rgba(0, 0, 0, 0.08);
        width: 300px;
        top: 0px;
        bottom: 0px;
        transition: all 300ms cubic-bezier(0.23, 1, 0.32, 1);
        perspective: 2000px;
        display: none;
        transform: translateX(100px) scale(0.9);
        transform-origin: center right;
        opacity: 0;
        transition-behavior: allow-discrete;
        overflow: hidden;
        overflow-y: auto;
      }

      .offcanvas_button_open,
      .offcanvas_button_close {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 5px;
        font-size: 20px;

        i {
          transition: all 250ms ease-in;
        }
      }

      .th_offcanvas.open .offcanvas_container {
        display: block;
        transform: translateX(0) scale(1);
        opacity: 1;
      }

      @starting-style {
        .th_offcanvas.open .offcanvas_container {
          transform: translateX(100px) scale(0.9);
          opacity: 0;
        }
      }
    </style>

    <div id="<?php echo $id; ?>" class="th_offcanvas">
      <span class="offcanvas_button_open">
        <i class="{{settings.open_icon.value}}" aria-hidden="true"></i>
      </span>
      <div class="offcanvas_container" data-id=<?php echo $id; ?>>
        <span class="offcanvas_button_close">
          <i class="{{settings.close_icon.value}}" aria-hidden="true"></i>
        </span>
        <div class="offcanvas_content_area"></div>
      </div>
    </div>

    <#
      let iframe=document.getElementById("elementor-preview-iframe");
      let id="<?php echo $id; ?>" ;

      iframe.contentWindow.postMessage( {
      type:"offcanvas_event",
      data:{ widget_setting:settings, custom_data:{ id } }
      })
      #>
  <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Offcanvas());
