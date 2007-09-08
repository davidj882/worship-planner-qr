<html><head><title>Edit Info</title><link rel='stylesheet' type='text/css' href='qrcc.css' /></head>
<body onload="javascript:reloadMain()" class="popup">
<script language=javascript>
function reloadMain(){
	opener.location.reload()
}
</script>

<?php
require("var_config.php");
$date = $_REQUEST['date'];
$action = $_REQUEST['action'];
$PHP_SELF = $_SERVER['PHP_SELF'];
// BEGIN CONTENT
echo("<h2>Editing Song Order for $date</h2>");

$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");


// UPDATE
if($action=='update'){
	$newTheme = mysql_real_escape_string($_REQUEST['newTheme']);
	$newSeries = mysql_real_escape_string($_REQUEST['newSeries']);
	$newNotes = mysql_real_escape_string($_REQUEST['newNotes']);
	$newWL = $_REQUEST['newWL'];
	
	$query="UPDATE $Dates SET Theme='".$newTheme."', Series='$newSeries', Notes='$newNotes', WL='$newWL' WHERE Date='$date'";
	$result=mysql_query($query);
	if($result)echo("Update Successful!<br><small>Reload main page to see changes</small><br>");
	else echo("Update Failed.<br>");
}
	
$query = "SELECT * FROM $SongDates WHERE Date='$date'";
$result = mysql_query($query);
$row=mysql_fetch_array($result);

// DISPLAY FORM
echo("<form action='editSongOrder.php' method='post'>\n");
?>
<h3>Theme: <input name="newTheme" type="text" size="30" value="<?php echo(stripslashes($row["Theme"]))?>"/><br><br>
<?php
echo("Series: <input type='text' name='newSeries' size='30' value='".stripslashes($row["Series"])."'/><br><br>");
echo("Worship Leader:");



	
?>

</body>
</html>