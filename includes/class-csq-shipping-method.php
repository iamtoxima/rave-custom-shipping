<?php
/**
 * Rave Custom Shipping method.
 *
 * @package CustomShippingQuote
 */

defined( 'ABSPATH' ) || exit;

/**
 * Provides a selectable shipping method with a fee confirmed after checkout.
 */
final class CSQ_Shipping_Method extends WC_Shipping_Method {

	/**
	 * Shipping method identifier.
	 */
	const METHOD_ID = 'csq_shipping_quote';

	/**
	 * Maximum customer-facing title length.
	 */
	const MAX_TITLE_LENGTH = 80;

	/**
	 * Maximum customer-facing message length.
	 */
	const MAX_MESSAGE_LENGTH = 500;

	/**
	 * Sanitized checkout message.
	 *
	 * @var string
	 */
	private $customer_message = '';

	/**
	 * Creates a shipping-zone method instance.
	 *
	 * @param int $instance_id Shipping zone method instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->id                 = self::METHOD_ID;
		$this->method_title       = __( 'Rave Custom Shipping', 'custom-shipping-quote' );
		$this->method_description = __( 'Let customers place an order before the shipping fee is calculated and emailed to them.', 'custom-shipping-quote' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->tax_status         = 'none';

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		$this->init_instance_form_fields();
		$this->init_settings();

		$this->title            = self::normalize_title( $this->get_option( 'title', self::get_default_title() ) );
		$this->customer_message = self::normalize_message( $this->get_option( 'message', self::get_default_message() ) );
	}

	/**
	 * Defines settings editable for every shipping-zone instance.
	 *
	 * @return void
	 */
	public function init_instance_form_fields() {
		$this->instance_form_fields = array(
			'title'   => array(
				'title'       => __( 'Method title', 'custom-shipping-quote' ),
				'type'        => 'text',
				'default'     => self::get_default_title(),
				'description' => __( 'The name customers see when choosing a shipping method.', 'custom-shipping-quote' ),
				'desc_tip'    => true,
			),
			'message' => array(
				'title'       => __( 'Customer message', 'custom-shipping-quote' ),
				'type'        => 'textarea',
				'default'     => self::get_default_message(),
				'description' => __( 'Shown under this shipping option at checkout and repeated in order details and order emails. Plain text only.', 'custom-shipping-quote' ),
				'desc_tip'    => false,
				'css'         => 'min-width: 400px; min-height: 100px;',
			),
		);
	}

	/**
	 * Adds the pending-quote rate to the package.
	 *
	 * @param array $package Shipping package supplied by WooCommerce.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$rate_id = $this->get_rate_id();

		$this->add_rate(
			array(
				'id'      => $rate_id,
				'label'   => $this->title,
				'cost'    => 0,
				'taxes'   => false,
				'package' => $package,
			)
		);

		if ( isset( $this->rates[ $rate_id ] ) && is_callable( array( $this->rates[ $rate_id ], 'set_description' ) ) ) {
			$this->rates[ $rate_id ]->set_description( $this->customer_message );
		}
	}

	/**
	 * Validates and sanitizes the method title before WooCommerce saves it.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Submitted value.
	 * @return string
	 * @throws Exception When the title is empty or too long.
	 */
	public function validate_title_field( $key, $value ) {
		unset( $key );

		$title = self::sanitize_setting_value( $value, false );

		if ( '' === $title ) {
			throw new Exception( esc_html__( 'The shipping method title cannot be empty.', 'custom-shipping-quote' ) );
		}

		if ( self::string_length( $title ) > self::MAX_TITLE_LENGTH ) {
			throw new Exception(
				esc_html(
					sprintf(
						/* translators: %d: maximum number of characters. */
						__( 'The shipping method title must be %d characters or fewer.', 'custom-shipping-quote' ),
						self::MAX_TITLE_LENGTH
					)
				)
			);
		}

		return $title;
	}

