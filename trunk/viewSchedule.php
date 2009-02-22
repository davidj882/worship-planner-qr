<?php

require("header.php");
require("functions.php");

if((!isset($_SESSION['globalStart'])||!isset($_SESSION['globalEnd']))&&$action!="view")
{
	showGlobalDateSelect($PHP_SELF);
}

//	if($action=="view")
//	{
//		if((isset($_REQUEST['sDate'])&& isset($_REQUEST['eDate'])))
//		{
//			$sDate = $_REQUEST['sDate'];
//			$eDate = $_REQUEST['eDate'];
//		} else {
//			$sDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['smonth'],$_REQUEST['sday'],$_REQUEST['syear']));
//			$eDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['emonth'],$_REQUEST['eday'],$_REQUEST['eyear']));
//		}
//		
//		//session_register("globalStart", "globalEnd");
//		$_SESSION['globalStart']=$sDate;
//		$_SESSION['globalEnd']=$eDate;
//	}
	
	if(isset($globalStart) && isset($globalEnd)) {
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");
	
// Get all Dates in this Range	
	$query = "SELECT Date FROM $Dates WHERE Date >= '$globalStart' and Date <= '$globalEnd' ORDER BY Date ASC";
	$dateResult = mysql_query($query);
	$num_date_results = mysql_num_rows($dateResult);
	for($i=0;$i<$num_date_results;$i++){
		$row = mysql_fetch_array($dateResult);
		$date[$i]=$row["Date"];
	}
// Get Names of Personnel on Worship Team
	$query="SELECT $Personnel.UID, $Personnel.Name FROM $Personnel,$WorshipTeam WHERE $Personnel.UID=$WorshipTeam.UID ORDER BY Name";
	$result=mysql_query($query);
	$num_Name_results=mysql_num_rows($result);
	for($i=0;$i<$num_Name_results;$i++){
		$row = mysql_fetch_array($result);
		$name[$i]=$row["Name"];
		$userID[$i]=$row["UID"];
	}
	
// Create Table for Viewing Schedule
	echo("<table border=2 cellspacing=0>");
	echo("<tr><th>Date/Name</th>\n");
	for($i=0;$i<$num_Name_results;$i++){
		echo("<th width=100>".$name[$i]."</th>\n");
	}
	echo("</tr>\n");

	for($i=0;$i<$num_date_results;$i++){
		$dateday = date('D', strtotime($date[$i]));
		echo("<tr><th><a href=\"planning.php?date=".$date[$i]."&action=view\">$dateday ".date_convert($date[$i],3)."</a></th>\n");
		
		for($j=0; $j<$num_Name_results; $j++){
			$query = "SELECT Part FROM $SchedDates WHERE UID='".$userID[$j]."' AND Date='".$date[$i]."'";
			$result = mysql_query($query);
			$row=mysql_fetch_array($result);
			$part = $row["Part"];
			if(!$part)$part = "&nbsp;";
			echo("<td align='center' ");
			if(mysql_num_rows($result)==0)echo("bgcolor='#888888'");
			echo(">".$part."</td>\n");  
		}

		echo("</tr>\n");
	}
	echo("</table>");

	}


require("footer.php");
?>