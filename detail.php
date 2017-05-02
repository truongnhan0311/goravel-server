<?php 
require_once('../connect.php');

require_once('../functions_general.php');

@session_start();

if( !isset($_SESSION['admin']) ) {

header('location: /spread/index.php');



}



$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);



if(isset($_POST['btn_submit']))

{

	$check_in_person = security(intval($_POST['check_in_person']));

	$cleaning_person = security(intval($_POST['cleaning_person']));

	$check_out_time = security($_POST['check_out_time']);

	$check_in_time = security($_POST['check_in_time']);

	$note_cleaning = trim($_POST['note_cleaning']);
	$note_check_in = trim($_POST['note_check_in']);

	$res_no = security($_POST['res_no']);

	$prid_sub = intval($_POST['prid_sub']);

	

	$check_r_no = $mysqli->query("SELECT id from gcal_imports where booking_number='".$res_no."'");

	if($check_r_no->num_rows > 0) 

	{



		$cost_cleaning = '';

		$cost_checkin = '';

		$fetchCost = $mysqli->query("SELECT keybox,cost_cleaning,cost_checkin,cost_late_checkout from properties where id='".$prid_sub."'");

		if($fetchCost->num_rows > 0) { 

		$row = $fetchCost->fetch_assoc();

		$cost_cleaning = $row['cost_cleaning'];

		$cost_checkin = $row['cost_checkin'];

		}

		

		$mysqli->query("UPDATE gcal_imports SET check_in_person='".$check_in_person."',check_in_time='".$check_in_time."',".

		"check_out_time='".$check_out_time."',cleaning_person='".$cleaning_person."',note_cleaning='".$note_cleaning."',note_check_in='" . $note_check_in . "'  where booking_number='".$res_no."'");

	

		$fetch_payment = $mysqli->query("SELECT pay_id from payments where reservation_no='".$res_no."'");

		if($fetch_payment->num_rows > 0) 

		{ 

			$mysqli->query("delete from payments where reservation_no='".$res_no."'");

		}

		if($check_in_person>0)

		{

			$amount = -15;

			if(is_numeric($cost_checkin)){

				$amount = $cost_checkin;

			}

			

			$mysqli->query("INSERT INTO payments SET reservation_no='".$res_no."',employee_id='".$check_in_person."',".

			"task='Check in',amount=".$amount.",property=".$prid_sub);

		}

		if($cleaning_person>0)

		{

			$amount = -20;

			if(is_numeric($cost_cleaning)){

				$amount = $cost_cleaning;

			}

			$mysqli->query("INSERT INTO payments SET reservation_no='".$res_no."',employee_id='".$cleaning_person."',".

			"task='Cleaning',amount=".$amount.",property=".$prid_sub);

		}

	}

	



	header('location: spread.php');



}



function check_null($value)



{



if(is_null($value) || ($value == NULL) || ($value == 'NULL'))



{



return '';



}



else



{



return $value;



}







}



?>



<!DOCTYPE html>



<html lang="en">



