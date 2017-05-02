<?php
session_start();
error_reporting(0);
require_once ('../connect.php');
require_once ('../functions_general.php');
require('../smtp_mail/class.phpmailer.php');
if (!isset($_SESSION['admin']))
{
	header('location: index.php');
	exit;
}
$previous_month_year = date('F-Y', strtotime(date('Y-m') . " -1 month"));
$system_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="../jslibs/ajaxupload-min.js" type="text/javascript"></script>
		<script type="text/javascript" src="../tinymce/tinymce.min.js"></script>
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
			.table tr th, .table tbody tr td {
				border-right: 1px solid #ccc;
				border-left: 1px solid #ccc;
				border-top: 1px solid #ccc;
				border-bottom: 1px solid #ccc;
				min-width:3%;
			}
		</style>
		<link rel="stylesheet" href="../css/baseTheme/style.css" type="text/css" media="all" />
	</head>
	<body>
		
				<!--menu bar-->     
				<?php
require_once ('admin_menu.php') ?>
				<!--end of menu bar-->				
<div style="clear:both;float:none;"></div>
<?php
	$month_year= date('F-Y', strtotime(date('Y-m')." -1 month"));
	$filepath	=	"http://".$_SERVER['HTTP_HOST'];
?>

<?php if (isset ($_POST['pdffile'])):?>
<?php
// echo '<pre>' . print_r ($_POST,true) . '</pre>';exit;
?>
<div class="clearfix">
	<div class="row" style="margin:10px;">
<?php
$path = dirname( realpath( __FILE__ ) ) . DIRECTORY_SEPARATOR;
//$path = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , $path );
$path = preg_replace( '~[/\\\\]~' , DIRECTORY_SEPARATOR , $path );
$path .='pdf'.DIRECTORY_SEPARATOR;

// echo '<pre>' . print_r ($_POST,true) . '</pre>';

$note_for_next_month = $_POST['note_for_next_month'];
$month = $_POST['month'];
$property_id = $_POST['property'];
$due_now = $_POST['due_now'];
$management_fee = $_POST['management_fee'];
$total_due = $_POST['total_due'];

if ($property_id!='')
{
	
	try
	{
		$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$sql = "INSERT INTO billing SET property_id = :property_id, 
				total_due = :total_due, 
				management_fee = :management_fee,  
				due_now = :due_now,            
				note = :note_for_next_month,            
				month = :month";
				
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);       
		$stmt->bindParam(':total_due', $total_due, PDO::PARAM_INT);       
		$stmt->bindParam(':management_fee', $management_fee, PDO::PARAM_INT);       
		$stmt->bindParam(':due_now', $due_now, PDO::PARAM_INT);       
		$stmt->bindParam(':month', $month, PDO::PARAM_STR);		   
		$stmt->bindParam(':note_for_next_month', $note_for_next_month, PDO::PARAM_STR);		   
		$stmt->execute(); 
		
		echo 'Record inserted: ';
		$result["success"]=true;
		$result["id"]=$pdo->lastInsertId();
		$result["status"]='SENT';
		// echo json_encode ($result);
		
	}
	catch (PDOException $e) 
	{
		// print_r ($_POST);
		echo 'Operation failed: ' . $e->getMessage();
	}	
}	


$mailto=$_POST['send_to'];
$title=$_POST['title'];
$message=$_POST['message'];
// $pdffile=$path . trim($_POST['pdffile']);
$pdffile=$_SERVER['DOCUMENT_ROOT'] . '/pdf/' . trim($_POST['pdffile']);
$mailfrom = 'julia.mesner@gmail.com';


// echo $pdffile;exit;
mb_internal_encoding("UTF-8");
$mail = new PHPMailer();
$mail->IsSMTP();
$subject_evernote = mb_encode_mimeheader($title);
$mail->CharSet = 'UTF-8';
$mail->ContentType = 'text/html';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'ssl';
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587

$mail->Username = "julia.mesner@gmail.com";
// $mail->Username = "sebastienburdge@gmail.com";
// $mail->Password = "sebastien2015";
$mail->Password = "Ilovevietnam2017";


$mail->isHTML = true;
$mail->From = $mailfrom;
$mail->FromName = "Julia";

