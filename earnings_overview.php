<?php
require_once ('../connect.php');

require_once ('../functions_general.php');

session_start();
$where = "";
$where2 = '';
$property = '';
$mysqli = new mysqli($db_host_connect, $db_user_connect, $db_pass_connect, $db_name_connect);

if (!isset($_SESSION['admin']))
	{
	header('location: index.php');
	}

if (isset($_GET['pname']))
	{
	if ($_GET['pname'] != '')
		{
		$property = security($_GET['pname']);

		$pro_fees 	= $mysqli->query("select id,name,management_fee from properties where id='".$property."' ");
		$pro_management_fee = 0.2;
		if($pro_fees->num_rows > 0) { 
		   $pro_fees_row = $pro_fees->fetch_assoc();
		   $pro_management_fee = ($pro_fees_row['management_fee']/100);
		}
		
			
		
		$where_1 = "and property = " . $property;
		
		$where.= "and property_id = " . $property;
		$prvious_month_year = date('F-Y', strtotime(date('Y-m') . " -1 month"));
		$prvious_month_year_second = date('F-Y', strtotime(date('Y-m') . " -2 month"));
		$prvious_month_year_third = date('F-Y', strtotime(date('Y-m') . " -3 month"));
		$prvious_month_year_fourth = date('F-Y', strtotime(date('Y-m') . " -4 month"));
		$prvious_month_year_fifth = date('F-Y', strtotime(date('Y-m') . " -5 month"));
		$prvious_month_year_sixth = date('F-Y', strtotime(date('Y-m') . " -6 month"));
		$current_month_year = date('F-Y');
		$next_month_year = date('F-Y', strtotime(date('Y-m') . " +1 month"));
		$next_month_year_second = date('F-Y', strtotime(date('Y-m') . " +2 month"));
		$next_month_year_third = date('F-Y', strtotime(date('Y-m') . " +3 month"));
		$next_month_year_fourth = date('F-Y', strtotime(date('Y-m') . " +4 month"));
		$next_month_year_fifth = date('F-Y', strtotime(date('Y-m') . " +5 month"));
		$next_month_year_sixth = date('F-Y', strtotime(date('Y-m') . " +6 month"));
		
		$pmonth_where_first = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year . "' ";
		$pmonth_where_second = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year_second . "' ";
		$pmonth_where_third = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year_third . "' ";
		$pmonth_where_fourth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year_fourth . "' ";
		$pmonth_where_fifth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year_fifth . "' ";
		$pmonth_where_sixth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $prvious_month_year_sixth . "' ";
		$month_where = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $current_month_year . "' ";
		$month_where_first = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year . "' ";
		$month_where_second = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year_second . "' ";
		$month_where_third = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year_third . "' ";
		$month_where_fourth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year_fourth . "' ";
		$month_where_fifth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year_fifth . "' ";
		$month_where_sixth = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $next_month_year_sixth . "' ";
		
		/* code for payment table */
		$pay_pmonth_where_first = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year . "' ";
		$pay_pmonth_where_second = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year_second . "' ";
		$pay_pmonth_where_third = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year_third . "' ";
		$pay_pmonth_where_fourth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year_fourth . "' ";
		$pay_pmonth_where_fifth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year_fifth . "' ";
		$pay_pmonth_where_sixth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $prvious_month_year_sixth . "' ";
		$pay_month_where = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $current_month_year . "' ";
		$pay_month_where_first = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year . "' ";
		$pay_month_where_second = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year_second . "' ";
		$pay_month_where_third = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year_third . "' ";
		$pay_month_where_fourth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year_fourth . "' ";
		$pay_month_where_fifth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year_fifth . "' ";
		$pay_month_where_sixth = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $next_month_year_sixth . "' ";
		
		/* code for payment table */

			
		
		$total_count = $total_count1 = $total_count2 = $total_count3 = $total_count4 = $total_count5 = $total_count6 = 0;
		$total_countp1 = $total_countp2 = $total_countp3 = $total_countp4 = $total_countp5 = $total_countp6 = 0;
		
		
		$fetchTotalCountp1 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports  where 1  $where $pmonth_where_first  ");
		
		$booking_number1 = $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_first  ");
		$reservation_no1 =	'';
		if($booking_number1->num_rows > 0) {
			while($booking_number1_data = $booking_number1->fetch_assoc())
			{
			     $reservation_no1	.=	"'".$booking_number1_data['booking_number']."'".',';
			}
		}
		if($reservation_no1!=''){
			$reservation_no1=rtrim($reservation_no1,',');
		}
		
		$pmonth_first_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no1) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_first and amount < 0 $where_1 )) ";
		
		//echo $pmonth_first_sql;
	
		/* $pmonth_first_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_first GROUP BY gi.booking_number Order By check_in_new"; */
		
		$pmonth_first_ck_cl_query = $mysqli->query($pmonth_first_sql);
		
		$pmonth_first_ck_clt = 0;
		if($pmonth_first_ck_cl_query->num_rows > 0) {
			while($pmonth_where_first_row = $pmonth_first_ck_cl_query->fetch_assoc())
			{
			     $pmonth_first_ck_clt =	$pmonth_first_ck_clt + $pmonth_where_first_row['tamount'];
			}
		}
		
		$fetchTotalCountp2 	= $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $pmonth_where_second  ");
		
		$booking_number2 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_second ");
		$reservation_no2	= '';
		
		if($booking_number2->num_rows > 0) {
			while($booking_number2_data = $booking_number2->fetch_assoc())
			{
			     $reservation_no2	.=	"'".$booking_number2_data['booking_number']."'".','; 
			}
		}
		
		if($reservation_no2!=''){
			$reservation_no2=rtrim($reservation_no2,',');
		}
		
		/* $pmonth_second_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no2) "; */
		
		$pmonth_second_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no2) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_second and amount < 0 $where_1 )) ";
		
		
		/* $pmonth_second_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_second GROUP BY gi.booking_number Order By check_in_new"; */
		
		$pmonth_second_ck_cl_query = $mysqli->query($pmonth_second_sql);
		
		$pmonth_second_ck_clt = 0;
		if($pmonth_second_ck_cl_query->num_rows > 0) {
			while($pmonth_where_second_row = $pmonth_second_ck_cl_query->fetch_assoc())
			{
			     $pmonth_second_ck_clt =	$pmonth_second_ck_clt + $pmonth_where_second_row['tamount'];
			}
		}
		
		
		
		
		
		$fetchTotalCountp3 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $pmonth_where_third  ");
		
		
		$booking_number3 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_third ");
		$reservation_no3	= '';
		
		if($booking_number3->num_rows > 0) {
			while($booking_number3_data = $booking_number3->fetch_assoc())
			{
			     $reservation_no3	.=	"'".$booking_number3_data['booking_number']."'".','; 
			}
		}
		
		if($reservation_no3!=''){
			$reservation_no3=rtrim($reservation_no3,',');
		}
		
		/* $pmonth_third_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no3) "; */
		
		$pmonth_third_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no3) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_third and amount < 0 $where_1 )) ";
		
		
		/* $pmonth_third_sql ="Select sum(amount) as tamount,property From payments where 1 $where $pmonth_where_third and amount < 0 "; */
		
		
		
		/* $pmonth_third_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_third GROUP BY gi.booking_number Order By check_in_new"; */
		
		$pmonth_third_ck_cl_query = $mysqli->query($pmonth_third_sql);
		
		$pmonth_third_ck_clt = 0;
		if($pmonth_third_ck_cl_query->num_rows > 0) {
			while($pmonth_where_third_row = $pmonth_third_ck_cl_query->fetch_assoc())
			{
			     $pmonth_third_ck_clt =	$pmonth_third_ck_clt + $pmonth_where_third_row['tamount'];
			}
		}
		
		
		
		
		$fetchTotalCountp4 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $pmonth_where_fourth  ");
		
		
		$booking_number4 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_fourth ");
		$reservation_no4	= '';
		
		if($booking_number4->num_rows > 0) {
			while($booking_number4_data = $booking_number4->fetch_assoc())
			{
			     $reservation_no4	.=	"'".$booking_number4_data['booking_number']."'".',';  
			}
		}
		
		if($reservation_no4!=''){
			$reservation_no4=rtrim($reservation_no4,',');
		}
		
		$pmonth_fourth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no4) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_fourth and amount < 0 $where_1 )) ";
		
		/* $pmonth_fourth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no4) "; */
	
		/* $pmonth_fourth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $pmonth_where_fourth and amount < 0 "; */
		
		
		/* $pmonth_fourth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_fourth GROUP BY gi.booking_number Order By check_in_new"; */
		$pmonth_fourth_ck_cl_query = $mysqli->query($pmonth_fourth_sql);
		
		$pmonth_fourth_ck_clt = 0;
		if($pmonth_fourth_ck_cl_query->num_rows > 0) {
			while($pmonth_where_fourth_row = $pmonth_fourth_ck_cl_query->fetch_assoc())
			{
			     $pmonth_fourth_ck_clt =	$pmonth_fourth_ck_clt + $pmonth_where_fourth_row['tamount'];
			}
		}
		
		
		$fetchTotalCountp5 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $pmonth_where_fifth  ");
		
		
		$booking_number5 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_fifth ");
		$reservation_no5	= '';
		
		if($booking_number5->num_rows > 0) {
			while($booking_number5_data = $booking_number5->fetch_assoc())
			{
			     $reservation_no5	.=	"'".$booking_number5_data['booking_number']."'".',';  
			}
		}
		
		if($reservation_no5!=''){
			$reservation_no5=rtrim($reservation_no5,',');
		}
		
		/* $pmonth_fifth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no5) "; */
		
		$pmonth_fifth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no5) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_fifth and amount < 0 $where_1 )) ";
		
		/* $pmonth_fifth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $pmonth_where_fifth and amount < 0 "; */
		
		
		
		/* $pmonth_fifth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_fifth GROUP BY gi.booking_number Order By check_in_new"; */
		
		$pmonth_fifth_ck_cl_query = $mysqli->query($pmonth_fifth_sql);
		
		$pmonth_fifth_ck_clt = 0;
		if($pmonth_fifth_ck_cl_query->num_rows > 0) {
			while($pmonth_where_fifth_row = $pmonth_fifth_ck_cl_query->fetch_assoc())
			{
			     $pmonth_fifth_ck_clt =	$pmonth_fifth_ck_clt + $pmonth_where_fifth_row['tamount'];
			}
		}
		
		
		
		
		$fetchTotalCountp6 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $pmonth_where_sixth  ");
		
		$booking_number6 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $pmonth_where_sixth ");
		$reservation_no6	= '';
		
		if($booking_number6->num_rows > 0) {
			while($booking_number6_data = $booking_number6->fetch_assoc())
			{
			     $reservation_no6	.=	"'".$booking_number6_data['booking_number']."'".',';  
			}
		}
		
		if($reservation_no6!=''){
			$reservation_no6=rtrim($reservation_no6,',');
		}
		
		/* $pmonth_sixth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no6) "; */
		
		$pmonth_sixth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no6) and amount < 0 ) or (reservation_no = '' $pay_pmonth_where_sixth and amount < 0 $where_1 )) ";
		
		/* $pmonth_sixth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $pmonth_where_sixth and amount < 0 "; */
		
		
		
		/* $pmonth_sixth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $pmonth_where_sixth GROUP BY gi.booking_number Order By check_in_new"; */
		$pmonth_sixth_ck_cl_query = $mysqli->query($pmonth_sixth_sql);
		
		$pmonth_sixth_ck_clt = 0;
		if($pmonth_sixth_ck_cl_query->num_rows > 0) {
			while($pmonth_where_sixth_row = $pmonth_sixth_ck_cl_query->fetch_assoc())
			{
			     $pmonth_sixth_ck_clt =	$pmonth_sixth_ck_clt + $pmonth_where_sixth_row['tamount'];
			}
		}
		
		
		
		
		$fetchTotalCount = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where  ");
		
		$booking_number7 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where ");
		$reservation_no7	= '';
		
		if($booking_number7->num_rows > 0) {
			while($booking_number7_data = $booking_number7->fetch_assoc())
			{
			     $reservation_no7	.=	"'".$booking_number7_data['booking_number']."'".',';  
			}
		}
		
		if($reservation_no7!=''){
			$reservation_no7=rtrim($reservation_no7,',');
		}
		
		/* $pmonth_0_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no7) "; */
		
		$pmonth_0_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no7) and amount < 0 ) or (reservation_no = '' $pay_month_where and amount < 0 $where_1 )) ";
			
		
		/* $pmonth_0_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where and amount < 0 "; */
		 
		 
		 /* $pmonth_0_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new"; */
		$pmonth_0_ck_cl_query = $mysqli->query($pmonth_0_sql);
		
		$pmonth_0_ck_clt = 0;
		if($pmonth_0_ck_cl_query->num_rows > 0) {
			while($pmonth_where_0_row = $pmonth_0_ck_cl_query->fetch_assoc())
			{
			     $pmonth_0_ck_clt =	$pmonth_0_ck_clt + $pmonth_where_0_row['tamount'];
			}
		}
		
		
		
		$fetchTotalCount1 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_first  ");
		
		$booking_number8 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_first ");
		$reservation_no8	= '';
		
		if($booking_number8->num_rows > 0) {
			while($booking_number8_data = $booking_number8->fetch_assoc())
			{
			     $reservation_no8	.=	"'".$booking_number8_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no8!=''){
			$reservation_no8=rtrim($reservation_no8,',');
		}
		
		/* $month_first_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no8) "; */
		
		$month_first_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no8) and amount < 0 ) or (reservation_no = '' $pay_month_where_first and amount < 0 $where_1 )) ";
		
		
	/* 	$month_first_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_first and amount < 0 "; */
		
		
		
		/* $month_first_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_first GROUP BY gi.booking_number Order By check_in_new"; */
		$month_first_ck_cl_query = $mysqli->query($month_first_sql);
		
		$month_first_ck_clt = 0;
		if($month_first_ck_cl_query->num_rows > 0) {
			while($month_where_first_row = $month_first_ck_cl_query->fetch_assoc())
			{
			     $month_first_ck_clt =	$month_first_ck_clt + $month_where_first_row['tamount'];
			}
		}
		
		
		
		$fetchTotalCount2 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_second  ");
		
		$booking_number9 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_second ");
		$reservation_no9	= '';
		
		if($booking_number9->num_rows > 0) {
			while($booking_number9_data = $booking_number9->fetch_assoc())
			{
			     $reservation_no9	.=	"'".$booking_number9_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no9!=''){
			$reservation_no9=rtrim($reservation_no9,',');
		}
		
		/* $month_second_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no9) "; */
			
		$month_second_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no9) and amount < 0 ) or (reservation_no = '' $pay_month_where_second and amount < 0 $where_1 )) ";	
		
		/* $month_second_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_second and amount < 0 "; */
		
		
		
		/* $month_second_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_second GROUP BY gi.booking_number Order By check_in_new"; */
		$month_second_ck_cl_query = $mysqli->query($month_second_sql);
		
		$month_second_ck_clt = 0;
		if($month_second_ck_cl_query->num_rows > 0) {
			while($month_where_second_row = $month_second_ck_cl_query->fetch_assoc())
			{
			     $month_second_ck_clt =	$month_second_ck_clt + $month_where_second_row['tamount'];
			}
		}
			
		
		$fetchTotalCount3 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_third  ");
		
		$booking_number10 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_third ");
		$reservation_no10	= '';
		
		if($booking_number10->num_rows > 0) {
			while($booking_number10_data = $booking_number10->fetch_assoc())
			{
			     $reservation_no10	.=	"'".$booking_number10_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no10!=''){
			$reservation_no10=rtrim($reservation_no10,',');
		}
		
		/* $month_third_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no10) "; */
		
		$month_third_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no10) and amount < 0 ) or (reservation_no = '' $pay_month_where_third and amount < 0 $where_1 )) ";	
		
		/* $month_third_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_third and amount < 0 "; */
		
		/* $month_third_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_third GROUP BY gi.booking_number Order By check_in_new"; */
		$month_third_ck_cl_query = $mysqli->query($month_third_sql);
		
		$month_third_ck_clt = 0;
		if($month_third_ck_cl_query->num_rows > 0) {
			while($month_where_third_row = $month_third_ck_cl_query->fetch_assoc())
			{
			     $month_third_ck_clt =	$month_third_ck_clt + $month_where_third_row['tamount'];
			}
		}
		
		
		$fetchTotalCount4 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_fourth  ");
		
		
		$booking_number11 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_fourth ");
		$reservation_no11	= '';
		
		if($booking_number11->num_rows > 0) {
			while($booking_number11_data = $booking_number11->fetch_assoc())
			{
			     $reservation_no11	.=	"'".$booking_number11_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no11!=''){
			$reservation_no11=rtrim($reservation_no11,',');
		}
		
		/* $month_fourth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no11) "; */
		
		$month_fourth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no11) and amount < 0 ) or (reservation_no = '' $pay_month_where_fourth and amount < 0 $where_1 )) ";	
		
		/* $month_fourth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_fourth and amount < 0 "; */

		/* $month_fourth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_fourth GROUP BY gi.booking_number Order By check_in_new"; */
		$month_fourth_ck_cl_query = $mysqli->query($month_fourth_sql);
		
		$month_fourth_ck_clt = 0;
		if($month_fourth_ck_cl_query->num_rows > 0) {
			while($month_where_fourth_row = $month_fourth_ck_cl_query->fetch_assoc())
			{
			     $month_fourth_ck_clt =	$month_fourth_ck_clt + $month_where_fourth_row['tamount'];
			}
		}
		
		
		$fetchTotalCount5 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_fifth  ");
		
		$booking_number12 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_fifth ");
		$reservation_no12	= '';
		
		if($booking_number12->num_rows > 0) {
			while($booking_number12_data = $booking_number12->fetch_assoc())
			{
			     $reservation_no12	.=	"'".$booking_number12_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no12!=''){
			$reservation_no12=rtrim($reservation_no12,',');
		}
		
		/* $month_fifth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no12) "; */
		
		$month_fifth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no12) and amount < 0 ) or (reservation_no = '' $pay_month_where_fifth and amount < 0 $where_1 )) ";	
		
		/* $month_fifth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_fifth and amount < 0 "; */
		
		/* $month_fifth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_fifth GROUP BY gi.booking_number Order By check_in_new"; */
		
		$month_fifth_ck_cl_query = $mysqli->query($month_fifth_sql);
		
		$month_fifth_ck_clt = 0;
		if($month_fifth_ck_cl_query->num_rows > 0) {
			while($month_where_fifth_row = $month_fifth_ck_cl_query->fetch_assoc())
			{
			     $month_fifth_ck_clt =	$month_fifth_ck_clt + $month_where_fifth_row['tamount'];
			}
		}
		
		
		
		$fetchTotalCount6 = $mysqli->query("Select SUM(earning) as total_sum  From gcal_imports where 1  $where $month_where_sixth  ");
		
		
		$booking_number13 	= $mysqli->query("Select booking_number From gcal_imports  where 1  $where $month_where_sixth ");
		$reservation_no13	= '';
		
		if($booking_number13->num_rows > 0) {
			while($booking_number13_data = $booking_number13->fetch_assoc())
			{
			     $reservation_no13	.=	"'".$booking_number13_data['booking_number']."'".',';
			}
		}
		
		if($reservation_no13!=''){
			$reservation_no13=rtrim($reservation_no13,',');
		}
		
		/* $month_sixth_sql =" SELECT SUM(amount) AS tamount,property FROM payments WHERE reservation_no IN($reservation_no13) "; */
		
		$month_sixth_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 and ((reservation_no IN($reservation_no13) and amount < 0 ) or (reservation_no = '' $pay_month_where_sixth and amount < 0 $where_1 )) ";	
		
		/* $month_sixth_sql ="Select sum(amount) as tamount,property From payments where 1 $where $month_where_sixth and amount < 0 "; */
		
		/* $month_sixth_sql ="Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and amount<0) where 1  $where $month_where_sixth GROUP BY gi.booking_number Order By check_in_new"; */
		
		$month_sixth_ck_cl_query = $mysqli->query($month_sixth_sql);
		
		$month_sixth_ck_clt = 0;
		if($month_sixth_ck_cl_query->num_rows > 0) {
			while($month_where_sixth_row = $month_sixth_ck_cl_query->fetch_assoc())
			{
			     $month_sixth_ck_clt =	$month_sixth_ck_clt + $month_where_sixth_row['tamount'];
			}
		}
		
		
		if ($fetchTotalCountp1->num_rows > 0)
			{
			$rowTotalCountp1 = $fetchTotalCountp1->fetch_assoc();
			if ($rowTotalCountp1['total_sum'] > 0)
				{
				
				$total_countp1 = $rowTotalCountp1['total_sum'];
				$management_feep1 = $total_countp1 - (($rowTotalCountp1['total_sum'] * $pro_management_fee) + (-1 *$pmonth_first_ck_clt));
				
				}
			  else
				{
				$total_countp1 = 0;
				$management_feep1=0;
				}
			}

		if ($fetchTotalCountp2->num_rows > 0)
			{
			$rowTotalCountp2 = $fetchTotalCountp2->fetch_assoc();
			if ($rowTotalCountp2['total_sum'] > 0)
				{
				$total_countp2 = $rowTotalCountp2['total_sum'] ;
				$management_feep2 =   $total_countp2 - (($rowTotalCountp2['total_sum'] * $pro_management_fee) + (-1 *$pmonth_second_ck_clt));
				}
			  else
				{
				$total_countp2 = 0;
				$management_feep2 =0;
				}
			}

		if ($fetchTotalCountp3->num_rows > 0)
			{
			$rowTotalCountp3 = $fetchTotalCountp3->fetch_assoc();
			if ($rowTotalCountp3['total_sum'] > 0)
				{
				$total_countp3 = $rowTotalCountp3['total_sum'];
				$management_feep3 =   $total_countp3 - (($rowTotalCountp3['total_sum'] * $pro_management_fee) + (-1 *$pmonth_third_ck_clt));
				}
			  else
				{
				$total_countp3 = 0;
				$management_feep3 = 0;
				}
			}

		if ($fetchTotalCountp4->num_rows > 0)
			{
			$rowTotalCountp4 = $fetchTotalCountp4->fetch_assoc();
			if ($rowTotalCountp4['total_sum'] > 0)
				{
				$total_countp4 = $rowTotalCountp4['total_sum'];
				$management_feep4 =   $total_countp4 - (($rowTotalCountp4['total_sum'] * $pro_management_fee) + (-1 *$pmonth_fourth_ck_clt));
				}
			  else
				{
				$total_countp4 = 0;
				$management_feep4 =  0;
				}
			}

		if ($fetchTotalCountp5->num_rows > 0)
			{
			$rowTotalCountp5 = $fetchTotalCountp5->fetch_assoc();
			if ($rowTotalCountp5['total_sum'] > 0)
				{
				$total_countp5 = $rowTotalCountp5['total_sum'] ;
				$management_feep5 =   $total_countp5 - (($rowTotalCountp5['total_sum'] * $pro_management_fee) + (-1 *$pmonth_fifth_ck_clt));
				}
			  else
				{
				$total_countp5 = 0;
				$management_feep5 = 0;
				}
			}

		if ($fetchTotalCountp6->num_rows > 0)
			{
			$rowTotalCountp6 = $fetchTotalCountp6->fetch_assoc();
			if ($rowTotalCountp6['total_sum'] > 0)
				{
				$total_countp6 = $rowTotalCountp6['total_sum'];
				$management_feep6 =   $total_countp6 - (($rowTotalCountp6['total_sum'] * $pro_management_fee) + (-1 *$pmonth_sixth_ck_clt));
				}
			  else
				{
				$total_countp6 = 0;
				$management_feep6 = 0;
				}
			}

		if ($fetchTotalCount->num_rows > 0)
			{
			$rowTotalCount = $fetchTotalCount->fetch_assoc();
			if ($rowTotalCount['total_sum'] > 0)
				{
				$total_count = $rowTotalCount['total_sum'] ;
				$management_fee0 =   $total_count - (($rowTotalCount['total_sum'] * $pro_management_fee) + (-1 *$pmonth_0_ck_clt));
				}
			  else
				{
				$total_count = 0;
				$management_fee0 =  0;
				}
			}

		if ($fetchTotalCount1->num_rows > 0)
			{
			$rowTotalCount1 = $fetchTotalCount1->fetch_assoc();
			if ($rowTotalCount1['total_sum'] > 0)
				{
				$total_count1 = $rowTotalCount1['total_sum'] ;
				$management_fee1 =   $total_count1 - (($rowTotalCount1['total_sum'] * $pro_management_fee) + (-1 *$month_first_ck_clt));
				}
			  else
				{
				$total_count1 = 0;
				$management_fee1 = 0;
				}
			}

		if ($fetchTotalCount2->num_rows > 0)
			{
			$rowTotalCount2 = $fetchTotalCount2->fetch_assoc();
			if ($rowTotalCount2['total_sum'] > 0)
				{
				$total_count2 = $rowTotalCount2['total_sum'];
				$management_fee2 =   $total_count2 - (($rowTotalCount2['total_sum'] * $pro_management_fee) + (-1 *$month_second_ck_clt));
				}
			  else
				{
				$total_count2 = 0;
				$management_fee2 =  0;
				}
			}

		if ($fetchTotalCount3->num_rows > 0)
			{
			$rowTotalCount3 = $fetchTotalCount3->fetch_assoc();
			if ($rowTotalCount3['total_sum'] > 0)
				{
				$total_count3 = $rowTotalCount3['total_sum'] ;
				$management_fee3 =  $total_count3 - (($rowTotalCount3['total_sum'] * $pro_management_fee) + (-1 *$month_third_ck_clt));
				}
			  else
				{
				$total_count3 = 0;
				$management_fee3 =  0;
				}
			}

		if ($fetchTotalCount4->num_rows > 0)
			{
			$rowTotalCount4 = $fetchTotalCount4->fetch_assoc();
			if ($rowTotalCount4['total_sum'] > 0)
				{
				$total_count4 = $rowTotalCount4['total_sum'];
				$management_fee4 =  $total_count4 - (($rowTotalCount4['total_sum'] * $pro_management_fee) + (-1 *$month_fourth_ck_clt));
				}
			  else
				{
				$total_count4 = 0;
				$management_fee4 = 0;
				}
			}

		if ($fetchTotalCount5->num_rows > 0)
			{
			$rowTotalCount5 = $fetchTotalCount5->fetch_assoc();
			if ($rowTotalCount5['total_sum'] > 0)
				{
				$total_count5 = $rowTotalCount5['total_sum'];
				$management_fee5 = $total_count5 - (($rowTotalCount5['total_sum'] * $pro_management_fee) + (-1 *$month_fifth_ck_clt));
				}
			  else
				{
				$total_count5 = 0;
				$management_fee5 =0;
				}
			}

		if ($fetchTotalCount6->num_rows > 0)
			{
			$rowTotalCount6 = $fetchTotalCount6->fetch_assoc();
			if ($rowTotalCount6['total_sum'] > 0)
				{
				$total_count6 = $rowTotalCount6['total_sum'] ;
				$management_fee6 = $total_count6 - (($rowTotalCount6['total_sum'] * $pro_management_fee) + (-1 *$month_sixth_ck_clt));
				}
			  else
				{
				$total_count6 = 0;
				$management_fee6 = 0;
				}
			}
		}
	}

