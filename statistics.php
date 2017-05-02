<?php
require_once ('../connect.php');
require_once ('../functions_general.php');
session_start();
$mysqli = new mysqli($db_host_connect, $db_user_connect, $db_pass_connect, $db_name_connect);

if (!isset($_SESSION['admin']))
{
	header('location: index.php');
	exit;
}

if (isset($_GET['pname']))
{
	if ($_GET['pname'] != '')
	{
		$property = security($_GET['pname']);		
	}
}
else
	$property="";

//0 for including current month
for ($i = 0; $i < 15; $i++) {
    // $months[] = date("M-Y", strtotime( date( 'Y-m-01' )." -$i months"));
    $months[] = date("F-Y", strtotime( date( 'Y-m-01' )." -$i months"));
}

if ($property!='')
{
	
	$data=array();
	$i=0;
	
	// print_r ($months);
	foreach ($months as $month)
	{
		 
		$data[$i]['month']=date ("M-Y",strtotime ($month));
		// echo $month . ' ';
		// First day of the month.
		// echo date('Y-m-01', strtotime($month)) . ' ';
		$first=date('Y-m-01', strtotime($month));
		// Last day of the month.
		// echo date('Y-m-t', strtotime($month)) . '<br />';  
		$last=date('Y-m-t', strtotime($month));
		
		$where="where 1 and booking_status != 'cancel' and property_id = " . $property . " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $month . "' ";
		/*
		$sql="select sum(earning) as total_earning,sum(nights) as total_nights, sum(amount) as tamount, SUM(gi.earning*p.management_fee/100) as total_payout  From gcal_imports as gi 
		INNER JOIN properties p ON gi.property_id=p.id
		LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid')
		where 1 and booking_status != 'cancel' and property_id = " . $property . " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $month . "' ";
		*/
				
		$sql="select sum(amount) as tamount From gcal_imports as gi 
		
		LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid')
		" . $where;
		
		// echo $sql . '<br />';exit;
		$stmt = $mysqli->query($sql);
		if ($stmt->num_rows > 0)
		{
			$row = $stmt->fetch_assoc();			
			
			$check_in_and_cleaning_fees = -1 * (intval($row['tamount']));
			if($check_in_and_cleaning_fees < 1) 
			{
				$check_in_and_cleaning_fees = 0;
			}
			
			$data[$i]['check_in_and_cleaning_fees']=number_format($check_in_and_cleaning_fees,2);
			
			$sql="select 
				sum(earning) as total_earning,
				sum(nights) as total_nights, 
				SUM(gi.earning*p.management_fee/100) as total_payout  
				From gcal_imports as gi 
				INNER JOIN properties p ON gi.property_id=p.id
				" . $where;
			$stmt = $mysqli->query($sql);
			$row = $stmt->fetch_assoc();
			
			$data[$i]['total_earning']=intval($row['total_earning']);
			$data[$i]['total_nights']=$row['total_nights'];
			$data[$i]['average_price_per_night']=number_format($row['total_earning']/$row['total_nights'],2);
			$data[$i]['management_fee']=$row['total_payout'];
			$total_payout=$row['total_payout']+$check_in_and_cleaning_fees;
			$data[$i]['total_payout']=number_format($total_payout,2);	
			
			
			
			$fetchTotalCountp1 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports   $where ");
			
			// echo "Select SUM(earning) as total_sum  From gcal_imports  where 1  $where ";exit;
			
			$booking_number1 = $mysqli->query("Select booking_number From gcal_imports   $where ");
			$reservation_no1 =	'';
			if($booking_number1->num_rows > 0) {
				while($booking_number1_data = $booking_number1->fetch_assoc())
				{
					 $reservation_no1	.=	"'".$booking_number1_data['booking_number']."'".',';
				}
			}
			if($reservation_no1!=''){
				$reservation_no1=rtrim($reservation_no1,',');
			}
			
			$pmonth_first_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no1) and amount < 0 ) or (reservation_no = '' and DATE_FORMAT(req_date,'%M-%Y') = '" . $month . "'  and amount < 0 and property=$property )) ";
			
			// echo $pmonth_first_sql;exit;
			$pmonth_first_ck_cl_query = $mysqli->query($pmonth_first_sql);
		
			$pmonth_first_ck_clt = 0;
			if($pmonth_first_ck_cl_query->num_rows > 0) {
				while($pmonth_where_first_row = $pmonth_first_ck_cl_query->fetch_assoc())
				{
					 $pmonth_first_ck_clt =	$pmonth_where_first_row['tamount'];
				}
			}
			
			
			$total_countp1 = $data[$i]['total_earning'];
			$management_feep1 = $total_countp1 - ( $data[$i]['total_payout']+ (-1 *$pmonth_first_ck_clt));
			
			$data[$i]['net_income']=$management_feep1+$check_in_and_cleaning_fees;
			
			$data[$i]['others']=$data[$i]['total_earning']-($data[$i]['net_income']+$data[$i]['total_payout']);
			
			$management_feep1=0;
			
			
			//make minus values zero
			if ($data[$i]['others']<0)
				$data[$i]['others']=0;			
			if ($data[$i]['net_income']<0)
				$data[$i]['net_income']=0;
			if ($data[$i]['check_in_and_cleaning_fees']<0)
				$data[$i]['check_in_and_cleaning_fees']=0;
			if ($data[$i]['management_fee']<0)
				$data[$i]['management_fee']=0;
			
				
		}
		
		
		
		
		$i++;
		
	}
	
	//reverse array
	$data2=array_reverse($data,true);
	// echo '<pre>' . print_r ($data,true) . '</pre>';exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
