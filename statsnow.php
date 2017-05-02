<?php
require_once('../connect.php');
require_once('../functions_general.php');
session_start();
$db = new PDO('mysql:host='.$db_host_connect.';dbname='.$db_name_connect,$db_user_connect, $db_pass_connect);
$db->exec("SET NAMES 'utf8'");
if(!isset($_SESSION['admin']))
{
    header('location: index.php');
}

//get total for current month
$month_where = '';
$month_and_year = '';

$mysqli = new mysqli($db_host_connect, $db_user_connect,$db_pass_connect, $db_name_connect);
$mysqli->set_charset("utf8");
$where = ' and booking_status != "cancel" ';
$c_monthYear = date('F-Y');
$month_and_year = $c_monthYear;
$month_where .=" and DATE_FORMAT(check_in_new,'%M-%Y') = '".$month_and_year."' ";

$fetchData = $mysqli->query("Select gi.id,gi.name,gi.booking_number,gi.check_in,gi.check_out,gi.earning,sum(amount) as tamount,property_id ,nights From gcal_imports as gi LEFT join payments on (payments.reservation_no= gi.booking_number and task !='paid') where 1  $where $month_where GROUP BY gi.booking_number Order By check_in_new ");

$total_m_charge = 0;

while($row = $fetchData->fetch_assoc()) {
    $earning = intval($row['earning']);

    $pro_fees 	= $mysqli->query("select id,name,management_fee from properties where id='".$row['property_id']."' ");
    $pro_management_fee = 0.2;
    if($pro_fees->num_rows > 0) {
        $pro_fees_row = $pro_fees->fetch_assoc();
        $pro_management_fee = ($pro_fees_row['management_fee']/100);
    }

    $payout = ($earning * $pro_management_fee);
    $total_m_charge = $total_m_charge + $payout;
}

//earning

//get list property
$where2 = '';
$stmt1 = $mysqli->query("select id,name from properties where 1 " . $where2 . " and active_status = 'YES' order by name asc");
$propertys = array();

while ($row = $stmt1->fetch_assoc())
{
    $propertys[$row['id']] = $row;
}

foreach ($propertys as $key=>$property){

    $total_countp1 = 0;
    //echo $property['id'];
    $pro_fees 	= $mysqli->query("select id,name,management_fee from properties where id='".$property['id']."' ");
    $pro_management_fee = 0.2;
    if($pro_fees->num_rows > 0) {
        $pro_fees_row = $pro_fees->fetch_assoc();
        $pro_management_fee = ($pro_fees_row['management_fee']/100);
    }

    $where_1 = "and property = " . $property['id'];
    $where = "and property_id = " . $property['id'];

    $prvious_month_year = date('F-Y', strtotime(date('Y-m') . " -1 month"));
    $current_month_year = date('F-Y');
    $totalday_in_month = date("t");
    $next_month_year = date('F-Y', strtotime(date('Y-m') . " +1 month"));
    //echo $next_month_year;
    $pmonth_where_first = " and DATE_FORMAT(check_in_new,'%M-%Y') = '" . $current_month_year . "' ";
    $pay_pmonth_where_first = " and DATE_FORMAT(req_date,'%M-%Y') = '" . $current_month_year . "' ";

    $query = "SELECT SUM(earning) as total_sum  From gcal_imports  where 1  $where $pmonth_where_first";
    //echo $query.'<br><br>';
    $fetchTotalCountp1 = $mysqli->query($query);

    $query = "Select booking_number, nights, booking_status From gcal_imports  where 1  $where $pmonth_where_first";
    $booking_number1 = $mysqli->query($query);
    //echo $query.'<br><br>';
    $reservation_no1 =	'';
    $sum_book = 0;
    if($booking_number1->num_rows > 0) {
        while($booking_number1_data = $booking_number1->fetch_assoc())
        {
            if($booking_number1_data['booking_status'] != 'cancel') {
                //echo $booking_number1_data['nights'].'_';
                $sum_book += $booking_number1_data['nights'];
            }

            $reservation_no1 .=	"'".$booking_number1_data['booking_number']."'".',';
        }
    }
    if($reservation_no1!=''){
        $reservation_no1 = rtrim($reservation_no1,',');
    }

    $propertys[$key]['empty_n'] = $totalday_in_month - $sum_book;

//    echo $reservation_no1;
//    echo $sum_book;
//    echo '<br>';


    $pmonth_first_sql ="SELECT SUM(amount) AS tamount,property FROM payments WHERE 1 
                        AND ((reservation_no IN($reservation_no1) and amount < 0 ) 
                        OR (reservation_no = '' $pay_pmonth_where_first and amount < 0 $where_1 )) ";

    $pmonth_first_ck_cl_query = $mysqli->query($pmonth_first_sql);

    $pmonth_first_ck_clt = 0;
    if($pmonth_first_ck_cl_query->num_rows > 0) {
        while($pmonth_where_first_row = $pmonth_first_ck_cl_query->fetch_assoc())
        {
            $pmonth_first_ck_clt =	$pmonth_first_ck_clt + $pmonth_where_first_row['tamount'];
        }
    }

    $total_countp1 = 0;
    $management_feep1=0;

    if ($fetchTotalCountp1->num_rows > 0)
    {
        $rowTotalCountp1 = $fetchTotalCountp1->fetch_assoc();
        if ($rowTotalCountp1['total_sum'] > 0)
        {

            $total_countp1 = $rowTotalCountp1['total_sum'];
            $management_feep1 = $total_countp1 - (($rowTotalCountp1['total_sum'] * $pro_management_fee) + (-1 *$pmonth_first_ck_clt));
            $propertys[$key]['fee'] = $management_feep1;
        }

    }

//    print_r($property);
//    echo $management_feep1;
//    echo '<hr>';

}



