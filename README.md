# M-Pesa for WooCommerce - Enhanced

A comprehensive WordPress plugin that integrates Safaricom M-Pesa payment gateway with WooCommerce, featuring real-time payment verification, customizable success redirects, and extensive logging capabilities.

## Features

### Core Functionality
- **M-Pesa STK Push** - Lipa Na M-Pesa Online integration
- **C2B Payments** - Customer to Business manual payments
- **Payment Verification** - Real-time payment status tracking
- **Transaction Reversals** - Automated refund processing
- **Bonga Points** - Support for Lipa Na Bonga Points payments

### Enhanced Payment Verification (v3.1.0)
- **Real-time Status Polling** - Checks payment status every 3 seconds
- **Custom Verification Page** - Beautiful animated interface with spinner and countdown timer
- **STK Push Resend** - Allow customers to resend payment requests with configurable attempts
- **Flexible Redirects** - Redirect to WordPress pages, external URLs, or default order received page
- **Customizable Styling** - Color picker for background colors and theme style inheritance
- **Mobile Optimized** - Responsive design with proper padding for all devices
- **Comprehensive Logging** - Detailed order notes for every payment action

### Security
- Order key validation using timing-safe comparison
- Sanitized inputs and escaped outputs
- Rate limiting on resend attempts
- Webhook signature verification
- No sensitive data exposure

## Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.0 or higher
- SSL Certificate (HTTPS required for M-Pesa IPN)
- M-Pesa Business Account (Paybill or Till Number)
- Safaricom Daraja API Credentials

## Installation

### Automatic Installation

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Search for "M-Pesa for WooCommerce"
4. Click **Install Now** and then **Activate**
5. Configure the plugin at **WooCommerce > Settings > Payments > Lipa Na M-Pesa**

### Manual Installation

