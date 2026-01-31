<?php

/**
 * @package Mpesa for WooCommerce
 * @author Michael Mwanzia
 * @version 3.1.0
 *
 * Plugin Name: Mpesa Woocommerce
 * Plugin URI: https://github.com/MikohMick/M-pesa-plugin-for-WordPress-enhanced
 * Description: This plugin extends WordPress and WooCommerce functionality to integrate M-Pesa for making and receiving online payments with real-time payment verification.
 * Author: Michael Mwanzia
 * Version: 3.1.0
 * Author URI: https://github.com/MikohMick
 *
 * Requires at least: 5.0
 * Tested up to: 6.4
 *
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 *
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2024  Michael Mwanzia
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

define('WCM_VER', '3.1.0');
if (!defined('WCM_PLUGIN_FILE')) {
    define('WCM_PLUGIN_FILE', __FILE__);
}

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

register_activation_hook(__FILE__, function () {
    set_transient('wc-mpesa-activation-notice', true, 5);
});

add_action('admin_notices', function () {
    /* Check transient, if available display notice */
    if (get_transient('wc-mpesa-activation-notice')) {
        echo '<div class="updated notice is-dismissible">
            <p>Thank you for installing the M-Pesa for WooCommerce plugin! <strong>You are awesome</strong>.</p>
            <p>
            <a class="button" href="'.admin_url('admin.php?page=wc_mpesa_about').'">About M-Pesa for WooCommerce</a>
            <a class="button button-primary" href="'.admin_url('admin.php?page=wc_mpesa_go_live').'">How to Go Live</a>
            </p>
        </div>';
        /* Delete transient, only display this notice once. */
        delete_transient('wc-mpesa-activation-notice');
    }
});

/**
 * Initialize all plugin features and utilities
 */
new Osen\Woocommerce\Initialize;
new Osen\Woocommerce\Utilities;

/**
 * Initialize metaboxes for C2B API
 */
new Osen\Woocommerce\Post\Metaboxes\C2B;

/**
 * Initialize our admin menus (submenus under WooCommerce)
 */
new Osen\Woocommerce\Admin\Menu;