?>
<!DOCTYPE html>


<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Admin Panel" />
    <meta name="author" content="" />
    <link rel="icon" href="assets/images/favicon.ico">
    <title>Statsnow | Dashboard</title>

   	<link rel="stylesheet" href="assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="assets/css/font-icons/entypo/css/entypo.css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/neon-core.css">
	<link rel="stylesheet" href="assets/css/neon-theme.css">
	<link rel="stylesheet" href="assets/css/neon-forms.css">
	<link rel="stylesheet" href="assets/css/custom.css">

	<script src="assets/js/jquery-1.11.3.min.js"></script>
</head>

<body>
    <div class="page-body">
        <div class="page-container">
            <div class="sidebar-menu">
                <div class="sidebar-menu-inner">
                    <header class="logo-env">
                        <!-- logo -->
                        <!-- <div class="logo">
                            <a href="index.html">
                                <img src="assets/images/logo@2x.png" width="120" alt="" />
                            </a>
                        </div> -->

                        <!-- logo collapse icon -->
                        <div class="sidebar-collapse">
                            <a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                                <i class="entypo-menu"></i>
                            </a>
                        </div>


                        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
                        <div class="sidebar-mobile-menu visible-xs">
                            <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                                <i class="entypo-menu"></i>
                            </a>
                        </div>
                    </header>
                    <ul id="main-menu" class="main-menu">
                        <li class="">
                            <a href="index.php">
                                <i class="entypo-gauge"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="pay.php">
                                <i class="entypo-window"></i>
                                <span class="title">Pay employees</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="report_manager.php">
                                <i class="entypo-window"></i>
                                <span class="title">Checkup</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="properties.php">
                                <i class="entypo-window"></i>
                                <span class="title">Properties</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="add_booking.php">
                                <i class="entypo-window"></i>
                                <span class="title">Add a booking</span>
                            </a>
                        </li>
                        <li class="has-sub">
                            <a href="#">
                                <i class="entypo-mail"></i>
                                <span class="title">Messages</span>
                            </a>
                            <ul class="">
                                <li class="">
                                    <a href="sms.php">
                                        <span class="title">SMS box</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="guest_mail_inbox.php">
                                        <span class="title">Email inbox</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="sms_templates.php">
                                        <span class="title">Templates</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub">
                            <a href="#">
                                <i class="entypo-doc-text"></i>
                                <span class="title">Billing</span>
                            </a>
                            <ul class="">
                                <li class="">
                                    <a href="bill.php">
                                        <span class="title">Bill</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="earnings_overview.php">
                                        <span class="title">Earnings Brut/Net</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="clientBilling.php">
                                        <span class="title">Client Billing PDF</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="send_bill.php">
                                        <span class="title">Send Bill</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="statistics.php">
                                        <span class="title">Statistics</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="edit-instructions.php">
                                <i class="entypo-doc-text"></i>
                                <span class="title">Instructions</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="global_calendar.php">
                                <i class="entypo-calendar"></i>
                                <span class="title">Calendar</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="cleanordirty.php">
                                <i class="entypo-calendar"></i>
                                <span class="title">CleanORdirty</span>
                            </a>
                        </li>
                        <li class="has-sub">
                            <a href="#">
                                <i class="entypo-hourglass"></i>
                                <span class="title">Cron</span>
                            </a>
                            <ul class="">
                                <li class="">
                                    <a href="gcal.php">
                                        <span class="title">Update spread</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="gmail_content.php">
                                        <span class="title">Update from/nb</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="payment_parser.php">
                                        <span class="title">Parser Versements</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="../calendar/create_ics.php">
                                        <span class="title">Generate employee calendar</span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="guest_mail.php">
                                        <i class="entypo-mail"></i>
                                        <span class="title">Email Inbox</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!--end menu-->
                </div>
            </div>
            <div class="main-content">
                <div class="row">
                    <div class="col-md-6 col-sm-8 clearfix">

                    </div>
                    <div class="col-md-6 col-sm-4 clearfix hidden-xs">
                        <ul class="list-inline links-list pull-right">
                            <li>
                                <a href="logout.php">
                                    Log Out <i class="entypo-logout right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>


                <div class="col-sm-3">

                    <div class="tile-stats tile-primary">
                        <div class="icon"><i class="entypo-suitcase"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $total_m_charge; ?>"  data-postfix=" &euro;" data-duration="1500" data-delay="0">0 &euro;</div>

                        <h3><?php echo $c_monthYear ?></h3>
                        <p>Fees total for current month</p>
                    </div>
                </div>
                <?php foreach ($propertys as $property): ?>
                    <div class="col-sm-3">
                        <div class="tile-stats tile-red">
                            <div class="icon"><i class="entypo-gauge"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $property['fee']; ?>"  data-postfix=" &euro;" data-duration="1500" data-delay="0">0 &euro;</div>

                            <h3><?php echo $property['name']?></h3>
                            <p><?php echo $current_month_year ?>: <?php echo $property['empty_n']?> emtpy nights</p>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <!-- Imported styles on this page -->
