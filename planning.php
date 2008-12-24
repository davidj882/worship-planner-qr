<?php

require("header.php");
require("functions.php");

// BEGIN MAIN CODE
if(!$myWP->userIsLoggedIn()) {
	echo("<h2>Log in to View this Page!</h2>");
} else {



if(!$action == "view")
{
	// display date select box
	echo("\n<form action='".$PHP_SELF."' method='get'><big>View Programming for :</big>\n");
	displayDateSelect("date");
	echo("<input type='hidden' name='action' value='view'/>");
	echo(" <input type='submit' value='Submit'>\n</form>");
}


if($action=="view")
{
	//
	$date = $myWP->dbSanitize($_REQUEST['date']);
	echo("<form name='dateForm'><input type='hidden' name='date' value='$date'/></form>\n");
	$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());

	$date = $myWP->dbSanitize($_REQUEST['date']);
	
		//Display Information about this Date
	$query = "SELECT * FROM $Dates WHERE Date='$date'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	echo("<table border=0 cellpadding=5 width='100%''>\n<tr><td width='70%'>");
	echo("<h1>".date_convert($date,8).": ".htmlentities(stripslashes($row['Theme']))."</h1>");
	echo("<h2>Series: ".stripslashes($row["Series"])."</h2>");

	$userID = $row["WL"];
	$WLName = $myWP->getUserName($userID);

	echo("<h3>Worship Leader: $WLName </h3><br><br>");
	echo("\n</td><td width='30%' valign='top'>");
	//Display date navigation links if this is not the printable view
	
	if(!($_REQUEST['printable']=="yes"))
	{
		echo("<table border='1' cellspacing=0 cellpadding=5 bgcolor='333333'><tr><td>\n");
		$query = "SELECT Date FROM $Dates WHERE Date < '".$date."' ORDER BY Date DESC";
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);

		echo("<form action='".$PHP_SELF."' method='get'>"); //this is put here to prevent line break in wrong place
		if($numrows>1)
		{
			$row = mysql_fetch_array($result);
			echo("<a href='".$PHP_SELF."?action=view&date=".$row["Date"]."'>Previous Date</a>&nbsp;&nbsp;&nbsp;");
		}
		
		$query = "SELECT Date FROM $Dates WHERE Date > '".$date."' ORDER BY Date ASC";
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);
		if($numrows>1)
		{
			$row = mysql_fetch_array($result);
			echo("<a href='".$PHP_SELF."?action=view&date=".$row["Date"]."'>Following Date</a>&nbsp;&nbsp;&nbsp;<br><br>");
		}
	
		echo("\n or Jump To:\n");
		displayDateSelect("date");
		echo("<input type='hidden' name='action' value='view'/>");
		echo(" <input type='submit' value='Submit'>\n</form>");
		
		echo("</td></tr></table>\n");
		
	}  //FINISH DISPLAY OF DATE NAVIGATION

	echo("&nbsp; </td></tr></table>\n");
	$query = "SELECT * FROM $Dates WHERE Date='$date'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	//TODO:  display custom fields
        if(!empty($customFields)){
	  echo '<table border="0" cellpadding="4">';
          foreach($customFields as $field=>$label){
            $tmpstr = "<tr><td align=\"right\">$label:</td><td> ".stripslashes($row[$field])."</td></tr>";
            echo $tmpstr . "\n";
          }
	  echo '</table';
          echo '<br/><br/>';
        }
	$original = array("\n"," ","\t");
	$replace = array("<br>","&nbsp;","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	echo("<strong><u>Notes:</u><br></strong>".replace_line_breaks(stripslashes($row["Notes"]))."<br><br>");
//	echo("<strong><u>Notes:</u><br></strong>".str_replace($original,$replace,$row["Notes"])."<br><br>");
//	echo("<form><strong>Notes:<br></strong><textarea rows=6 cols=35>".$row["Notes"]."</textarea></form><br><br>");
	echo("\n<table width='100%' border='1' cellspacing=0>\n<tr><th width='50%'>Musicians ");
	if($_REQUEST['printable']!="yes" && $auth_level>1){
		echo("<a href=javascript:assignParts('$date')><small><i>Assign Parts</i></small></a>");
	}
	echo("</th>\n<th width='50%'>Songs</th></tr>\n");
	echo("<tr><td valign='top'>\n");

// List Musicians and Songs that are scheduled for this week

	$query = "SELECT UID, Part FROM $SchedDates WHERE Date='$date'";
	$result = mysql_query($query);
	if(!$result) echo('No Musicians Scheduled');
	else {
		$num_results = mysql_num_rows($result);
		if($num_results==0) echo('No Musicians Scheduled');
		echo("<table width='100%' border=0 cellspacing=2>\n<tr><th><u>Name</u></th><th><u>Part</u></th></tr>");
		for($i=0; $i<$num_results; $i++){
			if($i > 0) $email_list=$email_list."&";
			$row = mysql_fetch_array($result);
			$userID = $row["UID"];
			$name_result=mysql_query("SELECT Name,Email FROM $Personnel WHERE UID=$userID");
			$namerow = mysql_fetch_array($name_result);
			echo("<tr><td align='center'>".$namerow["Name"]."</td><td align='center'>".$row["Part"]."</td></tr>\n");
			$email_list = $email_list."TO=".$namerow["Email"];
			$fieldname[$i]="Email$i";
			$fieldvalue[$i]=$namerow["Email"];
		}
		echo("</table><br>\n");
		
		echo("<form name='mailform' action='composeMail.php' method='post'>");
		echo("\n");
		for($i=0; $i<$num_results; $i++){
			echo("<input type='hidden' name='".$fieldname[$i]."' value='".$fieldvalue[$i]."'>\n");
		}
		echo("<input type='hidden' name='numEmails' value='$num_results'>\n");
		echo("<input type='hidden' name='thedate' value='$date'>\n");
		$email_subject = "Worship Team - ".$date;
		echo("<input type='hidden' name='Subject' value='$email_subject'>\n");
		if($_REQUEST['printable']!="yes"){
			if($mailMethod=="local")
			{
			echo("<small><a href='mailto:?".htmlentities($email_list)."&SUBJECT=".$email_subject."'>Send Group Email</a></small>");
			} elseif ($mailMethod=="SMTP")
			{
			echo("<input type='submit' name='sendMail' value='Send Group Email'/>\n");
			}
		}
		echo("</form>\n");
	}
	echo("</td><td valign='top'>\n");
	
// list songs
	$query = "SELECT SID FROM $SongDates WHERE Date='$date' ORDER BY `Order`";
	$result = mysql_query($query);
	if(!$result) echo('No Songs Selected');
	else 
	{
		$num_results = mysql_num_rows($result);
		if($num_results==0) echo('No Songs Selected');
		echo("<br><ul>");
		for($i=0; $i<$num_results; $i++){
			$row = mysql_fetch_array($result);
			$SID = $row["SID"];
			$name_result=mysql_query("SELECT Title FROM $Songs WHERE SID=$SID");
			$namerow = mysql_fetch_array($name_result);
			echo("<li>".stripslashes($namerow["Title"])."&nbsp;&nbsp;<small>");
			if(!($_REQUEST['printable']=="yes"))display_song_urls($SID);
			echo("</small></li>");
		}
		echo("</ul>");
	}
	
	echo("</td></tr></table>\n");
?>
	<script language="javascript">
	function editDates(thedate){
		myurl='editDates.php?date='+thedate
		dateWindow = window.open(myurl,'dateWindow','scrollbars=yes,width=500,height=550')
		dateWindow.focus()
	}
	
	function addSongs(thedate){
		myurl='addSongs.php?date='+thedate
		songWindow = window.open(myurl,'songWindow','scrollbars=yes,width=680,height=550')
		songWindow.focus()
	}
	
	function editPersonnel(thedate){
		myurl='editPersonnel.php?date='+thedate
		pWindow = window.open(myurl,'pWindow','scrollbars=yes,width=550,height=550')
		pWindow.focus()
	}

	function editSongOrder(thedate){
		myurl='editSongOrder.php?date='+thedate
		pWindow = window.open(myurl,'pWindow','scrollbars=yes,width=550,height=550')
		pWindow.focus()
	}
	
	function assignParts(thedate){
		myurl='assignParts.php?date='+thedate
		partWin = window.open(myurl,'partWin','scrollbars=yes,width=500,height=550')
		partWin.focus()
	}
	
	</script>
<br>

<?php 
	if(!($_REQUEST['printable']=="yes") && $myWP->userIsLoggedIn() && ($auth_level > 1))
	{ ?>
		<center>
		<a href="javascript:editDates(<?echo("'$date'");?>)">Edit Date Info</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript:addSongs(<?echo("'$date'");?>)">Add or Delete Songs</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript:editSongOrder(<?echo("'$date'");?>)">Song Order</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript:editPersonnel(<?echo("'$date'");?>)">Add or Delete Musicians</a>
		</center>
	<br>
	
	<?php	
	}
	
	
	if($_REQUEST['printable']=="yes")
	{
		echo("<i><a href='".$PHP_SELF."?action=view&printable=no&date=".$date."'>Normal View</a></i>");
	} else {
		//echo("<i><a href='".$PHP_SELF."?action=view&printable=yes&date=".$date."'>Printer-Friendly View</a></i>");
	}
} //END Action =="view" block

} //END AUTHORIZATION "IF" BLOCK
require("footer.php");

function displayDateSelect($returnFieldName)
{
	require("var_config.php");

	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");
	$query = "SELECT Date FROM $Dates ORDER BY Date ASC";
	$result = mysql_query($query);
	$num_results = mysql_num_rows($result);
	$today = date("Y-m-d");
	$selected = 0;
	
	echo("<select name='$returnFieldName'>\n");
	for($i=0; $i<$num_results; $i++){
		$row=mysql_fetch_array($result);
		$date=$row["Date"];
		$dateday = date('D', strtotime($date));
		echo("<option value='$date' ");
		if($date > $today && $selected==0){
			echo("selected");
			$selected=1;
		}
		echo(">$dateday $date</option>\n");
	}
	echo("</select>");
}

?>