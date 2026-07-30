<?php
/**
 * Plugin integration hooks.
 *
 * @package CustomShippingQuote
 */

defined( 'ABSPATH' ) || exit;

/**
 * Connects the shipping method to WooCommerce checkout, orders, and emails.
 */
final class CSQ_Plugin {

	/**
	 * Registers all runtime hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_shipping_methods', array( __CLASS__, 'register_shipping_method' ) );
		add_filter( 'woocommerce_cart_shipping_method_full_label', array( __CLASS__, 'append_classic_checkout_message' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( CSQ_PLUGIN_FILE ), array( __CLASS__, 'add_settings_action_link' ) );

		add_action( 'woocommerce_order_details_after_order_table', array( __CLASS__, 'render_order_details_message' ) );
		add_action( 'woocommerce_email_after_order_table', array( __CLASS__, 'render_email_message' ), 10, 4 );
	}

	/**
	 * Registers the method with WooCommerce.
	 *
	 * @param array $methods Existing shipping methods.
	 * @return array
	 */
	public static function register_shipping_method( $methods ) {
		$methods[ CSQ_Shipping_Method::METHOD_ID ] = 'CSQ_Shipping_Method';
		return $methods;
	}

	/**
	 * Displays the message below the rate in classic cart and checkout templates.
	 *
	 * Block checkout reads the same text from WC_Shipping_Rate::description.
	 *
	 * @param string           $label Shipping method label HTML.
	 * @param WC_Shipping_Rate $rate Shipping rate.
	 * @return string
	 */
	public static function append_classic_checkout_message( $label, $rate ) {
		if ( ! $rate instanceof WC_Shipping_Rate || CSQ_Shipping_Method::METHOD_ID !== $rate->get_method_id() ) {
			return $label;
		}

		$message = is_callable( array( $rate, 'get_description' ) ) ? $rate->get_description() : '';

		if ( '' === $message ) {
			$message = CSQ_Shipping_Method::get_instance_message( $rate->get_instance_id() );
		}

		return $label . '<br><small class="csq-shipping-message">' . nl2br( esc_html( $message ) ) . '</small>';
	}

	/**
	 * Adds a direct link to WooCommerce shipping settings.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array
	 */
	public static function add_settings_action_link( $links ) {
		$settings_url  = admin_url( 'admin.php?page=wc-settings&tab=shipping' );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Shipping settings', 'custom-shipping-quote' ) . '</a>';

		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Repeats the selected shipping explanation on customer order pages.
	 *
	 * @param WC_Order $order Order being rendered.
	 * @return void
	 */
	public static function render_order_details_message( $order ) {
		$messages = self::get_order_messages( $order );

		if ( empty( $messages ) ) {
			return;
		}

		echo '<section class="woocommerce-order-details csq-order-shipping-message">';
		echo '<h2>' . esc_html__( 'Shipping fee update', 'custom-shipping-quote' ) . '</h2>';

		foreach ( $messages as $message ) {
			echo '<p>' . nl2br( esc_html( $message ) ) . '</p>';
		}

		echo '</section>';
	}

	/**
	 * Repeats the selected shipping explanation in WooCommerce order emails.
	 *
	 * @param WC_Order      $order         Order included in the email.
	 * @param bool          $sent_to_admin Whether this is an admin email.
	 * @param bool          $plain_text    Whether this is a plain-text email.
	 * @param WC_Email|null $email         Email object, when supplied by WooCommerce.
	 * @return void
	 */
	public static function render_email_message( $order, $sent_to_admin, $plain_text, $email ) {
		unset( $sent_to_admin, $email );

		$messages = self::get_order_messages( $order );

		if ( empty( $messages ) ) {
			return;
		}

		if ( $plain_text ) {
			echo "\n" . esc_html__( 'Shipping fee update', 'custom-shipping-quote' ) . "\n";
			foreach ( $messages as $message ) {
				echo wp_strip_all_tags( $message ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			return;
		}

		echo '<div class="csq-email-shipping-message">';
		echo '<h2>' . esc_html__( 'Shipping fee update', 'custom-shipping-quote' ) . '</h2>';

		foreach ( $messages as $message ) {
			echo '<p>' . nl2br( esc_html( $message ) ) . '</p>';
		}

		echo '</div>';
	}

	/**
	 * Gets unique messages for selected custom-shipping items on an order.
	 *
	 * @param mixed $order Possible WooCommerce order object.
	 * @return array
	 */
	private static function get_order_messages( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return array();
		}

		$messages = array();

		foreach ( $order->get_shipping_methods() as $shipping_item ) {
			if ( CSQ_Shipping_Method::METHOD_ID !== $shipping_item->get_method_id() ) {
				continue;
			}

			$message              = CSQ_Shipping_Method::get_instance_message( $shipping_item->get_instance_id() );
			$messages[ $message ] = $message;
		}

		return array_values( $messages );
	}
}
