<?php
require("header.php");
require("functions.php");


if((!isset($_SESSION['globalStart'])||!isset($_SESSION['globalEnd']))&&$action!="view"){
	showGlobalDateSelect($PHP_SELF);
}


if($action=="view" || !$action)
{

	
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
	
	if(isset($_SESSION['globalStart']) && isset($_SESSION['globalEnd']))
	{
//		$globalStart = $_SESSION['globalStart'];
//		$globalEnd = $_SESSION['globalEnd'];
		echo("<h2>Overview of Weekly Worship Gatherings</h2>");
		$dbh=$myWP->dbConnect();
		$query="SELECT * FROM $Dates WHERE Date >='$globalStart' AND Date <= '$globalEnd' ORDER BY Date ASC";
		$result=mysql_query($query);
		$num_date_results = mysql_num_rows($result);
	
	
	// Create table for viewing info
		echo("<table border=2 cellspacing=0 cellpadding=7>");
		echo("<tr>\n<th>Date</th><th>Theme</th><th>Series</th><th>Worship Leader</th>\n");
		echo("</tr>\n");
	
		for($i=0;$i<$num_date_results;$i++){
			$row = mysql_fetch_array($result);
			$query = "SELECT Name FROM $Personnel WHERE UID=".$row["WL"];
			$name_result = mysql_query($query);
			$name_row = mysql_fetch_array($name_result);
			$name = $name_row["Name"];
			$dateday = date('D', strtotime($row["Date"]));
			echo("<tr><td><a href=\"planning.php?date=".$row["Date"]."&action=view\">$dateday ".date_convert($row["Date"],3)."</a></td>\n");
			echo("<td>".htmlentities(stripslashes($row["Theme"]))."</td>\n");
			  echo("<td>".htmlentities(stripslashes($row["Series"]))."</td>\n");
			echo("<td>".$name."</td></tr>\n");
				
			}
		echo("</table>");
	}			
}


require("footer.php");
?>