?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--<link href="/bootstrap.min.css" rel="stylesheet">-->
 <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
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
</style>

<style>
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
.input-large {
	min-width: 600px !important;
	min-height: 60px !important;
	border-bottom: 1px solid #ccc !important;
}
</style>
</head>

<body>
<div class="container-fluid table-responsive">
<div class="row" style="margin:0 !important">
<!--menu bar-->     
<?php require_once ('admin_menu.php')?>
<!--end of menu bar-->

  <form class="form-horizontal" action="" method="get">
    &nbsp;&nbsp;
    <select name="pname"  >
      <option value="">Please Select Property</option>
      <?php
$stmt1 = $mysqli->query("select id,name from properties where 1 " . $where2 . " and active_status = 'YES' order by name asc");

if ($stmt1->num_rows > 0)
	{
	while ($rows2 = $stmt1->fetch_assoc())
		{ ?>
      <option value="<?php
		echo $rows2['id'] ?>" <?php
		if ($rows2['id'] == $property)
			{
			echo "selected='selected'";
			} ?>><?php
		echo $rows2['name'] ?></option>
      <?php
		}
	}

?>
    </select>
    <input type="submit" value="submit" />
  </form>
  <br />
  <?php

if (isset($_GET['pname']))
	{ ?>
  <table class='table table-striped future_tble' >
    <tr>
      <th colspan="7" width="100%"> <table class="header" width="100%">
          Total Earning
            </br>
            </br>
          
          <tr>
            <td style="color:blue; font-size:16px;"  width="30%">Previous</td>
			 <td   width="30%">Earning </td>
			 <td   width="30%">Earning Net</td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $prvious_month_year_sixth; ?></td>
            <td><?php
	echo $total_countp6; ?></td>
	 <td><?php
	echo $management_feep6; ?></td>
          </tr>
          <tr>
            <td width="30%"><?php
	echo $prvious_month_year_fifth; ?></td>
            <td><?php
	echo $total_countp5; ?></td>
	<td><?php
	echo $management_feep5; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $prvious_month_year_fourth; ?></td>
            <td><?php
	echo $total_countp4; ?></td>
	<td><?php
	echo $management_feep4; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $prvious_month_year_third; ?></td>
            <td><?php
	echo $total_countp3; ?></td>
	<td><?php
	echo $management_feep3; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $prvious_month_year_second; ?></td>
            <td><?php
	echo $total_countp2; ?></td>
	<td><?php
	echo $management_feep2; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $prvious_month_year; ?></td>
            <td><?php
	echo $total_countp1; ?></td>
	<td><?php
	echo $management_feep1; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $current_month_year; ?></td>
            <td><?php
	echo $total_count; ?></td>
	<td><?php
	echo $management_fee0; ?></td>
          </tr>
          <tr>
            <td style="color:blue; font-size:16px;" colspan="2" width="30%">&nbsp;</td>
          </tr>
          <tr>
            <td style="color:blue; font-size:16px;" colspan="2" width="30%">Next</td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year; ?></td>
            <td><?php
	echo $total_count1; ?></td>
	<td><?php
	echo $management_fee1; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year_second; ?></td>
	
            <td><?php
	echo $total_count2; ?></td>
	<td><?php
	echo $management_fee2; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year_third; ?></td>
            <td><?php
	echo $total_count3; ?></td>
	<td><?php
	echo $management_fee3; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year_fourth; ?></td>
            <td><?php
	echo $total_count4; ?></td>
	<td><?php
	echo $management_fee4; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year_fifth; ?></td>
            <td><?php
	echo $total_count5; ?></td>
	<td><?php
	echo $management_fee5; ?></td>
          </tr>
          <tr>
            <td  width="30%"><?php
	echo $next_month_year_sixth; ?></td>
            <td><?php
	echo $total_count6; ?></td>
	<td><?php
	echo $management_fee6; ?></td>
          </tr>
        </table>
      </th>
    </tr>
  </table>
  <?php
	} ?>
</div>
</div>
</body>
</html>
<script>
$(window).load(function(){
$(document).ready(function(){
    $(".marker").tooltip({placement: 'right'});
});
});
</script>