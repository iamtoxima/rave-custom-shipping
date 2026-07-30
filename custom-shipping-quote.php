<?php
/**
 * Plugin Name:       Rave Custom Shipping for WooCommerce
 * Plugin URI:        https://github.com/iamtoxima/rave-custom-shipping
 * Description:       Adds a selectable zero-cost shipping method whose final fee is calculated and emailed after the order is placed.
 * Version:           1.0.3
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 9.7
 * WC tested up to:   10.9.4
 * Author:            Toxima
 * Author URI:        https://github.com/iamtoxima
 * Update URI:        https://github.com/iamtoxima/rave-custom-shipping
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-shipping-quote
 * Domain Path:       /languages
 *
 * @package CustomShippingQuote
 */

defined( 'ABSPATH' ) || exit;

define( 'CSQ_VERSION', '1.0.3' );
define( 'CSQ_PLUGIN_FILE', __FILE__ );
define( 'CSQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Declares compatibility with supported WooCommerce storage and checkout features.
 *
 * @return void
 */
function csq_declare_woocommerce_compatibility() {
	if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		return;
	}

	\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CSQ_PLUGIN_FILE, true );
	\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', CSQ_PLUGIN_FILE, true );
}
add_action( 'before_woocommerce_init', 'csq_declare_woocommerce_compatibility' );

/**
 * Starts the plugin after all plugins have loaded.
 *
 * @return void
 */
function csq_bootstrap() {
	if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Shipping_Method' ) ) {
		add_action( 'admin_notices', 'csq_render_missing_woocommerce_notice' );
		return;
	}

	require_once CSQ_PLUGIN_DIR . 'includes/class-csq-shipping-method.php';
	require_once CSQ_PLUGIN_DIR . 'includes/class-csq-plugin.php';

	CSQ_Plugin::init();
}
add_action( 'plugins_loaded', 'csq_bootstrap', 20 );

/**
 * Shows a specific dependency error to administrators.
 *
 * @return void
 */
function csq_render_missing_woocommerce_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Rave Custom Shipping requires WooCommerce to be installed and active. Activate WooCommerce, then configure this method in WooCommerce > Settings > Shipping > Shipping zones.', 'custom-shipping-quote' );
	echo '</p></div>';
}
