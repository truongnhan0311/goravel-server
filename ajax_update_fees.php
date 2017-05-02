<?php
session_start();
// error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_POST['id']))
	die("Invalid Call to Script");

$id = $_POST['id'];
$month = $_POST['month'];

if ($id!='')
{
	
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->beginTransaction();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "UPDATE billing SET status = 'PAID'
				where id = :id";
				
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);       			   
		$stmt->execute(); 
		
		
		$sql = "SELECT * from billing where id=:id";
		$stmt = $pdo->prepare($sql);  
		$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$due_now=$row['due_now'];
		$property_id=$row['property_id'];
				
		
		$property_ids = '';
		$sql 	= "select id from properties  where id=:property_id";
		$stmt = $pdo->prepare($sql); 
		$stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT); 
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$property_ids .="'" . $row['id'] . "',";
			$property=$row['id'];
		}
		
		if (is_null($property))
			$property=0;
		
		$property_ids = rtrim($property_ids,',');
		
		// $comment="PAID FROM clientBilling properties(" . $property_ids . ")";
		$comment="payment " . $month . "";
		
		//employee_id 4 Julia as was requested
		$sql = "INSERT INTO payments SET reservation_no = '', 
				employee_id = '4', 
				task = 'virement',  
				amount = :due_now,            
				comment = :comment,
				property = :property";
		
		$stmt = $pdo->prepare($sql);                                  		      
		$stmt->bindParam(':due_now', $due_now, PDO::PARAM_INT);       
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':property', $property, PDO::PARAM_STR);
		$stmt->execute(); 
		
		$pdo->commit();
		// echo 'Record updated: ';
		$result["success"]=true;
		$result["id"]=$pdo->lastInsertId();
		$result["status"]='PAID';
		$result["month"]=$month;
		echo json_encode ($result);
	}
	catch (PDOException $e) 
	{
		$pdo->rollBack();
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}	
