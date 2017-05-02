<style>
.navbar-static-top {
  margin-bottom:20px;
}

i {
  font-size:16px;
}
.nav > li a {
	padding:7px;
}
.nav > li > a {
  color:#000;  
}
  
.navbar-nav > li > a {
    padding-bottom: 5px;
    padding-top: 5px;
}

.dropdown {
    border: 0px solid #ccc;
    color: #444444;
    font-family: Arial;
    font-size: 14px;
    padding: 0px 0px;
}

/* count indicator near icons */
.nav>li .count {
  position: absolute;
  bottom: 12px;
  right: 6px;
  font-size: 9px;
  background: rgba(51,200,51,0.55);
  color: rgba(255,255,255,0.9);
  line-height: 1em;
  padding: 2px 4px;
  -webkit-border-radius: 10px;
  -moz-border-radius: 10px;
  -ms-border-radius: 10px;
  -o-border-radius: 10px;
  border-radius: 10px;
}

</style>
<div id="top-nav" class="navbar navbar-static-top navbar-left ">
    <div class="container-fluid">
        <div class="navbar-header" >
            <button  type="button" class="navbar-toggle navbar-inverse" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>            
        </div>
		
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">                
                <li><a href="index.php"><i class="glyphicon glyphicon-home"></i> Home</a></li>
                <li><a href="statsnow.php"> Stats now</a></li>
                <li><a href="pay.php">Pay employees</a></li>
                <li><a href="report_manager.php">Checkup</a></li>
                <li><a href="properties.php">Properties</a></li>
                <li><a href="add_booking.php">Add a booking</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-envelope"></i> Messages <span class="caret"></span></a>
                    <ul id="g-account-menu" class="dropdown-menu" role="menu">
                        <li><a href="sms.php">SMS box</a></li>
                        <li><a href="guest_mail_inbox.php">Email inbox</a></li>
                        <li><a href="sms_templates.php">Templates</a></li>

                    </ul>
                </li>
				<li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">Billing<span class="caret"></span></a>
                    <ul id="g-account-menu" class="dropdown-menu" role="menu">
						<li ><a href="bill.php">Bill</a></li>
                        <li><a href="earnings_overview.php">Earnings Brut/Net</a></li>
                        <li><a href="clientBilling.php">Client Billing PDF</a></li>
                        <li><a href="send_bill.php">Send Bill</a></li>
                        <li><a href="statistics.php">Statistics</a></li>
                    </ul>
                </li>
                <li><a href="edit-instructions.php">Instructions</a></li>
                
				<li><a href="global_calendar.php"><i class="glyphicon glyphicon-calendar"></i> Calendar</a></li>
				
                <li><a href="cleanordirty.php">CleanORdirty</a></li>
				<li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">Cron<span class="caret"></span></a>
                    <ul id="g-account-menu" class="dropdown-menu" role="menu">
                        <li><a href="gcal.php">Update spread</a></li>
                        <li><a href="gmail_content.php">Update from/nb</a></li>
                        <li><a href="payment_parser.php">Parser Versements</a></li>
                        <li><a href="../calendar/create_ics.php">Generate employee calendar</a></li>
                        <li><a href="guest_mail.php">Email Inbox</a></li>
                    </ul>
                </li>
                <li><a href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Logout</a></li>
            </ul>
        </div>
		
    </div>
    <!-- /container -->
</div>
<div class="row clearfix" style="celar:both;float:none;">&nbsp;</div>