foreach ($_POST['ax-uploaded-files'] as $file_attached)
{
	$file_attached=$_SERVER['DOCUMENT_ROOT'] . '/uploads/' . trim(basename($file_attached));
	$mail->AddAttachment($file_attached);
}

$mail->AddAttachment($pdffile);

mb_internal_encoding("UTF-8");
$mail->Subject = $subject_evernote;
$mail->Body = trim ($message);
$mail->ReturnPath = array(
	$mailfrom
);
$mail->AddAddress($mailto); /*reciver email address*/
$mail->AddReplyTo($mailfrom, 'Julia');
// $mail->Send();//disabled when testing by nazir

if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		
	} else {
		$error = 'Message sent!';
		
	}

echo $error;
?>
</div></div>
<?php else:?>
<div class="modal" style="display: none;justify-content: center;align-items: center;">
		<!--<p class="small">please wait! pdf is being generated&nbsp;&nbsp;</p>-->
        <img style="width:50px;height:50px;" alt="" src="../images/ajax_loader_gray_512.gif" />
    
</div>

<div class="clearfix">
	<div class="row" style="margin:10px;">
	<form class="form-horizontal" id="email_form" name="email_form" method="POST">	
		<div class="row">
			<div class="col-md4 col-md-offset-4">
			<?php
			$properties=array();
			try
			{
				$pdo = new PDO('mysql:host=' . $db_host_connect . ';dbname=' . $db_name_connect, $db_user_connect, $db_pass_connect);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				// $sql = "select * from clients where active_status='YES'";
				$sql = "select id,name from properties where active_status='YES' order by name asc";
				$stmt = $pdo->prepare($sql);
				// $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
				$stmt->execute();
			?>
					
									
							<div class="form-group">
								<div class="col-xm-12 col-sm-6 col-md-4 col-lg-3 col-xl-1 col-xs-offset-1 col-sm-offset-5 col-md-offset-3 col-lg-offset-8 col-xg-offset-8">
									<select class="form-control" id="property_id" name="property_id">
										<option value="">Select a property</option>
					<?php
					$flag = 0;
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					{
						// echo '<pre>' . print_r ($row,true) . '</pre>';
						$properties[$row['id']]['email']=$row['email'];
						echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
					}
				?>
									</select>
								</div>
							</div>					
			<?php
			}
			catch(PDOException $e)
			{
				echo 'Database query failed: ' . $e->getMessage();
			}
			?>
			</div>
		</div>
		<div class="col-sm-12 col-md-5 col-lg-5 col-xl-4">
			
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 control-label" for="send_to">Note from Previous Month</label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						<div id="note_from_prev_month" value="" ></div>						
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 control-label" for="send_to">Send to</label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						<input type="email" class="form-control" placeholder="Send to" name="send_to" id="send_to" value="" >									
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 control-label" for="title">Title</label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						<input type="text" class="form-control" placeholder="title" name="title" id="title" value="Airbnb <?php echo $month_year?>" >
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 control-label" for="message">Text</label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						<textarea cols="30" style="width:100%;"  rows="10" name="message" id="message" class="mceEditor"></textarea>						
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 control-label" for="note_for_next_month">Note for next month</label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						<textarea cols="30" style="width:100%;"  rows="5" name="note_for_next_month" id="note_for_next_month"></textarea>						
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-xs-12 col-sm-5 col-md-3 col-form-label" for=""></label>
					<div class="col-xs-12 col-sm-7 col-md-9 col-lg-8 col-xl-8">
						
						<div id="uploader_div" ></div>
						
						<input type="hidden" name="pdffile" id="pdffile" value="" />
						<p  class="form-control-static"><span class="glyphicon glyphicon-paperclip"></span><span id="attachment">.pdf</span>
						
						
						
						&nbsp;<button type="submit" class="btn btn-primary">Send</button>
						</p>
					</div>
				</div>
				
						
		</div>
		
		<div class="col-sm-12 col-md-7 col-lg-7 col-xl-6 well" id="pdf_area" style="min-height:900px;">
		
		
			
		</div>
		<input type="hidden" value="" name="month" id="month" />
		<input type="hidden" value="" name="property" id="property" />
		<input type="hidden" value="" name="due_now" id="due_now" />
		<input type="hidden" value="" name="management_fee" id="management_fee" />
		<input type="hidden" value="" name="total_due" id="total_due" />
		
	</form>
	
