<?php 

require_once('../connect.php');

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BNB Stories -  Answers</title>
<link href="../css/answer.css" rel="stylesheet" type="text/css" />
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

if(isset($_GET['b']) && $_GET['b']!='')

{

	$fetchRoom = $mysqli->query("SELECT * from gcal_imports where booking_number='".$_GET['b']."'");

	if($fetchRoom->num_rows > 0) { 

			$row = $fetchRoom->fetch_assoc();
			if($row['booking_status'] =='cancel')
		{ ?>
  <div class="questionformarea">
    <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
      <h1  style="color:#FF0000;"><b>Wrong booking number!</b></h1>
    </div>
  </div>
  <?php } else {
if( !empty($row['check_in'])) {

$cur_month = date('n', strtotime($row['check_in']));

$fetchMonth = $mysqli->query("SELECT * from weather where id='".$cur_month."'");

if($fetchMonth->num_rows > 0) {

$row_weather = $fetchMonth->fetch_assoc();

}

}



		$check_in_person_name = '';

 		$check_in_phone = '';		

					/*check in person*/ 

					 $check_in_person = intval($row["check_in_person"]);

					$check_in_person_res = $mysqli->query("SELECT * from employee where id=".$check_in_person);
					
					$driver_person_no = '';
					$driver_person_name = '';
					
					
					if($row['driving_person']) {
						$driver_person = $mysqli->query("SELECT name,phone from employee where id='".$row['driving_person']."'");                  if($driver_person->num_rows > 0)  
						{
							$driver_persons 	= $driver_person->fetch_assoc();
							$driver_person_name = $driver_persons['name'];
							$driver_person_no = $driver_persons['phone'];
						}
					} else {
						$driver_person = $mysqli->query("SELECT name,phone from employee where id='3'");
						if($driver_person->num_rows > 0)  
						{
							$driver_persons 	= $driver_person->fetch_assoc();
							$driver_person_name = $driver_persons['name'];
							$driver_person_no = $driver_persons['phone'];
						}
					}
					
					
					
					if($check_in_person_res->num_rows > 0)  

					{

						$check_in_person_res_row 	= $check_in_person_res->fetch_assoc();

						$check_in_person_name = $check_in_person_res_row['name'];

						if($check_in_person_res_row['phone']!='')

							$check_in_phone = $check_in_person_res_row['phone'];

					}



$pro_details = $mysqli->query("SELECT * from properties where id=".intval($row['property_id']));

if($pro_details->num_rows > 0)  

{

$pro_details_row 	= $pro_details->fetch_assoc();

?>
  <div class="questionformarea">
    <div class="questionhead">
      <h1>Bonjour <span class="yellow"><strong> <?php echo $row["name"] ?> </strong></span> </h1>
    </div>
    <div class="questionsarea">
      <ul class="questionlist">
	  <?php 
	  $check_in = array('6','7');
	  if( ! in_array($row['property_id'],$check_in)) { ?>
        <li>
          <div class="iconsec"><img src="../images/a3.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p> Votre check in est le <?php echo $row["check_in"] ?>, 
                <?php if( !empty($row_weather)) { ?>
                la température à Paris <?php echo date('F', strtotime($row['check_in'])); ?> est généralement entre <a target="_blank" href="http://www.holiday-weather.com/paris/forecast/" ><?php echo $row_weather['low_C']; ?>°C (<?php echo $row_weather['low_F']; ?>°F)</a> et <a target="_blank" href="http://www.holiday-weather.com/paris/forecast/" ><?php echo $row_weather['high_C']; ?>°C (<?php echo $row_weather['high_F']; ?>°F)</a>
                <?php } ?>
              </p>
            </div>
          </div>
        </li>
         
        <?php if($row["taxi"]=='Yes') { ?>
        <li>
          <div class="iconsec"><img src="../images/q1.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>Un chauffeur vous attendra après l'arrivée des bagages, si vous ne trouvez pas votre chauffeur, appelez  <?php echo $driver_person_name; ?>  au <br />
                <?php echo $driver_person_no; ?> - merci de payer le chauffeur en cash (euros).</p>
            </div>
          </div>
        </li>
        <?php } ?>
	  <?php } ?>
        <li>
          <div class="iconsec"><img src="../images/a2.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>
			    <?php if($pro_details_row['keybox'] !='YES'){ ?>
			  <?php echo $check_in_person_name; ?> vous attendra dans l'appartement aux alentours de <?php echo $row["check_in_time"] ?> merci de l'appeler au <br />
				 <?php echo $check_in_phone; ?> si vous êtes en retard ou perdu.
        <?php }else{ ?>
				L’appartement sera prêt pour vous à <?php echo $row["check_in_time"] ?>
			  <?php } ?>
                </br></br>
                
                <?php if($row["check_in_time"] == '21:00' or $row["check_in_time"] == '21:30' or $row["check_in_time"] == '22:00' or $row["check_in_time"] == '22:30'){?>
				<p>Pour le late check in, donnez 15€ à <?php echo $check_in_person_name; ?> qui vous accueille pour prendre un taxi pour rentrer comme noté dans mon annonce.<br />
           <!--     <strong>Note :</strong> Guest will give 15€ to < ?php echo $check_in_person_name; ?>. -->
                </p>		
				<?php } else if($row["check_in_time"] == '23:00' or $row["check_in_time"] == '23:30'){?>
                <p>Pour le late check in, donnez 30€ à <?php echo $check_in_person_name; ?> qui vous accueille pour prendre un taxi pour rentrer comme noté dans mon annonce.<br />
            <!--     <strong>Note :</strong>Guest will give 30€ to < ?php echo $check_in_person_name; ?>. -->
                </p>
				<?php }?>
                
						
		<?php if($pro_details_row['descriptionFR']!='') {
         echo html_entity_decode(stripslashes($pro_details_row['descriptionFR'])); 
         } ?>
				
				</p>
            </div>
          </div>
        </li>
		
       
        
        
		<?php

/* if($pro_details_row['description']!='') { ?>
        <li>
          <div class="iconsec"><img src="images/a5.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>
                <?php echo html_entity_decode(stripslashes($pro_details_row['description'])); ?> </p>
            </div>
          </div>
        </li>
        <?php } */ ?>
		
        <?php if(($row["check_out_request"] == 'I need to request a late check out' ) || ($row["check_out_request"] == 'I need to request a late check out for 40e')   )  { ?>
        <li>
          <div class="iconsec"><img src="../images/q3.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>Merci de donner <?php echo $row['cost_late_checkout'];?>€ en cash à <?php echo $check_in_person_name; ?> lors de votre check in pour le late check out. </p>
            </div>
          </div>
        </li>
        <?php } ?>
        <?php

if(strtoupper($row["mifi"])=='YES')

{

if($row['nights']>0)

{

$nights = $row['nights'];

$night = 5*$nights.'€';

} else {

$night = '[5 * NIGHTS] €';

}

?>
        <li>
          <div class="iconsec"><img src="../images/q6.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>Please give <?php echo $night; ?> in cash to <?php echo $check_in_person_name; ?> for the MIFI dongle (5€/nights * <?php echo $nights; ?> nights)</p>
            </div>
          </div>
        </li>
        <?php } ?>
        
		 <?php 
	  $check_in = array('6','7');
	  if( ! in_array($row['property_id'],$check_in)) { ?>
		<li>
          <div class="iconsec"><img src="../images/a7.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>Merci de lire ces <a href="/welcome.php?id=<?php echo intval($row['property_id'])?>">informations</a></p>
            </div>
          </div>
        </li>
	  <?php } ?>
		
        <li>
          <div class="iconsec"><img src="../images/q4.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>Le <?php echo $row["check_out"] ?>, vous devez quitter l'appartement avant <?php echo $row["check_out_time"] ?>, merci de prendre tous vos effets personnels avec vous avant de laisser les clés comme expliqué lors du check in. </p>
              
              
            </div>
          </div>
        </li>
        <li>
          <div class="iconsec"><img src="../images/a4.png" width="100" height="100" /></div>
          <div class="anscontent">
            <div class="width100">
              <p>C'est très important pour moi d'avoir une note de 5 étoiles, n'hésitez pas à me demander si vous avez besoin de quoique ce soit pendant votre séjour. </p>
            </div>
          </div>
        </li>
        
     <!--    <?php

 if($pro_details_row['descriptionFR']!='') { ?>
        <li>
          <div class="iconsec">Description FR</div>
          <div class="anscontent">
            <div class="width100">
              <p>
                <?php echo html_entity_decode(stripslashes($pro_details_row['descriptionFR'])); ?> </p>
            </div>
          </div>
        </li>
        <?php }  ?>-->
        
        
        
      </ul>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
  <?php	

}
}
} else {

?>
  <div class="questionformarea">
    <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
      <h1 style="color:#FF0000;"><b>There is problem with booking number! please try again with correct booking number. </b> </h1>
    </div>
  </div>
  <?php
}

} else {
?>
  <div class="questionformarea">
    <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
      <h1 style="color:#FF0000;"><b>There is problem with booking number! please try again with correct booking number. </b></h1>
    </div>
  </div>
  <?php

}

?>
</div>
</body>
</html>
