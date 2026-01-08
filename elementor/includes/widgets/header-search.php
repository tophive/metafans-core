<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TH_Advanced_Header_Search extends Widget_Base {

    public function get_name() {
        return 'th_advanced_header_search';
    }

    public function get_title(){
        return __('Search', 'th-elementor');
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return ['th-header'];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'general_section',
            [
                'label' => esc_html__('Header search', 'th-elementor'),
            ]
        );

        $this->add_control(
            'th_search_mode',
            [
                'label' => esc_html__('Style', 'th-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'box',
                'options' => [
                    'box'   => esc_html__('Search Box', 'th-elementor'),
                    'icon'     => esc_html__('Search Icon', 'th-elementor'),
                ],
            ]
        );
        $this->add_control(
            'th_search_icon_click_open',
            [
                'label' => esc_html__('Search open mode', 'th-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'full'   => esc_html__('Full Screen Projection', 'th-elementor'),
                    'normal'     => esc_html__('Slide from top', 'th-elementor'),
                ],
            ]
        );

        
        $this->add_control(
            'custom_search_type',
            [
                'label' => esc_html__('Custom post type', 'th-elementor'),
                'description' => esc_html__('Enter the custom post type slug', 'th-elementor'),
                'placeholder' => 'my-post-type-slug',
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'search_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => esc_html__('Show icon', 'th-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'th_search_mode' => 'box'
                ]
            ]
        );
        $this->add_control(
			'search_box_icon_position',
			[
				'label' => esc_html__( 'Icon Alignment', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'textdomain' ),
						'icon' => 'eicon-flex eicon-align-start-h',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'textdomain' ),
						'icon' => 'eicon-flex eicon-align-end-h',
					],
				],
				'default' => 'right',
				'toggle' => true,
                'condition' => [
                    'show_icon' => 'yes',
                    'th_search_mode' => 'box'
                ]
			]
		);


        $this->add_control(
            'show_button',
            [
                'label' => esc_html__('Show Button', 'th-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'th_search_mode' => 'box'
                ]
            ]
        );

		$this->add_control(
            'search_btn_text',
            [
                'label' => esc_html__('Button text', 'th-elementor'),
                'placeholder' => 'Search button text',
                'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Search', 'th-elementor'),
                'condition' => [
                    'show_button' => 'yes',
                    'th_search_mode' => 'box'
                ]
            ]
        );

		$this->add_control(
            'search_placeholder',
            [
                'label' => esc_html__('Placeholder', 'th-elementor'),
                'placeholder' => 'Search...',
                'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Search...', 'th-elementor')
            ]
        );

        $this->add_control(
            'th_search_behaviour',
            [
                'label' => esc_html__('Search Behaviour', 'th-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ajax',
                'options' => [
                    'ajax'   => esc_html__('Dynamic Ajax Search on typing', 'th-elementor'),
                    'page'     => esc_html__('Goto search page on search button click', 'th-elementor'),
                ],
                'condition' => [
                    'th_search_mode' => 'box'
                ]
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'query_builder',
            [
                'label' => esc_html__('Query builder', 'th-elementor'),
            ]
        );
        // Select2 Multi-select for Post Types
        $this->add_control(
            'post_types',
            [
                'label'   => __( 'Post Types', 'plugin-name' ),
                'type'    => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    'post'     => __( 'Posts', 'plugin-name' ),
                    'page'     => __( 'Pages', 'plugin-name' ),
                    'product'  => __( 'Products', 'plugin-name' ),
                    'custom'   => __( 'Custom Post Type', 'plugin-name' ),
                ],
                'default' => ['post'],
            ]
        );

        // Results Per Query
        $this->add_control(
            'results_per_page',
            [
                'label'   => __( 'Results Per Query', 'plugin-name' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 20,
                'default' => 6,
            ]
        );


        $this->end_controls_section();

        Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'button', '{{WRAPPER}} .th-header-search form button');

        // FORM INPUT STYLES
        // Tophive_Elementor_Style_Helper::add_ui_style_controls($this, 'input', '{{WRAPPER}} .th-header-search form input');

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $post_types = !empty($settings['post_types']) ? implode(',', $settings['post_types']) : 'post';
        $results_per_page = !empty($settings['results_per_page']) ? $settings['results_per_page'] : 6;

        ?>
        <div class="th-header-search">
			<?php
				if($settings['th_search_mode'] == 'icon'){
					?>
						<span id="searchBtn" 
                            data-search-type="<?php echo $settings['th_search_icon_click_open']; ?>"
                            data-post-types="<?php echo esc_attr($post_types); ?>"
                            data-results-per-page="<?php echo esc_attr($results_per_page); ?>"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </span>
					<?php
				}
				if( $settings['th_search_mode'] == 'box' ){
					?>
						<form action="<?php echo esc_url(home_url('/')); ?>" 
                            class="ajax-search-form"
                            method="get" 
                            data-icon="<?php echo $settings['search_box_icon_position']; ?>"
                            data-post-types="<?php echo esc_attr($post_types); ?>"
                            data-results-per-page="<?php echo esc_attr($results_per_page); ?>"
                        >
							<input id="ajaxSearchInput" type="text" name="s" placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>">
							<?php if ($settings['show_icon'] === 'yes') : ?>
								<span class="search-icon"><i class="eicon-search"></i></span>
							<?php endif; ?>
							<?php if ($settings['show_button'] === 'yes') : ?>
								<button><?php echo $settings['search_btn_text']; ?></button>
							<?php endif; ?>
						</form>
                        <!-- Results Container -->
                        <div id="searchResults" class="search-results absolute-containers"></div>
					<?php
				}
				?>
				<?php
			?>            
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initSearchBar(widgetScope) {
                const searchBtn = widgetScope.querySelector('#searchBtn');
                const searchForm = widgetScope.querySelector('.ajax-search-form');
                const searchInput = widgetScope.querySelector('#ajaxSearchInput');
                const resultsContainer = widgetScope.querySelector('#searchResults');

                if (searchBtn) {
                    searchBtn.addEventListener('click', function() {
                        const searchType = this.dataset.searchType || 'full';
                        const postTypes = this.dataset.postTypes;
                        const resultsPerPage = this.dataset.resultsPerPage;

                        const searchOverlay = document.createElement('div');
                        searchOverlay.className = 'th-search-overlay ' + searchType;
                        searchOverlay.innerHTML = `
                            <div class="th-search-container">
                                <span class="th-search-close">&times;</span>
                                <form class="th-ajax-search-form" method="get">
                                    <input type="text" name="s" class="th-ajax-search-input" placeholder="Search..."
                                        data-post-types="${postTypes}"
                                        data-results-per-page="${resultsPerPage}"
                                    >
                                    <div class="th-search-results"></div>
                                </form>
                            </div>
                        `;
                        document.body.appendChild(searchOverlay);

                        setTimeout(() => {
                            searchOverlay.classList.add('active');
                            searchOverlay.querySelector('.th-ajax-search-input').focus();
                        }, 50);

                        searchOverlay.querySelector('.th-search-close').addEventListener('click', function() {
                            searchOverlay.classList.remove('active');
                            setTimeout(() => searchOverlay.remove(), 300);
                        });

                        const overlayInput = searchOverlay.querySelector('.th-ajax-search-input');
                        const overlayResults = searchOverlay.querySelector('.th-search-results');
                        setupAjaxSearch(overlayInput, overlayResults);
                    });
                }

                if (searchForm && searchInput && resultsContainer) {
                    setupAjaxSearch(searchInput, resultsContainer);
                }
            }

            function setupAjaxSearch(inputElement, resultsContainer) {
                let timeout = null;
                inputElement.addEventListener('keyup', function() {
                    clearTimeout(timeout);
                    const query = this.value;
                    const postTypes = this.dataset.postTypes;
                    const resultsPerPage = this.dataset.resultsPerPage;

                    if (query.length < 2) {
                        resultsContainer.innerHTML = '';
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    timeout = setTimeout(() => {
                        fetch(ajaxurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                'action': 'custom_search',
                                'query': query,
                                'post_types': postTypes,
                                'results_per_page': resultsPerPage
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            if (data.success && data.data.length > 0) {
                                data.data.forEach(item => {
                                    resultsContainer.innerHTML += `<a href="${item.url}" class="search-result-item"><img src="${item.image}" alt="${item.title}"><span>${item.title}</span></a>`;
                                });
                                resultsContainer.style.display = 'block';
                            } else {
                                resultsContainer.innerHTML = '<div class="no-results">No results found.</div>';
                                resultsContainer.style.display = 'block';
                            }
                        });
                    }, 300);
                });
            }

            const widgetScope = document.querySelector('.elementor-widget-th_advanced_header_search');
            if (widgetScope) {
                initSearchBar(widgetScope);
            }
        });
        </script>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TH_Advanced_Header_Search());