function drawCharts() 
{
	
		//draw line chart above	
	/*
	var data = new google.visualization.DataTable();
      data.addColumn('string', 'Month');
      data.addColumn('number', 'Average Price per Night per Month');
      data.addColumn('number', 'Number of Nights rented on month');
      data.addColumn('number', 'Net Income per Month');
	  
	
	  data.addRows([
	 */
		
		var data = google.visualization.arrayToDataTable([
        ['Month', 'Net Income per Month', 'Average Price per Night per Month', 'Number of Nights rented on month'],
		
	  <?php
		$i = 0;
		$len = count($data2);
		$max_value=0;
	  ?>
	  <?php foreach ($data2 as $item):?>
	  <?php 
		$max_value=($max_value<$item['net_income']) ? $item['net_income'] : $max_value;
	  ?>
	  
	  ['<?php echo $item['month']?>',  <?php echo $item['net_income']?>,      
	  <?php echo $item['average_price_per_night']?>,<?php echo $item['total_nights']?>]
	  <?php
	  if ($i == $len - 1)
	  {
		  //last element do not put comma
	  }
	  else
	  {
		  echo ',';
	  }
	  $i++
	  ?>
	  <?php endforeach?>
	]);
	
	 /*To specify a chart with multiple vertical axes, first define a new axis using series.targetAxisIndex, then configure the axis using vAxes. The following example assigns series 2 to the right axis and specifies a custom title and text style for it:*/
	 
	var options = {
	  title: 'Last 15 months data',
	  curveType: 'function',
	  subtitle: 'prices in dollars (USD)',	  	   
	  legend: { position: 'top' },
	  series: {
				0:{targetAxisIndex:0,pointShape: 'triangle'},
                1:{targetAxisIndex:1,pointShape: 'square'},
                // 2:{targetAxisIndex:2,pointShape: 'circle',lineWidth: 3,lineDashStyle: [14, 2, 7, 2]},                
                2:{targetAxisIndex:2,pointShape: 'circle'},                
            },
	  hAxis: {
			showTextEvery: 2			
	  },	  
      vAxes: {0: {viewWindowMode:'explicit',
                      viewWindow:{
                                  max:<?php echo $max_value*1.3?>,
                                  min:0
                                  },
                      gridlines: {color: 'transparent'},					  
					  title: 'Net Income',
					  textPosition:'in',
					  textStyle:{ color: '##3366CC',
								  fontName: 'Arial',
								  fontSize: '12',
								  bold: true,
								  italic: true
								},
						
                  },
              1: {
						viewWindow:{
                                  max:200,
                                  min:0
                                  },
					  gridlines: {color: 'transparent'},					  
					  title:'Average Price per Night',
					  textPosition:'in',
					  position:{left:30}
                      },
			  2: {
						viewWindow:{
                                  max:50,
                                  min:0
                                  },
					  // gridlines: {color: '#333', count: 4},
					  gridlines: {color: 'transparent'},
					  // title:'Nights rented',
					  titleTextStyle:{ color: 'red',
								  fontName: 'Arial',
								  fontSize: '12',
								  bold: true,
								  italic: true
								},
					  textPosition:'in',
					  textStyle:{ color: '#FF9900',
								  fontName: 'Arial',
								  fontSize: '12',
								  bold: true,
								  italic: true
								}
					  
					  
                      },
                  },
			  
	
	 chartArea:{left:50,top:50, width:900, height:350},
	  pointSize: 5,
	
	  
	  
	};
	
	var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));    
	// var chart = new google.charts.Line(document.getElementById('curve_chart'));
	chart.draw(data, options);
	
	
	//draw pie chart for current month
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Month');
	data.addColumn('number', 'Amount');
	data.addRows([
	  ['Net Income', <?php echo $data[0]['net_income']?>],
	  ['Cleaning + check in fees',<?php echo $data[0]['check_in_and_cleaning_fees']?> ],
	  ['Management fees', <?php echo $data[0]['management_fee']?>],          
	  ['Others', <?php echo $data[0]['others']?>]
	]);

	var piechart_options = {title:'Current Month',
				   width:700,
				   height:200};
	var piechart = new google.visualization.PieChart(document.getElementById('current_month'));
	piechart.draw(data, piechart_options);
	
	
	//draw 1st pie chart for last four months
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Month');
	data.addColumn('number', 'Amount');
	data.addRows([
	  ['Net Income', <?php echo $data[1]['net_income']?>],
	  ['Cleaning + check in fees',<?php echo $data[1]['check_in_and_cleaning_fees']?> ],
	  ['Management fees', <?php echo $data[1]['management_fee']?>],          
	  ['Others', <?php echo $data[1]['others']?>]
	]);

	var piechart_options = {
				   legend: {position: 'none'},
				   width:100,
				   height:100};
	var piechart = new google.visualization.PieChart(document.getElementById('pie_1'));
	piechart.draw(data, piechart_options);
	
	//draw 2nd pie chart for last four months
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Month');
	data.addColumn('number', 'Amount');
	data.addRows([
	  ['Net Income', <?php echo $data[2]['net_income']?>],
	  ['Cleaning + check in fees',<?php echo $data[2]['check_in_and_cleaning_fees']?> ],
	  ['Management fees', <?php echo $data[2]['management_fee']?>],          
	  ['Others', <?php echo $data[2]['others']?>]
	]);

	var piechart_options = {
				   legend: {position: 'none'},
				   width:100,
				   height:100};
	var piechart = new google.visualization.PieChart(document.getElementById('pie_2'));
	piechart.draw(data, piechart_options);
	
	
	//draw 3rd pie chart for last four months
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Month');
	data.addColumn('number', 'Amount');
	data.addRows([
	  ['Net Income', <?php echo $data[3]['net_income']?>],
	  ['Cleaning + check in fees',<?php echo $data[3]['check_in_and_cleaning_fees']?> ],
	  ['Management fees', <?php echo $data[3]['management_fee']?>],          
	  ['Others', <?php echo $data[3]['others']?>]
	]);

	var piechart_options = {
				   legend: {position: 'none'},
				   width:100,
				   height:100};
	var piechart = new google.visualization.PieChart(document.getElementById('pie_3'));
	piechart.draw(data, piechart_options);
 
	//draw 4th pie chart for last four months
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Month');
	data.addColumn('number', 'Amount');
	data.addRows([
	  ['Net Income', <?php echo $data[4]['net_income']?>],
	  ['Cleaning + check in fees',<?php echo $data[4]['check_in_and_cleaning_fees']?> ],
	  ['Management fees', <?php echo $data[4]['management_fee']?>],          
	  ['Others', <?php echo $data[4]['others']?>]
	]);

	var piechart_options = {
				   legend: {position: 'none'},
				   width:100,
				   height:100};
	var piechart = new google.visualization.PieChart(document.getElementById('pie_4'));
	piechart.draw(data, piechart_options);
}

