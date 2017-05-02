<?php 
require_once('../connect.php');
require_once('../functions_general.php');
include( 'function.php');
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
$db->exec("SET NAMES 'utf8';");
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
    margin-left: 5px;}

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


<div class="container-fluid table-responsive">
  <div class="row" style="margin:0 !important"> 
    <!--menu bar-->
    <?php require_once ('admin_menu.php');	?>
  </div>
  

<div  class="row">
<form class="form-horizontal" action="" method="post">
    &nbsp;&nbsp;
    <select name="p_id"  >
              <option value="">Please Select Property</option>
              <?php
			 $stmt1 	= $db->query("select id,name from properties where 1 and active_status = 'YES' order by name asc");
			
			 while($rows2 = $stmt1->fetch(PDO::FETCH_ASSOC))
			{ ?>
              <option value="<?php echo $rows2['id']?>" <?php if($rows2['id']==$_POST['p_id']){ echo "selected='selected'";} ?>><?php echo $rows2['name']?></option>
              <?php }
			
			?>
            </select>
             <select name="month">
                <option value='0'>Select Month</option>
                <option value='1' <?php if(1==$_POST['month']){ echo "selected='selected'";} ?>>Janaury</option>
                <option value='2' <?php if(2==$_POST['month']){ echo "selected='selected'";} ?>>February</option>
                <option value='3' <?php if(3==$_POST['month']){ echo "selected='selected'";} ?>>March</option>
                <option value='4' <?php if(4==$_POST['month']){ echo "selected='selected'";} ?>>April</option>
                <option value='5' <?php if(5==$_POST['month']){ echo "selected='selected'";} ?>>May</option>
                <option value='6' <?php if(6==$_POST['month']){ echo "selected='selected'";} ?>>June</option>
                <option value='7' <?php if(7==$_POST['month']){ echo "selected='selected'";} ?>>July</option>
                <option value='8' <?php if(8==$_POST['month']){ echo "selected='selected'";} ?>>August</option>
                <option value='9' <?php if(9==$_POST['month']){ echo "selected='selected'";} ?>>September</option>
                <option value='10' <?php if(10==$_POST['month']){ echo "selected='selected'";} ?>>October</option>
                <option value='11' <?php if(11==$_POST['month']){ echo "selected='selected'";} ?>>November</option>
                <option value='12' <?php if(12==$_POST['month']){ echo "selected='selected'";} ?>>December</option>
    </select> 
    <input type="submit" value="submit" name="submit" />
  </form>
  <div class="col-md-6">
  
  <?php

  $where ='';
  if(isset($_POST['submit'])){
	  if(!empty($_POST['p_id'])){
		  $where .= " and property_id = ".$_POST['p_id'];
	  }
	   if(!empty($_POST['month'])){
		  $where .=" and DATE_FORMAT(recive_date,'%c') = '".$_POST['month']."'";
	  }
  }
  
      	
				$res2 = $db->query("select * from guest_msg");
				$rows1	= $res2->fetchAll(PDO::FETCH_ASSOC);
			
				foreach($rows1 as $detail){					
					if(!empty($detail['booking_number'])&& !empty($detail['email_title'])&& !empty($detail['property_id']) ){
						
						$db->query("update guest_msg set property_id = '".$detail['property_id']."',booking_number = '".$detail['booking_number']."' where email_title = '".$detail['email_title']."'");
					}
				}
		
		
		
		
		?>
  
</div></div>

<br>
<br>
<table class="data-tbl">
	<thead>
    <tr><th width="70" style="text-align:center;">S. No.</th><th width="120">Guest name</th><th>Booking number</th><th>Property Name</th><th width="150">Check in </th><th>Message</th><th width="160">Recive Date</th></tr></thead>
    <tbody>
    <?php
	
	
	$res	= $db->query("select * from guest_msg where 1 ".$where." order by recive_date desc");
	$rows	= $res->fetchAll(PDO::FETCH_ASSOC);
	 
	$i		= 1;
	if(!empty($rows )){
	foreach($rows as $message){ 
	
			
	$query	= $db->query("select * from gcal_imports where booking_number = '".$message['booking_number']."'");
	
	$rec	= $query->fetch(PDO::FETCH_ASSOC);
	
		if(strpos($rec['name'],$message['guest_name'])===false)
		{	
			$style = "style='background-color:#D8D8D8'";	
		}else{
				$style = '';
			}
	
			$res1	= $db->query("SELECT name FROM properties where id = ".$message['property_id']);
			$pname	= $res1->fetch(PDO::FETCH_ASSOC); 
			$pName	= isset($pname['name'])?$pname['name']:'';
				echo "<tr><td style='text-align:center;'>".$i."</td>";
				echo "<td><a href=detail.php?b=", $message['booking_number'], ">".$message['guest_name']."</a></td>";
		
		if(!empty($message['booking_number'])){
		  echo "<td>";
		 echo  "<a href=guest_mail_detail.php?bn=", $message['booking_number'], ">".$message['booking_number']."</a></td>";
		}else{
		  echo "<td>Demande</td>";	
		}
		echo "<td>".$pName."</td>";	
		echo "<td>".date('l d M',strtotime($message['check_in_date']))."</td>";
		echo "<td ",$style,">".$message['message']."</td>";
		echo "<td>".date('l,Y-m-d',strtotime($message['recive_date']))."</td></tr>";
		
		$i++;
	} }else{
	echo "<tr><td colspan='8'>NO Record</td></tr>";
	}?>

	
    </tbody>
</table>

</div>
</body></html>
