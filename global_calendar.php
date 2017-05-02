<?php

require_once ('../connect.php');
require_once ('../functions_general.php');
include_once ('../event_calander/functions.php');
$db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
session_start();
// session_destroy();
if (!isset($_SESSION['admin']))
{
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
.check_in_today td,.check_in_today_li{background-color:#00FF00 !important}
.check_out_today td,.check_out_today_li{background-color:#F78181 !important}
.taxi_li{background-color:#FFFF00 !important;color:black !important}
.mifi_li{background-color:#58D3F7 !important;color:black !important}
.check_in_tomorrow_li a,.in_apartment_li a,.in_late_check_out_li a,.check_in_today_li a,.taxi_li a{color:#777 !important}
.check_in_tomorrow_li a,.check_in_today_li a,.check_out_today_li a{cursor: default; !important}
.navbar-inverse{background-color:  #D8D8D8 !important;}
.navbar-inverse a { color:#000000 !important;}	
.in_apartment td,.in_apartment_li{background-color:#F5D0A9 !important}
.in_late_check_out_li{background-color:#FF8000 !important}
.Gray  td {
	background-color:#828383  !important;
}
</style>
<!-- addedd on 7-12-5016 -->
<link type="text/css" rel="stylesheet" href="../event_calander/style.css"/>
<script src="../event_calander/jquery.min.js"></script>
<!-- addedd on 7-12-5016 -->
</head>
<body>

  <!--menu bar-->     
<?php
require_once ('admin_menu.php') ?>
<!--end of menu bar-->
<div class="container-fluid table-responsive">
  <div class="row">
    <?php
$employee_id = $_SESSION['employee']['id'];
$properties_detail = array();
$check_on_date = '';
if (isset($_REQUEST['check_date']))
{
	$check_on_date = $_REQUEST['check_date'];
	$date = DateTime::createFromFormat("Y-m-d", $check_on_date);
	$date_y = $date->format("Y");
	$date_m = $date->format("m");
	$date_d = $date->format("d");
}
else
{
	$check_on_date = date('Y-m-d');
	$date = DateTime::createFromFormat("Y-m-d", $check_on_date);
	$date_y = $date->format("Y");
	$date_m = $date->format("m");
	$date_d = $date->format("d");
}
$properties = $db->query("select id,name from properties where active_status = 'YES' And id != 3 ");
$pro_detail = $properties->fetchAll(PDO::FETCH_ASSOC);
if (!empty($pro_detail))
{
	$i = 0;
	foreach($pro_detail as $p_detail)
	{
		$sql_0 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '" . $p_detail['id'] . "'    AND  ( gcal_imports.check_in_new < '$check_on_date' AND gcal_imports.check_out_new > '$check_on_date')   ";
		$current_stay = $db->query($sql_0);
		$current_stay = $current_stay->fetchAll(PDO::FETCH_ASSOC);
		
		$sql_1 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '" . $p_detail['id'] . "'    AND  (gcal_imports.check_in_new = '$check_on_date')   ";
		$check_in = $db->query($sql_1);
		$check_in = $check_in->fetchAll(PDO::FETCH_ASSOC);
		
		$sql_2 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '" . $p_detail['id'] . "'   AND  (gcal_imports.check_out_new = '$check_on_date')  ";
		$check_out = $db->query($sql_2);
		$check_out = $check_out->fetchAll(PDO::FETCH_ASSOC);
		
		$sql_3 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '" . $p_detail['id'] . "'    AND  (gcal_imports.check_in_new > '$check_on_date')  order by gcal_imports.check_in_new ASC limit 1  ";
		$next_check_in = $db->query($sql_3);
		$next_check_in = $next_check_in->fetchAll(PDO::FETCH_ASSOC);
		
		$sql_4 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '" . $p_detail['id'] . "'   AND  (gcal_imports.check_out_new > '$check_on_date')  order by gcal_imports.check_out_new ASC limit 1  ";
		$next_check_out = $db->query($sql_4);
		$next_check_out = $next_check_out->fetchAll(PDO::FETCH_ASSOC);
		
		$properties_detail[$i]['properties_detail'] = $p_detail;
		$properties_detail[$i]['current_stay'] = $current_stay;
		$properties_detail[$i]['check_in'] = $check_in;
		$properties_detail[$i]['check_out'] = $check_out;
		$properties_detail[$i]['next_check_in'] = $next_check_in;
		$properties_detail[$i]['next_check_out'] = $next_check_out;
		$i++;
	}
}
$pro_status = array();
$j = 0;
$pro_empty_status = 0;
if (!empty($properties_detail))
{
	foreach($properties_detail as $p_detail)
	{
		$pro_status[$j]['pro_name'] = $p_detail['properties_detail']['name'];
		if (!empty($p_detail['current_stay']))
		{
			$pro_status[$j]['current_stay_name'] = $p_detail['current_stay'][0]['name'];
			$pro_status[$j]['current_stay_date_time'] = $p_detail['current_stay'][0]['check_out'] . ' at ' . $p_detail['current_stay'][0]['check_out_time'];
			if ($employee_id != 4)
			{
				$pro_empty_status = 1;
			}
		}
		else
		{
			$pro_status[$j]['current_stay_name'] = '';
			$pro_status[$j]['current_stay_date_time'] = '';
		}
		if (!empty($p_detail['check_in']))
		{
			$pro_status[$j]['check_in_name'] = $p_detail['check_in'][0]['name'];
			$pro_status[$j]['check_in_date_time'] = $p_detail['check_in'][0]['check_in'] . ' at ' . $p_detail['check_in'][0]['check_in_time'];
			if ($employee_id != 4)
			{
				$pro_empty_status = 1;
			}
		}
		else
		{
			$pro_status[$j]['check_in_name'] = '';
			$pro_status[$j]['check_in_date_time'] = '';
		}
		if (!empty($p_detail['check_out']))
		{
			$pro_status[$j]['check_out_name'] = $p_detail['check_out'][0]['name'];
			$pro_status[$j]['check_out_date_time'] = $p_detail['check_out'][0]['check_out'] . ' at ' . $p_detail['check_out'][0]['check_out_time'];
			if ($employee_id != 4)
			{
				$pro_empty_status = 1;
			}
		}
		else
		{
			$pro_status[$j]['check_out_name'] = '';
			$pro_status[$j]['check_out_date_time'] = '';
		}
		if (!empty($p_detail['next_check_in']))
		{
			$pro_status[$j]['next_check_in_name'] = $p_detail['next_check_in'][0]['name'];
			$pro_status[$j]['next_check_in_date_time'] = $p_detail['next_check_in'][0]['check_in'] . ' at ' . $p_detail['next_check_in'][0]['check_in_time'];
		}
		else
		{
			$pro_status[$j]['next_check_in_name'] = '';
			$pro_status[$j]['next_check_in_date_time'] = '';
		}
		if (!empty($p_detail['next_check_out']))
		{
			$pro_status[$j]['next_check_out_name'] = $p_detail['next_check_out'][0]['name'];
			$pro_status[$j]['next_check_out_date_time'] = $p_detail['next_check_out'][0]['check_out'] . ' at ' . $p_detail['next_check_out'][0]['check_out_time'];
		}
		else
		{
			$pro_status[$j]['next_check_out_name'] = '';
			$pro_status[$j]['next_check_out_date_time'] = '';
		}
		$j++;
	}
}
?>
	<form id="check_propertie_submit" class="form-horizontal" action="global_calendar.php" method="post">		
		<div id="calendar_div">
			<?php
echo getCalender($date_y, $date_m, $date_d, $pro_empty_status); ?>
		</div>
	</form>
	<br/>	
    <table class="table table-striped">
      <thead>
        <tr>
          <th></th>
        </tr>
      </thead>
    </table>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Property Name</th>
          <th>Property Status</th>
        </tr>
      </thead>
		<tbody>
			<?php
if (!empty($pro_status))
{
	$i = 0;
	foreach($pro_status as $status)
	{
		$person_stay_in_property = '';
		if (($status['current_stay_name'] != '' && $status['current_stay_date_time'] != ''))
		{
			$person_stay_in_property = "'" . $status['current_stay_name'] . "' until '" . $status['current_stay_date_time'] . "'";
		}
		$today_checkin = '';
		if (($status['check_in_name'] != '' && $status['check_in_date_time'] != ''))
		{
			$today_checkin = "'" . $status['check_in_name'] . "' check in '" . $status['check_in_date_time'] . "'";
		}
		$today_checkout = '';
		if (($status['check_out_name'] != '' && $status['check_out_date_time'] != ''))
		{
			$today_checkout = "'" . $status['check_out_name'] . "' check out '" . $status['check_out_date_time'] . "'";
		}
		$next_checkin = '';
		if (($status['next_check_in_name'] != '' && $status['next_check_in_date_time'] != ''))
		{
			$next_checkin = "'" . $status['next_check_in_name'] . "' will check in '" . $status['next_check_in_date_time'] . "'";
		}
		$empty_until = '';
		if (($status['next_check_in_name'] != '' && $status['next_check_in_date_time'] != ''))
		{
			$empty_until = "empty until '" . $status['next_check_in_date_time'] . "'";
		}
		$green_class = '';
		if ($person_stay_in_property == '' && $today_checkin == '' && $today_checkout == '')
		{
			$green_class = "Class = 'green_td' ";
		}
		$combine_status = '';
		if ($person_stay_in_property != '')
		{
			$combine_status.= ' - ' . $person_stay_in_property;
		}
		if ($today_checkout != '')
		{
			$combine_status.= ' - ' . $today_checkout;
		}
		if ($today_checkin != '')
		{
			$combine_status.= ' - ' . $today_checkin;
		}
		if ($today_checkin == '' && $person_stay_in_property != '' && $next_checkin != '')
		{
			$combine_status.= ' - ' . $next_checkin;
		}
		if ($today_checkin == '' && $today_checkout != '' && $next_checkin != '')
		{
			$combine_status.= ' - ' . $next_checkin;
		}
		if ($person_stay_in_property == '' && $today_checkin == '' && $today_checkout == '' && $empty_until != '')
		{
			$combine_status.= ' - ' . $empty_until;
		}
		if ($combine_status != '')
		{
			$combine_status = ltrim($combine_status, " -");
			$combine_status = str_replace("'", "", $combine_status);
		}
?>
			<tr>
			  <th <?php
		echo $green_class; ?>><?php
		echo $status['pro_name']; ?></th>
			  <th><?php
		echo $combine_status; ?></th>
			</tr>
		<?php
	}
} ?>
		</tbody>
    </table>
  </div>
</div>
<?php ?>
<link rel="stylesheet" href="../datetimepicker/css/jquery.datetimepicker.css">
<script src="../datetimepicker/js/jquery.datetimepicker.js"></script>
<script>
	$('#check_date').datetimepicker({
		lang:'en',
		format:'Y-m-d',
		timepicker:false,
		scrollInput:false,
	});
</script>
</body>
</html>
<?php
function check_null($value)
{
	if (is_null($value) || ($value == NULL) || ($value == 'NULL'))
	{
		return '';
	}
	else
	{
		return $value;
	}
}
function check_date($dates)
{
	$timestamp = strtotime($dates);
	$date = date('Y-m-d', $timestamp);
	$today = date('Y-m-d');
	$tomorrow = date('Y-m-d', strtotime('tomorrow'));
	if ($date == $today)
	{
		return "today";
	}
	else
	if ($date == $tomorrow)
	{
		return "tomorrow";
	}
	else
	{
		return 'no';
	}
}

