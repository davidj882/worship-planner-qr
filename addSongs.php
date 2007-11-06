<?php require 'var_config.php'; ?>
<html><head><title>Add Songs</title><link rel='stylesheet' type='text/css' href='<?php echo $stylesheet;?>' /></head>
<body onload="javascript:reloadMain()" class="popup">
<script language=javascript>
function reloadMain(){
	opener.location.reload()
}
</script>

<?php

	require("var_config.php");
	if(isset($_REQUEST['action'])) $action = $_REQUEST['action'];
	$date = $_REQUEST['date'];
	$SID = $_REQUEST['SID'];
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");

// ADD SONG IF NECESSARY
if($action=="add"){
	$query = "SELECT * FROM $SongDates WHERE Date='$date' and SID=$SID";
	$result = mysql_query($query);
	if($result){
		$num_results=mysql_num_rows($result);
		if($num_results==0){
			$query="INSERT INTO $SongDates (Date,SID) VALUES ('$date',$SID)";
			$result = mysql_query($query);
			if($result)echo("Song successfully added!<br>");
		}
	if(!$result || $num_results>0)	echo("That song is already selected for $date!</br>");
	}
}

if($action=="delete"){
	$query = "DELETE FROM $SongDates WHERE Date='$date' and SID=$SID";
	$result = mysql_query($query);
	if($result){
		if($result)echo("Song successfully deleted!<br>");
		}
	if(!$result) echo("Error Deleting Song</br>");
	}


	
// LIST CURRENT SONGS
$query = "SELECT * FROM $Songs ORDER BY Title";
$result = mysql_query($query);
$num_results=mysql_num_rows($result);
echo("<h2>Editing Songlist for $date</h2>");
echo("<i>Shaded rows indicate songs that are retired</i><br>");
echo("<form action='songPopup.php' method='post'>\n");
echo("<table border=1 cellpadding=5 cellspacing=0>\n");
echo("<tr><th>&nbsp;</th><th>Title</th><th>Author</th><th>Publisher</th><th>Year</th><th>Last Used</th></tr>\n");

for($i=0; $i<$num_results; $i++){
	$row=mysql_fetch_array($result);
	$query = "SELECT * FROM $SongDates WHERE Date='$date' and SID=".$row["SID"];
	$test_result = mysql_query($query);
	@ $num_test_res=mysql_num_rows($test_result);
	
	$lastused = "";
	$SID = $row["SID"];
	$lastusedquery = "SELECT * FROM $SongDates WHERE SID='$SID' ORDER BY Date DESC";
	$lastusedresult = mysql_query($lastusedquery);
	if (mysql_num_rows($lastusedresult) > 0)
	{
		$lastusedrow=mysql_fetch_array($lastusedresult);
		$lastused = $lastusedrow["Date"];
	} else {
		$lastused = "Never";
	}
	
	echo("<tr ");
	if($row["Current"]==0)echo("class='shadedrow'");
	echo(">\n<td>");
	if($num_test_res > 0)echo("<small><small><i><a href='addSongs.php?action=delete&date=$date&SID=".$row["SID"]."'>Remove</a></i></small></small>");
	echo("</td>");
	echo("<td>");
	echo("<a href='addSongs.php?action=add&date=$date&SID=".$row["SID"]."'>".stripslashes($row["Title"]));
	echo("</a></td><td>".stripslashes($row["Author"])."&nbsp;</td><td>".stripslashes($row["Publisher"])."&nbsp;</td><td>".$row["Year"]."&nbsp;</td>\n");
	echo("<td>$lastused</td></tr>\n");
}
echo("</table>");

?>
</body>
</html>
