<?php
require_once('../connect.php');
require_once('../functions_general.php');

$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
$db->exec("SET NAMES 'utf8'");
session_start();

if(!isset($_SESSION['admin'])) {
	header('location: index.php');
}

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$mysqli->set_charset("utf8");
/*print_r($_REQUEST["ax-uploaded-files"]);
	die;*/
if(isset($_POST['btn_submit'])){

	$emp_id		= security(intval($_POST['emp_id']));
	$paid		= security(intval($_POST['paid']));
	$comment	= security(trim($_POST['comment']));
	$p_id		= security(intval($_POST['p_id']));
	
	$mysqli->query("INSERT INTO payments SET reservation_no='',property='".$p_id."', employee_id='".$emp_id."',".
	"task='Paid',comment='".$comment."',amount=".$paid );
}

$emp_id	= 0; 
$where	= '';

if(isset($_GET['e']) && $_GET['e']!='')

{

	$check_emp = $mysqli->query("SELECT id from employee  where UPPER(name)='".strtoupper($_GET['e'])."'");

	if($check_emp->num_rows ==0) { 

			header('location: index.php');

	}else

	{

		$row = $check_emp->fetch_assoc();

		$emp_id =  $row['id'];

		$where = " and payments.employee_id=".$emp_id."";

	}

}

$previous_due_amount = 0;
$previous_due_cond = "";
$monthBackDate = date("Y-m-d",strtotime("-1 Months"));
if(($_GET['show'] == 'previous') && isset($_GET['show']))
{
	$showWhere = " ";
} else {
	$showWhere = " and DATE_FORMAT(CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%Y-%m-%d') >= '".$monthBackDate."' ";
	$previous_due_cond = " and DATE_FORMAT(CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%Y-%m-%d') < '".$monthBackDate."' ";
}

?>



<!DOCTYPE html>



<html lang="en">

<head>

<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">



<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>


