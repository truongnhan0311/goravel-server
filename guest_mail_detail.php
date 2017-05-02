
<?php 
	require_once('../connect.php');
	require_once('../functions_general.php');
	include( 'function.php');	
	require('../phpmailer/PHPMailerAutoload.php');
	require('./nexmo/vendor/autoload.php');	
	
	
	$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
	$db->exec("SET NAMES 'utf8';");
	 session_start();
	
	 if( !isset($_SESSION['admin']) )
	 {
		 header('Location: index.php');
	}
	
	extract($_REQUEST);
	if(isset($_REQUEST['email']))
	{
		$mailbody  ='';
			$mailbody .= $message;				
			$response = false;
			$mail = new PHPMailer();
			$mail->CharSet   	= 'UTF-8';			
			$mail->IsSMTP();			
			$subject_evernote	= $mail_title;
			 	
			
			$mail->SMTPAuth 	= true;
			$mail->Host       	= "smtp.gmail.com";
			$mail->SMTPSecure	= "ssl";
			$mail->Port       	= 465;
			$mail->Username 	= "bnbstories@gmail.com";
			$mail->Password 	= "hostbetter";
			$mail->isHTML 		= true; 
			$mail->From 		= "bnbstories@gmail.com";
			$mail->FromName 	= "Airbnb";
			mb_internal_encoding("UTF-8");
			$mail->Subject		= $subject_evernote;
			$mail->Body   		= $mailbody;
			$mail->AddAddress(trim($mail_id),$g_name);
			$mail->Send();		
		
			if($mail->IsError()) { 	
				echo $response1 = "<br />Mailer Error: " . $mail->ErrorInfo;
			} else {
				$response = true;
			}
			
		
	}
	if(isset($_REQUEST['sms']))
	{
		
		
		
		define('API_KEY','7c8e58d2');
		define('API_SECRET','7ba989241c35a8dc');

		$client			= new Nexmo\Client(new Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET));
	
	//print_r($client);
	
		$send	= false;
			
		
		$txt_message	= $message;
		$to 			= $phone;
		$url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
			[
			  'api_key' =>  'API_KEY',
			  'api_secret' => 'API_SECRET',
			  'to' => $to,
			 'from' => 33644636262,
			 'text' => $txt_message
			]
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$msg = curl_exec($ch);

		  
		
		/*$messages 		= $client->message()->send([
							'to' => $to,
							'from' => 33644636262,
							'text' => $txt_message
						]);
						echo "Sent message to " . $message['to'] . ". Balance is now " . $message['remaining-balance'] . PHP_EOL;
					*/
		if(!empty($msg))
		{
			$send	= true;
		}
	
	}

?>

<!DOCTYPE html>

<html lang="en">
	<head>
    
<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

<!--<link href="/bootstrap.min.css" rel="stylesheet">-->
<link href="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<script type='text/javascript' src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script>
<link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
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
        
.text_a {
	background: none repeat scroll 0% 0% rgb(255, 255, 255);
	border: medium none;
	overflow: hidden;
	color: rgb(255, 255, 255);
	z-index: 99999;
	position: absolute;
	opacity: 0;
	width: 50px;
	height: 20px;
	cursor: pointer;
}
.text_aa {
	top: 0px;
	z-index: 0;
	left: 0px;
}

