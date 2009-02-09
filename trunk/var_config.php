<?php
// This is the date to start the database.
// THIS SHOULD BE A SUNDAY, or whatever day you hold your services.

// Only edit the numbers below for start and end dates
$startDate["Month"]=9;
$startDate["Day"]=12;
$startDate["Year"]=2007;

$endDate["Month"]=9;
$endDate["Day"]=12;
$endDate["Year"]=2010;

// Edit the values in the quotes below to match your setup

$dbhost="localhost";  //probably don't need to change this
$dbuser="root";
$dbpass="root";
$dbname="WorshipTest";
$mailMethod = "SMTP";  // set to "SMTP" to use an SMTP server,
					  //  or set to "local" to use your email client on your local machine
$smtphost = "smtp.myserver.net";
$smtpport = 25;
$smtpauth = true;  // set to true if your SMTP server requires authentication
$smtpuser = "";
$smtppass = "";

$sheetmusic_loc = "/wpdev/sheetmusic/"; 
									//make the folder you specify exists
									// and has 777 permissions.
									// This path should be relative to your
									//   public_html directory
									
$stylesheet = "blue.css";

$useOldDBAddQuery = False;
									
if(!isset($_SERVER['DOCUMENT_ROOT']))
{
$_SERVER['DOCUMENT_ROOT'] = '.';  //might work for IIS
}

$pagetitle = "Worship Planner QR";  
$banner = "images/wpqr_simple.png";  //you can create your own banner that
                                     // will display at the top of the page
$logo = "";

define("WELCOME_MESSAGE",
"<p>Welcome to Worship Planner QR!</p>
<p>This is the default welcome message.
It can be changed by modifying the text in the file var_config.php</p>");


// Table names  DON'T EDIT.  THIS IS FOR DEVELOPMENT.
$prefix = '';

if(isset($_SESSION['prefix'])) $prefix = $_SESSION['prefix'];
$Availability = $prefix."Availability";
$Dates = $prefix."Dates";
$Personnel = $prefix."Personnel";
$SchedDates = $prefix."SchedDates";
$Sheetmusic = $prefix."Sheetmusic";
$SongDates = $prefix."SongDates";
$Songs = $prefix."Songs";
$WorshipTeam = $prefix."WorshipTeam";
$SongLinks = $prefix."SongLinks";



?>