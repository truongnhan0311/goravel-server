<?php
require_once ('../connect.php');
// require_once ('../functions_general.php');
include ('function.php');
session_start();
$where = "";
$where2 = '';
$property = '';
$mysqli = new mysqli($db_host_connect, $db_user_connect, $db_pass_connect, $db_name_connect);
if (!empty($_POST['basic_submit']))
{
	$min_file_size = 55000;
	$valid_exts = array(
		'jpeg',
		'jpg',
		'png',
		'gif'
	);
	$sizes = array(
		500 => 500
	);
	if (isset($_POST['img'])) $property_img = $_POST['img'];
	else $property_img = "";
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && !empty($_FILES['image']['name']))
	{
		if ($_FILES['image']['size'] > $min_file_size)
		{
			$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
			if (in_array($ext, $valid_exts))
			{
				foreach($sizes as $w => $h)
				{
					$property_img = resize($w, $h);
				}
			}
			else
			{
				$message = 'Unsupported file';
			}
		}
		else
		{
			$message = 'Please upload image larger than 60KB';
		}
	}
	else
	{
		//we do not need to upload image if it has not been provided
		// $message = "Please upload image";
	}
	$img_where = '';
	if (!empty($property_img))
	{
		$img_where = " property_img='" . security($property_img) . "', ";
	}
	
	// echo '<PRE>Image uploaded is ' , $img_where , print_r($_FILES,true) , '</PRE>';
	
	$sql = "UPDATE properties SET ics_link= '" . security($_POST['ics_link']) . "',
	                            address='" . security($_POST['address']) . "',
								emergency_1='" . security($_POST['emergency_1']) . "',
								emergency_2='" . security($_POST['emergency_2']) . "',
								name='" . security($_POST['p_name']) . "',
								description='" . security(trim($_POST['description'])) . "',
								descriptionFR='" . security($_POST['descriptionFR']) . "',
								wifi='" . security($_POST['wifi']) . "',
								metro='" . security($_POST['metro']) . "',
								washing='" . security($_POST['washing']) . "',
								activities='" . security($_POST['activities']) . "',
								directions='" . security($_POST['directions']) . "',
								restaurants='" . security($_POST['restaurants']) . "',
								grocery='" . security(trim($_POST['grocery'])) . "',
								bakery='" . security($_POST['bakery']) . "',
								check_out='" . security($_POST['check_out']) . "',
								wifi_status='" . security($_POST['wifi_status']) . "',
								driver_status='" . security($_POST['driver_status']) . "',
								cost_cleaning='" . security($_POST['cost_cleaning']) . "',
								cost_checkin='" . security($_POST['cost_checkin']) . "',
								
								
								p_link='" . security($_POST['p_link']) . "',
								p_title='" . security($_POST['p_title']) . "',
								home_page_status='" . security($_POST['home_page_status']) . "',
								p_description='" . security($_POST['p_description']) . "',
								" . $img_where . "
								
								
								p_description='" . security($_POST['p_description']) . "',
								Lavomatic='" . security($_POST['Lavomatic']) . "',
								
								
								management_fee='" . security($_POST['management_fee']) . "',
								keybox='" . security($_POST['keybox']) . "',
							cost_late_checkout='" . security($_POST['cost_late_checkout']) . "' WHERE id = '" . $_POST['id'] . "' ";
	$u = $mysqli->query($sql);
	
	if ($u)
	{
		$message = "<span  class='btn bg-success ' >" . "<br />" . $message . "Property Has Been Updated Successfully</span >";
	}
	else
	{
		$message = "<span  class='btn bg-danger '>" . "<br />" . $message . "There is some problem Try Again!</span >";
	}
	// echo $sql;exit;
}
if (isset($_GET['id']))
{
	$property = intval($_GET['id']);
	$client_id = intval($_SESSION['clients']['id']);
	$where.= "and id = " . $property . " and client_ID=" . $client_id;
	$where2.= "and id = " . $property . " and client_ID=" . $client_id;
}
else
{
	if (!isset($_SESSION['admin']))
	{
		header('location: index.php');
	}
	if (isset($_GET['pname']))
	{
		if ($_GET['pname'] != '')
		{
			$property = security($_GET['pname']);
			$where.= "and id = " . $property;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
        <head>
        <meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
  <!--<link href="/bootstrap.min.css" rel="stylesheet">-->
		<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
        <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<script type='text/javascript' src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script>
<link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css"> 
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

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

<style>
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

body {
  margin: auto;
  background: #eaeaea;  
  font-family: 'Open Sans', sans-serif;
}

.info p {
  text-align:center;
  color: #999;
  text-transform:none;
  font-weight:600;
  font-size:15px;
  margin-top:2px
}

.info i {
  color:#F6AA93;
}
form h1 {
  font-size: 18px;
  background: #F6AA93 none repeat scroll 0% 0%;
  color: rgb(255, 255, 255);
  padding: 22px 25px;
  border-radius: 5px 5px 0px 0px;
  margin: auto;
  text-shadow: none; 
  text-align:left
}

form {
  border-radius: 5px;
  /*max-width:700px;*/
  /*width:100%;*/
  /*margin: 5% auto;*/
  /*background-color: #FFFFFF;*/
  overflow: hidden;
}

p span {
  color: #F00;
}

p {
  margin: 0px;
  font-weight: 500;
  line-height: 2;
  color:#333;
}

h1 {
  text-align:center; 
  color: #666;
  text-shadow: 1px 1px 0px #FFF;
  margin:50px 0px 0px 0px
}

input {
  border-radius: 0px 5px 5px 0px;
  border: 1px solid #eee;
  margin-bottom: 15px;
  width: 75%;
  height: 40px;
  float: left;
  padding: 0px 15px;
}

a {
  text-decoration:inherit
}

textarea {
  border-radius: 0px 5px 5px 0px;
  border: 1px solid #EEE;
  margin: 0;
  width: 75%;
  height: 130px; 
  float: left;
  padding: 0px 15px;
}

.form-group {
  overflow: hidden;
  clear: both;
}

.icon-case {
  width: 35px;
  float: left;
  border-radius: 5px 0px 0px 5px;
  background:#eeeeee;
  height:42px;
  position: relative;
  text-align: center;
  line-height:40px;
}

i {
  color:#555;
}

.contentform {
  padding: 40px 30px;
}

.bouton-contact{
  background-color: #81BDA4;
  color: #FFF;
  text-align: center;
  width: 100%;
  border:0;
  padding: 17px 25px;
  border-radius: 0px 0px 5px 5px;
  cursor: pointer;
  margin-top: 40px;
  font-size: 18px;
}

.leftcontact {
  width:49.5%; 
  float:left;
  border-right: 1px dotted #CCC;
  box-sizing: border-box;
  padding: 0px 15px 0px 0px;
}

.rightcontact {
  width:49.5%;
  float:right;
  box-sizing: border-box;
  padding: 0px 0px 0px 15px;
}

.validation {
  display:none;
  margin: 0 0 10px;
  font-weight:400;
  font-size:13px;
  color: #DE5959;
}

#sendmessage {
  border:1px solid #fff;
  display:none;
  text-align:center;
  margin:10px 0;
  font-weight:600;
  margin-bottom:30px;
  background-color: #EBF6E0;
  color: #5F9025;
  border: 1px solid #B3DC82;
  padding: 13px 40px 13px 18px;
  border-radius: 3px;
  box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.03);
}

#sendmessage.show,.show  {
  display:block;
}

