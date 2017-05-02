<?php
session_start();
session_start();
// error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once ('../connect.php');
require_once ('../functions_general.php');
require ('../phpmailer/PHPMailerAutoload.php');

		
$employee_email = $_POST['employee_email'];
$employee_subject = $_POST['employee_subject'];
$email_msg = $_POST['email_msg'];

print_r ($_POST);exit;

if ($employee_email!='' and $email_msg!='')
{	
	$mailto = $employee_email;
	mb_internal_encoding("UTF-8");
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$subject_evernote = mb_encode_mimeheader($employee_subject);
	$mail->CharSet = 'UTF-8';
	$mail->ContentType = 'text/html';
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'ssl';
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 465; // or 587
	$mail->Username = "sebastienburdge@gmail.com";
	$mail->Password = "sebastien2015";
	$mail->isHTML = true;
	$mail->From = "sebastienburdge@gmail.com";
	$mail->FromName = "Julia";
	mb_internal_encoding("UTF-8");
	$mail->Subject = $subject_evernote;
	$mail->Body = $email_msg;
	$mail->ReturnPath = array(
		'julia.mesner@gmail.com'
	);
	$mail->AddAddress($employee_email); /*reciver email address*/
	$mail->AddReplyTo('julia.mesner@gmail.com', 'Information');
	$mail->Send();//
}