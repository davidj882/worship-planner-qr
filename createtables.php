<?php

require 'var_config.php';

	$PHP_SELF = $_SERVER['PHP_SELF'];
//Connect to database
$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");


// Table structure for table `Availability`

echo("Creating table $Availability...");
$query = "CREATE TABLE `$Availability` (`Date` date default NULL,`UID` int(11) NOT NULL default '0',";
$query = $query ."  `Available` tinyint(4) NOT NULL default '2', KEY `Date` (`Date`,`UID`)) TYPE=MyISAM";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }

 
// Table structure for table `Dates`
echo("Creating table $Dates...");

$query = "CREATE TABLE `$Dates` (`Date` date NOT NULL default '0000-00-00',";
  $query = $query ."`Theme` varchar(50) NOT NULL default '',";
  $query = $query ."`Series` varchar(50) NOT NULL default '',";
  $query = $query ."`WL` int(11) NOT NULL default '0',";
  $query = $query ."`Notes` text NOT NULL,";
  $query = $query ."PRIMARY KEY  (`Date`)) TYPE=MyISAM";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Table structure for table `Personnel`
echo("Creating table $Personnel...");

$query = "CREATE TABLE `$Personnel` (`UID` int(11) NOT NULL auto_increment,";
  $query = $query ."`Name` varchar(30) NOT NULL default '',";
  $query = $query ."`PhoneAdd` text NOT NULL,";
  $query = $query ."`Email` varchar(50) NOT NULL default '',";
  $query = $query ."`Password` varchar(100) NOT NULL default '$1$0pJmb06c$tvhnEHSKDq0p/nKnQxjCF1',";
  $query = $query ."`AuthLevel` tinyint(4) NOT NULL default '1',";
  $query = $query ."PRIMARY KEY  (`UID`),";
  $query = $query ."UNIQUE KEY `Name` (`Name`)) TYPE=MyISAM AUTO_INCREMENT=31";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Table structure for table `SchedDates`
echo("Creating table $SchedDates...");

$query = "CREATE TABLE `$SchedDates` (`Date` date default NULL,`UID` int(11) default '0',";
  $query = $query ."`Part` char(25) NOT NULL default '',KEY `Date` (`Date`,`UID`)) TYPE=MyISAM";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Table structure for table `Sheetmusic`
echo("Creating table $Sheetmusic...");

$query = "CREATE TABLE `$Sheetmusic` (`SID` int(11) NOT NULL default '0',`URL` varchar(90) NOT NULL default '',";
    $query = $query ."`Description` varchar(5) NOT NULL default '', KEY `SID` (`SID`)) TYPE=MyISAM";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }

// Table structure for table `SongDates`

echo("Creating table $SongDates...");

$query = "CREATE TABLE `$SongDates` (`Date` date NOT NULL default '0000-00-00',`SID` int(11) NOT NULL default '0',`Order` int(11) NOT NULL default '1') TYPE=MyISAM";
if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Table structure for table `Songs`
echo("Creating table $Songs...");

$query = "CREATE TABLE `$Songs` (`SID` int(10) unsigned NOT NULL auto_increment,`Title` varchar(70) NOT NULL default '',";
  $query = $query ."`Author` varchar(50) NOT NULL default '',`Publisher` varchar(50) NOT NULL default 'unknown',";
  $query = $query ."`Year` year(4) NOT NULL default '0000',`Current` tinyint(4) NOT NULL default '1',";
  $query = $query ."`Chart` mediumblob, PRIMARY KEY  (`SID`), UNIQUE KEY `Title` (`Title`)) TYPE=MyISAM AUTO_INCREMENT=74";

  if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }

// Table structure for table `SongLinks`
echo("Creating table $SongLinks...");

$query = "CREATE TABLE $SongLinks (`SID` int(11), `URL` varchar(90), `Description` varchar(5))";
  if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Table structure for table `WorshipTeam`
echo("Creating table $WorshipTeam...");

$query = "CREATE TABLE `$WorshipTeam` (`UID` int(11) NOT NULL default '0',";
  $query = $query ."PRIMARY KEY  (`UID`)) TYPE=MyISAM;";
  
 if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
} else { echo("Create table failed"); }


// Make user Admin	
echo("Creating default user Admin...");
$cryptpass = crypt("admin");
if ($useOldDBAddQuery){
$query = "INSERT INTO $Personnel (UID, Name, PhoneAdd, Email, Password, AuthLevel) Values ('','Admin','000-0000','nobody@nobody.net',";
  } else {
$query = "INSERT INTO $Personnel (Name, PhoneAdd, Email, Password, AuthLevel) Values ('Admin','000-0000','nobody@nobody.net',";
  }
$query = $query."'$cryptpass',3)";

 if($result=mysql_query($query)){
	echo("Success!<br><br>\n");
	echo("Default user is <pre>Admin</pre> with password <pre>admin</pre><br>");
} else { echo("Creation of Admin user failed"); }


?>