</style>
        </head>
        <body>
		<!--menu bar-->     
<?php
require_once ('admin_menu.php') ?>
<!--end of menu bar-->
<div class="clearfix" style="celar:both;float:none;">&nbsp;</div>
<div class="row" style="margin:0 !important">

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-10 col-xl-offset-1">

<div class="panel panel-danger">
	<div class="panel-heading">

<?php
if (!isset($_GET['mode']))
{
?>
    <form class="form-inline" action="" method="get">		
		<div class="row">
			<div class="col-sm-10 col-xs-12 col-md-6 col-lg-5 col-xl-4"> 
				<select class="form-control" id="pname" name="pname"  >
					<option value="">Select Property</option>
					<?php
						$stmt1 = $mysqli->query("select id,name from properties where 1 " . $where2 . "  and active_status = 'YES' order by name asc");
						if ($stmt1->num_rows > 0)
						{
							while ($rows2 = $stmt1->fetch_assoc())
							{ ?>
								  <option value="<?php
								echo $rows2['id'] ?>" <?php
								if ($rows2['id'] == $property)
								{
									echo "selected='selected'";
								} ?>><?php
								echo $rows2['name'] ?></option>
								  <?php
							}
						}
					?>
				</select>
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>		
		</div>
	</form>
	</div>
	<div class="panel-body">
	<div>
		<?php
		if (!empty($message)) echo $message; ?>
	</div>
		  <?php
	if ($where != '')
	{
		$fetchRoom = $mysqli->query("SELECT * from properties where 1 " . $where);
		if ($fetchRoom->num_rows > 0)
		{
			$row = $fetchRoom->fetch_assoc();
			foreach ($row as $key=>$value)
			{				
				$row[$key]=stripslashes($value);
			}
	?>	

	<form class="form" action="" method="POST" enctype="multipart/form-data"> 
	<input type="hidden" value="<?php
			echo $row['id'] ?>" name="id" />
	
	<!--start of new form by Nazir-->

	
  <div id="left-content" class="col-xm-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 col-xl-offset-1" style="padding:20px;">
		<div class="form-group">
			<label class="control-label colsm3" for="p_name"><i class="fa fa-home" aria-hidden="true"></i>
</i>Name:</label>
			<div class="colsm9">
			  <input type="text" class="form-control" name="p_name" id="p_name"  value="<?php
		echo $row['name']; ?>"placeholder="Property Name">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="exampleInputtitle">Title:</label>
			<div class="colsm9">
			  <input type="text" class="form-control" name="p_title" id="exampleInputtitle"  value="<?php
		echo $row['p_title']; ?>"placeholder="Title">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="p_link"><i class="fa fa-link" aria-hidden="true"></i>
</i>Link:</label>
			<div class="colsm9">
			  <input type="text" class="form-control" name="p_link"  id="p_link" value="<?php
		echo $row['p_link']; ?>" placeholder="Link">
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="ics_link"><i class="fa fa-link" aria-hidden="true"></i>
</i>Link for the property:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="ics_link" name="ics_link" title="Click here to edit" ><?php
				echo $row["ics_link"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for=""><i class="fa fa-question" aria-hidden="true"></i>
</i>Showe Home Page:</label>
			
			<div class="colsm9">
				<div class="col-sm-1">
					<input type="radio" name="home_page_status" value="1" <?php
					if ($row['home_page_status'] == 1) echo "checked"; ?>>
				</div>
				<div class="col-sm-1" style="padding:0px;margin:0px;">
					<label class="control-label" style="padding-top:15px;text-align:left;">
					  Yes
					</label>
				</div>
				
				<div class="col-sm-1">
					<input type="radio" name="home_page_status" value="0" <?php
					if ($row['home_page_status'] == 0) echo "checked"; ?>>
					</div>
				<div class="col-sm-1" style="padding:0px;margin:0px;">
					<label class="control-label" style="padding-top:15px;text-align:left;">
					  No
					</label>
				</div>
				
				
				
							
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="address"><i class="fa fa-address-book" aria-hidden="true"></i>
</i>Address of the property <a href="#" class="marker" title="Full postal address">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="address" name="address" title="Click here to edit" ><?php
				echo $row["address"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="emergency_1"><i class="fa fa-mobile" aria-hidden="true"></i>
</i>Emergency contact 1 name + phone number <a href="#" class="marker" title="Someone who has your keys and client can contact in case of emergency">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="emergency_1" name="emergency_1" title="Click here to edit" ><?php
				echo $row["emergency_1"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="emergency_2"><i class="fa fa-mobile" aria-hidden="true"></i>
</i>Emergency contact 2 name + phone number <a href="#" class="marker" title="Someone who has your keys and client can contact in case of emergency">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="emergency_2" name="emergency_2" title="Click here to edit" ><?php
				echo $row["emergency_2"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="p_description"><i class="fa fa-sticky-note" aria-hidden="true"></i>
</i>Description homepage:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="p_description" name="p_description" title="Click here to edit" ><?php
				echo $row["p_description"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="description"><i class="fa fa-building-0" aria-hidden="true"></i>
</i>Check in directions en<a href="#" class="marker" title="building door code 1st or 2nd building floor door (name, number or sign to recognize) ">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="description" name="description" title="Click here to edit" ><?php
				echo $row["description"]; ?></textarea>
			</div>
		</div>
			
		<div class="form-group">
			<label class="control-label colsm3" for="descriptionFR"><i class="fa fa-building" aria-hidden="true"></i>
</i>Check in directions FR<a href="#" class="marker" title="Description FR">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="descriptionFR" name="descriptionFR" title="Click here to edit" ><?php
				echo $row["descriptionFR"]; ?></textarea>
			</div>
		</div>
			
		<div class="form-group">
			<label class="control-label colsm3" for="wifi"><i class="fa fa-wifi" aria-hidden="true"></i>
</i>Wifi <a href="#" class="marker" title="Wifi...">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="wifi" name="wifi" title="Click here to edit" ><?php
				echo $row["wifi"]; ?></textarea>
			</div>
		</div>
		
			
		<div class="form-group">
			<label class="control-label colsm3" for="Lavomatic">Lavomatic:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="Lavomatic" name="Lavomatic" title="Click here to edit" ><?php
				echo $row["Lavomatic"]; ?></textarea>
			</div>
		</div>
		
			
		<div class="form-group">
			<label class="control-label colsm3" for="metro"><i class="fa fa-bus" aria-hidden="true"></i>
</i>Metro:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="metro" name="metro" title="Click here to edit" ><?php
				echo $row["metro"]; ?></textarea>
			</div>
		</div>
			
		<div class="form-group">
			<label class="control-label colsm3" for="washing">How to use the washing machine:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="washing" name="washing" title="Click here to edit" ><?php
				echo $row["washing"]; ?></textarea>
			</div>
		</div>
		
  </div><!--//left-->
		
  <div id="right-content" class="col-xm-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 col-xl-offset-1" style="padding:20px;">
		<div class="form-group">
			<label class="control-label colsm3" for="activities"><i class="fa fa-bank" aria-hidden="true"></i>
</i>Activities <a href="#" class="marker" title="Museum to book, activities available around the appartement">?</a>:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="activities" name="activities" title="Click here to edit" ><?php
				echo $row["activities"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="directions"><i class="fa fa-arrows" aria-hidden="true"></i>
</i>Directions <a href="#" class="marker" title="how to go to the appartement from airports/stations">?</a>:</label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="directions" name="directions" title="Click here to edit" ><?php
				echo $row["directions"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="restaurants"><i class="fa fa-hotel" aria-hidden="true"></i>
</i>Restaurants <a href="#" class="marker" title="Good restaurant in your area (style, price, adress, phone number)">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="restaurants" name="restaurants" title="Click here to edit" ><?php
				echo $row["restaurants"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="grocery"><i class="fa fa-shopping-cart" aria-hidden="true"></i>
</i>Groceries <a href="#" class="marker" title="Nearest supermarket, adress and time">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="grocery" name="grocery" title="Click here to edit" ><?php
				echo $row["grocery"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="bakery">Bakery <a href="#" class="marker" title="good bakery near your place, adress and time, and your favorite order there !">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="bakery" name="bakery" title="Click here to edit" ><?php
				echo $row["bakery"]; ?></textarea>
			</div>
		</div>
		
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="check_out"><i class="fa fa-calendar" aria-hidden="true"></i>
</i>Check out <a href="#" class="marker" title="How is the guest supposed to check out">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="check_out" name="check_out" title="Click here to edit" ><?php
				echo $row["check_out"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="wifi_status"><i class="fa fa-wifi" aria-hidden="true"></i>
</i>dongle-mifi status:</label>
			<div class="colsm9">
				<select class="form-control" name="wifi_status" id="wifi_status">
				<option value="YES" <?php
				if ($row["wifi_status"] == 'YES')
				{
					echo "selected='selected'";
				} ?> >YES</option>
			  <option value="NO" <?php
				if ($row["wifi_status"] == 'NO')
				{
					echo "selected='selected'";
				} ?> >NO</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label colsm3" for="driver_status"><i class="fa fa-car" aria-hidden="true"></i>
</i>Driver status:</label>
			<div class="colsm9">
				<select class="form-control" name="driver_status" id="driver_status">
				 <option value="YES" <?php
					if ($row["driver_status"] == 'YES')
					{
						echo "selected='selected'";
					} ?> >YES</option>
				  <option value="NO" <?php
					if ($row["driver_status"] == 'NO')
					{
						echo "selected='selected'";
					} ?> >NO</option>
				</select>
			</div>
		</div>

		
		
		<div class="form-group">
			<label class="control-label colsm3" for="cost_cleaning"><i class="fa fa-credit-dollar" aria-hidden="true"></i>
</i>Cost of Cleaning <a href="#" class="marker" title="Cost of Cleaning">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="cost_cleaning" name="cost_cleaning" title="Click here to edit" ><?php
				echo $row["cost_cleaning"]; ?></textarea>
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="cost_checkin"><i class="fa fa-credit-dollar" aria-hidden="true"></i>
</i>Cost of Check in <a href="#" class="marker" title="Cost of Check in">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="cost_checkin" name="cost_checkin" title="Click here to edit" ><?php
				echo $row["cost_checkin"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="cost_late_checkout"><i class="fa fa-dollar" aria-hidden="true"></i>
</i>Cost of Late Check out <a href="#" class="marker" title="Cost of Late Check out">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="cost_late_checkout" name="cost_late_checkout" title="Click here to edit" ><?php
				echo $row["cost_late_checkout"]; ?></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label colsm3" for="management_fee"><i class="fa fa-dollar" aria-hidden="true"></i>
</i>Management fee <a href="#" class="marker" title="Management fee ">?</a></label>
			<div class="colsm9">
			  <textarea class="edit form-control "  id="management_fee" name="management_fee" title="Click here to edit" ><?php
				echo $row["management_fee"]; ?></textarea>
			</div>
		</div>
		
		
		
		
		<div class="form-group">
			<label class="control-label colsm3" for="keybox"><i class="fa fa-key" aria-hidden="true"></i>
</i>>KEYBOX:</label>
			<div class="colsm9">
				<select class="form-control" name="keybox" id="keybox">
				 <option value="YES" <?php
					if ($row["keybox"] == 'YES')
					{
						echo "selected='selected'";
					} ?> >YES</option>
				  <option value="NO" <?php
					if ($row["keybox"] == 'NO')
					{
						echo "selected='selected'";
					} ?> >NO</option>
				</select>
			</div>
		</div>

		
		<div class="form-group">			
			<div class="col-md-3">		
			<label class="form-label" for="image"><i class="fa fa-image" aria-hidden="true"></i>
			Image:</label>
			</div>
			<div class="col-md-9">
			<input class="form-control" style="padding-bottom:40px;width:420px" id="image" name="image" type="file">
		</div>
		<div class="row">						
				<div class="col-md-8">
				<img src="<?php echo $row['property_img']; ?>" width="200" height="200" />
				
				<?php if ($row['property_img']!=''):?>
					<input type="hidden" name="img" value="<?php echo $row['property_img']?>" />
				<?php endif;?>
				
				</div>
			</div>
		</div>
  </div><!--//right-->
  
          
	
	
	
		
	
	
	<div class="form-group">		
		<div class="col-sm-4 col-sm-offset-4">
			<input type="submit" value="submit" name="basic_submit" class="btn btn-primary center-block" />
		</div>
	</div>

	
	<!--end of new form-->
	</form>
          <div id="update"></div>
          <?php
		}
		else
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No Records found ";
		}
	}
}
else
{
	// edit
	$fetchRoom = $mysqli->query("SELECT * from properties where id= " . intval($_GET['id']));
	if ($fetchRoom->num_rows > 0)
	{
		$row = $fetchRoom->fetch_assoc();
?>
          <form class="form-horizontal" action="" method="post">
    <table class='table table-striped' style="margin-left:20px; width:80%">
              <tr>
        <th>Link </th>
      </tr>
              <tr>
        <td><input type="text" name="link" value="<?php
		echo $row["ics_link"]; ?>"></td>
      </tr>
              <tr>
        <th >Address</th>
      </tr>
              <tr>
        <td><input type="text" name="address" value="<?php
		echo $row["address"] ?>"></td>
      </tr>
              <tr>
        <th>Emergency contact 1</th>
      </tr>
              <tr>
        <td><input type="text" name="em1" value="<?php
		echo $row["emergency_1"] ?>"></td>
      </tr>
              <tr>
        <th>Emergency contact 2</th>
      </tr>
              <tr>
        <td><input type="text" name="em2" value="<?php
		echo $row["emergency_2"] ?>"></td>
      </tr>
              <tr>
        <th>Name</th>
      </tr>
              <tr>
        <td><input type="text" name="name" value="<?php
		echo $row["name"] ?>"></td>
      </tr>
              <tr>
        <th>Description</th>
      </tr>
              <tr>
        <td><input type="text" name="description" value="<?php
		echo $row["description"] ?>">
                  <input type="hidden" name="pr_id" value="<?php
		echo $row["id"] ?>"></td>
      </tr>	  	  	  	  	  	  
              <tr>
        <td><input type="submit" name="update" value="Update" /></td>
      </tr>
            </table>
  </form>
          <?php
	}
}
?>
</div><!--panel body-->
        </div><!--main panel-->
</div><!--main col-div-->
</div>
</body>
        </html>
<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.edit').editable({
				   // validate: function(value) {
				     // if($.trim(value) == '') 
				      //  return 'This field is required';
				   // },
				    success: function(data,newValue) {
					// alert(newValue);
						jQuery('#update').html(data);
						// location.reload();
				    },showbuttons:false
				});
			});
		</script>
		<script>
$(window).load(function(){
$(document).ready(function(){
    $(".marker").tooltip({placement: 'right'});
});
});
</script>
<script>
	$(function(){
		$('#wifi_status').editable({
										value	: '<?php
echo ($row["wifi_status"]); ?>',
										source	: [  
													 {value: 'YES', text: 'YES'},
													 {value: 'NO', text: 'NO'},
												  ]
									});
		});
 </script>
 <script>	
 $(function(){
	 $('#driver_status').editable({
		 							value	: '<?php
echo ($row["driver_status"]); ?>',
									source	: [ 
												{value: 'YES', text: 'YES'},
												{value: 'NO', text: 'NO'},
											  ]
						  		});
							});
   </script>
   <script>
   	function ajaxUpdate(pk,name,fvalue)
	{	 
		var dataString = {'name': name,'value':fvalue,'pk':pk};
			jQuery.ajax({		
							type	: "POST",
							url		: '../ajax.php',
							data	: dataString,
							success : function(data){ }
						});	
	}
    </script>
	
<?php
function security($value)
{
	// echo $value;exit;
	global $mysqli;
	if (is_array($value))
	{
		$value = array_map('security', $value);
	}
	else
	{
		if (get_magic_quotes_gpc()) 
		{
			$value = stripslashes($value);
		}
		else {
			$value = $value;
		}


		$value = trim($mysqli->real_escape_string ($value));
	}
	// echo $value;
	// echo $value;exit;
	return $value;
}

