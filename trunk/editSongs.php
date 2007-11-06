<?php
require("header.php");
require("var_config.php");
require("functions.php");


// **********************************************
//BEGIN MAIN CODE
if($auth_level > 0) {
	
$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");

if(isset($_REQUEST['A'])) $A = $_REQUEST['A'];
if(isset($_REQUEST['Z'])) $Z = $_REQUEST['Z'];
if(!$A) { $A = "A";}
if(!$Z) { $Z = "E";}

// ADD NEW SONG IF NECESSARY
if($action=="add"){
	$newTitle=mysql_real_escape_string($_REQUEST['newTitle']);
	$newPublisher=mysql_real_escape_string($_REQUEST['newPublisher']);
	$newAuthor=mysql_real_escape_string($_REQUEST['newAuthor']);
	$newYear = mysql_real_escape_string($_REQUEST['newYear']);
	
	$query="INSERT INTO $Songs (SID, Title , Author , Publisher , Year , Current) VALUES (";
	$query = $query."'','$newTitle','$newAuthor','$newPublisher','$newYear','1');";
	$result = mysql_query($query);
	if(!$result)echo("Error: ".mysql_error()."<br>");
}

// UPDATE IF NECESSARY
if($action=="Update"){
	if($_REQUEST['Retire']=="Yes")$curr=0;
	else $curr=1;
	$newTitle=mysql_real_escape_string($_REQUEST['newTitle']);
	$newPublisher=mysql_real_escape_string($_REQUEST['newPublisher']);
	$newAuthor=mysql_real_escape_string($_REQUEST['newAuthor']);
	$newYear = mysql_real_escape_string($_REQUEST['newYear']);
	$SID = ($_REQUEST['SID']);
	//$curr = $_REQUEST['curr'];	
	$query="UPDATE $Songs SET Title='$newTitle', Author='$newAuthor', Publisher='$newPublisher', Year='$newYear', Current=$curr WHERE SID=$SID";
	$result = mysql_query($query);
	if(!$result)echo("Error: ".mysql_error()."<br>");
}

// UPLOAD SHEET MUSIC
if($action=="Upload Music"){
	if(!isset($_REQUEST['edit_SID'])){
		echo("No Song Selected!");
	} else {

?>
	<h2>Upload Sheet Music</h2>

	
	<!-- FORM FOR UPLOADING FILE -->
	
	<form enctype="multipart/form-data" action="<?php echo("$PHP_SELF"); ?>	" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
	<input name="userfile" size='30' type="file"><br>
	<small>Make sure there is no punctuation (comma, apostrophe, etc) in the file name!</small><br><br>
	<input type="hidden" name="edit_SID" value="<?echo $_REQUEST['edit_SID']; ?>">
	Short Descriptor <small>(ie Key, file format. <i>5 chars or less</i>)</small><br><input type='text' size='5' name='Description'>
	<br><br><input type="submit" name="action" value="Upload">
	</form>
	<?php
	}
}

// ADD LINK
if($action=="Add Link"){
	if(!isset($_REQUEST['edit_SID'])){
		echo("No Song Selected!");
	} else {

?>
	<h2>Add an External Link</h2>
	<!-- FORM FOR UPLOADING FILE -->
	
	<form enctype="multipart/form-data" action="<?php echo("$PHP_SELF"); ?>	" method="post">
	<!-- <small>Make sure there is no punctuation (comma, apostrophe, etc) in the file name!</small><br><br>-->
	<input type="hidden" name="edit_SID" value="<?echo $_REQUEST['edit_SID']; ?>">
	<br>URL: <input type='text' size='50' name='URL'><br/>
	Short Descriptor <small>(ie Key, file format. <i>10 chars or less</i>)</small><input type='text' size='10' name='Description'>
	<br><br><input type="submit" name="action" value="AddLink">
	</form>
<?php
	}
}

// Add link to database
if($action=="AddLink"){
	$SID = ($_REQUEST['edit_SID']);	
	$URL = $_REQUEST['URL'];
	$Description = $_REQUEST['Description'];
	
	$query =  "INSERT INTO $SongLinks (SID,URL,Description) ";
	$query .= "VALUES ('$SID','$URL','$Description')";
	if($result = mysql_query($query)) echo 'Link Added<br/>';
	if(!$result)echo("Error: ".mysql_error()."<br>");
}

// DELETE SHEET MUSIC
if($action=="Delete Music"){
if($auth_level > 1){
	if(!isset($_REQUEST['edit_SID'])){
		echo("No Song Selected!");
	} else {
		$edit_SID = $_REQUEST['edit_SID'];
		$query = "SELECT $Sheetmusic.*,$Songs.Title FROM $Sheetmusic,$Songs WHERE $Songs.SID=$edit_SID AND $Sheetmusic.SID=$edit_SID";
		if($result = mysql_query($query)){
			$numresults = mysql_num_rows($result);
			echo("<h2>IF YOU'RE SURE YOU WANT TO DO IT,<br>Click on the Description to Delete that Music</h2>\n");
			for($i=0; $i<$numresults; $i++){
				$row = mysql_fetch_array($result);
				echo("<a href=".$PHP_SELF."?URL=".str_replace("+","_isps_",$row["URL"])."&action=deleteURL>");
				echo("Delete music for <b>".$row["Title"]."</b> - ".$row["Description"]."</a><br>");

			}
		}
			
	}
}else{
	echo("You are not authorized to view this page");
}
}

// DELETE Link
if($action=="Delete Link"){
if($auth_level > 1){
	if(!isset($_REQUEST['edit_SID'])){
		echo("No Song Selected!");
	} else {
		$edit_SID = $_REQUEST['edit_SID'];
		$query = "SELECT $SongLinks.*,$Songs.Title FROM $SongLinks,$Songs WHERE $Songs.SID=$edit_SID AND $SongLinks.SID=$edit_SID";
		if($result = mysql_query($query)){
			$numresults = mysql_num_rows($result);
			echo("<h2>IF YOU'RE SURE YOU WANT TO DO IT,<br>Click on the Description to Delete that Link</h2>\n");
			for($i=0; $i<$numresults; $i++){
				$row = mysql_fetch_array($result);
				echo("<a href=".$PHP_SELF."?URL=".str_replace("+","_isps_",$row["URL"])."&action=deleteLink&SID=$edit_SID>");
				echo("Delete link for <b>".$row["Title"]."</b> - ".$row["Description"]."</a><br>");

			}
		}
			
	}
}else{
	echo("You are not authorized to view this page");
}
}

// PERFORM SQL QUERIES TO DELETE MUSIC
if($action=="deleteURL"){
	if($auth_level > 1){
		// TRY TO DELETE FROM DATABASE.  IF SUCCESSFUL, DELETE FILE
		$tempURL = basename($_REQUEST['URL']);
		$URL=str_replace("_isps_","+",$tempURL);
		$badthings = array("%","like","LIKE");
		$okthings = array("o","o","o");
		$URL=str_replace($badthings,$okthings,$URL);
		$query="DELETE FROM $Sheetmusic WHERE URL='$URL'";
		if(@ $result=mysql_query($query)){
			// DELETION SUCCESSFUL, SO TRY TO DELETE FILE
			//if(@ unlink($_SERVER["DOCUMENT_ROOT"].$URL)){
			if( unlink($_SERVER["DOCUMENT_ROOT"].$sheetmusic_loc.$URL)){
				echo("<b>File successfully deleted from system</b>");
			}else{
				echo("<b>Error Deleting File.  $php_errormsg</b>");
			}
		} else {
			// DELETION FAILED
			echo("<b>Cannot Delete from Database.  Contact system Administrator.</b>");
		}
	} else {
		echo("<b>You are not authorized to view this page</b>");
	}
}

// PERFORM SQL QUERIES TO DELETE Link
if($action=="deleteLink"){
	if($auth_level > 1){
		// TRY TO DELETE FROM DATABASE.
		$tempURL = $_GET['URL'];
		$delSID = $_GET['SID'];
		$URL=str_replace("_isps_","+",$tempURL);
		$badthings = array("%","like","LIKE");
		$okthings = array("o","o","o");
		$URL=str_replace($badthings,$okthings,$URL);
		$query="DELETE FROM $SongLinks WHERE URL='$URL' AND SID='$delSID'";
		if(@ $result=mysql_query($query)){
			echo("Link Removed from Database");
		} else {
			// DELETION FAILED
			echo("<b>Cannot Delete from Database.  Contact system Administrator.</b>");
		}
	} else {
		echo("<b>You are not authorized to view this page</b>");
	}
}

//UPLOAD SHEET MUSIC
if($action=="Upload") {
	$userfile_name = $_FILES['userfile']['name'];
	$userfile = $_FILES['userfile']['tmp_name'];
	$userfile_size = $_FILES['userfile']['size'];
	
	
	if ($userfile=="none")
  {
    echo "Problem: no file uploaded";
    exit;
  }

  if ($userfile_size==0)
  {
    echo "Problem: uploaded file is zero length";
    exit;
  }

  if (!is_uploaded_file($userfile))
  {
    echo "Problem: possible file upload attack";
    exit;
  }
  $new_file_name = date("U").str_replace("'"," ",basename($userfile_name));
  $upfile = $_SERVER["DOCUMENT_ROOT"].$sheetmusic_loc.urlencode($new_file_name);

  if (@ !copy($userfile, $upfile))
  {
    echo "Problem: Could not move file into directory";
    exit;
  }
	$new_file_name = mysql_real_escape_string($new_file_name);
	$rdesc = mysql_real_escape_string($_REQUEST['Description']);
  	$query = "INSERT INTO $Sheetmusic (SID,URL,Description) VALUES (".$_REQUEST['edit_SID'].",'";
  	//$query = $query.$sheetmusic_loc.urlencode($new_file_name)."','".$rdesc."')";
	$query = $query.urlencode($new_file_name)."','".$rdesc."')";
  	if($result=mysql_query($query)){
		echo("<h3>File &quot;".$userfile_name."&quot; Successfully Uploaded!</h3>");
	} else {
		echo("Error! ".mysql_error());
	}
}

//VIEW SONG HISTORY
if($action=='viewHistory'){
	$SID = $_REQUEST['SID'];
	$query = "SELECT $SongDates.Date, $Songs.Title FROM $SongDates, $Songs WHERE $SongDates.SID=$SID AND $Songs.SID=$SID ORDER BY $SongDates.Date";
	if($result=mysql_query($query)){
		$numres=mysql_num_rows($result);
		if($numres==0)echo("It appears that this song has never been used.");
		for($i=0; $i<$numres; $i++){
			$row = mysql_fetch_array($result);
			if($i==0)echo("<big><strong>".$row["Title"]."</strong> was scheduled for the following dates:</big><br><br>");
			echo(date_convert($row["Date"],9)."<br>");
		}
	} else {
		echo("An error occurred<br>");
		echo(mysql_error());
	}	
}


if($action=='Edit' || $action=='Delete'){
	$query = "SELECT * FROM $Songs WHERE SID=".$_REQUEST['edit_SID'];
	$result = mysql_query($query);
	$row=mysql_fetch_array($result);
}

if($action=="Delete"){
	echo("<h2><i>Do you really want to delete ".$row["Title"]."?</i></h2>");

	echo("<form action='".$PHP_SELF."' method='post'>\n");
	echo("<input type='hidden' name='SID' value='".$_REQUEST['edit_SID']."'/>\n");
	echo("<input type='submit' name='action' value='Yes'/><input type='submit' name='action' value='No'/>");
	echo("<h3>Note: You should manually delete all sheet music associated with this song before deleting from database.</h3>");	
	echo("\n</form><br><br><br>");
}
if($action=="Yes"){
	$query = "DELETE FROM $Songs WHERE SID=".$_REQUEST['SID'];
	$result=mysql_query($query);
	echo("Song successfully deleted from database!");
}
if($action=="Edit")echo("<h2>Editing Song</h2>");

if(!$action || $action=="Edit" || $action=="add" || $action=="Update"){
	echo("<table border=0 cellpadding=8>");
	echo("<tr valign='top'><td valign='top'>");
		if($auth_level > 1) {
			if(!$action && $auth_level > 1) echo("<h2>Add a Song</h2>");

?>

	<!-- PUT FORM FOR ADDING NEW SONGS AT TOP (or left) OF PAGE -->
	
		<form action="<?php echo("$PHP_SELF");?>" method="post">
		<table border=1 cellpadding=5 cellspacing=0>
		<tr>
		<th align="right">Title:</th><td><input type="text" name="newTitle" 
			value="<?if($action=='Edit')echo(stripslashes($row['Title']));?>"
			size=15 /></td>
		</tr><tr>
		<th align="right">Author:</th><td><input type="text" name="newAuthor" 
			value="<?if($action=='Edit')echo(stripslashes($row['Author']));?>"
			size=15 /></td>
		</tr>
		<tr>
		<th align="right">Pub:</th><td><input type="text" name="newPublisher" 
			value="<?if($action=='Edit')echo(stripslashes($row['Publisher']));else echo("unknown");?>"
			 size=15 /></td>
		</tr><tr>
		<th align="right">Year:</th><td><input type="text" name="newYear"  
			<?if($action=='Edit')echo(" value='".$row["Year"]."' ");else echo(" value='0000' ");?>
			size=15 /></td>
		</tr>
		</table>
		<input type="hidden" name="newCurrent" value=1 />
	
		<?if($action=='Edit'){
			echo("Do you want to retire this song? <input type='radio' name='Retire' value='Yes'>Yes <input type='radio' name='Retire' value='No' selected>No\n");
			echo("<br><input type='hidden' name='SID' value='" . $_REQUEST['edit_SID'] . "'/><input type='submit' name='action' value='Update' />");
		}
		  	else echo("<input type='submit' value='Add Song' /><input type='hidden' name='action' value='add' />");
		
		echo("</form>\n");
		}
		?>
	</td><td valign=top>
		<table border=0>
		<tr><td>
		<h2>Current Songlist:&nbsp;&nbsp;&nbsp;</h2>
		</td><td>
		<? if($A=="A" && $Z=="E") echo("<strong><i>"); ?>
			<a href="<?$PHP_SELF?>?A=A&Z=E">A-E</a>&nbsp;&nbsp;&nbsp;
		<? if($A=="A" && $Z=="E") echo("</i></strong>"); ?>
		<? if($A=="F") echo("<strong><i>"); ?>
			<a href="<?$PHP_SELF?>?A=F&Z=L">F-L</a>&nbsp;&nbsp;&nbsp;
		<? if($A=="F") echo("</i></strong>"); ?>
		<? if($A=="M") echo("<strong><i>"); ?>
			<a href="<?$PHP_SELF?>?A=M&Z=R">M-R</a>&nbsp;&nbsp;&nbsp;
		<? if($A=="M") echo("</i></strong>"); ?>
		<? if($A=="S") echo("<strong><i>"); ?>
			<a href="<?$PHP_SELF?>?A=S&Z=Z">S-Z</a>&nbsp;&nbsp;&nbsp;
		<? if($A=="S") echo("</i></strong>"); ?>
		<? if($A=="A" && $Z=="Z") echo("<strong><i>"); ?>
			<a href="<?$PHP_SELF?>?A=A&Z=Z">All</a>
		<? if($A=="A" && $Z=="Z") echo("</i></strong>"); ?>
		</td>
		</tr>
		</table>
		<br/>
		<small><i>Shaded rows indicate songs that are &quot;retired&quot;, or that we want to phase out</i></small><br>
		
	<?php
	
	
	// LIST CURRENT SONGS
	$query = "SELECT * FROM $Songs WHERE Title REGEXP '^[$A-$Z]' ORDER BY Title";
//	$query = "SELECT * FROM $Songs ORDER BY Title";
	$result = mysql_query($query);
	$num_results=mysql_num_rows($result);
	
	echo("<form action='".$PHP_SELF."' method='post'>\n");
	echo("<table border=1 cellpadding=5 cellspacing=0>\n");
	echo("<tr><th>&nbsp;</th><th>Title</th><th>Download</th><th>Author</th><th>Publisher</th><th>Year</th></tr>\n");
	
	for($i=0; $i<$num_results; $i++){
		$row=mysql_fetch_array($result);
		echo("<tr ");
//		if($row["Current"]==0)echo("bgcolor=dddddd");
		if($row["Current"]==0)echo("class='shadedrow'");
		echo(">\n<td><input type='radio' name='edit_SID' value='".$row["SID"]."' /></td>");
		echo("<td><a href='".$PHP_SELF."?action=viewHistory&SID=".$row["SID"]."'>".htmlentities(stripslashes($row["Title"]))."</a></td><td><small>");
		
		display_song_urls($row["SID"]);
	
			// GET URLS FOR DOWNLOADABLE MUSIC, AND DISPLAY THEM
	/*	$query="SELECT * FROM $Sheetmusic WHERE SID=".$row["SID"];
		
		if(@ $dl_result = mysql_query($query)){
			$dl_rows = mysql_num_rows($dl_result);
			for($j=0; $j < $dl_rows; $j++){
				$dl_row=mysql_fetch_array($dl_result);
				if($j>1)echo(" | ");
				echo("<a href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>");
			}
			if($dl_rows==0)echo("&nbsp;");
		} */
		echo("&nbsp;</small></td><td>".htmlentities(stripslashes($row["Author"]))."&nbsp;</td><td>".htmlentities(stripslashes($row["Publisher"]))."&nbsp;</td><td>".$row["Year"]."&nbsp;</td>\n</tr>");
	}
	echo("</table><br>\n");
	
		if($auth_level > 1) {
			echo("<input type='submit' name='action' value='Edit' /> \n");
			echo("<input type='submit' name='action' value='Delete'/> \n");
			echo("<input type='submit' name='action' value='Upload Music'/>\n");
			echo("<input type='submit' name='action' value='Delete Music'/>\n");
			echo("<input type='submit' name='action' value='Add Link'/>\n");
			echo("<input type='submit' name='action' value='Delete Link'/>\n</form>");
		}
	echo("</td></tr></table>");
}



} // CLOSE AUTHORIZATION BLOCK

require("footer.php");
?>