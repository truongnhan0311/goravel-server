<?php
require_once('../connect.php');
require_once('cron_mail.php');
header('Content-Type: text/html; charset=utf-8');

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

session_start();

if(!isset($_SESSION['admin'])) {
	header('location: index.php');
}

?>
<!DOCTYPE html>


<html lang="en">


<head>


<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">


<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>


<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<style>


.green_td {


  background-color: #acfa58 !important;


  width: 1%;


}.blue_td { background-color:#58D3F7 !important}


.check_in_tomorrow td,.check_in_tomorrow_li{background-color:#D0F5A9 !important}
.check_in_tomorrow td.green_class { background-color:green !important;}

.check_in_today td,.check_in_today_li{background-color:#00FF00 !important}
.check_in_today td.green_class { background-color:green !important;}

.check_out_today td,.check_out_today_li{background-color:#F78181 !important}
.check_out_today td.green_class { background-color:green !important;}

.booking_cancel td,.booking_cancel_li{background-color:#A9A9A9 !important}


.taxi_li{background-color:#FFFF00 !important;color:black !important}


.instructions{background-color:#FF99FF !important}


.mifi_li{background-color:#58D3F7 !important;color:black !important}


.check_in_tomorrow_li a,.check_in_today_li a,.taxi_li a{color:#777 !important}





.check_in_tomorrow_li a,.in_apartment_li a,.in_late_check_out_li a,.check_in_today_li a,.check_out_today_li a{cursor: default; !important}


.navbar-inverse{background-color:  #D8D8D8 !important;}


.navbar-inverse a { color:#000000 !important;}	








.in_apartment td,.in_apartment_li{background-color:#F5D0A9 !important ;}
.in_apartment td.green_class { background-color:green !important;}


.in_late_check_out_li{background-color:#FF8000 !important}


.form-horizontal #loading {


    display: inline-block;


    float: left;


    margin-left: 5px;


}

.green_class{background-color:green !important;}
.white_class{background-color:#fff !important;}
</style>

</head>


<body>
<div class="container-fluid table-responsive">
<div class="row">
<!--menu bar-->     
<?php require_once ('admin_menu.php');

set_time_limit(4000); 

$imapPath = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
$username = 'bnbstories@gmail.com';
$password = 'hostbetter';
//$username = 'anuj@jasapp.com';
//$password = '9785914691';
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

//$emails = imap_search($inbox,'ALL');
$date = date ( "d M Y", strToTime ( "-7 days" ) );

$emails = imap_search($inbox, "SINCE \"$date\"");



$booking = array();
$output = array();
$i=0;

if(!empty($emails)){
	foreach($emails as $mail) {
		
			$headerInfo 	= imap_headerinfo($inbox,$mail);	
			$current_date	= date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
			$convert_date	= date('Y-m-d',strtotime($headerInfo->date));

		$dummy = array();			
		if($convert_date >= $current_date){
			$end_date = $convert_date;
		//confirmée
			//confirm=E9e
			//confirm=C3=A9e
			 $explode_data = explode('_',$headerInfo->subject);
			/*echo "<pre>";
			 print_r($explode_data);*/
			
			if($explode_data[0] == "=?UTF-8?Q?Versement" ){
				
					 	$msgBody 						= imap_fetchbody ($inbox, $mail, 2);
						$msgBodycontaining_text		 	= strafter($msgBody,'Montant');
						$msgBodycontaining_text			= strbefore($msgBodycontaining_text,'=E2=82=AC');
						$output[$i]['earning'] 			= trim(strbefore($explode_data[2],'=E2=82=AC'));
						//echo $output[$i]['earning'] 	= trim(strip_tags(strafter($msgBodycontaining_text,'Montmartre')));
						$msgBodycontaining_text			= trim(quoted_printable_decode ($msgBodycontaining_text));
						$bookingNo						= explode("-",trim(strip_tags($msgBodycontaining_text)));
						$bookingNo 						= explode(" ",trim(strip_tags($bookingNo[1])));
						$output[$i]['earnBooking_number']= trim($bookingNo[38]);
						
						 
					}
		}
		
		$i++;
	}
}

//echo '<pre>'; print_r($output);


imap_expunge($inbox);
imap_close($inbox);

}catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
 /*echo '<pre>'; print_r($output);die; */
//die;
$flag	= 0;
$j		= 0;

if(!empty($output)){
	foreach($output as $detail){
		if($detail['earnBooking_number']!=''){
		$sqlquery = $mysqli->query("SELECT g.booking_number,g.earning,p.name, g.check_in FROM gcal_imports g LEFT JOIN properties p ON g.property_id = p.id  where g.booking_number = '".$detail['earnBooking_number']."'"); 
			
			if ($sqlquery->num_rows >= "1") { 
				$erdetail = $sqlquery->fetch_assoc();
				if($erdetail['earning']!=$detail['earning'])
				{
					$oldAmount[$j]	=	$erdetail['earning'];
					$newAmoount[$j]	=	$detail['earning'];
					$booking[$j]	=	$detail['earnBooking_number'];
	
					$earningsql 	= "UPDATE gcal_imports SET earning ='".$detail['earning']."' WHERE booking_number = '".$detail['earnBooking_number']."'";
					if ($mysqli->query($earningsql)) {
							echo $update_data[$j]="Earning amount updated successfully earning amount = ".$detail['earning']." in booking_number = ".$detail['earnBooking_number']."<br />";
							echo "Booking Number - ".$erdetail['booking_number']."  Appartement Name - ".$erdetail['name']." Check In -".$erdetail['check_in']."<br />";
							$j++;
							$flag=1;
					} else {
							echo "There is some problem in earning amount while updateing booking_number = ".$detail['earnBooking_number']."</br>";
							continue;
						}					
					
				}
			}else{
				echo $detail['earnBooking_number']." booking no. is not in datadate. so can not update <br>"  ;
			}
	}
 }
 
 if($flag==0){
	echo "Not update any data";	
	}else{
		echo "Parsed from".$current_date. "date to ".$end_date. " date";
		$currentdate	= date("Y-m-d H:i:s");
 		$file			= "cron run log  payment_parser ".$currentdate;
		corn_mail($file,$update_data); 
	}
}else{
	echo "No Record Found";
}
if($flag==1)
{
	amount_mail($oldAmount,$newAmoount,$booking);	
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
</div>
</div>
</body>
</html>