	/**
	 * Validates and sanitizes the customer message before WooCommerce saves it.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Submitted value.
	 * @return string
	 * @throws Exception When the message is empty or too long.
	 */
	public function validate_message_field( $key, $value ) {
		unset( $key );

		$message = self::sanitize_setting_value( $value, true );

		if ( '' === $message ) {
			throw new Exception( esc_html__( 'The customer shipping message cannot be empty.', 'custom-shipping-quote' ) );
		}

		if ( self::string_length( $message ) > self::MAX_MESSAGE_LENGTH ) {
			throw new Exception(
				esc_html(
					sprintf(
						/* translators: %d: maximum number of characters. */
						__( 'The customer shipping message must be %d characters or fewer.', 'custom-shipping-quote' ),
						self::MAX_MESSAGE_LENGTH
					)
				)
			);
		}

		return $message;
	}

	/**
	 * Returns the configured message for a shipping-zone method instance.
	 *
	 * WordPress caches option reads, and this request-level cache avoids repeated
	 * normalization when an order is rendered in more than one location. The
	 * cache naturally invalidates at the end of the request.
	 *
	 * @param int $instance_id Shipping zone method instance ID.
	 * @return string
	 */
	public static function get_instance_message( $instance_id ) {
		static $message_cache = array();

		$instance_id = absint( $instance_id );

		if ( isset( $message_cache[ $instance_id ] ) ) {
			return $message_cache[ $instance_id ];
		}

		$settings = get_option( 'woocommerce_' . self::METHOD_ID . '_' . $instance_id . '_settings', array() );
		$message  = is_array( $settings ) && isset( $settings['message'] ) ? $settings['message'] : self::get_default_message();

		$message_cache[ $instance_id ] = self::normalize_message( $message );

		return $message_cache[ $instance_id ];
	}

	/**
	 * Returns the default customer-facing method title.
	 *
	 * @return string
	 */
	public static function get_default_title() {
		return __( 'Rave Custom Shipping', 'custom-shipping-quote' );
	}

	/**
	 * Returns the default customer-facing explanation.
	 *
	 * @return string
	 */
	public static function get_default_message() {
		return __( 'The shipping fee will be calculated and emailed to you after you place your order.', 'custom-shipping-quote' );
	}

	/**
	 * Normalizes a title loaded from storage.
	 *
	 * @param mixed $value Stored value.
	 * @return string
	 */
	private static function normalize_title( $value ) {
		$title = self::sanitize_setting_value( $value, false );

		if ( '' === $title ) {
			return self::get_default_title();
		}

		return self::limit_string( $title, self::MAX_TITLE_LENGTH );
	}

	/**
	 * Normalizes a message loaded from storage.
	 *
	 * @param mixed $value Stored value.
	 * @return string
	 */
	private static function normalize_message( $value ) {
		$message = self::sanitize_setting_value( $value, true );

		if ( '' === $message ) {
			return self::get_default_message();
		}

		return self::limit_string( $message, self::MAX_MESSAGE_LENGTH );
	}

	/**
	 * Converts an untrusted setting into plain text.
	 *
	 * @param mixed $value     Submitted or stored value.
	 * @param bool  $multiline Whether line breaks are allowed.
	 * @return string
	 */
	private static function sanitize_setting_value( $value, $multiline ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = wp_unslash( (string) $value );

		return $multiline ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	}

	/**
	 * Gets a UTF-8-aware string length when the extension is available.
	 *
	 * @param string $value Text to measure.
	 * @return int
	 */
	private static function string_length( $value ) {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
	}

	/**
	 * Limits a stored string defensively without changing valid settings.
	 *
	 * @param string $value  Text to limit.
	 * @param int    $length Maximum length.
	 * @return string
	 */
	private static function limit_string( $value, $length ) {
		if ( self::string_length( $value ) <= $length ) {
			return $value;
		}

		return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $length, 'UTF-8' ) : substr( $value, 0, $length );
	}
}
