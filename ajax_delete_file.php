<?php
session_start();
require_once('class_question.php');
$question_obj=new Question();
$row_id = $_POST['row_id'];

if ($row_id!='')
{
	$row=$question_obj->get_question($row_id);
	$images=$question_obj->get_attachments($row_id,'image');
	$videos=$question_obj->get_attachments($row_id,'video');
	
	$question_obj->delete_file($row_id);
	echo 'success';
}