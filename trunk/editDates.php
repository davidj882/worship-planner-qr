<?php
require 'var_config.php'; 
include 'customFields.php';
?>
<html><head><title>Edit Info</title><link rel='stylesheet' type='text/css' href='<? echo $stylesheet;?>' /></head>
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
echo("<h2>Editing Information for $date</h2>");

$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");


// UPDATE
if($action=='update'){
	$newTheme = mysql_real_escape_string($_REQUEST['newTheme']);
	$newSeries = mysql_real_escape_string($_REQUEST['newSeries']);
	$newNotes = mysql_real_escape_string($_REQUEST['newNotes']);
	$newWL = $_REQUEST['newWL'];

	$customString = '';
	if(!empty($customFields)){
	  foreach($customFields as $field=>$label){
	    $getRequestVariableName = 'new'.$field;
	    $newValue = mysql_real_escape_string($_REQUEST[$getRequestVariableName]);
	    $customString = $customString . ", $field='$newValue' ";
	  }
	}
	$query="UPDATE $Dates SET Theme='$newTheme', Series='$newSeries', Notes='$newNotes', WL='$newWL' ";
	$query = $query . $customString . "WHERE Date='$date'";

	$result=mysql_query($query);
	if($result)echo("Update Successful!<br><small>Reload main page to see changes</small><br>");
	else echo("Update Failed.<br>");
}
	
$query = "SELECT * FROM $Dates WHERE Date='$date'";
$result = mysql_query($query);
$row=mysql_fetch_array($result);

// DISPLAY FORM
echo("<form action='editDates.php' method='post'>\n");
?>
<h3>Theme: <input name="newTheme" type="text" size="30" value="<?php echo(stripslashes($row["Theme"]))?>"/><br><br>
<?php
echo("Series: <input type='text' name='newSeries' size='30' value='".stripslashes($row["Series"])."'/><br><br>");
echo("Worship Leader:");

// Display Worship Leader Select Box	
	$query="SELECT UID, Name FROM $Personnel";
	$result=mysql_query($query);
	$num_Name_results=mysql_num_rows($result);
	for($i=0;$i<$num_Name_results;$i++){
		$namerow = mysql_fetch_array($result);
		$name[$i]=$namerow["Name"];
		$userID[$i]=$namerow["UID"];
	}

	echo("<select name='newWL'>\n");
	
	for($i=0;$i<$num_Name_results;$i++){
		echo("<option value='".$userID[$i]."' ");
		if($userID[$i]==$row["WL"])echo("selected");
		echo(">".$name[$i]."</option>\n");
	}
	echo("</select><br>");
// End display WL select


echo("<input type='hidden' name='date' value='$date'><input type='hidden' name='action' value='update'>\n");
if(!empty($customFields)){
  foreach($customFields as $field=>$label){
    $tmpstr = "<br/>$label: <input name='new$field' type='text' size=30 value='".stripslashes($row[$field])."' />";
    echo $tmpstr . "\n";
  }
  echo '<br/>';
}

echo("<br>Notes: <br><textarea name='newNotes' cols=45 rows=8>".stripslashes($row["Notes"])."</textarea>\n");
echo("<br><input type='submit' value='Update'/></form>");

	
?>

</body>
</html>