1. Download the latest release from [GitHub Releases](https://github.com/MikohMick/M-pesa-plugin-for-WordPress-enhanced/releases)
2. Upload the plugin folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. Configure the plugin at **WooCommerce > Settings > Payments > Lipa Na M-Pesa**

## Configuration

### Basic Settings

Navigate to **WooCommerce > Settings > Payments > Lipa Na M-Pesa**

1. **Enable/Disable** - Enable the payment gateway
2. **Environment** - Select Sandbox (testing) or Live (production)
3. **Identifier Type** - Choose Paybill Number or Till Number
4. **Business Shortcode** - Your M-Pesa shortcode
5. **Consumer Key & Secret** - From Safaricom Daraja Portal
6. **Passkey** - Lipa Na M-Pesa Online Passkey

### Payment Verification Settings

Configure the custom verification page experience:

- **Enable Verification Page** - Turn on/off custom verification interface
- **Verification Timeout** - How long to wait for payment (30-300 seconds)
- **Custom Messages** - Pending, success, and error messages
- **Success Redirect** - Choose default page, WordPress page, or external URL
- **Resend Settings** - Button delay and maximum attempts

### Styling Settings

Customize the verification page appearance:

- **Background Color** - Choose any color with visual color picker
- **Inherit Theme Styles** - Use your WordPress theme fonts and styles

## Sandbox Testing

The plugin comes pre-configured with sandbox credentials for testing:

- **Environment**: Sandbox
- **Shortcode**: 174379
- **Consumer Key**: 9v38Dtu5u2BpsITPmLcXNWGMsjZRWSTG
- **Consumer Secret**: bclwIPkcRqw61yUt
- **Passkey**: bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919

### Test Phone Numbers

Use these Safaricom test numbers in sandbox:
- 254708374149
- 254711111111
- 254722111111

## Going Live

To move to production:

1. Apply for M-Pesa API credentials at [Safaricom Daraja Portal](https://developer.safaricom.co.ke/)
2. Get your Production API credentials
3. Update plugin settings:
   - Change Environment to **Live**
   - Enter your Production Consumer Key
   - Enter your Production Consumer Secret
   - Enter your Production Passkey
   - Enter your Live Shortcode
4. Update callback URLs in Daraja Portal:
   - Validation URL: `https://yoursite.com/wc-api/lipwa?action=validate&sign=YOUR_SIGNATURE`
   - Confirmation URL: `https://yoursite.com/wc-api/lipwa?action=confirm&sign=YOUR_SIGNATURE`
   - Reconciliation URL: `https://yoursite.com/wc-api/lipwa?action=reconcile&sign=YOUR_SIGNATURE`

## User Flow

### Customer Experience

1. Customer selects "Lipa Na M-Pesa" at checkout
2. Enters M-Pesa registered phone number
3. Clicks "Place Order"
4. Redirected to verification page with spinner
5. Receives STK push on phone
6. Enters M-Pesa PIN to complete payment
7. Verification page shows success checkmark
8. Automatically redirected to configured thank you page

### If Payment Fails

- Customer can resend STK push (configurable attempts)
- After timeout, redirected back to checkout
- Clear error messages displayed

## Order Notes

The plugin logs detailed information to WooCommerce order notes:

```
✓ Customer redirected to payment verification page
✓ Customer requested to resend STK push from verification page
✓ STK push resent successfully. New request ID: ws_CO_22012026123456789
✓ M-Pesa payment reconciliation successful. Phone: 254794988063, Amount: 1500
✓ Full MPesa Payment Received From 254794988063. Transaction ID UAMH94H7EH
```

## Troubleshooting

### Common Issues

**1. "Invalid Access Token" Error**
- Check your Consumer Key and Secret
- Ensure environment (Sandbox/Live) matches your credentials

**2. STK Push Not Received**
- Verify phone number is M-Pesa registered
- Check phone has network coverage
- Ensure phone is on (not off/airplane mode)

**3. Payment Confirmed but Order Pending**
- Check webhook URLs are correctly configured
- Verify SSL certificate is valid
- Check server firewall allows Safaricom IPs

**4. Verification Page Not Showing**
- Ensure "Enable Verification Page" is checked
- Clear WordPress and browser cache
- Check there are no JavaScript errors in browser console

### Debug Mode

Enable Debug Mode in settings to:
- View request payloads sent to M-Pesa
- See callback URLs for webhook configuration
- Display validation/confirmation endpoints

## Filters and Hooks

### Available Filters

```php
// Modify M-Pesa settings
add_filter('wc_mpesa_settings', function($config) {
    $config['timeout'] = 120;
    return $config;
});

// Customize verification page redirect
add_filter('wc_mpesa_verification_redirect', function($url, $order) {
    return add_query_arg('custom', 'param', $url);
}, 10, 2);
```

### Available Actions

```php
// After successful payment
add_action('send_to_external_api', function($order, $parsed, $settings) {
    // Send data to external system
}, 10, 3);

// When STK is resent
add_action('mpesa_stk_resent', function($order_id, $request_id) {
    // Custom logging or notifications
}, 10, 2);
```

## Changelog

### Version 3.1.0 (2024-01-31)
- Added custom payment verification page with real-time status tracking
- Implemented STK push resend functionality with configurable attempts
- Added flexible success redirects (WordPress page, external URL, or default)
- Introduced customizable verification page styling with color picker
- Added theme style inheritance option
- Implemented mobile-optimized responsive design
- Added comprehensive logging to order notes
- Enhanced webhook logging with detailed error tracking
- Improved security with hash-based order key validation

### Version 3.0.0
- Initial enhanced release
- Core M-Pesa integration
- STK Push and C2B support
- Basic payment processing

## Support

For issues, feature requests, or contributions:

- **GitHub Issues**: [Report an issue](https://github.com/MikohMick/M-pesa-plugin-for-WordPress-enhanced/issues)
- **Pull Requests**: Contributions are welcome!

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This plugin is licensed under the GPLv3 License. See the [LICENSE](LICENSE) file for details.

## Acknowledgements

- **M-Pesa** and the M-Pesa logo are registered trademarks of Safaricom Ltd
- **WordPress** and the WordPress logo are registered trademarks of Automattic Inc.
- **WooCommerce** and the WooCommerce logo are registered trademarks of Automattic Inc.

## Disclaimer

This plugin is not officially affiliated with or endorsed by Safaricom Ltd. It is an independent integration built using the publicly available Safaricom Daraja API.

## Credits

Developed by **Michael Mwanzia**

Special thanks to all contributors and the WordPress/WooCommerce community.
