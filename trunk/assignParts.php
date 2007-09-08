<html>
<head><title>Assign Parts</title><link rel='stylesheet' type='text/css' href='qrcc.css' /></head>
<body onload="javascript:reloadMain()" class="popup">
<script language=javascript>
function reloadMain(){
	opener.location.reload()
}
</script>
<?php
	require("var_config.php");
	$date = $_REQUEST['date'];
	if(isset($_REQUEST['action'])) $action = $_REQUEST['action'];
	$PHP_SELF = $_SERVER['PHP_SELF'];
		
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");

	if($action=="update"){
		$query="SELECT * FROM $SchedDates WHERE Date='$date'";
		$res = mysql_query($query);
		$num_res = mysql_num_rows($res);
		echo("Updated!<br>");
		for($i=0; $i<$num_res; $i++){
			$row=mysql_fetch_array($res);
			$UID = $row["UID"];
			$thePart=$_REQUEST["$UID"];
			$query = "UPDATE $SchedDates SET Part='".$thePart."' WHERE UID=$UID AND Date='$date'";
			$result = mysql_query($query);
		}
	}

		
	$query="SELECT * FROM $SchedDates WHERE Date='$date'";
	$result = mysql_query($query);
	@ $num_results = mysql_num_rows($result);
	if($num_results > 0){
		echo("<form action='assignParts.php' method='post'>\n");
		echo("<table border=1><tr><th>Name</th><th>Part</th></tr>\n");
		for($i=0; $i<$num_results; $i++){
			$row=mysql_fetch_array($result);
			$query = "SELECT Name FROM $Personnel WHERE UID=".$row["UID"];
			$name_result = mysql_query($query);
			$name_row=mysql_fetch_array($name_result);
			echo("\n<tr><td>".$name_row["Name"]."</td><td><input type='text' size='20' name='".$row["UID"]."' value='".$row["Part"]."' /></td></tr>");
		}
		echo("\n</table>");
		echo("<input type='hidden' name='date' value='$date'>\n");
		echo("<input type='hidden' name='action' value='update'/><input type='submit' value='Update'/>");
		echo("</form>");
	}
	else echo("No one is scheduled for $date.");
?>

</body>
</html>