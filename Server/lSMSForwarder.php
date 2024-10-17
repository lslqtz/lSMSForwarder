<?php
define('Token', 'SFToken');
define('SMTPAddress', 'smtp.mail.me.com');
define('SMTPPort', 465);
define('SMTPSSL', true);
define('SMTPUsername', 'example@example.com');
define('SMTPPassword', 'example');
define('TargetEmail', 'example2@example.com');

require_once('PHPMailer/src/PHPMailer.php');
require_once('PHPMailer/src/Exception.php');
require_once('PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendMail(string $receiver, string $subject, string $content): bool {
	$mail = new PHPMailer(true);
	try {
		$mail->isSMTP();
		$mail->Host = SMTPAddress;
		$mail->Port = SMTPPort;
		$mail->SMTPAuth = true;
		$mail->Username = SMTPUsername;
		$mail->Password = SMTPPassword;

		if (SMTPSSL) {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		}

		$mail->CharSet = PHPMailer::CHARSET_UTF8;

		$mail->setFrom(SMTPUsername, Title);
		$mail->addAddress($receiver);

		$mail->isHTML(false);
		$mail->Subject = $subject;
		$mail->Body = $content;

		$mail->send();

		return true;
	} catch (Throwable $e) {
		return false;
	}
}

header('Content-Type: text/plain; charset=UTF-8');
if (!isset($_GET['token']) || $_GET['token'] !== Token) {
	http_response_code(404);
	die("File not found.\n");
}
if (!empty($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'json') === false) {
	http_response_code(403);
	die("Bad request content type.\n");
}
$content = file_get_contents('php://input');
$json = json_decode($content);
if ($json === null || $json === false || !isset($json->query, $json->query->sender, $json->query->message, $json->query->message->text)) {
	http_response_code(403);
	die("Bad JSON.\n");
}
$msgSender = $json->query->sender;
$msgContent = $json->query->message->text;
SendMail(TargetEmail, "Received SMS from {$msgSender}", "{$msgContent}\n");
?>
