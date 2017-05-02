<?php
require_once ('../connect.php');
require_once ('../functions_general.php');
$db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
session_start();
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
		<style>
            .green_td {  background-color: #acfa58 !important;  width: 1%;}
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
            .checkin_agian td{background-color: #FF8000 !important;}
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
        </style>
    </head>
	<body>
<div class="container-fluid table-responsive">
    <div class="row">
 <?php
if (!isset($_SESSION['admin']))
{ ?>
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
    </div>   <?php
}
else
{ ?>
    <!--menu bar-->     
    <?php
	require_once ('admin_menu.php') ?>
    <!--end of menu bar-->
    <?php
	$stmt = $db->query("select * from properties  where active_status = 'YES' and id!=3");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// echo "<pre>"; print_r($rows);
	if (!empty($rows))
	{
?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Property Name</th>
               	<th>Last Cleaner</th>
                <th>Clean Status</th>
             </tr>
        </thead>
    <tbody>
    <?php
		
			
		foreach($rows as $key => $value)
		{
			$db->query("UPDATE properties p
								LEFT JOIN gcal_imports g ON p.id=g.property_id
								SET cleaning_status = 'Dirty',last_cleaner=''
								where p.cleaning_status='Occupied'
								AND p.id ='" . $value['id'] . "'
								AND p.clean_date < g.check_in_new
								AND CURDATE() >= g.check_out_new");
			/*
			$res = $db->query("UPDATE properties p 
									LEFT JOIN gcal_imports g ON g.property_id = p.id 
									SET cleaning_status = 'Occupied',last_cleaner='' 
									WHERE p.id = '" . $value['id'] . "'
									AND CURDATE() BETWEEN  DATE(DATE_ADD(g.check_in_new, INTERVAL 1 DAY)) AND DATE(DATE_ADD(g.check_out_new, INTERVAL -1 DAY))");
			if ($res->num_rows > 0)
			{
			}
			else
			{
				$db->query("UPDATE properties p 
											LEFT JOIN gcal_imports g ON g.property_id = p.id 
											SET cleaning_status = 'Occupied',last_cleaner='' 
											WHERE p.id = '" . $value['id'] . "' AND g.check_out_new = DATE(DATE_ADD(g.check_in_new, INTERVAL 1 DAY))
											AND CURDATE() BETWEEN  g.check_in_new AND g.check_out_new");
			}
			*/
			
			echo "<tr> <td>" . $value['name'] . "</td><td>" . $value['last_cleaner'] . "</td>";
			
			//current-booking=check_in_date <=today's date and check_out_date >=today's date
			//previous booking check_out_date < today's date
			//next booking	check_in_date > today's date
			
			$info="";
			$today="'" . date ('Y-m-d') . "'";
			// echo $today;
			if ($value['cleaning_status']=="Occupied")
			{
				$sql="select booking_number,check_out_new, check_out_time from gcal_imports   
				where property_id=" . $value['id'] . " and check_out_new >= $today AND check_in_new <=$today order by check_out_new desc  limit 0,1";
				// echo $sql . '<br />';
				$info_stmt = $db->query($sql);
				$info_rows = $info_stmt->fetch(PDO::FETCH_ASSOC);
				// echo '<pre>' . print_r ($info_rows,true) . '</pre>';		
				if (count($info_rows))
				{
					if ($info_rows['check_out_new']!='')
					{
						$info .=" until " . date ('l d M',strtotime ($info_rows['check_out_new'])) . " at ";
						
						if ($info_rows['check_out_time']!='' or !is_null ($info_rows['check_out_time']))
						{
							$info.= date ('G:s',strtotime ($info_rows['check_out_time']));
						}
						
						$info .= " (" . $info_rows['booking_number'] . ")";
					}
					
				}
			}
			
			
			
			if (strtolower ($value['cleaning_status'])=="dirty")
			{
				$sql="select g.booking_number, e.name, check_out_new, check_out_time,cleaning_person from gcal_imports g
				INNER JOIN employee e ON  g.cleaning_person=e.id  
				where property_id=" . $value['id'] . " and check_out_new < $today order by check_out_new desc limit 0,1";
				// echo $sql;
				$info_stmt = $db->query($sql);
				$info_rows = $info_stmt->fetch(PDO::FETCH_ASSOC);
				
				$last_cleaning_person="";
				
				if (count($info_rows))
				{
					if ($info_rows['check_out_new']!='')
					{
						$info .=" - last check out was on " . date ('l d M',strtotime ($info_rows['check_out_new'])) . " at ";
						
						if ($info_rows['check_out_time']!='' or !is_null ($info_rows['check_out_time']))
						{
							$info .= date ('G:s',strtotime ($info_rows['check_out_time']));  
						}
						
						$info .= " (" . $info_rows['booking_number'] . ") ";
						
						$last_cleaning_person=$info_rows['name'];
						
						$last_booking_no=$info_rows['booking_number'];
						
					}
					
				}
				
				$sql="select g.booking_number, check_in_new, check_in_time from gcal_imports g				
				where property_id=" . $value['id'] . " and check_in_new >= $today order by check_out_new asc limit 0,1";
				
				$info_stmt = $db->query($sql);
				$info_rows = $info_stmt->fetch(PDO::FETCH_ASSOC);
				
				if (count($info_rows))
				{
					$info .= " - Clean before "  . date ('l d M',strtotime ($info_rows['check_in_new'])) . " at ";
					
					if ($info_rows['check_in_time']!='' or !is_null ($info_rows['check_in_time']))
					{
						$info .= date ('G:s',strtotime ($info_rows['check_in_time']));
					}
					
					
					$info .=  " (" . $info_rows['booking_number'] . ")";
					
					$info .= " - Cleaning attributed to " . $last_cleaning_person  . " (" . $last_booking_no . ")";
				}
				
			}
			
			if (strtolower ($value['cleaning_status'])=="cleaned")
			{
				$sql="select g.booking_number, p.last_cleaner,e.name from properties p 
				INNER JOIN gcal_imports g ON g.property_id=p.id
				INNER JOIN employee e ON g.cleaning_person=e.id
				where property_id=" . $value['id'] . " and check_out_new < $today order by check_out_new desc limit 0,1";
				// echo $sql . '<br />';
				$info_stmt = $db->query($sql);
				$info_rows = $info_stmt->fetch(PDO::FETCH_ASSOC);
				// echo '<pre>' . print_r ($info_rows,true) . '</pre>';		
				if (count($info_rows))
				{
					$info .=" - Cleaning logged by " . $last_cleaning_person . " - Cleaning will be paid to "  . $info_rows['name']  . " (" . $info_rows['booking_number'] . ")";					
				}
			}
			
			
			
			echo "<td>" . $value['cleaning_status'] . $info . "</td></tr>";
		}
?>
        </tbody>
    </table>
    <?php
	}
} ?>
    </div>
</div>
<?php
if (isset($_POST['cat_id']) && !empty($_POST['cat_id']))
{
}
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
	$string = str_replace('&nbsp;', ' ', $string); // --- replace with space	
	// ----- remove multiple spaces -----
	$string = trim(preg_replace('/ {2,}/', ' ', $string));
	return $string;
}
?>
        <script src="clipboard.min.js"></script>
</body>
</html>