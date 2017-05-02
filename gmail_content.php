<?php

require_once('../connect.php');
require_once('cron_mail.php');
include_once('EmailMessage.php');
header('Content-Type: text/html; charset=utf-8');
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
session_start();
if(!isset($_SESSION['admin'])) {
	header('location: index.php');
}
//echo $sinceDate = date('d F Y', strtotime('-7 day', strtotime(date('Y-m-d'))));
//echo $beforeDate = date('d F Y');
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
if(isset($_REQUEST['name'])){
	$sub	= "Réservation instantanée confirmée - ".$_REQUEST['name'];
	$emails = imap_search($inbox, "SUBJECT \"$sub\"");
}else{
	$emails = imap_search($inbox, "SINCE \"$date\"");	
}
$output = array();
$i=0;
if(!empty($emails)){
	foreach($emails as $mail) {
		
			
			$headerInfo 	= imap_headerinfo($inbox,$mail);	
			$current_date	= date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
			$convert_date	= date('Y-m-d',strtotime($headerInfo->date));
			$end_date 		= "";
		$dummy = array();			
		
			$end_date = $convert_date;
		
			$explode_data = explode('_',$headerInfo->subject);
			
			if(in_array("confirm=C3=A9e", $explode_data)){
			
				$emailMessage = new EmailMessage($inbox, $mail);
				$emailMessage->fetch();
				
				preg_match_all("/<img .*?(?=src)src=\"([^\"]+)\"/si", $emailMessage->bodyHTML, $matches);
				$output[$i]['match_sub'] 		= 'confirm=C3=A9e';
				$output[$i]['subject'] 			= $headerInfo->subject;				
				$output[$i]['date'] 			= $headerInfo->date;
				
				
				
				$content 						= imap_fetchbody($inbox,$mail,2);
				$dummy[] 						= $content;
				$content_1						= $content;
				$content 						= strip_tags($content);
				$content						= str_replace("=20","",$content);
				$content						= preg_replace('/\s+/',' ', $content);
				
					
				$name_containing_text		 	= strafter($content,'bienvenue');
				$name_containing_text			= strbefore($name_containing_text,'Membre');
				$name 							= strafter($content,'=C3=A0');
				
				$name							= strbefore($name,'.');
					
				$split 							= explode(",", $name_containing_text);
				
				if(count($split) > 1){
					$county 					= $split[count($split)-1];
					if( $county == ' ' ){							
						$county 				= $split[count($split)-2];
							}
					}else{
					$csplit						= explode(" ", $split[0]);
						 $county                = $csplit[count($csplit)-2];
						}
				
				$name 							= strafter($content,'=C3=A0');
				
				$name							= strbefore($name,'.');
				
				$number_of_guests 				= strafter($content,'Voyageurs');
				$number_of_guests				= strbefore($number_of_guests,'Code de confirmation');
				
				$booking_number 				= strafter($content,'Code de confirmation');
				$booking_number					= strbefore($booking_number,'Voir');
				$amount			 				= strafter($content,'Vous gagnez');
				$amount							= strbefore($amount,'=E2=82=AC');
				
				$output[$i]['name_containing_text']	= $name_containing_text;
				$output[$i]['name']				= trim($name,' ');
				$output[$i]['county']			= trim($county,' ');
				$output[$i]['number_of_guests'] = trim($number_of_guests,' ');
				$output[$i]['booking_number'] 	= trim($booking_number,' ');
				$output[$i]['amount'] 			= trim($amount,' ');
				$output[$i]['pic_url'] 			= $matches[1][1];
					
			}
			
		
		$i++;
	}
}
imap_expunge($inbox);
imap_close($inbox);
}catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}

$flag	= 0;
$j		= 0;
$k		= 0;
$change	= 0;
$update_data = array();
if(!empty($output)){
	
	foreach($output as $detail){
	
		if($detail['county']!=''){
			
			 $sqlquery = $mysqli->query("SELECT booking_number,guests_number,earning FROM gcal_imports where booking_number = '".$detail['booking_number']."'"); 
		
							
		
			if(isset($_REQUEST['parseBooking'])&& $_REQUEST['parseBooking']==$detail['booking_number'])
				{
					
					
					$contents	=	file_get_contents($detail['pic_url']);
					$save_path	=	"uploads/image".$detail['booking_number'].".jpg";
					
						file_put_contents($save_path,$contents);
					
					$sql = 'UPDATE gcal_imports SET country ="'. $detail['county'].'" , guests_number ='.(int)$detail['number_of_guests'].',profile_pic ="'.$save_path.'" WHERE booking_number = "'.$_REQUEST['parseBooking'].'"';
					//echo $sql = "UPDATE gcal_imports SET country = '".$detail['county']."' , guests_number = '".(int)$detail['number_of_guests']."',profile_pic ='".$save_path."' WHERE booking_number = '".$_REQUEST['parseBooking']."'";
							
				if ($mysqli->query($sql)) {
					echo $update_data[$j]="Record updated successfully guests_number = ".(int)$detail['number_of_guests']." in booking_number = ".$_REQUEST['parseBooking']."</br>";
					$j++;
					$flag=1;
					
				}
							
			}
				
			else{
					
			if ($sqlquery->num_rows >= "1") { 
					$gtdetail = $sqlquery->fetch_assoc();
				if($gtdetail['earning']==0 || empty($gtdetail['earning']))
				{
					$query = "UPDATE gcal_imports SET earning = '".$detail['amount']."' WHERE booking_number = '".$detail['booking_number']."'";
					
					if($mysqli->query($query )){
						echo $updateEr[$k] = "Record updated successfully earning payment = ".$detail['amount']." in booking_number = ".$detail['booking_number']."</br>";
						$change=1;
						$k++;
					}
					
				}
				if($gtdetail['guests_number']==0 || empty($gtdetail['guests_number']))
					{				
						$contents	=	file_get_contents($detail['pic_url']);
						$save_path	=	"uploads/image".$detail['booking_number'].".jpg";
					
						file_put_contents($save_path,$contents);
				
						$sql = "UPDATE gcal_imports SET country = '".$detail['county']."' , guests_number = '".(int)$detail['number_of_guests']."',profile_pic ='".$save_path."' WHERE booking_number = '".$detail['booking_number']."'";		
			
					if ($mysqli->query($sql)) {			
						echo $update_data[$j]="Record updated successfully guests number = ".(int)$detail['number_of_guests']." in booking_number = ".$detail['booking_number']."</br>";
						$j++;
						$flag=1;
					} else {
						echo "There is some problem while updateing booking_number = ".$detail['booking_number']."</br>";
						continue;
					}
				}
					
			}else{
					echo $detail['booking_number']." booking no. is not in datadate. so can not update <br>"  ;
				}	
			}
		  }
		
	}
	
	$currentdate	= date("Y-m-d H:i:s");
	$file			= "cron run log  gmail_content ".$currentdate;
	if($flag == 1){
	 echo "Parsed from".$current_date. "date to ".$end_date. " date";
	 	corn_mail($file,$update_data); 
	}else{
		echo "Not update any data";
	}
}else{
	echo "No Record Found";
}
if($change==1)
{
	corn_mail($file,$updateEr);	
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
?>