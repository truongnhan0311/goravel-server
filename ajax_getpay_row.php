<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');
if(!isset($_POST['row_id']))
	die("Invalid Call to Script");

$row_id = $_POST['row_id'];

if ($row_id!='')
{
	$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
	$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
	
	$sql .="select * from payments where pay_id='" . $row_id . "' ";
	$result=$mysqli->query($sql);
	if ($result->num_rows > 0)
	{
		$row=$result->fetch_assoc();
		$reservation_no=stripslashes ($row['reservation_no']);
		$employee_id=$row['employee_id'];
		$amount=$row['amount'];		
		$req_date=$row['req_date'];
		$req_date=date_create($req_date);
		$property=$row['property'];
		$task=stripslashes ($row['task']);
		$comment=stripslashes ($row['comment']);		
	}
	else
	{
		die ("Invalid pay id please close and try again");
	}
	?>
    <style>
		.edit_gallery_images img {
			display: inline-block;
			float: left;
			height: 64px;
			margin: 3px;
			width: 64px;
		}
		.remove-img img {
			height: 100%;
			margin: 0;
			padding: 3px;
			width: 100%;
		}
		.edit_gallery_images img {
			display: inline-block;
			float: left;
		}
		img {
			vertical-align: middle;
		}
		.remove-img {
			background: #ffffff none repeat scroll 0 0;
			border: 1px solid #cccccc;
			cursor: pointer;
			height: 20px;
			position: absolute;
			right: 0;
			top: 0;
			width: 20px;
			z-index: 2;
		}
		.remove_icon {
			float: left;
			position: relative;
		}
	</style>
		<form id="payedit_form"  method="post" enctype="multipart/form-data" >
			<input type="hidden" value="<?php echo trim($row_id)?>" name="row_id" id="row_id" />
			<!--<input type="hidden" value="<?php //echo trim($req_date)?>" name="req_date" id="req_date" />-->
			
			<div class="form-group row">
				<label class="col-md-2 control-label" for="amount">Amount:</label>
				<div class="col-md-2">
					<input class="form-control" type="text" id="amount" value="<?php echo $amount?>" name="amount" maxlength="6"/>
				</div>				
				<label class="col-md-2 control-label" style="text-align:right" for="task">Task:</label>
				<div class="col-md-6">
					<input class="form-control" type="text" id="task" value="<?php echo $task?>" name="task" />
				</div>
				
			</div>
			<div class="form-group row">
            <label class="col-md-2 control-label" for="amount">Date:</label>
				<div class="col-md-2">
					<input class="form-control" type="date"  id="req_date" value="<?php echo date_format($req_date,"Y-m-d"); ?>" name="req_date"  readonly="readonly" />
				</div>			
            </div>
			
			
			<div class="form-group row">
				<label class="col-md-2 control-label" for="property">Property:</label>
				<div class="col-md-6">
					<select class="form-control" id="property" name="property">
						<option value="">Please Select Property</option>
						<?php
						$stmt1 	= $db->query("select id,name from properties where active_status = 'YES' order by name asc");
						$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);
						if(!empty($rows1))
						{ 
							foreach($rows1 as $rows2)
							{ ?>
								<option <?php if ($rows2['id']==$property) echo "selected=selected" ?> value="<?php echo $rows2['id']?>" ><?php echo $rows2['name']?></option>
							<?php 
							}
						}
						?>						
					</select>
				</div>				
			</div>
			
			<div class="form-group row">
				<label class="col-md-2 control-label" for="employee_id">Employees:</label>
				<div class="col-md-6">
					<select class="form-control" id="employee_id" name="employee_id">
						<option value="">Please Select Property</option>
						<?php
						$stmt1 	= $db->query("select id,name from employee where status='YES' order by name asc");
						$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);
						if(!empty($rows1))
						{ 
							foreach($rows1 as $rows3)
							{ ?>
								<option <?php if ($rows3['id']==$employee_id) echo "selected=selected" ?> value="<?php echo $rows3['id']?>" ><?php echo $rows3['name']?></option>
							<?php 
							}
						}
						?>						
					</select>
				</div>				
			</div>
			
			<div class="form-group row">
				<label class="col-md-2 control-label" for="comment">Comment:</label>
				<div class="col-md-10">
					<textarea rows="3" cols="100" class="form-control" id="comment" name="comment"><?php echo $comment?></textarea>
				</div>				
			</div>
            <div class="form-group row">
				<label class="col-md-2 control-label" for="uploadImage">Upload Image:</label>
				<div class="col-md-10">
					<input type="file" name="fileImg" id="fileImg" class="form-control" >
				</div>				
			</div>
            <div class="form-group row">
				<label class="col-md-2 control-label"></label>
				<div class="col-md-10">
                	<span class="edit_gallery_images">
					<?php
					//echo "SELECT * FROM employee_pictures where pay_id='" . $row_id . "' ";
                     $stmt2 	= $db->query("SELECT * FROM employee_pictures where pay_id='" . $row_id . "' ");
                     $rows2 	= $stmt2->fetchAll(PDO::FETCH_ASSOC);
					 
						if(!empty($rows2))
						{ 
							foreach($rows2 as $img)
							{   ?>
                            
                              <div class="remove_icon" id="imgold_<?php echo $img['id']; ?>">  
                                 <img src="../uploads/<?php echo $img['filename'];?>" /><span class="remove-img" onclick="removeimg(<?php echo $img['id']; ?>)"><img src="../uploads/cross.png" /></span>
                                     <input type="hidden" name="old_gallery_img[]" value="<?php echo $img['image'];?>">
                                                    </div>
                                                    
                                                    <?php
                                                     } 
                                         }?>
                        </span>                         
				</div>				
			</div>
            	
          </form>
	<?php	
}	
?>
<link rel="stylesheet" href="../datetimepicker/css/jquery.datetimepicker.css">
<link rel="stylesheet" href="../css/baseTheme/style.css" type="text/css" media="all" />
<script src="../jslibs/ajaxupload-min.js" type="text/javascript"></script>
<script src="../datetimepicker/js/jquery.datetimepicker.js"></script>

<script>
$('#req_date').datetimepicker({
lang:'en',
format:'Y-m-d',
timepicker:false,
scrollInput:false,
});
function removeimg(oldimg){
	
	$("#imgold_"+oldimg).remove();
	var del_oldimg = '<input type="hidden" id="delOldImg" name="delOldImg[]" value="'+oldimg+'">';
	$('.edit_gallery_images').append(del_oldimg);

}
</script>
