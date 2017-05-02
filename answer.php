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
            .gradient { filter: none;  }
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
					
					}else{
						return $value;
					}
			
			}
			if(isset($_GET['b']) && $_GET['b']!='')
			{
				$fetchRoom = $mysqli->query("SELECT * from gcal_imports where booking_number='".$_GET['b']."'");
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
						<?php 
					}else {
						if( !empty($row['check_in'])) {
					
							$cur_month	 = date('n', strtotime($row['check_in']));
							$fetchMonth  = $mysqli->query("SELECT * from weather where id='".$cur_month."'");
					
							if($fetchMonth->num_rows > 0) {
					
								$row_weather = $fetchMonth->fetch_assoc();
							}
						}
					
					$check_in_person_name 	=	'';
					$check_in_phone 		= 	'';		
					
					/*check in person*/ 
					
					$check_in_person		=	intval($row["check_in_person"]);
					$check_in_person_res 	=	$mysqli->query("SELECT * from employee where id=".$check_in_person);
					
					$driver_person_no		=	'';
					$driver_person_name		=	'';
					
					if($row['driving_person']) {
					
						$driver_person = $mysqli->query("SELECT name,phone from employee where id='".$row['driving_person']."'");
						
						
						if($driver_person->num_rows > 0)  
						
						{
						
							$driver_persons 	= $driver_person->fetch_assoc();
							$driver_person_name = $driver_persons['name'];
							$driver_person_no 	= $driver_persons['phone'];
						
						}
					
					
					
					} else {
					
					/*	$driver_person = $mysqli->query("SELECT name,phone from employee where id='3'");
					
						if($driver_person->num_rows > 0)  
						
						{
						
							$driver_persons 	= $driver_person->fetch_assoc();
							$driver_person_name = $driver_persons['name'];
							$driver_person_no 	= $driver_persons['phone'];
						
						}*/
						
					
					}
					
					
					
					if($check_in_person_res->num_rows > 0)  
					{
						$check_in_person_res_row 	= $check_in_person_res->fetch_assoc();
						$check_in_person_name 		= $check_in_person_res_row['name'];
						
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
                            	<h1>Hello <span class="yellow"><strong> <?php echo $row["name"] ?> </strong></span> </h1>
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
                                            	<p> Your check in is on <?php echo $row["check_in"] ?>, 
													<?php if( !empty($row_weather)) { ?>
                                                    temperature in Paris <?php echo date('F', strtotime($row['check_in'])); ?> is generally between <a target="_blank" href="http://www.holiday-weather.com/paris/forecast/" ><?php echo $row_weather['low_C']; ?>°C (<?php echo $row_weather['low_F']; ?>°F)</a> and <a target="_blank" href="http://www.holiday-weather.com/paris/forecast/" ><?php echo $row_weather['high_C']; ?>°C (<?php echo $row_weather['high_F']; ?>°F)</a>
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
                                                <p>A driver will wait for you after you took your luggage, if you can’t find your driver please call  <?php echo $driver_person_name; ?>  on <br />
                                                	<?php echo $driver_person_no; ?> - please give cash (euros) to the driver.
                                                </p>
                                  			  </div>
                                    	 </div>
                                
                                	</li>
                                	<?php 
									} 
								 } ?>
                                    <li>
                                    	<div class="iconsec"><img src="../images/a2.png" width="100" height="100" /></div>
                                        <div class="anscontent">
                                            <div class="width100">
                                                <p>
                                                <?php 
                                                if($pro_details_row['keybox'] !='YES'){ 
													echo $check_in_person_name; ?> is going to wait for you inside the appartement, she will be expecting you around <?php echo $row["check_in_time"] ?>
													Please call her on <?php echo $check_in_phone; ?> if you are late or lost.                                         
												<?php }else{ ?>
                                                	 Apartement will be ready for you at <?php echo $row["check_in_time"] ?>
                                                <?php } ?>
                                                </br></br>
                                             
                                                <?php if($row["check_in_time"] == '21:00' or $row["check_in_time"] == '21:30' or $row["check_in_time"] == '22:00' or $row["check_in_time"] == '22:30'){?>
                                                <?php if($pro_details_row['keybox'] !='YES'):?>
												<p>Due to late check in, please give 15€ cash to <?php echo $check_in_person_name; ?> to take a taxi back home as written in my add.<br />
												<?php endif;?>
                                               <!-- <strong>Note :</strong>	Guest will give 15€ to < ?php echo $check_in_person_name; ?>. --> 
                                                </p>	
                                                
                                                <?php } else if($row["check_in_time"] == '23:00' or $row["check_in_time"] == '23:30'){?>
                                                <?php if($pro_details_row['keybox'] !='YES'):?>
												<p>Due to very late check in, please give 30€ cash to <?php echo $check_in_person_name; ?> to take a taxi back home as written in my add.<br />
												<?php endif;?>
                                               <!-- <strong>Note :</strong> Guest will give 30€ to < ?php // echo $check_in_person_name; ?>. -->
                                                </p>
                                                
                                                <?php }?>
                                                <?php if($pro_details_row['description']!='') {
                                                
                                                echo html_entity_decode(stripslashes($pro_details_row['description'])); 
                                                
                                                } ?>
                                                
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                
                                <?php
                                /* if($pro_details_row['description']!='') { ?>
                                
                                <li>
                                
                                <div class="iconsec"><img src="../images/a5.png" width="100" height="100" /></div>
                                
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
                                        		<p>Please give <?php echo $row['cost_late_checkout'];?>€ in cash to <?php echo $check_in_person_name; ?> on check in for the late check out. </p>
                                        	</div>
                                        </div>
                                    
                                    </li>
                                
                                <?php } 
									if(strtoupper($row["mifi"])=='YES')
									{                               
										if($row['nights']>0)
										 {
											$nights	= $row['nights'];
											$night	= 5*$nights.'€';
										
										 } else {
												$night = '[5 * NIGHTS] €';
										 } ?>
                                
                                   <li>
                                    
                                    <div class="iconsec"><img src="../images/q6.png" width="100" height="100" /></div>
                                        <div class="anscontent">
                                            <div class="width100">
                                           	 <p>Please give <?php echo $night; ?> in cash to <?php echo $check_in_person_name; ?> for the MIFI dongle (5€/nights * <?php echo $nights; ?> nights)</p>
                                            </div>
                                        </div>
                                    
                                    </li>
                                		<?php 
									} ?>
                                
                                
                                
                                <?php 
                                
                                $check_in = array('6','7');
                                
                                if( ! in_array($row['property_id'],$check_in)) { ?>
                                
                                <li>
                               		<div class="iconsec"><img src="../images/a7.png" width="100" height="100" /></div>
                                	<div class="anscontent">
                                        <div class="width100">
                                        	<p>read those  <a href="/welcome.php?id=<?php echo intval($row['property_id'])?>"> informations</a></p>
                                        </div>
                                	</div>
                                
                                </li>
                                
                                <?php } ?>
                                
                                <li>
                                	<div class="iconsec"><img src="../images/q4.png" width="100" height="100" /></div>
                               		<div class="anscontent">
                               			 <div class="width100">
                                			<p>On <?php echo $row["check_out"] ?>, you are expected to check out at <?php echo $row["check_out_time"] ?>, please take all your belongings with you before leaving the appartement and leave the key as instructed. </p>
                               			 </div>
                               		 </div>
                                
                                </li>
                                <li>
                                    <div class="iconsec"><img src="../images/a4.png" width="100" height="100" /></div>
                                    <div class="anscontent">
                                        <div class="width100">
                                             <p>It is very important for me to have a 5 star review, so please let me know if I can help you with anything before or during your stay. </p>
                                        </div>
                                    </div>
                                </li>
                              </ul>
                             <div class="clear"></div>
                           </div>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
						<?php	
					}
					
				}
			} else {?>
			<div class="questionformarea">
				<div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
				<h1 style="color:#FF0000;"><b>There is problem with booking number! please try again with correct booking number. </b> </h1>
			
				</div>
			</div>
			
			<?php
			
			}
		} else {    ?>
    
			<div class="questionformarea">
                <div class="questionhead" style=" background: linear-gradient(to bottom, #ffffff 0%, #66c3f3 100%) repeat scroll 0 0 rgba(0, 0, 0, 0) !important;  text-shadow: 0 0 1px #fff;">
            
                  <h1 style="color:#FF0000;"><b>There is problem with booking number! please try again with correct booking number. </b></h1>
            
                </div>
      		</div>
     	 <?php
    }?>
    </div>
   </body>

</html>

