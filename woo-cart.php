<?php
/**
 * Plugin Name: Woo Cart
 * Description: A WooCommerce cart plugin.
 * Version: 1.0.0
 * Author: sablu hasan
 * Author URI: https://sablu-hasan.vercel.app/
 * Text Domain: woo-cart
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WOO_CART_OPTION_KEY', 'woo_cart_settings');

function woo_cart_default_settings()
{
    return array(
        'cart_title' => 'Your Cart',
        'cart_description' => 'Review your selected products before checkout.',
        'show_product_image' => '1',
        'product_image_radius' => '8',
        'cart_radius' => '8',
        'button_radius' => '6',
        'view_cart_text' => 'View cart',
        'checkout_text' => 'Checkout',
        'button_bg_color' => '#111827',
        'button_text_color' => '#ffffff',
        'cart_bg_color' => '#ffffff',
    );
}

function woo_cart_get_settings()
{
    return wp_parse_args(
        get_option(WOO_CART_OPTION_KEY, array()),
        woo_cart_default_settings()
    );
}

function woo_cart_sanitize_settings($input)
{
    $defaults = woo_cart_default_settings();

    return array(
        'cart_title' => sanitize_text_field($input['cart_title'] ?? $defaults['cart_title']),
        'cart_description' => sanitize_textarea_field($input['cart_description'] ?? $defaults['cart_description']),
        'show_product_image' => !empty($input['show_product_image']) ? '1' : '0',
        'product_image_radius' => absint($input['product_image_radius'] ?? $defaults['product_image_radius']),
        'cart_radius' => absint($input['cart_radius'] ?? $defaults['cart_radius']),
        'button_radius' => absint($input['button_radius'] ?? $defaults['button_radius']),
        'view_cart_text' => sanitize_text_field($input['view_cart_text'] ?? $defaults['view_cart_text']),
        'checkout_text' => sanitize_text_field($input['checkout_text'] ?? $defaults['checkout_text']),
        'button_bg_color' => sanitize_hex_color($input['button_bg_color'] ?? $defaults['button_bg_color']) ?: $defaults['button_bg_color'],
        'button_text_color' => sanitize_hex_color($input['button_text_color'] ?? $defaults['button_text_color']) ?: $defaults['button_text_color'],
        'cart_bg_color' => sanitize_hex_color($input['cart_bg_color'] ?? $defaults['cart_bg_color']) ?: $defaults['cart_bg_color'],
    );
}

add_action('admin_menu', 'woo_cart_add_settings_page');
function woo_cart_add_settings_page()
{
    add_menu_page(
        __('Woo Cart Settings', 'woo-cart'),
        __('Woo Cart', 'woo-cart'),
        'manage_options',
        'woo-cart-settings',
        'woo_cart_render_settings_page',
        'dashicons-cart',
        56
    );
}

add_action('admin_init', 'woo_cart_register_settings');
function woo_cart_register_settings()
{
    register_setting('woo_cart_settings_group', WOO_CART_OPTION_KEY, 'woo_cart_sanitize_settings');
}

function woo_cart_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $settings = woo_cart_get_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Woo Cart Settings', 'woo-cart'); ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields('woo_cart_settings_group'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="woo-cart-title"><?php esc_html_e('Cart title', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="woo-cart-title"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[cart_title]"
                            value="<?php echo esc_attr($settings['cart_title']); ?>"
                            class="regular-text"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-description"><?php esc_html_e('Description', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <textarea
                            id="woo-cart-description"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[cart_description]"
                            rows="3"
                            class="large-text"
                        ><?php echo esc_textarea($settings['cart_description']); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e('Product image', 'woo-cart'); ?></th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[show_product_image]"
                                value="1"
                                <?php checked($settings['show_product_image'], '1'); ?>
                            >
                            <?php esc_html_e('Show product images', 'woo-cart'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-product-image-radius"><?php esc_html_e('Product image radius', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="woo-cart-product-image-radius"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[product_image_radius]"
                            value="<?php echo esc_attr($settings['product_image_radius']); ?>"
                            min="0"
                            max="80"
                        > px
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-radius"><?php esc_html_e('Cart box radius', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="woo-cart-radius"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[cart_radius]"
                            value="<?php echo esc_attr($settings['cart_radius']); ?>"
                            min="0"
                            max="80"
                        > px
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-button-radius"><?php esc_html_e('Button radius', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="woo-cart-button-radius"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[button_radius]"
                            value="<?php echo esc_attr($settings['button_radius']); ?>"
                            min="0"
                            max="80"
                        > px
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-view-cart-text"><?php esc_html_e('View cart button text', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="woo-cart-view-cart-text"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[view_cart_text]"
                            value="<?php echo esc_attr($settings['view_cart_text']); ?>"
                            class="regular-text"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-checkout-text"><?php esc_html_e('Checkout button text', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="woo-cart-checkout-text"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[checkout_text]"
                            value="<?php echo esc_attr($settings['checkout_text']); ?>"
                            class="regular-text"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-button-bg-color"><?php esc_html_e('Button background color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-button-bg-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[button_bg_color]"
                            value="<?php echo esc_attr($settings['button_bg_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-button-text-color"><?php esc_html_e('Button text color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-button-text-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[button_text_color]"
                            value="<?php echo esc_attr($settings['button_text_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-bg-color"><?php esc_html_e('Cart background color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-bg-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[cart_bg_color]"
                            value="<?php echo esc_attr($settings['cart_bg_color']); ?>"
                        >
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('woocommerce_before_cart', 'woo_cart_render_cart_header', 5);
function woo_cart_render_cart_header()
{
    $settings = woo_cart_get_settings();

    if (empty($settings['cart_title']) && empty($settings['cart_description'])) {
        return;
    }
    ?>
    <div class="woo-cart-header">
        <?php if (!empty($settings['cart_title'])) : ?>
            <h2><?php echo esc_html($settings['cart_title']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($settings['cart_description'])) : ?>
            <p><?php echo esc_html($settings['cart_description']); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

add_action('wp_loaded', 'woo_cart_replace_woocommerce_buttons');
function woo_cart_replace_woocommerce_buttons()
{
    remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
    add_action('woocommerce_proceed_to_checkout', 'woo_cart_render_checkout_button', 20);

    remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10);
    remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20);
    add_action('woocommerce_widget_shopping_cart_buttons', 'woo_cart_render_mini_cart_buttons', 10);
}

function woo_cart_render_checkout_button()
{
    $settings = woo_cart_get_settings();
    ?>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-button button alt wc-forward">
        <?php echo esc_html($settings['checkout_text']); ?>
    </a>
    <?php
}

function woo_cart_render_mini_cart_buttons()
{
    $settings = woo_cart_get_settings();
    ?>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="button wc-forward">
        <?php echo esc_html($settings['view_cart_text']); ?>
    </a>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button checkout wc-forward">
        <?php echo esc_html($settings['checkout_text']); ?>
    </a>
    <?php
}

add_filter('woocommerce_cart_item_thumbnail', 'woo_cart_maybe_hide_product_image');
function woo_cart_maybe_hide_product_image($thumbnail)
{
    $settings = woo_cart_get_settings();

    if ($settings['show_product_image'] === '0') {
        return '';
    }

    return $thumbnail;
}

add_action('wp_head', 'woo_cart_print_dynamic_styles');
function woo_cart_print_dynamic_styles()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    $settings = woo_cart_get_settings();
    ?>
    <style id="woo-cart-dynamic-styles">
        .woocommerce-cart .woocommerce,
        .woocommerce-cart .cart-collaterals,
        .woo-cart-header {
            background: <?php echo esc_html($settings['cart_bg_color']); ?>;
            border-radius: <?php echo absint($settings['cart_radius']); ?>px;
        }

        .woo-cart-header {
            margin-bottom: 20px;
            padding: 18px 20px;
        }

        .woo-cart-header h2 {
            margin: 0 0 6px;
        }

        .woo-cart-header p {
            margin: 0;
        }

        .woocommerce-cart table.cart img,
        .woocommerce-mini-cart-item img {
            border-radius: <?php echo absint($settings['product_image_radius']); ?>px;
        }

        .woocommerce a.button,
        .woocommerce button.button,
        .woocommerce input.button,
        .woocommerce #respond input#submit,
        .woocommerce a.checkout-button {
            background-color: <?php echo esc_html($settings['button_bg_color']); ?>;
            border-radius: <?php echo absint($settings['button_radius']); ?>px;
            color: <?php echo esc_html($settings['button_text_color']); ?>;
        }

        .woocommerce a.button:hover,
        .woocommerce button.button:hover,
        .woocommerce input.button:hover,
        .woocommerce #respond input#submit:hover,
        .woocommerce a.checkout-button:hover {
            background-color: <?php echo esc_html($settings['button_bg_color']); ?>;
            color: <?php echo esc_html($settings['button_text_color']); ?>;
            opacity: 0.9;
        }
    </style>
    <?php
}
