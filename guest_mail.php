<?php
require_once('../connect.php');
require_once('cron_mail.php');
require_once('../functions_general.php');
include( 'function.php');
include_once('EmailMessage.php');
header('Content-Type: text/html; charset=utf-8');
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$mysqli->set_charset("utf8");
session_start();
if(!isset($_SESSION['admin'])) {
	header('location: index.php');
}
set_time_limit(4000); 
$imapPath = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
//$username = 'anuj@jasapp.com';
//$password = '9785914691';
$username = 'bnbstories@gmail.com';
$password = 'hostbetter';
	
try {
	
	$inbox = imap_open($imapPath,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
 
   /* ALL - return all messages matching the rest of the criteria
    ANSWERED - match messages with the \\ANSWERED flag set
    BCC "string" - match messages with "string" in the Bcc: field
    BEFORE "date" - match messages with Date: before "date"
    BODY "string" - match messages with "string" in the body of the message
    CC "string" - match messages with "string" in the Cc: field
    DELETED - match deleted messages
    FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    FROM "string" - match messages with "string" in the From: field
    KEYWORD "string" - match messages with "string" as a keyword
    NEW - match new messages
    OLD - match old messages
    ON "date" - match messages with Date: matching "date"
    RECENT - match messages with the \\RECENT flag set
    SEEN - match messages that have been read (the \\SEEN flag is set)
    SINCE "date" - match messages with Date: after "date"
    SUBJECT "string" - match messages with "string" in the Subject:
    TEXT "string" - match messages with text "string"
    TO "string" - match messages with "string" in the To:
    UNANSWERED - match messages that have not been answered
    UNDELETED - match messages that are not deleted
    UNFLAGGED - match messages that are not flagged
    UNKEYWORD "string" - match messages that do not have the keyword "string"
    UNSEEN - match messages which have not been read yet
 
// search and get unseen emails, function will return email ids
*/
//$emails = imap_search($inbox,'NEW');
 
$date = date ( "d M Y", strtotime ( "-7 days" ) );
	$emails = imap_search($inbox, "SINCE \"$date\"");
		
/*$sub1	= "Réservation instantanée confirmée - Evelyn Edith";
$emails = imap_search($inbox, "SUBJECT \"$sub1\"");*/
$output = array();
$ress = array();
$i=0;
$j=0;

if(!empty($emails)){
	  foreach($emails as $mail) {
		
			
			$headerInfo 	= imap_headerinfo($inbox,$mail);
			
			$current_date	= date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
			$convert_date	= date('Y-m-d',strtotime($headerInfo->date));
			$recive_date	= date('Y-m-d H:i:s',strtotime($headerInfo->date));
						
			$end_date 		= "";
			$dummy 			= array();		
			$end_date 		= $convert_date;			
								
			
			if (strpos($headerInfo->subject, '=?UTF-8?Q?RE_:_R=C3=A9servation_') !== false || strpos($headerInfo->subject, '=?UTF-8?Q?R=C3=A9servation_instantan=C3=A9e_confirm=C3=A9e_') !== false ){
				
				$reply		= $headerInfo->reply_to;
				$reply_to	= $reply[0]->mailbox.'@'.$reply[0]->host;
			
				$emailMessage = new EmailMessage($inbox, $mail);
				$emailMessage->fetch();
				
				
				$content 						= imap_fetchbody($inbox,$mail,2);
				$dummy[] 						= $content;
				$content_1						= $content;				
				$content 						= strip_tags($content);
				$content						= str_replace("=20","",$content);				
				$content						= preg_replace('/\s+/',' ', $content);
				$content						= str_replace(" = "," ",$content);
				$content						= str_replace("= ","",$content);
				$content						= str_replace("=","",$content);
				$subject						= str_replace("=","",$headerInfo->subject);
			 		if(strpos($headerInfo->subject, '=?UTF-8?Q?R=C3=A9servation_instantan=C3=A9e_confirm=C3=A9e_') !== false){
					
					$msg_containing_text		 	= strafter($content,"d'Airbnb depuis");
					$msg_containing_text			= strbefore($msg_containing_text,'Envoyez un message');
					$msg_containing_text			= trim($msg_containing_text);
					 
				 	$msg 							= substr(strstr($msg_containing_text," "), 1);
					
					$bno_containing_text		 	= strafter($content,"Code de confirmation");
					$bno_containing_text			= strbefore($bno_containing_text,'Voir le');
					
					$booking_number 				= $bno_containing_text;
					
					$guest_name						= explode('-',$headerInfo->subject);
									
					$name							= str_replace('?','',strbefore($guest_name[2],'='));
					
				}else{
				$msg_containing_text		 	= strafter($content,'e-mail.');
				$posmsg 						= strpos($msg_containing_text,'Rappelez-vous : ');
				if($posmsg){
					$msg_containing_text			= strbefore($msg_containing_text,'Rappelez-vous : ');
					$msg 							= $msg_containing_text;
				}else{
					$msg_containing_text			= strbefore($msg_containing_text,'VC3A9rifications');
					$msg_contain					= array();
					$msg_contain = $msg_name 		= explode(' ',$msg_containing_text);					
														array_splice( $msg_contain, -4 );
					$msg							= implode( " ", $msg_contain );
				}
			
				
			if(strpos($content,'En savoir plus.'))
				{
			
				$name_containing_text			= strafter($content,'En savoir plus.');
				$name_containing_text 			= strbefore($name_containing_text,'voyageur');
				
				$pos = strpos($name_containing_text, "VC3A9rifications");
				if($pos){
						$name_contain		= array();
						$name_containing	= strbefore($name_containing_text,"VC3A9rifications");
					 	$name_contain	 	= explode(' ',$name_containing);
						$name				= $name_contain[1];		 		
				}else{
						$name	=  strstr($name_containing_text,"+",true);
				}
			}else{
						//print_r($msg_name);
					$name = $msg_name[count($msg_name)-4];				
								
			}
		}
		
					 $msg = stringformat($msg);
					
					$name =  stringformat($name);
			
				  $check_in_containing_text		= strafter($content,'ArrivC3A9e');
				  $check_in						= strbefore($check_in_containing_text,'DC3A9part');
			      $check_in						= get_date_in_eng($check_in);
				
					
	
	
			
				$output[$i]['msg']				= $msg;
				$output[$i]['name']				= trim($name,' ');
				$output[$i]['check_in']			= trim($check_in,' ');
				$output[$i]['booking_number'] 	= trim($booking_number,' ');
				$output[$i]['recive_date'] 		= trim($recive_date,' ');
				$output[$i]['reply_to'] 		= trim($reply_to,' ');
				$output[$i]['subject']			= str_replace('?','',stringformat($subject));
							
			}elseif(strpos($headerInfo->subject, '=?UTF-8?Q?Demande_pour') !== false){
				$sub								= str_replace("=","",$headerInfo->subject);
				$title = $sub 						= str_replace('?','',stringformat($sub));
				$sub						 		= strafter($sub,'Demande pour');
				$ress[$j]['sub']					= strbefore($sub,'pour');
				$ress[$j]['title']				= $title;
				$j++;
			}
			
		
		$i++;
	}
}
imap_expunge($inbox);
imap_close($inbox);
}catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
/*echo "<pre>";
print_r($output);*/
//die;
$flag = 0;
if(!empty($output)){
	
	foreach($output as $detail)
	{
		if(!empty($detail['name']))
		{
			
			
			$check_in_date	= date("Y-m-d",strtotime($detail['check_in']));
			$msg 		= mysqli_real_escape_string($mysqli, $detail['msg']);
			$msubject 	= $detail['subject'];
			
			
			
								
			$res_sql		= $mysqli->query('select booking_number,property_id from gcal_imports where name like "'.$detail['name'].'%" and check_in_new = "'.$check_in_date.'" ');
			$booking		= $res_sql->fetch_assoc();
			
			$booking_number	= isset($booking['booking_number'])?$booking['booking_number']:$detail['booking_number'];
			$p_id			= isset($booking['property_id'])?$booking['property_id']:'';
			$check_in_date	= isset($check_in_date)?$check_in_date:'';
			$msg			= isset($msg)?$msg:'';
			$recive_date	= isset($detail['recive_date'])?$detail['recive_date']:'';
			
			if(empty($booking_number))
			{				
				$res1 = $mysqli->query("select * from guest_msg where reply_to = '".$detail['reply_to']."'");
				if($res1->num_rows >=1){			 	
					while($rows	= $res1->fetch_assoc()){
					if(!empty($rows['booking_number']))
							$booking_number 	= $rows['booking_number'];
							$p_id				= $rows['property_id'];											
					}					
				}
				$res2 = $mysqli->query("select * from guest_msg");
				while($rows1	= $res2->fetch_assoc()){
					if(!empty($rows1['booking_number'])&& !empty($rows1['email_title']) ){
						$mysqli->query("update guest_msg set property_id = '".$rows1['property_id']."', booking_number = '".$rows1['booking_number']."' where email_title = '".$rows1['email_title']."'");
					}
				}
			 }else{
					$mysqli->query("update guest_msg set property_id = '".$p_id."', booking_number = '".$booking_number."' where reply_to = '".$detail['reply_to']."'");
						
			}
			
				$res = $mysqli->query('select * from guest_msg where booking_number = "'.$booking_number .'" and  message = "'.$msg.'"');
			
			
			if($res->num_rows < 1){
				
				$sql		= 'INSERT INTO `guest_msg`( `guest_name`, `booking_number`,`property_id`, `check_in_date`, `message`,reply_to,email_title,`created_on`,recive_date) VALUES ("'.$detail['name'].'","'.$booking_number.'","'.$p_id.'", "'.$check_in_date.'","'.$msg.'","'.$detail['reply_to'].'","'.$msubject.'","'.date("Y-m-d H:m").'","'.$recive_date.'")';
				
				
				if($mysqli->query($sql))
				{
					$flag = 1;
				}
					
			}
			
		}
			
	}
}else{
	echo "No Record Found";
}
if(!empty($ress)){

	foreach($ress as $result)
	{
		
		if(!empty($result['sub']))
		{
								
			$sql_query		= $mysqli->query('select gm.email_title, gm.booking_number,p.name from guest_msg gm,properties p where email_title like "%'.$result['sub'].'%" and p.id = property_id group by gm.booking_number');
			
			
			while($pr= $sql_query->fetch_assoc())
			{
				echo $result['title'] ." = ". $pr['name']." = ".$pr['booking_number'];
				echo "<br>";
				
				$mysqli->query('update guest_msg set demande = "'.$result['title'].'" where email_title like "%'.$result['sub'].'%"');
				
			}
		}
	}
			
	
}
if($flag == 1 ){
	echo "Data inserted sucessfully";
}else{
	echo "Data is not inserted";	
}

function strafter($string, $substring) {
  $pos = strpos($string, $substring);
  if ($pos === false)
   return $string;
  else  
   return(substr($string, $pos+strlen($substring)));
}
function strbefore($string, $substring) {
  $pos = strpos($string, $substring);
  if ($pos === false)
   return $string;
  else  
   return(substr($string, 0, $pos));
} 
function get_date_in_eng($dateTime){

$find = array('aoc3bbt','janvier', 'fc3a9vrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre','dC3A9cembre','dim','lun','mar','mer','jeu','ven','sam','jan','fc3a9v','mar','avr','mai','jun','juil','aoc3bb','sep','oct','nov','dc3a9c');

$replace = array('August','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

return $date = str_replace($find, $replace, strtolower($dateTime));
//return date('Y/m/d ', strtotime($date));
}
function stringformat($str)
{
	$strfind = array('C3A9','C3A0','E282AC','C3A8' ,'C3A7','C3BB','C3AA','C3B4','C3AD','C3B1','C3 A9','EFBFBD','C380','php?b','watch?v','C3B9','C2B4','?UTF-8?Q?','_','? ','C387', 'EFBC9F','.php?b=3D','C3B3','E38080');
	$strreplace=array('é','à','€','è','ç','u','ê','ô','í','ñ','é','?','À','php?b=','watch?v=','ù','´','',' ','','Ç',' ?','php?b=','ó',' ');
	return $actualstr = str_replace($strfind, $strreplace, $str);
	
}


?>