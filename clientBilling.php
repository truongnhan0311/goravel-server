<?php
session_start();
error_reporting (0);
require_once ('../connect.php');
require_once('../functions_general.php');

if(!isset($_SESSION['admin'])) 
{
		header('location: index.php');
		exit;
}
$previous_month_year = date('F-Y', strtotime(date('Y-m')." -1 month"));
?>
<!DOCTYPE html>
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
	</head>
	<body>
		<div class="container-fluid table-responsive">
			<div class="row">
				<!--menu bar-->     
				<?php require_once ('admin_menu.php')?>
				<!--end of menu bar-->				
<?php
try
{
	$pdo = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// $sql = "select * from clients where active_status='YES'";
	$sql = "select id,name from properties where active_status='YES' order by name asc";
			
	$stmt = $pdo->prepare($sql);                                  
	// $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);       		   
	$stmt->execute(); 
	
	?>
	
	<form class="form-horizontal" action="" method="get">
		<div class="form-group row">				
				<div class="col-md-3 col-md-offset-1">				
					<select class="form-control" id="property_id" name="property_id">
						<option value="">Select a property</option>
			
	<?php
	
	$flag			=	0;
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) 
	{		
        // echo '<pre>' . print_r ($row,true) . '</pre>';
        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";		
    }
	
	?>
					</select>
				</div>
		</div>
		
		<div class="form-group row">
		
			<table class="table table-striped" id="data_table" style="width:95%;margin: 0 auto;">		
				
				<tbody>		
					<tr class='header'>
						<th>Billing Month</th>
						<th>cleaning/checking in fees</th>
						<th>Management Fees</th>
						<th>Total due</th>				
						<th>Status</th>
						<th></th>
						<th></th>
					</tr>
                    	<tr id="appendRow" style="display:none"></tr>
                    
						<tr id="fee_box" style="display:none"></tr>
				</tbody>
			</table>		
		</div>
	</form>
	<?php
}
catch (PDOException $e) 
{	
	echo 'Database query failed: ' . $e->getMessage();
}
?>
			
			</div><!--row-->
		</div>	
<script>
 function downloadPdf(id){
		var date;
		date 		= $('#billing_month'+id).val();
		due_now		= $('#due_now'+id).val();
		date 		= btoa(date);
		due_now 	= btoa(due_now);
		url 		= "download_bill.php?data_encry="+date+"&data_stim="+due_now+"&pdf=";
		window.open(url);
	
}
	function genrate_bills(id){
		
		var date;
		
		date 	= $('#billing_month'+id).val();
		due_now = $('#due_now'+id).val();
		date 	= btoa(date);
		due_now = btoa(due_now);
		url 	= "pdf_bill.php?data_encry="+date+"&data_stim="+due_now;
	
		window.open(url);
	}
	
    function get_pdf(i) {
        var date;
		billing_month = '.billing_month'+i;
		due_now = '.due_now'+i;
		date = $(billing_month).html();
		due_now = $(due_now).html();
		date = btoa(date);
		due_now = btoa(due_now);
		
		url = "pdf_bill.php?data_encry="+date+"&data_stim="+due_now;
		window.open(url);
    }
	 function download_pdf(i) {
        var date;
		billing_month = '.billing_month'+i;
		due_now = '.due_now'+i;
		date = $(billing_month).html();
		due_now = $(due_now).html();
		date = btoa(date);
		due_now = btoa(due_now);
		
		url = "download_bill.php?data_encry="+date+"&data_stim="+due_now;
		window.open(url);
    }

