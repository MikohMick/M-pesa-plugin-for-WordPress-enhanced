=== M-Pesa for WooCommerce - Enhanced ===
Contributors: Michael Mwanzia
Tags: mpesa, woocommerce, payment gateway, kenya, safaricom, mobile money, stk push
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 3.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Enhanced M-Pesa payment gateway for WooCommerce with real-time payment verification, customizable redirects, and comprehensive logging.

== Description ==

A comprehensive WordPress plugin that integrates Safaricom M-Pesa payment gateway with WooCommerce, featuring real-time payment verification, customizable success redirects, and extensive logging capabilities.

= Key Features =

* **M-Pesa STK Push** - Lipa Na M-Pesa Online integration
* **Real-time Payment Verification** - Live status tracking with animated interface
* **STK Push Resend** - Allow customers to retry failed payment requests
* **Flexible Redirects** - Redirect to WordPress pages or external URLs
* **Customizable Styling** - Color picker and theme style inheritance
* **C2B Payments** - Support for manual/offline payments
* **Transaction Reversals** - Automated refund processing
* **Bonga Points Support** - Lipa Na Bonga Points integration
* **Comprehensive Logging** - Detailed order notes for debugging
* **Mobile Optimized** - Responsive design for all devices

= New in Version 3.1.0 =

* Custom payment verification page with real-time status polling
* Animated spinner with countdown timer
* STK push resend functionality with configurable attempts
* Success redirect to WordPress pages or external URLs
* Background color customization with visual color picker
* Theme style inheritance option
* Mobile-optimized responsive design
* Enhanced logging to WooCommerce order notes
* Improved security with timing-safe order key validation

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 5.0 or higher
* PHP 7.0 or higher
* SSL Certificate (HTTPS required)
* M-Pesa Business Account
* Safaricom Daraja API Credentials

= Sandbox Testing =

The plugin comes pre-configured with sandbox credentials for immediate testing. No M-Pesa account required for development.

= Support =

For support, feature requests, or bug reports, please visit our [GitHub repository](https://github.com/MikohMick/M-pesa-plugin-for-WordPress-enhanced).

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin dashboard
2. Navigate to Plugins > Add New
3. Search for "M-Pesa for WooCommerce"
4. Click Install Now and then Activate
5. Go to WooCommerce > Settings > Payments > Lipa Na M-Pesa

= Manual Installation =

1. Download the plugin zip file
2. Upload to /wp-content/plugins/ directory
3. Activate through the Plugins menu
4. Configure at WooCommerce > Settings > Payments > Lipa Na M-Pesa

= Configuration =

1. Enable the payment method
2. Select Environment (Sandbox for testing, Live for production)
3. Choose Identifier Type (Paybill or Till Number)
4. Enter your Business Shortcode
5. Add Consumer Key and Consumer Secret from Daraja Portal
6. Enter your Lipa Na M-Pesa Online Passkey
7. Configure verification page settings (optional)
8. Save changes

== Frequently Asked Questions ==

= Do I need an M-Pesa business account? =

Yes, for live transactions you need a registered M-Pesa Paybill or Till number. For testing, use the included sandbox credentials.

= How do I get Daraja API credentials? =

Visit the [Safaricom Daraja Portal](https://developer.safaricom.co.ke/), create an account, and generate your API credentials.

= Does this work with WooCommerce Subscriptions? =

Yes, the plugin is compatible with WooCommerce Subscriptions for recurring payments.

= Can I customize the verification page? =

Yes! You can customize background colors, messages, timeout duration, and choose to inherit your theme styles.

= What happens if the customer misses the STK push? =

The customer can resend the STK push from the verification page. You can configure how many attempts are allowed.

= Is HTTPS required? =

Yes, M-Pesa requires HTTPS for webhook callbacks (IPN). Your site must have a valid SSL certificate.

= Can I redirect customers to a custom thank you page? =

Yes, you can redirect to any WordPress page or external URL after successful payment.

== Screenshots ==

1. Payment verification page with spinner and countdown
2. Success state with checkmark and transaction ID
3. Gateway settings - Basic configuration
4. Gateway settings - Verification page settings
5. Gateway settings - Styling options
6. Order notes showing detailed payment logs

== Changelog ==

= 3.1.0 - 2024-01-31 =
* Added custom payment verification page with real-time status tracking
* Implemented STK push resend functionality with configurable attempts
* Added flexible success redirects (WordPress page, external URL, or default)
* Introduced customizable verification page styling with color picker
* Added theme style inheritance option
* Implemented mobile-optimized responsive design with proper padding
* Added comprehensive logging to order notes for all payment actions
* Enhanced webhook logging with detailed error tracking
* Improved security with hash-based order key validation
* Updated to support WordPress 6.4 and WooCommerce 8.5

= 3.0.0 =
* Core M-Pesa STK Push integration
* C2B payment support
* Transaction reversals
* Bonga Points support
* Basic payment processing

== Upgrade Notice ==

= 3.1.0 =
Major update with custom payment verification page, real-time status tracking, and enhanced mobile experience. Backup your site before upgrading.

== Privacy Policy ==

This plugin integrates with Safaricom M-Pesa API. When customers make payments:
* Phone numbers are sent to Safaricom for payment processing
* Transaction IDs and payment details are stored in WooCommerce orders
* No sensitive payment data is stored in your WordPress database
* All communication with M-Pesa API is encrypted via HTTPS

== Third-Party Services ==

This plugin connects to:
* Safaricom Daraja API (api.safaricom.co.ke) for payment processing
* Privacy Policy: https://www.safaricom.co.ke/privacy-policy
* Terms of Service: https://www.safaricom.co.ke/terms-and-conditions

== Acknowledgements ==

* M-Pesa and the M-Pesa logo are registered trademarks of Safaricom Ltd
* WordPress and the WordPress logo are registered trademarks of Automattic Inc.
* WooCommerce and the WooCommerce logo are registered trademarks of Automattic Inc.
