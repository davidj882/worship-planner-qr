<?php
require("header.php");
require("functions.php");



if(!$myWP->userIsLoggedIn()) 
{
	echo("<h2>Log in to View this Page!</h2>");
} else {

	//get request variables
	if(isset($_REQUEST['userID']) && is_numeric($_REQUEST['userID'])) 
	{
	$userID = $_REQUEST['userID'];
	} else {
	$userID = -1;
	}
	
switch($action)
{
	case 'confirm':
// CONFIRM ADDITION OF NEW USER

		if(!$_REQUEST['newName'] || !$_REQUEST['newPass'] || !$_REQUEST['newAuth'] || !is_numeric($_REQUEST['newAuth']))
		{
			echo("Missing Information, or wrong data type!");
		} else {  //all information is there; keep going
	
		$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
		
		$newName = $myWP->dbSanitize($_REQUEST['newName']);
		$newPhoneAdd = $myWP->dbSanitize($_REQUEST['newPhoneAdd']);
		$newEmail = $myWP->dbSanitize($_REQUEST['newEmail']);
		$cryptpass = crypt($_REQUEST['newPass']);
		$newAuth = $myWP->dbSanitize($_REQUEST['newAuth']);
		
		$query = "INSERT INTO ". $Personnel;
		$query.= " (UID, Name, PhoneAdd, Email, Password, AuthLevel) ";
		$query.= " Values ('','$newName','$newPhoneAdd','$newEmail','$cryptpass','$newAuth')";
		$result = mysql_query($query);
		
		if($result)
		{
			echo("Name Added!<br><br>");
		
			//Create Default Availability Records for all dates in database
			$query = "SELECT UID FROM " . $Personnel;
			$query.= " WHERE Name='$newName'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$userID = $row["UID"];
		
			$query = "SELECT Date FROM ".$Dates;
			$result = mysql_query($query);
			$num_results = mysql_num_rows($result);
		
			for($i=0;$i<$num_results;$i++)
			{
				$row = mysql_fetch_array($result);
				$date = $row["Date"];
				$query = "INSERT INTO " . $Availability . " VALUES('$date','$userID','2')";
				$success = mysql_query($query);
				if(!$success)echo("Error in iteration $i");
			}
		} else {
			echo("An Error Occurred While Processing the Request: ".mysql_error()."<br>");
		}
		
		
		if($_REQUEST['newWT']=="Yes"){
			$query = "INSERT INTO " . $WorshipTeam ." (UID) Values ('$userID')";
			@ $result = mysql_query($query);
		}
		
	
		echo("<form action='".$PHP_SELF."' method='post'><input type='submit' value='Return' /></form>");
	
		}
	break;
	
	
	case 'update':

		// PERFORM SQL QUERY TO CHANGE USER INFO USER

		$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
	
		$newName = mysql_real_escape_string($_REQUEST['newName']);
		$newPhoneAdd = mysql_real_escape_string($_REQUEST['newPhoneAdd']);
		$newEmail = mysql_real_escape_string($_REQUEST['newEmail']);
		//$cryptpass = crypt($_REQUEST['newPass']);
		$newAuth = $_REQUEST['newAuth'];

		$query = "UPDATE $Personnel SET Name='$newName',phoneAdd='$newPhoneAdd',Email='$newEmail', AuthLevel=$newAuth WHERE UID='$userID'";
		$result = mysql_query($query);
		if($result){
			echo("Record Updated!<br><br>");
		}else{
			echo("$query<br>Error:".mysql_error());
		}

		if(isset($_REQUEST['pass1']) && $_REQUEST['pass1']!="" && ($_REQUEST['pass1'] == $_REQUEST['pass2'])) {
			$cryptpass = crypt($_REQUEST['pass1']);
			$query = "UPDATE $Personnel Set Password='$cryptpass' WHERE UID='$userID'";
			$result = mysql_query($query);
			if($result)echo("Password Updated!<br><br>");
		} else {
			if($pass1)echo("Passwords do not match<br><br>");
		}
		
		if($_REQUEST['newWT']=="Yes"){
			$query = "INSERT INTO $WorshipTeam (UID) Values ('".$userID."')";
			echo("Member of worship team<br>");
			@ $result = mysql_query($query);
		} else {
			$query = "DELETE FROM $WorshipTeam WHERE UID=$userID";
			echo("Not a member of worship team<br>");
			@ $result = mysql_query($query);
		}
	
	
		echo("<form action='".$PHP_SELF."' method='post'><input type='submit' value='Return' /></form>");
	
	break;
	
	case 'add':
	// DISPLAY FORM FOR ADDING NEW USER
		echo("<h2>Add a new user:</h2><br>");
		echo("<form action='".$PHP_SELF."' method='post'>\n");
	?>
	
		Name: <br><input type="text" size="30" name="newName" /><br><br>
		Address &amp; Phone:<br><textarea name="newPhoneAdd" rows="5" cols="30">Please Enter</textarea><br><br>
		Email Address: <br><input type="text" size="35" name="newEmail" value="Email"/><br><br>
		Password (20 chars max):<br><input type="password" name="newPass"/><br><br>
		Authorization Level (1 = normal user, 2 = worship leader, 3 = administrator):<br>
		<input type='text' name='newAuth' value='1' size='1'/><br><br>
		<input type="checkbox" name="newWT" value="Yes"/>Member of Worship Team?<br><br>
		<input type="hidden" name="action" value="confirm" />
	
	<?php
		echo("<input type='submit' value='Add' />\n</form>");
		
		
	break;

	case 'Delete':
		// DELETE USER
		if($myWP->userIsAdmin()){
			$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
			$delUID = $userID;
			$query = "SELECT Name FROM $Personnel WHERE UID='$delUID'";
			$result=mysql_query($query);
			$row = mysql_fetch_array($result);
			echo("<form action='".$PHP_SELF."' method='post'>");
			echo("\n<input type='hidden' name='userID' value='".$delUID."'/>\n");
			echo("<h2>Do you really want to delete ".$row["Name"]." from the system?<h2>");
			echo("<input type='submit' name='action' value='Confirm Delete'/> <input type='submit' name='action' value='Cancel'/>\n</form>");
		} else {
			echo("Not Authorized to Delete Users!");
		}
	
	break;
	
	case 'Confirm Delete':
		// ACTUALLY Do the deletion
		if($myWP->userIsAdmin())
		{
			$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
		
			$tablename[0] = $Personnel;
			$tablename[1] = $Availability;
			$tablename[2] = $SchedDates;
			
			$delUID = $userID;
			if(is_numeric($delUID))
			{
				$num_errors=0;
				for($i=0; $i<3; $i++)
				{
					$query="DELETE FROM ".$tablename[$i]." WHERE UID='$userID'";
					$result=mysql_query($query);
					if(!$result) 
					{
						echo("Error: ".mysql_error()."<br>");
						$num_errors++;
					} else {
						if($result) echo("Entries deleted from table $tablename[$i]<br>");
					}
				}
				if($num_errors == 0) echo("Success!<br>");
			} else {
				echo("Not a valid user ID.");
			}
		}
	break;
	
	case 'Edit':
	//EDIT USER INFO

		if(!isset($_REQUEST['userID']) || !is_numeric($_REQUEST['userID']))
		{
			 echo("Not a valid user!");
		} else { 
			//$userID = $_REQUEST['userID'];
			if(!$myWP->userIsAdmin()) $userID=$auth_UID; // KEEP A SNEAKY PERSON FROM EDITING SOMEONE ELSE'S INFORMATION
			
			$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
	
			$query="SELECT * FROM " . $Personnel ." WHERE UID='$userID'";
	
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			
			$wtmember = "No";
			$query="SELECT * FROM $WorshipTeam WHERE UID='$userID'";
			//echo("$query");
			$wtresult = mysql_query($query);
			//echo(mysql_num_rows($wtresult));
			//echo("UID=$userID , table = $WorshipTeam");
			if(mysql_num_rows($wtresult)>0) $wtmember="Yes";
	
	
			//echo("Editing information for ".stripslashes($row["Name"])."<br>");
	
		
		echo("<form action='".$PHP_SELF."' method='post'>\n");
		echo("Name: <br><input type='text' size='30' name='newName' value='".stripslashes($row["Name"])."'/><br><br>");
		echo("Address &amp; Phone:<br>");
		echo("<textarea name='newPhoneAdd' rows='5' cols='30'>".stripslashes($row["PhoneAdd"])."</textarea><br><br>");
		echo("Email Address: <br><input type='text' size='35' name='newEmail' value='".stripslashes($row["Email"])."'/><br><br>");
		echo("New Password (leave blank to keep old password):<br><input type='password' name='pass1' size=20/> \n");
		echo("Confirm: <input type='password' name='pass2' size=20/><br><br> \n");
		if($myWP->userIsAdmin()){
			echo("Authorization Level (1 = normal user, 2 = worship leader, 3 = administrator):<br>");
			echo("<input type='text' name='newAuth' value='".$row["AuthLevel"]."' size='1'/><br><br>\n");
			echo("<input type='checkbox' name='newWT' value='Yes' ");
			if($wtmember=="Yes")echo("checked");
			echo(" />Member of Worship Team?<br><br>");
		} else {
			echo("<input type='hidden' name='newWT' value='");
			if($wtmember=="Yes"){
				echo("Yes");
			}else{
				echo("No");
			}
			echo("'>\n");
			echo("<input type='hidden' name='newAuth' value='".$row["AuthLevel"]."' size='1'/><br>\n");
		}
		echo('<input type="hidden" name="action" value="update" />');
		echo('<input type="hidden" name="userID" value="'.$userID.'" />');	
	
	
		echo("<br><br><input type='submit' value='Update' />\n</form>");
	
		}
		//echo("<form action='".$PHP_SELF."' method='post'>\n<input type='submit' value='Done' />\n</form>");
	
	break;

	default:
	// DEFAULT BEHAVIOR : LIST MEMBERS
		$dbh=$myWP->dbConnect() or die ('I cannot connect to the database because: ' . mysql_error());
		
		$query = "select * from $Personnel ORDER BY Name";
		$users = mysql_query($query);
		
		$num_results = mysql_num_rows($users);
		
		echo("<h2>Viewing Personnel</h2><br>");
		?>
	
		<form action="".$PHP_SELF."" method="post">
	
		<table border=1 cellpadding=10 cellspacing=0>
		<tr>
		<?PHP if ($myWP->userIsAdmin()) echo("<th>Edit</th>"); // ONLY ADMINISTRATORS CAN EDIT INFO FOR EVERYONE 
		?> 
		<th>Name</th><th>Address/Phone</th><th>Email Address</th>
		</tr>
		<?php
	
		for($i=0; $i<$num_results; $i++) {
			$row = mysql_fetch_array($users);
			echo("<tr>");
			if($myWP->userIsAdmin()) echo("<td><input type='radio' name='userID' value='".stripslashes($row["UID"])."'></td>");
			echo("<td valign='top'>".stripslashes($row["Name"])."</td>");
			echo("<td valign='top'>".replace_line_breaks(stripslashes($row["PhoneAdd"]))."</td>");
			echo("<td valign='top'>".stripslashes($row["Email"])."</td></tr>");
		}
	
		echo("</table><br>");
	?>
	
	<input type="submit" name="action" value="Edit"> 
	
	<?php if($myWP->userIsAdmin()) { ?>
	
	<input type="submit" name="action" value="Delete"/>
	</form>
	<form action="".$PHP_SELF."" method="post">
	<input type="hidden" name="action" value="add">
	<br>
	<input type="submit" value="Add New User">
	</form>
	
	<?php
	} else {  
		echo(" <i>Click to Edit Your Personal Information</i>\n<input type='hidden' name='userID' value='$auth_UID'/></form>\n");
	}
	
} // CLOSE SWITCH BLOCK

} // CLOSE AUTHORIZATION "IF" BLOCK
require("footer.php");
?>