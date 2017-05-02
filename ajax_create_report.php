<?php
session_start();
session_start();
// error_reporting (0);

require_once ('../connect.php');


error_reporting(E_ALL);
ini_set('display_errors',true);

$report_title = $_POST['report_title'];
$employee_id = $_POST['employee_id'];
$property_id = $_POST['property_id'];
$template_id = $_POST['template_id'];

// print_r ($questions);exit;
if ($report_title!='' && $employee_id!='' && $property_id!='' && $template_id!='')
{
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "INSERT INTO checkup_reports (title,employee_id,property_id,template_id )
		VALUES('$report_title',$employee_id,$property_id, '$template_id'  )";
		
		
		// echo $sql;		
		$stmt = $pdo->prepare($sql);                                  		
		// $stmt->bindValue(':report_title', $report_title, PDO::PARAM_STR); 				 
		// $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT); 
		// $stmt->bindValue(':property_id', $property_id, PDO::PARAM_INT); 
		// $stmt->bindValue(':template_id', $template_id, PDO::PARAM_STR); 
		$stmt->execute(); 
		
		echo 'created';
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}