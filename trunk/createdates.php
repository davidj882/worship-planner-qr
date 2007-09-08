<?php
//require("header.php");
require("var_config.php");
	$PHP_SELF = $_SERVER['PHP_SELF'];
echo("<html><head><title></title></head><body>\n");
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");

	$timestamp = mktime(0,0,0,$startDate["Month"],$startDate["Day"],$startDate["Year"]);
	$maxstamp = mktime(0,0,0,$endDate["Month"],$endDate["Day"],$endDate["Year"]);
	$day = $startDate["Day"];
	
	echo("First Date:".date("M-d-y",$timestamp)."<br>");
	
	while($timestamp < $maxstamp) {
		//if($day > $startDate["Day"]){
		//	$query=$query.";";
		//	$query=$query."INSERT INTO $Dates VALUES ('".$timestamp."','','')";
		//} else {
		//	$query="INSERT INTO $Dates VALUES ('".$timestamp."','','')";
		//}		
		$query="INSERT INTO $Dates (Date,Theme,Series) VALUES ('".date("Y-m-d",mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]))."','','')";
		if($success = mysql_query($query)){
					echo($query."<br>");
		}else {echo(mysql_error());}
		
		$day = $day + 7;
		$timestamp = mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]);
	}	
	//echo($query);
	//$success = mysql_query($query);
	//echo(mysql_error() );
	//echo("<br>Last Date:".date("M-d-y",$timestamp));

//require("footer.php");
echo("</body></html>");
?>