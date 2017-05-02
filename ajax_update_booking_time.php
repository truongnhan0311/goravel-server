<?php
session_start();
// error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_POST['row_id']))
	die("Invalid Call to Script");

$booking_number = $_POST['row_id'];
$value = $_POST['value'];
$mode = $_POST['mode'];

if ($booking_number!='')
{
	
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		if ($mode=='checkin')
		{
			$sql = "UPDATE gcal_imports SET check_in_time = :value 
				where booking_number = :booking_number";
		
		}
		else if ($mode=='checkout')
		{
			$sql = "UPDATE gcal_imports SET check_out_time = :value 
				where booking_number = :booking_number";
		}
				
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':value', $value, PDO::PARAM_INT);       		      
		$stmt->bindParam(':booking_number', $booking_number, PDO::PARAM_STR); 
		$stmt->execute(); 
		
		echo 'updated';
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}	
