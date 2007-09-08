<?php

function date_convert($date,$type){ 
  $date_year=substr($date,0,4); 
  $date_month=substr($date,5,2); 
  $date_day=substr($date,8,2); 
  if($type == 1): 
      // Returns the year Ex: 2003 
      $date=date("Y", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 2): 
      // Returns the month Ex: January 
      $date=date("F", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 3): 
      // Returns the short form of month Ex: Jan 
      $date=date("M-d-y", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 4): 
      // Returns numerical representation of month with leading zero Ex: Jan = 01, Feb = 02 
      $date=date("m", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 5): 
      // Returns numerical representation of month without leading zero Ex: Jan = 1, Feb = 2 
      $date=date("n", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 6): 
      // Returns the day of the week Ex: Monday 
      $date=date("l", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 7): 
      // Returns the day of the week in short form Ex: Mon, Tue 
      $date=date("D", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 8): 
      // Returns a combo ExL Wed,Nov 12th,2003 
      $date=date("D, M jS, Y", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 9): 
      // Returns a combo Ex: November 12th,2003 
      $date=date("M d, Y", mktime(0,0,0,$date_month,$date_day,$date_year)); 
  elseif($type == 10):
	  $date=date("D, Y-m-d", mktime(0,0,0,$date_month,$date_day,$date_year));
  endif; 
  return $date; 
}; 

function replace_line_breaks($mystring){
	//$original = array("\n"," ","\t");
	//$replace = array("<br>","&nbsp;","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	$original = array("\n","\t");
	$replace = array("<br/>","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");	
	$newstring = str_replace($original,$replace,$mystring);
	return $newstring;
}


function display_song_urls($SID){
	
	require("var_config.php");
	$numSheetmusicHits = 0;
	$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("$dbname");
	
	// GET URLS FOR DOWNLOADABLE MUSIC, AND DISPLAY THEM
	$query="SELECT * FROM $Sheetmusic WHERE SID=$SID";
	
	if(@ $dl_result = mysql_query($query)){
		$dl_rows = mysql_num_rows($dl_result);
		for($j=0; $j < $dl_rows; $j++){
			$dl_row=mysql_fetch_array($dl_result);
			if($j>0)echo(" | ");
			//echo("<a href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>"); //OLD WAY
			echo("<a href='".$sheetmusic_loc.$dl_row["URL"]."'>".$dl_row["Description"]."</a>");
		}
		//if($dl_rows==0)echo("&nbsp;");
		$numSheetmusicHits = $dl_rows;
	}
	
	$query="SELECT * FROM $SongLinks WHERE SID=$SID";
	
	if(@ $dl_result = mysql_query($query)){
		$dl_rows = mysql_num_rows($dl_result);
		for($j=0; $j < $dl_rows; $j++){
			$dl_row=mysql_fetch_array($dl_result);
			if($j>0 || $numSheetmusicHits > 0)echo(" | ");
			//echo("<a href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>"); //OLD WAY
			echo("<a class=songlink href='".$dl_row["URL"]."'>".$dl_row["Description"]."</a>");
		}
		if($dl_rows==0)echo("&nbsp;");
	}
}

function ShowGlobalDateSelect($referring_page, $AddDelDatesButtons = false)
{
	if($AddDelDatesButtons)
	{
		echo("<h2>Add or Delete Dates</h2>");
	} else {
		echo("<h2>Select a Range of Dates</h2>");
	}
?>
	<form action='<?php echo $referring_page; ?>' method='post'>
	<table border=0 />
	<tr>
	<th>Start Date:</th>
	<td><input type='text' name='smonth' value='M' size='2'></td>
	<td><input type='text' name='sday' value='D' size='2'></td>
	<td><input type='text' name='syear' value='Y' size='4'></td>
	</tr>
	<tr>	
	<th>End Date:</th>
	<td><input type='text' name='emonth' value='M' size='2'></td>
	<td><input type='text' name='eday' value='D' size='2'></td>
	<td><input type='text' name='eyear' value='Y' size='4'></td>
	</tr>
	<tr>
	<td colspan=4 align=center>
	<?php
	if(!$AddDelDatesButtons)
	{  ?>
	<input type='hidden' name='action' value='view'>
	<input type='submit' value='Submit' /> &nbsp;<input type='reset' value='Reset' />
	<?php
	} else {
	?>
	
	Recurrence: Every <input type='text' name='recurrence' value='7' size='2' /> days.
	</td>
	</tr>
	
	<tr>
	<td colspan=4 align='center'>
	<input type='submit' name='action' value='Add Dates' />
	<input type='submit' name='action' value='Delete Dates' />
	<!--<input type='reset' value='Reset' />-->

	<?php
	} ?>
	</td>
	</tr>
	</table>
	</form>
	
	<?php
	if($AddDelDatesButtons)
	{ ?>
	<small><em>
	*To delete all dates in range, set Recurrence to 1.<br/>
	*To add/delete one date, set start date = end date
	<br/></em></small>
<?php
	}
}

?>