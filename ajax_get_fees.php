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
$month_year[0] = date('F-Y', strtotime(date('Y-m')." -1 month"));
$month_year[1] = date('F-Y', strtotime(date('Y-m')." 0 month"));

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$pNames = $mysqli->query("select * from properties where id=".$property_id );
$pName 	= $pNames->fetch_assoc();
$pname	= $pName['name'];

		
for($i=0; $i<count($month_year);$i++ ){		
	
	$already_have_billing_entry=$mysqli->query("select * from billing  where property_id='".$property_id  ."' AND (month='" . $month_year[$i] ."')");
	$flag[$i]	= 0;

	if ($already_have_billing_entry->num_rows>0){
			while($existing_row=$already_have_billing_entry->fetch_assoc()){
				
				
				$filePath	= "../pdf/".$pname."_".$month_year[$i].".pdf";
			
				if (file_exists($filePath)== true) {
						$flag[$i]   = 1;
					
				}
					
					if ($month_year[$i]== $existing_row['month']){
					
						// $result=array ("id"=>"","status"=>"");
						$result[$i]["success"]=true;
						$result[$i]["due_now"]=$existing_row['due_now'];
						$result[$i]["management_fee"]=$existing_row['management_fee'];
						$result[$i]["total_due"]=$existing_row[''];
						$result[$i]["property_id"]=$existing_row['property_id'];
						$result[$i]["month"]=$existing_row['month'];
						$result[$i]["total_due"]=$existing_row['total_due'];
						$result[$i]["status"]=$existing_row['status'];
						$result[$i]["id"]=$existing_row['id'];
						$result[$i]["flag"]=$flag[$i];
						// echo json_encode ($result);
						
				
				}
			
		}
	}else{
				$clientIds = '';
				// $stmtNew 	= $mysqli->query("select id from properties  where client_ID='".$client_id  ."' ");
				
				// if($stmtNew->num_rows > 0) 
				// { 
				// while($rowsNew = $stmtNew->fetch_assoc())
				// { 
				// $clientIds .= "'".$rowsNew['id']."'".",";
				// }
				// }
				
				// $clientIds = rtrim($clientIds,',');
				$clientIds	= rtrim($property_id,',');
				$where		= ' and booking_status != "cancel" ';
				$where 	   .=" and property_id IN (".$clientIds.") ";
				
				$month_where =" and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $month_year[$i] . "' ";
				
				
				$fetchData = $mysqli->query("Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='Paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new ");
				
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
				
				$total_m_charge	= 0;
				$total_earning 	= 0;
				$total_payout 	= 0;
				
				if ($fetchData->num_rows >0){
					while($row = $fetchData->fetch_assoc()){
						
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
				$monthDate	=$month_year[$i] ;
				
				$showWhere	= " and payments.task != 'virement'  and DATE_FORMAT(CASE task WHEN   'Paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%M-%Y') = '".$monthDate."' ";
				
				$query = "Select amount From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number And (payments.employee_id = gcal_imports.check_in_person Or payments.employee_id = gcal_imports.cleaning_person) where payments.property IN (".$clientIds.") ".$showWhere." ";
				
				$fetchTotalSum = $mysqli->query($query);
				
				
				/*
				$fetchTotalSum = $mysqli->query("Select SUM(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1 and payments.task != 'virement'  and DATE_FORMAT(CASE task WHEN   'paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%M-%Y') = '".$previous_month_year."' and payments.property IN (".$clientIds.") " );
				
				
				$fetchTotalSum = $mysqli->query("Select SUM(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1 and DATE_FORMAT(CASE task WHEN   'Paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%Y-%m-%d') <= CURDATE()   and payments.property IN (".$clientIds.") ");
				*/
				if($fetchTotalSum->num_rows > 0){
					while($rowTotalSum = $fetchTotalSum->fetch_assoc()){
						$due_now += abs($rowTotalSum['amount']);
					}
					$due_now = -1 * $due_now;
				}
				
				if($due_now != 0){
					$due_now = -1 * $due_now;
				}
				
				$result[$i]['success']			= true;
				$result[$i]['due_now']			= $due_now;
				$result[$i]['management_fee']	= $total_m_charge;
				$result[$i]['total_due']		= $total_m_charge+$due_now;
				$result[$i]['property_id']		= $property_id;
				$result[$i]['month']			= $month_year[$i];
				$result[$i]["flag"]				= $flag[$i];
				$result[$i]["status"]			=  "";
				$result[$i]["id"]				=  "";
			}
}
$data=array();
//$_SESSION['due']= $result['due_now'];
echo json_encode ($result);

