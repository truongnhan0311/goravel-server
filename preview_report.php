<?php
session_start();
require_once ('../connect.php');
error_reporting (0);
// error_reporting(E_ALL);
// ini_set('display_errors',true);

if (!isset($_SESSION['admin']))
{
	header ('Location: /index.php');
	exit();
}

$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
$report_id = $_REQUEST['report_id'];
$property_id = $_REQUEST['property_id'];
$employee_id = $_REQUEST['employee_id'];
if ($report_id!='')
{
	?><!DOCTYPE html>
<html lang="en">
    <head>
    	<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
   		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    	<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
    	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    	<script src="jquery.timepicker.min.js"></script>
		<link href="jquery.timepicker.css" rel="stylesheet">
		
		<script src="../jslibs/ajaxupload-min.js" type="text/javascript"></script>
		
		<style>
		.table-fixed thead 
		{
		  width: 97%;
		}
		.table-fixed tbody {
		  height: 230px;
		  overflow-y: auto;
		  width: 100%;
		}
		.table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
		  display: block;
		}
		.table-fixed tbody td, .table-fixed thead > tr> th {
		  float: left;
		  border-bottom-width: 0;
		}
		</style>
		
    </head>
	<body>	
	<?php	
	if ($report_id!="")
	{
		echo '<div class="row" style="margin-top:50px;"><div class="col-md-10 col-md-offset-1">';
		try
		{
			
					
			$sql = "select questions,employee_id,property_id,template_id from checkup_reports INNER JOIN checkup_templates on template_id=md5(checkup_templates.id) where md5(checkup_reports.id)= :report_id";
			$stmt = $pdo->prepare($sql);                                  		
			$stmt->bindValue(':report_id', $report_id, PDO::PARAM_STR); 
			$stmt->execute(); 
			$row=$stmt->fetch(PDO::FETCH_ASSOC);
			$questions=unserialize($row['questions']);
			$employee_id=$row['employee_id'];
			$property_id=$row['property_id'];
			$template_id=$row['template_id'];
			
			// echo '<pre>' . print_r ($row,true) . '</pre>';
			
			$total=count($questions);
			
			require_once('class_question.php');					
			require_once('class_answer.php');
					
			$question_obj=new Question();
			$answer_obj=new Answer();			
			
			echo '<ul>';
			foreach ($questions as $question_id)			
			{
				$question_row=$question_obj->get_question($question_id);
				$images=$question_obj->get_attachments($question_id,'image');
				$videos=$question_obj->get_attachments($question_id,'video');
				
				echo "<li style='margin-bottom:30px;clear:both;float:none;'><strong>" . $question_row['description'] . '</strong><br />';
				$answer_row=$answer_obj->get_employee_answer ($question_id,$employee_id,$template_id);
				
				echo $answer_row['description'] . '<br />';
				if ($answer_row['id'])
				{
					$images=$answer_obj->get_attachments($answer_row['id'],'image');
					$videos=$answer_obj->get_attachments($answer_row['id'],'video');
					
					?>
					<div>
						<?php
						foreach ($images as $file)
						{
							if ($file['type']=='image')
							{
							?>
							<div style="float:left;">
								<img style="max-width:150px;min-width:150px;max-height:100px;min-height:150px;" src="uploads/<?php echo $file['file']?>" />
							</div>						
							<?php
							}
						}
						?>
					</div>
					<div style="clear:both;float:none;"></div>
					<div style="float:left;">
						<?php
						foreach ($videos as $file)
						{
							if ($file['type']=='video')
							{
							?>
							<div class="embed-responsive embed-responsive-4by3">
								<iframe class="embed-responsive-item" src="uploads/<?php echo $file['file']?>"></iframe>
							</div>
													
							<?php
							}
						}
						?>
					</div>
					<div style="clear:both;float:none;"></div>	
					<?php
				}
				echo '</li>';
			}
			echo '</ul>';
		}
		catch (PDOException $e) 
		{
			// print_r ($_POST);
			echo 'Operation failed: ' . $e->getMessage();
		}	
		
		echo '</div></div>';
	}
	else
	{
		$properties=array();
		$employees=array();

		$sql = "select name,id from properties  where active_status='YES' ";
		$stmt = $pdo->prepare($sql);                                  				
		$stmt->execute(); 
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($rows as $row)
		{
			$properties[$row['id']]=$row['name'];
		}

		$sql = "select name,id from employee where status='YES' ";
		$stmt = $pdo->prepare($sql);                                  				
		$stmt->execute(); 
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($rows as $row)
		{
			$employees[$row['id']]=$row['name'];
		}
		
		?>
		<form method="post">
			
			<div class="row" style="margin-top:50px;">
			<div class="col-md-6 col-md-offset-3">
			<select id="property_id" name="property_id">
				<option value="">Select Property</option>
			<?php foreach ($properties as $key=>$value):?>
				<option value="<?php echo $key?>"><?php echo $value?></option>
			<?php endforeach;?>
			</select>
			
			<select id="employee_id" name="employee_id">
				<option value="">Select Employee</option>
			<?php foreach ($employees as $key=>$value):?>
				<option value="<?php echo $key?>"><?php echo $value?></option>
			<?php endforeach;?>
			</select>
			
			<input type="hidden" name="report_id" value="<?php echo $_REQUEST['report_id']?>" />
			
			<button type="submit" class="btn btn-primary">Submit</button>
			</div>
			</div>
		</form>
		<?php
	}
}
?>
</body>
</html>