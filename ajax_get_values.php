<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');
if(!isset($_POST['id']))
	die("Invalid Call to Script");

$result=array ();

$property_id = $_POST['id'];
$_SESSION['property_id']=$property_id;
$month_year = date('F-Y', strtotime(date('Y-m')." -1 month"));

$prev_month_year = date('F-Y', strtotime(date('Y-m')." -2 month"));

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$pNames = $mysqli->query("select p.id,p.name,c.email from properties p INNER JOIN  clients c ON p.client_ID=c.id where p.id=".$property_id );
$pRow 	= $pNames->fetch_assoc();
$pname	= $pRow['name'];
$client_email	= $pRow['email'];

// print_r ($pRow);

		
if ($month_year!='')
{	
	$flag	= 0;
	$filePath	= $pname."_".$month_year.".pdf";
	if (file_exists($filePath)== true) 
	{
			$flag   = 1;					
	}				
	// echo "select * from billing  where property_id='".$property_id  ."' AND month='" . $month_year ."'";	
	if (1==1)
	{				
		$where		= ' and booking_status != "cancel" ';
		$where 	   .=" and property_id =".$property_id." ";
		
		$month_where =" and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $month_year . "' ";
		
		
		$sql="select 
				sum(earning) as total_earning,
				sum(nights) as total_nights, 
				SUM(gi.earning*p.management_fee/100) as total_payout  
				From gcal_imports as gi 
				INNER JOIN properties p ON gi.property_id=p.id where 1 
				" . $where . $month_where;
		$stmt = $mysqli->query($sql);
		$row = $stmt->fetch_assoc();
		
		$total_earning2=intval($row['total_earning']);
		$total_nights=$row['total_nights'];
		$average_price_per_night=number_format($row['total_earning']/$row['total_nights'],2);
		$management_fee2=$row['total_payout'];
		$total_payout2=$row['total_payout']+$check_in_and_cleaning_fees;
		$total_payout2=number_format($total_payout2,2);
		
		
		
		$fetchTotalSQL="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='Paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new ";
		$fetchData = $mysqli->query($fetchTotalSQL);
		// echo $fetchTotalSQL;
		/*for total , no 76,77 {START} */
		$total_count		= 0;
		$total_payout_count = 0;
		
		$fetchTotalCount 	= $mysqli->query("Select SUM(earning) as total_sum, SUM(earning * 0.2) as total_pay_sum  From gcal_imports where 1  $where $month_where  ");
		
		if($fetchTotalCount->num_rows > 0) 
		{
			$rowTotalCount		= $fetchTotalCount->fetch_assoc(); 
			$total_count		= $rowTotalCount['total_sum'];
			$total_payout_count = $rowTotalCount['total_pay_sum'];
		}
		// print_r ($rowTotalCount);
		$total_m_charge	= 0;
		$total_earning 	= 0;
		$total_payout 	= 0;
		
		if ($fetchData->num_rows >0)
		{
			while($row = $fetchData->fetch_assoc())
			{
				// print_r ($row);
				$earning 			= intval($row['earning']);
				$pro_fees 			= $mysqli->query("select id,name,management_fee from properties where id='".$row['property_id']."' ");
				$pro_management_fee = 0.2;
				
				if($pro_fees->num_rows > 0) {
					$pro_fees_row 		= $pro_fees->fetch_assoc();
					$pro_management_fee = ($pro_fees_row['management_fee']/100);
				}
				
				$payout 		= ($earning * $pro_management_fee);
				$total_m_charge = $total_m_charge + $payout;
				$total_earning	= $total_earning + $earning;
			}
		}
		
		$due_now	= 0;
		$monthDate	=$month_year ;
		
		$showWhere	= " and payments.task != 'virement'  and DATE_FORMAT(CASE task WHEN   'Paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%M-%Y') = '".$monthDate."' ";
		
		
		
		$query = "Select amount From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number And (payments.employee_id = gcal_imports.check_in_person Or payments.employee_id = gcal_imports.cleaning_person) where payments.property  =".$property_id." ".$showWhere." ";
		// echo $query;
		$fetchTotalSum = $mysqli->query($query);	
		
		if($fetchTotalSum->num_rows > 0){
			while($rowTotalSum = $fetchTotalSum->fetch_assoc()){
				$due_now += abs($rowTotalSum['amount']);
			}
			$due_now = -1 * $due_now;
		}
		
		if($due_now != 0){
			$due_now = -1 * $due_now;
		}
		
		$result['success']			= true;
		$result['due_now']			= $due_now;
		$result['management_fee']	= $total_m_charge;
		$result['total_due']		= floor($total_m_charge+$due_now);
		$result["net_income"]=  floor($total_earning2-($total_m_charge+$due_now));
		$result['property_id']		= $property_id;
		$result['month']			= $month_year;
		$result["flag"]				= $flag;
		$result["status"]			=  "";
		$result["id"]				=  "";
		$result["average_price_per_night"]=  floor($average_price_per_night);
		$result["total_nights"]=  $total_nights;
		$result["filePath"]=  $filePath;
		$result["client_email"]=  $client_email;
		
		$query="select note from billing where status='SENT' property_id=".$property_id." AND month='" . $prev_month_year . "'";
		$fetchNote=$mysqli->query ($query);
		
		if ($fetchNote->num_rows>0)
		{
			$rowNote=$fetchNote->fetch_assoc();
			$note_from_prev_month=$rowNote['note'];
		}
		else
		{
			$note_from_prev_month='';
		}
		
		
		$result["note_from_prev_month"]=  $note_from_prev_month;
		
		// $result["management_fee2"]=  $management_fee2;
		// $result["total_payout2"]=  $total_payout2;
	}
}
echo json_encode ($result);

