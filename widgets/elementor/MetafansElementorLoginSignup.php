<?php 
	

	namespace METAFANSCORE\widgets\elementor;
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	/**
	 **
	 * Metafans elementor team class
	 * Since Version 1.0.0
	 * @package wordpress
	 * @subpackage MasterClass
	 *
	 *
	 */
	class MetafansElementorLoginSignup extends \Elementor\Widget_base
	{
		public function get_title(){
			return esc_html__( 'Login Signup', WP_TH_CORE_SLUG );
		}
		public function get_name(){
			return 'th-login-signup-block';
		}
		public function get_icon(){
			return 'eicon-user-circle-o';
		}
		public function get_categories(){
	        return [ WP_TH_CORE_SLUG ];
	    }
		public function get_keywords() {
			return [ 'login', 'signin', 'signup' ];
		}
		protected function register_controls(){

			/*
			** Team Content Section
			*
			* @_register_controls
			*/

			$this->start_controls_section(
				'th_team_section',
				[
					'label' => esc_html__( 'Signin/Signup', WP_TH_CORE_SLUG ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);
			$this->add_control(
				'th_login_signup_padding',
				[
					'label' => esc_html__( 'Padding', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .tophive-popup-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_control(
				'th_login_signup_margin',
				[
					'label' => esc_html__( 'Margin', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .tophive-popup-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_control(
				'border_rad',
				[
					'label' => esc_html__( 'Border Radius', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .tophive-popup-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'th_login_signup_box_shadow',
					'label' => esc_html__( 'Box Shadow', WP_TH_CORE_SLUG ),
					'selector' => '{{WRAPPER}} .tophive-popup-content-wrapper',
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' => 'background',
					'label' => esc_html__( 'Background', 'plugin-domain' ),
					'types' => [ 'classic'],
					'selector' => '{{WRAPPER}} .tophive-popup-content-wrapper',
				]
			);
			$this->add_control(
				'th_login_signup_login_title',
				[
					'label' => esc_html__( 'Login Form title', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Default title', WP_TH_CORE_SLUG ),
					'placeholder' => esc_html__( 'Type your title here', WP_TH_CORE_SLUG ),
				]
			);
			$this->add_control(
				'th_login_signup_login_recover_pass_title',
				[
					'label' => esc_html__( 'Recover Password Form title', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Default title', WP_TH_CORE_SLUG ),
					'placeholder' => esc_html__( 'Type your title here', WP_TH_CORE_SLUG ),
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'heading_typography',
					'label' => esc_html__( 'Heading typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .tophive-popup-content-wrapper h3',
				]
			);
			$this->add_control(
				'heading_typography_color',
				[
					'label' => esc_html__( 'Heading Typography Color', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .tophive-popup-content-wrapper h3' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'th_login_signup_register_title',
				[
					'label' => esc_html__( 'Register title', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Default title', WP_TH_CORE_SLUG ),
					'placeholder' => esc_html__( 'Type your title here', WP_TH_CORE_SLUG ),
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'meta_font_typography',
					'label' => esc_html__( 'Meta font typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .tophive-popup-content-wrapper .rememberme',
				]
			);
			$this->add_control(
				'meta_font_color',
				[
					'label' => esc_html__( 'Meta Font Color', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Core\Schemes\Color::get_type(),
						'value' => \Elementor\Core\Schemes\Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .tophive-popup-content-wrapper .rememberme' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
				'th_login_signup_register_redirect',
				[
					'label' => esc_html__( 'Redirect page after login/signup', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'solid',
					'options' => $this->get_pages_list(),
				]
			);

			$this->end_controls_section();


			$this->start_controls_section(
				'th_signin_signup_style_section',
				[
					'label' => esc_html__( 'Signin/Signup', WP_TH_CORE_SLUG ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
				$this->add_group_control(
				   \Elementor\Group_Control_Border::get_type(),
				   [
					'name' => 'input_border',
					'label' => esc_html__( 'Input field Border', WP_TH_CORE_SLUG ),
					'selector' => '{{WRAPPER}} #tophive-signin-signup .th-form-field input, #tophive-signin-signup select.th-form-field',
				   ]
			     );
				$this->add_control(
					'input_back_color',
					[
						'label' => esc_html__( 'Input background Color', WP_TH_CORE_SLUG ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} #tophive-signin-signup .th-form-field input, #tophive-signin-signup select.th-form-field' => 'background-color: {{VALUE}} !important',
						],
					 ]
				  );
				$this->add_control(
					'input_text_color',
					[
						'label' => esc_html__( 'Input Text Color', WP_TH_CORE_SLUG ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} #tophive-signin-signup .th-form-field input, #tophive-signin-signup select.th-form-field' => 'color: {{VALUE}} !important',
						],
					 ]
				  );
				 $this->add_control(
				  'input_border_radius',
				   [
					'label' => esc_html__( 'Input Border Radius', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} #tophive-signin-signup .th-form-field input, #tophive-signin-signup select.th-form-field ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				  ]
				 );
			  $this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'rememberme_typo',
					'label' => esc_html__( 'Remember me typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .th-modal-login label',
				]
			  );
			$this->add_control(
			'rememberme_color',
			  [
				'label' => esc_html__( 'Remember Color', WP_TH_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-modal-login label' => 'color: {{VALUE}}',
				],
			  ]
		   );
			 $this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'lostpass_typo',
					'label' => esc_html__( 'Lost password typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .switch-lost-pass',
				]
			  );
			 $this->add_control(
			  'lostpass_color',
			  [
				'label' => esc_html__( 'Lost password Color', WP_TH_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .switch-lost-pass' => 'color: {{VALUE}}',
				],
			  ]
		   );
			 $this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'btn_typo',
					'label' => esc_html__( 'Button typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #tophive-signin-signup button[type="submit"]',
				]
			  );
			 $this->add_control(
			  'btn_color',
			  [
				'label' => esc_html__( 'Button Color', WP_TH_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #tophive-signin-signup button[type="submit"]' => 'color: {{VALUE}}',
				],
			  ]
		    );
			$this->add_control(
				  'btn_border_radius',
				   [
					'label' => esc_html__( 'Button Border Radius', WP_TH_CORE_SLUG ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} #tophive-signin-signup button[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				  ]
				 );
			$this->add_control(
			  'btn_bg_color',
			  [
				'label' => esc_html__( 'Button Background Color', WP_TH_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #tophive-signin-signup button[type="submit"]' => 'background-color: {{VALUE}}',
				],
			  ]
		    );
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'other_text_typo',
					'label' => esc_html__( 'Other text typography', WP_TH_CORE_SLUG ),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #tophive-signin-signup p.ec-text-center',
				]
			  );
			 $this->add_control(
			  'other_text_color',
			  [
				'label' => esc_html__( 'Other text Color', WP_TH_CORE_SLUG ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  #tophive-signin-signup p.ec-text-center,  #tophive-signin-signup p.ec-text-center a b,#tophive-signin-signup p.ec-text-center a' => 'color: {{VALUE}}',
				],
			  ]
		    );
		$this->end_controls_section();

		}
		public function get_pages_list(){
			$page_list = array();
			$pages = get_pages(); 
			foreach ( $pages as $page ) {
				$page_list[get_page_link( $page->ID )] = $page->post_title;
			}
			return $page_list;
		}
		protected function render(){
			$settings = $this->get_settings_for_display();
			$login_text = $settings['th_login_signup_login_title'];
			$register_text = $settings['th_login_signup_register_title'];
			$redirect_url = $settings['th_login_signup_register_redirect'];
			$recover_text = $settings['th_login_signup_login_recover_pass_title'];

			?>
				<div class="" id="tophive-signin-signup">
			
					<div class="tophive-popup-content-wrapper">
						<div class="ec-d-block login-segment">
							<?php if( !empty($login_text) ){ ?>
						    	<h3 class="ec-text-center ec-mb-4"><?php echo $login_text; ?></h3>
							<?php } ?>
						    <?php do_action( 'tophive/login/form' ); ?>
						</div>
						<div class="ec-d-none signup-segment">
						    <?php if( !empty($register_text) ){ ?>
						    	<h3 class="ec-text-center ec-mb-4"><?php echo $register_text; ?></h3>
							<?php } ?>

						    <?php do_action( 'tophive/registration/form' ); ?>
						</div>
						<div class="ec-d-none recover-segment ec-py-5">
						    <?php if( !empty($recover_text) ){ ?>
					    	<h3 class="ec-text-center ec-mb-4"><?php esc_html_e( $recover_text, WP_TH_CORE_SLUG ); ?></h3>
					    	<?php } ?>
						    <?php do_action( 'tophive/recover-pass/form' ); ?>
						</div>

					</div>
				</div>
			<?php
		}
		
	}

?>
