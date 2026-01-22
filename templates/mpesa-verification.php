<?php
/**
 * M-Pesa Payment Verification Page Template
 *
 * @package MPesa For WooCommerce
 * @since 3.1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Security check - validate order key
if (empty($_GET['order_id']) || empty($_GET['key'])) {
    wp_die(__('Invalid order verification link.', 'woocommerce'));
}

$order_id = absint($_GET['order_id']);
$order_key = sanitize_text_field($_GET['key']);
$order = wc_get_order($order_id);

if (!$order || !hash_equals($order->get_order_key(), $order_key)) {
    wp_die(__('Invalid order or order key.', 'woocommerce'));
}

// Get gateway instance to access settings
$gateway = WC()->payment_gateways->payment_gateways()['mpesa'] ?? null;
if (!$gateway) {
    wp_die(__('Payment gateway not found.', 'woocommerce'));
}

// Get masked phone number for display
$phone = $order->get_meta('mpesa_phone');
$masked_phone = $phone ? substr($phone, 0, 6) . '***' . substr($phone, -3) : '';

// Get configuration from settings
$timeout = absint($gateway->verification_timeout);
$pending_msg = esc_html($gateway->verification_pending_msg);
$success_msg = esc_html($gateway->verification_success_msg);
$error_msg = esc_html($gateway->verification_error_msg);
$resend_delay = absint($gateway->verification_resend_delay);
$max_resends = absint($gateway->verification_max_resends);
$bg_color = esc_attr($gateway->verification_bg_color);
$inherit_theme = $gateway->verification_inherit_theme;

// Get site name
$site_name = get_bloginfo('name');

// Calculate complementary gradient color (darker shade)
$bg_color_dark = $bg_color;
if (preg_match('/^#([A-Fa-f0-9]{6})$/', $bg_color, $matches)) {
    $hex = $matches[1];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Darken by 20%
    $r = max(0, min(255, $r * 0.8));
    $g = max(0, min(255, $g * 0.8));
    $b = max(0, min(255, $b * 0.8));

    $bg_color_dark = sprintf('#%02x%02x%02x', $r, $g, $b);
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo esc_html__('Payment Verification', 'woocommerce') . ' - ' . esc_html($site_name); ?></title>

    <?php if ($inherit_theme): ?>
        <?php wp_head(); ?>
    <?php endif; ?>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            <?php if (!$inherit_theme): ?>
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            <?php endif; ?>
            background: linear-gradient(135deg, <?php echo $bg_color; ?> 0%, <?php echo $bg_color_dark; ?> 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verification-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            <?php if ($inherit_theme): ?>
            /* Allow theme styles to influence container */
            color: inherit;
            <?php endif; ?>
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }

        /* Spinner Animation */
        .spinner-container {
            margin: 30px auto;
            position: relative;
            width: 100px;
            height: 100px;
        }

        .spinner {
            width: 100px;
            height: 100px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00a651;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success Icon */
        .success-icon {
            display: none;
            width: 100px;
            height: 100px;
            margin: 30px auto;
            background: #00a651;
            border-radius: 50%;
            position: relative;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon::before {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 60px;
            font-weight: bold;
        }

        /* Error Icon */
        .error-icon {
            display: none;
            width: 100px;
            height: 100px;
            margin: 30px auto;
            background: #d32f2f;
            border-radius: 50%;
            position: relative;
            animation: scaleIn 0.5s ease-out;
        }

        .error-icon::before {
            content: "✕";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 60px;
            font-weight: bold;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .status-message {
            font-size: 18px;
            color: #333;
            margin: 20px 0;
            line-height: 1.6;
        }

        .phone-reminder {
            font-size: 14px;
            color: #666;
            margin: 15px 0;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 8px;
        }

        .phone-number {
            font-weight: bold;
            color: #00a651;
        }

        .timer {
            font-size: 14px;
            color: #999;
            margin: 10px 0;
        }

        .timer.warning {
            color: #ff9800;
            font-weight: bold;
        }

        .transaction-id {
            font-size: 16px;
            color: #00a651;
            font-weight: bold;
            margin: 15px 0;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 8px;
            word-break: break-all;
        }

        .resend-button {
            display: none;
            background: #00a651;
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .resend-button:hover:not(:disabled) {
            background: #008a42;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 166, 81, 0.4);
        }

        .resend-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .resend-info {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }

        .error-details {
            font-size: 14px;
            color: #d32f2f;
            margin: 15px 0;
            padding: 12px;
            background: #ffebee;
            border-radius: 8px;
            border-left: 4px solid #d32f2f;
        }

        .redirect-info {
            font-size: 14px;
            color: #666;
            margin-top: 15px;
            font-style: italic;
        }

        /* Loading dots animation */
        .loading-dots::after {
            content: '.';
            animation: dots 1.5s steps(3, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }

        /* Mobile responsive */
        @media (max-width: 600px) {
            .verification-container {
                padding: 30px 20px;
            }

            .status-message {
                font-size: 16px;
            }

            .spinner-container,
            .success-icon,
            .error-icon {
                width: 80px;
                height: 80px;
            }

            .spinner {
                width: 80px;
                height: 80px;
            }

            .success-icon::before,
            .error-icon::before {
                font-size: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="logo">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                echo '<img src="' . esc_url($logo[0]) . '" alt="' . esc_attr($site_name) . '">';
            } else {
                echo '<h2>' . esc_html($site_name) . '</h2>';
            }
            ?>
        </div>

        <!-- Spinner (shown while pending) -->
        <div class="spinner-container" id="spinner">
            <div class="spinner"></div>
        </div>

        <!-- Success Icon (hidden initially) -->
        <div class="success-icon" id="success-icon"></div>

        <!-- Error Icon (hidden initially) -->
        <div class="error-icon" id="error-icon"></div>

        <!-- Status Message -->
        <div class="status-message" id="status-message">
            <span class="loading-dots"><?php echo $pending_msg; ?></span>
        </div>

        <!-- Phone Reminder -->
        <div class="phone-reminder" id="phone-reminder">
            Check your phone <?php if ($masked_phone) echo '<span class="phone-number">(' . esc_html($masked_phone) . ')</span>'; ?> for M-Pesa prompt
        </div>

        <!-- Timer -->
        <div class="timer" id="timer">
            Timeout in: <span id="countdown"><?php echo $timeout; ?></span> seconds
        </div>

        <!-- Transaction ID (hidden initially) -->
        <div class="transaction-id" id="transaction-id" style="display: none;">
            Transaction ID: <span id="transaction-id-value"></span>
        </div>

        <!-- Error Details (hidden initially) -->
        <div class="error-details" id="error-details" style="display: none;"></div>

        <!-- Resend Button -->
        <button class="resend-button" id="resend-button" style="display: none;">
            Resend Payment Request
        </button>
        <div class="resend-info" id="resend-info" style="display: none;">
            Attempts remaining: <span id="resend-count"><?php echo $max_resends; ?></span>
        </div>

        <!-- Redirect Info -->
        <div class="redirect-info" id="redirect-info" style="display: none;"></div>
    </div>

    <script>
        // Configuration from PHP
        const CONFIG = {
            orderId: <?php echo json_encode($order_id); ?>,
            orderKey: <?php echo json_encode($order_key); ?>,
            timeout: <?php echo json_encode($timeout); ?>,
            resendDelay: <?php echo json_encode($resend_delay); ?>,
            maxResends: <?php echo json_encode($max_resends); ?>,
            messages: {
                pending: <?php echo json_encode($pending_msg); ?>,
                success: <?php echo json_encode($success_msg); ?>,
                error: <?php echo json_encode($error_msg); ?>
            },
            ajaxUrl: <?php echo json_encode(home_url('wc-api/')); ?>,
            checkoutUrl: <?php echo json_encode(wc_get_checkout_url()); ?>
        };

        // State management
        let state = {
            status: 'pending',
            startTime: Date.now(),
            elapsedTime: 0,
            pollInterval: null,
            countdownInterval: null,
            resendAttempts: 0,
            isResending: false
        };

        // DOM elements
        const elements = {
            spinner: document.getElementById('spinner'),
            successIcon: document.getElementById('success-icon'),
            errorIcon: document.getElementById('error-icon'),
            statusMessage: document.getElementById('status-message'),
            phoneReminder: document.getElementById('phone-reminder'),
            timer: document.getElementById('timer'),
            countdown: document.getElementById('countdown'),
            transactionId: document.getElementById('transaction-id'),
            transactionIdValue: document.getElementById('transaction-id-value'),
            errorDetails: document.getElementById('error-details'),
            resendButton: document.getElementById('resend-button'),
            resendInfo: document.getElementById('resend-info'),
            resendCount: document.getElementById('resend-count'),
            redirectInfo: document.getElementById('redirect-info')
        };

        // Initialize
        window.addEventListener('DOMContentLoaded', function() {
            console.log('[MPesa Verification] Page loaded, starting verification...');
            startVerification();
        });

        function startVerification() {
            // Start polling for payment status
            pollPaymentStatus();
            state.pollInterval = setInterval(pollPaymentStatus, 3000); // Poll every 3 seconds

            // Start countdown timer
            updateCountdown();
            state.countdownInterval = setInterval(updateCountdown, 1000);

            // Show resend button after delay
            setTimeout(showResendButton, CONFIG.resendDelay * 1000);
        }

        function pollPaymentStatus() {
            state.elapsedTime = Math.floor((Date.now() - state.startTime) / 1000);

            console.log(`[MPesa Verification] Polling payment status... (${state.elapsedTime}s elapsed)`);

            fetch(CONFIG.ajaxUrl + 'mpesa_verify_payment?order_id=' + CONFIG.orderId + '&key=' + CONFIG.orderKey, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('[MPesa Verification] Status:', data);

                if (data.status === 'success') {
                    handleSuccess(data);
                } else if (data.status === 'error') {
                    handleError(data);
                } else if (data.status === 'pending') {
                    // Continue polling
                    if (state.elapsedTime >= CONFIG.timeout) {
                        handleTimeout();
                    }
                }
            })
            .catch(error => {
                console.error('[MPesa Verification] AJAX error:', error);
            });
        }

        function handleSuccess(data) {
            console.log('[MPesa Verification] Payment confirmed!');

            state.status = 'success';
            stopPolling();

            // Update UI
            elements.spinner.style.display = 'none';
            elements.successIcon.style.display = 'block';
            elements.statusMessage.innerHTML = CONFIG.messages.success;
            elements.phoneReminder.style.display = 'none';
            elements.timer.style.display = 'none';
            elements.resendButton.style.display = 'none';
            elements.resendInfo.style.display = 'none';

            // Show transaction ID
            if (data.transaction_id) {
                elements.transactionIdValue.textContent = data.transaction_id;
                elements.transactionId.style.display = 'block';
            }

            // Show redirect message
            elements.redirectInfo.textContent = 'Redirecting in 3 seconds...';
            elements.redirectInfo.style.display = 'block';

            // Redirect after 3 seconds
            setTimeout(function() {
                if (data.redirect_url) {
                    console.log('[MPesa Verification] Redirecting to:', data.redirect_url);
                    window.location.href = data.redirect_url;
                }
            }, 3000);
        }

        function handleError(data) {
            console.log('[MPesa Verification] Payment error:', data.message);

            state.status = 'error';
            stopPolling();

            // Update UI
            elements.spinner.style.display = 'none';
            elements.errorIcon.style.display = 'block';
            elements.statusMessage.innerHTML = CONFIG.messages.error;
            elements.phoneReminder.style.display = 'none';
            elements.timer.style.display = 'none';
            elements.resendButton.style.display = 'none';
            elements.resendInfo.style.display = 'none';

            // Show error details
            if (data.message) {
                elements.errorDetails.textContent = data.message;
                elements.errorDetails.style.display = 'block';
            }

            // Show redirect message
            elements.redirectInfo.textContent = 'Redirecting to checkout in 5 seconds...';
            elements.redirectInfo.style.display = 'block';

            // Redirect to checkout after 5 seconds
            setTimeout(function() {
                console.log('[MPesa Verification] Redirecting to checkout');
                window.location.href = CONFIG.checkoutUrl;
            }, 5000);
        }

        function handleTimeout() {
            console.log('[MPesa Verification] Verification timeout');

            handleError({
                message: 'Payment verification timeout. Please check your phone and try again.'
            });
        }

        function updateCountdown() {
            const remaining = CONFIG.timeout - state.elapsedTime;

            if (remaining <= 0) {
                elements.countdown.textContent = '0';
                elements.timer.classList.add('warning');
                return;
            }

            elements.countdown.textContent = remaining;

            if (remaining <= 20) {
                elements.timer.classList.add('warning');
            }
        }

        function showResendButton() {
            if (state.status === 'pending' && state.resendAttempts < CONFIG.maxResends) {
                elements.resendButton.style.display = 'inline-block';
                elements.resendInfo.style.display = 'block';

                console.log('[MPesa Verification] Showing resend button');
            }
        }

        function resendSTKPush() {
            if (state.isResending || state.resendAttempts >= CONFIG.maxResends) {
                return;
            }

            state.isResending = true;
            state.resendAttempts++;

            elements.resendButton.disabled = true;
            elements.resendButton.textContent = 'Sending...';

            console.log(`[MPesa Verification] Resending STK push (attempt ${state.resendAttempts}/${CONFIG.maxResends})`);

            fetch(CONFIG.ajaxUrl + 'mpesa_resend_stk?order_id=' + CONFIG.orderId + '&key=' + CONFIG.orderKey, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('[MPesa Verification] Resend response:', data);

                if (data.success) {
                    elements.statusMessage.innerHTML = 'Payment request resent! <span class="loading-dots">Please check your phone</span>';

                    // Reset timer
                    state.startTime = Date.now();
                    state.elapsedTime = 0;

                    // Update resend count
                    elements.resendCount.textContent = CONFIG.maxResends - state.resendAttempts;

                    // Re-enable button if attempts remain
                    if (state.resendAttempts < CONFIG.maxResends) {
                        setTimeout(function() {
                            elements.resendButton.disabled = false;
                            elements.resendButton.textContent = 'Resend Payment Request';
                            state.isResending = false;
                        }, 5000); // Wait 5 seconds before allowing another resend
                    } else {
                        elements.resendButton.style.display = 'none';
                        elements.resendInfo.textContent = 'Maximum resend attempts reached';
                    }
                } else {
                    alert(data.message || 'Failed to resend payment request. Please try again.');
                    elements.resendButton.disabled = false;
                    elements.resendButton.textContent = 'Resend Payment Request';
                    state.isResending = false;
                }
            })
            .catch(error => {
                console.error('[MPesa Verification] Resend error:', error);
                alert('Failed to resend payment request. Please try again.');
                elements.resendButton.disabled = false;
                elements.resendButton.textContent = 'Resend Payment Request';
                state.isResending = false;
            });
        }

        function stopPolling() {
            if (state.pollInterval) {
                clearInterval(state.pollInterval);
                state.pollInterval = null;
            }
            if (state.countdownInterval) {
                clearInterval(state.countdownInterval);
                state.countdownInterval = null;
            }
        }

        // Attach resend button click handler
        elements.resendButton.addEventListener('click', resendSTKPush);

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopPolling();
        });
    </script>

    <?php if ($inherit_theme): ?>
        <?php wp_footer(); ?>
    <?php endif; ?>
</body>
</html>