$(document).ready(function(){	
	
	var width=$('tr').eq(0).width();
	
	// alert (width);
	$('#fee_box').css('width',width);
	$(document).delegate('select[id=property_id]','change',function (event) 
	{		
		// alert ($(this).val());
		$('.temp').remove ();
		$('.appendtd').remove ();		
		$('#bill_paid').prop('disabled', false);
		$('#bill_sent').prop('disabled', false);
		id=$(this).val();
		var flag;
		if (id!="")
		{				
			$.post("ajax_get_fees.php",{id:id},
			
			function(responseText)
			{			
			
				responseText	= $.parseJSON(responseText);
				length			= 0;
				$.each(responseText, function(i,v){
						length++;
						
				});
			
			for(var j=0 ; length >= j ;j++)
			{
				$('#'+j).remove ();
					//console.log(responseText);
				if (responseText[j].success)
				{
				
					if (responseText[j].id=='' && responseText[j].status=='')
					{
						
						var tdAppend = "<tr class='appendtd' id='"+j+"'><td><input readonly type='text' id='billing_month"+j+"' value='"+responseText[j].month+"' name='billing_month[]' maxlength='6'/></td><td><input  readonly type='text' id='due_now"+j+"' value='"+responseText[j].due_now+"' name='due_now' maxlength='6'/></td><td><input  type='text' readonly id='management_fee"+j+"' value='"+responseText[j].management_fee+"' name='management_fee'/></td><td><input  type='text' readonly id='total_due"+j+"' value='"+responseText[j].total_due+"' name='total_due' /></td><td><input type='hidden' value='"+responseText[j].month+"' id='month"+j+"' name='month' /><input type='hidden' value='"+responseText[j].id+"' id='id"+j+"' name='id' />&nbsp;</td><td><input  type='button' id='bill_sent"+j+"' class='bill_sent' value='Bill Sent' name='bill_set' />                               <input type='button' class='genrate_bills'onclick='genrate_bills("+j+")' value='Genrate Bill'>                                     <input type='button' class='downloadPdf'  onclick='downloadPdf("+j+")'  value='Download Bill' style='display:none'></td><td>           <input  type='button' class='bill_paid' id='bill_paid"+j+"' value='Paid' name='bill_set'/></td></tr>";
						$(tdAppend).insertAfter('#appendRow');
						
						$('#data_table').css('display','block');
						$('#bill_paid'+j).css ('display','none');
						$('#bill_sent'+j).prop('disabled', false);
						flag = responseText[j].flag;
						if(flag ==1){
							$('#genrate_bills'+j).attr('value', "Regenrate Bill");
							$('#downloadPdf'+j).css('display','block');
						}else{
								$('#genrate_bills'+j).attr('value', "Genrate Bill");
								$('#downloadPdf'+j).css('display','none');
							
						}
						
					}
					else if (responseText[j].status=='SENT')
					{
						$('#fee_box').css('display','');
						var tdAppend = "<tr class='appendtd' id='"+j+"'><td><input readonly type='text' id='billing_month"+j+"' value='"+responseText[j].month+"' name='billing_month[]' maxlength='6'/></td><td><input  readonly type='text' id='due_now"+j+"' value='"+responseText[j].due_now+"' name='due_now' maxlength='6'/></td><td><input  type='text' readonly id='management_fee"+j+"' value='"+responseText[j].management_fee+"' name='management_fee'/></td><td><input  type='text' readonly id='total_due"+j+"' value='"+responseText[j].total_due+"' name='total_due' /></td><td><input type='hidden' value='"+responseText[j].month+"' id='month"+j+"' name='month' /><input type='hidden' value='"+responseText[j].id+"' id='id"+j+"' name='id' />&nbsp;</td><td><input  type='button' id='bill_sent"+j+"' class='bill_sent' value='Bill Sent' name='bill_set' />                               <input type='button' id='genrate_bills"+j+"' class='genrate_bills' onclick='genrate_bills("+j+")' value='Genrate Bill'>                                     <input type='button' id='downloadPdf"+j+"' class='downloadPdf'  onclick='downloadPdf("+j+")'  value='Download Bill' style='display:none'></td><td>           <input  type='button' class='bill_paid' id='bill_paid"+j+"' value='Paid' name='bill_set'/></td></tr>";
						$(tdAppend).insertAfter('#appendRow');
						
						$('#property_id').val(responseText[j].property_id);

						$('#bill_sent'+j).prop('disabled', true);
						$('#bill_paid'+j).css('display', 'block');
						
						flag = responseText[j].flag;
						if(flag ==1){
							$('#genrate_bills'+j).attr('value', "Regenrate Bill");
							$('#downloadPdf'+j).css('display','block');
							
						}else{
								$('#genrate_bills'+j).attr('value', "Genrate Bill");
								$('#downloadPdf'+j).css('display','none');
							
						}

								
								
					}
					// else if (responseText.status=='PAID')
					else if (responseText[j].status=='PAID')
					{
						$('#fee_box').css('display','none');
						flag = responseText[j].flag;
						if(flag ==1){
							$('#genrate_bills'+j).attr('value', "Regenrate Bill");
							$('#downloadPdf'+j).css('display','block');
							
						}else{
								$('#genrate_bills'+j).attr('value', "Genrate Bill");
								$('#downloadPdf'+j).css('display','none');
							
						}

					}	
				}
			
				else
				{
					alert ('could not get data');
				} 
			}               
		});
			
			
			$.post("ajax_get_old_fees.php",{id:id},
			function(responseText,status)
			{	
				if (responseText)
				{
					
					// $('#data_table > tbody:last-child').append(responseText);
					// $('#data_table > tbody> tr.header').append(responseText);
					// $('#data_table > tbody>tr.header').append(responseText);
					$('#data_table').find('#fee_box	').after(responseText)
					// $('#data_table > tbody:first-child').append(responseText);
				}              
			});
			
		}
		else
		{
			$('#data_table').css('display','none');
		}
		return;		
	});
	
	
	
	$(document).delegate('input.bill_sent','click',function (event) 
	{		
		var id 				= $(this).closest('tr').attr('id');
		var total_due		= $('#total_due'+id).val();
		var due_now			= $('#due_now'+id).val();		
		var management_fee	= $('#management_fee'+id).val();
		var property_id		= $('#property_id').val();
		var month			= $('#month'+id).val();
		
		
		$.post("ajax_save_fees.php",{total_due:total_due,due_now:due_now,management_fee:management_fee,property_id:property_id,month:month},
			function(responseText,status)
			{
				responseText=jQuery.parseJSON(responseText);
				$('#bill_paid'+id).css ('display','block');
				$('#bill_sent'+id).prop('disabled', true);
				$('#bill_paid'+id).prop('disabled', false);
				$('#id'+id).val(responseText.id);
				
				// alert (data);				
			});
			
		
	});
	
	$(document).delegate('input.bill_paid','click',function (event) 
	{		
		var tid 			= $(this).closest('tr').attr('id');
		var id 			= $('#id'+tid).val();	
		var month 		= $('#month'+tid).val();	
		var property_id	=$('select[id="property_id"]').val();		
		$.post("ajax_update_fees.php",{id:id,month:month},
		
		function(responseText,status,id)
		{
			// alert (data);
			responseText=jQuery.parseJSON(responseText);
			var month=responseText.month;
			console.log();			
			alert ("PAID FROM clientBilling properties('"+property_id+"')Virement pour "+month+" added")
			
			$('#bill_sent'+tid).prop('disabled', true);
			$('#bill_paid'+tid).prop('disabled', true);	
			$('#fee_box').css('display','none');
			
			$('#id'+tid).val(responseText.id);
			$('input[id=property_id]').val(responseText.property_id);
			$('#month'+tid).val(responseText.month);
			
			/*$.post("ajax_get_old_fees.php",{id:property_id},
			function(responseText,status)
			{	
				if (responseText)
				{				
					
					$('#data_table').find('.header').after(responseText);
				}              
			});	*/		
			// location.reload(true);
		});
		
	});
	
});
</script>
	
	</body>
</html>
