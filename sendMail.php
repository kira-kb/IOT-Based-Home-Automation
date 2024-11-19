<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($email, $subject, $message, $altMessage)
{
//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output

        $mail->isSMTP(); //Send using SMTP
        $mail->SMTPAuth = true; //Enable SMTP authentication

        // #fff for GMAIL
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable implicit TLS encryption
        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
        $mail->Port = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->Username = 'kirubelbewket@gmail.com'; //SMTP username
        $mail->Password = 'jiku jxqa hnyx apkg'; //SMTP password

        // #fff using MAIL SANDBOX
        // $mail->Host = 'sandbox.smtp.mailtrap.io';
        // $mail->Port = 2525;
        // $mail->Username = '2c324278681842';
        // $mail->Password = '8f4ac586a8537c';

        //Recipients
        $mail->setFrom('noReplay@SMARTIZER.team', 'SMARITZER');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true); //Set email format to HTML

        // $mail->Subject = 'Here is the subject';
        // $mail->Body = 'This is the HTML message body <b>in bold!</b>';
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = $altMessage;

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }

        // echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}