<!--    <link rel="stylesheet" href="assets/js/jvectormap/jquery-jvectormap-1.2.2.css">-->
    <link rel="stylesheet" href="assets/js/rickshaw/rickshaw.min.css">

    <!-- Bottom scripts (common) -->
    <script src="assets/js/gsap/TweenMax.min.js"></script>
    <script src="assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/joinable.js"></script>
    <script src="assets/js/resizeable.js"></script>
    <script src="assets/js/neon-api.js"></script>
<!--    <script src="assets/js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>-->


    <!-- Imported scripts on this page -->
<!--    <script src="assets/js/jvectormap/jquery-jvectormap-europe-merc-en.js"></script>-->
    <script src="assets/js/jquery.sparkline.min.js"></script>
    <script src="assets/js/rickshaw/vendor/d3.v3.js"></script>
    <script src="assets/js/rickshaw/rickshaw.min.js"></script>
    <script src="assets/js/raphael-min.js"></script>
    <script src="assets/js/morris.min.js"></script>
    <script src="assets/js/toastr.js"></script>
<!--    <script src="assets/js/neon-chat.js"></script>-->


    <!-- JavaScripts initializations and stuff -->
    <script src="assets/js/neon-custom.js"></script>


    <!-- Demo Settings -->
    <script src="assets/js/neon-demo.js"></script>

</body>