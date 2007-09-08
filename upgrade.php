<?php
require_once('var_config.php');
$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");
echo '<br/>';
// Add table SongLinks
$query = "CREATE TABLE $SongLinks (SID int(11), URL varchar(90), Description varchar(5))";
if(@ $result = mysql_query($query)){
	echo "Table $SongLinks created.";
}else{
	echo "Error Creating Table $SongLinks - ".mysql_error();
}

echo '<br/>';
// Add field order to Table SongDates
$query = "ALTER TABLE $SongDates ADD `Order` int(11) default '1'";
if(@ $result = mysql_query($query)){
	echo "Column 'Order' added to table $SongLinks";
}else{
	echo "Error adding column to table $SongLinks - ".mysql_error();
}
?>