<style>
	.green_td { background-color: #acfa58 !important;  width: 1%;}
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
	.form-horizontal #loading {display: inline-block; float: left; margin-left: 5px;}
	.green_class{background-color:green !important;}
	.white_class{background-color:#fff !important;}
	.table tr th, .table tr td {border-right: 1px solid #ccc}
	.LightGreen {background-color:#62fed7 !important;}
	.Green {background-color:#A9F5BC !important;}
	.LightBlue  {background-color:#9de4ff !important;}
	.Pink  {background-color:#FFC0CB !important;}
	.btn.btn-link {margin-left: 250px;}
	.rotateImg{position:absolute; bottom:0px;}
</style>

</head>

<body>

<div class="container-fluid table-responsive">
  <div class="row">
<!--menu bar-->     
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->
    <?php 

   if(!empty($_POST['property_id'])){

	 $property_id = " and ( property = '".security($_POST['property_id'])."'  OR gcal_imports.property_id = '".security($_POST['property_id'])."' ) ";

	}

	else{

		$property_id = '';

	}

?>

<form class="form-horizontal" action="" method="get">

<select name="e"  style="margin-left:400px;">

			 <option value="">Please Select Employee</option>

			 <?php

			 $stmt1 	= $db->query("select id,name from employee where status='YES' order by name asc");

			$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($rows1)){ 

			 foreach($rows1 as $rows2)

			{ ?>

              <option value="<?php echo $rows2['name']?>" <?php if($rows2['name']==@$_GET['e']){ echo "selected='selected'";} ?>><?php echo $rows2['name']?></option>

             <?php }

			  }

			?>

			</select>

            <input type="submit"  value="Submit" />

            </form>

            <br>
            
        <style>
     .month-lists-div{ width:88%; margin:0px auto; padding:0px; list-style:none}
	 ul.month-lists{float: left; width:15%;  padding:0px; list-style:none}
        ul.month-lists li{ /*float:left;*/ width:100%; font-size:13px;   margin-bottom: 5px; list-style:none}
		.item-year {
    display: inline-block;
    width: 105px !important;
}
        </style>    
            
            
         <div class="month-lists-div">       
      
        
            
 <?php  
 
    $year = array(
					'January' => array('s' => '1','e' => '31' ,'m'=>'1'),
					'February' => array('s' => '1','e' => '28','m'=>'2' ),
					'March' => array('s' => '1','e' => '31' ,'m'=>'3'),
					'April' => array('s' => '1','e' => '30','m'=>'4' ),
					'May' => array('s' => '1','e' => '31','m'=>'5' ),
					'June' => array('s' => '1','e' => '30' ,'m'=>'6'),
					'July' => array('s' => '1','e' => '31','m'=>'7' ),
					'August' => array('s' => '1','e' => '31' ,'m'=>'8'),
					'September' => array('s' => '1','e' => '30','m'=>'9' ),
					'October' => array('s' => '1','e' => '31' ,'m'=>'10'),
					'November' => array('s' => '1','e' => '30' ,'m'=>'11'),
					'December' => array('s' => '1','e' => '31','m'=>'12' ),
);
//echo $m_text = date("F", strtotime( date( 'Y-m-01' )));
$u_s = 1;
$u_e = 3;$count = 1;
for ($t = 8; $t >= 0; $t--) {
   
	$m_text = date("F", strtotime( date( 'Y-m-01' )." -$t months"));
	$y = date("Y", strtotime( date( 'Y-m-01' )." -$t months"));
	
	$m = $year[$m_text];
	
	  $s_sate = $y.'-'.$m['m'].'-'.$m['s'];
	
	if( $y%4 == 0   &&  $m['m']==2 ){
	    $m['e'] = '29';
	}
	
	$e_sate = $y.'-'.$m['m'].'-'.$m['e'];
	
 $sql_month_query = "Select sum(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where (CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date ELSE check_in_new END BETWEEN '".$s_sate."' AND '".$e_sate."') and amount < 0 $where  $property_id "; 


$month_query = $mysqli->query($sql_month_query);
if($month_query->num_rows > 0) {
$month_result = $month_query->fetch_assoc(); 
$month_sum = $month_result['total_sum'];
}

if($month_sum != 0) {
$month_sum = -1 * $month_sum;

if ($count%3 == 1)
    {  
         echo "<div class='month-lists-item' ><ul class='month-lists'> ";
    }


  echo '<li><span class="item-year" >'.$m_text.' '.$y.'</span>:&nbsp;'.$month_sum.'</li>';
if ($count%3 == 0)
    {
        echo "</ul></div>";
    }
    $count++;
}
	
	
	
}      
 if ($count%3 != 1) echo "</div>";           
            
   ?>         
            
 <?php  

 
    $year = array(
'January' => array('s' => '1','e' => '31' ,'m'=>'1'),
'February' => array('s' => '1','e' => '28','m'=>'2' ),
'March' => array('s' => '1','e' => '31' ,'m'=>'3'),
'April' => array('s' => '1','e' => '30','m'=>'4' ),
'May' => array('s' => '1','e' => '31','m'=>'5' ),
'June' => array('s' => '1','e' => '30' ,'m'=>'6'),
'July' => array('s' => '1','e' => '31','m'=>'7' ),
'August' => array('s' => '1','e' => '31' ,'m'=>'8'),
'September' => array('s' => '1','e' => '30','m'=>'9' ),
'October' => array('s' => '1','e' => '31' ,'m'=>'10'),
'November' => array('s' => '1','e' => '30' ,'m'=>'11'),
'December' => array('s' => '1','e' => '31','m'=>'12' ),

);

$u_s = 1;
$u_e = 3;$cont=1;
for ($t = 1; $t <= 3; $t++) {
   
	$m_text = date("F", strtotime( date( 'Y-m-01' )." +$t months"));
	$y = date("Y", strtotime( date( 'Y-m-01' )." +$t months"));
	
	$m = $year[$m_text];
	
	  $s_sate = $y.'-'.$m['m'].'-'.$m['s'];
	
	if( $y%4 == 0 ){
	    $m['e'] = '29';
	}
	
	  $e_sate = $y.'-'.$m['m'].'-'.$m['e'];
	
 $sql_month_query = "Select sum(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where (CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date ELSE check_in_new END BETWEEN '".$s_sate."' AND '".$e_sate."') and amount < 0 $where  $property_id ";



$month_query = $mysqli->query($sql_month_query);
if($month_query->num_rows > 0) {
$month_result = $month_query->fetch_assoc(); 
$month_sum = $month_result['total_sum'];
}

if($month_sum != 0) {
$month_sum = -1 * $month_sum;
if ($cont%3 == 1)
    {  
         echo "<div class='month-lists-item' ><ul class='month-lists'> ";
    }
  echo '<li><span class="item-year" >'.$m_text.' '.$y.'</span>:&nbsp;'.$month_sum.'</li>';
if ($cont%3 == 0)
    {
        echo "</ul></div>";
    }
    $cont++;
}
	
	
	
}      
  if ($cont%3 != 1) echo "</div>";             
            
   ?>         
            
           
            
</div>
<?php 

/* "Select *,DATE_FORMAT(CASE WHEN task = 'paid' THEN req_date ELSE check_in_new END,'%W %d %M %Y') as order_date,DATE_FORMAT(CASE WHEN task = 'paid' THEN req_date ELSE check_in_new END,'%Y-%m-%d') as order_by_date From payments Left Join gcal_imports On payments.reservation_no = gcal_imports.booking_number And (payments.employee_id = gcal_imports.check_in_person Or payments.employee_id = gcal_imports.cleaning_person) where 1  $where $property_id Order By order_by_date";*/

/*
"Select *,DATE_FORMAT(CASE WHEN task = 'paid' THEN req_date ELSE check_in_new END,'%W %d %M %Y') as order_date,DATE_FORMAT(CASE WHEN task = 'paid' THEN req_date ELSE check_in_new END,'%Y-%m-%d') as order_by_date From payments left Join gcal_imports On payments.property = gcal_imports.property_id And

    payments.reservation_no = gcal_imports.booking_number And (payments.employee_id = gcal_imports.check_in_person Or payments.employee_id = gcal_imports.cleaning_person) where 1  $where $property_id group by pay_id Order By order_by_date"
*/


$fetchRoom = $mysqli->query("Select *,
DATE_FORMAT(CASE task WHEN  'paid' THEN req_date WHEN  'add spending' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%W %d %M %Y') as order_date,
DATE_FORMAT(CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%Y-%m-%d') as order_by_date From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1  $where $property_id $showWhere group by pay_id Order By order_by_date");

if ($previous_due_cond != '')
{
	$previous_due = "Select *,
		DATE_FORMAT(CASE task WHEN  'paid' THEN req_date WHEN  'add spending' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%W %d %M %Y') as order_date,
		DATE_FORMAT(CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%Y-%m-%d') as order_by_date From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1  $where $property_id $previous_due_cond group by pay_id Order By order_by_date";
	$fetchPreviousDue = $mysqli->query($previous_due);
	if ($fetchPreviousDue->num_rows > 0)
	{
		while ($row = $fetchPreviousDue->fetch_assoc())
		{
			$previous_due_amount += $row['amount'];
		}
	}
}

/*for total due, no 74 {START} */
$due_now = 0;

$fetchTotalSum = $mysqli->query("Select SUM(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1 and DATE_FORMAT(CASE task WHEN 'paid' THEN req_date WHEN 'add spending' THEN req_date WHEN 'virement' THEN req_date WHEN 'virement' THEN req_date WHEN 'Cleaning' THEN check_out_new ELSE check_in_new END,'%Y-%m-%d') <= CURDATE()  $where $property_id");

if($fetchTotalSum->num_rows > 0) {
$rowTotalSum = $fetchTotalSum->fetch_assoc(); 
$due_now = $rowTotalSum['total_sum'];
}
if($due_now != 0) {
$due_now = -1 * $due_now;
}

$total_count = 0;

$fetchTotalCount = $mysqli->query("Select SUM(amount) as total_sum From payments left Join gcal_imports On payments.property = gcal_imports.property_id And payments.reservation_no = gcal_imports.booking_number where 1 $where $property_id");
if($fetchTotalCount->num_rows > 0) {
$rowTotalCount = $fetchTotalCount->fetch_assoc(); 
$total_count = $rowTotalCount['total_sum'];
}
/*for total due, no 74 {END} */

	if($fetchRoom->num_rows > 0) {

			?>

    <table class='table table-striped' >

      <tr>

        <th colspan="7" width="100%"> <table class="header" width="100%">

            <tr>
				
			 
			 
			 <td colspan="3" width="60%"><?php echo @$_GET['e'] ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  DUE NOW = <?php echo $due_now; ?></td>

              <td width="40%" colspan="2" align="center"><form action='' method='post'>

                  Property :

                  <select name="property_id"   onchange="this.form.submit();">

                    <option value="">All Properties</option>

                    <?php

			 $stmt1 	= $db->query("select id,name from properties where active_status = 'YES' order by name asc");

			$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($rows1)){ 

			 foreach($rows1 as $rows2)

			{ ?>

                    <option value="<?php echo $rows2['id']?>" <?php if(!empty($_POST['property_id'])){if($rows2['id']==$_POST['property_id']){ echo "selected='selected'";}} ?> ><?php echo $rows2['name']?></option>

                    <?php }

			  }

			?>

                  </select>

                </form></td>

            </tr>

          </table>

        </th>

      </tr>

      <tr>

        <th>Date</th>

        <th >Name</th>

        <th>Property</th>

        <th>Task</th>

        <th>Amount</th>
        <th>Total Due</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>

      </tr>

	  <tr>
        <td colspan="7" >
		<?php if(!isset($_GET['show'])) { ?>
             <a href="pay.php?e=<?php echo $_GET['e']; ?>&show=previous">show previous payments</a> 
             <?php } else { ?>
			<a href="pay.php?e=<?php echo $_GET['e']; ?>">show current payments</a> 
             <?php } ?>
		
		</td>
      </tr>
	  
      <?php 

	   $total = 0;

	   while($row = $fetchRoom->fetch_assoc())

	   {
		$NClass = "";
	   $name=$row["name"];

	  // $date_show =$row["check_in"];

	  $date_show = $row['order_date'];

	  if($row["task"] == "virement")
	  {
	  		  $date_show = date('l d F Y',strtotime($row['req_date']));
	  }

			
			if(strtoupper($row["task"]) =="PAID" || $row["task"] == "virement")
			{
				$name=$row["comment"];				
				$NClass = "Green";
				if($row["amount"] > 0) {
					$NClass = "LightGreen";
				}
			}
			
			// if($row["task"] =="Paid")
			// {

				// $name=$row["comment"];
				// if($row["amount"] > 0) {
					// $NClass = "LightGreen";
				// }
				// $date_show = date('l d F Y',strtotime($row['req_date']));

			// }
			
			$lessi = array("lessives", "lessive", "Lessives", "Lessive");
			
			if (in_array($name, $lessi))
			{
			   $NClass = "LightBlue";
			}
			
			if($row["task"] == 'add spending') {
				$name=$row["comment"];
					$NClass = "Pink";
				}
				

	   ?>

      <tr id="row_<?php echo $row["pay_id"]?>">

        <td id="date_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>" ><?php  echo $date_show;?></td>

        <td id="name_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>"><?php echo $name ?></td>

        <td id="property_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>"><?php 

		   	$fetchpro = $mysqli->query("SELECT name from properties where (id=".intval($row["property_id"])." OR id=".intval($row["property"])." )  ");

	if($fetchpro->num_rows > 0) { 

		$row_pro = $fetchpro->fetch_assoc();

		   	echo $row_pro["name"];

	}



			?></td>

        <td id="task_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>"><?php echo $row["task"]?></td>

        <td id="amount_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>"><?php echo $row["amount"]; $total = $total+$row["amount"];?></td>
        <td id="totaldue_<?php echo $row["pay_id"]?>" class="<?php echo $NClass; ?>"><?php $total_due = -1 * ($previous_due_amount + $total); echo $total_due; ?></td>
		<td class="<?php echo $NClass; ?>"><a id="edit_<?php echo $row["pay_id"]?>" href="javascript:void(0)"><span class="glyphicon glyphicon-pencil"></span></a> | <a id="delete_<?php echo $row["pay_id"]?>" href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span></a></td>
        <td class="<?php echo $NClass; ?>">
        <?php 
		//echo $row["employee_id"];
		
		$fetchpics = $mysqli->query("SELECT * from employee_pictures where employee_id=".intval($row["employee_id"]) . " and pay_id=" . intval($row["pay_id"]));
		
		// echo "SELECT * from employee_pictures where employee_id=".intval($row["employee_id"]) . " and property_id=" . intval($row["property"]);
		
		if($fetchpics->num_rows > 0) 
		{ 
			$has_pics=true;
			while ($pics_row = $fetchpics->fetch_assoc())
			{
				$filename = $pics_row["filename"];
				$imgId	  =	$pics_row['id'];
				$image_rows.='<div class="item" id="employee_' . $row['employee_id'] . '_' . $row["property"] . '_' . $row['pay_id'] . '">
			<img class="thumbnail img-responsive image'.$imgId.'" title="" src="../uploads/' . $filename . '">
			<button  onClick="rotateimg('.$imgId.')" class="btn btn-link rotateImg"><span class="glyphicon glyphicon-repeat"></span></button>
				</div>';?>
                <form id="saveImg_form" method="post">
                	<input type="hidden" id="imgName<?php echo $imgId; ?>" name="imgName" value="<?php echo $filename; ?>">
                </form>
                
				<?php 
			}
		}
	?>
		<?php if ($has_pics):?>
			<a href="javascript:void(0)">
          <span style="background-color: transparent; max-width: 35;text-align: center; border: none; " class="glyphicon glyphicon-picture thumbnail" id="employee_<?php echo $row['employee_id']?>_<?php echo $row["property"]?>_<?php echo $row["pay_id"]?>"></span>
        </a>
		<?php endif; $has_pics=false;?>
            </td>
      </tr>
		
        	
       
      <?php 

	   }

		?>

      <tr>

       
        <td >&nbsp;</td>

        <td colspan="3" align="right">Total = </td>

        <td><?php echo $total_count;?></td>

      </tr>

    </table>

    Pay Today

    <form action='' method='post'>

      <table class='table table-striped'>

        <tr>

          <td align="center"> Amount

            <input type="number" name="paid" value="" maxlength="6"></td>

          <td align="center"> Property

            <select name="p_id"  >

              <option value="">Please Select Property</option>

              <?php

			 $stmt1 	= $db->query("select id,name from properties where active_status = 'YES' order by name asc");

			$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($rows1)){ 

			 foreach($rows1 as $rows2)

			{ ?>

              <option value="<?php echo $rows2['id']?>" ><?php echo $rows2['name']?></option>

              <?php }

			  }

			?>

            </select></td>

            <td align="center">Employee

            <select name="emp_id">

			 <option value="">Please Select Employee</option>

			 <?php

			 $stmt1 	= $db->query("select id,name from employee where status='YES' order by name asc");

			$rows1 	= $stmt1->fetchAll(PDO::FETCH_ASSOC);

			if(!empty($rows1)){ 

			 foreach($rows1 as $rows2)

			{ ?>

              <option value="<?php echo $rows2['id']?>" <?php if($rows2['id']==@$emp_id){ echo "selected='selected'";} ?>><?php echo $rows2['name']?></option>

             <?php }

			  }

			?>

			</select>

            </td>

          <td align="center"> Comment

            <input type="text" name="comment" value="" maxlength="250"></td>

        </tr>

        <tr>

          <td colspan="3" align="center">

            <input type="submit" name="btn_submit" value="Pay Today"></td>

        </tr>

      </table>

    </form>

    <?php 

	} else {

		echo "No Records found ";

	}

?>

  </div>

</div>

<div class="modal fade" id="modal-edit" >
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit box</h4>
				
            </div>

            <div class="modal-body">

                Content is loading...

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="drop_and_close_edit" data-dismiss="modal">Close without saving</button>
                <button type="button" class="btn btn-info" id="save_and_close_edit" data-dismiss="modal">Save & Close</button>                
            </div>
        </div>
    </div>
</div>

<div class="hidden" id="img-repo">
	<?php echo $image_rows;?>
</div>
<!--modal dialog for gallery-->
<div class="modal" id="modal-gallery" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button class="close" type="button" data-dismiss="modal">×</button>
          <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body">
          <div id="modal-carousel" class="carousel">
   
            <div class="carousel-inner">           
            </div>
            
            <a class="carousel-control left" href="#modal-carousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
            <a class="carousel-control right" href="#modal-carousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
            
          </div>
      </div>
      <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--end of modal dialog for gallery-->
	
<script>
$(document).ready(function(){
	$(document).delegate('[id^=edit_]','click',function (event) 
	{		
		var id = $(this).attr('id');
		var terms = id.split('_');
		var pay_id = terms[1];		
		// alert (pay_id);
		$('#modal-edit').modal('show', {backdrop: 'static'});
		$.post("ajax_getpay_row.php",{row_id:pay_id},
			function(data,status)
			{
				// $('#statusedit_'+itemID).html ('');
				$('#modal-edit .modal-body').html(data);
                $('#modal-edit').modal('show', {backdrop: 'static'});
			});
			
		
	});
	
	
	$(document).delegate('[id^=delete_]','click',function (event) 
	{		
		event.preventDefault();
		var id = $(this).attr('id');
		var terms = id.split('_');
		var pay_id = terms[1];		
		var name=$('#name_'+pay_id).html();
		var property=$('#property_'+pay_id).html();
		
		var r = confirm("Are you sure you want to delete "+name+" for "+property);
		if (r == true) 
		{
			$.post("ajax_delete_payment.php",{row_id:pay_id},
			function(data,status)
			{				
				location.reload(true);
			});
		} 		
	});
	
	
	$(document).delegate('#save_and_close_edit','click',function (event) 	
	{
		event.preventDefault();
		 
		 var formData = new FormData($('#payedit_form')[0]);
		  $.ajax({
				 url: "ajax_save_payment.php",
				 type: 'POST',
				 data: formData,
				async: false,
				success: function (data) {
					location.reload(true);
					
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
		 console.log(data);	
		/*var row_id	= $('#row_id').val();
		var amount		= $('#amount').val();
		var employee_id	= $('#employee_id').val();
		var property	= $('#property').val();
		var task		= $('#task').val();
		var comment		= $('#comment').val();
		var req_date	= $('#req_date').val();
		var fileImg		= $('#fileImg').val();
		var delOldImg = $('input[name="delOldImg[]"]').map(function(){ 
                    return this.value; 
                }).get();

		$.post("ajax_save_payment.php",data,
			function(data,status)
			{	
				console.log(data);			
				//location.reload(true);
			});		*/
	});
	
	
});

$(document).ready(function() {

  /* activate the carousel */
  $("#msodal-carousel").carousel({
    interval: false
  });

  /* change modal title when slide changes */
  $("#modal-carousel").on("slid.bs.carousel", function() {
    $(".modal-title")
      .html($(this)
        .find(".active img")
        .attr("title"));
  });

  /* when clicking a thumbnail */
  $(".row .thumbnail").click(function() {
    var content = $(".carousel-inner");
    var title = $(".modal-title");

    content.empty();
    title.empty();

    var id = this.id;
    var repo = $("#img-repo .item");
    var repoCopy = repo.filter("#" + id).clone();
    var active = repoCopy.first();

    active.addClass("active");
    title.html(active.find("img").attr("title"));
    content.append(repoCopy);

    // show the modal
    $("#modal-gallery").modal("show");
  });


});
var angle = 0;
function rotateimg(id){
	var img = document.getElementById("image");
	
			angle += 90;
    	
		$(".image"+id).css('transform', 'rotate('+angle+'deg)');
		var imgName=$('#imgName'+id).val();
		
		$.post("ajax_save_image.php",{imgName:imgName},
			function(data,status)
			{		
					console.log(data);		
				//location.reload(true);
			});		
		
	 	}
</script>
</body>

</html>
