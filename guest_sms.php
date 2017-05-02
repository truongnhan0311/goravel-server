<?php
error_reporting(1);
require_once('../connect.php');
require_once('../functions_general.php');
include( '../function.php');
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
 session_start();
 if( !isset($_SESSION['admin']) ) {
	header('Location: index.php');
}

//example of sending an sms using an API key / secret
    require_once './nexmo/vendor/autoload.php';
    define('API_KEY','7c8e58d2');
    define('API_SECRET','7ba989241c35a8dc');
    
    $client			= new Nexmo\Client(new Nexmo\Client\Credentials\Basic(API_KEY, API_SECRET));
    
    //send message using simple api params
    if(isset($_REQUEST['submit'])){
        
        $to 			= $_REQUEST['cellNumber'];
        $txt_message	= $_REQUEST['message'];
        $message 		= $client->message()->send([
                                'to' => $to,
                                'from' => 33644636262,
                                'text' => $txt_message
                            ]);
        
        //array access provides response data
        
        //echo "Sent message to " . $message['to'] . ". Balance is now " . $message['remaining-balance'] . PHP_EOL;
		if($message!=''){	
       		 $send_message="Sent Message Suceesfully";
		 }else{
       		$error_message="Message Not Send";
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
    <link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script type='text/javascript' src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script>

	<style>
	.green_td { background-color: #acfa58 !important;  width: 1%;}
	.blue_td { background-color:#58D3F7 !important}
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
	.form-horizontal #loading { display: inline-block;  float: left; margin-left: 5px;}
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
	.text_aa {top: 0px;	z-index: 0;	left: 0px;}
	.input-large{min-width:600px !important; min-height:60px !important;border-bottom: 1px solid #ccc !important;}
	.editable-popup{display:none !important;}
</style>
      
</head>


<div class="container-fluid table-responsive">
  	<div class="row" style="margin:0 !important"> 
    <!--menu bar-->
    <?php require_once ('admin_menu.php')?>
  </div>
<?php 
 
		$res	= $db->query("select name,phone_number from gcal_imports where booking_number = '".$_REQUEST['booking_number']."'");
		$detail = $res->fetch(PDO::FETCH_ASSOC);
		$contact= isset($detail['phone_number'])?preg_replace("/[^0-9,.]/", "", $detail['phone_number']):''; 
		$name	= isset($detail['name'])?strstr($detail['name'],' ',true):''; 
	?>

	<div class="row">
    <div class="col-md-6">
    <?PHP
     if(isset($send_message)){
    
            echo '<div class="alert alert-success" role="alert">',$send_message,'</div>';
			
        } 
		if(isset($error_message)){
    
            echo '<div class="alert alert-danger" role="alert">',$error_message,'</div>';
			
        }?>
		<form method="post" action="" enctype="multipart/form-data">
  
  
  		<div class="form-group">
            <label for="exampleInputLink">Receiver Mobile No. </label>
            <input type="text" class="form-control" name="cellNumber" value="<?php echo $contact;?>"  id="cellNumber" placeholder="Mobile No." required>
          </div>
  
 		<div class="form-group">
            <label for="exampleInputLink">Message </label>
            <textarea  class="form-control"  name="message" id="exampleInputDescription" rows="6" placeholder="Message"  ><?php echo "Hi, $name";?></textarea>
  		</div>
   
  		<button name="submit" value="submit" type="submit" class="btn btn-default">Submit</button>
	</form>
	</div>
    </div>
</div>