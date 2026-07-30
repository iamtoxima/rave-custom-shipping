# Rave Custom Shipping for WooCommerce

This plugin adds a selectable WooCommerce shipping method for orders whose shipping fee will be calculated after checkout.

Author: [Toxima](https://github.com/iamtoxima)

[![PHP lint](https://github.com/iamtoxima/rave-custom-shipping/actions/workflows/php-lint.yml/badge.svg)](https://github.com/iamtoxima/rave-custom-shipping/actions/workflows/php-lint.yml)

## Customer experience

- The customer can choose the method alongside Flat Rate, Free Shipping, and other methods enabled for their shipping zone.
- The checkout adds no shipping charge yet.
- A customizable message explains that the shipping fee will be calculated and emailed after the order is placed.
- The same message appears in the customer's order details and WooCommerce order emails.

## Store setup

1. Install and activate WooCommerce.
2. Install and activate this plugin.
3. Open **WooCommerce > Settings > Shipping > Shipping zones**.
4. Edit a shipping zone and select **Add shipping method**.
5. Choose **Rave Custom Shipping**.
6. Edit the method title and customer message.
7. Save the shipping method.

Each shipping zone can use a different title and message.

Only orders that select **Rave Custom Shipping** are intended for the planned shipping quote and separate shipping-payment workflow. Orders using Flat Rate, Free Shipping, local pickup, or another shipping method are outside that workflow.

## Important behavior

The rate added to checkout is `0`. This plugin does not calculate the eventual fee and does not send a separate quote email. The store owner must calculate the fee and contact the customer using the store's existing process.

## Compatibility

- PHP 7.4 or newer
- WordPress 6.7 or newer
- WooCommerce 9.7 or newer
- WooCommerce High-Performance Order Storage
- WooCommerce classic and block-based Cart/Checkout

## Security

- Settings use WooCommerce's authenticated settings workflow.
- Titles and messages are validated, length-limited, and stored as plain text.
- All customer-facing output is escaped.
- The plugin does not execute direct database queries or process raw shell input.

## License

Rave Custom Shipping is licensed under the GNU General Public License v2.0 or later. See [LICENSE](LICENSE).

## Contributing and security

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

Please do not report security vulnerabilities in public issues. Follow [SECURITY.md](SECURITY.md) to submit a private report.
