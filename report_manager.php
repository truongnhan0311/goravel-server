<?php
session_start();
if (!isset($_SESSION['admin']) or $_SESSION['employee']['level'] != 3)
{
	header ('Location: index.php');
	exit;
}
require_once ('../connect.php');
error_reporting (0);
error_reporting(E_ALL);
ini_set('display_errors',true);

require_once('class_question.php');
$db = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);


$properties=array();
$employees=array();

$sql = "select name,id from properties where active_status='YES' ";
$stmt = $db->prepare($sql);                                  				
$stmt->execute(); 
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row)
{
	$properties[$row['id']]=$row['name'];
}

$sql = "select name,id from employee where status='YES' ";
$stmt = $db->prepare($sql);                                  				
$stmt->execute(); 
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row)
{
	$employees[$row['id']]=$row['name'];
}
		


$msg=array();
$question_obj=new Question();
if (isset ($_POST['description']))
{
	
	//if new question is submitted	
	$values=array();
	$values['description']=$_POST['description'];
	$values['status']=$_POST['status'];
	
	if (isset ($_POST['row_id']))
	{
		$values['id']=$_POST['row_id'];
	}
	
	
	// echo '<pre>' . print_r ($values,true) . '</pre>';
	// echo '<pre>' . print_r ($_POST,true) . '</pre>';exit;
	$question_id=$question_obj->insertorupdate($values);
	
	$msg[]="Question Updated";
	
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
								$question_obj->insert_attachments($question_id,$file_name,$file_type);
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
		<header>
			<div class="row">
			<?php require_once ('admin_menu.php');?>
			</div>
		</header>
		
			  
		<div class="row clearfix" style="padding:20px"> 
			<div class="col-sm-12">
			<?php 
				if (count($msg))
				{
					echo "<p class='error'>" . implode ("<br />",$msg) . "</p>";
				}
				
				$questions=$question_obj->get_all_questions();				
			?>
			<div class="row">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h4><span class="small" ><a href="" id="add_new"><i class="glyphicon glyphicon-plus"></i> Add New Question</a>&nbsp;&nbsp;&nbsp;<a href="" ><i class="glyphicon glyphicon-refresh"></i> refresh</a> </span></h4>
				</div>
				<table class="table table-fixed">
				  <thead>
					<tr>
					  <th class="col-xs-1">ID</th>
					  <th class="col-xs-8">Question</th>
					  <th class="col-xs-1">Status</th>
					  <th class="col-xs-2">Options</th>
					</tr>
				  </thead>
				  <tbody>
					<?php foreach ($questions as $question):?>
					<tr>
					  <td class="col-xs-1"><?php echo $question['id']?></td>
					  
					  <td class="col-xs-8">
						<?php echo stripslashes ($question['description'])?>
					  </td>
					  
					  <td class="col-xs-1">
						<?php 
						if ($question['status']=='active')
						{
							echo '<i class="glyphicon glyphicon-ok"></i>';
						}
						else
						{
							echo '<i class="glyphicon glyphicon-remove"></i>';
						}
						?>
					  
					  </td>
					  
					  <td class="col-xs-2">
					  
					  <a href="#" id="edit_question_<?php echo $question['id']?>"><i class="glyphicon glyphicon-edit"></i></a>
					  
					  <input type="checkbox" class="selected_question" value="<?php echo $question['id']?>" name="selected_questions[]" />
					  
					  
					  </td>
					</tr>
					<?php endforeach;?>
				  </tbody>
				</table>
			  </div>
			</div><!--table row-->

			<div class="row">
				<div class="col-md-12">
					<h4>Reports Templates <span class="small" ><a href="" id="create_template"><i class="glyphicon glyphicon-plus"></i> Create template with selected questions</a></h4>
					<div class="row" style="margin:20px;">
					<?php
					$sql = "SELECT * from checkup_templates order by created desc";
					$stmt = $db->prepare($sql); 
					$stmt->execute();
					$reports=$stmt->fetchAll(PDO::FETCH_ASSOC);
					?>
					<table class="table table-responsive">
					  <thead>
						<tr class="row">
						  <th class="col-xs-3">Title</th>
						  <th class="col-xs-3">Report title</th>						  
						  <th class="col-xs-6">Options</th>
						</tr>
					  </thead>
					  <tbody>
					<?php foreach ($reports as $report):?>
					<tr class="row">
						<td class="col-xs-3"><?php echo stripcslashes($report['title'])?></td>
						<td class="col-xs-3"><input type="text" name="report_title" id="report_title_<?php echo md5($report['id'])?>" value="" /></td>						  
						<td class="col-xs-6">
						<!--
						<a target=_blank href="preview_report.php?report_id=<?php echo md5($report['id'])?>" title="Preview Report as employee will see"><i class="glyphicon glyphicon-play-circle"></i></a>
						-->
						
						<select id="<?php echo md5($report['id'])?>">
							<option value="">Select Property</option>
						<?php foreach ($properties as $key=>$value):?>
							<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
						</select>
						
						<select class="<?php echo md5($report['id'])?>">
							<option value="">Select Employee</option>
						<?php foreach ($employees as $key=>$value):?>
							<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
						</select>
						
						<a href="#" role="button" class="btn btn-sm btn-primary" id="create_report_<?php echo md5($report['id'])?>" title="Send this report to employee">Create Report</a>
						
						<!--
						<a href="#" id="email_report_<?php echo md5($report['id'])?>" title="Send this report to employee"><i class="glyphicon glyphicon-envelope"></i></a>-->
						
						</td>
					</tr>
					<?php endforeach;?>
					</tbody>
					</table>
					</div>
				</div>
			</div>
			
			
			<div class="row">
				<div class="col-md-12">
					<h4>Existing Reports </h4>
					<div class="row" style="margin:20px;">
					<?php
					$sql = "SELECT * from checkup_reports ";
					$stmt = $db->prepare($sql); 
					$stmt->execute();
					$reports=$stmt->fetchAll(PDO::FETCH_ASSOC);
					?>
					<table class="table table-responsive">
					  <thead>
						<tr class="row">
						  <th class="col-xs-4">Title</th>
						  <th class="col-xs-4">Report url</th>						  
						  <th class="col-xs-2">Property</th>
						  <th class="col-xs-2">Employee</th>
						</tr>
					  </thead>
					  <tbody>
					<?php foreach ($reports as $report):?>
					<tr class="row">
						<td class="col-xs-4"><a target=_blank href="preview_report.php?report_id=<?php echo md5($report['id'])?>" title="See results" ><?php echo stripcslashes($report['title'])?></a></td>
						<td class="col-xs-4">http://<?php echo $_SERVER['HTTP_HOST']?>/spread/answer_checkup.php?report_id=<?php echo md5($report['id'])?></td>
						<td class="col-xs-2"><?php echo $properties[$report['property_id']]?></td>						  
						<td class="col-xs-2"><?php echo $employees[$report['employee_id']]?></td>
					</tr>
					<?php endforeach;?>
					</tbody>
					</table>
					</div>
				</div>
			</div>
			
			
			
			</div><!--main col-->
		</div><!--row-->
<!--dialogues-->
<div class="modal fade" id="modal_form" >

    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body">			
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="close_without_saving" data-dismiss="modal">Cancel</button>
                                
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modal_form_report" >

    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Create Template</h4>
            </div>

            <div class="modal-body">
				<form>
					<div class="form-group row">
						<label class="col-md-3 control-label" for="report_title">Template Title</label>
						<div class="col-md-9">							
							<input type="text" class="form-control" id="report_title" name="report_title" />
						</div>
					</div>
				</form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="close_without_saving" data-dismiss="modal">Cancel</button>
                 <button type="submit" class="btn btn-danger btn-lg" id="save_report" >Create</button>               
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="modal_form_email" >

    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Send to Employee</h4>
            </div>

            <div class="modal-body">
				<form>
					<div class="form-group row">
						<label class="col-md-3 control-label" for="employee_email">Email</label>
						<div class="col-md-9">							
							<input type="email" class="form-control" id="employee_email" name="employee_email" />
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 control-label" for="email_subject">Subject</label>
						<div class="col-md-9">							
							<input type="text" class="form-control" id="email_subject" name="email_subject" />
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 control-label" for="email_msg">Message</label>
						<div class="col-md-9">							
							<textarea rows="3" class="form-control" id="email_msg" name="email_msg" ></textarea>
						</div>
					</div>
				</form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="close_without_saving" data-dismiss="modal">Cancel</button>
                 <button type="submit" class="btn btn-danger btn-lg" id="send_report" >Send</button>               
            </div>
        </div>
    </div>

</div>

<!--dialgoues end here-->
		
	<script>             
	
$(document).ready(function(){
	
	
	
   $(document).delegate('[id^=delete_attachment_]','click',function (event) 
	{		
		event.preventDefault();
		if (!confirm('Are you sure you want to delete? this cannot be un-done'))
			return false;
		
		var row_id = $(this).attr('id');		
		row_id=$(this).attr('id');
		row_id=row_id.split("_");
		row_id=row_id[2];
		$(this).parent('div').remove();
		
		// alert (row_id);
		$.post("ajax_delete_file.php",{'row_id':row_id},
		function(data,returned_status)
		{	
			// $('#add_row_'+row_id).remove();
		});
			
	});
	
	
   $(document).delegate('[id^=edit_question_]','click',function (event) 
	{		
		event.preventDefault();

		var row_id = $(this).attr('id');		
		row_id=$(this).attr('id');
		row_id=row_id.split("_");
		row_id=row_id[2];
		$(this).parent('div').remove();
		
		$.post(
		"ajax_get_question.php",
		{row_id:row_id},
		function(data,status)
		{				
			// alert (data);
			$('#modal_form .modal-body').html(data);
			$('#modal_form').modal('show', {backdrop: 'static'});
		});	 	
	});
	
	$(document).delegate('[id=save_report]','click',function (event) 
	{
		var n = $( "input.selected_question:checked" ).length;
		if (n==0)
		{
			alert ("Please selected questions");
			return;
		}
		
		var report_title=$('#report_title').val();
		var data = { 'report_title':report_title, 'selected_questions[]' : []};
		$("input.selected_question:checked").each(function() {
		  data['selected_questions[]'].push($(this).val());
		});		
		// alert (data);
		$('#modal_form_report').modal('hide');
		$.post("ajax_create_template.php",data,
		function(data,status)
		{				
			// alert (data);
			window.location='report_manager.php';
		});
		
	});
	
	
	
	
	$(document).delegate('[id^=create_template]','click',function (event) 
	{		
		event.preventDefault();
		var n = $( "input.selected_question:checked" ).length;
		if (n==0)
		{
			alert ("Please selected questions");
			return;
		}
		$('#modal_form_report').modal('show', {backdrop: 'static'});		
	});	
	
	$(document).delegate('[id^=add_new]','click',function (event) 
	{		
		event.preventDefault();		
		$.post(
		"ajax_get_question.php",
		{},
		function(data,status)
		{				
			// alert (data);
			$('#modal_form .modal-body').html(data);
			$('#modal_form').modal('show', {backdrop: 'static'});
		});	 	
	});	
	
	var fields = '<div class="form-group row">'+
'		<label class="col-md-3 control-label" for="files[]">Media:</label>'+
'		<div class="col-md-7">'+
'			<input style="float:left" type="file"  id="files" name="uploads[]" /><a style="float:left" href="#" class="remove_field"><i class="glyphicon glyphicon-remove"></i></a></div>'+
'		</div>		'+
'</div>';
	

	
	var max_fields      = 20; //maximum input boxes allowed
	var wrapper         = ".input_fields_wrap"; //Fields wrapper
	var add_button      = ".add_field_button"; //Add button ID
   
	var x = 1; //initlal text box count
	
	//on add input button click
	$(document).delegate(add_button,'click',function (e){ 
	
		e.preventDefault();
		// alert ('called');
		if(x < max_fields)
		{ 
			//max input box allowed
			x++; //text box increment
			$(wrapper).append(fields); //add input box			
		}
		// alert ('called');
	});
	
	// $(wrapper).on("click",".remove_field", function(e){ 
	$(document).delegate(".remove_field",'click',function (e){ 		
		//user click on remove text
		e.preventDefault(); 
		$(this).parent('div').parent('div').remove(); 
		x--;
	})
	
	
	$(document).delegate('[id^=create_report_]','click',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');		
		row_id=$(this).attr('id');
		row_id=row_id.split("_");
		
		var report_id=row_id[2];
		var template_id=report_id;
		// alert (report_id);
		
		var property_id=$("#"+report_id).val();
		if (property_id=="")
		{
			alert('Please select property');
			return;
		}
		
		var report_title=$("#report_title_"+report_id).val();
		if (report_title=="")
		{
			alert('Please provide report title');
			return;
		}
		
		var employee_id=$("."+report_id).val();
		if (employee_id=="")
		{
			alert('Please select an employee');
			return;
		}
		
		// alert (report_title);
		// return;
		$.ajax(
		{
			url:"ajax_create_report.php",
			type:"post",
			data:{'report_title':report_title,'employee_id':employee_id,'property_id':property_id,'template_id':template_id},
			async : false,
			success: function(data,status)
			{				
				alert ('report created');
				window.location='report_manager.php';
			}
		});			
	});
	
	
	
	$(document).delegate('[id^=email_report_]','click',function (event) 
	{		
		event.preventDefault();
		var row_id = $(this).attr('id');		
		row_id=$(this).attr('id');
		row_id=row_id.split("_");
		
		var report_id=row_id[2];
		
		// alert (report_id);
		
		var property_id=$("#"+report_id).val();
		if (property_id=="")
		{
			alert('Please select property');
			return;
		}
		
		
		$('#employee_email').val("");
		
		$('#email_msg').val("Please visit http://<?php echo $_SERVER['HTTP_HOST'] . '/spread\/' . 'answer_checkup.php?report_id='?>"+report_id+"&"+"property_id="+property_id);
		
		$('#email_subject').val("Checkup Report "+new Date($.now()));
		
		$('#modal_form_email').modal('show', {backdrop: 'static'});		
	});
	
	
	$(document).delegate('[id^=send_report]','click',function (event) 
	{		
		event.preventDefault();

		var employee_email=$('#employee_email').val();
		var employee_subject=$('#employee_subject').val();
		var email_msg=$('#email_msg').val();
		
		$.post(
		"ajax_send_email.php",
		{'employee_email':employee_email,'employee_subject':employee_subject,'email_msg':email_msg},
		function(data,status)
		{	
			$('#modal_form').modal('hide');
		});	 	
	});
	
});
</script>
</body>
</html><?php
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
