<html><head><title>Add or Remove Personnel</title><link rel='stylesheet' type='text/css' href='qrcc.css' /></head>
<body onload="javascript:reloadMain()" class="popup">
<script language=javascript>
function reloadMain(){
	opener.location.reload()
}
</script>

<?php

	require("var_config.php");
	require("functions.php");
	$action = $_REQUEST['action'];
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");

// ADD PERSONNEL IF NECESSARY
if($action=="add"){
	$date = $_REQUEST['date'];
	$UID  = $_REQUEST['UID'];
	$part = $_REQUEST['part'];
	
	$query = "SELECT * FROM $SchedDates WHERE $SchedDates.Date='$date' and $SchedDates.UID=$UID";
	$result = mysql_query($query);
	if($result){
		$num_results=mysql_num_rows($result);
		if($num_results==0){
			$query="INSERT INTO $SchedDates (Date,UID,Part) VALUES ('$date',$UID,'$part')";
			$result = mysql_query($query);
			if($result)echo("Musician successfully added!<br>");
		}
	if(!$result || $num_results>0)	echo("That person is already scheduled for $date!</br>");
	}
}

// DELETE PERSONNEL IF NECESSARY
if($action=="delete"){
	$date = $_REQUEST['date'];
	$UID  = $_REQUEST['UID'];

	$query = "DELETE FROM $SchedDates WHERE Date='$date' and UID=$UID";
	$result = mysql_query($query);
	if($result){
		if($result)echo("Person successfully removed from $date list!<br>");
		}
	if(!$result) echo("Error Removing Musician</br>");
}


	
// LIST Available Personnel
$date = $_REQUEST['date'];

$query = "SELECT $Personnel.UID, $Personnel.Name, $Availability.Available FROM $Personnel, $Availability, $WorshipTeam ";
$query = $query."WHERE $Availability.Date='$date' AND $Personnel.UID=$Availability.UID AND $Availability.Available>0 AND $Personnel.UID=$WorshipTeam.UID ORDER BY $Personnel.Name";
$result = mysql_query($query);
$num_results=mysql_num_rows($result);
echo("<h2>Editing Personnel for ".date_convert($date,9)."</h2>");
echo("<i>Shaded rows indicate that person has not indicated their availability for this day!</i><br>");
//echo("<form action='songPopup.php' method='post'>\n");
echo("<table border=1 cellpadding=5 cellspacing=0>\n");
echo("<tr><th>&nbsp;</th><th>Name</th><th>Part</th></tr>\n");

for($i=0; $i<$num_results; $i++){
	$row=mysql_fetch_array($result);
	$query = "SELECT * FROM $SchedDates WHERE Date='$date' and UID=".$row["UID"];
	$test_result = mysql_query($query);
	@ $num_test_res=mysql_num_rows($test_result);
	@ $test_row = mysql_fetch_array($test_result);
	
	echo("<tr ");
	//if($row["Available"]>1)echo("bgcolor=dddddd");
	if($row["Available"]>1)echo("class=shadedrow ");
	echo(">\n<td>");
	if($num_test_res > 0)echo("<small><small><a href='editPersonnel.php?action=delete&date=$date&UID=".$row["UID"]."'><i>Remove</i></a></small></small>");
	echo("</td>");
	echo("<td>");
	echo("<a href='editPersonnel.php?action=add&date=$date&UID=".$row["UID"]."'>".$row["Name"]);
	if($test_row["Part"])$part=$test_row["Part"];
	else $part = "not assigned";
	echo("</a></td><td>$part</td>\n</tr>");
}
echo("</table>");

?>
</body>
</html>