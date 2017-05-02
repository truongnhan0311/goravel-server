<?php 
require_once('../connect.php');
require_once('cron_mail.php');
set_time_limit(0);
$dbuser = $db_user_connect;
$dbpass = $db_pass_connect;
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect.'', $dbuser, $dbpass);
$db->exec("SET NAMES 'utf8';");
?><!DOCTYPE html>
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
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->
<?php
$i=0;
$update_data = array();
$stmt2	 	= $db->query("select booking_number from gcal_imports");
$bookings	= $stmt2->fetchAll(PDO::FETCH_ASSOC);
						foreach($bookings as $booking)
						{
							 $arrBooking[]= $booking['booking_number'];
						}
$stmt1 	= $db->query("select id,ics_link from properties where active_status = 'YES'");
$rows 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);
$ical_files = array();
if(!empty($rows)){
	foreach($rows as $id=>$ics_link)
	{
		$ical_files[$id] = $ics_link;
	}
}
function curl_get_contents($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
require '../icsparser.php';
$ical_class = new ical();
foreach ($ical_files as $ikey => $ivalue_arr) {
		 $property_id	=  $ivalue_arr['id'];
	 	 $ivalue 		= $ivalue_arr['ics_link'];
	
	/* $ical = file_get_contents($ivalue); */
	$ical = curl_get_contents($ivalue);
	file_put_contents('../ics/' . md5($ivalue).'.ics', $ical);
	/*$ical_class->fileame(md5($ivalue).'.ics');*/
	$ical_class   = new ICal('../ics/' . md5($ivalue).'.ics');
    $events 	  = $ical_class->events();
	if( is_array($events) && !empty($events) ){
		/*
		* Start batch insert job, using batch insert to have a better performance than seperate insert command!!!
		*/
		$db->beginTransaction();
		$stmt = $db->prepare("INSERT INTO gcal_imports (nights, name, booking_number, check_in, check_out, email, phone_number, property_id) 
								VALUES (:nights, :name, :booking_number, :check_in, :check_out, :email, :phone_number, :property_id)" );
		foreach($events as $k=>$value){
			if(array_key_exists('DESCRIPTION',$value))
			{
				if(trim($value['SUMMARY'])!='Not available'){
				$value['DESCRIPTION'] = $value['DESCRIPTION'].@$value['E'];
				$desc 		= explode('\n', $value['DESCRIPTION']);
				//echo '<pre>'; print_r($desc); die;
				$checkin 	= explode(':', $desc[0]);
				$checkout 	= explode(':', $desc[1]);
				if(array_key_exists(2,$desc))
				{
					$nights 	= explode(':', $desc[2]);	
					$nights_value = trim($nights[1]);
				}else
				{
					$nights_value = 0;
				}
				if(array_key_exists(4,$desc))
				{
					$email 		= explode(':', $desc[4]);	
					$email 		= trim($email[1]);
				}else
				{
					$email = '';
				}
				$phone 		= explode(':', $desc[3]);
				if(array_key_exists(1,$phone))
				{
					$phone 		= trim($phone[1]);
				}else
				{
					$phone 		= '';
				}
				/*if($ivalue=='https://www.airbnb.fr/calendar/ical/1207649.ics?s=46c588809c67982dc912e5e79029d018')
				{
					$property_id=1;
					
				}else if($ivalue=='https://www.airbnb.fr/calendar/ical/1672334.ics?s=a4556b23ef8079936750f7dbdad8486f')
				{
					$property_id=2;
				}				
				else if($ivalue=='https://www.airbnb.fr/calendar/ical/1323640.ics?s=6fd4b94497a17455a482b64f0c1d6b5b')
				{
					$property_id=3;
				}*/
				list($name, $reservation_number) = explode('(', $value['SUMMARY']);
				$reservation_number=str_replace(')', '', $reservation_number);
				/* format checkin date */
				$date	=	date_create_from_format('d/m/Y', trim($checkin[1]));
				$dci	= 	date_format($date, 'l d F Y');
				$check_in=	date_format($date,"d/m/Y");
				/* format checkout date */
				$date	 = date_create_from_format('d/m/Y', trim($checkout[1]));
				$dco	 =  date_format($date, 'l d F Y');
				$check_out=	date_format($date,"d/m/Y");
				
				$pos = strpos(strtolower($name), 'pending');
				
				if ($pos === false) {
					if($reservation_number || trim($reservation_number)!=''){				
					
					/* for update old database 
					$db->query("update gcal_imports set nights='".$nights_value."' where booking_number='".$reservation_number."' "); */
					
						$stmt->bindParam(":nights", $nights_value);
						$stmt->bindParam(":name", $name);
						$stmt->bindParam(":booking_number", $reservation_number);
						$stmt->bindParam(":check_in", $dci);
						$stmt->bindParam(":check_out", $dco);
						$stmt->bindParam(":email", $email);
						$stmt->bindParam(":phone_number", $phone);			
						$stmt->bindParam(":property_id", $property_id);	
						$stmt->execute();
					}
				}
			}
			if (!in_array($reservation_number, $arrBooking)) {
				$stmt1 	= $db->query("select name from properties where id = $property_id");
				$rows 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);
					$Details = "";
					echo $Details = $rows[0]['name'] .'-'.$check_in ."-".$check_out. "-" .$name."-".$reservation_number."<br>" ;
					
					$update_data[$i] = $Details;
					$i++;		
				}
			}
		}
		$db->commit();
	}	
	/*$stmt1 	= $db->query("select name from properties where id = $property_id");
	$rows 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);
		echo $rows[0]['name'] .':'.$check_in ."-".$check_out. ":" .$name."<br>" ;*/
	/* unlink(md5($ivalue).'.ics'); */
}
if(!empty($update_data)){
	$currentdate	= date("Y-m-d H:i:s");
	$file			= "cron run log  gcal ".$currentdate;
	corn_mail($file,$update_data);
}
?>
</div>
</div>
</body>
</html>