<?php
set_time_limit(60);
define('Token', 'SFToken');
define('Mode', 2); // 1: GET, 2: POST JSON.
define('SMTPAddress', 'smtp.mail.me.com');
define('SMTPPort', 587);
define('SMTPSSL', 2);
define('SMTPUsername', 'example@example.com');
define('SMTPPassword', 'example');
define('SMTPAuthor', 'SMSForwarder');
define('TargetEmail', 'example2@example.com');

require_once('PHPMailer/src/PHPMailer.php');
require_once('PHPMailer/src/Exception.php');
require_once('PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendMail(string $receiver, string $subject, string $content) {
	$mail = new PHPMailer(true);
	try {
		$mail->isSMTP();
		$mail->Host = SMTPAddress;
		$mail->Port = SMTPPort;
		$mail->SMTPAuth = true;
		$mail->Username = SMTPUsername;
		$mail->Password = SMTPPassword;
		$mail->Timeout = 15;

		switch (SMTPSSL) {
			case 2:
				// For iCloud mail.
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				break;
			case 1:
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				break;
		}

		$mail->CharSet = PHPMailer::CHARSET_UTF8;

		$mail->setFrom(SMTPUsername, SMTPAuthor);
		$mail->addAddress($receiver);

		$mail->isHTML(false);
		$mail->Subject = $subject;
		$mail->Body = $content;

		$mail->send();
		$mail->smtpClose();

		return null;
	} catch (Exception $e) {
		return $e;
	} catch (\Exception $e) {
		return $e;
	}
}

header('Content-Type: text/plain; charset=UTF-8');

if (!isset($_GET['token']) || $_GET['token'] !== Token) {
	http_response_code(404);
	die("File not found.\n");
}

if (Mode === 2) {
	if (!empty($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'json') === false) {
		http_response_code(403);
		die("Bad request content type.\n");
	}
	$content = file_get_contents('php://input');
	$json = json_decode($content);
	//file_put_contents("t.txt", $content);
	if ($json === null || $json === false || !isset($json->query, $json->query->sender, $json->query->message, $json->query->message->text)) {
		http_response_code(403);
		die("Bad JSON.\n");
	}
	$msgSender = $json->query->sender;
	$msgContent = $json->query->message->text;
} else {
	if (!isset($_GET['sender'], $_GET['content'])) {
		http_response_code(403);
		die("Bad content.\n");
	}
	$msgSender = $_GET['sender'];
	$msgContent = $_GET['content'];
}
$sendCount = 0;
while (($sendStatus = SendMail(TargetEmail, "Received SMS from {$msgSender}", "{$msgContent}\n")) !== null) {
	$sendCount++;
	if ($sendCount >= 3) {
		throw $sendStatus;
		break;
	}
}
?>
