<?php 
require_once('../connect.php');
require_once('../functions_general.php');
$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BNB Stories -  Questions</title>
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,100,400italic,300italic,500,700,700italic,500italic' rel='stylesheet' type='text/css'>
<!--[if gte IE 9]>
  <style type="text/css">
    .gradient {
       filter: none;
    }
  </style>
<![endif]-->

</head>

<body class="questionwrap">
<div class="questionsection">
<?php
if(isset( $_GET['b']) && $_GET['b']!='')
{
	$fetchRoom = $mysqli->query("SELECT property_id,taxi,name,booking_status from gcal_imports where upper(booking_number)='".strtoupper($_GET['b'])."'");
	
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
			header('Location: answerFR.php?b='.$_GET['b']);
		}
		$wifi_status ='';
		$driver_status = '';
		$cost_late_checkout = '0';
		$fetchProp = $mysqli->query("SELECT wifi_status,driver_status,cost_late_checkout from properties where id='".$row['property_id']."'");
		if($fetchProp->num_rows > 0) 
		{ 
		$rowProp = $fetchProp->fetch_assoc();
		$wifi_status = $rowProp['wifi_status'];
		$driver_status = $rowProp['driver_status'];
		$cost_late_checkout = $rowProp['cost_late_checkout'];
		}
