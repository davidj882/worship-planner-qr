<?php
include 'var_config.php';
include 'customFields.php';

$dbh=mysql_pconnect ("$dbhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("$dbname");

//$query = 'ALTER TABLE  `Dates` ';//ADD  `Lesson1` VARCHAR( 100 ) NOT NULL , ADD  `Psalm` VARCHAR( 100 ) NOT NULL ;';

if(!empty($customFields)){

  foreach($customFields as $field=>$label){
    $query = "ALTER TABLE `Dates` ADD `$field` VARCHAR( $customFieldLength ) NOT NULL;";
    $result=mysql_query($query);
    if($result){
      echo "<p>Field $field successfully added</p>";
    } else {
      echo "<p>Could not add field $field: ". mysql_error()."</p>";
    }

  }

}


?>