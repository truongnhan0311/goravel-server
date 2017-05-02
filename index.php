<?php
require_once ('../connect.php');

require_once ('../functions_general.php');


try {
    $db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
} catch(\PDOException $e){
    echo "Error connecting to mysql: ". $e->getMessage();
}

$db->exec("SET NAMES 'utf8';");
session_start();
$name = '';
$property = '';
$where = '';
$booking_status_where = " and booking_status != 'cancel' ";

if (isset($_GET['booking_status']))
{
	if ($_GET['booking_status'] != '')
	{
		$booking_status_where = " and booking_status = 'cancel' ";
	}
}

if (isset($_POST['sname']))
{
	if ($_POST['sname'] != '')
	{
		$name = security($_POST['sname']);
		$where.= " and (check_in_person = '" . $name . "' || cleaning_person = '" . $name . "')";
	}
}

if (isset($_POST['pname']))
{
	if ($_POST['pname'] != '' && intval($_POST['pname']) > 0)
	{
		$property = security($_POST['pname']);
		$where.= "and property_id = " . $property;
	}

	if ($_POST['pname'] == 'all')
	{
		$stmt1 = $db->query("select id,name from properties where cat_id='" . $_POST['cat_id'] . "' ");
		$rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
		if (!empty($rows1))
		{
			$ids = '';
			foreach($rows1 as $rows2)
			{
				$ids.= "'" . $rows2['id'] . "',";
			}

			$property = rtrim($ids, ',');
		}

		$where.= " and property_id  IN (" . $property . ") ";

		// echo $where; die;

	}
}

if (isset($_POST['cat_id']))
{
	if ($_POST['cat_id'] != '')
	{
		$cat_id = security($_POST['cat_id']);
		$where.= " and cat_id = " . $cat_id;
	}
}

/*
if(isset($_POST['pname'])){
if(!empty($_POST['pname']))
{
$property = implode(',' ,$_POST['pname']);
$where .="and property_id IN (".$property.")";
}
}*/

if (!isset($_SESSION['admin']))
{
	if ($_POST)
	{
		$username = security($_POST['username']);
		$password = security($_POST['password']);
		if (empty($username) || empty($password))
		{
			$error_mess = 'Please enter Username or Password';
		}
		else
		{
			$stmt = $db->prepare("select * from admin WHERE username=? AND password=?");
			$stmt->bindValue(1, $username, PDO::PARAM_STR);
			$stmt->bindValue(2, md5($password) , PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!empty($rows))
			{
				$_SESSION['admin'] = $rows;
				header('Location: index.php');
			}
			else
			{
				$error_mess = 'Please enter correct Username or Password';
			}
		}
	}
}

?>
<!DOCTYPE html>


