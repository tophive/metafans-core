<?php


namespace TophiveElementor\Widgets;

use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;


class Mega_Menu extends Widget_Nested_Base
{
  public function get_name()
  {
    return "mega_menu";
  }

  public function get_title()
  {
    return esc_html__("Mega Menu", TH_ELEMENTOR_SLUG);
  }

  public function get_icon()
  {
    return 'eicon-mega-menu';
  }

  public function get_categories()
  {
    return ['th-header'];
  }

  public function get_keywords()
  {
    return ['menu', 'mega', 'mega-menu'];
  }

  protected function get_all_pages($full_info = false)
  {
    $_pages = get_pages();
    $pages = [];

    if ($full_info) {
      foreach ($_pages as $page) {
        $pages[$page->ID] = ["title" => $page->post_title, "permalink" => get_the_permalink($page->ID)];
      }
    } else {
      foreach ($_pages as $page) {
        $pages[$page->ID] = $page->post_title;
      }
    }

    return $pages;
  }

  public function get_script_depends()
  {
    return ["tophive-elementor-bundle"];
  }

  protected function register_controls()
  {
    $this->start_controls_section('top_menus', ['label' => esc_html__('Menus', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);

    $this->add_control(
      'top_menu',
      [
        'label' => esc_html__('Top Menu', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'default' => [],
        'fields' => [
          [
            'name' => "page_id",
            'label' => esc_html__('Page', TH_ELEMENTOR_SLUG),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => $this->get_all_pages(),
          ],
          [
            'name' => "hasDropdown",
            'label' => esc_html__('Is Mega Menu', TH_ELEMENTOR_SLUG),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', TH_ELEMENTOR_SLUG),
            'label_off' => esc_html__('No', TH_ELEMENTOR_SLUG),
            'return_value' => 'yes',
            'default' => 'yes',
          ],
          [
            'name' => "active",
            'label' => esc_html__('Active', TH_ELEMENTOR_SLUG),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', TH_ELEMENTOR_SLUG),
            'label_off' => esc_html__('No', TH_ELEMENTOR_SLUG),
            'return_value' => 'yes',
            'default' => 'no',
          ],
          [
            'name' => "width",
            'label' => esc_html__('Width (only editor)', TH_ELEMENTOR_SLUG),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 2000,
            'step' => 5,
            'default' => 400,
          ],
          [
            'name' => "height",
            'label' => esc_html__('Height (only editor)', TH_ELEMENTOR_SLUG),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 2000,
            'step' => 5,
            'default' => 400,
          ],
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section('nav_root_style', ['label' => esc_html__('Navbar', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);

    $this->add_group_control(
      \Elementor\Group_Control_Background::get_type(),
      [
        'name' => 'nav_bg',
        'types' => ['classic', 'gradient', 'video'],
        'selector' => '{{WRAPPER}} .navRoot, {{WRAPPER}} #mega-menu-mobile',
      ]
    );

    $this->add_responsive_control(
      'rootlink_alignment',
      [
        'label' => esc_html__('Link Alignment', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'center',
        'options' => [
          '' => esc_html__('Default', TH_ELEMENTOR_SLUG),
          'center' => esc_html__('Center', TH_ELEMENTOR_SLUG),
          'start'  => esc_html__('Start', TH_ELEMENTOR_SLUG),
          'end' => esc_html__('End', TH_ELEMENTOR_SLUG),
        ],
        'selectors' => [
          '{{WRAPPER}} .navSection.primary' => 'justify-content: {{VALUE}};',
          '{{WRAPPER}} #mega-menu-mobile' => 'justify-content: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'navroot_padding',
      [
        'label' => esc_html__('Navbar padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', 'rem'],
        'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false],
        'selectors' => [
          '{{WRAPPER}} .navRoot' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} #mega-menu-mobile' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'navroot_border_radius',
      [
        'label' => esc_html__('Navbar Border Radius', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', 'rem'],
        'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => false],
        'selectors' => [
          '{{WRAPPER}} .navRoot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} #mega-menu-mobile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section('root_link_style', ['label' => esc_html__('Link', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);

    $this->add_group_control(
      \Elementor\Group_Control_Typography::get_type(),
      [
        'name' => 'rootlink_typography',
        'selector' => '{{WRAPPER}} .rootLink, #mobile-content-sidebar .rootLink',
      ]
    );

    $this->add_responsive_control(
      'rootlink_color',
      [
        'label' => esc_html__('Link Color', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => ['{{WRAPPER}} .rootLink, #mobile-content-sidebar .rootLink' => 'color: {{VALUE}}'],
      ]
    );

    $this->add_responsive_control(
      'rootlink_padding',
      [
        'label' => esc_html__('Link Padding', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', 'rem'],
        'default' => ['top' => 0, 'right' => 10, 'bottom' => 0, 'left' => 10, 'unit' => 'px', 'isLinked' => false],
        'selectors' => [
          '{{WRAPPER}} .rootLink, #mobile-content-sidebar .rootLink' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section('popover_style', ['label' => esc_html__('Popover', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);

    $this->add_group_control(
      \Elementor\Group_Control_Background::get_type(),
      ['name' => 'dropdown_bg', 'types' => ['classic', 'gradient', 'video'], 'selector' => '{{WRAPPER}} .dropdownBackground']
    );

    $this->add_group_control(
      \Elementor\Group_Control_Box_Shadow::get_type(),
      ['name' => 'popover_box_shadow', 'selector' => '{{WRAPPER}} .dropdownBackground']
    );

    $this->add_control(
      'popover_border_radius',
      [
        'label' => esc_html__('Border Radius', TH_ELEMENTOR_SLUG),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', 'rem'],
        'default' => ['top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px', 'isLinked' => true],
        'selectors' => [
          '{{WRAPPER}} .dropdownBackground' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section('popover_arrow_style', ['label' => esc_html__('Popover Arrow', TH_ELEMENTOR_SLUG), 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);

    $this->add_group_control(
      \Elementor\Group_Control_Background::get_type(),
      ['name' => 'dropdown_arrow_bg', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dropdownArrow']
    );

    $this->add_group_control(
      \Elementor\Group_Control_Box_Shadow::get_type(),
      ['name' => 'popover_arrow_box_shadow', 'selector' => '{{WRAPPER}} .dropdownArrow']
    );

    $this->end_controls_section();
  }

  protected function get_default_children_elements()
  {
    return [];
  }

  protected function get_default_repeater_title_setting_key()
  {
    return '';
  }

  protected function get_default_children_title()
  {
    return esc_html__('Menu #%d', TH_ELEMENTOR_SLUG);
  }

  protected function get_default_children_placeholder_selector()
  {
    return '.dropdownContainer';
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

  protected function drop_down_id($index, $repeter_items)
  {
    return $repeter_items[$index]["_id"];
  }

  protected function root_link_class($repeter_item)
  {
    if (!empty($repeter_item["hasDropdown"])) return "rootLink hasDropdown";
    return "rootLink"; //remove hard coded hasDropdown
  }

  protected function line_height($line_height)
  {
    if (empty($line_height)) return "";
    return esc_attr("--root-link-line-height:{$line_height}px;");
  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
?>

    <section id="mega-menu" class="mega-menu">
      <div class="globalNav noDropdownTransition">
        <ul class="navRoot">
          <li class="navSection primary">
            <?php foreach ($settings["top_menu"] as $index => $menu):  ?>
              <a
                href="<?php echo esc_url(get_the_permalink($menu['page_id'])) ?>"
                data-dropdown="<?php echo esc_attr($this->drop_down_id($index, $settings['top_menu'])); ?>"
                class="<?php echo esc_attr($this->root_link_class($menu)); ?>">
                <?php echo get_the_title($menu["page_id"]); ?>
              </a>
            <?php endforeach; ?>
          </li>
        </ul>

        <div class="dropdownRoot">
          <div class="dropdownBackground"></div>
          <div class="dropdownArrow"></div>
          <div class="dropdownContainer">
            <?php $children = $this->get_children(); ?>
            <?php foreach ($children as $index => $child) : ?>
              <div class="dropdownSection left" data-dropdown="<?php echo esc_attr($this->drop_down_id($index, $settings['top_menu'])); ?>">
                <div class="dropdownContent">
                  <?php $child->print_element(); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section id="mega-menu-mobile">
      <svg width="16" height="10" viewBox="0 0 16 10" class="open">
        <title>Open mobile navigation</title>
        <g fill="#000" fill-rule="evenodd">
          <rect y="8" width="16" height="2" rx="1"></rect>
          <rect y="4" width="16" height="2" rx="1"></rect>
          <rect width="16" height="2" rx="1"></rect>
        </g>
      </svg>

      <div id="mobile-content-sidebar" class="close fade_way">
        <div class="mobile-menu-content-header">
          <div class="back">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M9.5 12L5.5 8L9.5 4" stroke="#000" stroke-width="1.75" stroke-linecap="square"></path>
            </svg>
            <span>Back</span>
          </div>

          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="close">
            <title>Close mobile navigation</title>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00061 6.58508L12.5926 1.99312C12.9835 1.60223 13.6172 1.60223 14.0081 1.99312C14.399 2.384 14.399 3.01775 14.0081 3.40864L9.41678 7.99995L14.0081 12.5913C14.399 12.9822 14.399 13.6159 14.0081 14.0068C13.6172 14.3977 12.9835 14.3977 12.5926 14.0068L8.00061 9.41482L3.40864 14.0068C3.01775 14.3977 2.384 14.3977 1.99312 14.0068C1.60223 13.6159 1.60223 12.9822 1.99312 12.5913L6.58443 7.99995L1.99312 3.40864C1.60223 3.01775 1.60223 2.384 1.99312 1.99312C2.384 1.60223 3.01775 1.60223 3.40864 1.99312L8.00061 6.58508Z" fill="#4F5B76"></path>
          </svg>
        </div>

        <div class="mobile-menu-links">
          <?php foreach ($settings["top_menu"] as $index => $menu):  ?>
            <a
              href="<?php echo esc_url(get_the_permalink($menu['page_id'])) ?>"
              data-dropdown="<?php echo esc_attr($this->drop_down_id($index, $settings['top_menu'])); ?>"
              class="<?php echo esc_attr($this->root_link_class($menu)); ?>">
              <?php echo get_the_title($menu["page_id"]); ?>

              <?php if (!empty($menu["hasDropdown"])): ?>
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29316 2.90864C4.90228 2.51775 4.90228 1.884 5.29316 1.49312C5.68405 1.10223 6.3178 1.10223 6.70869 1.49312L13.0084 7.79284C13.3989 8.18337 13.3989 8.81653 13.0084 9.20706L6.70869 15.5068C6.3178 15.8977 5.68405 15.8977 5.29316 15.5068C4.90228 15.1159 4.90228 14.4822 5.29316 14.0913L10.8845 8.49995L5.29316 2.90864Z" fill="#3F4B66"></path>
                </svg>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="mobile-menu-content">
          <?php $children = $this->get_children(); ?>
          <?php foreach ($children as $index => $child) : ?>
            <div class="con" data-dropdown="<?php echo esc_attr($this->drop_down_id($index, $settings['top_menu'])); ?>">
              <div>
                <?php $child->print_element(); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  <?php
  }

  protected function content_template()
  {
    $all_pages = json_encode($this->get_all_pages(full_info: true));
    $id = uniqid("th");
  ?>

    <#
      let line_height=settings.rootlink_typography_line_height.size ? `--root-link-line-height:${settings.rootlink_typography_line_height.size}px` : "" ;
      #>

      <section class="mega-menu" id="<?php echo $id ?>" style="{{{line_height}}}">
        <div class="globalNav noDropdownTransition">
          <ul class="navRoot">
            <li class="navSection primary"> </li>
          </ul>

          <div class="dropdownRoot">
            <div class="dropdownBackground"></div>
            <div class="dropdownArrow"></div>
            <div class="dropdownContainer"></div>
          </div>
        </div>
      </section>

      <#
        let iframe=document.getElementById("elementor-preview-iframe");
        let pages=<?php echo $all_pages; ?>;
        let id="<?php echo $id; ?>" ;

        iframe.contentWindow.postMessage( {
        type:"mega_menu_event",
        data:{ widget_setting:settings, custom_data:{ id, pages } }
        })
        #>

    <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Mega_Menu());
