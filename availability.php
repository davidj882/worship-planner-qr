<?php
require("header.php");
//require("var_config.php");
require("functions.php");

if((!isset($_SESSION['globalStart'])||!isset($_SESSION['globalEnd']))&&$action!="view"){
	showGlobalDateSelect($PHP_SELF);
}

if(isset($_REQUEST['edit_userID']) && is_numeric($_REQUEST['edit_userID']))
{
	$edit_userID = $_REQUEST['edit_userID'];
} else {
	$edit_userID = -1;
}

//$sDate = $_REQUEST['sDate'];
//$eDate = $_REQUEST['eDate'];
//$sDate = $_SESSION['globalStart'];
//$eDate = $_SESSION['globalEnd'];
	
	
switch($action)
{

// EDIT AVAILABILITY FOR USER

case 'edit':
	$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
	
	
	$query = "SELECT * FROM $Availability WHERE UID = '$edit_userID' AND Date >='$sDate' AND Date <= '$eDate' ORDER BY Date ASC";
	
	$result = mysql_query($query);
	
	$edit_userName = $myWP->getUserName($edit_userID);
		
		
	if(!$result)echo("No results returned");

	else {
		echo("Editing $Availability for <strong>$edit_userName</strong><br><br>");
		echo("<form action='".$PHP_SELF."' method='post'>\n<table border=1>\n");
		echo("<input type='hidden' name='action' value='update'>\n");
		echo("<input type='hidden' name='sDate' value='$sDate'>\n");
		echo("<input type='hidden' name='eDate' value='$eDate'>\n");
		echo("<input type='hidden' name='edit_userID' value='$edit_userID'>\n");
		
		$num_date_results = mysql_num_rows($result);

		for($i=0; $i<$num_date_results; $i++){
			$row = mysql_fetch_array($result);
			$date=$row["Date"];
			$dateday = date('D', strtotime($date));
			$av=$row["Available"];
			echo("<tr><td>$date ($dateday)</td><td>");
			dispAvSelect($av,$date);
			echo("</td></tr>\n");
		}
		
		echo("</table><br><input type='submit' value='Update'>\n</form>");
		
	}

	
	break;

// PERFORM UPDATE AVAILABILITY
case 'update':
	// REQUEST VARIABLES ARE ASSIGNED ABOVE
	
	$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
	
	$query = "SELECT * FROM $Availability WHERE UID = '$edit_userID' AND Date >='$sDate' AND Date <= '$eDate' ORDER BY Date ASC";
	$result = mysql_query($query);
	$num_results = mysql_num_rows($result);
	
	for($i=0; $i<$num_results; $i++){
		$row = mysql_fetch_array($result);
		$date = $row["Date"];
		$query = "UPDATE $Availability SET Available='".$_REQUEST["$date"]."' WHERE UID='$edit_userID' AND Date='".$date."'";
		$up_result=mysql_query($query);
		if(!$up_result)echo("$query<br>");
	}
	//TODO Report an error if records are not properly updated.
	?>
	Records Updated!<br>
	<form action='<?php echo "$PHP_SELF"; ?>' method='post'>
	<input type='hidden' name='action' value='view'>
	<input type='hidden' name='sDate' value='<?php echo $sDate; ?>'>
	<input type='hidden' name='eDate' value='<?php echo $eDate; ?>'>
	<input type='submit' value='Return' />
	</form>
	
	<?php
	break;

case 'view':
default:
	$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());

	if($action=="view"){
		if((isset($_REQUEST['sDate'])&& isset($_REQUEST['eDate']))){
			$sDate = $_REQUEST['sDate'];
			$eDate = $_REQUEST['eDate'];
		} else {
			//TODO make sure this is robust...
			$sDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['smonth'],$_REQUEST['sday'],$_REQUEST['syear']));
			$eDate=date("Y-m-d",mktime(0,0,0,$_REQUEST['emonth'],$_REQUEST['eday'],$_REQUEST['eyear']));
		}
		session_register("globalStart", "globalEnd");
		$_SESSION['globalStart']=$sDate;
		$_SESSION['globalEnd']=$eDate;
	
		$sDate=$_SESSION['globalStart'];
		$eDate=$_SESSION['globalEnd'];
		$globalStart = $_SESSION['globalStart'];
		$globalEnd = $_SESSION['globalEnd'];

	}
	
	if(isset($globalStart)&&isset($globalEnd))
	{
		$query="SELECT Date FROM $Dates WHERE Date >='$globalStart' AND Date <= '$globalEnd' ORDER BY Date ASC";
		$result=mysql_query($query);
		$num_date_results = mysql_num_rows($result);
		for($i=0;$i<$num_date_results;$i++)
		{
			$row = mysql_fetch_array($result);
			$date[$i]=$row["Date"];
		}
	
		$query="SELECT $Personnel.UID, $Personnel.Name FROM $Personnel,$WorshipTeam WHERE $Personnel.UID=$WorshipTeam.UID ORDER BY Name";
		$result=mysql_query($query);
		$num_Name_results=mysql_num_rows($result);
		for($i=0;$i<$num_Name_results;$i++)
		{
			$row = mysql_fetch_array($result);
			$name[$i]=$row["Name"];
			$userID[$i]=$row["UID"];
		}
		echo("\n<form action='".$PHP_SELF."' method='post'>\n");
		echo("<input type='hidden' name='action' value='edit'>\n");
		echo("<input type='hidden' name='sDate' value='$sDate'>\n");
		echo("<input type='hidden' name='eDate' value='$eDate'>\n");
		if($myWP->userIsAdmin() || $myWP->userIsLeader()) 
		{
			echo("<h3>Edit Availability on These Dates for: ");
			echo("<select name='edit_userID'>\n");
			for($i=0;$i<$num_Name_results;$i++)
			{
				echo("<option value='".$userID[$i]."'>".$name[$i]."</option>\n");
			}
			echo("</select><br>");
		} else {
			echo("<h3>Edit My Availability on these Dates\n");
			echo("<input type='hidden' name='edit_userID' value='$auth_UID' />\n");
		}
		echo("<input type='submit' value='Edit'></h3>\n</form>\n");
			
	
	// Create table for viewing availability
		echo("<small><i>Y=yes, N=no, U=unknown</i></small><br>\n");
		echo("<table border=1 cellspacing=0>");
		echo("<tr><th>Date/Name</th>\n");
		for($i=0;$i<$num_Name_results;$i++){
			echo("<th width=125>".$name[$i]."</th>\n");
		}
		echo("</tr>\n");
	
		for($i=0;$i<$num_date_results;$i++){
			$dateday = date('D', strtotime($date[$i]));
			echo("<tr><th>$dateday ".date_convert($date[$i],3)."</th>\n");
			
			for($j=0; $j<$num_Name_results; $j++){
				$query = "SELECT Available FROM $Availability WHERE UID='".$userID[$j]."' AND Date='".$date[$i]."'";
				$result = mysql_query($query);
				if(!$result){
					 $av = 2;
				 }
				else {
					$row=mysql_fetch_array($result);
					$av = $row["Available"];
				}
				
				if($av==0){
					$avc="N";
					$bgc="red";
				}
				else if($av==1){
					$avc="Y";
					$bgc="green";
				}
				else {
					$avc="U";
					$bgc="dddd00";
				}
				
				echo("<td align='center' bgcolor='".$bgc."'>".$avc."</td>\n");  
			}
			if($num_Name_results > 4) echo("<th>".date_convert($date[$i],3)."</th>\n");
			echo("</tr>\n");
		}
		echo("</table>");
	}	//THIS ENDS THE STUFF THAT HAPPENS ONLY IF START AND END DATES ARE SET

	
}  //END SWITCH


function dispAvSelect($avail,$date){
	echo("<select name='$date'>\n");
	    //"NO" selection
		echo("<option value='0' ");
		if($avail==0)echo("selected");
		echo(">N</option>\n");
		
		//"YES" selection
		echo("<option value='1' ");
		if($avail==1)echo("selected");
		echo(">Y</option>\n");
		
		//"UNKNOWN" selection
		echo("<option value='2' ");
		if($avail>1)echo("selected");
		echo(">U</option>\n");
		
		echo("</select>\n");
}
	
		

		


require("footer.php");
?>