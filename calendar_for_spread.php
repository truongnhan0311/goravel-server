<?php
require_once('../connect.php');
require_once('../functions_general.php');

include_once('../event_calander/functions_1.php'); 

$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);

session_start();
//session_destroy();

if( !isset($_SESSION['admin']) ) {
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
<div class="container-fluid table-responsive">
  <div class="row">
  
  <!--menu bar-->     
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->

    <?php
function check_null($value)
{
	if(is_null($value) || ($value == NULL) || ($value == 'NULL'))
	{
		return '';
	}else{
		return $value;
	}
}
function check_date($dates)
{
	$timestamp = strtotime($dates);
	$date = date('Y-m-d', $timestamp);
	$today = date('Y-m-d');
	$tomorrow = date('Y-m-d', strtotime('tomorrow')); 
	
	if ($date == $today) {
	  return "today";
	} else if ($date == $tomorrow) {
	  return "tomorrow";
	}else
	{
		return 'no';
	}
}		
		/* if( !isset($_SESSION['employee']) ) {
		
			if($_POST){
				$username = security(strtoupper(trim($_POST['username'])));
				$password = security($_POST['password']);
				if(empty($username) || empty($password)){
					$error_mess = 'Please enter Name or Password';
				}
				else{
					$stmt 	= $db->prepare("select * from employee WHERE UPPER(name)=? AND password=?");
					$stmt->bindValue(1, $username, PDO::PARAM_STR);
					$stmt->bindValue(2, md5($password), PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetch(PDO::FETCH_ASSOC);
					
					if(!empty($rows)){
						if(strtoupper($rows['job'])=='TAXI')
						{
							$_SESSION['taxi'] = $rows;
							header('Location: taxi.php');
						}else
						{
							$_SESSION['employee'] = $rows;
							
							header('Location: job.php');
						}
					}
				}
			} */
			
			?>
    <div class="col-xs-12 col-md-8">
		<?php
			/* if(isset($error_mess)){
				echo '<div class="alert alert-danger" role="alert">',$error_mess,'</div>';
			} */
		?>
<!--<div>
</div>
      <form class="form-horizontal" action="job.php" method="post">
        <div class="form-group">
          <label for="username">Name</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
<!-- <a href="./instructions.php" class="btn btn-default"> Instructions</a> 
      </form>-->
    </div>
    <?php
		/* }else{ */
		
		
		/* if($_SESSION['employee']['id'] == 7  ||  $_SESSION['employee']['id'] == 16  ){
			
		if($_SESSION['employee']['level'] == 1){
			
		$stmt 	= $db->query("select * from gcal_imports where booking_status != 'cancel'  and  check_in_new >= Date_SUB(CURDATE(), INTERVAL 14 DAY)  order by check_in_new asc");
			
		}elseif($_SESSION['employee']['level'] == 2){
			
		$stmt 	= $db->query("select * from gcal_imports where booking_status != 'cancel'  and  check_in_new >= Date_SUB(CURDATE(), INTERVAL 14 DAY) and ((check_in_person = '".$_SESSION['employee']['id']."' || cleaning_person = '".$_SESSION['employee']['id']."') or (check_in_person = '14' || cleaning_person = '14')) order by check_in_new asc");	
		}
		
		}else{
			
			if($_SESSION['employee']['level'] == 1){
				
			$stmt 	= $db->query("select * from gcal_imports where booking_status != 'cancel'  and check_in_new >= Date_SUB(CURDATE(), INTERVAL 14 DAY)  order by check_in_new asc");
			
			}elseif($_SESSION['employee']['level'] == 2){
				
				$stmt 	= $db->query("select * from gcal_imports where booking_status != 'cancel'  and check_in_new >= Date_SUB(CURDATE(), INTERVAL 14 DAY) and (check_in_person = '".$_SESSION['employee']['id']."' || cleaning_person = '".$_SESSION['employee']['id']."') order by check_in_new asc");
				
			}else{
				
				$stmt 	= $db->query("select * from gcal_imports where booking_status != 'cancel'  and check_in_new >= Date_SUB(CURDATE(), INTERVAL 14 DAY) and (check_in_person = '".$_SESSION['employee']['id']."' || cleaning_person = '".$_SESSION['employee']['id']."') order by check_in_new asc");
				
			}
		
		} */
			$employee_id 		= $_SESSION['employee']['id'];
			
			$properties_detail	= array();	
			$check_on_date='';
			if(isset($_REQUEST['check_date'])){
				$check_on_date 		= $_REQUEST['check_date'];
				
				$date = DateTime::createFromFormat("Y-m-d", $check_on_date);
				$date_y = $date->format("Y");
				$date_m = $date->format("m");
				$date_d = $date->format("d");
			
			}else{
				$check_on_date 		= date('Y-m-d');
				
				$date = DateTime::createFromFormat("Y-m-d", $check_on_date);
				$date_y = $date->format("Y");
				$date_m = $date->format("m");
				$date_d = $date->format("d");
			}
			
			$properties 	= $db->query("select id,name from properties where active_status = 'YES' And id != 3 ");	
			
			$pro_detail 	= $properties->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($pro_detail)){
				$i=0;
				foreach($pro_detail as $p_detail){
					
					$sql_0 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '".$p_detail['id']."'    AND  ( gcal_imports.check_in_new < '$check_on_date' AND gcal_imports.check_out_new > '$check_on_date')   "; 
										
					$current_stay 	= $db->query($sql_0);	
					$current_stay 	= $current_stay->fetchAll(PDO::FETCH_ASSOC);
										
					$sql_1 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '".$p_detail['id']."'    AND  (gcal_imports.check_in_new = '$check_on_date')   "; 
					$check_in 	= $db->query($sql_1);	
					$check_in 	= $check_in->fetchAll(PDO::FETCH_ASSOC);
					
					$sql_2 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '".$p_detail['id']."'   AND  (gcal_imports.check_out_new = '$check_on_date')  "; 
					
					
					$check_out 	    = $db->query($sql_2);	
					$check_out	 	= $check_out->fetchAll(PDO::FETCH_ASSOC);	
					

					$sql_3 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '".$p_detail['id']."'    AND  (gcal_imports.check_in_new > '$check_on_date')  order by gcal_imports.check_in_new ASC limit 1  "; 
					$next_check_in 	= $db->query($sql_3);	
					$next_check_in 	= $next_check_in->fetchAll(PDO::FETCH_ASSOC);
					
					$sql_4 = "select property_id,check_in_person,name,check_in_new,check_in_time,check_out_new,check_out_time,check_in,check_out from gcal_imports where property_id = '".$p_detail['id']."'   AND  (gcal_imports.check_out_new > '$check_on_date')  order by gcal_imports.check_out_new ASC limit 1  "; 
					$next_check_out 	= $db->query($sql_4);
					$next_check_out 	= $next_check_out->fetchAll(PDO::FETCH_ASSOC);		
			
					$properties_detail[$i]['properties_detail'] 		    = $p_detail;
					$properties_detail[$i]['current_stay'] 		    		= $current_stay;
					$properties_detail[$i]['check_in'] 		    			= $check_in;
					$properties_detail[$i]['check_out'] 					= $check_out;
					$properties_detail[$i]['next_check_in'] 				= $next_check_in;
					$properties_detail[$i]['next_check_out'] 				= $next_check_out;				
				$i++; 
				
				}
			}
			
			//echo '<pre>'; print_r($properties_detail);die();
			$pro_status = array();
			$j=0;
			$pro_empty_status=0;
			if(!empty($properties_detail)){
				foreach($properties_detail as $p_detail){
					$pro_status[$j]['pro_name'] = $p_detail['properties_detail']['name'];
					
					
					if(!empty($p_detail['current_stay'])){
						$pro_status[$j]['current_stay_name'] 		= $p_detail['current_stay'][0]['name'];
						$pro_status[$j]['current_stay_date_time'] 	= $p_detail['current_stay'][0]['check_out'].' at '.$p_detail['current_stay'][0]['check_out_time'];
						
						if($employee_id!=4)
						{
							$pro_empty_status = 1;
						}
						
					}else{
						$pro_status[$j]['current_stay_name'] 		= '';
						$pro_status[$j]['current_stay_date_time'] 	= '';
					}
					
					if(!empty($p_detail['check_in'])){
						$pro_status[$j]['check_in_name'] 		= $p_detail['check_in'][0]['name'];
						$pro_status[$j]['check_in_date_time'] 	= $p_detail['check_in'][0]['check_in'].' at '.$p_detail['check_in'][0]['check_in_time'];
						
						if($employee_id!=4)
						{
							$pro_empty_status = 1;
						}
						
					}else{
						$pro_status[$j]['check_in_name'] 		= '';
						$pro_status[$j]['check_in_date_time'] 	= '';
					}
					
					if(!empty($p_detail['check_out'])){
						$pro_status[$j]['check_out_name'] 		= $p_detail['check_out'][0]['name'];
						$pro_status[$j]['check_out_date_time'] 	= $p_detail['check_out'][0]['check_out'].' at '.$p_detail['check_out'][0]['check_out_time'];
						
						if($employee_id!=4)
						{
							$pro_empty_status = 1;
						}
						
					}else{
						$pro_status[$j]['check_out_name'] 		= '';
						$pro_status[$j]['check_out_date_time'] 	= '';
					}
					
					if(!empty($p_detail['next_check_in'])){
						$pro_status[$j]['next_check_in_name'] 		= $p_detail['next_check_in'][0]['name'];
						$pro_status[$j]['next_check_in_date_time'] 	= $p_detail['next_check_in'][0]['check_in'].' at '.$p_detail['next_check_in'][0]['check_in_time'];
					}else{
						$pro_status[$j]['next_check_in_name'] 		= '';
						$pro_status[$j]['next_check_in_date_time'] 	= '';
					}

					if(!empty($p_detail['next_check_out'])){
						$pro_status[$j]['next_check_out_name'] 		  = $p_detail['next_check_out'][0]['name'];
						$pro_status[$j]['next_check_out_date_time']   = $p_detail['next_check_out'][0]['check_out'].' at '.$p_detail['next_check_out'][0]['check_out_time'];
					}else{
						$pro_status[$j]['next_check_out_name'] 		  = '';
						$pro_status[$j]['next_check_out_date_time']   = '';
					}
					
				$j++; 
				}
			}
				
			/* find date selected */
			
			/* $find_date_sql = "select check_in_new,check_out_new	from gcal_imports where check_in_person != '4' AND ( ( YEAR(gcal_imports.check_in_new)=$date_y  Or YEAR(gcal_imports.check_out_new)=$date_y) and ( month(gcal_imports.check_in_new)=$date_m  Or month(gcal_imports.check_out_new)=$date_m )) order by gcal_imports.id DESC "; */
			
			/* $find_date_sql = "select check_in_new,check_out_new from gcal_imports where check_in_person != '4' order by gcal_imports.id DESC "; 

			$avail_date 		= $db->query($find_date_sql);
			$avail_all_date 	= $avail_date->fetchAll(PDO::FETCH_ASSOC);	
			$avail_all_date		= $avail_all_date->fetch_assoc()
			$exclude_date	=  array();
			if(!empty($avail_all_date)){
				foreach($avail_all_date as $av_date){
					$exclude_date[] = $av_date['check_in_new'];
					$exclude_date[] = $av_date['check_out_new'];
				}
			}
			
			$shorted_exclude_date	=	'';
			if(!empty($exclude_date)){
				$temp = array_unique($exclude_date);
				foreach($temp as $t){
					$new_date = DateTime::createFromFormat("Y-m-d", $t);
					$new_date_y = $new_date->format("Y");
					$new_date_m = $new_date->format("m");	
					if(($date_y==$new_date_y) && ($date_m==$new_date_m)){
						$shorted_exclude_date[] = $t;
					}				
				}				
			 }*/
			
			
		//	echo '<pre>'; print_r($shorted_exclude_date);die;
			//echo '<pre>'; print_r($shorted_exclude_date);print_r($exclude_date);die;
			
			/* find date selected */
				
			?>

	
	<form id="check_propertie_submit" class="form-horizontal" action="calendar_for_spread.php" method="post">
		<!--<input type='text' name="check_date" id="check_date"  style="margin-left:400px;" value="<?php //echo $check_on_date; ?>" readonly />
		<input type="submit" name="submit" value="Check Property Status" />
		<div id="loading" ></div>-->
		
		<div id="calendar_div">
			<?php echo getCalender($date_y,$date_m,$date_d,$pro_empty_status); ?>
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
			<?php if(!empty($pro_status)){ 
				$i=0;
					foreach($pro_status as $status){
													
					$person_stay_in_property = '';
					if(($status['current_stay_name']!='' && $status['current_stay_date_time']!='') ){
						$person_stay_in_property = "'".$status['current_stay_name']."' until '".$status['current_stay_date_time']."'";
					}
					
					$today_checkin = '';
					if(($status['check_in_name']!='' && $status['check_in_date_time']!='')){
						$today_checkin = "'".$status['check_in_name']."' check in '".$status['check_in_date_time']."'";
					}
					
					$today_checkout = '';
					if(($status['check_out_name']!='' && $status['check_out_date_time']!='')){
						$today_checkout = "'".$status['check_out_name']."' check out '".$status['check_out_date_time']."'";
					}
					
					$next_checkin = '';
					if(($status['next_check_in_name']!='' && $status['next_check_in_date_time']!='')){
						$next_checkin = "'".$status['next_check_in_name']."' will check in '".$status['next_check_in_date_time']."'";
					}
					
					$empty_until = '';
					if(($status['next_check_in_name']!='' && $status['next_check_in_date_time']!='')){
						$empty_until = "empty until '".$status['next_check_in_date_time']."'";
					}
									
					$green_class='';
					if($person_stay_in_property=='' && $today_checkin=='' && $today_checkout==''){
						$green_class="Class = 'green_td' ";	
					}
					
					$combine_status = '';
					
					if($person_stay_in_property!=''){
						$combine_status .= ' - '.$person_stay_in_property;
					}
					
					if($today_checkout!=''){
						$combine_status .= ' - '.$today_checkout;
					}
					
					if($today_checkin!=''){
						$combine_status .= ' - '.$today_checkin;
					}
					
					if($today_checkin=='' && $person_stay_in_property!='' && $next_checkin!=''){
						$combine_status .= ' - '.$next_checkin;
					}
					if($today_checkin=='' && $today_checkout!='' && $next_checkin!=''){
						$combine_status .= ' - '.$next_checkin;
					}
					
					if($person_stay_in_property=='' && $today_checkin=='' && $today_checkout==''  && $empty_until !=''){
						$combine_status .= ' - '.$empty_until;
					}
										
					if($combine_status!=''){
						$combine_status = ltrim($combine_status," -");
						$combine_status = str_replace("'","",$combine_status);
					}
			?>
			<tr>
			  <th <?php echo $green_class; ?>><?php echo $status['pro_name']; ?></th>
			  <th><?php echo $combine_status; ?></th>
			</tr>
		<?php } } ?>
		</tbody>
    </table>
    
  </div>
</div>
<?php /* } */ ?>

<link rel="stylesheet" href="../datetimepicker/css/jquery.datetimepicker.css">
<script src="../datetimepicker/js/jquery.datetimepicker.js"></script>
<script>
	/* var cur_date='<?php echo date('Y-m-d') ?>';
	$('#check_date').val(cur_date); */
	$('#check_date').datetimepicker({
		lang:'en',
		format:'Y-m-d',
		timepicker:false,
		scrollInput:false,
	});

</script>

</body>
</html>