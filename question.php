<?php 
require_once('../connect.php');
require_once('../functions_general.php');
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BNB Stories -  Questions</title>
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,100,400italic,300italic,500,700,700italic,500italic' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<style>
textarea {
  background: transparent;
  color: white;
  resize: none;
  border: 0 none;
  width: 100%;
  font-size: 5em;
  outline: none;
  
  
      background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #d2d2d2 0%, #fefefe 100%) repeat scroll 0 0;
    border: 1px solid #b8b8b8;
    border-radius: 5px;
    color: #000;
    float: left;
    font-family: "Roboto",sans-serif;
    font-size: 15px;
    font-weight: 400;
    padding: 5px;
    width: 100%;

}
</style>
</head>

<body class="questionwrap">
<div class="questionsection">
<?php

if(isset( $_GET['b']) && $_GET['b']!='')
{


	$fetchRoom = $mysqli->query("SELECT property_id,taxi,name,booking_status,arrival_place,flight_number,email,phone_number,communication_way,intereste from gcal_imports where upper(booking_number)='".strtoupper($_GET['b'])."'");

	

	if($fetchRoom->num_rows > 0) 

	{ 

		$row = $fetchRoom->fetch_assoc();

		

		if($row['booking_status'] =='cancel')

		{ ?>
<div class="questionformarea">
  <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
    <h1  style="color:#FF0000;"><b>Wrong booking number!</b></h1>
  </div>
</div>
<?php } else {

	if($row['arrival_place']!='')
		{
			
			header('location:answer.php?b='.$_GET['b']);
			
		}
		$wifi_status ='';
		$driver_status = '';
		$cost_late_checkout = '0';
		$keybox='';
		$fetchProp = $mysqli->query("SELECT keybox, wifi_status,driver_status,cost_late_checkout from properties where id='".$row['property_id']."'");
		if($fetchProp->num_rows > 0) 
		{ 
		$rowProp = $fetchProp->fetch_assoc();
		$wifi_status = $rowProp['wifi_status'];
		$driver_status = $rowProp['driver_status'];
		$cost_late_checkout = $rowProp['cost_late_checkout'];
		$keybox=$rowProp['keybox'];
		}
?>
<?php
if(($row['cat_id'] == '1') || ($row['cat_id'] == '2')) 
{
	//form for Los Angeles
?>
<form id="form_876157" name="form_876157" class="appnitro"  method="post" action="submit.php" onsubmit="return validate_form()">

  <div class="questionformarea">
    <div class="questionhead">
      <h1>Hello <span class="yellow"><strong><?php echo $row['name']?> </strong></span> , welcome to my place in Los Angeles !</h1>
      <p>I would like to get to know you and your needs to make your stay the best possible !</p>
    </div>
    <div class="questionsarea">
      <ul class="questionlist">
        <li>
          <div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> What time will you arrive ? </span><br />
                Check in is after 4PM, early check in might be allowed for some cases.<br />
				<?php if ($keybox !='YES'):?>				
                Late check in between 9PM and 11PM : 15€ extra fees.<br />
                Check in after 11PM : 15€ + 15€ taxi fees = 30€. 
				<?php endif;?>
				</p>
            </div>
            <div class="width100">
              <input id="arrival_time" name="arrival_time" class="qinput1" type="text" maxlength="255" value=""  />
            </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Do you have a car you need to park in my parking spot ?</span><br />
              </p>
            </div>
            
            <!--<div class="width100">

  <input id="taxi_1" name="taxi" class="qinput1" type="text"  maxlength="255" value=""  />

  </div>-->
            
            <div class="width100" id="taxi_box">
              <input  id="taxi_1" name="taxi" class="element radio " type="radio" value="Yes"   style="display:inline-block !important"  />
              <label class="choice radio_btn" for="taxi_1" >Yes</label>
              <input  id="taxi_2" name="taxi" class="element radio " type="radio" value="No"   style="display:inline-block !important"  >
              <label class="choice radio_btn" for="taxi_2"  >No</label>
            </div>
          </div>
        </li>
		
        <li>
          <div class="iconsec"><img src="../images/q4.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">What time will you check out of the apartment ? </span><br />
                Late check out fee is <?php echo $cost_late_checkout; ?>€ in cash on arrival. 4PM is the latest you can check out. You can't leave your luggage in the appartement once 
                
                you check out.</p>
            </div>
            <div class="width100">
              <select  class="qinput2" id="check_out_request" name="check_out_request" >
                <option value="" selected="selected"></option>
                <option value="Before 8 am" >Before 8 am</option>
                <option value="9 am" >9 am</option>
                <option value="10 am" >10 am</option>
                <option value="11 am" >11 am</option>
                <option value="I need to request a late check out" >I need to request a late check out for <?php echo $cost_late_checkout; ?>€</option>
              </select>
              <input type="hidden" name="cost_late_checkout" value="<?php echo $cost_late_checkout; ?>" />
            </div>
          </div>
        </li>
        <li> IMPORTANT : On the next page, you will have all the informations for your check in, please READ IT carefully and PRINT IT. </li>
        <li>
          <input type="hidden" name="form_id" value="876157" />
          <input type="hidden" name="reservation_number" value="<?php echo isset($_GET['b']) ? htmlentities($_GET['b']) : ''; ?>"/>
          <input id="saveForm" class="questionbtn" type="submit" name="submit" value="Next" />
        </li>
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</form>
<?php 
	//end of form for Los Angeles
} 
else	
{ 
	//form for Paris
?>
<form id="form_876157" name="form_876157" class="appnitro"  method="post" action="submit.php" onsubmit="return validate_form()">
  <div class="questionformarea">
    <div class="questionhead">
      <h1>Hello <span class="yellow"><strong><?php echo $row['name']?></strong></span> , welcome to my place in Paris !</h1>
      <p>I would like to get to know you and your needs to make your stay the best possible !</p>
    </div>
    <div class="questionsarea">
      <ul class="questionlist">
        
        <li>
          <div class="iconsec"><img src="../images/q8.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Where will you arrive in Paris ?</span></p>
            </div>
            <div class="width100">
              <select  class="qinput2" id="arrival_place" name="arrival_place" onChange="return showhideit(this.value);"  >
                <option value="" selected="selected"></option>
                <option value="CDG airport" >CDG airport</option>
                <option value="Orly airport" >Orly airport</option>
                <option value="Gare du Nord" >Gare du Nord (Eurostar, Thalys...)</option>
                <option value="Gare de l'Est" >Gare de l'Est</option>
                <option value="Gare de St Lazare" >Gare de St Lazare</option>
                <option value="Gare d'Austerlitz" >Gare d'Austerlitz</option>
                <option value="Gare de Bercy" >Gare de Bercy</option>
                <option value="Gare de Lyon" >Gare de Lyon</option>
                <option value="Gare Montparnasse" >Gare Montparnasse</option>
                <option value="I arrive by car" >I arrive by car</option>
                <option value="Other" >Other</option>
              </select>
            </div>
            <div style="display:none;" id="showhideit1">please define in the next field with your arrival time</div>
          </div>
        </li>
        <!--this question is displayed if arrived by car-->
				
		<li id="car_arrival_time" style="display:none">
			<div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
			<div class="qcontent">
				<div class="width100">
					<div class="width100">
					  <p><span class="mainheading">What time do you want to set your check in? </span><br />
						Please allow enough time to go through traffic and find a parking spot, to prevent the check in person from waiting for you for too long</p>
					</div>
				</div>
				<div class="width100">
				  <input id="arrival_time1" name="arrival_time" class="qinput1" type="text" maxlength="255" value="" />
				</div>
			</div>
		</li>
		
		<!--
		<li id="car_need_drive" style="display:none">
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p>
				From airports (CDG/Orly) the sedan is 60€ up to 3 person OR the van is 80€ for up to 6 persons.<br />
				A driver with a sign with your name will wait for you right after the luggage pick up. He will help you with your luggage at the airport and up the stairs at the appartement. Driver already knows how to access the appartement and will call me in case of any problem so you will NOT PAY LATE FEES if your plane is delayed.<br />
				Uber flat rate from the airport is 50€.<br />
				Public Transport from the airport is about 12€/person.<br />
				If you have heavy luggage or arrive after dark, I recomend to not use public transportation.<br />
				Please write<br />
              </p>
            </div>
            <div class="width100" id="taxi_box2">
				<textarea id="taxi_details" name="taxi_details" class="element text medium"   rows="10" cols="90"  ></textarea>
            </div>
          </div>
        </li>
		-->
		<!--//end of questions displaed for arrive by car-->
		
		
        <li id="train_flight_number">
          <div class="iconsec"><img src="../images/plane-tickets.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">  What is your train / flight number ?  </span><br />
                It's important so I can check if your flight/train is late !<br />
              
            </div>
            <div class="width100">
              <input id="flight_number" name="flight_number" class="qinput1" type="text" maxlength="255" value="" />
            </div>
          </div>
        </li>
        
        <li id="train_flight_arrive_time">
          <div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> What time does your flight/train arrive ? </span><br />
                Check in is after 4PM, early check in might be allowed for some cases.<br />
                <?php if ($keybox !='YES'):?>				
				Late check in between 9PM and 11PM : 15€ extra fees.<br />
                Check in after 11PM : 15€ + 15€ taxi fees = 30€. </p>
				<?php endif;?>
            </div>
            <div class="width100">
              <input id="arrival_time" name="arrival_time" class="qinput1" type="text" maxlength="255" value=""  />
            </div>
          </div>
        </li>
        <?php

  if($driver_status == 'YES') 
  { 

  ?>
        <li id="train_flight_need_drive" style="display:none">
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Do you need a driver from the airport/train station ? </span><br />
                <img src="../images/Sedan.jpg" alt="Sedan" style="width:341px; height:150px;" /> <img src="../images/Van.jpg" alt="Sedan" style="width:315px; height:150px;" /> <br />
                From airports (CDG/Orly) the sedan is 60€ up to 3 person OR the van is 80€ for up to 5 persons.<br />
				A driver with a sign with your name will wait for you right after the luggage pick up. He will help you with your luggage at the airport and up the stairs at the appartement. Driver already knows how to access the appartement and will call me in case of any problem so you will NOT PAY LATE FEES if your plane is delayed.<br />
				Uber flat rate from the airport is 50€.<br />
				Public Transport from the airport is about 12€/person.<br />
				If you have heavy luggage or arrive after dark, I recomend to not use public transportation.<br />
				
				</p>
            </div>
            <div class="width100" id="taxi_box">
              <input id="taxi_1" name="taxi" class="element radio " type="radio" value="Yes"  onClick="Show_yes('Yes');" style="display:inline-block !important"   />
              <label class="choice radio_btn" for="taxi_1" onClick="Show_yes('Yes');">Yes</label>
              <input id="taxi_2" name="taxi" class="element radio " type="radio" value="No"  onClick="Show_yes('No');"  style="display:inline-block !important"  >
              <label class="choice radio_btn" for="taxi_2" onClick="Show_yes('No');" >No</label>
            </div>
          </div>
        </li>
<?php 
	} 
?>
        <li id="li_9"  style="display:none">
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Please write : </br>
                - the number of your flight </br>
                - where the flight is from </br>
                - number of persons in the car including children/babies </br>
                - the type of car : the sedan or the van (3 people with many luggage or 4 or 5 people), please do not book the sedan if you have too many luggage or people : book the van. Children count. </br>
                - If you need a child seat please write it here</span></p>
            </div>
            <div class="width100">
              <textarea id="taxi_details" name="taxi_details" class="element text medium"   rows="10" cols="90"  ></textarea>
            </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/q4.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">What time will you check out of the apartment ? </span><br /><!--replaced the dynamic value of cost_late_checkout-->
                Normal check out is before 11AM.<br />
You CAN NOT leave your luggage in the appartement once you check out. <br />
Late check out fee is 40€ in cash on arrival. <br />
Please message me on Airbnb to tell me what time you would like to check out if you book a late check out. I will let you know if it's possible.</p>
            </div>
            <div class="width100">
              <select  class="qinput2" id="check_out_request" name="check_out_request"  >
                <option value="" selected="selected"></option>
                <option value="Before 8 am" >Before 8 am</option>
                <option value="9 am" >9 am</option>
                <option value="10 am" >10 am</option>
                <option value="11 am" >11 am</option>
                <option value="I need to request a late check out" >I need to request a late check out for <?php echo $cost_late_checkout; ?>€</option>
              </select>
              <input type="hidden" name="cost_late_checkout" value="<?php echo $cost_late_checkout; ?>" />
            </div>
          </div>
        </li>
        <?php
  if($wifi_status == 'YES') { 
  ?>
        <li>
          <div class="iconsec"><img src="../images/q6.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">I have a wifi dongle which allows you to have wifi up to 5 devices when you are in the street/park/coffee 
                
                outside of the house. I rent it for 5€/day (pay cash on arrival), do you want it ?</span></p>
            </div>
            <div class="width100" id="mifi_box">
              <input name="mifi" type="radio" value="yes"  />
              Yes
              <input name="mifi" type="radio" value="no" />
              No </div>
          </div>
        </li>
        <?php } ?>
        <li>
          <div class="iconsec"><img src="../images/q7.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Please tell me more about your stay so I can tailor my preparation for you:</span><br />
			  What brings you to Paris ? What made you chose this apartment? Do you have any special needs like baby bed, babysitting, champagne in the fridge?</p>
            </div>
            <div class="width100">              
			  
			   <textarea id="specific_needs" name="specific_needs" class="element text medium"   rows="3" cols="90"  ></textarea>
			  
            </div>
          </div>
        </li>        
        <li>
          <div class="iconsec"><img src="../images/star.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> Would you be interested in a VIP experience ? <br /></span>If you select one of those activities, I'll contact you to talk about it, this is not a final booking, and you will only pay to the activity provider the day of the activity</p>
            </div>
            <div class="width100" id="intereste">
              <input id="intereste1" name="intereste[]" class="intereste" type="checkbox"  value="flying over Versailles and around Paris with a private pilote (300€)"/>flying over Versailles and around Paris with a private pilote (300€)<br />

              <input id="intereste2" name="intereste[]" class="intereste" type="checkbox"  value="having a english or french speaking nanny (12€/hour)"  />having a english or french speaking nanny (12€/hour)<br />
               <input id="intereste3" name="intereste[]" class="intereste" type="checkbox"  value="photoshoot by a pro photographer (95€)"/>photoshoot by a pro photographer (95€)<br />
                <input id="intereste4" name="intereste[]" class="intereste" type="checkbox"  value="Grocery shopped per your request before you arrive (15€+receipt price)"/>Grocery shopped per your request before you arrive (15€+receipt price)<br />
                 <input id="intereste5" name="intereste[]" class="intereste" type="checkbox"  value="Breakfast dellivery (12€/person)"/>Breakfast dellivery (12€/person)<br />
                  <input id="intereste6" name="intereste[]" class="intereste" type="checkbox"  value="Personalized tour of Paris with a professional guide (40€)"/>Personalized tour of Paris with a professional guide (40€)<br />
                   <input id="none" name="intereste[]" class="" type="checkbox"  value="none"/>none
            </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/email_black.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> What email can I message you on ?</span></p>
            </div>
            <div class="width100">
              <input id="email" name="email" class="qinput1" type="text" maxlength="255" value=""  />
            </div>
          </div>
        </li>
		
		<!--new question value stored in -->
		<li>
          <div class="iconsec"><img src="../images/hand-graving-smartphone.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> I will reach out to you via Airbnb or Email. But if there is something urgent, I will reach out to you on<br /> <?php echo $row['phone_number']?></span><br />
			  If this phone number won't work in France, if you prefer me to use Viber/Whatsapp/Telegram/Imessage please let me know what's the easiest for you :
			  </p>
            </div>
            <div class="width100">
              <input id="communication_way" name="communication_way" class="qinput1" type="text" maxlength="255" value="<?php echo $row['communication_way']?>"  />
            </div>
          </div>
        </li>
		
        <li> IMPORTANT : On the next page, you will have all the informations for your check in, please READ IT carefully and PRINT IT. </li>
        <li>
          <input type="hidden" name="form_id" value="876157" />
          <input type="hidden" name="reservation_number" value="<?php echo isset($_GET['b']) ? htmlentities($_GET['b']) : ''; ?>" />
          <input id="saveForm" class="questionbtn" type="submit" name="submit" value="Next" />
        </li>   
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</form>
<?php 
	//end of form for Paris
} 

?>
<div class="clear"></div>
<?php
	} }else
	{
	?>
<div class="questionformarea">
<div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
  <h1  style="color:#FF0000;"> <b>There is problem with booking number! please try again with correct booking number.</b></h1>
</div>
<?php
	}
}else
{
?>
<div class="questionformarea">
  <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
    <h1  style="color:#FF0000;"><b>There is problem with booking number! please try again with correct booking number.</b></h1>
  </div>
  <?php
}
?>
</div>
<script type="text/javascript">
function Show_yes(vals)
{
	if(vals=='Yes')
	{
		document.getElementById("li_9").style.display="block";
	}else	{
		document.getElementById("li_9").style.display="none";
	}
}
function show_hide(vals)
{
	if(vals=='Yes')
	{
		document.getElementById("photoshoot_email").style.display="block";
	}else
	{
		document.getElementById("photoshoot_email").style.display="none";
	}
}
function validate_form()
{	
	var arrival_place= $('#arrival_place').val();
	var flight_number= $('#flight_number').val();
	var check_out_request= $('#check_out_request').val();
	var specific_needs= $('#specific_needs').val();
	var email= $('#email').val();
	
	if (arrival_place!='Other' && arrival_place!='I arrive by car')
	{
		var arrival_time= $('#arrival_time').val();
	}
	else
	{
		var arrival_time= $('#arrival_time1').val();
	}
	
	
	var taxi_details= $('#taxi_details').val();	
	var status = true;	
	
	if(arrival_place.trim()=='')
	{
		status = false;
		$('#arrival_place').css('border', '1px solid red'); 
		$('#arrival_place').addClass('focus'); 
		
	}
	if(flight_number.trim()=='' && arrival_place!='Other' && arrival_place!='I arrive by car')
	{
		status = false;
		$('#flight_number').css('border', '1px solid red'); 
		$('#flight_number').addClass('focus');
		
	}
	if(check_out_request.trim()=='')
	{
		status = false;
		$('#check_out_request').css('border', '1px solid red'); 
		$('#check_out_request').addClass('focus'); 		
	}
	if(specific_needs.trim()=='')
	{
		status = false;
		$('#specific_needs').css('border', '1px solid red'); 
		$('#specific_needs').addClass('focus');
		
	}
	
	if(arrival_time.trim()=='')
	{
		status = false;
		$('#arrival_time').css('border', '1px solid red'); 
		$('#arrival_time').addClass('focus'); 		
	}
	if(email.trim()=='')
	{
		status = false;
		$('#email').css('border', '1px solid red'); 
		$('#email').addClass('focus'); 
	}
	
	
	if($('input[name=taxi]:checked').length<=0  && arrival_place!='Other' && arrival_place!='I arrive by car')
	{		
		status=false;
		$('#taxi_box').css('border', '1px solid red');
		$('#taxi_box').addClass('focus');
	}			
	else if ($("input[id='taxi_1']:checked").val()=='Yes' && $('#taxi_details').length  && arrival_place!='Other' && arrival_place!='I arrive by car')
	{		
		if(taxi_details.trim()=='')
		{
			status = false;		
			$('#taxi_details').css('border', '1px solid red'); 	
			$('#taxi_details').addClass('focus');
		}
	}
	if($("input[name='intereste[]']:checked").length<=0)
	{
	
		status = false;	
		$('#intereste').css('border', '1px solid red'); 
		$('#intereste').addClass('focus');
		
	}
	
	<?php if($wifi_status == 'YES'):?>
	if($('input[name=mifi]:checked').length<=0)
	{
		status=false;
		$('#mifi_box').css('border', '1px solid red');
		$('#mifi_box').addClass('focus');
	}	
	<?php endif;?>

	if(status==false)
	{
		alert('All fields are required ! Make sure form is fill properly')		
		$(".focus:first").focus();
		return false;
	}
}
function showhideit(data) {

	
	if(data == 'Other') 
	{
		document.getElementById("showhideit1").style.display="block";
	} else 
	{
		document.getElementById("showhideit1").style.display="none";
	}
	
	if (data=='I arrive by car' || data == 'Other')
	{
		document.getElementById("train_flight_number").style.display="none";
		document.getElementById("train_flight_arrive_time").style.display="none";
		document.getElementById("train_flight_need_drive").style.display="none";		
		document.getElementById("li_9").style.display="none";		
		
		document.getElementById("car_arrival_time").style.display="block";		
		// document.getElementById("car_need_drive").style.display="block";		
		// document.getElementById("car_late_checkout").style.display="block";		
	}
	else
	{
		document.getElementById("train_flight_number").style.display="block";
		document.getElementById("train_flight_arrive_time").style.display="block";
		document.getElementById("train_flight_need_drive").style.display="block";
		
		document.getElementById("car_arrival_time").style.display="none";	
		// document.getElementById("car_need_drive").style.display="none";	
		// document.getElementById("car_late_checkout").style.display="none";	
		
		if (document.getElementById("taxi_1").checked == true)
		{
			document.getElementById("li_9").style.display="block";
		}else	{
			document.getElementById("li_9").style.display="none";
		}
	}
}
$(document).ready(function()
{
	

	$("#arrival_place").val($("#arrival_place option:first").val());


	$("select").change(function () 
	{
		$(this).css('border-color', '#b8b8b8');
		$(this).removeClass('focus');
	});
	$("textarea").change(function () 
	{
		$(this).css('border-color', '#b8b8b8');
		$(this).removeClass('focus');
	});
	$("input").change(function () 
	{
		$(this).css('border-color', '#b8b8b8');
		$(this).removeClass('focus');
	});
	
	$("input[name=mifi]").change(function () 
	{
		$('#mifi_box').css('border-color', '#b8b8b8');
		$('#mifi_box').removeClass('focus');
	});
	
	$("input[name=photoshoot]").change(function () 
	{
		$('#photoshoot_box').css('border-color', '#b8b8b8');
		$('#photoshoot_box').removeClass('focus');
	});
	
	$("input[name=taxi]").change(function () 
	{
		$('#taxi_box').css('border-color', '#b8b8b8');
		$('#taxi_box').removeClass('focus');
	});
	$("input[name='intereste[]']").change(function () 
	{
		$('#intereste').css('border-color', '#b8b8b8');
		$('#intereste').removeClass('focus');
	});
	$("input[id='none']").change(function () 
	{
		$("input[id^='intereste']").removeAttr('checked');
	});
	$("input[name='intereste[]']").change(function () 
	{
	if(document.getElementById('none').checked){
			$(".intereste").removeAttr('checked');
			//alert("Please uncheked none");
			return false		
		}
	});
	
});
</script>
</body>
</html>