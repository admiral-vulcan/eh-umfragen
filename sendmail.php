<?php
require_once ("vendor/autoload.php");
require_once('gitignore/code.php');
require_once ('pwdChgSet.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendmail($content, $subject, $to) {

	$mail = new PHPMailer;

	$mail->CharSet = 'utf-8';     
	$mail->From = 'kontakt@eh-umfragen.de';
	$mail->FromName = 'EH-Umfragen';
	$mail->addAddress($to); // Der Name ist dabei optional 
	$mail->addReplyTo('kontakt@eh-umfragen.de'); // Antwortadresse festlegen
	//$mail->addCC('cc@beispiel.de'); 
	//$mail->addBCC('kontakt@eh-umfragen.de');
	$mail->isHTML(true); // Mail als HTML versenden 
	$mail->Subject = $subject; 
	$mail->Body = $content; 
	$mail->AltBody = 'Bitte öffne die Mail in einem HTML-fähigen Mail-Programm.';

    try {
        $result = $mail->send();
        if ($result) {
            echo "OK";
        } else {
            echo "ERROR";
        }
    } catch (Exception $e) {
        // Handle exceptions here, if necessary
        return $e;
    }

/*
	if(!) {
		echo 'Mail wurde nicht abgesendet';
		echo 'Fehlermeldung: ' . $mail->ErrorInfo;
	} 
	else { 
		echo 'Nachricht wurde abgesendet.';
	}

*/
}

function sendconfirmation($uid, $uemail, $target) {
    if ($target == "studs") {
        $mail1 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-du-1.html');
        $mail2 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-du-2.html');
    }
    else {
        $mail1 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-sie-1.html');
        $mail2 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-sie-2.html');
    }
    $link1 = "https://www.eh-umfragen.de?content=validate&uid=".encodeString($uid);
    $mail_content = $mail1.$link1.$mail2;
    //echo $uemail . $mail_content;
    return sendmail($mail_content, "Vielen Dank, nur noch ein Klick!", $uemail);
}

function sendCreatorConfirmation($cid, $uemail) { //, $target
    $mail1 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-creator-1.html');
    $mail2 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-creator-2.html');
    $link1 = "https://www.eh-umfragen.de?content=validate&cid=".encodeString($cid);
    $mail_content = $mail1.$link1.$mail2;
    //echo $uemail . $mail_content;
    return sendmail($mail_content, "Hey Creator, nur noch ein Klick!", $uemail);
}

function sendCreatorNewPassword($email) {
    $mail1 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-pwd-chg-du-1.html');
    $mail2 = file_get_contents('https://' . $_SERVER['HTTP_HOST'] . '/maildata/mail-pwd-chg-du-2.html');
    $link1 = 'https://' . $_SERVER['HTTP_HOST'] . '?content=newpass&psetstr='.genPwdMailKey($email);
    $mail_content = $mail1.$link1.$mail2;
    //echo $uemail . $mail_content;
    return sendmail($mail_content, "Passwort zurücksetzen", $email);
}
/*
sendconfirmation("6e854g6d5g65b4","rau1@studnet.eh-ludwigsburg.de", "studs");
*/
?>