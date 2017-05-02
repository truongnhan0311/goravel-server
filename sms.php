<?php 
require_once('../connect.php');
require_once('../functions_general.php');
include( 'function.php');
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
 session_start();
 if( !isset($_SESSION['admin']) ) {
	header('Location: index.php');
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
.data-tbl { border-collapse: collapse; font-family: "sans-serif",Arial,Helvetica,sans-serif;  width: 90%; margin-bottom:30px;}
.data-tbl td {
    border: 1px solid #ddd;
    color: #333;
    font-size: 14px;
    padding: 10px;
    text-align: justify;vertical-align: top;
}
.data-tbl th  {
    border: 1px solid #ddd;
    color: #333;
    font-size: 14px;
    padding: 10px;
    text-align: justify; background:#fafafa;}
</style>


      
</head>


<div class="container-fluid table-responsive">
  <div class="row" style="margin:0 !important"> 
    <!--menu bar-->
    <?php require_once ('admin_menu.php')?>
  </div>
  

<div class="row">
<?php 
if(isset($_SESSION['send_message'])){
	
}

?>
  <div class="col-md-6">
  <?php
        if(isset($_SESSION['send_message'])){
    
            echo '<div class="alert alert-success" role="alert">',$_SESSION['send_message'],'</div>';
			unset($_SESSION['send_message']);
        } 
		if(isset($_SESSION['error_message'])){
    
            echo '<div class="alert alert-danger" role="alert">',$_SESSION['error_message'],'</div>';
			unset($_SESSION['error_message']);
        }?>
  <form method="post" action="nexmo/send.php" enctype="multipart/form-data">
  
  
  <div class="form-group">
    <label for="exampleInputLink">Receiver Mobile No. </label>
    <input type="text" class="form-control" name="cellNumber"  id="cellNumber" placeholder="Mobile No." required>
  </div>
  
   <div class="form-group">
    <label for="exampleInputLink">Message </label>
    <textarea  class="form-control"  name="message" id="exampleInputDescription" rows="6" placeholder="Message" ></textarea>
  </div>
   
  <button name="submit" value="submit" type="submit" class="btn btn-default">Submit</button>
</form>
</div></div>

<br>
<br>
<table class="data-tbl">
	<thead>
    <tr><th  width="80" style="text-align:center;">S. No.</th><th width="200">Sender Mobille</th><th>Name</th><th width="200">Check in </th><th>Message</th><th width="200">Date</th></tr></thead>
    <tbody>
    <?php
	$res	= $db->query("select * from sms_message order by date desc");
	$rows	= $res->fetchAll(PDO::FETCH_ASSOC); 
	$i		= 1;

	foreach($rows as $message){
		echo "<tr><td style='text-align:center;'>".$i."</td><td>".$message['mobile']."</td><td><a   href=detail.php?b=", $message['booking_number'], ">".$message['name']."</a></td><td>".$message['check_in']."</td><td>".$message['message']."</td><td>".$message['date']."</td></tr>";
		
		$i++;
	}
	
	?>
    </tbody>
</table>
</div>
</body></html>
