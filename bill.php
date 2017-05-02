<?php
require_once('../connect.php');
require_once('../functions_general.php');
session_start();
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
$db->exec("SET NAMES 'utf8'");
if(!isset($_SESSION['admin'])) 
{
		header('location: index.php');
}
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$mysqli->set_charset("utf8");
if($_POST){ 
$csv_file = $_FILES['csvimport']['tmp_name']; // Name of your CSV file
$csvfile = fopen($csv_file, 'r');
$theData = fgets($csvfile);
$allBookingNumber = array();
$i = 0;
$text = '';
while (!feof($csvfile)) { 
$csv_array = fgetcsv($csvfile, 1024,",");
//$csv_array = explode(",", $csv_data[$i]);
$text = $csv_array[1];
$booking_number ='';
$amount ='';
$booking_number = $csv_array[2];
$amount = $csv_array[10];
if(!empty($booking_number) && !empty($amount)) {
	
    if( strpos($text,'co-hôtes')  == false){
	
		if (in_array($booking_number, $allBookingNumber)) {
			$mysqli->query("Update gcal_imports set earning= earning+".$amount." where booking_number='".$booking_number."' ");
		} else {
			$mysqli->query("Update gcal_imports set earning=".$amount." where booking_number='".$booking_number."' ");
		}
    }  
$allBookingNumber[] = $booking_number;
}
$i++;
}
fclose($csvfile);
}
$month_where = '';
$where = ' and booking_status != "cancel" ';
$month_and_year = '';
$c_monthYear = date('F-Y');
if(isset($_GET['month_and_year']) && $_GET['month_and_year']!='') {
	$month_and_year =  $_GET['month_and_year'];
    
	if(empty($month_and_year)){	
     $month_and_year = $c_monthYear;
	}
	$month_where .=" and DATE_FORMAT(check_in_new,'%M-%Y') = '".$month_and_year."' ";
}
if(empty($_GET['month_and_year'])){	
     $month_and_year = $c_monthYear;
	 $month_where .=" and DATE_FORMAT(check_in_new,'%M-%Y') = '".$month_and_year."' ";
	}
	