?>
<?php
if(($row['cat_id'] == '1') || ($row['cat_id'] == '2')) {
?>
<form id="form_876157" name="form_876157" class="appnitro"  method="post" action="submit.php?lang=FR" onsubmit="return validate_form();">
  <div class="questionformarea">
    <div class="questionhead">
      <h1>Bonjour <span class="yellow"><strong><?php echo $row['name']?> </strong></span> , bienvenue chez moi à Paris !</h1>
      <p>Je voudrais connaitre vos besoins pour que votre séjour se déroule au mieux.</p>
    </div>
    <div class="questionsarea">
      <ul class="questionlist">
        <li>
          <div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> A quelle heure arrivezWhat time will you arrive ? </span><br />
                Check in is after 4PM, early check in might be allowed for some cases.<br />
                Late check in between 9PM and 11PM : 15€ extra fees.<br />
                Check in after 11PM : 15€ + 15€ taxi fees = 30€. </p>
            </div>
            <div class="width100">
              <input id="arrival_time" name="arrival_time" class="qinput1" type="text" maxlength="255" value="" " />
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
            
            <div class="width100">
              <input id="taxi_1" name="taxi" class="element radio " type="radio" value="Yes"   style="display:inline-block !important" "  />
              <label class="choice radio_btn" for="taxi_1" >Yes</label>
              <input id="taxi_2" name="taxi" class="element radio " type="radio" value="No"   style="display:inline-block !important"  >
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
          <input type="hidden" name="reservation_number" value="<?php echo isset($_GET['b']) ? htmlentities($_GET['b']) : ''; ?>" />
          <input id="saveForm" class="questionbtn" type="submit" name="submit" value="Next" />
        </li>
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</form>
<script type="text/javascript">
function Show_yes(vals)
{
	if(vals=='Yes')
	{
		document.getElementById("li_9").style.display="block";
	}else
	{
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
	
	var taxi 			= 	document.form_876157.taxi;  
	var taxi_details 	= 	document.form_876157.taxi_details.value;  
	var arrival_place 	= 	document.form_876157.arrival_place.value; 
	var arrival_time 	= 	document.form_876157.arrival_time.value;  
	var check_out_request = document.form_876157.check_out_request.value;  
	var mifi 			= 	document.form_876157.mifi;  
	var specific_needs 	= 	document.form_876157.specific_needs.value;
 
	var trip_reason 	= 	document.form_876157.trip_reason.value;
	
	var first_time 		= 	document.form_876157.first_time;
	
	var status = true;
		console.log(mifi);
	alert(status);
	return false;	

	if(taxi[0].checked == false && taxi[1].checked == false){
		status = false;
	}else if(taxi[0].checked ==true)
	{
	
		if(taxi_details.trim()=='')
		{
			
			status = false;
		}
	}
	
	if(arrival_place.trim()=='')
	{
			status = false;
	}
	
	if(arrival_time.trim()=='')
	{
			status = false;
	}
	
	if(check_out_request.trim()=='')
	{
			status = false;
	}
	
	if(mifi[0].checked == false && mifi[1].checked == false){
		status = false;
	}
	
		
	if(specific_needs.trim()=='')
	{
			status = false;
	}
	
	
	if(trip_reason.trim()=='')
	{
			status = false;
	}
	
	if(first_time[0].checked == false && first_time[1].checked == false){
		status = false;
	}
	
	if(status==false)
	{
		alert('All fields are required ! Make sure form is fill properly')
		return false;
	}
}

function showhideit(data) {
	if(data == 'Other') {
		document.getElementById("showhideit1").style.display="block";
	} else {
		document.getElementById("showhideit1").style.display="none";
	}
}
</script>
<?php } else { ?>
<form id="form_876157" name="form_876157" class="appnitro"  method="post" action="submit.php?lang=FR" onsubmit="return validate_form()">
  <div class="questionformarea">
    <div class="questionhead">
      <h1>Bonjour <span class="yellow"><strong><?php echo $row['name']?></strong></span> , bienvenue chez moi à Paris !</h1>
      <p>Je voudrais connaitre vos besoins pour que votre séjour se déroule au mieux.</p>
    </div>
    <div class="questionsarea">
      <ul class="questionlist">
        <!--<li>
          <div class="iconsec"><img src="/images/q2.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Quelle est la raison pricipale de votre voyage ?</span></p>
            </div>
            <div class="width100">
              <select class="qinput2" id="trip_reason" name="trip_reason" >
                <option value="" selected="selected"></option>
                <option value="Family" >Famille</option>
                <option value="Romantic" >Romantique</option>
                <option value="Buisness" >Buisness</option>
                <option value="Friends" >Amis</option>
              </select>
            </div>
          </div>
        </li>-->
        <!--<li>
          <div class="iconsec"><img src="/images/q9.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Est-ce votre première fois à Paris ?</span></p>
            </div>
            <div class="width100">
              <input name="first_time" type="radio" value="Yes, First time in Paris" />
              Oui
              <input name="first_time" type="radio" value="No, I have already visited Paris, before" />
              Non </div>
          </div>
        </li>-->
        <li>
          <div class="iconsec"><img src="../images/q8.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Où arrivez vous à Paris ?</span></p>
            </div>
            <div class="width100">
              <select  class="qinput2" id="arrival_place" name="arrival_place" onChange="return showhideit(this.value);" >
                <option value="" selected="selected"></option>
                <option value="CDG airport" >Aéroport CDG</option>
                <option value="Orly airport" >Aéroport d'Orly </option>
                <option value="Gare du Nord" >Gare du Nord (Eurostar, Thalys...)</option>
                <option value="Gare de l'Est" >Gare de l'Est</option>
                <option value="Gare de St Lazare" >Gare de St Lazare</option>
                <option value="Gare d'Austerlitz" >Gare d'Austerlitz</option>
                <option value="Gare de Bercy" >Gare de Bercy</option>
                <option value="Gare de Lyon" >Gare de Lyon</option>
                <option value="Gare Montparnasse" >Gare Montparnasse</option>
                <option value="I arrive by car" >J'arrive en voiture</option>
                <option value="Other" >Autre</option>
              </select>
            </div>
            <div style="display:none;" id="showhideit1">Précisez dans le champs suivant votre mode d'arrivée, avec l'heure</div>
          </div>
        </li>
        
        <li>
          <div class="iconsec"><img src="../images/q8.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> Quel est votre numéro de train/vol  ?  </span><br />
                C’est important pour que je puisse voir si votre train/avion est en retard !<br />
              
            </div>
            <div class="width100">
              <input id="flight_number" name="flight_number" class="qinput1" type="text" maxlength="255" value="" " />
            </div>
          </div>
        </li>
        
        <li>
          <div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading"> A quelle heure votre avion/train arrive ? </span><br />
                Le Check in est après 16h, un check in plus tôt sera possible selon la disponibilité de l'appartement.<br />
                Check in entre 21h et 23h : 15€ de frais.<br />
                Check in à partir de 23h : 15€ + 15€ de taxi = 30€ de frais. </p>
            </div>
            <div class="width100">
              <input id="arrival_time" name="arrival_time" class="qinput1" type="text" maxlength="255" value="" " />
            </div>
          </div>
        </li>
        <?php
  if($driver_status == 'YES') { 
  ?>
        <li>
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Avez vous besoin d'un transport depuis l'aéroport/la gare ? </span><br />
                <img src="../images/Sedan.jpg" alt="Sedan" style="width:341px; height:150px;" /> <img src="../images/Van.jpg" alt="Sedan" style="width:315px; height:150px;" /> <br />
                Depuis les aéroport (CDG/Orly) la berline est à 60€ jusqu'à 3 personnes OU le van est à 80€ pour 4 ou 5 personnes.<br />
                Mon chauffeur a une Mercedes noire et vous attendra à votre arrivée avec un signe à votre nom. Il vous aidera avec vos bagages à l'aéroport et pour jusqu'à l'appartement.<br />
              </p>
            </div>
            <div class="width100">
              <input id="taxi_1" name="taxi" class="element radio " type="radio" value="Yes"  onClick="Show_yes('Yes');" style="display:inline-block !important"  />
              <label class="choice radio_btn" for="taxi_1" onClick="Show_yes('Yes');">Oui</label>
              <input id="taxi_2" name="taxi" class="element radio " type="radio" value="No"  onClick="Show_yes('No');"  style="display:inline-block !important"  >
              <label class="choice radio_btn" for="taxi_2" onClick="Show_yes('No');" >Non</label>
            </div>
          </div>
        </li>
        <?php } ?>
        <li id="li_9"  style="display:none">
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">S'il vous plait indiquez : </br>
                - le numéro et l'origine de votre vol </br>
                - le nombre de personnes dans la voiture en comptant les enfants/bébés</br>
                - le type de voiture : la berline ou le van (3 personnes avec beaucoup de bagages ou 4 ou 5 personnes). S'il vous plait ne réservez pas la berline si vous avez beaucoup de bagages : réservez le van.</br>
                - Si vous avez besoin d'un siège enfant, précisez le ici</span></p>
            </div>
            <div class="width100">
              <textarea id="taxi_details" name="taxi_details" class="element text medium"   rows="2" cols="50"  ></textarea>
            </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/q4.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">A quelle heure quiterez vous l'appartment ? </span><br />
                Frais de Late check out : <?php echo $cost_late_checkout; ?>€ en liquide à donner à l'arrivée. 16h est le plus tard que vous pouvez partir. Vous NE pouvez PAS laisser vos baggages dans l'appartement une fois que vous avez fais le check out.</p>
            </div>
            <div class="width100">
              <select  class="qinput2" id="check_out_request" name="check_out_request" " >
                <option value="" selected="selected"></option>
                <option value="Before 8 am" >Avant 8h</option>
                <option value="9 am" >9h</option>
                <option value="10 am" >10h</option>
                <option value="11 am" >11h</option>
                <option value="I need to request a late check out" >J'ai besoin de réserver un late check out pour <?php echo $cost_late_checkout; ?>€</option>
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
            <div class="width100">
              <input name="mifi" type="radio" value="yes" />
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
              <p><span class="mainheading"> Avez vous un besoin particulier ?</span> Lit de bébé, besoin d'aide avec quelque chose à votre arrivée, nouriture spéciale, cadeau disposé dans l'appartement avant votre arrivée ? </p>
            </div>
            <div class="width100">
              <input id="specific_needs" name="specific_needs" class="qinput1" type="text" maxlength="255" value="" " />
            </div>
          </div>
        </li>
        
        <!--<li>
          <div class="iconsec"><img src="../images/pgotoshoot.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Are you interested by a pro photoshoot in Paris ?</span></p>
            </div>
            <div class="width100">
            <img src="../images/Krystal-1.jpg" width="700" height="150" /><br />
            Krystal moved from the USA and does amazing photos in Paris. Photoshoot 
            starting at 95€.<br />
            <br />

              <input name="photoshoot" onClick="show_hide('Yes');" type="radio" value="YES" " />
              Yes, I'm intersted to get in contact with Krystal.<br />

              <input name="photoshoot" onClick="show_hide('No');" type="radio" value="NO" />
              No thank you</div>
              
              <div class="width100" id="photoshoot_email" style="display:none;" >
              Email adress <br />

              <input type="text"   name="photoshoot_email"   />
              </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/email_black.png" width="100" height="100" /></div>
          <div class="qcontent">
            <div class="width100">
              <p><span class="mainheading">Sur quel email puis-je vous joindre ?</span></p>
            </div>
            <div class="width100">
              <input id="specific_needs" name="email" class="qinput1" type="text" maxlength="255" value="" " />
            </div>
          </div>
        </li>-->
        
        <li> IMPORTANT : Sur la page suivante, vous aurez toutes les informations pour votre check in, LISEZ LE puis IMPRIMEZ LE quelques jours avant votre arrivée. </li>
        <li>
          <input type="hidden" name="form_id" value="876157" />
          <input type="hidden" name="reservation_number" value="<?php echo isset($_GET['b']) ? htmlentities($_GET['b']) : ''; ?>" />
          <input id="saveForm" class="questionbtn" type="submit" name="submit" value="Suivant" />
        </li>
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</form>
<script type="text/javascript">
function Show_yes(vals)
{
	if(vals=='Yes')
	{
		document.getElementById("li_9").style.display="block";
	}else
	{
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
	
	var taxi 			= 	document.form_876157.taxi;  
	var taxi_details 	= 	document.form_876157.taxi_details.value;  
	var arrival_place 	= 	document.form_876157.arrival_place.value; 
	var arrival_time 	= 	document.form_876157.arrival_time.value;  
	var check_out_request = document.form_876157.check_out_request.value;  
	var mifi 			= 	document.form_876157.mifi;  
	var specific_needs 	= 	document.form_876157.specific_needs.value;
	/*var trip_reason 	= 	document.form_876157.trip_reason.value;*/
	
	var first_time 		= 	document.form_876157.first_time;
	
	var status = true;
		

	if(taxi[0].checked == false && taxi[1].checked == false){
		status = false;
	}else if(taxi[0].checked ==true)
	{
	
		if(taxi_details.trim()=='')
		{
			
			status = false;
		}
	}
	
	if(arrival_place.trim()=='')
	{
			status = false;
	}
	
	if(arrival_time.trim()=='')
	{
			status = false;
	}
	
	if(check_out_request.trim()=='')
	{
			status = false;
	}
	
	/*if(mifi[0].checked == false && mifi[1].checked == false){
		status = false;
	}
	
		
	if(specific_needs.trim()=='')
	{
			status = false;
	}if(trip_reason.trim()=='')
	{
			status = false;
	}
	
	if(first_time[0].checked == false && first_time[1].checked == false){
		status = false;
	}*/
	
	if(status==false)
	{
		alert('All fields are required ! Make sure form is fill properly')
		return false;
	}
}

function showhideit(data) {
	if(data == 'Other') {
		document.getElementById("showhideit1").style.display="block";
	} else {
		document.getElementById("showhideit1").style.display="none";
	}
}
</script>
<?php } ?>
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



</body>
</html>