<html lang="en">
    <head>
    	<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
   		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    	<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
    	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    	<script src="jquery.timepicker.min.js"></script>
		<link href="jquery.timepicker.css" rel="stylesheet">
		<style>
            .green_td {  background-color: #acfa58 !important;  max-width: 1%;}
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
            .form-horizontal #loading {display: inline-block;  float: left; margin-left: 5px;}
            .green_class{background-color:green !important;}
            .white_class{background-color:#fff !important;}
            /*.checkin_agian td{background-color: #FF8000 !important;}*/
			.check_in_tomorrow td.cleaning_persion {background-color: #CEE3F6 !important;}
			.in_apartment td.cleaning_persion {background-color: #CEE3F6 !important;}
			.check_in_today td.cleaning_persion {background-color: #CEE3F6 !important;}
			.check_out_today td.cleaning_persion {background-color: #CEE3F6 !important;}
			.cleaning_persion {background-color: #CEE3F6 !important;}
			
			.check_in_tomorrow td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.in_apartment td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.check_in_today td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.check_out_today td.maxNight{background-color: rgb(255, 128, 0) !important ;}
			.maxNight{background-color: rgb(255, 128, 0)!important;}
			.interest{background-color:#D0A9F5;}
			.spreadlogo {width: 34px; padding-left: 5px;}
			
			.symbol a{ text-decoration:none !important; color:#000 !important}
			
			
			/*style edited by nazir for removing padding*/
			
			.mytable > thead > tr > th, .mytable > tbody > tr > th, .mytable > tfoot > tr > th, .mytable > thead > tr > td, .mytable > tbody > tr > td, .mytable > tfoot > tr > td
			{
				padding-left: 4px !important;
				padding-right: 0px !important;	
				border-top: 1px solid #ddd;
				line-height: 1.42857;
				padding-top: 8px;
				padding-bottom: 8px;
				vertical-align: top;				
			}
			
			.pro-pic { width:45px; height:45px; border-radius:50%; padding:0px; margin-bottom:0px; }
			.profile-pic .item img.thumbnail { margin:auto; border-radius:50%; border:0px solid; padding:0px; }
			.verticalLine { display:block; padding-right:3px; }
        </style>
    </head>

	<body>

<div class="container-fluid table-responsive">
    <div class="row">
    
    
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

if (!isset($_SESSION['admin']))
{
?>
    
    
    <div class="col-xs-12 col-md-8">
        <?php
	if (isset($error_mess))
	{
		echo '<div class="alert alert-danger" role="alert">', $error_mess, '</div>';
	} ?>
    
        <form class="form-horizontal" action="index.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    
    </div>
    
    
    <?php
}
else
{
?>
    
    
    
    
    
    <!--menu bar-->     
    <?php
	require_once ('admin_menu.php') ?>
    <!--end of menu bar-->
	<div class="clearfix"></div>
    <div class="navbar-collapse collapse">
    <ul class="nav navbar-nav">
     <li class="taxi_li"><a href="../job/taxi.php">Taxi </a></li>
    <!-- <li class="mifi_li"><a>Mifi dongle </a></li>-->
     <li class="check_in_tomorrow_li"><a>Check in tomorrow </a></li>
     <li class="check_in_today_li"><a>Check in today </a></li>
     <li class="check_out_today_li"><a>Check out today</a></li>
     <li class="in_apartment_li"><a>In the appartement now</a></li>
     <li class="in_late_check_out_li"><a>Late check out</a></li>
     <li class="booking_cancel_li"><a href="index.php?booking_status=yes">Booking cancelled</a></li>
     <!--<li class="interest"><a>Interest</a></li>-->
    </ul>
  
    
    </div>
      <br />
    <div class="navbar-collapse collapse">
    	<ul class="nav navbar-nav">
    	
     <li class="symbol"><a><img src="uploads/experience-green.png" width="40px" height="40">&nbsp;<strong>Activities</strong></a></span></li>
     <li class="symbol"><a><img src="uploads/late-check-out-green.png" width="40px" height="40">&nbsp;<strong>Late check out</strong></a></li>
     <li class="symbol"><a><img src="uploads/long-stay-green.png" width="40px" height="40">&nbsp;<strong>Long Stay</strong><strong></strong></a></li>
     <li class="symbol"><a><img src="uploads/note-green.png" width="40px" height="40">&nbsp;<strong>Notes</strong></a></li>
     <li class="symbol"><a><img src="uploads/short-flipping-time-green.png" width="40px" height="40">&nbsp;<strong>Short flipping time</strong></a></li>
     <li class="symbol"><a><img src="uploads/taxi-green.png" width="40px" height="40">&nbsp;<strong>taxi</strong></a></li>
     </ul>
    </div>
    <br />
    <?php
	$employees_array=array();
	
	?>
	
	<form class="form-horizontal" action="" method="post">
    <select name="sname"  style="margin-left:400px;">
    <option value="">Please Select Employee</option>
    <?php
	
	
	
	$stmt1 = $db->query("select id,name,job from employee where status='YES' order by name asc");
	$rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($rows1))
	{
		foreach($rows1 as $rows2)
		{ 
			$employees_array[$rows2['id']]['name']=$rows2['name'];
			$employees_array[$rows2['id']]['job']=$rows2['job'];		
		?>
    <option value="<?php
			echo $rows2['id'] ?>" <?php
			if ($rows2['id'] == $name)
			{
				echo "selected='selected'";
			} ?>><?php
			echo $rows2['name'] ?></option>
         <?php
		}
	}
	
?>
    </select>    
    
	
    <select name="cat_id"  onChange="return get_property(this.value);" >
        <option value="">Please Select category</option>
    <?php
	$cat_sql = $db->query("select cat_id,category_name from categories where status='active' ");
	$cat_results = $cat_sql->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($cat_results))
	{
		foreach($cat_results as $cat_result)
		{
			if ($_POST['cat_id'])
			{
				$cat_id = $_POST['cat_id'];
			}
			else
			{
				$cat_id = '';
			}

?>
    <option value="<?php
			echo $cat_result['cat_id'] ?>" <?php
			if ($cat_result['cat_id'] == $cat_id)
			{
				echo "selected='selected'";
			} ?>><?php
			echo $cat_result['category_name'] ?></option>
    <?php
		}
	} ?>
    </select>
    <select name="pname"  id="property" >
    <option value="">Please Select Property</option>
    <?php
	if ($_POST['cat_id'])
	{
		$stmt1 = $db->query("select id,name from properties  where active_status = 'YES' and cat_id=" . $_POST['cat_id'] . " order by name asc");
	}
	else
	{
		$stmt1 = $db->query("select id,name from properties where active_status = 'YES' order by name asc");
	}

	$rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($rows1))
	{
		foreach($rows1 as $rows2)
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








    <input type="submit" name="submit" value="submit" /><div id="loading" ></div>
    
    
    </form>
    <br />
   
    <?php
    $query = "UPDATE gcal_imports SET check_in_new = str_to_date(check_in,'%W %d %M %Y'), check_out_new = str_to_date(check_out,'%W %d %M %Y')";
    //echo $query;
	$update = $db->query($query);
	
	@$update->execute();
	
	$stmt = $db->query("select gcal_imports.*,properties.name as property,properties.keybox from gcal_imports left join properties on gcal_imports.property_id=properties.id where check_out_new >= Date_SUB(CURDATE(), INTERVAL 2 DAY) " . $where . " " . $booking_status_where . " order by check_in_new asc");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// echo "<pre>"; print_r($rows);

	if (!empty($rows))
	{ ?>
    
    <table class="mytable table-striped table-condensed">
        <thead>
            <tr>
            <th></th>
                <th style="width:70px;max-width:70px;">Name</th>
				<th style="width:30px;max-width:30px;"></th>
                <th style="width:40px;max-width:40px;">Booking number</th>
                <th style="width:110px;max-width:110px;">Property</th>
				<th style="width:400px;">Check in</th>
                <th style="width:400px;">Check out/Cleaning</th>                
                <th style="width:220px;max-width:220px;"></th>
                <th style="width:10px;max-width:10px;">?</th>
                <th style="width:10px;max-width:10px;">!</th>
                <th style="width:10px;max-width:10px;"></th>
                
                
            </tr>
        </thead>
    <tbody>
    <?php
		foreach($rows as $key => $value)
		{
			$check_in_person_name = '';
			$cleaning_person_name = '';
			/*check in person*/
			$check_in_person = intval($value['check_in_person']);
			$check_in_person_res = $db->query("SELECT * from employee where id=" . $check_in_person);
			$check_in_person_res_row = $check_in_person_res->fetchAll(PDO::FETCH_ASSOC);
			if (!empty($check_in_person_res_row)) $check_in_person_name = $check_in_person_res_row[0]['name'];
			/*cleaning person*/
			$cleaning_person = intval($value['cleaning_person']);
			$cleaning_person_res = $db->query("SELECT * from employee where id=" . $cleaning_person);
			$cleaning_person_res_row = $cleaning_person_res->fetchAll(PDO::FETCH_ASSOC);
			if (!empty($cleaning_person_res_row)) $cleaning_person_name = $cleaning_person_res_row[0]['name'];
			$tr_class = '';
			$tdLog = '';
			$logColor = '';
			$new_checkin_date = date('Y-m-d', strtotime($value['check_in']));
			$new_checkout_date = date('Y-m-d', strtotime($value['check_out']));
			$curr_dt = date('Y-m-d');
			if (($curr_dt >= $new_checkin_date) && ($curr_dt <= $new_checkout_date))
			{
				$tr_class = ' class="in_apartment"';
			}

			$date_check = check_date($value['check_in']);
			if ($date_check == 'today') $tr_class = ' class="check_in_today"';
			else{
			if ($date_check == 'tomorrow') $tr_class = ' class="check_in_tomorrow"';
			
			}
			$date_checkout = check_date($value['check_out']);
			if ($date_checkout == 'today') $tr_class = ' class="check_out_today"';
			if ($value['booking_status'] == 'cancel') $tr_class = ' class="booking_cancel"';
			$check_out_status = "and check_in ='" . $value['check_out'] . "'";
			$queytm = $db->query("select *,gcal_imports.id as checkout from gcal_imports left join properties on gcal_imports.property_id=properties.id where check_out_new >= Date_SUB(CURDATE(), INTERVAL 2 DAY) " . $where . " " . $booking_status_where . " " . $check_out_status . "  order by check_in_new asc");
			$checktime = $queytm->fetchAll(PDO::FETCH_ASSOC);

			// echo "<pre>"; print_r($checktime);

			foreach($checktime as $key => $check_time)
			{

				// echo $check_time['check_in_time']."  ".$value['check_out_time']."<br />";

				$to_time = strtotime($check_time['check_in_time']);
				$from_time = strtotime($value['check_out_time']);
				$propertyId = $value['property_id'];
				$check_timeId = $check_time['id'];
				if (!empty($to_time) AND !empty($from_time))
				{
					$diff_time = ($to_time - $from_time) / 60;
					if ($diff_time < 120 AND $diff_time > - 120 AND $propertyId == $check_timeId)
					{
						$check_outtime = $check_time['checkout'];
					}
				}

				// echo $diff_time ,"= ", $check_time['check_in_time'] ," - ", $value['check_out_time'],"<br />";

			}

			// echo   (int)$check_outtime ."== ".(int)$value['id']."<br />";

			if ($check_outtime == $value['id'])
			{
				$tdLog = 'style="background-color: #FF8000 !important;"';
				$logColor = "late check out";
				//$tr_class = " class ='checkin_agian' ";
			}

			$td_maxNight = " ";
			$td_text = " ";
			if ($value['nights'] >= 6)
			{
				$logColor = "long stay";
				//$tdLog = 'style="background-color: rgb(255, 128, 0)!important;"';
                                $tdLog = '';
				$td_maxNight = " class ='maxNight' ";
				$td_text = " class ='nightTxt' ";
			}

			echo '<tr' . $tr_class . '>';
			
			echo '<td>';
				if(!empty($value['profile_pic'])){
					$has_pics=true;
						$image_rows.='<div class="item" id="img_' . $value['id'] . '">
						<img class="thumbnail img-responsive" title="" src="' . $value['profile_pic'] . '"></div>';
					
					}
				 if ($has_pics):?>
			<a class="verticalLine" href="javascript:void(0)">
            	<img class="thumbnail pro-pic" id="img_<?php echo $value['id']; ?>" src="<?php echo $value['profile_pic'];?>" />
        	</a>
		<?php endif; $has_pics=false;
			echo '</td>';
			$understyle = '';
			if ($value['photoshoot'] == 'YES')
			{
				$understyle = 'style="background-color: #58d3f7 !important;"';
			}
			elseif (!empty($value['intereste']) && $value['intereste'] != 'none')
			{
				$logColor = "interest";
				//$tdLog = $understyle = 'style="background-color: #D0A9F5 !important;"';
                                $tdLog = $understyle = '';
			}

			echo '<td ' . $understyle . ' ><a   href=detail.php?b=', $value['booking_number'], '>
    
    
                    '; echo  $name = strstr($value['name'],' ',true); 
					if(empty($name)){ echo $value['name'];} echo  '</a></td>';
					echo'<td><a   href=guest_mail_detail.php?bn=', $value['booking_number'], '><img src="./uploads/email-message-by-mobile-phone.png" width="100%"></a></td>';
			
			if ($value['booking_status'] == 'cancel')
			{
				echo '<td class="green_td"><a>', substr($value['booking_number'],0,3), '..</a></td>';
			}
			else
			{
				if (check_null($value['arrival_time']) != '')
				{
					echo '<td class="green_td"><a href=./question.php?b=', $value['booking_number'], '>
    
    
                        ', substr($value['booking_number'],0,3), '..</a></td>';
				}
				else
				{
					echo '<td><a href=./question.php?b=', $value['booking_number'], '>
    
    
                        ', substr($value['booking_number'],0,3), '..</a></td>';
				}
			}
			
			echo '<td>', $value['property'], '</td>';
			
			$check_in_string="";
			$check_out_string="";
			
			$check_in_date_timestamp = strtotime($value['check_in']);
			$check_in = date('l d M', $check_in_date_timestamp);
			// echo '<td class="copyInstruction">', $check_in, '</td>';
			$check_out_date_timestamp = strtotime($value['check_out']);
			$check_out = date('l d M', $check_out_date_timestamp);
			// echo '<td>', $check_out, '</td>';
			
			if (strtoupper($value['taxi']) == 'YES')
			{
				$logColor = "taxi";
				//$tdLog = 'style="background-color:#FFFF00 !important"';
                                $tdLog = '';
				// echo '<td style="background-color:#FFFF00 !important">', $value['check_in_time'], '</td>';
				$check_in_time=$value['check_in_time'];
			}
			// else echo '<td>', $value['check_in_time'], '</td>';
			else $check_in_time=$value['check_in_time'];
			
			if (($value["check_out_request"] == 'I need to request a late check out') || ($value["check_out_request"] == 'I need to request a late check out for 40e'))
			{
				$logColor = "short flipping time";
				//$tdLog = 'style="background-color:#FF8000 !important"';
                                $tdLog = '';
				// echo '<td style="background-color:#FF8000 !important">', $value['check_out_time'], '</td>';
				$check_out_time=$value['check_out_time'];
			}
			else
			{
				// echo '<td>', $value['check_out_time'], '</td>';
				$check_out_time=$value['check_out_time'];
			}

			// echo '<td>', $check_in_person_name, '</td>' . '<td', $td_class, '>', $cleaning_person_name, '</td>';
			
			
			$check_in_string=$check_in . ' - Check in at ' . '<input value="' . $check_in_time . '" style="width:70px;" type="text"  id="checkintimedp_' . $value['booking_number'] . '" />';
			
			if ($value["keybox"] != "YES")
			{
				$check_in_string .= ' by ' . get_dropdown_for_employee ($employees_array,$check_in_person_name,$value['booking_number'],'checkindp');
			}
			
			$check_out_string=$check_out . ' - Cleaning after ' . '<input value="' . $check_out_time . '" style="width:70px;" type="text"  id="checkouttimedp_' . $value['booking_number'] . '" />'  . ' by ' . get_dropdown_for_employee ($employees_array,$cleaning_person_name,$value['booking_number'],'cleaningdp');
			
			echo '<td>' . $check_in_string . '</td>';
			echo '<td>' . $check_out_string . '</td>';
			
?>
                        <td <?php
			echo $tdLog; ?> >
                        	<?php
			$flag = 0;
			if ($value["keybox"] == "NO")
			{
				if ($value["check_in_time"] == '21:00' or $value["check_in_time"] == '21:30' or $value["check_in_time"] == '22:00' or $value["check_in_time"] == '22:30' or $value["check_in_time"] == '23:00')
				{
					$flag = 1;
				}
			}

			if ($logColor == "interest") echo '<img class="spreadlogo" src="uploads/experience-green.png">';
			else echo '<img class="spreadlogo" src="uploads/experience.png">';
			if ($logColor == "late check out") echo '<img class="spreadlogo" src="uploads/late-check-out-green.png">';
			else echo '<img class="spreadlogo" src="uploads/late-check-out.png">';
			if ($logColor == "long stay") echo '<img class="spreadlogo" src="uploads/long-stay-green.png">';
			else echo '<img class="spreadlogo" src="uploads/long-stay.png">';
			if (!empty($value['notes']) || $flag == 1) echo '<img class="spreadlogo" src="uploads/note-green.png">';
			else echo '<img class="spreadlogo" src="uploads/note.png">';
			/*if($logColor == "photo" )
			echo '<img class="spreadlogo" src="uploads/photo-green.png">';
			else
			echo '<img class="spreadlogo" src="uploads/photo.png">';*/
			if ($logColor == "short flipping time") echo '<img class="spreadlogo" src="uploads/short-flipping-time-green.png">';
			else echo '<img class="spreadlogo" src="uploads/short-flipping-time.png">';
			if ($logColor == "taxi") echo '<img class="spreadlogo" src="uploads/taxi-green.png">';
			else echo '<img class="spreadlogo" src="uploads/taxi.png">';
?>
                        	</td>
                         
				 
					<?php
			$class = 'class=""';
			$checked = '';
			if ($value['question_mark'] == 1)
			{

				// $style = 'style="background-color:green !important;"';

				$class = 'class="green_class"';
				$checked = ' checked="checked"';
			}

			echo '<td ' . $class . '  id="' . 'question_mark_' . $value['booking_number'] . '" >'; ?>
                   
                   <div  class="loader" ></div>
  						  <input type="checkbox"  class="question_mark" name="question_mark" <?php
			echo $checked; ?> value="1" onClick="return save_check_box('question_mark','<?php
			echo $value['booking_number']; ?>');" >
                    
                    <?php
			echo '</td>';
			$class1 = 'class=""';
			$checked1 = '';
			if ($value['exclamatio_mark'] == 1)
			{
				$class1 = 'class="green_class"';
				$checked1 = ' checked="checked"';
			}

			echo '<td ' . $class1 . '  id="' . 'exclamatio_mark_' . $value['booking_number'] . '" >';
?>
                    <div class="loader" ></div>
   						 <input type="checkbox"  class="exclamatio_mark" name="exclamatio_mark"  <?php
			echo $checked1; ?> value="1" onClick="return save_check_box('exclamatio_mark','<?php
			echo $value['booking_number']; ?>');" ></td>
                      <td ><i id="<?php
			echo $value['id'] ?>" class="button glyphicon glyphicon-arrow-down"></i></td>   
                    
                    <?php
			echo '</tr>';
			echo '<tr' . $tr_class . ' id="trValue' . $value['id'] . '" ><td colspan="13">', $value['guests_number'], ' guests - From  ', $value['country'], '  - Flight  ', $value['flight_number'], ' - <span', $td_maxNight, '>' . $value['nights'] . ' nights</span>  - ';
			echo $value['notes'];
			if ($value['notes'] != '')
			{
				echo ' ';
				echo '<br />';
			}

			if ($value["keybox"] == "NO")
			{
				if ($value["check_in_time"] == '21:00' or $value["check_in_time"] == '21:30' or $value["check_in_time"] == '22:00' or $value["check_in_time"] == '22:30')
				{ ?>
                                                    Guest will give 15€ to *<?php
					echo $check_in_person_name; ?>*. 
                                                    
                                                    
                                                    <?php
					echo '<br />';
				}
				else
				if ($value["check_in_time"] == '23:00' or $value["check_in_time"] == '23:30')
				{ ?>
                                                     Guest will give 30€ to *<?php
					echo $check_in_person_name; ?>*.
                                                    
                                                    
                                                    <?php
					echo '<br />';
				}
			}

			echo $value['note_manager'];
			$client_name_dr = explode(' ', $value['name']);
			if (($value["check_out_request"] == 'I need to request a late check out') || ($value["check_out_request"] == 'I need to request a late check out for 40e'))
			{
				echo $client_name_dr[0] . " will give 40€ to " . $check_in_person_name . " for late check out. ";
			}

			if ($value['mifi'] == 'yes')
			{
				echo $client_name_dr[0] . " will give " . ($value['nights'] * 5) . "€ to " . $check_in_person_name . " in exchange of the mifi dongle.";
			}

			if (strtoupper($value['mifi']) == 'YES')
			{
				echo '<span style="background-color:#58D3F7 !important">', $value['phone_number'], ' Phone number</span>';
			}
			else
			{
				echo '<span>', $value['phone_number'], '</span>';
			}

			echo '</td></tr>'; ?>
                
                
               
                <?php
		}

?>
        
        </tbody>
    </table>
    
    
    <?php
	}
} ?>
    
    
    </div>
</div>
<div class="hidden" id="img-repo">
	<?php echo $image_rows;?>
</div>
<!--modal dialog for gallery-->
<div class="modal" id="modal-gallery" role="dialog">
  <div class="modal-dialog" style="width: 356px !important;">
    <div class="modal-content">
      <div class="modal-header">
          <button class="close" type="button" data-dismiss="modal">×</button>
          <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body">
          <div id="modal-carousel" class="carousel">
   
            <div class="carousel-inner profile-pic">           
            </div>
            
           <div></div>
            
          </div>
      </div>
      
    </div>
  </div>
</div>

<!--end of modal dialog for gallery-->
<script>
	$(document).ready(function() {

  /* activate the carousel */
  $("#msodal-carousel").carousel({
    interval: false
  });

  /* change modal title when slide changes */
  $("#modal-carousel").on("slid.bs.carousel", function() {
    $(".modal-title")
      .html($(this)
        .find(".active img")
        .attr("title"));
  });

  /* when clicking a thumbnail */
  $(".row .thumbnail").click(function() {
    var content = $(".carousel-inner");
    var title = $(".modal-title");

    content.empty();
    title.empty();

    var id = this.id;
	
    var repo = $("#img-repo .item");
	
    var repoCopy = repo.filter("#" + id).clone();
	
    var active = repoCopy.first();

    active.addClass("active");
    title.html(active.find("img").attr("title"));
    content.append(repoCopy);

    // show the modal
    $("#modal-gallery").modal("show");
  });


});

</script>
<?php

if (isset($_POST['cat_id']) && !empty($_POST['cat_id']))
{ ?>
    	<script type="text/javascript">
        $(window).load(function(){
            get_property(<?php
	echo $_POST['cat_id']; ?>);
        
        });
    
    </script>
		<?php
} ?>
		<?php
$stmt1 = $db->query("select * from static_page where id='1' ");
$rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);

foreach($rows as $key => $instructions)
{
	$instruction = mysql_real_escape_string($instructions['check_in']);
	$instruction = rip_tags($instruction);
}

function rip_tags($string)
{

	// ----- remove HTML TAGs -----

	$string = preg_replace('/<[^>]*>/', ' ', $string);

	// ----- remove control characters -----
	// $string = str_replace('\r', '', $string);    // --- replace with empty space
	// $string = str_replace('\n', ' ', $string);   // --- replace with space
	// $string = str_replace('\t', ' ', $string);   // --- replace with space

	$string = str_replace('&nbsp;', ' ', $string); // --- replace with space

	// $string = stripslashes($string);;   // --- replace with empty space
	// ----- remove multiple spaces -----

	$string = trim(preg_replace('/ {2,}/', ' ', $string));
	return $string;
}

?>
        <script src="clipboard.min.js"></script>
<script>             
	
		
		var instruction = '<?php
echo addslashes($instruction); ?>';
		  var clipboard = new Clipboard('.copyInstruction', {
        text: function() {
            return instruction;
        }
    });

    clipboard.on('success', function(e) {
        console.log(e);
    });

    clipboard.on('error', function(e) {
        console.log(e);
    });
		
	

	function get_property(id) 
	{ <?php

if (isset($_POST['pname']) && !empty($_POST['pname']))
{
	if (intval($_POST['pname']) > 0)
	{ ?>	
				var property_id	= <?php
		echo $_POST['pname']; ?>;<?php
	}
	else
	{ ?>
					var property_id = "<?php
		echo $_POST['pname']; ?>";	<?php
	}
}
else
{ ?>
				var property_id = '';<?php
} ?>
		if(property_id!=''){
			var p_id = property_id;
		}else{
				var p_id = '';
			}
				
		if(id==''){	
			alert('Please select category.');
		}else{	
				$('#loading').html('<img width="32" hieght="32" src="../img/loader.gif" >');
				$.ajax({
						type	:	"GET",
						url		:	"../ajax-cat.php",
						data	:	'id='+id+'&property_id='+p_id,
						contentType	:	false,
						processData	:	false,
						success	: function(data){
													$('#loading').html('');
													$('#property').html(data);
												}
					});
			 }
	}
	
	
	function save_check_box(type,booking_number){
		
		if(type == 'question_mark'){
			
			if($('#'+type+'_'+booking_number+" .question_mark").is(':checked')){	
				var type_val = 1;	
				$('#'+type+'_'+booking_number).removeClass("white_class");
				$('#'+type+'_'+booking_number).addClass("green_class");
			}else{
				var type_val = 0;	 
				$('#'+type+'_'+booking_number).removeClass("green_class");
				$('#'+type+'_'+booking_number).addClass("white_class");
			}
		}else{
			
			if($('#'+type+'_'+booking_number+" .exclamatio_mark").is(':checked')){	
				var type_val = 1;	
				$('#'+type+'_'+booking_number).removeClass("white_class");
				$('#'+type+'_'+booking_number).addClass("green_class");
			}else{
				var type_val = 0; 
				$('#'+type+'_'+booking_number).removeClass("green_class");
				$('#'+type+'_'+booking_number).addClass("white_class");
			}
		}
		$('#'+type+'_'+booking_number+' .loader').html('<img   width="32" hieght="32"  src="../img/loader.gif" >');	
		$.ajax({
				type		: "GET",
				url			: "../ajax3.php",
				data		: 'booking_number='+booking_number+'&type_val='+type_val+'&type='+type,
				contentType	: false, 
				processData	:false, 
				success: function(data){
											$('#'+type+'_'+booking_number+' .loader').html('');

											// $('#property').html(data);

										}
		});
	}
	



$(document).ready(function(){
	$("[id^=trHead]").hide();
	$("[id^=trValue]").hide();
   	$(".button").click(function(){
	   var id = $(this).attr('id')
	    $("#trHead"+id ).toggle();
		$("#trValue"+id ).toggle();
   });
   
   $(document).delegate('[id^=checkindp_]','change',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');
		var value=$('#'+row_id).val();
		if (row_id!='')
		{
			row_id=$(this).attr('id');
			row_id=row_id.split("_");
			row_id=row_id[1];
			// alert (row_id);			
			// alert (value);
		}
		// return;
		/* $.post(
		"ajax_update_booking.php",
		{row_id:row_id,value:value,mode:'checkin'},
		function(data,status)
		{				
			
		});	 */
		$.post(
		"../ajax_detail.php",
		{pk:row_id,value:value,name:'check_in_person'},
		function(data,status)
		{				
			
		});		
	});
	
   
	$(document).delegate('[id^=cleaningdp_]','change',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');
		var value=$('#'+row_id).val();
		if (row_id!='')
		{
			row_id=$(this).attr('id');
			row_id=row_id.split("_");
			row_id=row_id[1];
			// alert (row_id);			
			// alert (value);
		}
		// return;
		$.post(
		"../ajax_detail.php",
		{pk:row_id,value:value,name:'cleaning_person'},
		function(data,status)
		{				
			
		});	
		/* $.post(
		"ajax_update_booking.php",
		{row_id:row_id,value:value,mode:'cleaning'},
		function(data,status)
		{				
			
		});	 */	
	});
	
	
   $(document).delegate('[id^=checkintimedp_]','changeTime',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');
		var value=$('#'+row_id).val();
		if (row_id!='')
		{
			row_id=$(this).attr('id');
			row_id=row_id.split("_");
			row_id=row_id[1];
			// alert (row_id);			
			// alert (value);
		}
		// return;
		$.post(
		"ajax_update_booking_time.php",
		{row_id:row_id,value:value,mode:'checkin'},
		function(data,status)
		{				
			
		});		
	});
	
   
	$(document).delegate('[id^=checkouttimedp_]','changeTime',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');
		var value=$('#'+row_id).val();
		if (row_id!='')
		{
			row_id=$(this).attr('id');
			row_id=row_id.split("_");
			row_id=row_id[1];
			// alert (row_id);			
			// alert (value);
		}
		// return;
		$.post(
		"ajax_update_booking_time.php",
		{row_id:row_id,value:value,mode:'checkout'},
		function(data,status)
		{				
			
		});		
	});
	
	
	// checkintimedp_
	$('input[id^=checkintimedp_]').timepicker({ 'step': 15,'timeFormat': 'G:i'});
	$('input[id^=checkouttimedp_]').timepicker({ 'step': 15,'timeFormat': 'G:i'});
	
   
   
});


</script>


</body>

</html>
<?php
function get_dropdown_for_employee ($employees_array,$name,$booking_number,$suffix)
{
	$result="<select id='" . $suffix . "_" . $booking_number . "'>";
	if ($name!='')
		$result .="<option value=''></option>";
	else
		$result .="<option selected value=''></option>";
	
	foreach ($employees_array as $id=>$value)
	{
		$selected="";
		if ($value['name']==$name)
		{
			$selected = " selected ";
		}
		
		$result .="<option $selected value='" . $id . "'>" . $value['name'] . "</option>";
	}
	
	$result .="</select>";	
	
	return $result;
}