<head>
    	<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
   		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    	<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
    	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    	<script src="jquery.timepicker.min.js"></script>
		<link href="jquery.timepicker.css" rel="stylesheet">
		<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
		<style>
            .green_td {  background-color: #acfa58 !important;  max-width: 1%;}
            .blue_td { background-color:#58D3F7 !important}
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
            .form-horizontal #loading {display: inline-block;  float: left; margin-left: 5px;}
            .green_class{background-color:green !important;}
            .white_class{background-color:#fff !important;}
            .checkin_agian td{background-color: #FF8000 !important;}
			.check_in_tomorrow td.cleaning_persion {background-color: #CEE3F6 !important;}
			.in_apartment td.cleaning_persion {background-color: #CEE3F6 !important;}
			.check_in_today td.cleaning_persion {background-color: #CEE3F6 !important;}
			.check_out_today td.cleaning_persion {background-color: #CEE3F6 !important;}
			.cleaning_persion {background-color: #CEE3F6 !important;}
			
			.check_in_tomorrow td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.in_apartment td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.check_in_today td.maxNight{background-color: rgb(255, 128, 0) !important;}
			.check_out_today td.maxNight{background-color: rgb(255, 128, 0) !important ;}
			.maxNight{background-color: rgb(255, 128, 0)!important;}
			.interest{background-color:#D0A9F5;}
			.spreadlogo {width: 34px; padding-left: 5px;}
			
			.symbol a{ text-decoration:none !important; color:#000 !important}
			
			
			/*style edited by nazir for removing padding*/
			
			.mytable > thead > tr > th, .mytable > tbody > tr > th, .mytable > tfoot > tr > th, .mytable > thead > tr > td, .mytable > tbody > tr > td, .mytable > tfoot > tr > td
			{
				padding-left: 4px !important;
				padding-right: 0px !important;	
				border-top: 1px solid #ddd;
				line-height: 1.42857;
				padding-top: 8px;
				padding-bottom: 8px;
				vertical-align: top;				
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







if(isset($_GET['b']) && $_GET['b']!='')



{



	$fetchRoom = $mysqli->query("SELECT * from gcal_imports where booking_number='".$_GET['b']."'");



	if($fetchRoom->num_rows > 0) { 



	



			$row = $fetchRoom->fetch_assoc();



			?>



    <form action='' method='post'>



      <table class='table table-striped'>



        <tr>



          <th width="50%">Name</th>



          <td><?php echo $row["name"]?></td>



        </tr>



        <tr>



          <th>Reservation number</th>



          <td><?php echo $row["booking_number"]?></td>



        </tr>



        <tr>



          <th>Check in</th>



          <td><?php echo $row["check_in"]?></td>



        </tr>



        <tr>



          <th>Check out</th>



          <td><?php echo $row["check_out"]?></td>



        </tr>



        <tr>



          <th>Email</th>



          <td><a href="javascript:void(0)"  class="edit form-control" data-type="text" data-pk="<?php echo $_GET['b'];?>" data-name="email" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="email"  data-display="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['email']);?>" value="<?php echo $row["email"]?>"></a></td>



        </tr>

		

		<tr>



          <th>Country</th>



          <td>

		  	<a href="javascript:void(0)"  class="edit form-control" data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="country" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Country"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['country']);?>" value="<?php echo check_null($row["country"]);?>"></a>

		  </td>
        </tr>
		<tr>
          <th>Guests number</th>

          <td>

		  <a href="javascript:void(0)"  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="guests_number" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Guests Number"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['guests_number']);?>" value="<?php echo check_null($row["guests_number"]);?>"></a>

		  </td>



        </tr>
<tr><th>Parse booking</th><td>		
	            <a href="gmail_content.php?parseBooking=<?php echo $_GET['b']; ?>&name=<?php echo trim(strstr($row["name"],' ',true),' ') ?> "  class="btn btn-info">parse this booking</a></td>		
	            		
	        </tr>


        <tr>



          <th>Phone number</th>



          <td>

		 <!--  <input  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="phone_number" data-placement="bottom" data-send="always" data-url="/ajax_detail.php" data-original-title="Phone number"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["phone_number"]);?>"value="<?php echo check_null($row["phone_number"]);?>">-->

		  <a href="javascript:void(0)"  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="phone_number" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Phone number"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['phone_number']);?>" value="<?php echo check_null($row["phone_number"]);?>"></a>

		  </td>

        </tr>
		
		<!--new field displayed here edited by Nazir communication_way-->
		<tr>
          <th>Communication Way</th>
          <td>
		  <?php echo check_null($row["communication_way"]);?>
		  </td>
        </tr>
		<!--end of new field communication_way-->
		
        <tr>
          <th>Property</th>



          <td>



          <?php 

		   	$fetchpro = $mysqli->query("SELECT name,keybox from properties where id=".intval($row["property_id"]));


	if($fetchpro->num_rows > 0) { 

		$row_pro = $fetchpro->fetch_assoc();

		   	echo $row_pro["name"];



	}



			?></td>



        </tr>



        <tr>



          <th>Check in time</th>



          <td>

         <select name="check_in_time" id="check_in_time" onchange="ajaxUpdate('<?php echo $_GET['b']?>','check_in_time',this.value)" class="form-control input-sm">



              <option value="">Select</option>



              <?php for($i=1;$i<=24;$i++) { ?>



              <?php if($i<10) { ?>



			   <option value="<?php echo '0'.($i-1).':30';?>" <?php if(('0'.($i-1).':30')==$row['check_in_time']){ echo "selected='selected'";} ?>><?php echo '0'.($i-1).':30';?></option>



			   <option value="<?php echo '0'.($i).':00';?>" <?php if(('0'.$i.':00')==$row['check_in_time']){ echo "selected='selected'";} ?>><?php echo '0'.$i.':00';?></option>



	<?php } else { ?>



	 <option value="<?php echo ($i-1).':30';?>" <?php if((($i-1).'30')==$row['check_in_time']){ echo "selected='selected'";} ?>><?php echo ($i-1).':30';?></option>



			   <option value="<?php echo ($i).':00';?>" <?php if(($i.':00')==$row['check_in_time']){ echo "selected='selected'";} ?>><?php echo $i.':00';?></option>



			   <?php } ?>



			  <?php } ?>



            </select>

			<script>

			document.getElementById('check_in_time').value = '<?php echo $row['check_in_time']?>';

			</script>

		 

		

          

            </td>



        </tr>

		<tr>
        	 <th>Check out time</th>
        

		  <td>

		   <select name="check_out_time" id="check_out_time" onchange="ajaxUpdate('<?php echo $_GET['b']?>','check_out_time',this.value)" class="form-control input-sm">



              <option value="">Select</option>



             <?php for($i=1;$i<=24;$i++) { ?>



              <?php if($i<10) { ?>



			   <option value="<?php echo '0'.($i-1).':30';?>" <?php if(('0'.($i-1).':30')==$row['check_out_time']){ echo "selected='selected'";} ?>><?php echo '0'.($i-1).':30';?></option>



			   <option value="<?php echo '0'.($i).':00';?>" <?php if(('0'.$i.':00')==$row['check_out_time']){ echo "selected='selected'";} ?>><?php echo '0'.$i.':00';?></option>



	<?php } else { ?>



	 <option value="<?php echo ($i-1).':30';?>" <?php if((($i-1).'30')==$row['check_out_time']){ echo "selected='selected'";} ?>><?php echo ($i-1).':30';?></option>



			   <option value="<?php echo ($i).':00';?>" <?php if(($i.':00')==$row['check_out_time']){ echo "selected='selected'";} ?>><?php echo $i.':00';?></option>



			   <?php } ?>



			  <?php } ?>



            </select>

			<script>

			document.getElementById('check_out_time').value = '<?php echo $row['check_out_time']?>';

			</script>

		  

            </td>



        </tr>


		<?php if ($row_pro["keybox"] != "YES"):?>
        <tr>



          <th>Check in person</th>



          <td>  <select name="check_in_person" onchange="ajaxUpdate('<?php echo $_GET['b']?>','check_in_person',this.value)" class="form-control input-sm">



              <option value="">Select</option>



              <?php 



				$fetchemp = $mysqli->query("SELECT * from employee where status='YES'");



				if($fetchemp->num_rows > 0) {



					while($row_emp = $fetchemp->fetch_assoc())



					{ ?>



              <option value="<?php echo $row_emp['id']?>" <?php if($row_emp['id']==$row['check_in_person']){ echo "selected='selected'";} ?>><?php echo $row_emp['name']?></option>



              <?php }



										}



								



								?>



            </select>

			</td>



        </tr>
		<?php endif;?>


        <tr>



          <th>Cleaning person</th>



          <td>

			<select name="cleaning_person"  onchange="ajaxUpdate('<?php echo $_GET['b']?>','cleaning_person',this.value)" class="form-control input-sm">



              <option value="">Select</option>



              <?php 



					$fetchemp = $mysqli->query("SELECT * from employee where status='YES'");



					if($fetchemp->num_rows > 0) {



						while($row_emp = $fetchemp->fetch_assoc())



						{ ?>



              <option value="<?php echo $row_emp['id']?>" <?php if($row_emp['id']==$row['cleaning_person']){ echo "selected='selected'";} ?>><?php echo $row_emp['name']?></option>



              <?php }



										}



								



								?>



            </select>

			</td>



        </tr>

		

		

		

		<tr id="choose_driver" style="display:none;">

          <th>Choose the driver</th>

          <td>  <select name="driving_person" onchange="ajaxUpdate('<?php echo $_GET['b']?>','driving_person',this.value)" class="form-control input-sm">

              <option value="">Select</option>

              <?php 

				$fetchemp = $mysqli->query("SELECT * from employee where status='YES' and job='taxi' ");

				if($fetchemp->num_rows > 0) {

					while($row_emp = $fetchemp->fetch_assoc())

					{ ?>

              <option value="<?php echo $row_emp['id']?>" <?php if($row_emp['id']==$row['driving_person']){ echo "selected='selected'";} ?>><?php echo $row_emp['name']?></option>

              <?php }

										}

								?>

            </select>

			</td>

        </tr>

		<tr>
        	<th>Notes Cleaning</th>
          <td>
          	<textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="note_cleaning" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Notes"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" ><?php echo check_null($row["note_cleaning"]);?></textarea>
           </td>
         </tr>
		<tr>
        	<th>Notes Check in</th>
          <td>
          	<textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="note_check_in" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Notes"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" ><?php echo check_null($row["note_check_in"]);?></textarea>
           </td>
         </tr>

		  

		  

		  

		  <tr>

          <th>Cancel a booking</th>

          <td>  <select name="booking_status" onchange="ajaxUpdate('<?php echo $_GET['b']?>','booking_status',this.value)" class="form-control input-sm">

              <option value="">Select</option>

              <option value="cancel" <?php if($row['booking_status'] == 'cancel'){ echo "selected='selected'";} ?>>Cancel a booking</option>

				 

            </select>

			</td>

        </tr>
		<tr>
         <th>Taxi service</th>
          <td>  <select name="taxi_service" onchange="ajaxUpdate('<?php echo $_GET['b']?>','taxi_service',this.value)" class="form-control input-sm">

              <option value="">Select</option>
			  
			<?php   $taxiemp = $mysqli->query("SELECT * from employee where job='taxi'");



				if($taxiemp->num_rows > 0) {



					while($taxi_row_emp = $taxiemp->fetch_assoc())



					{
						?>

              <option value="<?php echo $taxi_row_emp['name']; ?>" <?php if($taxi_row_emp['name'] == 'Sofiane'){ echo "selected='selected'";} ?>><?php echo $taxi_row_emp['name']; ?></option>

					<?php 
					
					}
				}
					?>

            </select>

			</td>

        </tr>
		<tr>
        	<th>Photo service</th>
        
          <td>  <select name="photo_service" onchange="ajaxUpdate('<?php echo $_GET['b']?>','photo_service',this.value)" class="form-control input-sm">

              <option value="">Select</option>
			
              <option value="Krystal" selected="selected" >Krystal</option>

				

            </select>

			</td>

        </tr>
		  

		  

		  

		  

         <tr class="tr_row">



          <th>Questions</th>



          <th>Anwsers</th>



        </tr>



         <tr>



          <th>Do you need a taxi from the airport/train station ? 



I can arrange one for you for 60€ up to 3 person/ 80€ for 4 or 5 persons.



If you do, write the number of your flight + name on the sign. </th>



         <td>		 

		  <a href="javascript:void(0)"  class="edit2 form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="taxi" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="taxi ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['taxi']);?>" value="<?php echo check_null($row["taxi"]);?>">

		<!--  <a href="#" class="edit2 form-control" data-type="select" data-value="<?php echo $row['taxi']?>" data-source='{"Yes":"Yes","No":"No"}' data-pk="<?php echo $_GET['b']?>" data-name="taxi" data-placement="bottom" data-send="always" data-url="/ajax_detail.php" data-original-title="Check in time"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit">	

		 <input  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="taxi" data-placement="bottom" data-send="always" data-url="/ajax_detail.php" data-original-title="taxi ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['taxi']);?>" value="<?php echo check_null($row["taxi"]);?>" >-->

		  </a>

		 </td>



        </tr>

<?php 

	 /*  if(strtoupper($row["taxi"])=='YES')

	  {  */

	  ?>

      <tr  id="taxiDetail" style="display:none;">

      <th>

      Details for the taxi

      </th>

       <td>

	   <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="taxi_details" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Details for the taxi"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" ><?php echo check_null($row["taxi_details"]);?></textarea>

	   </td>

      </tr>

	  

	 <?php  // }

	  ?>

         <tr>



          <th>Where will you arrive in Paris ? </th>



		  <td>

		  <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="arrival_place" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Where will you arrive in Paris ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["arrival_place"]);?>"><?php echo check_null($row["arrival_place"]);?></textarea>

		  </td>



        </tr>

<tr>			
   <th>What is your train / flight number ?</th>



          <td>

		  <a href="javascript:void(0)"  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="flight_number" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="Flight number"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['flight_number']);?>" value="<?php echo check_null($row["flight_number"]);?>"></a>

		  </td>

 </tr>

        <tr>



          <th>What time is you flight/train in ?</th>



		    <td>

			<!--<input  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="arrival_time" data-placement="bottom" data-send="always" data-url="/ajax_detail.php" data-original-title="What time is you flight/train in ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["arrival_time"]);?>" value="<?php echo check_null($row["arrival_time"]);?>" >-->

			<a href="javascript:void(0)"  class="edit form-control " data-type="text" data-pk="<?php echo $_GET['b']?>" data-name="arrival_time" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time is you flight/train in ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row['arrival_time']);?>" value="<?php echo check_null($row["arrival_time"]);?>"></a>

			</td>



        </tr>



         <tr>



          <th>What time will you check out of the appartment ?</th>



		   <td>

		   <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="check_out_request" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time will you check out of the appartment ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["check_out_request"]);?>" ><?php echo check_null($row["check_out_request"]);?></textarea>

		   </td>



        </tr>

 <?php if($row['property_id'] != '1') { ?>

        <tr>



          <th>I have a wifi dongle wich allows you to have wifi up to 5 devices when you are in the street/park/coffee outside of the house. I rent it for 5€/day, do you want it ?</th>



		   <td>

		    <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="mifi" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time will you check out of the appartment ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["mifi"]);?>" ><?php echo check_null($row["mifi"]);?></textarea>

		   

		   </td>



        </tr>

<?php } ?>

        <tr>



          <th>Do you have specific needs ? Baby cot, help for something in particular on arrival ? </th>



		   <td>

		   <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="specific_needs" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time is you flight/train in ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" ><?php echo check_null($row["specific_needs"]);?></textarea>

		   

		   </td>



        </tr>



        <tr>



          <th>What is the main reason of your trip ? </th>



		   <td>

		    <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="trip_reason" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time will you check out of the appartment ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["trip_reason"]);?>" ><?php echo check_null($row["trip_reason"]);?></textarea>

		   

		   </td>



        </tr>



        <tr>



          <th>Is this your first time in Paris ?</th>



		   <td>

		   <textarea  class="edit form-control input-large" data-type="textarea" data-pk="<?php echo $_GET['b']?>" data-name="first_time" data-placement="bottom" data-send="always" data-url="../ajax_detail.php" data-original-title="What time will you check out of the appartment ?"  data-display ="true" title="Click here to edit" data-mode="inline" data-onblur="submit" data-value="<?php echo check_null($row["first_time"]);?>" data-source='{"Yes":"Yes","No":"No"}'><?php echo check_null($row["first_time"]);?></textarea>

		   </td>



        </tr>
        
        
        <tr>



          <th>Select the photographer</th>



		   <td>
<?php 

//echo '<pre>'; print_r($row);

?>
		   <select name="photoshoot" onchange="ajaxUpdate('<?php echo $_GET['b']?>','photoshoot',this.value)" class="form-control input-sm">

              <option value="">Select</option>

              <option value="YES" <?php if($row['photoshoot'] == 'YES'){ echo "selected='selected'";} ?>>Yes</option>
              
                 <option value="NO" <?php if($row['photoshoot'] == 'NO'){ echo "selected='selected'";} ?>>No</option>

             

            </select>
<?php if($row['photoshoot'] == 'YES'){ echo $row['photoshoot_email'];} ?>
		   </td>



        </tr>
	<tr><th>Would you be interested in a VIP experience ?</th><td>
            <?php if(!empty($row['intereste'])){  echo $row['intereste'];?></td><?php }?></td>
            
        </tr>
     <tr>



          <td colspan="2" align="center"><input type="hidden" name="res_no" value="<?php echo $_GET['b']?>">

          <input type="hidden" name="prid_sub" value="<?php echo intval($row["property_id"])?>">



           </td>



        </tr>



      </table>



    </form>



    <?php 



		



	} else {



		?>

	

  <h3  style="color:#FF0000;">There is problem with booking number! please try again with correct booking number.</h3>

 

 

	<?php



	}



}else



{



	?>

	

  <h3  style="color:#FF0000;">There is problem with booking number! please try again with correct booking number.</h3>

 

 

	<?php



}











?>



  </div>



</div>



</body>



</html>

<script type="text/javascript">

			

			jQuery(document).ready(function() {

				

				jQuery('.edit').editable({

				   // validate: function(value) {

				      //if($.trim(value) == '') 

				       // return 'This field is required';

				   // },

				    success: function(data,newValue) {

					

					

						jQuery('#update').html(data);

						

						//location.reload();

				    },showbuttons:false

				});

				jQuery('.edit2').editable({

				   // validate: function(value) {

				      //if($.trim(value) == '') 

				       // return 'This field is required';

				   // },

				    success: function(data,newValue) {

					//alert(newValue);

					

						jQuery('#update').html(data);

						

						taxiDetailShowHide(newValue);

						/* location.reload(); */

				    },showbuttons:false

				});

				

			});

			function ajaxUpdate(pk,name,fvalue)

			{

				if((name=='booking_status') && (fvalue=='cancel') ) {

				var r = confirm("are you sure");

				if (r == true) {

				} else {

					return false;

				}

				}

				 var dataString = {'name': name,'value':fvalue,'pk':pk};

				jQuery.ajax({

					type: "POST",

					url: '../ajax_detail.php',

					data: dataString,

					success: function(data){

					

						}

					});

				

			}

			function taxiDetailShowHide(data)

			{

				if(data == 'yes') {

				$( "#taxiDetail" ).show();

				$( "#choose_driver" ).show();

				} else {

				$( "#taxiDetail" ).hide();

				$( "#choose_driver" ).hide();

				}

			}

		</script>

		<script>

		jQuery(document).ready(function() {

		var data1 = "<?php echo strtolower($row['taxi']); ?>";

				taxiDetailShowHide(data1);

				});

		</script>