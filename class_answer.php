<?php
@session_start();
require_once ('../connect.php');

$db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
class Answer 
{	
	// Create a new object
	public function __construct() 
	{
		global $db;
		@session_start();		
		$this->pdo = $db;
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function get_answer ($id)
	{
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_answers WHERE id = :id');
		$stmt->bindValue(":id",$id,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function get_employee_answer ($question_id,$employee_id,$report_id)
	{
		
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_answers WHERE question_id = :question_id and employee_id=:employee_id and report_id=:report_id');
		$stmt->bindValue(":question_id",$question_id,PDO::PARAM_INT);
		$stmt->bindValue(":report_id",$report_id,PDO::PARAM_INT);
		$stmt->bindValue(":employee_id",$employee_id,PDO::PARAM_STR);
		try
		{
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e) 
		{
			// print_r ($_POST);
			echo 'Operation failed: ' . $e->getMessage();
		}
	}
	
	public function get_attachment_name ($id)
	{
		
		$stmt  = $this->pdo->prepare('SELECT file FROM checkup_answers_attachments WHERE id = :id ');
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
		
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_answers_attachments WHERE answer_id = :id ' . $type);
		$stmt->bindValue(":id",$id,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function get_all_answers ($status='')
	{
		if ($status=='active' or $status=='disabled')
		{
			$status=" AND status='$status' ";
		}
		
		$stmt  = $this->pdo->prepare('SELECT * FROM checkup_answers WHERE 1 ' . $status . " ORDER BY status");
		
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);	
	}
	
	public function insert_attachments($answer_id,$image_file,$type)
	{
		$stmt  = $this->pdo->prepare ("INSERT INTO checkup_answers_attachments (answer_id,file,type) VALUES (:answer_id,:file,:type)");
		$stmt->bindValue(':answer_id', $answer_id,PDO::PARAM_INT);
		$stmt->bindValue(':file', $image_file,PDO::PARAM_STR);
		$stmt->bindValue(':type', $type,PDO::PARAM_STR);
		$stmt->execute();
	}
	
	public function delete_file($id)
	{
		$filename=$this->get_attachment_name($id);
		unlink ('uploads/' . $filename);
		
		$stmt  = $this->pdo->prepare ("DELETE FROM checkup_answers_attachments where id=:id");
		$stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function insertorupdate ($values)
	{
		if (!isset ($values['id']))
			$values['id']=NULL;
		
		//check for existing answer_id
		$answer_row=$this->get_employee_answer ($values['question_id'],$values['employee_id'],$values['report_id']);
		
		if ($answer_row['id']!="")
		{
			//answer exists update will be done
			$values['id']=$answer_row['id'];
		}
		
		if ($values['description']!='')
		{
			$stmt  = $this->pdo->prepare('INSERT INTO checkup_answers (id,description,employee_id,question_id,property_id,report_id) VALUES (:id,:description,:employee_id,:question_id,:property_id,:report_id) ON DUPLICATE KEY UPDATE description=:description');
		
			$stmt->bindValue(':id', $values['id']); 
			$stmt->bindValue(':employee_id', $values['employee_id'],PDO::PARAM_INT);
			$stmt->bindValue(':question_id', $values['question_id'],PDO::PARAM_INT);
			$stmt->bindValue(':property_id', $values['property_id'],PDO::PARAM_INT);
			$stmt->bindValue(':report_id', $values['report_id'],PDO::PARAM_STR);
			$stmt->bindValue(':description', $values['description'],PDO::PARAM_STR);
		}
		
		try
		{			
			// print_r ($stmt);
			$stmt->execute();
			
		} 
		catch (Exception $e) 
		{
			echo 'Operation failed: ' . $e->getMessage();
		}
		
		if (!isset ($values['id']))
		{
			$values['id']=NULL;
			$answer_id=$this->pdo->lastInsertId ();
			
		}
		else
		{
			$answer_id=$values['id'];
		}
		
		return $answer_id;
	}	
}