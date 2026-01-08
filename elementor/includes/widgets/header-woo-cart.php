<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


use Elementor\Widget_Base;

class Tophive_Header_Cart_Widget extends Widget_Base {
    public function get_name() {
        return 'header_cart';
    }

    public function get_title() {
        return __('Woo Cart', 'your-text-domain');
    }

    public function get_icon() {
        return 'eicon-cart';
    }

    public function get_categories() {
        return ['th-header'];
    }

    public function get_script_depends() {
        return ['tophive-elementor-bundle'];
    }

    protected function register_controls() {
        // Cart Icon Settings
        $this->start_controls_section('cart_icon_section', [
            'label' => __('Cart', 'your-text-domain'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
    
        $this->add_control('cart_icon', [
            'label' => __('Cart Icon', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => ['value' => 'fas fa-shopping-cart', 'library' => 'solid'],
        ]);
    
        

        $this->add_control(
            'cart_alignment',
            [
                'label' => __('Cart Alignment', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'your-text-domain'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'your-text-domain'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'your-text-domain'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'cart_spacing',
            [
                'label' => __('Spacing Between Items', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'selectors' => ['{{WRAPPER}} .cart-icon' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px'
                ]
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Cart Settings', 'custom-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control('cart_style', [
            'label' => __('Select a cart style', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'dd' => __('Dropdown menu', 'your-text-domain'),
                'dw' => __('Drawer', 'your-text-domain'),
            ],
            'default' => 'dw',
        ]);
    
        $this->add_control('cart_trigger', [
            'label' => __('Show Cart Dropdown', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'click' => __('On Click', 'your-text-domain'),
                'hover' => __('On Hover', 'your-text-domain'),
            ],
            'default' => 'click',
            'condition' => [
                'cart_style' => 'dd'
            ]
        ]);
    
        // Cart Position Control (Fix for Undefined Array Key)
        $this->add_control(
            'cart_position',
            [
                'label' => __('Cart Open Position', 'custom-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'right', // Default to right if not set
                'options' => [
                    'left' => __('Left', 'custom-elementor'),
                    'right' => __('Right', 'custom-elementor'),
                ],
                'condition' => [
                    'cart_style' => 'dw'
                ]
            ]
        );


        $this->add_control('show_cart_on_add', [
            'label' => __('Show Cart on Add to Cart', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'your-text-domain'),
            'label_off' => __('No', 'your-text-domain'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);


        $this->add_control('cart_substotal_position', [
            'label' => __('Cart amount and substotal position', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '1' => __('Cart icon + Cart count + substotal', 'your-text-domain'),
                '2' => __('Substotal + Cart count + Cart icon', 'your-text-domain'),
                '3' => __('Cart count + Subtotal + Cart icon', 'your-text-domain'),
                '4' => __('Cart count + Cart icon', 'your-text-domain'),
                '5' => __('Sub total + Cart icon', 'your-text-domain'),
                '6' => __('Cart icon + Sub total', 'your-text-domain'),
                '7' => __('Cart icon + Cart count', 'your-text-domain'),
            ],
            'default' => '1',
        ]);
        $this->add_control(
            'cart_count_position',
            [
                'label' => __('Cart Count Position', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'default' => __('Default', 'your-text-domain'),
                    'top-right' => __('Top Right', 'your-text-domain'),
                    'top-left' => __('Top left', 'your-text-domain'),
                ],
                'default' => 'top-right',
                'condition' => [
                    'cart_substotal_position' => ['1', '2', '7']
                ]
            ]
        );
        $this->add_control(
            'enable_separator',
            [
                'label' => __('Enable Separator', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'your-text-domain'),
                'label_off' => __('Hide', 'your-text-domain'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'separator_position',
            [
                'label' => __('Separator Position', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'before' => __('Before Count', 'your-text-domain'),
                    'after' => __('After Count', 'your-text-domain'),
                ],
                'default' => 'after',
                'condition' => ['enable_separator' => 'yes'], // Show only if separator is enabled
            ]
        );
        $this->add_control(
            'custom_separator',
            [
                'label' => __('Custom Separator', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '/',
                'condition' => ['enable_separator' => 'yes'], // Show only if separator is enabled
            ]
        );


        $this->add_control('seperator_spacing', [
            'label' => __('Seperator spacing', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 5, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .cart-separator' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};'],
            'condition' => ['enable_separator' => 'yes'],
        ]);
        
        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Cart Icon', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('cart_icon_size', [
            'label' => __('Icon Size', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 10, 'max' => 100, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .header-cart-icon i' => 'font-size: {{SIZE}}{{UNIT}};'],
        ]);
    
        $this->add_control('cart_icon_color', [
            'label' => __('Icon Color', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => ['{{WRAPPER}} .header-cart-icon i' => 'color: {{VALUE}};'],
        ]);
    
        $this->add_control('cart_icon_bg_color', [
            'label' => __('Icon Background Color', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => ['{{WRAPPER}} .header-cart-icon i' => 'background-color: {{VALUE}};'],
        ]);

        $this->add_responsive_control(
            'cart_icon_padding',
            [
                'label' => __('Cart padding', 'your-text-domain'),
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
                'selectors' => ['{{WRAPPER}} .header-cart-icon i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'cart_wrapper_margin',
            [
                'label' => __('Cart Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .elementor-widget-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_control('cart_icon_br', [
            'label' => __('Icon Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .header-cart-icon i' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(
            'count_style_section',
            [
                'label' => __('Product Count', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'product_count_typography',
                'label' => __('Product Count Typography', 'your-text-domain'),
                'selector' => '{{WRAPPER}} .cart-count',
            ]
        );
        
        $this->add_control(
            'product_count_color',
            [
                'label' => __('Product Count Color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .cart-count' => 'color: {{VALUE}}'],
                'default' => '#fff'
            ]
        );
        
        
        $this->add_control(
            'product_count_bg_color',
            [
                'label' => __('Product Count Background', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .cart-count' => 'background-color: {{VALUE}}'],
            ]
        );
        $this->add_control('cart_total_prod_br', [
            'label' => __('Border Radius', 'your-text-domain'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]],
            'default' => ['size' => 24],
            'selectors' => ['{{WRAPPER}} .cart-count' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_responsive_control(
            'product_count_padding',
            [
                'label' => __('Padding', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .cart-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->add_responsive_control(
            'product_count_margin',
            [
                'label' => __('Margin', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => ['{{WRAPPER}} .cart-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'subtotal_style_section',
            [
                'label' => __('Cart subtotal', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtotal_typography',
                'label' => __('Cart subtotal Typography', 'your-text-domain'),
                'selector' => '{{WRAPPER}} .cart-subtotal',
            ]
        );
        
        $this->add_control(
            'subtotal_color',
            [
                'label' => __('Subtotal Color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .cart-subtotal' => 'color: {{VALUE}}'],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'seperator_style_section',
            [
                'label' => __('Seperator', 'your-text-domain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'cart_separator_color',
            [
                'label' => __('Separator Color', 'your-text-domain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .cart-separator' => 'color: {{VALUE}};'],
            ]
        );
        
        $this->end_controls_section();
        
    }
    

    protected function render() {
        // ✅ Ensure WooCommerce is Installed & Active
        if (!class_exists('WooCommerce') || !function_exists('WC')) {
            echo '<div class="header-cart-wrapper"><p class="empty-cart">WooCommerce is not installed or activated.</p></div>';
            return;
        }

        // ✅ Force WooCommerce to Initialize the Session
        if (WC()->session === null) {
            WC()->initialize_session();
            WC()->session->set_customer_session_cookie(true);
        }

        // ✅ Ensure the Cart is Initialized
        if (!WC()->cart) {
            WC()->cart = new WC_Cart(); // Initialize WooCommerce cart manually
        }

        if (empty(WC()->cart->get_cart())) {
            echo '<div class="header-cart-wrapper"><p class="empty-cart">Your cart is empty.</p></div>';
            return;
        }
    
        $settings = $this->get_settings_for_display();
        $show_cart_on_add = $settings['show_cart_on_add'] === 'yes' ? 'true' : 'false';
        $cart_trigger = esc_attr($settings['cart_trigger']); // 'click' or 'hover'
        $cart_style = esc_attr($settings['cart_style']); // 'dropdown' or 'drawer' - dd/dw
        $cart_position = esc_attr($settings['cart_position']); // 'left' or 'right'
        
        $cart_total = WC()->cart->subtotal;
        $free_shipping_goal = 1500; // Example: Free shipping at $1500
        $remaining_for_free_shipping = max(0, $free_shipping_goal - $cart_total);
        $progress_percentage = min(100, ($cart_total / $free_shipping_goal) * 100);
        ?>
    
        <div class="header-cart-wrapper" data-cart-trigger="<?php echo $cart_trigger; ?>" data-cart-position="<?php echo $cart_position; ?>">
        <div class="header-cart-icon">
            <a href="javascript:void(0)" class="cart-toggle">
                <?php 
                // ✅ Define Cart Count with Dynamic Positioning
                $cart_count_position = isset($settings['cart_count_position']) ? $settings['cart_count_position'] : 'default';

                $cart_count = '<span class="cart-count '. $cart_count_position .'">' . WC()->cart->get_cart_contents_count() . '</span>';
                $cart_icon  = '<span class="cart-icon"><i class="' . esc_attr($settings['cart_icon']['value']) . '"></i></span>';
                $cart_subtotal = '<span class="cart-subtotal">' . WC()->cart->get_cart_subtotal() . '</span>';

                // ✅ Separator Logic
                $separator = ($settings['enable_separator'] === 'yes') ? 
                            '<span class="cart-separator">' . esc_html($settings['custom_separator']) . '</span>' : '';
                
                // ✅ Apply Separator Position
                $cart_count_with_separator = ($settings['separator_position'] === 'before') 
                    ? $separator . $cart_count 
                    : $cart_count . $separator;

                switch ($settings['cart_substotal_position']) {
                    case '1': echo '<span class="cart-count-inline">' . $cart_icon . $cart_count_with_separator . '</span>' . $cart_subtotal; break;
                    case '2': echo $cart_subtotal . '<span class="cart-count-inline">' . $cart_count_with_separator . $cart_icon . '</span>'; break;
                    case '3': echo $cart_count_with_separator . $cart_subtotal . $cart_icon; break;
                    case '4': echo $cart_count_with_separator . $cart_icon; break;
                    case '5': echo $cart_subtotal . $cart_icon; break;
                    case '6': echo $cart_icon . $cart_subtotal; break;
                    case '7': echo '<span class="cart-count-inline">' . $cart_icon . $cart_count_with_separator . '</span>'; break;
                    default: echo $cart_icon . $cart_count_with_separator . $cart_subtotal; break;
                }
                ?>
            </a>
        </div>

            <!-- Drowdown cart -->
             <?php
                if( $cart_style == "dd" ){
                    ?>
                        <div class="header-cart-dropdown">
                            
                            <div class="cart-content">
                                <ul class="cart-list">
                                    <?php
                                    $cart = WC()->cart->get_cart();
                                    if (!empty($cart)) {
                                        foreach ($cart as $cart_item_key => $cart_item) {
                                            $product = $cart_item['data'];
                                            $quantity = $cart_item['quantity'];
                                            ?>
                                            <li class="cart-item" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">
                                                <img src="<?= get_the_post_thumbnail_url($product->get_id(), 'thumbnail') ?>" />
                                                <div class="cart-item-info">
                                                    <span class="cart-item-name"><?= esc_html($product->get_name()) ?></span>
                                                    <div class="cart-quantity">
                                                        <button class="cart-minus" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">-</button>
                                                        <input type="number" data-cart-item-key="<?= esc_attr($cart_item_key) ?>" class="cart-qty" value="<?= esc_html($quantity) ?>" min="1" />
                                                        <button class="cart-plus" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">+</button>
                                                    </div>
                                                    <span class="cart-item-price"><?= wc_price($cart_item['line_total']) ?></span>
                                                </div>
                                                <button class="cart-remove" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">×</button>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo '<p class="empty-cart">Your cart is empty.</p>';
                                    }
                                    ?>
                                </ul>
                            </div>
                
                            <!-- Cart Footer with Subtotal and Free Shipping Progress -->
                            <div class="cart-footer">
                                <div class="cart-subtotal">
                                    <strong>Subtotal:</strong> <span class="cart-total-price"><?php echo wc_price($cart_total); ?></span>
                                </div>
                
                                <a href="<?php echo wc_get_cart_url(); ?>" class="cart-view-btn">View Cart</a>
                                <a href="<?php echo wc_get_checkout_url(); ?>" class="cart-checkout-btn">Checkout</a>
                            </div>
                        </div>
                    <?php
                }else{
                    ?>
                        <div class="header-cart-sidebar <?php echo $cart_position === 'left' ? 'cart-left' : 'cart-right'; ?>">
                            <div class="cart-header">
                                <span class="cart-title">Shopping Cart</span>
                                <button class="cart-close">×</button>
                            </div>
                            
                            <div class="cart-content">
                                <ul class="cart-list">
                                    <?php
                                    $cart = WC()->cart->get_cart();
                                    if (!empty($cart)) {
                                        foreach ($cart as $cart_item_key => $cart_item) {
                                            $product = $cart_item['data'];
                                            $quantity = $cart_item['quantity'];
                                            ?>
                                            <li class="cart-item" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">
                                                <img src="<?= get_the_post_thumbnail_url($product->get_id(), 'thumbnail') ?>" />
                                                <div class="cart-item-info">
                                                    <span class="cart-item-name"><?= esc_html($product->get_name()) ?></span>
                                                    <div class="cart-quantity">
                                                        <button class="cart-minus" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">-</button>
                                                        <input data-cart-item-key="<?= esc_attr($cart_item_key) ?>" type="number" class="cart-qty" value="<?= esc_html($quantity) ?>" min="1" />
                                                        <button class="cart-plus" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">+</button>
                                                    </div>
                                                    <span class="cart-item-price"><?= wc_price($cart_item['line_total']) ?></span>
                                                </div>
                                                <button class="cart-remove" data-cart-item-key="<?= esc_attr($cart_item_key) ?>">×</button>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo '<p class="empty-cart">Your cart is empty.</p>';
                                    }
                                    ?>
                                </ul>
                            </div>
                
                            <!-- Cart Footer with Subtotal and Free Shipping Progress -->
                            <div class="cart-footer">
                                <div class="cart-subtotal">
                                    <strong>Subtotal:</strong> <span class="cart-total-price"><?php echo wc_price($cart_total); ?></span>
                                </div>
                
                                <?php if ($remaining_for_free_shipping > 0): ?>
                                    <p class="cart-shipping-message">
                                        Add <strong><?php echo wc_price($remaining_for_free_shipping); ?></strong> to cart and get <strong>free shipping!</strong>
                                    </p>
                                    <div class="cart-shipping-progress">
                                        <div class="cart-shipping-bar" style="width: <?php echo $progress_percentage; ?>%;"></div>
                                    </div>
                                <?php endif; ?>
                
                                <a href="<?php echo wc_get_cart_url(); ?>" class="cart-view-btn">View Cart</a>
                                <a href="<?php echo wc_get_checkout_url(); ?>" class="cart-checkout-btn">Checkout</a>
                            </div>
                        </div>
                    <?php
                }
             ?>
            <!-- Sliding Cart Sidebar -->
            
        </div>
    
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                initWooCart(<?php echo $show_cart_on_add; ?>);
            });
        </script>
        <?php
    }
    
    private function get_cart_items() {
        $cart = WC()->cart->get_cart();
        if (!empty($cart)) {
            echo '<ul class="cart-list">';
            foreach ($cart as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                echo '<li class="cart-item">
                        <img src="' . get_the_post_thumbnail_url($product->get_id(), 'thumbnail') . '" />
                        <span class="cart-item-name">' . $product->get_name() . '</span>
                        <span class="cart-item-price">' . wc_price($cart_item['line_total']) . '</span>
                    </li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="empty-cart">Your cart is empty.</p>';
        }
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Tophive_Header_Cart_Widget());
?>
