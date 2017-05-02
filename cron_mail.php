<?php 
require('../phpmailer/PHPMailerAutoload.php');
session_start();
function check_null($value)
{
	if(is_null($value) || ($value == NULL) || ($value == 'NULL'))
	{
		return '';
	}
	else
	{
		return $value;
	}
}
function corn_mail($fileName, $data)
{		
	
	
		$mail = new PHPMailer();
		$mail->IsSMTP();
        $mailbody = '';
		for($i=0;$i<count($data);$i++){
			$mailbody		.=	 $data[$i]."<br />";	
		}
		
		$mailubject			= $fileName;
		$subject_evernote 	= mb_encode_mimeheader($mailubject);
		$mail->CharSet   	= 'UTF-8';
		$mail->ContentType  = 'text/html';
		$mail->SMTPAuth 	= true;
		$mail->Host       	= "smtp.gmail.com";
		$mail->SMTPSecure	= "ssl";
		$mail->Port       	= 465;
		$mail->Username 	= "alljasappusers@gmail.com";
		$mail->Password 	= "jasapp@jasapp";
		$mail->isHTML 		= true; 
		//$mail->From 		= "alljasappusers@gmail.com"; 
		$mail->From 		= "sebastienburdge@gmail.com";
		$mail->FromName 	= "Sebastien";
		mb_internal_encoding("UTF-8");
		$mail->Subject		= $subject_evernote;
		$mail->Body   		= $mailbody;
		$mail->AddAddress("alljasappusers@gmail.com");
		
		$mail->Send();		
		$response= NULL;
		if($mail->IsError()) { 	
			$response = "<br />Mailer Error: " . $mail->ErrorInfo;
		} else {
			$response = "<br />Message sent!";
		}
		
		echo $response;
		
	}

function amount_mail($oldamt, $newamt,$booking)
{
		$mail = new PHPMailer();
		$mail->IsSMTP();
        $mailbody = '';
		for($j=0;$j<count($oldamt);$j++){
			$mailbody		.=	 "Booking Number :".$booking[$j]."<br /> OLD AMOUNT : ".$oldamt[$j]."<br /> NEW AMOUNT : ".$newamt[$j]."<br />";	
		}
		
		$mailubject			= "Payment Amount";
		$subject_evernote 	= mb_encode_mimeheader($mailubject);
		$mail->CharSet   	= 'UTF-8';
		$mail->ContentType  = 'text/html';
		$mail->SMTPAuth 	= true;
		$mail->Host       	= "smtp.gmail.com";
		$mail->SMTPSecure	= "ssl";
		$mail->Port       	= 465;
		$mail->Username 	= "alljasappusers@gmail.com";
		$mail->Password 	= "jasapp@jasapp";
		$mail->isHTML 		= true; 
		//$mail->From 		= "alljasappusers@gmail.com"; 
		$mail->From 		= "sebastienburdge@gmail.com";
		$mail->FromName 	= "Sebastien";
		mb_internal_encoding("UTF-8");
		$mail->Subject		= $subject_evernote;
		$mail->Body   		= $mailbody;
		$mail->AddAddress("alljasappusers@gmail.com");
		
		$mail->Send();		
		$response= NULL;
		if($mail->IsError()) { 	
			$response = "<br />Mailer Error: " . $mail->ErrorInfo;
		} else {
			$response = "<br />Message sent!";
		}
		
		echo $response;
		
	}
	
?>