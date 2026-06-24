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
        'show_floating_cart' => '1',
        'drawer_title' => 'Shopping Cart',
        'floating_icon_bg_color' => '#1d4ed8',
        'floating_icon_color' => '#ffffff',
        'badge_bg_color' => '#ef4444',
        'button_bg_color' => '#111827',
        'button_text_color' => '#ffffff',
        'cart_bg_color' => '#ffffff',
        'drawer_bg_color' => '#ffffff',
        'drawer_text_color' => '#111827',
        'drawer_overlay_color' => '#000000',
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
        'show_floating_cart' => !empty($input['show_floating_cart']) ? '1' : '0',
        'drawer_title' => sanitize_text_field($input['drawer_title'] ?? $defaults['drawer_title']),
        'floating_icon_bg_color' => sanitize_hex_color($input['floating_icon_bg_color'] ?? $defaults['floating_icon_bg_color']) ?: $defaults['floating_icon_bg_color'],
        'floating_icon_color' => sanitize_hex_color($input['floating_icon_color'] ?? $defaults['floating_icon_color']) ?: $defaults['floating_icon_color'],
        'badge_bg_color' => sanitize_hex_color($input['badge_bg_color'] ?? $defaults['badge_bg_color']) ?: $defaults['badge_bg_color'],
        'button_bg_color' => sanitize_hex_color($input['button_bg_color'] ?? $defaults['button_bg_color']) ?: $defaults['button_bg_color'],
        'button_text_color' => sanitize_hex_color($input['button_text_color'] ?? $defaults['button_text_color']) ?: $defaults['button_text_color'],
        'cart_bg_color' => sanitize_hex_color($input['cart_bg_color'] ?? $defaults['cart_bg_color']) ?: $defaults['cart_bg_color'],
        'drawer_bg_color' => sanitize_hex_color($input['drawer_bg_color'] ?? $defaults['drawer_bg_color']) ?: $defaults['drawer_bg_color'],
        'drawer_text_color' => sanitize_hex_color($input['drawer_text_color'] ?? $defaults['drawer_text_color']) ?: $defaults['drawer_text_color'],
        'drawer_overlay_color' => sanitize_hex_color($input['drawer_overlay_color'] ?? $defaults['drawer_overlay_color']) ?: $defaults['drawer_overlay_color'],
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
                    <th scope="row"><?php esc_html_e('Floating cart icon', 'woo-cart'); ?></th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[show_floating_cart]"
                                value="1"
                                <?php checked($settings['show_floating_cart'], '1'); ?>
                            >
                            <?php esc_html_e('Show right-bottom cart icon on frontend', 'woo-cart'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-drawer-title"><?php esc_html_e('Drawer title', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="woo-cart-drawer-title"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[drawer_title]"
                            value="<?php echo esc_attr($settings['drawer_title']); ?>"
                            class="regular-text"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-floating-icon-bg-color"><?php esc_html_e('Floating icon background', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-floating-icon-bg-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[floating_icon_bg_color]"
                            value="<?php echo esc_attr($settings['floating_icon_bg_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-floating-icon-color"><?php esc_html_e('Floating icon color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-floating-icon-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[floating_icon_color]"
                            value="<?php echo esc_attr($settings['floating_icon_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-badge-bg-color"><?php esc_html_e('Cart count badge color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-badge-bg-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[badge_bg_color]"
                            value="<?php echo esc_attr($settings['badge_bg_color']); ?>"
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

                <tr>
                    <th scope="row">
                        <label for="woo-cart-drawer-bg-color"><?php esc_html_e('Drawer background color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-drawer-bg-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[drawer_bg_color]"
                            value="<?php echo esc_attr($settings['drawer_bg_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-drawer-text-color"><?php esc_html_e('Drawer text color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-drawer-text-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[drawer_text_color]"
                            value="<?php echo esc_attr($settings['drawer_text_color']); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="woo-cart-drawer-overlay-color"><?php esc_html_e('Drawer overlay color', 'woo-cart'); ?></label>
                    </th>
                    <td>
                        <input
                            type="color"
                            id="woo-cart-drawer-overlay-color"
                            name="<?php echo esc_attr(WOO_CART_OPTION_KEY); ?>[drawer_overlay_color]"
                            value="<?php echo esc_attr($settings['drawer_overlay_color']); ?>"
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

        .woo-cart-floating-button {
            align-items: center;
            background: linear-gradient(145deg, <?php echo esc_html($settings['floating_icon_bg_color']); ?>, <?php echo esc_html($settings['button_bg_color']); ?>);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 18px;
            bottom: 24px;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.28);
            color: <?php echo esc_html($settings['floating_icon_color']); ?>;
            cursor: pointer;
            display: flex;
            height: 58px;
            justify-content: center;
            position: fixed;
            right: 24px;
            text-decoration: none;
            transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease;
            width: 58px;
            z-index: 99999;
        }

        .woo-cart-floating-button:hover,
        .woo-cart-floating-button:focus {
            color: <?php echo esc_html($settings['floating_icon_color']); ?>;
            box-shadow: 0 22px 48px rgba(15, 23, 42, 0.34);
            opacity: 1;
            outline: none;
            transform: translateY(-3px) scale(1.02);
        }

        .woo-cart-floating-button svg {
            display: block;
            height: 28px;
            width: 28px;
        }

        .woo-cart-floating-button svg path {
            stroke: currentColor;
        }

        .woo-cart-floating-count {
            align-items: center;
            background: <?php echo esc_html($settings['badge_bg_color']); ?>;
            border: 2px solid #ffffff;
            border-radius: 999px;
            color: #ffffff;
            display: flex;
            font-size: 12px;
            font-weight: 700;
            height: 24px;
            justify-content: center;
            line-height: 1;
            min-width: 24px;
            padding: 0 6px;
            position: absolute;
            right: -7px;
            top: -9px;
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.32);
        }

        .woo-cart-drawer-overlay {
            background: <?php echo esc_html($settings['drawer_overlay_color']); ?>;
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

        .woo-cart-drawer {
            background: <?php echo esc_html($settings['drawer_bg_color']); ?>;
            bottom: 0;
            box-shadow: -22px 0 52px rgba(15, 23, 42, 0.2);
            color: <?php echo esc_html($settings['drawer_text_color']); ?>;
            display: flex;
            flex-direction: column;
            max-width: min(440px, 92vw);
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(105%);
            transition: transform 260ms ease;
            width: 440px;
            z-index: 99999;
        }

        .woo-cart-drawer.is-open {
            transform: translateX(0);
        }

        .woo-cart-drawer-overlay.is-open {
            opacity: 0.45;
            pointer-events: auto;
        }

        body.woo-cart-drawer-open {
            overflow: hidden;
        }

        .woo-cart-drawer-header {
            align-items: center;
            border-bottom: 1px solid rgba(148, 163, 184, 0.24);
            display: flex;
            justify-content: space-between;
            min-height: 72px;
            padding: 20px 24px;
        }

        .woo-cart-drawer-title {
            color: <?php echo esc_html($settings['drawer_text_color']); ?>;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.2;
            margin: 0;
        }

        .woo-cart-drawer-close {
            align-items: center;
            background: transparent;
            border: 0;
            color: <?php echo esc_html($settings['drawer_text_color']); ?>;
            cursor: pointer;
            display: flex;
            border-radius: 10px;
            height: 38px;
            justify-content: center;
            padding: 0;
            transition: background-color 160ms ease;
            width: 38px;
        }

        .woo-cart-drawer-close:hover,
        .woo-cart-drawer-close:focus {
            background: rgba(148, 163, 184, 0.14);
            outline: none;
        }

        .woo-cart-drawer-close svg {
            height: 22px;
            width: 22px;
        }

        .woo-cart-drawer-content {
            flex: 1;
            overflow-y: auto;
            padding: 18px 24px 24px;
        }

        .woo-cart-drawer-content,
        .woo-cart-drawer-content a,
        .woo-cart-drawer-content .woocommerce-mini-cart__total {
            color: <?php echo esc_html($settings['drawer_text_color']); ?>;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart-item {
            background: rgba(248, 250, 252, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 14px;
            display: block;
            list-style: none;
            margin: 0;
            min-height: 100px;
            padding: 12px 42px 12px 104px;
            position: relative;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart-item a:not(.remove) {
            display: block;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.35;
            text-decoration: none;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart-item img {
            background: #f8fafc;
            border-radius: <?php echo absint($settings['product_image_radius']); ?>px;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.18);
            float: none;
            height: 76px;
            left: 12px;
            margin: 0;
            object-fit: cover;
            position: absolute;
            top: 12px;
            width: 76px;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart-item .quantity {
            color: rgba(15, 23, 42, 0.72);
            display: block;
            font-size: 13px;
            line-height: 1.3;
            margin-top: 8px;
        }

        .woo-cart-drawer-content .remove,
        .woo-cart-drawer-content a.remove {
            align-items: center;
            background: rgba(15, 23, 42, 0.06);
            border-radius: 999px;
            color: <?php echo esc_html($settings['drawer_text_color']); ?> !important;
            display: flex;
            font-size: 18px;
            font-weight: 400;
            height: 24px;
            justify-content: center;
            line-height: 1;
            position: absolute;
            right: 10px;
            text-decoration: none;
            top: 10px;
            width: 24px;
        }

        .woo-cart-drawer-content .remove:hover,
        .woo-cart-drawer-content a.remove:hover {
            background: <?php echo esc_html($settings['badge_bg_color']); ?>;
            color: #ffffff !important;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart__total {
            align-items: center;
            border-top: 1px solid rgba(148, 163, 184, 0.22);
            display: flex;
            font-size: 16px;
            justify-content: space-between;
            margin: 20px 0 0;
            padding-top: 18px;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart__total strong {
            font-weight: 700;
        }

        .woo-cart-drawer-content .woocommerce-mini-cart__buttons {
            display: grid;
            gap: 10px;
            margin: 16px 0 0;
        }

        .woo-cart-drawer-content .button {
            background-color: <?php echo esc_html($settings['button_bg_color']); ?>;
            border-radius: <?php echo absint($settings['button_radius']); ?>px;
            box-shadow: none;
            color: <?php echo esc_html($settings['button_text_color']); ?>;
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            min-height: 46px;
            padding: 15px 18px;
            text-align: center;
            text-decoration: none;
            transition: opacity 160ms ease, transform 160ms ease;
            width: 100%;
        }

        .woo-cart-drawer-content .button:hover,
        .woo-cart-drawer-content .button:focus {
            color: <?php echo esc_html($settings['button_text_color']); ?>;
            opacity: 0.9;
            outline: none;
            transform: translateY(-1px);
        }

        .woo-cart-drawer-content .woocommerce-mini-cart__empty-message {
            background: rgba(248, 250, 252, 0.8);
            border: 1px dashed rgba(148, 163, 184, 0.45);
            border-radius: 14px;
            margin: 0;
            padding: 28px 20px;
            text-align: center;
        }

        @media (max-width: 782px) {
            .woo-cart-floating-button {
                bottom: 18px;
                border-radius: 16px;
                height: 54px;
                right: 18px;
                width: 54px;
            }

            .woo-cart-drawer {
                max-width: 100vw;
                width: 100vw;
            }
        }
    </style>
    <?php
}

add_action('wp_footer', 'woo_cart_render_floating_cart');
function woo_cart_render_floating_cart()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    $settings = woo_cart_get_settings();

    if ($settings['show_floating_cart'] !== '1') {
        return;
    }

    echo woo_cart_get_floating_cart_markup();
    echo woo_cart_get_cart_drawer_markup();
    woo_cart_print_drawer_script();
}

add_filter('woocommerce_add_to_cart_fragments', 'woo_cart_update_floating_cart_fragment');
function woo_cart_update_floating_cart_fragment($fragments)
{
    $settings = woo_cart_get_settings();

    if ($settings['show_floating_cart'] !== '1') {
        return $fragments;
    }

    $fragments['.woo-cart-floating-button'] = woo_cart_get_floating_cart_markup();
    $fragments['.woo-cart-drawer-content'] = woo_cart_get_cart_drawer_content_markup();

    return $fragments;
}

function woo_cart_get_floating_cart_markup()
{
    if (!function_exists('WC') || !WC()->cart) {
        return '';
    }

    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_label = sprintf(
        /* translators: %d: cart item count. */
        _n('%d item in cart', '%d items in cart', $cart_count, 'woo-cart'),
        $cart_count
    );

    ob_start();
    ?>
    <button class="woo-cart-floating-button" type="button" aria-label="<?php echo esc_attr($cart_label); ?>" aria-controls="woo-cart-drawer" aria-expanded="false">
        <span class="woo-cart-floating-count"><?php echo esc_html($cart_count); ?></span>
        <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none">
            <path d="M6.5 8.5h11l-.75 8.25a2 2 0 0 1-1.99 1.81H9.24a2 2 0 0 1-1.99-1.81L6.5 8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 8.5a3 3 0 0 1 6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>
    <?php

    return ob_get_clean();
}

function woo_cart_get_cart_drawer_markup()
{
    $settings = woo_cart_get_settings();

    ob_start();
    ?>
    <div class="woo-cart-drawer-overlay" data-woo-cart-close></div>
    <aside id="woo-cart-drawer" class="woo-cart-drawer" aria-hidden="true">
        <div class="woo-cart-drawer-header">
            <h2 class="woo-cart-drawer-title"><?php echo esc_html($settings['drawer_title']); ?></h2>
            <button class="woo-cart-drawer-close" type="button" aria-label="<?php esc_attr_e('Close cart', 'woo-cart'); ?>" data-woo-cart-close>
                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <?php echo woo_cart_get_cart_drawer_content_markup(); ?>
    </aside>
    <?php

    return ob_get_clean();
}

function woo_cart_get_cart_drawer_content_markup()
{
    if (!function_exists('woocommerce_mini_cart')) {
        return '';
    }

    ob_start();
    ?>
    <div class="woo-cart-drawer-content">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php

    return ob_get_clean();
}

function woo_cart_print_drawer_script()
{
    ?>
    <script>
        (function () {
            var drawer = document.getElementById('woo-cart-drawer');
            var overlay = document.querySelector('.woo-cart-drawer-overlay');
            var trigger = document.querySelector('.woo-cart-floating-button');

            if (!drawer || !overlay || !trigger) {
                return;
            }

            function openDrawer() {
                var currentTrigger = document.querySelector('.woo-cart-floating-button');

                drawer.classList.add('is-open');
                overlay.classList.add('is-open');
                document.body.classList.add('woo-cart-drawer-open');
                drawer.setAttribute('aria-hidden', 'false');

                if (currentTrigger) {
                    currentTrigger.setAttribute('aria-expanded', 'true');
                }
            }

            function closeDrawer() {
                var currentTrigger = document.querySelector('.woo-cart-floating-button');

                drawer.classList.remove('is-open');
                overlay.classList.remove('is-open');
                document.body.classList.remove('woo-cart-drawer-open');
                drawer.setAttribute('aria-hidden', 'true');

                if (currentTrigger) {
                    currentTrigger.setAttribute('aria-expanded', 'false');
                }
            }

            document.addEventListener('click', function (event) {
                if (event.target.closest('.woo-cart-floating-button')) {
                    event.preventDefault();
                    openDrawer();
                }

                if (event.target.closest('[data-woo-cart-close]')) {
                    event.preventDefault();
                    closeDrawer();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeDrawer();
                }
            });
        })();
    </script>
    <?php
}
