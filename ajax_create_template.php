<?php
session_start();
session_start();
// error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once ('../connect.php');

$report_title = $_POST['report_title'];
$questions = $_POST['selected_questions'];
// print_r ($questions);exit;
if ($report_title!='')
{
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "INSERT INTO checkup_templates SET questions = :questions, 
				title = :report_title";
						
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindValue(':questions', serialize($questions), PDO::PARAM_STR);
		$stmt->bindValue(':report_title', $report_title, PDO::PARAM_STR); 
		$stmt->execute(); 
		
		echo 'created';
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}