</script>
<script type="text/javascript">
google.charts.load('current', {'packages':['line','corechart']});
// google.charts.load("visualization", "1", {packages:["corechart"]});
// google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);


</script>


<style>
.green_td 
{
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
.table tr th, .table tr td {
	border-right: 1px solid #ccc
}
</style>
</head>
<body>
<div class="container-fluid table-responsive">
  <div class="row">
  
<!--menu bar-->     
<?php
require_once ('admin_menu.php') ?>
<!--end of menu bar-->
<h1>Statistics</h1>
<form class="form-horizontal" action="" method="get" style="float:left; width:50%;" >
    &nbsp;&nbsp;
	<select name="pname"  >
		<option value="">Please Select Property</option>
		<?php
$stmt1 = $mysqli->query("select id,name from properties where active_status = 'YES' order by name asc");
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
    <input type="submit" value="submit" />
</form>

<br /><br />

<div style="margin-left:50px;margin-bottom:100px;width:100%;min-height:500px;" id="curve_chart">
</div>

<div class="row">
	
	<div class="col-md-8" id="current_month">
	
	</div>
	<div class="col-md-4">
		<p>Last four months</p>
		<div class="row">			
			<div class="col-md-6" id="pie_1">
				
			</div>
			<div class="col-md-6" id="pie_2">
			</div>
		</div>
		<div class="row">
			<div class="col-md-6" id="pie_3">
				
			</div>
			<div class="col-md-6" id="pie_4">
			</div>
		</div>
	</div>


</div>



  </div><!--row-->
</div>
<script>
$(document).ready(function() {
	$('#put_here').html(<?php
echo $total_payout; ?>);
});
</script>
</body>
</html>