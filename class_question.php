<?php
@session_start();
require_once ('../connect.php');

$db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
class Question 
{	
	// Create a new object
	public function __construct() 
	{
		global $db;
		@session_start();		
		$this->pdo = $db;
	}
	
	public function get_question ($id)
	{
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_questions WHERE id = :id');
		$stmt->bindValue(":id",$id,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function get_attachment_name ($id)
	{
		
		$stmt  = $this->pdo->prepare('SELECT file FROM checkup_questions_attachments WHERE id = :id ');
		$stmt->bindValue(":id",$id,PDO::PARAM_INT);
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		
		return $row['file'];
	}
	
	public function get_attachments ($id,$type="")
	{
		if ($type!="")
			$type=" and type='$type'";
		else
			$type="";
		
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_questions_attachments WHERE question_id = :id ' . $type);
		$stmt->bindValue(":id",$id,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function get_all_questions ($status='')
	{
		if ($status=='active' or $status=='disabled')
		{
			$status=" AND status='$status' ";
		}
		
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_questions WHERE 1 ' . $status . " ORDER BY status");
		
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);	
	}
	
	public function insert_attachments($question_id,$image_file,$type)
	{
		$stmt  = $this->pdo->prepare ("INSERT INTO checkup_questions_attachments (question_id,file,type) VALUES (:question_id,:file,:type)");
		$stmt->bindValue(':question_id', $question_id,PDO::PARAM_INT);
		$stmt->bindValue(':file', $image_file,PDO::PARAM_STR);
		$stmt->bindValue(':type', $type,PDO::PARAM_STR);
		$stmt->execute();
	}
	
	public function delete_file($id)
	{
		$filename=$this->get_attachment_name($id);
		unlink ('uploads/' . $filename);
		
		$stmt  = $this->pdo->prepare ("DELETE FROM checkup_questions_attachments where id=:id");
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function insertorupdate ($values)
	{
		if (!isset ($values['id']))
			$values['id']=NULL;
		
		if (!isset ($values['status']))
		{
			$stmt  = $this->pdo->prepare('INSERT INTO checkup_questions (id,description) VALUES (:id,:description) ON DUPLICATE KEY UPDATE description=:description');
		
			$stmt->bindValue(':id', $values['id']); 
			$stmt->bindValue(':description', $values['description'],PDO::PARAM_STR);
						
		}
		else
		{
			
			$stmt  = $this->pdo->prepare('INSERT INTO `checkup_questions` (id,description,status) VALUES (:id,:description,:status) ON DUPLICATE KEY UPDATE description=:description, status=:status');
		
			$stmt->bindValue(':id', $values['id']); 
			$stmt->bindValue(':description', $values['description'],PDO::PARAM_STR); 
			$stmt->bindValue(':status', $values['status'],PDO::PARAM_STR);			
		}
		
		try
		{			
			// print_r ($stmt);
			$stmt->execute();
			
		} 
		catch (Exception $e) 
		{
			die("There's an error in the query!");
		}
		
		if (!isset ($values['id']))
		{
			$values['id']=NULL;
			$question_id=$this->pdo->lastInsertId ();
			
		}
		else
		{
			$question_id=$values['id'];
		}
		
		return $question_id;
	}	
}