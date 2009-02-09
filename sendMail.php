<?php
require_once 'header.php';
require 'functions.php';

$num_results = $_REQUEST["numEmails"];
$date = $_POST["date"];

$TO = "";
for($i=0; $i<$num_results; $i++){
	$fieldname = "Email$i";
	$fieldvalue = $_REQUEST["$fieldname"];
	if($i>0){
		$newTO = $TO.",$fieldvalue";
	} else {
		$newTO = $fieldvalue;
	}
	
	$TO = $newTO;
}

$FROM = $_REQUEST["From"];
$SUBJECT = $_REQUEST["Subject"];
$BODY = stripslashes($_REQUEST["Body"]);

if($mailMethod=="sendmail")
{
	$fromstr = "FROM: $FROM";
	$success = mail($TO, $SUBJECT, $BODY, $fromstr);
	echo "$TO, $SUBJECT, $fromstr";
	if($success)
	{
		echo("Message successfully sent");
	} else {
		echo("Message failed.");
	}
} elseif($mailMethod=="SMTP") {
		
	require("class.phpmailer.php");
	
	$mail = new PHPMailer();
	$mail->SetLanguage("en","./");
	
	$mail->IsSMTP();                                   // send via SMTP
	$mail->Host     = $smtphost; // SMTP servers
	$mail->SMTPAuth = $smtpauth;     // turn on SMTP authentication
	$mail->Username = $smtpuser;  // SMTP username
	$mail->Password = $smtppass; // SMTP password
	$mail->Port     = $smtpport;
	
	$mail->From     = $FROM;
	$mail->FromName = "WorshipPlanner Notification";
	for($i=0; $i<$num_results; $i++){
		$fieldname = "Email$i";
		$fieldvalue = $_REQUEST["$fieldname"];
		$mail->AddAddress("$fieldvalue");
	}
	
	$mail->IsHTML(true);                               // send as HTML
	// Begin getting info from database
	$HTMLBODY = '';
	$HTMLBODY = replace_line_breaks($BODY);
	$HTMLBODY .= "<hr>";
	$HTMLBODY .= create_html_body($date);
	
	//echo $HTMLBODY;	
	
	$mail->Subject  =  $SUBJECT;
	$mail->Body     =  $HTMLBODY;
	$mail->AltBody  =  $BODY;
	
	if(!$mail->Send())
	{
	   echo "Message was not sent <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   //exit;
	} else {
		echo "The following message has been sent:<br/><br/>";
		echo $HTMLBODY;
	}
} else {
	echo "No valid mailing method specified in var_config.php (should be SMTP or sendmail).";
}

require_once('footer.php');

function create_html_body($date)
{
	//require 'functions.php';
	require 'var_config.php';

	$query = "SELECT * FROM $Dates WHERE Date='$date'";
	
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$HTMLBODY = "<strong><u>Notes:</u><br></strong>".replace_line_breaks(stripslashes($row["Notes"]))."<br><br>";

	$HTMLBODY .= "\n<table width='100%' border='1' cellspacing=0>\n<tr><th width='50%'>Musicians ";

	$HTMLBODY .= "</th>\n<th width='50%'>Songs</th></tr>\n";
	$HTMLBODY .= "<tr><td valign='top'>\n";

// List Musicians and Songs that are scheduled for this week

	$query = "SELECT UID, Part FROM $SchedDates WHERE Date='$date'";
	$result = mysql_query($query);
	if(!$result) $HTMLBODY .= 'No Musicians Scheduled';
	else {
		$num_results = mysql_num_rows($result);
		if($num_results==0) $HTMLBODY .= 'No Musicians Scheduled';
		$HTMLBODY .= "<table width='100%' border=0 cellspacing=2>\n<tr><th><u>Name</u></th><th><u>Part</u></th></tr>";
		for($i=0; $i<$num_results; $i++){
			if($i > 0) $email_list=$email_list."&";
			$row = mysql_fetch_array($result);
			$userID = $row["UID"];
			$name_result=mysql_query("SELECT Name,Email FROM $Personnel WHERE UID=$userID");
			$namerow = mysql_fetch_array($name_result);
			$HTMLBODY .= "<tr><td align='center'>".$namerow["Name"]."</td><td align='center'>".$row["Part"]."</td></tr>\n";
			$email_list = $email_list."TO=".$namerow["Email"];
			$fieldname[$i]="Email$i";
			$fieldvalue[$i]=$namerow["Email"];
		}
		$HTMLBODY .= "</table><br>\n";
		
	
	}
	$HTMLBODY .= "</td><td valign='top'>\n";
	
// list songs
	$query = "SELECT SID FROM $SongDates WHERE Date='$date'";
	$result = mysql_query($query);
	if(!$result) $HTMLBODY .= 'No Songs Selected';
	else 
	{
		$num_results = mysql_num_rows($result);
		if($num_results==0) $HTMLBODY .= 'No Songs Selected';
		$HTMLBODY .= "<br><ul>";
		for($i=0; $i<$num_results; $i++){
			$row = mysql_fetch_array($result);
			$SID = $row["SID"];
			$name_result=mysql_query("SELECT Title FROM $Songs WHERE SID=$SID");
			$namerow = mysql_fetch_array($name_result);
			$HTMLBODY .= "<li>".stripslashes($namerow["Title"])."&nbsp;&nbsp;<small>";
			$hoststring = "http://".$_SERVER["HTTP_HOST"];
			$HTMLBODY .= return_song_urls($SID, $hoststring);
			$HTMLBODY .= "</small></li>";
		}
		$HTMLBODY .= "</ul>";
	}
	
	$HTMLBODY .= "</td></tr></table>\n";
	return $HTMLBODY;
}

function return_song_urls($SID, $site=""){
	$URLtext = '';
	
	require("var_config.php");
	
	$numSheetmusicHits = 0;
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");
	
	// GET URLS FOR DOWNLOADABLE MUSIC, AND DISPLAY THEM
	$query="SELECT * FROM $Sheetmusic WHERE SID=$SID";
	
	if(@ $dl_result = mysql_query($query)){
		$dl_rows = mysql_num_rows($dl_result);
		for($j=0; $j < $dl_rows; $j++){
			$dl_row=mysql_fetch_array($dl_result);
			if($j>0) $URLtext .= " | ";
			//echo("<a href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>"); //OLD WAY
			$URLtext .= "<a href='".$site.$sheetmusic_loc.$dl_row["URL"]."'>".$dl_row["Description"]."</a>";
		}
		//if($dl_rows==0)echo("&nbsp;");
		$numSheetmusicHits = $dl_rows;
	}
	
	$query="SELECT * FROM $SongLinks WHERE SID=$SID";
	
	if(@ $dl_result = mysql_query($query)){
		$dl_rows = mysql_num_rows($dl_result);
		for($j=0; $j < $dl_rows; $j++){
			$dl_row=mysql_fetch_array($dl_result);
			if($j>0 || $numSheetmusicHits > 0) $URLtext .= " | ";
			//echo("<a href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>"); //OLD WAY
			$URLtext .= "<a class=songlink href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>";
		}
		if($dl_rows==0) $URLtext .= "&nbsp;";
	}
	
	return $URLtext;
}
?>
