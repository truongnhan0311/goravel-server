<?php
 // INCLUDE THE phpToPDF.php FILE
require_once('../connect.php');
require_once('../functions_general.php');
session_start();
if(!isset($_SESSION['admin']))
{
		header('location: index.php');
}
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$data_encry = base64_decode($_GET['data_encry']);
$data_stim = base64_decode($_GET['data_stim']);

///======================================== START MANAGEMENT FEES=====================================================//////////
$previous_month_year = $data_encry;
$stmtNew 		=	$mysqli->query("SELECT * from properties  where id='".intval($_SESSION['property_id'])."'");
if($stmtNew->num_rows > 0) { 
		while($rowsNew = $stmtNew->fetch_assoc())
		{ 
		$Pname = $rowsNew['name'];
		
		
		 }
		}
	
$monthDate=$previous_month_year ;
$file=$Pname."_".$monthDate.".pdf";
$filepath	=	"http://".$_SERVER['HTTP_HOST']."/pdf/".$file;
/*if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($filepath);
    exit;
}*/?>
<a id="downloadLink" href="<?php echo $filepath; ?>" target="_blank" 
type="application/octet-stream" download="<?php echo $file; ?>"></a>
<script>
	document.getElementById('downloadLink').click();
</script>