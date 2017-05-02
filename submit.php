<?php
require_once ('../connect.php');
require_once ('../functions_general.php');
require ('../phpmailer/PHPMailerAutoload.php');
session_start();
function check_null($value)
{
	if (is_null($value) || ($value == NULL) || ($value == 'NULL'))
	{
		return '';
	}
	else
	{
		return $value;
	}
}
/*
** Author: Nazir Ahmed 
** URI : http://www.upwork.com
*/
// Taking ERROR to Level 0
error_reporting(0);
if (isset($_POST['reservation_number']))
{
	// echo '<pre>' . print_r ($_POST,true) . '</pre>';exit;
	// Fitting all Keys in Array
	$keys = array();
	// Generating MySQLi Connection
	$mysqli = new mysqli($db_host_connect, $db_user_connect, $db_pass_connect, $db_name_connect);
	// Generating Table Set 
	$_POST['intereste'] = implode(" ", $_POST['intereste']);
	
	foreach($_POST as $key => $value)
	{
		if ($key == 'form_id' || $key == 'reservation_number' || $key == 'submit')
		{
			continue;
		}
		else
		{
			if ($key == 'taxi' && $value == 'Yes')
			{
				$mailubject = "Taxi details";
				$fetchRoom = $mysqli->query("Select gcal_imports.name, gcal_imports.check_in, gcal_imports.phone_number, " . "gcal_imports.arrival_time,gcal_imports.arrival_place, gcal_imports.taxi_details," . " properties.name As name1 From gcal_imports Inner Join properties On" . " gcal_imports.property_id = properties.id Where gcal_imports.booking_number ='" . $pr_id . "'");
				if ($fetchRoom->num_rows > 0)
				{
					$row = $fetchRoom->fetch_assoc();
					$mailbody = "Name : " . $row['name'] . "<br /> Check in date : " . $row['check_in'] . "<br />" . "Phone number : " . $row['phone_number'] . "<br />Property : " . $row['name1'] . "<br />" . "Arrival place : " . $row['arrival_place'] . "<br />Arrival time : " . $row['arrival_time'] . "<br />" . "Detail for taxi : " . check_null($row['taxi_details']) . "<br /> ";
				}
				/*$mailfrom	=	'julia.mesner@gmail.com';
				$mailto 	=	'transportpriveleo@yahoo.com,julia.mesner@gmail.com';  */
				/* $mailto 	=	'v.s.ravlot@gmail.com';
				$mailfrom 	=	'v.s.ravlot@gmail.com';
				$headers = "MIME-Version: 1.0" . "\n";
				$headers .= "Content-type: text/html; charset=UTF-8" . "\n";
				$headers .= "From: <$mailfrom>" . "\n";
				$headers .= "Return-Path: <$mailfrom>" . "\n";
				$headers .= "Reply-To: <$mailfrom>";
				mb_internal_encoding("UTF-8");
				$subject_evernote = mb_encode_mimeheader($mailubject); */
				// mail($mailto, $subject_evernote, $mailbody, $headers);
			}
			$keys[] = !empty($value) ? $key . '="' . $mysqli->real_escape_string($value) . '"' : $key . '="' . '' . '"';
		}
	}
	// Assigning Term
	$term = implode(', ', $keys);
	if ($mysqli->connect_error)
	{
		// If Credentials goes wrong!, Die and Return Error!
		die('Could not Connect to database');
	}
	else
	{
		// print_r($_POST);
		$queryToDoMagica = "UPDATE gcal_imports set " . $term . " where booking_number='" . $mysqli->real_escape_string($_POST['reservation_number']) . "'";
		// Inserting Form Element
		$insertForm = $mysqli->query($queryToDoMagica);
		if ($mysqli->affected_rows > 0)
		{
			/*  mail body */
			/*$actual_link = "http://$_SERVER[HTTP_HOST]";
			$mailubject  = "Guest fill a form";
			$url = $actual_link."/detail.php?b=".$_POST['reservation_number'];
			$mailbody	 = "Please <a href='".$url."' >click here</a> for detail.php <br /> or use this  URl : ".$url."<br />";*/
			$fetchGuest = $mysqli->query("Select gcal_imports.name, gcal_imports.check_in, gcal_imports.check_out, gcal_imports.phone_number, " . "gcal_imports.arrival_time,gcal_imports.arrival_place, gcal_imports.taxi_details," . " properties.name As name1 From gcal_imports Inner Join properties On" . " gcal_imports.property_id = properties.id Where gcal_imports.booking_number ='" . $mysqli->real_escape_string($_POST['reservation_number']) . "'");
			if ($fetchGuest->num_rows > 0)
			{
				$row = $fetchGuest->fetch_assoc();
				$mailubject = "Guest " . $row['name'] . " for " . $row['name1'] . " filled a form";
				$mailbody.= " Guest  " . $row['name'] . "  staying at  " . $row['name1'] . "  from " . $row['check_in'] . " to " . $row['check_out'] . " filled a form. <br />He will arrive at " . $row['arrival_place'] . " at " . $row['arrival_time'] . "<br />";
				$actual_link = "http://$_SERVER[HTTP_HOST]";
				$url = $actual_link . "/spread/detail.php?b=" . $_POST['reservation_number'];
				$mailbody.= "Please <a href='" . $url . "' >click here</a> for detail.php <br /> or use this  URl : " . $url . "<br />";
			}
			else
			{
				$actual_link = "http://$_SERVER[HTTP_HOST]";
				$mailubject = "Guest fill a form";
				$url = $actual_link . "/spread/detail.php?b=" . $_POST['reservation_number'];
				$mailbody = "Please <a href='" . $url . "' >click here</a> for detail.php <br /> or use this  URl : " . $url . "<br />";
			}
			$mailfrom = 'julia.mesner@gmail.Com';
			$mailto = 'alljasappusers@gmail.com';
			mb_internal_encoding("UTF-8");
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$subject_evernote = mb_encode_mimeheader($mailubject);
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
			$mail->Body = $mailbody;
			$mail->ReturnPath = array(
				'julia.mesner@gmail.com'
			);
			$mail->AddAddress('julia.mesner@gmail.com'); /*reciver email address*/
			$mail->AddReplyTo('julia.mesner@gmail.com', 'Information');
			$mail->Send();//disabled when testing by nazir
			/* mail body */
			// echo '<h3 style="text-align:center;color: #32CD32;">Form Submitted! We will Contact you withing 24 hour</h3>';
			$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : '';
			$answer_php = './answer.php';
			if (!empty($lang) && $lang == 'FR')
			{
				$answer_php = './answerFR.php';
			}
			header('Location:' . $answer_php . '?b=' . security($_POST["reservation_number"]));
			exit;
		}
		else
		{
			echo '<h3 style="text-align:center">May be There is nothing new to update.</h3>';
		}
	}
	// Closing Connection
	$mysqli->close();
}
else
{
	echo '<h3 style="text-align:center">Invalid booking ID.</h3>';
}
?>