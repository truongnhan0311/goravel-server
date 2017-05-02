<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_POST['row_id']))
	die("Invalid Call to Script");

$row_id 		= $_POST['row_id'];
$amount 		= $_POST['amount'];
$employee_id	= $_POST['employee_id'];
$property 		= $_POST['property'];
$task 			= $_POST['task'];
$comment 		= $_POST['comment'];
$req_date 		= $_POST['req_date'];
$fileImg 		= $_FILES['fileImg']['name'];
 

if ($row_id!='')
{
	// $mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "UPDATE payments SET amount = :amount, 
				employee_id = :employee_id, 
				property = :property,  
				task = :task,            
				comment = :comment,            
				req_date = :req_date            
				WHERE pay_id = :row_id";
		
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':amount', $amount, PDO::PARAM_INT);       
		$stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);       
		$stmt->bindParam(':property', $property, PDO::PARAM_INT);       
		$stmt->bindParam(':row_id', $row_id, PDO::PARAM_INT);       
		$stmt->bindParam(':task', $task, PDO::PARAM_STR);	   
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);	   
		$stmt->bindParam(':req_date', $req_date, PDO::PARAM_STR);	   
		$stmt->execute(); 
		
		if(!empty($fileImg)){
			$sourcePath = $_FILES['fileImg']['tmp_name']; // Storing source path of the file in a variable
			$targetPath = "../uploads/".$_FILES['fileImg']['name']; // Target path where file is to be stored
		
			move_uploaded_file($sourcePath, $targetPath);
			$pdo->query("INSERT INTO employee_pictures set`filename`='$fileImg',`employee_id`='$employee_id',`property_id`='$property',`pay_id`='$row_id'");
		 
		}
		if(!empty($_POST['delOldImg']))
		{
			foreach($_POST['delOldImg'] as $delOldImg )
			{
				$pdo->query("delete from employee_pictures where id = $delOldImg");
				@unlink($targetPath);
			}
			
		}
		// if($stmt->errorCode() >0)
		// {
			// $errors = $stmt->errorInfo();
			// print_r ($errors);
		// }
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}
	
	// echo $sql;
	
	
}	
