<?php
// Database Configuration 
define('DB_HOST', 'localhost');
define('DB_NAME', 'allphptricks');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'newpassword');

// PayPal Configuration
define('PAYPAL_EMAIL', 'fakebusiny@gmail.com');
// ngrok address is update everytime when you start ngrok
define('RETURN_URL', 'https://3883-182-187-14-233.in.ngrok.io/PAYPAL/payment/return.php');
define('CANCEL_URL', 'https://3883-182-187-14-233.in.ngrok.io/PAYPAL/payment/cancel.php');
define('NOTIFY_URL', 'https://3883-182-187-14-233.in.ngrok.io/PAYPAL/payment/notify.php');
define('CURRENCY', 'USD');
define('SANDBOX', TRUE); // TRUE or FALSE 
define('LOCAL_CERTIFICATE', FALSE); // TRUE or FALSE

if (SANDBOX === TRUE) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}
// PayPal IPN Data Validate URL
define('PAYPAL_URL', $paypal_url);
