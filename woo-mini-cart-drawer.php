<?php
/**
 * Plugin Name: Mini Cart Drawer for WooCommerce
 * Plugin URI: https://appsbd.com/products/mini-cart-drawer-for-woocommerce/
 * Description: it's a plugin for WooCommerce Mini Cart.
 * Version: 4.0.5
 * Author: appsbd
 * Author URI: http://www.appsbd.com
 * Text Domain: minicart
 * Tested up to: 6.6
 * wc require:3.2.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package minicart-lite
 */

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once 'vendor/autoload.php';


use Minicart_Lite\Core\Minicart_Lite;


if ( true === \Minicart_Lite\Libs\Minicart_Loader::is_ready_to_load( __FILE__ ) ) {

	// __ron_start__
	Minicart_Lite::set_development_mode( true );

	// __ron_end__

	$mcnpos = new Minicart_Lite( __FILE__ );
	$mcnpos->start_plugin();
}



