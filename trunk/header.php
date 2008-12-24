<?php
session_start();
if(!isset($_SESSION['auth_level'])) session_regenerate_id();

require("var_config.php");
require_once 'class.WorshipPlanner.php';
include 'customFields.php';

$myWP = new WorshipPlanner;

$myWP->setDBInfo($dbhost, $dbuser, $dbpass, $dbname);
$myWP->setTableNames($prefix);

//AUTHORIZE
//if($_SESSION['auth_level']==0 || !isset($_SESSION['auth_level'])) 
//{
	if(isset($_REQUEST['username']) && isset($_REQUEST['password']))
	{  //USER HAS SUBMITTED LOGIN FORM
		session_unset();
		$username = $myWP->dbSanitize($_REQUEST['username']);
		$password = $myWP->dbSanitize($_REQUEST['password']);
		$auth_level = verifyUser($username, $password);
	}	
//}
//REGISTER SELECTED GLOBALS, REQUEST VARIABLES
$action = "";
if(isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if(isset($_SERVER['QUERY_STRING'])) $QUERY_STRING = $_SERVER['QUERY_STRING'];
if(isset($_SERVER['PHP_SELF'])) $PHP_SELF = $_SERVER['PHP_SELF'];
if(isset($_GET['printable'])) {
	$printable = $_GET['printable'];
} else {
	$printable = 'no';
}

if(!isset($_SESSION['globalStart']) || !isset($_SESSION['globalEnd']))
{
	if((isset($_REQUEST['sDate'])&& isset($_REQUEST['eDate'])))
	{
		$sDate = $_REQUEST['sDate'];
		$eDate = $_REQUEST['eDate'];
		$_SESSION['globalStart']=$sDate;
		$_SESSION['globalEnd']=$eDate;
	} elseif(isset($_REQUEST['smonth']) && !isset($_POST['recurrence'])) {  //assume others are set too
								//Don't set the global start/end if we are just deleting or adding dates
		//TODO check input
		$sDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['smonth'],$_REQUEST['sday'],$_REQUEST['syear']));
		$eDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['emonth'],$_REQUEST['eday'],$_REQUEST['eyear']));
		$_SESSION['globalStart']=$sDate;
		$_SESSION['globalEnd']=$eDate;
	}
}
	
if(isset($_SESSION['globalStart'])) 
{
	$globalStart = $_SESSION['globalStart'];
	$sDate = $globalStart;
}
if(isset($_SESSION['globalEnd']))
{
	$globalEnd = $_SESSION['globalEnd'];
	$eDate = $globalEnd;
}

if($action=="logout") {
	session_unset();
	session_destroy();
}

if($action=="clearDates"){
	$globalStart=NULL;
	$globalEnd=NULL;
	$_SESSION['globalStart']=NULL;
	$_SESSION['globalEnd']=NULL;
}


if(!isset($_SESSION['auth_level'])){
	//session_register("auth_level");
	$_SESSION['auth_level'] = 0;
	$auth_level = 0;
}else{
	$auth_level = $_SESSION['auth_level'];
}

if(isset($_SESSION['auth_UID'])){
	 $auth_UID = $_SESSION['auth_UID'];
 }else{
	 $auth_UID = 0;
}

//AUTHORIZE
//if($_SESSION['auth_level']==0 || !isset($_SESSION['auth_level'])) 
//{
	if(isset($_REQUEST['username']) && isset($_REQUEST['password']))
	{  //USER HAS SUBMITTED LOGIN FORM
		$username = $myWP->dbSanitize($_REQUEST['username']);
		$password = $myWP->dbSanitize($_REQUEST['password']);
		$auth_level = verifyUser($username, $password);
	}	
//}

if($printable == "yes"){
	echo("<html><head><title>Printable View</title><link rel='stylesheet' type='text/css' href='$stylesheet' /></head><body>\n");
}else{
	showHeaderContent($myWP);

}


