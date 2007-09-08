<?php
require_once 'header.php';
require_once 'functions.php';

if(!$myWP->userIsAdmin())
{
	echo("<h2>You are not authorized to view this page.</h2><br/>");
	require 'footer.php';
	die();
} 

//TODO input error checking
$startDate = array( 'Month'=>$_POST['smonth'],
					'Day'=>$_POST['sday'],
					'Year'=>$_POST['syear']);
					
$endDate = array( 'Month'=>$_POST['emonth'],
					'Day'=>$_POST['eday'],
					'Year'=>$_POST['eyear']);	

switch($action)
{					
case 'Add Dates':
	if(addDateRange($myWP, $startDate, $endDate, $_POST['recurrence'])) 
		echo '<br/>Dates Successfully Added';
	break;
case 'Yes':
	if(remDateRange($myWP, $startDate, $endDate, $_POST['recurrence'])) 
		echo '<br/>Dates Successfully Deleted';
	break;
case 'Delete Dates':
	?>
	<h2>Are you sure you want to delete all information
	    beginning with <?php echo $startDate['Month']."-".$startDate['Day']."-".$startDate['Year']; ?>,
	    repeating every <?php echo $_POST['recurrence']; ?> day(s),
	    ending <?php echo $endDate['Month']."-".$endDate['Day']."-".$endDate['Year']; ?>?</h2>
	 <form name="ConfirmDelete" action="manageDates.php" method="post">
	 <input type=hidden name="smonth" value="<?php echo $startDate['Month']; ?>" />
	 <input type=hidden name="sday" value="<?php echo $startDate['Day']; ?>" />
	 <input type=hidden name="syear" value="<?php echo $startDate['Year']; ?>" />
	 <input type=hidden name="emonth" value="<?php echo $endDate['Month']; ?>" />
	 <input type=hidden name="eday" value="<?php echo $endDate['Day']; ?>" />
	 <input type=hidden name="eyear" value="<?php echo $endDate['Year']; ?>" />
	 <input type=hidden name="recurrence" value="<?php echo $_POST['recurrence']; ?>" />
	 <input type=submit name="action" value="Yes" />
	 <input type=submit name="action" value="No" />
	 </form>
	<?php

	break;
default:
	ShowGlobalDateSelect($_SERVER['PHP_SELF'], true);
	
	echo("<hr/><h2>Dates Currently in Database:</h2>");
	listDates($myWP, $Dates);
	
	
}
//-------------------------FUNCTIONS----------------------//
function listDates(&$myWP, $Dates)
{
	$myWP->dbConnect();
	$query = "SELECT Date from $Dates ";
	if($result = mysql_query($query))
	{
		?>
		<table>
		
		<?php
		while($row = mysql_fetch_array($result))
		{   
		$date = $row['Date'];
		$dateday = date('D', strtotime($date));
		?>
		<tr><td><?php echo ("$dateday $date"); ?></td></tr>
		<?php
		}
		?>
		</table>
		
		<?php
	}
	
}

function addDate($date)
{
}

function remDate($date)
{
}

function addDateRange(&$myWP, $startDate, $endDate, $interval)
{
	$dbh=$myWP->dbConnect();
	$Dates = $myWP->getTableName('Dates');
	$Availability = $myWP->getTableName('Availability');
	if(!is_int($interval))
	{
		//echo('Interval is not a valid number');
		//return 0;
	}
	
	$timestamp = mktime(0,0,0,$startDate["Month"],$startDate["Day"],$startDate["Year"]);
	$maxstamp = mktime(0,0,0,$endDate["Month"],$endDate["Day"],$endDate["Year"]);
	$day = $startDate["Day"];
	
	//echo("First Date:".date("M-d-y",$timestamp)."<br>");
	
	//TODO Add stuff to other tables like availability
	while($timestamp <= $maxstamp) 
	{
		//TODO check to see if date exists; otherwise will get duplicate stuff
		$thedate = date("Y-m-d",mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]));
		$query = "SELECT * FROM $Dates WHERE Date='$thedate'";
		$result = mysql_query($query);
		$gotaresult=mysql_num_rows($result);
		if($gotaresult > 0){
		echo '<br/>The date already exists: '.$thedate.'<br/>';
		}else{
			$query="INSERT INTO $Dates (Date,Theme,Series) VALUES ('".$thedate."','','')";
			if($success = mysql_query($query))
			{
						echo("<br/> $query <br/>");
			}else {
			echo("<br/>".mysql_error()."<br/>");
			}
			
			$query = "SELECT DISTINCT UID FROM $Availability";
			$result = mysql_query($query);
			
			while($UIDrow = mysql_fetch_array($result))
			{
				$UID = $UIDrow['UID'];
				$query = "INSERT INTO $Availability (Date,UID) VALUES ('";
				$query.= date("Y-m-d",mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]));
				$query.= "', '$UID')";
				
				if($success = mysql_query($query))
				{
							echo($query."<br>");
				}else {
				echo(mysql_error()."<br/>");
				}		
			}
		}
		$day = $day + $interval;
		$timestamp = mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]);
	}	
}

function remDateRange(&$myWP, $startDate, $endDate, $interval)
{
	$dbh=$myWP->dbConnect();
	$Dates = $myWP->getTableName('Dates');
	$Availability = $myWP->getTableName('Availability');
	$SongDates = $myWP->getTableName('SongDates');
	$SchedDates = $myWP->getTableName('SchedDates');
	
	if(!is_int($interval))
	{
		//echo('Interval is not a valid number');
		//return 0;
	}
	
	$timestamp = mktime(0,0,0,$startDate["Month"],$startDate["Day"],$startDate["Year"]);
	$maxstamp = mktime(0,0,0,$endDate["Month"],$endDate["Day"],$endDate["Year"]);
	$day = $startDate["Day"];
	
	echo("First Date:".date("M-d-y",$timestamp)."<br>");
	
	while($timestamp <= $maxstamp) {
		//TODO check to see if date exists
		//TODO delete from other tables too like Availability
		$thedate = date("Y-m-d",mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]));
		
		$query = "DELETE FROM $Dates WHERE Date='$thedate'";
		if($success = mysql_query($query)){
					echo($query."<br>");
		}else {echo(mysql_error()."<br/>");}
		
		$query = "DELETE FROM $Availability WHERE Date='$thedate'";
		if($success = mysql_query($query)){
					echo($query."<br>");
		}else {echo(mysql_error()."<br/>");}
		
		$query = "DELETE FROM $SongDates WHERE Date='$thedate'";
		if($success = mysql_query($query)){
					echo($query."<br>");
		}else {echo(mysql_error()."<br/>");}
		
		$query = "DELETE FROM $SchedDates WHERE Date='$thedate'";
		if($success = mysql_query($query)){
					echo($query."<br>");
		}else {echo(mysql_error()."<br/>");}
		
		$day = $day + $interval;
		$timestamp = mktime(0,0,0,$startDate["Month"],$day,$startDate["Year"]);
		echo("<br/>");
	}	
}

?>