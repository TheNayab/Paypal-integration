<?php
require_once('dbclass.php');


require '../PHPMailer-master/includes/Exception.php';
require '../PHPMailer-master/includes/SMTP.php';
require '../PHPMailer-master/includes/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



define("IPN_LOG_FILE", "ipn.log");
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();


//  ON EMAIL just For checking if PAYPAL send IPN or NOT
$mail = new PHPMailer(true);
try {
	//Server settings
	$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->isSMTP();
	$mail->Host       = 'smtp.gmail.com';
	$mail->SMTPAuth   = true;
	$mail->Username   = '20021519-014@uog.edu.pk';
	$mail->Password   = 'Arshad hmmm';
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$mail->Port       = 465;

	//Recipients
	$mail->setFrom('20021519-014@uog.edu.pk', 'Mailer');
	$mail->addAddress('nayab8609@gmail.com', 'Joe User');



	//Content

	$mail->Subject = 'Testing';
	$mail->Body    = $raw_post_data;
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	$mail->send();
	echo 'Message has been sent';
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error";
}

// foreach ($raw_post_array as $keyval) {
// 	$keyval = explode('=', $keyval);
// 	if (count($keyval) == 2)
// 		$myPost[$keyval[0]] = urldecode($keyval[1]);
// }

// Build the body of the verification post request, 
// adding the _notify-validate command.
// $req = 'cmd=_notify-validate';
// if (function_exists($get_magic_quotes_gpc)) {
// 	$get_magic_quotes_exists = true;
// }
// foreach ($myPost as $key => $value) {
// 	if ($get_magic_quotes_exists == true && $get_magic_quotes_gpc() == 1) {
// 		$value = urlencode(stripslashes($value));
// 	} else {
// 		$value = urlencode($value);
// 	}
// 	$req .= "&$key=$value";
// }


/*
Post IPN data back to PayPal using curl to 
validate the IPN data is valid & genuine
Anyone can fake IPN data, if you skip it.
*/
// $ch = curl_init(PAYPAL_URL);
// if ($ch == FALSE) {
// 	return FALSE;
// }

// curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
// curl_setopt($ch, CURLOPT_SSLVERSION, 6);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
// curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);


/*
This is often required if the server is missing a global cert
bundle, or is using an outdated one.
Please download the latest 'cacert.pem' from 
http://curl.haxx.se/docs/caextract.html
*/
// if (LOCAL_CERTIFICATE == TRUE) {
// 	curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
// }

// Set TCP timeout to 30 seconds
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 	'Connection: Close',
// 	'User-Agent: PHP-IPN-Verification-Script'
// ));

// $res = curl_exec($ch);



// cURL error
// if (curl_errno($ch) != 0) {
// 	curl_close($ch);
// 	exit;
// } else {
// 	curl_close($ch);
// }


/* 
 * Inspect IPN validation result and act accordingly 
 * Split response headers and payload, a better way for strcmp 
 */
// $tokens = explode("\r\n\r\n", trim($res));
// $res = trim(end($tokens));



// assign posted variables to local variables
$item_number = $_POST['item_number'];
$item_name = $_POST['item_name'];
$payment_status = $_POST['payment_status'];
$amount = $_POST['mc_gross'];
$currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
// $payer_email = $_POST['payer_email'];

$db = new DB;
$db->query("SELECT * FROM `payment_info` WHERE txn_id=:txn_id");
$db->bind(':txn_id', $txn_id);
$db->execute();
$unique_txn_id = $db->rowCount();

if (!empty($unique_txn_id)) {
	error_log(date('[Y-m-d H:i e] ') .
		"Invalid Transaction ID: $req" . PHP_EOL, 3, IPN_LOG_FILE);
	$db->close();

	exit();
} else {

	$db->query("INSERT INTO `payment_info`
			(`item_number`, `item_name`, `payment_status`,
				 `amount`, `currency`, `txn_id`)
			VALUES
			(:item_number, :item_name, :payment_status, 
				:amount, :currency, :txn_id)");
	$db->bind(":item_number", $item_number);
	$db->bind(":item_name", $item_name);
	$db->bind(":payment_status", $payment_status);
	$db->bind(":amount", $amount);
	$db->bind(":currency", $currency);
	$db->bind(":txn_id", $txn_id);
	$db->execute();
	/* error_log(date('[Y-m-d H:i e] '). 
		"Verified IPN: $req ". PHP_EOL, 3, IPN_LOG_FILE);
		*/
}
$db->close();