// CHECK FOR AUTHENTICATION AND ACT ACCORDINGLY
if(!( $myWP->userIsLoggedIn() )){
	//SHOW LOGIN FORM
	if($action=='logout') $QUERY_STRING = ''; //don't want't to re-logout
	echo("<br><br>");
	echo("<form action='".$PHP_SELF."?".$QUERY_STRING."' method='post'>\n<table border=0><tr>");
	echo("<td>Name:</td><td><input type='text' size='25' name='username'/></td></tr>\n");
	echo("<tr><td>Password:</td><td><input type='password' size='25' name='password'/></td></tr>\n<tr><td colspan=2><input type='submit' value='Log In'/></td></tr></table>\n</form>\n");
	require_once 'footer.php';
	die();
}
//*************************************************
// END MAIN PHP LOGIC, BEGIN FUNCTION DEFINITIONS
//*************************************************

function verifyUser($username,$password)
{
		require 'var_config.php';

		$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ("$dbname");
		
		$query = "SELECT Name, Password, UID, AuthLevel FROM $Personnel WHERE Name='$username'";
		$result = mysql_query($query);
		
		if(!$result){
			echo("An Error Occurred: ".mysql_error());
		} else {
			$numres = mysql_num_rows($result);
			if($numres==0){
				echo("No Matching Information");
			} else {
				$row = mysql_fetch_array($result);
				$cryptpass = crypt($password,$row["Password"]);

				if($cryptpass==$row["Password"]){
					//session_register("auth_level","auth_UID","auth_user");
					$_SESSION['auth_level'] = $row["AuthLevel"];
					$_SESSION['auth_UID'] = $row["UID"];
					$_SESSION['auth_user'] = $row["Name"];
					$_SESSION['auth_pass'] = $cryptpass;
					return $_SESSION['auth_level'];
				} else {
					echo("<big><i>Invalid Password</i></big>");
					$_SESSION['auth_level'] = 0;
					return 0;
				}
			}
		}
		return 0;
	
}

function showHeaderContent(&$myWP)
{
	require 'var_config.php';
	?>
		<html>
		<head>
		<title><? echo ($pagetitle); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $stylesheet; ?>" />
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		</head>
		
		<body>
		<div id="wrapper">
		<div id="header">

			
			<?php 
			if($myWP->userIsLoggedIn()) 
			{ ?>
			<div id="headerbuttons">
				<ul>
				<li><a href="index.php?action=clearDates">Clear Start/End Dates</a></li>
				<li><a href="index.php?action=logout">Log Out</a></li>
				</ul>
			</div>
			
			<?php 
			}  ?>
			
			<h1>
			   <?php
			   if($logo<>"") { echo "<img src='$logo' align='top' alt=''/>"; }
	                    ?>
			<img src="<?echo($banner);?>" alt="QRCC Worship Programming" align="top"/>
			</h1>

			
			<!-- 
			<a href="./"><img border=0 src="images/welcome.png" alt="Welcome &nbsp;&nbsp;&nbsp;"/></a>
			-->
			
			<?php //SHOW NAVIGATION BAR ONLY IF PERSON IS LOGGED IN
			
			if($myWP->userIsLoggedIn()) 
			{ ?>
			<div id="menu1">
				<!-- 
				<a href="users.php"><img border=0 src="images/user.png" alt=" User Info &nbsp;&nbsp;&nbsp;"/></a>
				<a href="availability.php"><img border=0 src="images/availability.png" alt="Availability &nbsp;&nbsp;&nbsp;"/></a>
				<a href="editSongs.php"><img border=0 src="images/songs.png" alt="Songs &nbsp;&nbsp;&nbsp;"/></a>
				<a href="planning.php"><img border=0 src="images/programming.png" alt="Programming &nbsp;&nbsp;&nbsp;"/></a>
				<a href="overview.php"><img border=0 src="images/overview.png" alt="Overview of Gatherings &nbsp;&nbsp;&nbsp;"/></a>
				<a href="viewSchedule.php"><img border=0 src="images/schedule.png" alt="Schedule"/></a>
				-->
				<a href="./index.php">Welcome</a>
				<a href="users.php">Users</a>
				<a href="availability.php">Availability</a>
				<a href="editSongs.php">Songs</a>
				<a href="planning.php">Programming</a>
				<a href="overview.php">Series Overview</a>
				<a href="viewSchedule.php">Schedule</a>
				<?php if($myWP->userIsAdmin()) { ?><a href="manageDates.php">Dates</a> <?php } ?>
				
			</div>  <!-- END MENU1 DIV -->
			</div> <!-- END HEADER DIV -->
			<?php 
			}	
			?>

	<div id="content">		
		<?php
}
?>
