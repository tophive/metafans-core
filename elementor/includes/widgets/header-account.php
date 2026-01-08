<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Tophive_User_Account_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tophive-header-account-widget';
    }

    public function get_title() {
        return __('User Account', 'text-domain');
    }

    public function get_icon() {
        return 'eicon-nerd';
    }

    public function get_categories() {
        return ['th-header'];
    }

    public function get_script_depends(): array {
		return [ 'elementor-frontend' ];
	}

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('show_avatar', [
            'label'        => __('Show Avatar Icon', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ]);


        $this->add_control('avatar_type', [
            'label'   => __('Avatar Type', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'icon'  => __('Icon', 'text-domain'),
                'image' => __('Image (WP Default)', 'text-domain')
            ],
            'default' => 'icon',
            'condition' => ['show_avatar' => 'yes']
        ]);

        $this->add_control('avatar_icon', [
            'label'   => __('Choose Icon', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value' => 'far fa-user',
                'library' => 'regular',
            ],
            'condition' => ['avatar_type' => 'icon']
        ]);

        $this->add_control('avatar_image', [
            'label'   => __('WP Default Avatar', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::RAW_HTML,
            'raw'     => __('Displays the WordPress default user avatar.', 'text-domain'),
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            'condition' => ['avatar_type' => 'image']
        ]);

        $this->add_control('logged_in_text', [
            'label'       => __('Logged In Text', 'text-domain'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __('My Account', 'text-domain'),
        ]);

        $this->add_control('logged_out_text', [
            'label'       => __('Logged Out Text', 'text-domain'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __('Login / Register', 'text-domain'),
        ]);

        $this->add_control('after_login_redirect', [
            'label'   => __('After Login Redirect URL', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => site_url('/account')
        ]);

        $this->end_controls_section();
        $this->start_controls_section('account_dropdown_section', [
            'label' => __('Dropdown', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('show_dropdown', [
            'label'        => __('Show Dropdown', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ]);
        $this->add_control('show_dropdown_icon', [
            'label'        => __('Show Dropdown Icon', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ]);

        $this->add_control('dropdown_icon', [
            'label'   => __('Choose Icon', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-caret-down',
                'library' => 'solid',
            ],
            'condition' => ['avatar_type' => 'icon'],
            'recommended' => [
                'fa-solid' => [
                    'caret-down',
                    'angle-down',
                    'chevron-down',
                    'sort-down',
                    'caret-square-down',
                ]
            ],
        ]);

        $this->add_control('dropdown_items', [
            'label' => __('Dropdown Items (Logged In)', 'text-domain'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'label',
                    'label' => __('Label', 'text-domain'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __('Dashboard', 'text-domain'),
                ],
                [
                    'name' => 'url',
                    'label' => __('URL', 'text-domain'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'default' => [ 'url' => '#' ],
                ]
            ],
            'title_field' => '{{{ label }}}',
            'condition' => [
                'show_dropdown' => 'yes'
            ]
        ]);

        $this->add_control('show_logout', [
            'label'        => __('Show Logout Link', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'condition' => [
                'show_dropdown' => 'yes'
            ]
        ]);
        $this->end_controls_section();

        $this->start_controls_section('login_reg_section', [
            'label' => __('Login / register from', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('login_form_type', [
            'label'   => __('Login Form Display', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'modal'      => __('Modal', 'text-domain'),
                'sidedrawer' => __('Sidedrawer', 'text-domain')
            ],
            'default' => 'modal'
        ]);

        $this->add_control('show_login_form_content', [
            'label'        => __('Show login form', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'no',
        ]);
        $this->add_control('login_reg_heading', [
            'label'        => __('Heading title', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::TEXT,
            'default'      => 'SignIn',
        ]);
        $this->add_control('login_reg_heading_description', [
            'label'        => __('Header description', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::TEXTAREA,
            'placeholder' => esc_html__( 'Type your login form description here', 'textdomain' ),
        ]);
        $this->add_control('show_signup_form_content', [
            'label'        => __('Show Signup form', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'no',
        ]);
        $this->add_control('signup_form_heading', [
            'label'        => __('Signup text', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::TEXT,
            'default'      => 'Sign Up',
        ]);
        $this->add_control('signup_form_heading_description', [
            'type'         => \Elementor\Controls_Manager::TEXTAREA,
            'placeholder' => esc_html__( 'Type your login form description here', 'textdomain' ),
        ]);

        $this->add_control('show_forgot_pass_form_content', [
            'label'        => __('Show Forgot Pass form', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'no',
        ]);
        $this->add_control('login_forgot_pass_heading', [
            'label'        => __('Forgot password text', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::TEXT,
            'default'      => 'Forgot password?',
        ]);
        $this->add_control('login_forgot_pass_heading_description', [
            'type'         => \Elementor\Controls_Manager::TEXTAREA,
            'placeholder' => esc_html__( 'Type your login form description here', 'textdomain' ),
        ]);
        $this->end_controls_section();

        $this->start_controls_section('account_container_section', [
            'label' => __('Container', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);


        $this->start_controls_tabs(
            'account_container_style_tabs'
        );
        
        $this->start_controls_tab(
            'account_container_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );
        $this->add_control('account_container_bg', [
            'label' => __('Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_control(
            'account_text_color',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .account-text' => 'color: {{VALUE}}'],
                'default' => '#000'
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'account_container_border',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'account_container_box_shadow',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger',
			]
		);
        $this->end_controls_tab();
        $this->start_controls_tab(
            'account_container_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );
        $this->add_control('account_container_bg_hover', [
            'label' => __('Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger:hover' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_control(
            'account_text_color_hover',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-trigger:hover .account-text' => 'color: {{VALUE}}'],
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'account_container_border_hover',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger:hover',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'account_container_box_shadow_hover',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger:hover',
			]
		);
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_control(
			'account_more_options',
			[
				'label' => esc_html__( '', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
        
        $this->add_responsive_control(
            'account_container_padding',
            [
                'label' => __('Spacing', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'account_container_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        $this->add_control('account_container_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 5],
            'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
        $this->start_controls_section('icon_section', [
            'label' => __('Icon/Avatar', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('th_custom_icon_size', [
            'label' => __('Icon Size', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 10, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 15],
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon svg' => 'width: {{SIZE}}{{UNIT}};'
            ],
        ]);

        $this->add_control('th_custom_icon_color', [
            'label' => __('Icon Color', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFF',
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon svg' => 'fill: {{VALUE}};'
            ],
        ]);
        $this->add_control('th_custom_icon_bg', [
            'label' => __('Icon Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#CF1AF8',
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_responsive_control(
            'th_custom_icon_padding',
            [
                'label' => __('Spacing', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 14,
					'right' => 15,
					'bottom' => 14,
					'left' => 15,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'th_custom_icon_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        

        $this->add_control('th_custom_icon_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger .avatar-icon,{{WRAPPER}} .user-account-widget .user-trigger .avatar' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
        $this->start_controls_section('dd_icon_section', [
            'label' => __('Dropdown Icon', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('dd_icon_size', [
            'label' => __('Icon Size', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 10, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 10],
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger .dropdown-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .user-account-widget .user-trigger .dropdown-icon svg' => 'width: {{SIZE}}{{UNIT}};'
            ],
        ]);

        $this->add_control('dd_icon_color', [
            'label' => __('Icon Color', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#000',
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger .dropdown-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .user-account-widget .user-trigger .dropdown-icon svg' => 'fill: {{VALUE}};'
            ],
        ]);

        $this->add_responsive_control(
            'dd_icon_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger .dropdown-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section('text_typography_section', [
            'label' => __('Account text', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'account_text_typography',
                'label' => __('Account text typography', 'your-text-domain'),
                'selector' => '{{WRAPPER}} .account-text',
            ]
        );

        $this->add_responsive_control(
            'account_text_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 0,
					'right' => 10,
					'bottom' => 0,
					'left' => 10,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .account-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
       
        $this->end_controls_section();
        $this->start_controls_section('account_dropdown_style', [
            'label' => __('Dropdown', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('account_dropdown_bg', [
            'label' => __('Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#FFF',
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-dropdown' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'account_dd_border',
				'selector' => '{{WRAPPER}} .user-account-widget .user-dropdown',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'account_dd_box_shadow',
				'selector' => '{{WRAPPER}} .user-account-widget .user-dropdown',
			]
		);

        $this->add_responsive_control(
            'account_dd_box_padding',
            [
                'label' => __('Dropdown Padding', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 5,
					'right' => 5,
					'bottom' => 5,
					'left' => 5,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'account_dd_box_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 10,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_control('account_dd_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 5],
            'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->add_control(
			'account_dd_items',
			[
				'label' => esc_html__( 'Dropdown items', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

        $this->add_responsive_control(
            'account_dd_box_padding_item',
            [
                'label' => __('Dropdown Item Padding', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
					'unit' => 'px',
					'isLinked' => true,
				],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );


        $this->add_control('account_dd_item_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 5],
            'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);


        $this->start_controls_tabs(
            'account_container_dd_item_style_tab'
        );
        
        $this->start_controls_tab(
            'account_container_dd_item_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );

        $this->add_control(
            'account_dd_text_color',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a' => 'color: {{VALUE}}'],
                'default' => '#555'
            ]
        );

        $this->add_control(
            'account_dd_item_bg_color',
            [
                'label' => __('Background color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a' => 'background-color: {{VALUE}}'],
                'default' => '#FFF'
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'account_container_dd_item_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );

        $this->add_control(
            'account_dd_text_color_hover',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a:hover' => 'color: {{VALUE}}'],
                'default' => '#000'
            ]
        );

        $this->add_control(
            'account_dd_item_bg_color_hover',
            [
                'label' => __('Background color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-dropdown li a:hover' => 'background-color: {{VALUE}}'],
                'default' => '#e1e1e1'
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'account_dd_typography',
                'label' => __('Dropdown text typography', 'your-text-domain'),
                'selector' => '{{WRAPPER}} .user-account-widget .user-dropdown li a',
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section('account_login_register_style', [
            'label' => __('Login/register', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control(
            'account_login_register_form_spacing',
            [
                'label' => __('Form Padding', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
					'top' => 10,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
					'unit' => 'px',
					'isLinked' => false,
				],
                'selectors' => ['.login-register-wrapper .login-register-form-container form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        // FORM HEADING STYLES
        $this->add_control(
            'account_login_register_heading_section',
            [
                'label' => esc_html__('Heading', 'text-domain'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before', // Optional: Adds visual separator line above the heading
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'account_login_reg_title_typo',
                'label' => __('Title typography', 'your-text-domain'),
                'selector' => '.login-register-form-container .login-header h2',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'account_login_reg_desc_typo',
                'label' => __('Description typography', 'your-text-domain'),
                'selector' => '.login-register-form-container .login-header .desc',
            ]
        );

        $this->add_responsive_control(
            'account_login_reg_heading_padding',
            [
                'label' => __('Padding', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['.login-register-form-container .login-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'account_login_reg_heading_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['.login-register-form-container .login-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'account_login_reg_heading_border',
				'selector' => '.login-register-form-container .login-header',
			]
		);

        $this->add_control(
            'account_login_reg_heading_title_color',
            [
                'label' => __('Title color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '.login-register-form-container .login-header h2' => 'color: {{VALUE}}'],
            ]
        );

        $this->add_control(
            'account_login_reg_heading_desc_color',
            [
                'label' => __('Description color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '.login-register-form-container .login-header .desc' => 'color: {{VALUE}}'],
            ]
        );


        // FORM INPUT STYLES
        Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'input', '.login-register-form-container form input', '', false);
        
        // FORM BUTTON STYLES
        Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'button', '.login-register-form-container form button', '', false);

        $this->end_controls_tab();

        

        $this->end_controls_tabs();

        
        // FORM TEXT STYLES
        $this->add_control(
            'account_login_reg_text_styles',
            [
                'label' => esc_html__('Text styles', 'text-domain'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before', // Optional: Adds visual separator line above the heading
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'account_login_reg_text_typography',
                'label' => __('Typography', 'your-text-domain'),
                'selector' => '.login-register-form-container form span, .login-register-form-container form p, .login-register-form-container form a',
            ]
        );
        $this->add_control(
            'account_login_reg_text_color',
            [
                'label' => __('Color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '.login-register-form-container form span, .login-register-form-container form p, .login-register-form-container form a' => 'color: {{VALUE}}'],
            ]
        );

        $this->end_controls_section();




        $this->start_controls_section('account_logged_out_state_section', [
            'label' => __('Logged out state', 'text-domain'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('switch_logged_out', [
            'label'        => __('Switch logged out', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => '',
        ]);
        $this->add_control('show_logged_out_icon', [
            'label'        => __('Show loggedout icon', 'text-domain'),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'condition' => ['switch_logged_out' => 'yes']
        ]);

        $this->add_control('logged_out_icon', [
            'label'   => __('Choose Icon', 'text-domain'),
            'type'    => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'value' => 'far fa-user',
                'library' => 'regular',
            ],
            'condition' => ['switch_logged_out' => 'yes', 'show_logged_out_icon' => 'yes']
        ]);

        $this->add_control('logged_out_text', [
            'label'       => __('Logged Out Text', 'text-domain'),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __('Login / Register', 'text-domain'),
        ]);

        $this->start_controls_tabs(
            'logged_out_buttons_tabs'
        );
        
        $this->start_controls_tab(
            'logged_out_buttons_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'textdomain' ),
            ]
        );
        $this->add_control('login_register_btn_bg', [
            'label' => __('Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, 
                {{WRAPPER}} .user-account-widget .user-trigger.logged-out' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_control(
            'login_register_btn_text_color',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview .account-text, 
                        {{WRAPPER}} .user-account-widget .user-trigger.logged-out .account-text' => 'color: {{VALUE}}'],
                'default' => '#FFF'
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'login_register_btn_border_normal',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, 
                    {{WRAPPER}} .user-account-widget .user-trigger.logged-out',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'login_register_btn_box_shadow',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, 
                    {{WRAPPER}} .user-account-widget .user-trigger',
			]
		);
        $this->end_controls_tab();
        $this->start_controls_tab(
            'logged_out_buttons_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'textdomain' ),
            ]
        );
        $this->add_control('login_register_btn_bg_hover', [
            'label' => __('Background', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#000',
            'selectors' => [
                '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview:hover, 
                {{WRAPPER}} .user-account-widget .user-trigger.logged-out:hover' => 'background-color: {{VALUE}};'
            ],
        ]);

        $this->add_control(
            'login_register_btn_text_color_hover',
            [
                'label' => __('Text color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['body:not(.logged-in) {{WRAPPER}} .user-trigger:hover .account-text,
                    {{WRAPPER}} .user-account-widget .user-trigger.logged-out:hover .account-text' => 'color: {{VALUE}}'],
                'default' => '#FFF'
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'login_register_btn_border_hover',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview:hover, 
                                {{WRAPPER}} .user-account-widget .user-trigger.logged-out:hover',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'login_register_btn_box_shadow_hover',
				'selector' => '{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview:hover, {{WRAPPER}} .user-account-widget .user-trigger.logged-out:hover',
			]
		);
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_control(
			'account_more_options',
			[
				'label' => esc_html__( '', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
        
        $this->add_responsive_control(
            'login_register_btn_padding',
            [
                'label' => __('Spacing', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, {{WRAPPER}} .user-account-widget .user-trigger.logged-out' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'login_register_btn_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, {{WRAPPER}} .user-account-widget .user-trigger.logged-out' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );

        $this->add_control('login_register_btn_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 5],
            'selectors' => ['{{WRAPPER}} .user-account-widget .user-trigger.loggedout-preview, {{WRAPPER}} .user-account-widget .user-trigger.logged-out' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $is_logged_in = is_user_logged_in();
        $user = wp_get_current_user();
        $show_login_form = $settings['show_login_form_content'];
        ?>
        <div class="user-account-widget">
            <?php //var_dump($is_logged_in); ?>
            <div class="user-trigger <?php echo 'yes' === $settings['switch_logged_out'] ? 'loggedout-preview' : ''; ?>">
                <?php if ($is_logged_in && 'yes' !== $settings['switch_logged_out']) : ?>
                    <?php if ($settings['show_avatar'] === 'yes') : ?>
                        <?php if ($settings['avatar_type'] === 'image') : ?>
                            <?php echo get_avatar($user->ID, 32); ?>
                        <?php else : ?>
                            <span class="avatar-icon">
                                <?php \Elementor\Icons_Manager::render_icon( $settings['avatar_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                            <span class="account-text"><?php esc_html_e($settings['logged_in_text']); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if ('yes' === $settings['show_logged_out_icon']) : ?>
                        <span class="avatar-icon">
                            <?php \Elementor\Icons_Manager::render_icon( $settings['logged_out_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                        </span>
                    <?php endif; ?>
                    <span class="account-text"><?php esc_html_e($settings['logged_out_text']); ?></span>
                <?php endif; ?>

                
                <?php if ($settings['show_dropdown_icon'] === 'yes' && $is_logged_in && 'yes' !== $settings['switch_logged_out'] ) : ?>
                    <span class="dropdown-icon">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['dropdown_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>
            </div>
            <?php if ($is_logged_in) : ?>
                <?php if($settings['dropdown_items'] || $settings['show_logout'] === 'yes'): ?>
                    <ul class="user-dropdown">
                        <?php foreach ($settings['dropdown_items'] as $item) : ?>
                            <li><a href="<?php echo esc_url($item['url']['url']); ?>"><?php echo esc_html($item['label']); ?></a></li>
                        <?php endforeach; ?>
                        <?php if ($settings['show_logout'] === 'yes') : ?>
                            <li>
                                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                    </svg>Logout
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            <?php else :
                $settings['show_login_form_content'] = 'no';
                $this->get_login_form($settings);
                endif;
                    
                if( 'yes' === $show_login_form && \Elementor\Plugin::instance()->editor->is_edit_mode() ){
                    $this->get_login_form($settings);
                }
            ?>
            
        </div>
        <?php
    }

    public function get_login_form($settings){
        ?>
        <div class="login-register-wrapper" data-type="<?php echo esc_attr($settings['login_form_type']); ?>" data-show="<?php echo esc_attr($settings['show_login_form_content']);?>">
            <div class="login-register-overlay"></div>
            <div class="login-register-form-container">
                <div id="login-form">
                    <div class="login-header">
                        <div>
                            <h2><?php echo $settings['login_reg_heading']; ?></h2>
                            <span class="desc"><?php echo $settings['login_reg_heading_description']; ?></span>
                        </div>
                        <span class="login-close">&times;</span>
                    </div>
                    <form method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
                        <input type="text" name="log" placeholder="Username or Email" required>
                        <input type="password" name="pwd" placeholder="Password" required>
                        <div class="form-remember-section">
                            <label><input type="checkbox" /><span>Remember me</span></label>
                            <?php if($settings['show_forgot_pass_form_content']): ?>
                                <a href="#" id="show-forgot-password">Forgot password?</a>
                            <?php endif; ?>
                        </div>
                        <button type="submit">Login</button>
                        <?php if($settings['show_signup_form_content']): ?>
                            <p class="switch-form">
                                Don't have an account? <a href="#" id="show-signup">Sign up</a>
                            </p>
                        <?php endif; ?>
                    </form>
                </div>
                <?php if($settings['show_signup_form_content']): ?>
                    <div id="signup-form" style="display: none;">
                        <div class="login-header">
                            <div>
                                <h2><?php echo $settings['signup_form_heading']; ?></h2>
                                <span class="desc"><?php echo $settings['signup_form_heading_description']; ?></span>
                            </div>
                            <span class="login-close">&times;</span>
                        </div>
                        <form id="signup-user-form">
                            <input type="text" id="signup-username" name="user_login" placeholder="Username" required>
                            <input type="email" id="signup-email" name="user_email" placeholder="Email" required>
                            <input type="password" id="signup-password" name="user_password" placeholder="Password" required>
                            <button type="submit">Sign Up</button>
                            <p class="switch-form">
                                <a href="#" id="back-to-login">Back to Login</a>
                            </p>
                            <p id="signup-message"></p> <!-- To show errors or success -->
                        </form>
                    </div>
                <?php endif; ?>
                <?php if($settings['show_forgot_pass_form_content']): ?>
                    <div id="forgot-password-form" style="display: none;">
                        <div class="login-header">
                            <div>
                                <h2><?php echo $settings['login_forgot_pass_heading']; ?></h2>
                                <span class="desc"><?php echo $settings['login_forgot_pass_heading_description']; ?></span>
                            </div>
                            <span class="login-close">&times;</span>
                        </div>
                        <form method="post" action="<?php echo esc_url(wp_lostpassword_url()); ?>">
                            <input type="email" name="user_email" placeholder="Enter your email" required>
                            <button type="submit">Reset Password</button>
                            <p class="switch-form">
                                <a href="#" id="back-to-login-2">Back to Login</a>
                            </p>
                        </form>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php
    }

}
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Tophive_User_Account_Widget());
