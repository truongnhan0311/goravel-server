<?php
 // INCLUDE THE phpToPDF.php FILE
require("phpToPDF.php"); 
require_once('../connect.php');
require_once('../functions_general.php');
session_start();
if(!isset($_SESSION['admin']))
{
		header('location: index.php');
}
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

$data_encry = base64_decode($_GET['data_encry']);
$data_stim = base64_decode($_GET['data_stim']);
///======================================== START MANAGEMENT FEES=====================================================//////////
$previous_month_year = $data_encry;
$stmtNew 		=	$mysqli->query("SELECT * from properties  where id='".intval($_SESSION['property_id'])."'");
if($stmtNew->num_rows > 0) { 
		while($rowsNew = $stmtNew->fetch_assoc())
		{ 
		$Pname = $rowsNew['name'];
		$address=$rowsNew['address'];
		
		 }
		}
		$clientIds = "";
$property_id = $_SESSION['property_id'];
 	$clientIds = rtrim($property_id,',');
	$where = ' and booking_status != "cancel" ';
	$where .=" and property_id IN (".$clientIds.") ";


// PUT YOUR HTML IN A VARIABLE
$html		=	"<html> <head> <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/><style>					
	              .data-table{ border:1px solid #CCC; font-family:Helvetica; font-weight:normal; width:100%; margin-bottom:10px;margin-top:10px;} 
				  .data-table tr th { padding:10px; text-align:left; font-size:13px; border-bottom:1px solid #CCC; border-right:1px solid #CCC;} 
				  .data-table tr th { padding:10px; text-align:left; font-size:13px; border-bottom:1px solid #CCC; border-right:1px solid #CCC;}
				  .data-table tr td { padding:10px; text-align:left; font-size:11px; border-bottom:1px solid #CCC; border-right:1px solid #CCC;} 
				  .data-table tr td { padding:10px; text-align:left; font-size:11px; border-bottom:1px solid #CCC; border-right:1px solid #CCC;}
				  .data-table tr:nth-child(2n){ background:#f9f9f9; }
				   h4 { font-size:13px;}
				  .item { height:230px; width:200px; padding-top:15px; vertical-align:top; display:block; float:left; margin-left:10px;}
				  .item img { width:100%; height:100%; }
				  
	               </style></head> <body>
                  <h4 style='font-family:Helvetica; font-weight:normal; margin-bottom:0px;'>".$Pname."</h4>
				  <h4 style='font-family:Helvetica; font-weight:normal; margin-top:0px;'>".$address."</h4>";

$month_and_year = date('F-Y');

$month = date('F');

$month_where =" and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $previous_month_year . "' ";
//$html .="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new";

$fetchData	=	$mysqli->query("Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new");



/*for total , no 76,77 {START} */

$total_count		= 0;
$total_payout_count = 0;




$fetchTotalCount 	=	$mysqli->query("Select SUM(earning) as total_sum, SUM(earning * 0.2) as total_pay_sum  From gcal_imports where 1  $where $month_where  ");

if($fetchTotalCount->num_rows > 0) {
	$rowTotalCount		= $fetchTotalCount->fetch_assoc(); 
	$total_count		= $rowTotalCount['total_sum'];
	$total_payout_count	= $rowTotalCount['total_pay_sum'];
}
/*for total , no 76,77 {END} */

if($fetchData->num_rows > 0) {

	$html .=   "          
				   <table class='data-table' cellspacing='0'>
      				<tr>
						<th style='width:20%;'>Name</th>
						<th style='width:10%;'>Booking Number</th>
						<th style='width:25%;'>Check in data</th>
						<th style='width:25%;'>Check out data</th>
						<th style='width:10%;'>Earning</th>
						<th style='width:10%;'>Management charges</th>
      				</tr>";

	    
	  
	$total_m_charge	= 0;
	$total_earning	= 0;
	$total_payout	= 0;
		
	while($row = $fetchData->fetch_assoc())
	   {
		$earning 	= intval($row['earning']);
		$pro_fees 	= $mysqli->query("select id,name,management_fee from properties where id='".$row['property_id']."' ");
		$pro_management_fee = 0.2;
		
		if($pro_fees->num_rows > 0) { 
		   $pro_fees_row 		= $pro_fees->fetch_assoc();
		   $pro_management_fee 	= ($pro_fees_row['management_fee']/100);
		}
		
		
		$payout			 = ($earning * $pro_management_fee);
		$total_m_charge  = $total_m_charge + $payout;
		$total_earning   = $total_earning + $earning;
		
	   

    $html .=  "<tr>

        <td>".$row['name']."</td>
        <td>".$row['booking_number']."</td>
        <td>".$row['check_in']." </td>
        <td>".$row['check_out']."</td>
        <td>".$earning."</td>
        <td>".$payout."</td>


      </tr>";

	   }

	$html .=	"</table>";
	$html .= "<h4 style='font-family:Helvetica; font-weight:normal;margin-bottom:0px; padding-top:20px;'>Total earned brut : ".$total_earning." &euro;</h4>	
			 <h4 style='font-family:Helvetica; font-weight:normal;margin-top:0px;'>Total management fees to pay : ".$total_m_charge." &euro;</h4><br />";
      

	} else {

		$html.= "<table class='data-table' cellspacing='0' width='100%'>
      				<tr>
						<th style=''>Name</th>
						<th style=''>Booking Number</th>
						<th style=''>Check in data</th>
						<th style=''>Check out data</th>
						<th style=''>Earning</th>
						<th style=''>Management charges</th>
      				</tr>
					<tr><td colspan='6' style='text-align:center;' >No Records found</td></tr> 
				</table>
		       ";
	           }

///======================================== END MANAGEMENT FEES=====================================================//////////
 
 
 ///====================================== START CHECK IN  and CLEANING FEE=========================================//////////
 
 $check_emp	= $mysqli->query("SELECT id from properties  where id ='".intval($clientIds)."'");
 
 $i			=	0;

while($row = $check_emp->fetch_assoc()){
	 	
		$ids[$i] = $row['id'];
			$i++; 	 
		}
        $pr_id = join(',',$ids);

	$monthDate=$previous_month_year ;

$showWhere = " and payments.task != 'virement'  and DATE_FORMAT(CASE task WHEN   'paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%M-%Y') = '".$monthDate."' ";

$query = "Select *,DATE_FORMAT(CASE task WHEN   'paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%W %d %M %Y') as order_date,DATE_FORMAT(CASE task WHEN   'paid' THEN req_date  WHEN 'add spending' THEN req_date ELSE check_in_new END,'%Y-%m-%d') as order_by_date From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number And (payments.employee_id = gcal_imports.check_in_person Or payments.employee_id = gcal_imports.cleaning_person) where payments.property IN (".$pr_id.") ".$showWhere."  group by pay_id Order By order_by_date";
//echo $query; exit;
$fetchRoom = $mysqli->query($query);

	if($fetchRoom->num_rows > 0) {

	
    $html	.= '<table class="data-table"  cellspacing="0">     
				  <tr>
					<th>Date</th>
					<th >Name</th>
					<th>Property</th>
					<th>Task</th>
					<th>Amount</th>
					
				  </tr>';
	   
        
	   $total = 0;
	   $ammount_count = 0;
	   while($row = $fetchRoom->fetch_assoc())
	   {
		   $ammount_count += abs($row["amount"]);
		   if($row["task"] == 'Cleaning' OR $row["task"] == 'Check in')
			{
				$name=$row["name"];
				
				
			}else if(($row["task"]) =="Paid" OR $row["task"] == 'add spending'){
				
				$name=$row["comment"];
			}
			
         $html	.= '<tr>
					<td >'.$row["order_date"].'</td>
					<td >'.$name.'</td>
					<td >';
         $fetchpro = $mysqli->query("SELECT name from properties where id=".intval($row["property"]));
       	 if($fetchpro->num_rows > 0) { 
           	 $row_pro = $fetchpro->fetch_assoc();
             $html	 .= $row_pro["name"];
        }
             $html	.=  '</td>
				<td >'.$row["task"].'</td>
				<td >'.abs($row["amount"]).'</td>
			</tr>'; 
			
			$total = $total+$row["amount"];
			
           
  
		$fetchpics = $mysqli->query("SELECT * from employee_pictures where employee_id=".intval($row["employee_id"]) . " and property_id=" . intval($row["property"]). " and pay_id=" . intval($row["pay_id"]));
		
		// echo "SELECT * from employee_pictures where employee_id=".intval($row["employee_id"]) . " and property_id=" . intval($row["property"]);
		
		if($fetchpics->num_rows > 0) 
		{ 
			
			while ($pics_row = $fetchpics->fetch_assoc())
			{
				$filename = $pics_row["filename"];
				$image_rows.='<div class="item" id="employee_' . $row['employee_id'] . '_' . $row["property"] . '_' . $row['pay_id'] . '">
					<img class="thumbnail img-responsive" title="" src="http://'.$_SERVER['HTTP_HOST'].'/uploads/'.$filename.'">
				</div>';
				
				
			}
		}
	  }
	if($total < 0) 
			{
				$total = -1 * $total;
			}
	 
	$sub_total = $total_m_charge +$ammount_count;
	$html .= "</table>
     		<h4 style='font-family:Helvetica; font-weight:normal;margin-bottom:0px; padding-top:20px;'>total check in / cleaning / laundry / groceries fees : ".$ammount_count." &euro;</h4>
			<h4 style='font-family:Helvetica; font-weight:normal;margin-bottom:0px; margin-top:0px;'>Total bill to pay for $data_encry : $ammount_count &euro; + $total_m_charge &euro; = $sub_total&euro; </h4>
			
			 ";
    
  	} else {
		$html .= "<table class='data-table' cellspacing='0' width='100%'>
      				<tr>
						<th>Date</th>
						<th>Name</th>
						<th>Property</th>
						<th>Task</th>
						<th>Amount</th>
      				</tr>
					<tr><td colspan='5' style='text-align:center;' >No Records found</td></tr> 
				</table>
		
		
		";
	}
	$html .= "<h4 style='font-family:Helvetica; font-weight:normal;margin-bottom:0px; margin-top:10px;'>Receipt for $data_encry : </h4>". $image_rows .'</body>
 </html>';
 $pdfName	=	$Pname."_".$monthDate.".pdf";
 $structure	=	"../pdf";
 $filepath	=	"http://".$_SERVER['HTTP_HOST']."/pdf";
 
 if (file_exists($filepath)== false) {
    if (mkdir($structure, 0777, true)) {
    	
	}
}
$oldFilepath	=	"../pdf/".$pdfName;
if (file_exists($oldFilepath)== true) {
	
	$newFilepath	=	"../pdf/".$Pname."_".$monthDate."1.pdf";
	rename($oldFilepath,$newFilepath);
}

$pdf_options = array(
  "source_type" => 'html',
  "source" => $html,
  "action" => 'save',
  "save_directory" => '../pdf',
  "omit_images" => 'no',
  "file_name" => $pdfName);

// CALL THE phpToPDF FUNCTION WITH THE OPTIONS SET ABOVE
echo phptopdf($pdf_options);
// OPTIONAL - PUT A LINK TO DOWNLOAD THE PDF YOU JUST CREATED
$filepath	=	"http://".$_SERVER['HTTP_HOST']."/pdf/".$pdfName;
?>
<a id="downloadLink" href="<?php echo $filepath; ?>" target="_blank" 
type="application/octet-stream" download="<?php echo $pdfName; ?>"></a>
<script>
	document.getElementById('downloadLink').click();
</script>