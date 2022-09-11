<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require $_SERVER["DOCUMENT_ROOT"].'/PHPMailer-master/src/PHPMailer.php';
require $_SERVER["DOCUMENT_ROOT"].'/PHPMailer-master/src/Exception.php';
require $_SERVER["DOCUMENT_ROOT"].'/PHPMailer-master/src/SMTP.php';


function sendMail($from, $tabAdress, $reply, $replyName, $isHTML, $subject, $body, $altBody="", $tabCC = array(), $tabAttachments = array()) {
    $iniFileName = $_SERVER["DOCUMENT_ROOT"].'/monApplication.ini';

    if (file_exists($iniFileName)) {
        $ini_array = parse_ini_file($iniFileName, true);
        //print_r($ini_array);
        
        $host = $ini_array['PHPMailer']['host'];
        $SMTPAuth = $ini_array['PHPMailer']['smtp_auth'];
        $port = $ini_array['PHPMailer']['port'];
        $SMTPDebug = $ini_array['PHPMailer']['smtp_debug'];
        $username = $ini_array['PHPMailer']['user_name'];
        $password = $ini_array['PHPMailer']['password'];

        //Create an instance; passing `true` enables exceptions
        //$mail = new PHPMailer(true);
        $mail = new PHPMailer();

        try {
            //Server settings
            $mail->SMTPDebug = constant('\PHPMailer\PHPMailer\SMTP::'. $SMTPDebug);          //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            
            $mail->Host       = $host;                                  //Set the SMTP server to send through
            $mail->SMTPAuth   = $SMTPAuth;                              //Enable SMTP authentication
            $mail->Username   = $username;                                   //SMTP username
            $mail->Password   = $password;                                   //SMTP password

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption ENCRYPTION_SMTPS, ENCRYPTION_STARTTLS
            $mail->Port       = $port;                                    //TCP port 465 to connect to ENCRYPTION_SMTPS; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($from);

            foreach ($tabAdress as $adress) {
                if (isset($adress["name"])) {
                    $mail->addAddress($adress["adress"], $adress["name"]); //Name is optional
                }
                else {
                    $mail->addAddress($adress["adress"]);
                }
            }

            if (strlen($replyName) > 0) {     
                $mail->addReplyTo($reply, $replyName);
            }
            else {
                $mail->addReplyTo($reply);
            }

            foreach ($tabCC as $adress) {
                if (isset($adress["name"])) {
                    $mail->addCC($adress["adress"], $adress["name"]); //Name is optional
                }
                else {
                    $mail->addCC($adress["adress"]);
                }
            }        

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            foreach ($tabAttachments as $attachment) {
                if (isset($attachment["name"])) {
                    $mail->addAttachment($attachment["path"], $attachment["name"]); //Name is optional
                }
                else {
                    $mail->addAttachment($attachment["path"]);
                }
            } 

            //Content
            $mail->isHTML($isHTML);            //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $mail->send();
            //echo 'Message envoyé';
        } catch (Exception $e) {
            echo "Message non envoyé. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    else {
        echo "Le fichier d'initialisation n'existe pas : ". $iniFileName;
    }
}