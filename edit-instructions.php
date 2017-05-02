<?php
require_once('../connect.php');
require_once('../functions_general.php');
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$mysqli->set_charset("utf8");
session_start();



if(isset($_POST)){
	if($_POST)
	{
		$description = mysqli_real_escape_string($mysqli ,$_POST['description']); 
		$title = $_POST['title'];
		//$general_info = mysql_real_escape_string($_POST['general_info']); 
		$cleaning = mysqli_real_escape_string($mysqli ,$_POST['cleaning']); 
		$check_in = mysqli_real_escape_string($mysqli ,$_POST['check_in']); 
		
		$sql  = "UPDATE static_page SET title = '".$title."', 
		                                                 description = '".$description."' ,
														 cleaning = '".$cleaning."' ,
														 check_in = '".$check_in."' 
		         									     where id='1' ";
													 
											 
		$update = $mysqli->query($sql);
	}
}

if( !isset($_SESSION['admin']) ) {
	header('Location: job.php');
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<style>
.green_td {
  background-color: #acfa58 !important;
  width: 1%;
}.blue_td { background-color:#58D3F7 !important}

.navbar-inverse{background-color:  #D8D8D8 !important;}
.navbar-inverse a { color:#000000 !important;}	
</style>
<script type="text/javascript" src="../tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
   // selector: "mceEditor",
     //plugins : "fullpage",
    theme: "modern",
	 mode : "specific_textareas",
     editor_selector : "mceEditor",
    width: 1000,
    height: 400,
    plugins: [
         "advlist fullpage  link lists charmap  preview hr anchor pagebreak spellchecker",
         "searchreplace  visualblocks visualchars code fullscreen insertdatetime  nonbreaking",
         "save table contextmenu directionality emoticons  template paste textcolor "
   ],
   relative_urls : false,
remove_script_host : false,
convert_urls : true,
   content_css: "css/content.css",
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent  | forecolor backcolor emoticons ", 
   style_formats: [
        {title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
    ]
 }); 
</script>
<style>


.green_td {


  background-color: #acfa58 !important;


  width: 1%;


}.blue_td { background-color:#58D3F7 !important}


.check_in_tomorrow td,.check_in_tomorrow_li{background-color:#D0F5A9 !important}
.check_in_tomorrow td.green_class { background-color:green !important;}

.check_in_today td,.check_in_today_li{background-color:#00FF00 !important}
.check_in_today td.green_class { background-color:green !important;}

.check_out_today td,.check_out_today_li{background-color:#F78181 !important}
.check_out_today td.green_class { background-color:green !important;}

.booking_cancel td,.booking_cancel_li{background-color:#A9A9A9 !important}


.taxi_li{background-color:#FFFF00 !important;color:black !important}


.instructions{background-color:#FF99FF !important}


.mifi_li{background-color:#58D3F7 !important;color:black !important}


.check_in_tomorrow_li a,.check_in_today_li a,.taxi_li a{color:#777 !important}





.check_in_tomorrow_li a,.in_apartment_li a,.in_late_check_out_li a,.check_in_today_li a,.check_out_today_li a{cursor: default; !important}


.navbar-inverse{background-color:  #D8D8D8 !important;}


.navbar-inverse a { color:#000000 !important;}	








.in_apartment td,.in_apartment_li{background-color:#F5D0A9 !important ;}
.in_apartment td.green_class { background-color:green !important;}


.in_late_check_out_li{background-color:#FF8000 !important}


.form-horizontal #loading {


    display: inline-block;


    float: left;


    margin-left: 5px;


}

.green_class{background-color:green !important;}
.white_class{background-color:#fff !important;}
</style>


        <style>
.text_a {
	background: none repeat scroll 0% 0% rgb(255, 255, 255);
	border: medium none;
	overflow: hidden;
	color: rgb(255, 255, 255);
	z-index: 99999;
	position: absolute;
	opacity: 0;
	width: 50px;
	height: 20px;
	cursor: pointer;
}
.text_aa {
	top: 0px;
	z-index: 0;
	left: 0px;
}

.input-large{min-width:600px !important; min-height:60px !important;border-bottom: 1px solid #ccc !important;}

.editable-popup{display:none !important;}
</style>
</head>
<body>
<?php
$stmt1 	= $mysqli->query("select * from static_page where id='1' ");
$rows 	= $stmt1->fetch_assoc();

?>
<div class="container-fluid table-responsive">
<div class="row">
<!--menu bar-->     
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->

		
			
		<form class="form-horizontal" action="" method="post">
			<label>Title</label>
			<input type="text" value="<?php echo $rows['title'];?>" name="title" />
			</br>
			<label>Description</label>
            <textarea name="description" class="mceEditor"><?php echo $rows['description'];?></textarea>
			</br>
            
         <!--   </br>
			<label>General info</label>
           <textarea name="general_info" class="mceEditor"><?php echo $rows['general_info'];?></textarea>
			</br>-->
            
            </br>
			<label>Cleaning </label>
            <textarea name="cleaning" class="mceEditor"><?php echo $rows['cleaning'];?></textarea>
			</br>
            
            
            </br>
			<label>check in </label>
           <textarea name="check_in" class="mceEditor"><?php echo $rows['check_in'];?></textarea>
			</br>
            
            
            
			<input type="submit" name="submit" value="submit" />
			</form><br />

		
	
	</div>
</div>

</body>
</html>