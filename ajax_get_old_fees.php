<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');
if(!isset($_REQUEST['id']))
	die("Invalid Call to Script");


$return_array=array();
$return_array['success']=true;

// $result['success']=true;
// $result['data'][0]['id']=23;
// $result['data'][1]['id']=23;
// $result['data'][2]['id']=23;
// $result['data'][3]['id']=23;
$property_id = $_POST['id'];
$previous_month_year = date('F-Y', strtotime(date('Y-m')." -1 month"));

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

$pNames = $mysqli->query("select * from properties where id=".$property_id );
$pName = $pNames->fetch_assoc();
$pname = $pName['name'];			

$already_have_billing_entry=$mysqli->query("select * from billing  where property_id='".$property_id  ."' AND status ='PAID' order by date desc");

$result='';
if ($already_have_billing_entry->num_rows>0)
{	
	$i = 1;
	while ($existing_row=$already_have_billing_entry->fetch_assoc())
	{
		
		 $filePath	=	"../pdf/".$pname."_".$existing_row['month'].".pdf";
		if (file_exists($filePath)== true)
		 	$download = "Regenrate Bill";
		else
			$download = "Genrate Bill";		
			
		$result .="<tr class='temp'>";		
		$result .="<td class='billing_month$i'>" . $existing_row['month'] . "</td>";
		$result .="<td class='due_now$i'>" . $existing_row['due_now'] . "</td>";
		$result .="<td>" . $existing_row['management_fee'] . "</td>";
		$result .="<td>" . $existing_row['total_due'] . "</td>";
		$result .="<td>" . $existing_row['status'] . "</td>";
		$result .='<td><input onclick="return get_pdf('.$i.');" type="button" class="download_bills_link" value="'.$download.'">';
		if($download == "Regenrate Bill")
			$result .='<input onclick="return download_pdf('.$i.');" type="button" value="Download Bill">';
		$result .="</td></tr>";
		$filePath   = "";
		$i++;
	}		
}
$_SESSION['due']= $existing_row['due_now'];
// echo json_encode ($return_array);
echo $result;
