<?php 
	namespace METAFANSCORE\widgets\elementor;
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	/**
	 **
	 * Metafans elementor member count class
	 * Since Version 1.0.0
	 * @package wordpress
	 * @subpackage MasterClass
	 *
	 *
	 */
	class MetafansElementorMemberCount extends \Elementor\Widget_base
	{
		public function get_title(){
			return esc_html__( 'Member count', WP_TH_CORE_SLUG );
		}

		public function get_name(){
			return 'th-login-member-count-block';
		}

		public function get_icon(){
			return 'eicon-user-circle-o';
		}

		public function get_categories(){
	        return [ WP_TH_CORE_SLUG ];
	    	}

		public function get_keywords() {
			return [ 'member', 'count', 'users' ];
		}
		protected function register_controls(){

			$this->start_controls_section(
				'th_member_count_section',
				[
					'label' => esc_html__( 'Member Count', WP_TH_CORE_SLUG ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);
				
			       $this->add_control(
				'th_member_img_size',
				[
					'label' => esc_html__( 'Member Image Size', WP_TH_CORE_SLUG),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 500,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 40,
					],
					'selectors' => [
						'{{WRAPPER}} .members-images figure' => 'width: {{SIZE}}{{UNIT}} !important;',
					],
				]
			   );
				$this->add_control(
					'th_member_img_radius',
					[
						'label' => esc_html__( 'Image Border Radius', WP_TH_CORE_SLUG ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .members-images figure ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
					]
				);
				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
							'name' => 'th_member_img_border',
							'label' => esc_html__( 'Image Border', WP_TH_CORE_SLUG ),
							'selector' => '{{WRAPPER}} .members-images figure',
					]
				);
			       $this->add_control(
				'th_member_img_gap',
				[
					'label' => esc_html__( 'Member Image Gap', WP_TH_CORE_SLUG),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px','%' ],
					'range' => [
						'px' => [
							'min' => -50,
							'max' => 50,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 15,
					],
					'selectors' => [
						'{{WRAPPER}} .members-images figure:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}} !important;',
					],
				]
			   );
				$this->add_control(
					'th_member_img_margin',
					[
						'label' => esc_html__( 'Image Margin', WP_TH_CORE_SLUG ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
							'{{WRAPPER}} .members-images' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
						],
					]
				);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'th_member_count_text_typo',
						'label' => esc_html__( 'Count text typography', WP_TH_CORE_SLUG ),
						'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
						'selector' => '{{WRAPPER}} .member-count span',
					]
				);
				$this->add_control(
					'th_member_count_text_color',
					[
						'label' => esc_html__( 'Count text color', WP_TH_CORE_SLUG ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'scheme' => [
							'type' => \Elementor\Core\Schemes\Color::get_type(),
							'value' => \Elementor\Core\Schemes\Color::COLOR_1,
						],
						'selectors' => [
							'{{WRAPPER}} .member-count span' => 'color: {{VALUE}}',
						],
					]
				);


			$this->end_controls_section();

		}

		protected function render(){
			$settings = $this->get_settings_for_display();
			$users = get_users( array( "number" => 5,"fields" => array( "id" ) ) ); 
			$user_count = count_users();
			?>
			<style>
				.member-count-element {
					display: flex;
					align-items: center;
				}
				.members-images {
					display: flex;
					align-items: center;
					margin-right: 20px;
				}
				.members-images figure {
					width: 40px !important;
					object-fit: cover !important;
					border-radius: 50% !important;
					border: 2px solid #fff;
				}
				.members-images figure:not(:first-child) {
					margin-left: -15px !important;
				}
				.members-images figure:not(:last-child) {
					margin-left: 20px;
				}
				.members-images img {
					width: 100%;
					height: 100%;
					object-fit: cover;
					border-radius: 50% !important;
				}
			</style>
			<div class="member-count-element">
			    <div class="members-images">
				<?php foreach( $users as $userObj ): ?>
				    <figure>
				       <img src="<?php echo get_avatar_url($userObj->id) ?>" alt="user">
				    </figure>
				<?php endforeach; ?>
			    </div>
			    <div class="member-count">
			       <span><?php esc_html_e("Join ",WP_TH_CORE_SLUG); echo $user_count["total_users"];esc_html_e("+ users",WP_TH_CORE_SLUG); ?></span>
			    </div>
			</div>
	<?php }
		
	}

?>

