<?php
session_start();
require_once ('../connect.php');
error_reporting (0);
// error_reporting(E_ALL);
// ini_set('display_errors',true);

if (!isset($_SESSION['employee']))
{
	header ('Location: /job/index.php');
	exit();
}
$report_id = $_GET['report_id'];

$employee_id=NULL;
$property_id=NULL;
$template_id=NULL;
// print_r ($_SESSION);
// $employee_id = 11;
// print_r ($questions);exit;
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
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "select questions,employee_id,property_id,template_id from checkup_reports INNER JOIN checkup_templates on template_id=md5(checkup_templates.id) where md5(checkup_reports.id)= :report_id";
		$stmt = $pdo->prepare($sql);                                  		
		$stmt->bindValue(':report_id', $report_id, PDO::PARAM_STR); 
		$stmt->execute(); 
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		// echo '<pre>' . print_r ($row,true) .'</pre>';exit;
		
		$employee_id=$row['employee_id'];
		$property_id=$row['property_id'];
		$template_id=$row['template_id'];
		$questions=unserialize($row['questions']);
		// print_r($questions);exit;
		
		$total=count($questions);
		
		
		if (isset($_REQUEST['question_no']))
			$current_question=$_REQUEST['question_no'];
		else
			$current_question=0;
		require_once('class_question.php');		
		
		require_once('class_answer.php');
				
		$question_obj=new Question();
		$question_row=$question_obj->get_question($questions[$current_question]);
		$images=$question_obj->get_attachments($questions[$current_question],'image');
		$videos=$question_obj->get_attachments($questions[$current_question],'video');
		
		$answer_obj=new Answer();
		
		
		
		if (isset ($_POST['answer']) and $_POST['answer']!='' and $_POST['question_id']!='')
		{
			
			$values['description']=$_POST['answer'];
			$values['employee_id']=$employee_id;
			$values['property_id']=$property_id;
			$values['question_id']=$_POST['question_id'];
			$values['report_id']=$template_id;
			// print_r ($values);exit;
			$answer_id=$answer_obj->insertorupdate($values);
			
			if (isset ($_FILES['uploads']))
			{
				$files = reArrayFiles($_FILES['uploads']);
				// print_r ($files);exit;
				foreach ($files as $file)
				{
					$file_name = $file['name'];
					$file_type = $file['type'];
					$file_size = $file['size'];
					$tmp_name = $file['tmp_name'];
					
					$file_size_max = 50048;
					
					if (!empty($file_name))
					{    
						
						$file_name = time() . basename($file_name);
						$file_name = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $file_name);		 
						$file_name =  preg_replace("/^'|[^A-Za-z0-9\s-\.]|'$/", '', $file_name);
						
						$tempFile =  $tmp_name;  
						$file_info = new finfo(FILEINFO_MIME);	// object oriented approach!
						$mime_type = $file_info->buffer(file_get_contents($tempFile)); 
						
						if (strstr ($mime_type,"image"))
							$file_type="image";
						else if (strstr($mime_type,"video"))
							$file_type="video";
						else
							$file_type="error";
						
						
						if ($file_type!="error")
						{
							
							if ($file['error'] > 0)
							{
								// echo "Unexpected error occured, please try again later.";
								$msg[]=$file_name . ": Unexpected error ";
								continue;
							} 
							else 
							{
								if (file_exists("uploads/".$file_name))
								{
									// echo $file_name." already exists.";
									$msg[]=$file_name . ": File already exists";
									continue;
								} 
								else 
								{
									if (move_uploaded_file($tmp_name, "uploads/".$file_name))
										$answer_obj->insert_attachments($answer_id,$file_name,$file_type);
									// echo "Stored in: " . "secure/".$file_name;
								}
							}
							
						}
						else
						{
							//not allowed
							$msg[]=$file_name . ": File type error ";
							continue;
						}
					}
				}		
			}
		}
		
		?>
		<header>
			<div class="row" style="text-align:center;">
				<?php for ($i=0;$i<$total;$i++):?>
					<a href="answer_checkup.php?question_no=<?php echo $i?>&amp;report_id=<?php echo $_GET['report_id']?>"><?php echo $i?></a>&nbsp;
					
				<?php endfor;?>
			</div>
		</header>
		<div class="row clearfix" style="padding:20px"> 
			<div class="col-xm-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
			
			<?php
			$answer_row=$answer_obj->get_employee_answer ($questions[$current_question],$employee_id,$template_id);
		
			if ($answer_row['id']!='')
			{
				$description=$answer_row['description'];
				$answer_images=$answer_obj->get_employee_answer($answer_row['id']);
				$answer_videos=$answer_obj->get_employee_answer($answer_row['id']);
			}
			else
			{
				$description="";
				$answer_images=array();
				$answer_videos=array();
			}
			?>
			
			<?php if ($_REQUEST['question_no']!=-1):?>
			<form method="post" enctype="multipart/form-data">
			
				<div class="form-group row">
					<label class="form-label" for="answer"><?php echo stripslashes($question_row['description'])?></label>
										
									
						<div>
							<?php
							foreach ($images as $file)
							{
								if ($file['type']=='image')
								{
								?>
								<div style="float:left;">
									<img style="max-width:200px;min-width:200px;max-height:150px;min-height:150px;" src="uploads/<?php echo $file['file']?>" />
								</div>						
								<?php
								}
							}
							?>
						</div>
					
					
					
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
					
				</div>
				
				
				<div class="form-group row">
					<textarea id="answer" name="answer" rows="3" class="form-control"><?php echo stripslashes($description)?></textarea>
					<?php
					if ($answer_row['id'])
					{
						$answer_images=$answer_obj->get_attachments($answer_row['id'],'image');
						$answer_videos=$answer_obj->get_attachments($answer_row['id'],'video');
						
						?>
						<div>
							<?php
							foreach ($answer_images as $file)
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
							foreach ($answer_videos as $file)
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
					}?>
					
				</div>
				<div class="form-group row">
					<label class="col-md-3 control-label" for="files[]">Media:</label>
					<div class="col-md-7">
						<input type="file" id="files" name="uploads[]" />
					</div>		
				</div>
				
				<input type="hidden" name="question_id" value="<?php echo $questions[$current_question]?>" />
				<input type="hidden" name="report_id" value="<?php echo $template_id?>" />
				<input type="hidden" name="property_id" value="<?php echo $property_id ?>" />
				
				
				<div class="row">
					<div class="col-xs-4 col-xs-offset-4">
				<?php if ($current_question+1<$total):?>
				<input type="hidden" name="question_no" value="<?php echo $current_question+1?>" />
				

				<input class="btn btn-primary btn-block" type="submit" name="submit" value="Save and Next" />
					
			
				<?php else:?>				
				<input type="hidden" name="question_no" value="-1" />
				<input class="btn btn-primary btn-block" type="submit" name="submit" value="Save" />
				<?php endif;?>
					</div>
				</div>
			</div>	
			</form>
			<?php else:?>
			<h3>Thanks for your input</h3>
			<?php endif;?>
			</div>
		</div>
		<?php
		
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
	?>
	<?php
}
?>
</body>
</html>
<?php
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}