<?php endif;?>	
	</div><!--row-->
</div>	



<script>
tinymce.init({
   // selector: "mceEditor",
     //plugins : "fullpage",
    theme: "modern",
	 mode : "specific_textareas",
     editor_selector : "mceEditor",
    menubar:false,
    statusbar: false,
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

<script> 
$(document).ready(function()
{		
	document.getElementById("email_form").reset();
	$(document).delegate('select[id=property_id]','change',function (event) 
	{		
		id=$(this).val();
		var flag;
		var client_email="";
		var filePath="";		
		var host='<?php echo $filepath?>';
		if (id!="")
		{				
			$(".modal").css ('display','flex');
			$.ajax({
			url:"ajax_get_values.php",
			type:"POST",
			data:{id:id},
			async:false,
			success:function(responseText)
			{			
				responseText	= $.parseJSON(responseText);
				// console.log(responseText);
				var due_now=responseText.due_now;
				filePath=responseText.filePath;
				var management_fee=responseText.management_fee;
				var total_due=responseText.total_due;
				var month=responseText.month;
				var flag=responseText.flag;
				var revenu_net=responseText.net_income;
				var price_per_night=responseText.average_price_per_night;
				var number_of_nights=responseText.total_nights;
				var note_from_prev_month=responseText.note_from_prev_month;
				client_email=responseText.client_email;
				
				$('#note_from_prev_month').html(note_from_prev_month);
				$('#month').val(month);
				$('#property').val(id);
				$('#due_now').val(due_now);
				$('#management_fee').val(management_fee);
				$('#total_due').val(total_due);
		
				var message ="Bonjour,<br />Total à payer: "+total_due+"\u20AC<br />Revenu net: "+revenu_net+"\u20AC<br />Remplissage: "+number_of_nights+"<br />Prix moyen par nuit: "+price_per_night+"\u20AC";
				// var text = document.createTextNode(message);
				// $('#message').val(message);
				tinyMCE.get('message').setContent(message);
				
				$("#send_to").val(client_email);
				date 		= btoa(month);				
				due_now 	= btoa(due_now);
				url 		= "pdf_bill_new.php?data_encry="+date+"&data_stim="+due_now+"&pdf=";
				// alert (host+'/spread/'+url);
				if (flag==0)
				{
					$.ajax({
						type:"GET",
						async:false,
						url:url,
						success: function (result)
						{
							
						}
					});
				}
				// alert (host+'/pdf/'+filePath);
				$("#attachment").html(filePath);				
				$("#pdffile").val(filePath);
				
				$("#pdf_area").html('<embed src="'+host+'/pdf/'+filePath+'" style="width:100%;min-height:900px;" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">');
				
			}
			});
			// $(".modal").hide();
			$(".modal").css ('display','none');
		}
		else
		{
			// $('#data_table').css('display','none');
			document.getElementById("email_form").reset();
		}
		return;		
	});
});
</script>
<script>
$('#uploader_div').ajaxupload(
{
		
	
	url:"./upload_bill.php",		
	onInit: function(AU)
	{
		this.uploadFiles.hide();//Hide upload button
		this.removeFiles.hide();//hide remove button
	},
	beforeUploadAll: function(files)
	{
		//validate form. this validation will take 
		//place only if any file has been selected for upload
		var send_to=$('#send_to').val();
		var property_id=$('select[id=property_id]').val();
		var title=$('#title').val();
		// var message=$('#message').val();	
		var message=tinyMCE.get('message').getContent();	
		var pdffile=$('#pdffile').val();
		
		if (send_to=='' || property_id=='' || title=='' || title=='' || message=='' || pdffile=='')
		{
			
			alert ('Please fill required parameters');
			return false;
		}
		
		return true;
	},
	autoStart:false,
	removeOnSuccess:false,
	previews:       false,
	checkFileExists:false,
	form:'parent',
	remotePath:'<?php echo $system_path?>',
	
});
	
</script>

	</body>
</html>