.input-large{min-width:600px !important; min-height:60px !important;border-bottom: 1px solid #ccc !important;}

.editable-popup{display:none !important;}
.data-tbl { border-collapse: collapse; font-family: "sans-serif",Arial,Helvetica,sans-serif;  width:100%; margin-bottom:30px;}
.data-tbl td {
    border: 1px solid #ddd;
    color: #333;
    font-size: 14px;
    padding: 10px;
    text-align: justify;vertical-align:	top;
}
.data-tbl th  {
    border: 1px solid #ddd;
    color: #333;
    font-size: 14px;
    padding: 10px;
    text-align: justify; background:#fafafa;}


</style>


      
</head>

	<body>
		<div class="container-fluid table-responsive">
  			
            <div class="row" style="margin:0 !important"> 
	            <!--menu bar-->
    	        <?php require_once ('admin_menu.php')?>
           </div>
  

			<div class="row">
            
              <div class="col-md-10">
				  <?php
				  	
					
					
				  if(isset($response))
                        if($response){
                    
                            echo '<div class="alert alert-success" role="alert">Mail Send Successfully !!</div>';                            
                        }else{
							
							echo '<div class="alert alert-danger" role="alert">Mail not Send </div>';	
						}
						
					if(isset($send))	
                        if($send){
                    
                            echo '<div class="alert alert-success" role="alert">Message Send Successfully !!</div>';                            
                        }else{
							
							echo '<div class="alert alert-danger" role="alert">Message not Send </div>';	
						}
						
						
					
					$sql	= $db->query("select *,g.name as g_name from gcal_imports g,properties p where booking_number ='".$bn."' and p.id = g.property_id");
					$recs	= $sql->fetch(PDO::FETCH_ASSOC);
					if(empty($recs)){					
						$sql	= $db->query("select guest_name as g_name ,check_in_date as check_in_new  FROM guest_msg WHERE booking_number = '".$bn."'");
						$recs	= $sql->fetch(PDO::FETCH_ASSOC);
						
					}
					
					
                ?>
         <div>Guest name :<b><?php echo $recs['g_name'];?></b>  - Booking number :<b><?php echo $bn;  ?> </b>- Appartement : <b><?php echo $recs['name'];  ?></b> - Check in date + time : <b><?php echo date("d M Y",strtotime($recs['check_in_new']))." ".$recs['check_in_time'];  ?> </b>- Check out date + time : <b><?php echo date("d M Y", strtotime($recs['check_out_new']))." ".$recs['check_out_time'];  ?></b> -  number of guest : <b> <?php echo $recs['guests_number'];  ?> </b>- communication way :<b><?php echo $recs['communication_way'];  ?></b>
</div>       
              	<br>
<br>

                 <table class="data-tbl">
                    <thead>
                        <th>Guest Name</th>
                        <th>Property</th>
                        <th>Check In</th>
                        <th>Message</th>
                         <th>Recive Date</th>
                      
                      </thead>
                      <tbody>
                      <?php
					  
					  
					  
	
					$query	= $db->query("select gm.*,g.*,p.*,g.name as g_name ,gm.message as gm_message ,gm.email_title as gm_email_title,gm.reply_to as gm_reply_to  from gcal_imports as g LEFT JOIN properties as p ON p.id = g.property_id LEFT JOIN guest_msg as gm ON g.booking_number = gm.booking_number where g.booking_number = '".$bn."' ORDER BY `gm`.`msg_id` DESC");
					 $gm_rec		= $query->fetchAll(PDO::FETCH_ASSOC);
					 
					if(empty($gm_rec)){
					
						$query	= $db->query("select *,message as gm_message  FROM guest_msg WHERE booking_number = '".$bn."' ORDER BY `msg_id` DESC");
						$gm_rec		= $query->fetchAll(PDO::FETCH_ASSOC);
					}
													
							
					
					
					foreach( $gm_rec as $rec_data	){
							if(strpos($rec_data['g_name'],$rec_data['guest_name'])===false)
							{	
								$style = "style='background-color:#D8D8D8'";	
							}else{
								$style = '';
							}
						   
							if($rec_data['phone_number']){
								$phone = preg_replace("/[^0-9,.]/", "", $rec_data['phone_number']);
							}
							 ?>
                         
                        	<tr><td><?php echo $rec_data['guest_name'];?></td>
                            <td><?php echo $rec_data['name'];?></td>
                            <td><?php echo date('l d M',strtotime($rec_data['check_in_date']));?></td>
                           <td <?php echo $style; ?><> <?php echo $rec_data['gm_message'];?></td>
                           <td > <?php echo date('l,Y-m-d',strtotime($rec_data['recive_date']));?></td></tr>
						
							
                        <?php 
					}
						?>
                    </tbody>
                    </table>
                 
				 <?php 
				 
$sql_res	= $db->query("select phone_number,g.check_in_date,g.email_title,g.recive_date,g.reply_to,g.guest_name,p.name,g.message from guest_msg g,properties p,gcal_imports gi where  g.booking_number = '".$bn."' and p.id =g.property_id and gi.booking_number ='".$bn."'  ORDER BY g.recive_date desc");		
                    $rec		= $sql_res->fetch(PDO::FETCH_ASSOC);
					if(empty($rec)){					
						$sql_res	= $db->query("select *  FROM guest_msg WHERE booking_number = '".$bn."'");
						$rec		= $sql_res->fetch(PDO::FETCH_ASSOC);
					}						   
							
				if($rec['phone_number'])
				{
					$phone = preg_replace("/[^0-9,.]/", "", $rec['phone_number']);
				}
				 $gname = $rec['g_name'];
				 $pname = $rec['name'];
				 ?>
                 
                   <form method="post" id="msgform" action="#">
                   <div class="form-group">
    					<label for="exampleInputLink">Phone No. </label>
                    		<input class="form-control" type="text" name="phone" id="phone"  value="<?php echo $phone;?>" readonly>
                            </div>
                            <div class="form-group">
    					<label for="exampleInputLink">Mail Title </label>
                    <!--<input type="text" class="form-control" name="mail_title" id="mail_title"  value="<?php echo $rec['g_name']; ?>" readonly>-->
					 <input type="text" class="form-control" name="mail_title" id="mail_title"  value="<?php echo $rec['email_title']; ?>" readonly> 
                    </div>
                            <div class="form-group">
    					<label for="exampleInputLink">Mail </label>
                    <input type="text" class="form-control" name="mail_id" id="mail"  value="<?php echo $rec['reply_to'];?>" readonly>
                    </div>
                    <div class="form-group">
    					<label for="exampleInputLink">Guest Name </label>
                	<input class="form-control" type="text" name="g_name" id="g_name" value="<?php echo $recs['g_name']; ?>" readonly/>
                    </div>
                    <div class="form-group">
    					<label for="exampleInputLink">Property </label>
                            <input class="form-control" type="text" name="property" id="property" value="<?php echo $pname;?>" readonly/>
                            </div>
                            <div class="form-group">
    					<label for="exampleInputLink">Message </label>
                           <textarea  class="form-control"  name="message" id="exampleInputDescription" cols="5" rows="4" ></textarea>
                    </div>
              		<button name="email" value="submit" type="submit" class="btn btn-default">Email</button> &nbsp;
                    <button name="sms" value="submit" type="submit" class="btn btn-default">SMS</button>
                     
            		</form>
            
            
            </div>
		
        </div>
	</body>
</html>
