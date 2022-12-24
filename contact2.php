<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
require("phpmailer\src\PHPMailer.php");
require("phpmailer\src\SMTP.php");
//Load Composer's autoloader


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer\PHPMailer\PHPMailer();

try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'testingmaniac99@gmail.com';                     //SMTP username
    $mail->Password   = 'erhsgkoljkdxevga';                               //SMTP password
    $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('testingmaniac99@gmail.com', 'MoAuto');
    //$mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
    $mail->addAddress('a.abdou@aui.ma');               //Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $_POST['Subject'];
    $mail->Body    = 'Sender: ' .$_POST['Email'] ."\nMessage: ".$_POST['message'];
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo '<script type="text/javascript">';
    echo ' alert("Message sent successfuly, Thank you for your feedback")';  //not showing an alert box.
    echo '</script>';
    readfile('Contact.html');
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}