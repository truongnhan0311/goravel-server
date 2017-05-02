<?php
session_start();
// error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_POST['property_id']))
	die("Invalid Call to Script");

$month = $_POST['month'];
$property_id = $_POST['property_id'];
$due_now = $_POST['due_now'];
$management_fee = $_POST['management_fee'];
$total_due = $_POST['total_due'];

$result=array ("id"=>"","status"=>"");

if ($property_id!='')
{
	
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "INSERT INTO billing SET property_id = :property_id, 
				total_due = :total_due, 
				management_fee = :management_fee,  
				due_now = :due_now,            
				month = :month";
				
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);       
		$stmt->bindParam(':total_due', $total_due, PDO::PARAM_INT);       
		$stmt->bindParam(':management_fee', $management_fee, PDO::PARAM_INT);       
		$stmt->bindParam(':due_now', $due_now, PDO::PARAM_INT);       
		$stmt->bindParam(':month', $month, PDO::PARAM_STR);		   
		$stmt->execute(); 
		
		// echo 'Record inserted: ';
		$result["success"]=true;
		$result["id"]=$pdo->lastInsertId();
		$result["status"]='SENT';
		echo json_encode ($result);
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}	
