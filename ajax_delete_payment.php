<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_POST['row_id']))
	die("Invalid Call to Script");

$row_id = $_POST['row_id'];

if ($row_id!='')
{	
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "DELETE FROM payments WHERE pay_id = :row_id";
				
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':row_id', $row_id, PDO::PARAM_INT);       
		$stmt->execute(); 				
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Delete Operation failed: ' . $e->getMessage();
	}
	
	// echo $sql;
	
	
}	
