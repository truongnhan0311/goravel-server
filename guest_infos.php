<?php
require_once('../connect.php');
header('Content-Type: text/html; charset=utf-8');

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

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

$emails = imap_search($inbox,'ALL');
 


 
$output = array();
$i=0;

if(!empty($emails)){
	foreach($emails as $mail) {
		
			$headerInfo 	= imap_headerinfo($inbox,$mail);	
			$current_date	= date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
			$convert_date	= date('Y-m-d',strtotime($headerInfo->date));
			

		$dummy = array();			
		if($convert_date <= $current_date){ 
		//confirmée
			//confirm=E9e
			//confirm=C3=A9e
			$explode_data = explode('_',$headerInfo->subject);
			if(in_array("confirm=C3=A9e", $explode_data)){
				
				$output[$i]['match_sub'] 		= 'confirm=C3=A9e';
				$output[$i]['subject'] 			= $headerInfo->subject;				
				$output[$i]['date'] 			= $headerInfo->date;
				
				/* $output[$i]['toaddress'] 	= $headerInfo->toaddress;
				$output[$i]['fromaddress'] 		= $headerInfo->fromaddress;
				$output[$i]['reply_toaddress'] 	= $headerInfo->reply_toaddress; */
				
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
				
				$output[$i]['name_containing_text']	= $name_containing_text;
				$output[$i]['name']				= trim($name,' ');
				$output[$i]['county']			= trim($county,' ');
				$output[$i]['number_of_guests'] = trim($number_of_guests,' ');
				$output[$i]['booking_number'] 	= trim($booking_number,' ');
				
				
				/*
					$output[$i]['body'] 			= $content_1;
					$output[$i]['body_1'] 			= $content_1;
				*/
				
							
			}
		}
		$i++;
	}
}

//echo '<pre>'; print_r($output);die;

imap_expunge($inbox);
imap_close($inbox);

}catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
 /*echo '<pre>'; print_r($output);die; */

if(!empty($output)){
	foreach($output as $detail){
		if($detail['county']!=''){
			$sql = "UPDATE gcal_imports SET country = '".$detail['county']."' , guests_number = '".(int)$detail['number_of_guests']."' WHERE booking_number = '".$detail['booking_number']."' ";		
		
			if ($mysqli->query($sql)) {			
				echo "Record updated successfully booking_number = ".$detail['booking_number']."</br>";
			} else {
				echo "There is some problem while updateing booking_number = ".$detail['booking_number']."</br>";
				continue;
			}
			/* $Qury 	= $mysqli->que2ry("select * from gcal_imports  where booking_number ='".$detail['booking_number']."' ");
			$rowQ = $Qury->fetch_array(MYSQLI_ASSOC); */
			}
	}
}else{
	echo "No Record Found";
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