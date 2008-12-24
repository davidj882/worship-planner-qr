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
// Count Rows
$query = "SELECT * FROM $SongDates WHERE Date='$date'";
if($result = mysql_query($query)){
  $numrows = mysql_num_rows($result);
}

// UPDATE
if($action=='Update'){
  for($i=0; $i<$numrows; $i++){
    $neworder = mysql_real_escape_string($_POST["order" . $i]);
    $newsid = mysql_real_escape_string($_POST["sid" . $i]);
    $query = "UPDATE `$SongDates` SET `Order`='".$neworder."' WHERE `Date`='$date' AND `SID`='".$newsid."'";
    //echo $query;
    if(!$result=mysql_query($query)){
      echo("ERROR: ".mysql_error()."<br/>");
    } 
  }
}
	
$query = "SELECT $SongDates.SID as 'SID', $SongDates.Order as 'Order', $Songs.Title as 'Title' FROM $SongDates,$Songs WHERE $SongDates.Date='$date' and $SongDates.SID=$Songs.SID ORDER BY $SongDates.Order";
//echo($query);
if (!$result = mysql_query($query)){
  echo mysql_error();
}

// DISPLAY FORM
echo("<form action='editSongOrder.php' method='post'>\n");
?>
<input type='hidden' name='date' value='<?php echo($date); ?>'/>
<?php
   for ($i=0; $i<mysql_num_rows($result); $i++){
     $row=mysql_fetch_array($result);
     echo("<input type='text' name='order".$i."' size=3 value='".$row["Order"]."' />".$row["Title"]."<br/>\n");
     echo("<input type='hidden' name='sid".$i."' value='".$row["SID"]."' />\n");
   }
?>
<input type="submit" name="action" value="Update" />
</form>

</body>
</html>