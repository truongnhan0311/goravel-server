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
}
else
{
	$row['id']=NULL;
	$row['description']='';
	$row['status']=NULL;
	$images=array();
	$videos=array();
}
?>
<form id="rowedit_form" action="#" method="post" enctype="multipart/form-data">
	<?php if (!is_null($row['id'])):?>
		<input type="hidden" value="<?php echo $row['id']?>" name="row_id" id="row_id" />
	<?php endif;?>
	
	<div class="form-group row">
		<label class="col-md-3 control-label" for="description">Description</label>
		<div class="col-md-9">
			<span style="color:#3C3C29;font-size:small;"></span>
			<textarea class="form-control" id="description" name="description" rows="5"><?php echo stripslashes ($row['description'])?></textarea>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-md-3 control-label" for="status">Status:</label>
		<div class="col-md-4">
			<select class="form-control" id="status" name="status">
				<option <?php if (is_null($row['status'])) echo ' selected '?> value="">Select Status</option>
				<option <?php if ($row['status']=='active') echo ' selected '?> value="active">Active</option>
				<option <?php if ($row['status']=='disabled') echo ' selected '?> value="disabled">Disabled</option>
			</select>					
		</div>
	</div>	
	<div class="input_fields_wrap">	
		
		<div class="form-group row">
			<label class="col-md-3 control-label" for="files[]">Media:</label>
			<div class="col-md-7">
				<input type="file" id="files" name="uploads[]" />
			</div>		
		</div>
		
	</div>
	
	<div style="clear:both;float:none;"></div>
					
	<div align="center">
		<button class="add_field_button">Add More Files</button>
	</div>
	<br />
	<div class="row col-md-9 col-md-offset-3">				
		<div class="row">
			<?php
			foreach ($images as $file)
			{
				if ($file['type']=='image')
				{
				?>
				<div>
					<img style="max-width:200px;min-width:200px;max-height:150px;min-height:150px;" src="uploads/<?php echo $file['file']?>" />
					<a id="delete_attachment_<?php echo $file['id']?>" href="#" class="remove_image">delete</a>
				</div>						
				<?php
				}
			}
			?>
		</div>
	</div>
	
	<div class="row col-md-9 col-md-offset-3">				
		<div class="row">
			<?php
			foreach ($videos as $file)
			{
				if ($file['type']=='video')
				{
				?>
				<div class="embed-responsive embed-responsive-4by3">
					<iframe class="embed-responsive-item" src="uploads/<?php echo $file['file']?>"></iframe><a id="delete_attachment_<?php echo $file['id']?>" href="#" class="remove_image">delete</a>
				</div>
										
				<?php
				}
			}
			?>
		</div>
	</div>
	
	<button type="submit" class="btn btn-danger btn-lg" id="save_and_close" >Save</button>
</form>
