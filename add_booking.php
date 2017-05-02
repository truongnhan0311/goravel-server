<?php 

require_once('../connect.php');

require_once('../functions_general.php');

$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);

session_start();

if(!isset($_SESSION['admin'])) 

{

		header('location: index.php');

}



$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

if(isset($_POST['btn_submit']))

{

	$name = security(trim($_POST['name']));

	$check_in_new = security(trim($_POST['checkin']));

	$checkout = security(trim($_POST['checkout']));

	$p_id = security(intval($_POST['p_id']));
	
	$check_in = date('l d F Y',strtotime($check_in_new));
	$check_out = date('l d F Y',strtotime($checkout));
	
	$b_data = $db->query("select booking_number from gcal_imports where booking_number LIKE 'M000%' order by id DESC limit 1 ");
	$rows2 	= $b_data->fetchAll(PDO::FETCH_ASSOC);
	
	if(!empty($rows2)) {
	$old_booking_id = substr($rows2[0]['booking_number'],4);
	$booking_no = 'M000'.($old_booking_id + 1);
	} else {
	$booking_no = 'M0001';
	}
	$mysqli->query("INSERT INTO gcal_imports SET name='".$name."',property_id='".$p_id."',check_in_new='".$check_in_new."',check_out='".$check_out."', check_in='".$check_in."', booking_number='".$booking_no."',email='' ");
header('Location: index.php');
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
<style>

.table tr th, .table tr td {

	border-right: 1px solid #ccc

}

</style>

</head>

<body>
<div class="container-fluid table-responsive">
<div class="row">
<!--menu bar-->     
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->



  

            <br>



   

    Add a booking

    <form action='' method='post'>

      <table class='table table-striped'>

        <tr>

          <td align="center"> Name

            <input type="text" name="name" value="" maxlength="30"></td>

          <td align="center"> Property

            <select name="p_id"  >

              <option value="">Please Select Property</option>

              <?php

			 $stmt1 	= $db->query("select id,name from properties where active_status = 'YES' order by name asc");

			$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($rows1)){ 

			 foreach($rows1 as $rows2)

			{ ?>

              <option value="<?php echo $rows2['id']?>" ><?php echo $rows2['name']?></option>

              <?php }

			  }

			?>

            </select></td>

            

			<td align="center"> Check in (yyyy-mm-dd)
				<input type="text" id="check_in" name="checkin" value="<?php echo $date = date('Y-m-d') ?>" maxlength="250" readonly>
			</td>

			<td align="center"> Check out (yyyy-mm-dd)
				<input type="text" id="check_out"  name="checkout" value="<?php echo date('Y-m-d', strtotime('+2 day', strtotime($date))) ?>" maxlength="250" readonly>
			</td>

        </tr>

        <tr>

          <td colspan="3" align="center">

            <input type="submit" name="btn_submit" value="Submit"></td>

        </tr>

      </table>

    </form>

 

  </div>

</div>
<link rel="stylesheet" href="../datetimepicker/css/jquery.datetimepicker.css">
<script src="../datetimepicker/js/jquery.datetimepicker.js"></script>
<script>
	/* var cur_date='<?php echo date('Y-m-d') ?>';
	$('#check_date').val(cur_date); */
	//alert(cur_date);
	$('#check_in').datetimepicker({
		lang		:	'en',
		format		:	'Y-m-d',
		timepicker	:	false,
		scrollInput	:	false,
		minDate		: 'dateToday',
	});
	var currentDate		= new Date();
	var checkoutDate	= new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()+2);
	$('#check_out').datetimepicker({
			lang		:	'en',
			format		:	'Y-m-d',
			defaultDate	:	checkoutDate,
			timepicker	:	false,
			scrollInput	:	false,
			minDate		: 	checkoutDate
		});
</script>


</body>

</html>