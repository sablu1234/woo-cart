<?php
/**
 * Plugin Name: Woo Cart
 * Description: A professional WooCommerce floating cart drawer.
 * Version: 1.0.0
 * Author: sablu hasan
 * Author URI: https://sablu-hasan.vercel.app/
 * Text Domain: woo-cart
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Woo_Cart_Plugin')) {
    class Woo_Cart_Plugin
    {
        const OPTION_KEY = 'woo_cart_settings';

        public function __construct()
        {
            add_action('admin_menu', array($this, 'add_settings_page'));
            add_action('admin_post_woo_cart_save_settings', array($this, 'save_settings'));

            add_action('wp_head', array($this, 'print_styles'));
            add_action('wp_footer', array($this, 'render_cart_drawer'));
            add_filter('woocommerce_add_to_cart_fragments', array($this, 'update_cart_fragments'));
        }

        private function defaults()
        {
            return array(
                'show_cart' => '1',
                'drawer_title' => 'Your Cart',
                'empty_text' => 'Your cart is empty.',
                'view_cart_text' => 'View Cart',
                'checkout_text' => 'Checkout',
                'icon_bg_color' => '#111111',
                'icon_text_color' => '#ffffff',
                'badge_bg_color' => '#111111',
                'drawer_bg_color' => '#f6f6f4',
                'drawer_text_color' => '#111111',
                'button_bg_color' => '#111111',
                'button_text_color' => '#ffffff',
                'overlay_color' => '#000000',
            );
        }

        private function settings()
        {
            return wp_parse_args(
                get_option(self::OPTION_KEY, array()),
                $this->defaults()
            );
        }

        private function sanitize_settings($input)
        {
            $defaults = $this->defaults();

            return array(
                'show_cart' => !empty($input['show_cart']) ? '1' : '0',
                'drawer_title' => sanitize_text_field($this->posted_value($input, 'drawer_title', $defaults)),
                'empty_text' => sanitize_text_field($this->posted_value($input, 'empty_text', $defaults)),
                'view_cart_text' => sanitize_text_field($this->posted_value($input, 'view_cart_text', $defaults)),
                'checkout_text' => sanitize_text_field($this->posted_value($input, 'checkout_text', $defaults)),
                'icon_bg_color' => $this->sanitize_color($this->posted_value($input, 'icon_bg_color', $defaults), $defaults['icon_bg_color']),
                'icon_text_color' => $this->sanitize_color($this->posted_value($input, 'icon_text_color', $defaults), $defaults['icon_text_color']),
                'badge_bg_color' => $this->sanitize_color($this->posted_value($input, 'badge_bg_color', $defaults), $defaults['badge_bg_color']),
                'drawer_bg_color' => $this->sanitize_color($this->posted_value($input, 'drawer_bg_color', $defaults), $defaults['drawer_bg_color']),
                'drawer_text_color' => $this->sanitize_color($this->posted_value($input, 'drawer_text_color', $defaults), $defaults['drawer_text_color']),
                'button_bg_color' => $this->sanitize_color($this->posted_value($input, 'button_bg_color', $defaults), $defaults['button_bg_color']),
                'button_text_color' => $this->sanitize_color($this->posted_value($input, 'button_text_color', $defaults), $defaults['button_text_color']),
                'overlay_color' => $this->sanitize_color($this->posted_value($input, 'overlay_color', $defaults), $defaults['overlay_color']),
            );
        }

        private function posted_value($input, $key, $defaults)
        {
            return isset($input[$key]) ? $input[$key] : $defaults[$key];
        }

        private function sanitize_color($value, $fallback)
        {
            $color = sanitize_hex_color($value);

            return $color ? $color : $fallback;
        }

        public function add_settings_page()
        {
            add_menu_page(
                __('Woo Cart Settings', 'woo-cart'),
                __('Woo Cart', 'woo-cart'),
                'manage_options',
                'woo-cart-settings',
                array($this, 'render_settings_page'),
                'dashicons-cart',
                56
            );
        }

        public function save_settings()
        {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to save these settings.', 'woo-cart'));
            }

            check_admin_referer('woo_cart_save_settings', 'woo_cart_nonce');

            $raw_settings = isset($_POST[self::OPTION_KEY])
                ? wp_unslash($_POST[self::OPTION_KEY])
                : array();

            update_option(self::OPTION_KEY, $this->sanitize_settings($raw_settings));

            wp_safe_redirect(
                add_query_arg(
                    array(
                        'page' => 'woo-cart-settings',
                        'settings-updated' => 'true',
                    ),
                    admin_url('admin.php')
                )
            );
            exit;
        }

        public function render_settings_page()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            $settings = $this->settings();
            ?>
            <div class="wrap">
                <h1><?php esc_html_e('Woo Cart Settings', 'woo-cart'); ?></h1>

                <?php if (isset($_GET['settings-updated'])) : ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e('Settings saved.', 'woo-cart'); ?></p>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="woo_cart_save_settings">
                    <?php wp_nonce_field('woo_cart_save_settings', 'woo_cart_nonce'); ?>

                    <table class="form-table" role="presentation">
                        <?php $this->checkbox_field('show_cart', __('Floating cart drawer', 'woo-cart'), __('Show cart drawer on frontend', 'woo-cart'), $settings['show_cart']); ?>
                        <?php $this->text_field('drawer_title', __('Drawer title', 'woo-cart'), $settings['drawer_title']); ?>
                        <?php $this->text_field('empty_text', __('Empty cart text', 'woo-cart'), $settings['empty_text']); ?>
                        <?php $this->text_field('view_cart_text', __('View cart text', 'woo-cart'), $settings['view_cart_text']); ?>
                        <?php $this->text_field('checkout_text', __('Checkout text', 'woo-cart'), $settings['checkout_text']); ?>
                        <?php $this->color_field('icon_bg_color', __('Floating icon background', 'woo-cart'), $settings['icon_bg_color']); ?>
                        <?php $this->color_field('icon_text_color', __('Floating icon color', 'woo-cart'), $settings['icon_text_color']); ?>
                        <?php $this->color_field('badge_bg_color', __('Cart count badge color', 'woo-cart'), $settings['badge_bg_color']); ?>
                        <?php $this->color_field('drawer_bg_color', __('Drawer background color', 'woo-cart'), $settings['drawer_bg_color']); ?>
                        <?php $this->color_field('drawer_text_color', __('Drawer text color', 'woo-cart'), $settings['drawer_text_color']); ?>
                        <?php $this->color_field('button_bg_color', __('Button background color', 'woo-cart'), $settings['button_bg_color']); ?>
                        <?php $this->color_field('button_text_color', __('Button text color', 'woo-cart'), $settings['button_text_color']); ?>
                        <?php $this->color_field('overlay_color', __('Overlay color', 'woo-cart'), $settings['overlay_color']); ?>
                    </table>

                    <?php submit_button(__('Save Changes', 'woo-cart')); ?>
                </form>
            </div>
            <?php
        }

        private function field_name($key)
        {
            return self::OPTION_KEY . '[' . $key . ']';
        }

        private function text_field($key, $label, $value)
        {
            ?>
            <tr>
                <th scope="row">
                    <label for="woo-cart-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                </th>
                <td>
                    <input class="regular-text" type="text" id="woo-cart-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($this->field_name($key)); ?>" value="<?php echo esc_attr($value); ?>">
                </td>
            </tr>
            <?php
        }

        private function checkbox_field($key, $label, $text, $value)
        {
            ?>
            <tr>
                <th scope="row"><?php echo esc_html($label); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr($this->field_name($key)); ?>" value="1" <?php checked($value, '1'); ?>>
                        <?php echo esc_html($text); ?>
                    </label>
                </td>
            </tr>
            <?php
        }

        private function color_field($key, $label, $value)
        {
            ?>
            <tr>
                <th scope="row">
                    <label for="woo-cart-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                </th>
                <td>
                    <input type="color" id="woo-cart-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($this->field_name($key)); ?>" value="<?php echo esc_attr($value); ?>">
                </td>
            </tr>
            <?php
        }

        private function can_render_cart()
        {
            $settings = $this->settings();

            return class_exists('WooCommerce') && $settings['show_cart'] === '1';
        }

        public function print_styles()
        {
            if (!$this->can_render_cart()) {
                return;
            }

            $settings = $this->settings();
            ?>
            <style id="woo-cart-styles">
                .woo-cart-floating-button,
                .woo-cart-drawer,
                .woo-cart-drawer * {
                    box-sizing: border-box;
                    letter-spacing: 0;
                }

                .woo-cart-floating-button {
                    align-items: center;
                    background: <?php echo esc_html($settings['icon_bg_color']); ?>;
                    border: 0;
                    border-radius: 16px;
                    bottom: 24px;
                    box-shadow: 0 18px 40px rgba(17, 24, 39, 0.28);
                    color: <?php echo esc_html($settings['icon_text_color']); ?>;
                    cursor: pointer;
                    display: flex;
                    height: 58px;
                    justify-content: center;
                    position: fixed;
                    right: 24px;
                    transition: transform 180ms ease, box-shadow 180ms ease;
                    width: 58px;
                    z-index: 99999;
                }

                .woo-cart-floating-button:hover,
                .woo-cart-floating-button:focus {
                    box-shadow: 0 22px 48px rgba(17, 24, 39, 0.34);
                    color: <?php echo esc_html($settings['icon_text_color']); ?>;
                    outline: none;
                    transform: translateY(-2px);
                }

                .woo-cart-floating-button svg {
                    display: block;
                    height: 27px;
                    width: 27px;
                }

                .woo-cart-floating-count {
                    align-items: center;
                    background: <?php echo esc_html($settings['badge_bg_color']); ?>;
                    border: 2px solid #ffffff;
                    border-radius: 999px;
                    box-shadow: 0 8px 14px rgba(17, 24, 39, 0.18);
                    color: #ffffff;
                    display: flex;
                    font-size: 12px;
                    font-weight: 800;
                    height: 24px;
                    justify-content: center;
                    min-width: 24px;
                    padding: 0 7px;
                    position: absolute;
                    right: -7px;
                    top: -9px;
                }

                .woo-cart-drawer-overlay {
                    background: <?php echo esc_html($settings['overlay_color']); ?>;
                    bottom: 0;
                    left: 0;
                    opacity: 0;
                    pointer-events: none;
                    position: fixed;
                    right: 0;
                    top: 0;
                    transition: opacity 220ms ease;
                    z-index: 99998;
                }

                .woo-cart-drawer-overlay.is-open {
                    opacity: 0.58;
                    pointer-events: auto;
                }

                .woo-cart-drawer {
                    background: <?php echo esc_html($settings['drawer_bg_color']); ?>;
                    bottom: 0;
                    box-shadow: -24px 0 60px rgba(17, 24, 39, 0.18);
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    display: flex;
                    flex-direction: column;
                    max-width: min(446px, 92vw);
                    position: fixed;
                    right: 0;
                    top: 0;
                    transform: translateX(105%);
                    transition: transform 260ms ease;
                    width: 446px;
                    z-index: 99999;
                }

                .woo-cart-drawer.is-open {
                    transform: translateX(0);
                }

                body.woo-cart-drawer-open {
                    overflow: hidden;
                }

                .woo-cart-drawer-header {
                    align-items: center;
                    background: #ffffff;
                    display: flex;
                    justify-content: space-between;
                    min-height: 74px;
                    padding: 20px 22px;
                }

                .woo-cart-drawer-heading {
                    align-items: center;
                    display: flex;
                    gap: 10px;
                    min-width: 0;
                }

                .woo-cart-drawer-heading svg {
                    flex: 0 0 auto;
                    height: 22px;
                    width: 22px;
                }

                .woo-cart-drawer-title {
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    font-size: 16px;
                    font-weight: 800;
                    line-height: 1.2;
                    margin: 0;
                }

                .woo-cart-drawer-count {
                    align-items: center;
                    background: #050505;
                    border-radius: 999px;
                    color: #ffffff;
                    display: inline-flex;
                    font-size: 11px;
                    font-weight: 800;
                    height: 22px;
                    justify-content: center;
                    min-width: 22px;
                    padding: 0 7px;
                }

                .woo-cart-drawer-close {
                    align-items: center;
                    background: #ffffff;
                    border: 1px solid rgba(17, 24, 39, 0.08);
                    border-radius: 999px;
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    cursor: pointer;
                    display: flex;
                    height: 38px;
                    justify-content: center;
                    padding: 0;
                    transition: background-color 160ms ease;
                    width: 38px;
                }

                .woo-cart-drawer-close:hover,
                .woo-cart-drawer-close:focus {
                    background: #f4f4f1;
                    outline: none;
                }

                .woo-cart-drawer-content {
                    flex: 1;
                    overflow-y: auto;
                    padding: 14px 18px 18px;
                }

                .woo-cart-drawer-list {
                    display: grid;
                    gap: 14px;
                }

                .woo-cart-drawer-item {
                    align-items: center;
                    background: #ffffff;
                    border: 1px solid rgba(17, 24, 39, 0.06);
                    border-radius: 18px;
                    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.04);
                    display: grid;
                    gap: 14px;
                    grid-template-columns: 84px minmax(0, 1fr) auto;
                    min-height: 98px;
                    padding: 12px;
                    position: relative;
                }

                .woo-cart-item-thumb {
                    align-items: center;
                    background: #f7f7f5;
                    border-radius: 14px;
                    display: flex;
                    height: 84px;
                    justify-content: center;
                    overflow: hidden;
                    width: 84px;
                }

                .woo-cart-item-thumb img {
                    display: block;
                    height: 100%;
                    object-fit: contain;
                    width: 100%;
                }

                .woo-cart-item-main {
                    min-width: 0;
                    padding-right: 20px;
                }

                .woo-cart-item-title {
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    display: block;
                    font-size: 14px;
                    font-weight: 800;
                    line-height: 1.35;
                    text-decoration: none;
                }

                .woo-cart-item-title:hover {
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    opacity: 0.72;
                }

                .woo-cart-item-meta {
                    align-items: center;
                    display: flex;
                    gap: 8px;
                    margin-top: 12px;
                }

                .woo-cart-qty {
                    align-items: center;
                    background: #f4f4f1;
                    border-radius: 999px;
                    color: #111111;
                    display: inline-flex;
                    font-size: 13px;
                    font-weight: 800;
                    height: 28px;
                    justify-content: center;
                    min-width: 38px;
                    padding: 0 12px;
                }

                .woo-cart-item-price {
                    align-self: end;
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                    font-size: 14px;
                    font-weight: 900;
                    justify-self: end;
                    white-space: nowrap;
                }

                .woo-cart-item-remove {
                    align-items: center;
                    background: #050505;
                    border-radius: 999px;
                    color: #ffffff !important;
                    display: flex;
                    font-size: 15px;
                    font-weight: 800;
                    height: 26px;
                    justify-content: center;
                    line-height: 1;
                    position: absolute;
                    right: 12px;
                    text-decoration: none;
                    top: 12px;
                    width: 26px;
                }

                .woo-cart-item-remove:hover {
                    background: <?php echo esc_html($settings['badge_bg_color']); ?>;
                    color: #ffffff !important;
                }

                .woo-cart-drawer-summary {
                    background: #ffffff;
                    border: 1px solid rgba(17, 24, 39, 0.06);
                    border-radius: 18px;
                    margin-top: 16px;
                    padding: 16px;
                }

                .woo-cart-drawer-total {
                    align-items: center;
                    display: flex;
                    font-size: 16px;
                    justify-content: space-between;
                    margin: 0;
                }

                .woo-cart-drawer-actions {
                    display: grid;
                    gap: 10px;
                    margin-top: 14px;
                }

                .woo-cart-drawer-button {
                    background: <?php echo esc_html($settings['button_bg_color']); ?>;
                    border: 1px solid <?php echo esc_html($settings['button_bg_color']); ?>;
                    border-radius: 999px;
                    color: <?php echo esc_html($settings['button_text_color']); ?>;
                    display: block;
                    font-size: 14px;
                    font-weight: 800;
                    min-height: 48px;
                    padding: 15px 18px;
                    text-align: center;
                    text-decoration: none;
                    transition: opacity 160ms ease, transform 160ms ease;
                    width: 100%;
                }

                .woo-cart-drawer-button:hover,
                .woo-cart-drawer-button:focus {
                    color: <?php echo esc_html($settings['button_text_color']); ?>;
                    opacity: 0.9;
                    outline: none;
                    transform: translateY(-1px);
                }

                .woo-cart-drawer-button.is-secondary {
                    background: #ffffff;
                    border-color: rgba(17, 24, 39, 0.12);
                    color: <?php echo esc_html($settings['drawer_text_color']); ?>;
                }

                .woo-cart-drawer-empty {
                    background: #ffffff;
                    border: 1px dashed rgba(17, 24, 39, 0.18);
                    border-radius: 18px;
                    padding: 34px 22px;
                    text-align: center;
                }

                .woo-cart-drawer-empty p {
                    margin: 0;
                }

                @media (max-width: 782px) {
                    .woo-cart-floating-button {
                        bottom: 18px;
                        height: 54px;
                        right: 18px;
                        width: 54px;
                    }

                    .woo-cart-drawer {
                        max-width: 100vw;
                        width: 100vw;
                    }

                    .woo-cart-drawer-item {
                        grid-template-columns: 76px minmax(0, 1fr);
                    }

                    .woo-cart-item-thumb {
                        height: 76px;
                        width: 76px;
                    }

                    .woo-cart-item-price {
                        grid-column: 2;
                        justify-self: start;
                    }
                }
            </style>
            <?php
        }

        public function render_cart_drawer()
        {
            if (!$this->can_render_cart()) {
                return;
            }

            echo $this->floating_button_markup();
            echo $this->drawer_markup();
            $this->print_script();
        }

        public function update_cart_fragments($fragments)
        {
            if (!$this->can_render_cart()) {
                return $fragments;
            }

            $fragments['.woo-cart-floating-button'] = $this->floating_button_markup();
            $fragments['.woo-cart-drawer-count'] = $this->cart_count_markup();
            $fragments['.woo-cart-drawer-content'] = $this->drawer_content_markup();

            return $fragments;
        }

        private function floating_button_markup()
        {
            $count = $this->cart_count();
            $label = sprintf(
                _n('%d item in cart', '%d items in cart', $count, 'woo-cart'),
                $count
            );

            ob_start();
            ?>
            <button class="woo-cart-floating-button" type="button" aria-label="<?php echo esc_attr($label); ?>" aria-controls="woo-cart-drawer" aria-expanded="false">
                <span class="woo-cart-floating-count"><?php echo esc_html($count); ?></span>
                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none">
                    <path d="M6.5 8.5h11l-.75 8.25a2 2 0 0 1-1.99 1.81H9.24a2 2 0 0 1-1.99-1.81L6.5 8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M9 8.5a3 3 0 0 1 6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
            <?php

            return ob_get_clean();
        }

        private function drawer_markup()
        {
            $settings = $this->settings();

            ob_start();
            ?>
            <div class="woo-cart-drawer-overlay" data-woo-cart-close></div>
            <aside id="woo-cart-drawer" class="woo-cart-drawer" aria-hidden="true">
                <div class="woo-cart-drawer-header">
                    <div class="woo-cart-drawer-heading">
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none">
                            <path d="M6.5 8.5h11l-.75 8.25a2 2 0 0 1-1.99 1.81H9.24a2 2 0 0 1-1.99-1.81L6.5 8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9 8.5a3 3 0 0 1 6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <h2 class="woo-cart-drawer-title"><?php echo esc_html($settings['drawer_title']); ?></h2>
                        <?php echo $this->cart_count_markup(); ?>
                    </div>
                    <button class="woo-cart-drawer-close" type="button" aria-label="<?php esc_attr_e('Close cart', 'woo-cart'); ?>" data-woo-cart-close>
                        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none">
                            <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                <?php echo $this->drawer_content_markup(); ?>
            </aside>
            <?php

            return ob_get_clean();
        }

        private function drawer_content_markup()
        {
            $settings = $this->settings();
            $items = $this->cart_items();

            ob_start();
            ?>
            <div class="woo-cart-drawer-content">
                <?php if (empty($items)) : ?>
                    <div class="woo-cart-drawer-empty">
                        <p><?php echo esc_html($settings['empty_text']); ?></p>
                    </div>
                <?php else : ?>
                    <div class="woo-cart-drawer-list">
                        <?php foreach ($items as $cart_item_key => $cart_item) : ?>
                            <?php echo $this->cart_item_markup($cart_item_key, $cart_item); ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="woo-cart-drawer-summary">
                        <div class="woo-cart-drawer-total">
                            <strong><?php esc_html_e('Subtotal', 'woo-cart'); ?></strong>
                            <span><?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?></span>
                        </div>

                        <div class="woo-cart-drawer-actions">
                            <a class="woo-cart-drawer-button is-secondary" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <?php echo esc_html($settings['view_cart_text']); ?>
                            </a>
                            <a class="woo-cart-drawer-button" href="<?php echo esc_url(wc_get_checkout_url()); ?>">
                                <?php echo esc_html($settings['checkout_text']); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php

            return ob_get_clean();
        }

        private function cart_item_markup($cart_item_key, $cart_item)
        {
            $product = $cart_item['data'];

            if (!$product || !$product->exists() || $cart_item['quantity'] <= 0) {
                return '';
            }

            $product_name = $product->get_name();
            $product_url = $product->is_visible() ? $product->get_permalink($cart_item) : wc_get_cart_url();
            $product_image = $product->get_image('woocommerce_thumbnail');
            $line_price = WC()->cart->get_product_subtotal($product, $cart_item['quantity']);

            ob_start();
            ?>
            <div class="woo-cart-drawer-item">
                <a class="woo-cart-item-thumb" href="<?php echo esc_url($product_url); ?>">
                    <?php echo wp_kses_post($product_image); ?>
                </a>

                <div class="woo-cart-item-main">
                    <a class="woo-cart-item-title" href="<?php echo esc_url($product_url); ?>">
                        <?php echo esc_html($product_name); ?>
                    </a>
                    <div class="woo-cart-item-meta">
                        <span class="woo-cart-qty"><?php echo esc_html($cart_item['quantity']); ?></span>
                    </div>
                </div>

                <div class="woo-cart-item-price"><?php echo wp_kses_post($line_price); ?></div>

                <a
                    class="woo-cart-item-remove remove remove_from_cart_button"
                    href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
                    aria-label="<?php echo esc_attr(sprintf(__('Remove %s from cart', 'woo-cart'), $product_name)); ?>"
                    data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                    data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>"
                    data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                >&times;</a>
            </div>
            <?php

            return ob_get_clean();
        }

        private function cart_items()
        {
            if (!function_exists('WC') || !WC()->cart) {
                return array();
            }

            return WC()->cart->get_cart();
        }

        private function cart_count()
        {
            if (!function_exists('WC') || !WC()->cart) {
                return 0;
            }

            return WC()->cart->get_cart_contents_count();
        }

        private function cart_count_markup()
        {
            return '<span class="woo-cart-drawer-count">' . esc_html($this->cart_count()) . '</span>';
        }

        private function print_script()
        {
            ?>
            <script>
                (function () {
                    var drawer = document.getElementById('woo-cart-drawer');
                    var overlay = document.querySelector('.woo-cart-drawer-overlay');

                    if (!drawer || !overlay) {
                        return;
                    }

                    function setOpenState(isOpen) {
                        var trigger = document.querySelector('.woo-cart-floating-button');

                        drawer.classList.toggle('is-open', isOpen);
                        overlay.classList.toggle('is-open', isOpen);
                        document.body.classList.toggle('woo-cart-drawer-open', isOpen);
                        drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

                        if (trigger) {
                            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        }
                    }

                    document.addEventListener('click', function (event) {
                        if (event.target.closest('.woo-cart-floating-button')) {
                            event.preventDefault();
                            setOpenState(true);
                        }

                        if (event.target.closest('[data-woo-cart-close]')) {
                            event.preventDefault();
                            setOpenState(false);
                        }
                    });

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') {
                            setOpenState(false);
                        }
                    });
                })();
            </script>
            <?php
        }
    }
}

new Woo_Cart_Plugin();
