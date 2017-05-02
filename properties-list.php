<?php 
require_once('../connect.php');
require_once('../functions_general.php');
include( 'function.php');
session_start();


$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);


if(!empty($_POST['p_id'])){

		$min_file_size 	= 55000;
		$valid_exts 	= array('jpeg', 'jpg', 'png', 'gif');
		$sizes 			= array(500 => 500);
		
		if(isset($_POST['img']))
		 	$property_img=$_POST['img'];
		else 
			$property_img="";
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
			
				if( $_FILES['image']['size'] > $min_file_size ){
					
							$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
							if (in_array($ext, $valid_exts)) {
								
								foreach ($sizes as $w => $h) {
									 $property_img = resize($w, $h);
									 								
								}
					
							} else {
								$msg = 'Unsupported file';
							}
						} else{
							$msg = 'Please upload image larger than 60KB';
						}
		}else{
					$msg="Please upload image";
			}
	$img_where = '';
	 	
	if(!empty($property_img)){
	$img_where = " property_img='".security($property_img)."', ";
 	}
		
 	$sql="UPDATE properties SET p_link= '".security($_POST['p_link'])."',
								p_title='".security($_POST['p_title'])."',
								home_page_status='".security($_POST['home_page_status'])."',
								".$img_where."
								p_description='".security($_POST['p_description'])."' WHERE id = '".$_POST['p_id']."' "; 
								
	$u = $mysqli->query($sql);
	
	
	if($u){
		$message="<span  class='btn bg-success ' >Property Has Been Updated Successfully</span >";
	}else{
		$message="<span  class='btn bg-danger '>There is some problem Try Again!</span >";
	}
	
}



?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

<!--<link href="/bootstrap.min.css" rel="stylesheet">-->
<link href="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
<script type='text/javascript' src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script>
<link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">
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


<div class="container-fluid table-responsive">
  <div class="row" style="margin:0 !important"> 
    <!--menu bar-->
    <?php require_once ('admin_menu.php')?>
  </div>
  
  
  
  
<?php 

if(!isset($_GET['pname']) || $_GET['pname'])
{
?>
    
    <div class="col-md-12">
  
    <form class="form-horizontal" action="" method="get">
  
  <div class="form-group">
    <select name="pname"  >
              <option value="">Please Select Property</option>
              <?php
			 $stmt1 = $mysqli->query("select id,name from properties where 1 ".$where2."  and active_status = 'YES' order by name asc");
			if($stmt1->num_rows > 0) { 
			 while($rows2 = $stmt1->fetch_assoc())
			{ ?>
              <option value="<?php echo $rows2['id']?>" <?php if($rows2['id']==$_GET['pname']){ echo "selected='selected'";} ?>><?php echo $rows2['name']?></option>
              <?php }
			  }
			?>
            </select>
           
    <input type="submit" value="submit" />
     </div>
  </form>
  </div>
    <?php 
}



if(isset($_GET['pname']))
{
	
 $p_sql = $mysqli->query("select * from properties where id='".$_GET['pname']."' and active_status = 'YES' order by name asc");
    $p_name = ''; 
	$p_title = ''; 
	$p_link = ''; 
	$p_description = ''; 
	$p_image = ''; 

 if($p_sql->num_rows > 0) { 
 $p_row = $p_sql->fetch_assoc();
	$p_name = $p_row['name']; 
	$p_title = $p_row['p_title']; 
	$p_link = $p_row['p_link']; 
	$p_description = $p_row['p_description']; 
	$p_image = $p_row['property_img']; 
	$home_page_status = $p_row['home_page_status']; 
 
 }
 
?>

<br>


<div class="row">
  <div class="col-md-6">
  	<div class="alert alert-danger"><?php  if(!empty($msg)){echo  $msg;	}?></div>
  <form method="post" action="" enctype="multipart/form-data">
  <div class="form-group">
  <label for="exampleInputtitle">Property name</label> <?php echo $p_name; ?>
  </div>
  <div class="form-group">
    <label for="exampleInputtitle">Title</label>
    <input type="text" class="form-control" name="p_title" id="exampleInputtitle"  value="<?php echo $p_title; ?>"placeholder="Title">
  </div>
  <div class="form-group">
    <label for="exampleInputLink">Link </label>
    <input type="text" class="form-control" name="p_link"  id="exampleInputLink" value="<?php echo $p_link; ?>" placeholder="Link">
  </div>
  
   <div class="form-group">
    <label for="exampleInputLink">Description </label>
    <textarea  class="form-control" name="p_description" id="exampleInputDescription" ><?php echo $p_description; ?></textarea>
  </div>
   <div class="form-group">
    <label for="exampleInputLink">Showe Home Page </label>
    <input type="radio" name="home_page_status" <?php if($home_page_status==1) echo "checked"; ?>   value="1"> Yes
    <input type="radio" name="home_page_status" <?php if($home_page_status==0) echo "checked";?> value="0"> No
  </div>
  
  
  <div class="form-group">
    <label for="exampleInputFile">Image</label>
    <input type="file" id="exampleInputFile" name="image">
   
   <img src="<?php echo $p_image; ?>" width="200" height="200" />
   
  </div>
  
  <input type="hidden"  name="p_id"  value="<?php echo $_GET['pname']; ?>" >
  <button type="submit" class="btn btn-default">Submit</button>
</form>

    <?php 
	
}
?>
</div></div>


</div>
</body></html>
