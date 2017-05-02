<?php
require_once('../connect.php');
require_once('../functions_general.php');
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
session_start();
unset($_SESSION['admin']);
//session_destroy();
header('location:index.php'); 
?>