if(isset($_GET['pname'])){
	if($_GET['pname']!='')
	{
		$property = security($_GET['pname']);
		$where .=" and property_id = ".$property;
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
<form class="form-horizontal" action="" method="get" style="float:left; width:50%;" >
    &nbsp;&nbsp;
	<select name="pname"  >
		<option value="">Please Select Property</option>
		<?php
		$stmt1 	= $mysqli->query("select id,name from properties where active_status = 'YES' order by name asc");
		if($stmt1->num_rows > 0) { 
		while($rows2 = $stmt1->fetch_assoc())
		{ ?>
		<option value="<?php echo $rows2['id']?>" <?php if($rows2['id']==$property){ echo "selected='selected'";} ?>><?php echo $rows2['name']?></option>
		<?php }
		}
		?>
	</select>
	
	<select name="month_and_year"  >
		<option value="">Please Select Time</option>
        
        <?php 
		$monthYears = array('January-2014',
		                   'February-2014' ,
						   'March-2014' ,
						   'April-2014',
						   'May-2014',
						   'June-2014',
						   'July-2014',
						   'August-2014',
						   'September-2014',
						   'October-2014',
						   'December-2014',
						   
						   'January-2015',
						   'February-2015',
						   'March-2015',
						   'April-2015',
						   'May-2015',
						   'June-2015',
						   'July-2015',
						   'August-2015',
						   'September-2015',
						   'October-2015',
						   'November-2015',
						   'December-2015',
						   
						   'January-2016',
						   'February-2016',
						   'March-2016',
						   'April-2016',
						   'May-2016',
						   'June-2016',
						   'July-2016',
						   'August-2016',
						   'September-2016',
						   'October-2016',
						   'November-2016',
						   'December-2016',
						   
						   'January-2017',
						   'February-2017',
						   'March-2017',
						   'April-2017',
						   'May-2017',
						   'June-2017',
						   'July-2017',
						   'August-2017',
						   'September-2017',
						   'October-2017',
						   'November-2017',
						   'December-2017',						   				   
						   );
		     foreach($monthYears as  $monthYear){
		 $selected = '';
		 if(!empty($month_and_year)){
			 if($month_and_year == $monthYear){
				$selected =  "selected='selected'";
			 }
		 }
		 else{
			
			if($monthYear == $c_monthYear ){
				$selected =  "selected='selected'";
			 } 
		}
		 
		 
		 ?>
		<option value="<?php echo $monthYear ?>" <?php  echo $selected ; ?>>
		<?php echo str_replace('-',' ',$monthYear);?>
        </option>
        <?php 
			 }
		?>
	<!--<option value="January-2014" <?php if($month_and_year == 'January-2014'){ echo "selected='selected'";} ?>>January 2014</option>
	<option value="February-2014" <?php if($month_and_year == 'February-2014'){ echo "selected='selected'";} ?>>February 2014</option>
	<option value="March-2014" <?php if($month_and_year == 'March-2014'){ echo "selected='selected'";} ?>>March 2014</option>
	<option value="April-2014" <?php if($month_and_year == 'April-2014'){ echo "selected='selected'";} ?>>April 2014</option>
	<option value="May-2014" <?php if($month_and_year == 'May-2014'){ echo "selected='selected'";} ?>>May 2014</option>
	<option value="June-2014" <?php if($month_and_year == 'June-2014'){ echo "selected='selected'";} ?>>June 2014</option>
	<option value="July-2014" <?php if($month_and_year == 'July-2014'){ echo "selected='selected'";} ?>>July 2014</option>
	<option value="August-2014" <?php if($month_and_year == 'August-2014'){ echo "selected='selected'";} ?>>August 2014</option>
	<option value="September-2014" <?php if($month_and_year == 'September-2014'){ echo "selected='selected'";} ?>>September 2014</option>
	<option value="October-2014" <?php if($month_and_year == 'October-2014'){ echo "selected='selected'";} ?>>October 2014</option>
	<option value="November-2014" <?php if($month_and_year == 'November-2014'){ echo "selected='selected'";} ?>>November 2014</option>
	<option value="December-2014" <?php if($month_and_year == 'December-2014'){ echo "selected='selected'";} ?>>December 2014</option>
	
	<option value="January-2015" <?php if($month_and_year == 'January-2015'){ echo "selected='selected'";} ?>>January 2015</option>
	<option value="February-2015" <?php if($month_and_year == 'February-2015'){ echo "selected='selected'";} ?>>February 2015</option>
	<option value="March-2015" <?php if($month_and_year == 'March-2015'){ echo "selected='selected'";} ?>>March 2015</option>
	<option value="April-2015" <?php if($month_and_year == 'April-2015'){ echo "selected='selected'";} ?>>April 2015</option>
	<option value="May-2015" <?php if($month_and_year == 'May-2015'){ echo "selected='selected'";} ?>>May 2015</option>
	<option value="June-2015" <?php if($month_and_year == 'June-2015'){ echo "selected='selected'";} ?>>June 2015</option>
	<option value="July-2015" <?php if($month_and_year == 'July-2015'){ echo "selected='selected'";} ?>>July 2015</option>
	<option value="August-2015" <?php if($month_and_year == 'August-2015'){ echo "selected='selected'";} ?>>August 2015</option>
	<option value="September-2015" <?php if($month_and_year == 'September-2015'){ echo "selected='selected'";} ?>>September 2015</option>
	<option value="October-2015" <?php if($month_and_year == 'October-2015'){ echo "selected='selected'";} ?>>October 2015</option>
	<option value="November-2015" <?php if($month_and_year == 'November-2015'){ echo "selected='selected'";} ?>>November 2015</option>
	<option value="December-2015" <?php if($month_and_year == 'December-2015'){ echo "selected='selected'";} ?>>December 2015</option>
	
	<option value="January-2016" <?php if($month_and_year == 'January-2016'){ echo "selected='selected'";} ?>>January 2016</option>
	<option value="February-2016" <?php if($month_and_year == 'February-2016'){ echo "selected='selected'";} ?>>February 2016</option>
	<option value="March-2016" <?php if($month_and_year == 'March-2016'){ echo "selected='selected'";} ?>>March 2016</option>
	<option value="April-2016" <?php if($month_and_year == 'April-2016'){ echo "selected='selected'";} ?>>April 2016</option>
	<option value="May-2016" <?php if($month_and_year == 'May-2016'){ echo "selected='selected'";} ?>>May 2016</option>
	<option value="June-2016" <?php if($month_and_year == 'June-2016'){ echo "selected='selected'";} ?>>June 2016</option>
	<option value="July-2016" <?php if($month_and_year == 'July-2016'){ echo "selected='selected'";} ?>>July 2016</option>
	<option value="August-2016" <?php if($month_and_year == 'August-2016'){ echo "selected='selected'";} ?>>August 2016</option>
	<option value="September-2016" <?php if($month_and_year == 'September-2016'){ echo "selected='selected'";} ?>>September 2016</option>
	<option value="October-2016" <?php if($month_and_year == 'October-2016'){ echo "selected='selected'";} ?>>October 2016</option>
	<option value="November-2016" <?php if($month_and_year == 'November-2016'){ echo "selected='selected'";} ?>>November 2016</option>
	<option value="December-2016" <?php if($month_and_year == 'December-2016'){ echo "selected='selected'";} ?>>December 2016</option>-->
		
	</select>
	
	
    <input type="submit" value="submit" />
</form>



<form style="float:right;" enctype="multipart/form-data"  class="form-horizontal" action="" method="POST">
    &nbsp;&nbsp;
	
	<h5 style="float:left; font-weight:bold;"  >Import CSV</h5>
	<input style="float:left; margin-top:5px;" type="file" name="csvimport" />
    <input style="float:left; margin-top:5px;" type="submit" value="submit" name="Submit" />
	
</form>



            <br>



<?php 
//echo "Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id ,nights From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new ";
//exit;
$fetchData = $mysqli->query("Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id ,nights From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new ");
/*for total , no 76,77 {START} */
$total_count = 0;
$total_payout_count = 0;
$fetchTotalCount = $mysqli->query("Select SUM(earning) as total_sum, SUM(earning * 0.2) as total_pay_sum  From gcal_imports where 1  $where $month_where ");
if($fetchTotalCount->num_rows > 0) {
$rowTotalCount = $fetchTotalCount->fetch_assoc(); 
$total_count = $rowTotalCount['total_sum'];
$total_payout_count = $rowTotalCount['total_pay_sum'];
}
/*for total , no 76,77 {END} */
	if($fetchData->num_rows > 0) {
			?>



    <table class='table table-striped' >



      <tr>



        <th colspan="8" width="100%"> <table class="header" width="100%">



            <tr>
				
			 
			 
			 <td colspan="2" width="30%"><?php echo @$_GET['e'] ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Total Earning= <?php echo $total_count; ?></td>
			 <td colspan="2" width="30%"><?php echo @$_GET['e'] ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Total Payout= <div id="put_here" style="display:inline-block;"></div></td>
			 


            </tr>



          </table>



        </th>



      </tr>



      <tr>


        <th  >Name</th>
        <th style="width:100px;" >Booking Number</th>


        <th style="width:150px;">Check in data</th>
        <th style="width:150px;" >Check out data</th>


        <th>Earning</th>


        <th>Management charges</th>
       <!-- <th>Management fee %</th>-->
        <th>check in and cleaning fees</th>
        <th>price per night</th>


      </tr>

	  
	  


      <?php 
	  
		$total_m_charge = 0;
		$total_earning = 0;
		$total_payout = 0;
		$total_check_in_and_cleaning_fees = 0;
		$total_days=0;
//$row = $fetchData->fetch_assoc();
	   while($row = $fetchData->fetch_assoc())
	   {
		$earning = intval($row['earning']);
		
		$pro_fees 	= $mysqli->query("select id,name,management_fee from properties where id='".$row['property_id']."' ");
		$pro_management_fee = 0.2;
		if($pro_fees->num_rows > 0) { 
		   $pro_fees_row = $pro_fees->fetch_assoc();
		   $pro_management_fee = ($pro_fees_row['management_fee']/100);
		}
		
		
		
		$payout = ($earning * $pro_management_fee);
		
		$check_in_and_cleaning_fees = -1 * (intval($row['tamount']));
		if($check_in_and_cleaning_fees < 1) {
			$check_in_and_cleaning_fees = 0;
		}
		
		$total_check_in_and_cleaning_fees = $total_check_in_and_cleaning_fees + $check_in_and_cleaning_fees;
		$total_m_charge = $total_m_charge + $payout;
		$total_earning = $total_earning + $earning;
		$total_payout = $total_payout + ($payout + $check_in_and_cleaning_fees);
		
		/* code added 27-4-2016 */
			$number_of_night = $row['nights']; /* _days($row['check_out_new'],$row['check_in_new']); */
			if($number_of_night!=0){
				$price_per_night	=	($earning/$number_of_night);	
			}else{
				$price_per_night	=	0;
			}
			$total_days=$total_days+$number_of_night;
		/* code added 27-4-2016 */
		
	   ?>



      <tr>



        <td><?php echo $row['name'];?></td>


        <td><?php echo $row['booking_number'] ?> </td>
        <td><?php echo $row['check_in'] ?></td>
        <td><?php echo $row['check_out'] ?></td>
        <td><?php echo $earning; ?> </td>
        <td><?php echo $payout; ?></td>
       <!-- <td><?php echo $pro_fees_row['management_fee'] ?></td>-->
        <td><?php echo $check_in_and_cleaning_fees; ?></td>
        <td><?php echo number_format($price_per_night,2); ?></td>




      </tr>



      <?php 
	   }
		?>
<tr>
			 <td colspan="4" ><?php echo @$_GET['e'] ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Total Earning= <?php echo $total_count; ?></td>
			
			 
			 <td><b><?php echo $total_earning; ?></b></td>
			 <td><b><?php echo $total_m_charge; ?></b></td>
             <!-- <td></td>-->
			 <td><b><?php echo $total_check_in_and_cleaning_fees; ?></b></td>
			 <td><b><?php 
			 $total_price_per_night=0;
			 if($total_days!=0){
				$total_price_per_night	= ($total_earning/$total_days);
			 }
			 echo number_format($total_price_per_night,2); ?></b></td>
			 
            </tr>


      



    </table>



    <?php 
	} else {
		echo "No Records found ";
	}
?>



  </div>



</div>

<script>
$(document).ready(function() {
	$('#put_here').html(<?php echo $total_payout; ?>);
});
</script>







</body>



</html>

<?php 
	/* function _days($date1,$date2){
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);
		$diff = $date2->diff($date1)->format("%a");
		return $diff;
	} */
?>