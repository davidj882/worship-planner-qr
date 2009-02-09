<?php

class WorshipPlanner
{
	var $dbhost;
	var $dbuser;
	var $dbpass;
	var $dbname;
	
	var $auth_levels = array('none'=>0, 'User'=>1, 'Leader'=>2, 'Administrator'=>3);
	
	//TABLE NAMES;
	var $tableNames;
	var $Users;
	
	
	function setTableNames($pre)
	{
		$this->tableNames['Personnel'] = $pre.'Personnel';
		$this->tableNames['Dates'] = $pre.'Dates';
		$this->tableNames['SchedDates'] = $pre.'SchedDates';
		$this->tableNames['SongDates'] = $pre.'SongDates';
		$this->tableNames['Sheetmusic'] = $pre.'Sheetmusic';
		$this->tableNames['Songs'] = $pre.'Songs';
		$this->tableNames['WorshipTeam'] = $pre.'WorshipTeam';
		$this->tableNames['Availability'] = $pre.'Availability';
	}
	
	function getTableName($t)
	{
		return $this->tableNames[$t];
	}
	
	function getTableNames()
	{
		return $this->tableNames;
	}
	
	function setDBInfo($dbhost, $dbuser, $dbpass, $dbname)
	{
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
	}
	
	function dbConnect()
	{
		$dbh=mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpass) or die ('I cannot connect to the database because: ' . mysql_error());
		//if($dbh) echo("Connection Successful!");
		mysql_select_db($this->dbname);
		return $dbh;
	}
	
	function userIsAdmin()
	{
		if(isset($_SESSION['auth_level']) && $_SESSION['auth_level']==$this->auth_levels['Administrator'])
		{
			return true;
		} else {
			return false;
		}
	}
	
	function userIsLeader()
	{
		if(isset($_SESSION['auth_level']) && $_SESSION['auth_level']==$this->auth_levels['Leader'])
		{
			return true;
		} else {
			return false;
		}
	}

	function userIsUser()
	{
		if(isset($_SESSION['auth_level']) && $_SESSION['auth_level']==$this->auth_levels['User'])
		{
			return true;
		} else {
			return false;
		}
	}	
	
	function userIsLoggedIn()
	{
		if(isset($_SESSION['auth_level']) && $_SESSION['auth_level'] > 0)
		{	//verify that password matches database
			$Personnel = $this->getTableName('Personnel');
			$this->dbConnect();
			$theUID = $_SESSION['auth_UID'];
			$password = $_SESSION['auth_pass'];
			$query = "SELECT Password, AuthLevel FROM $Personnel WHERE UID='$theUID'";
			
			if($result = mysql_query($query))
			{
				$row = mysql_fetch_array($result);
				$cryptpass = crypt($password,$row["Password"]);	
				
				if($password==$row["Password"]) 
					return true;	
				else 
					return false;
			} else {
				echo(mysql_error());
				return false;
			}
			return true;
		} else {
			return false;
		}	
		return false;
	}
	
	function getUserName($UID=0)
	{	
		if(!is_numeric($UID)) return 'nobody';
		
		if($UID==0)
		{
			if(isset($_SESSION['auth_user']))
			{	
				return $_SESSION['auth_user'];
			} else {
				return 'nobody';
			}	
		}else{
			$this->dbConnect();
			$Personnel = $this->getTableName('Personnel');
			$query = "SELECT Name from $Personnel WHERE UID='$UID'";
			if($name_result = mysql_query($query))
			{
				$row = mysql_fetch_array($name_result);
				$thename = $row['Name'];
				return $thename;
			} else {
				return 'nobody';
			}
		}
	}
	
	function dbSanitize($string)
	{
		$this->dbConnect() or die('Cannot connect to database');
		$newstring = mysql_real_escape_string($string);
		//$newstring = str_replace('%',' ',$string);
		//$newstring = str_replace('_',' ',$string);
		return $newstring;
	}
	

}
?>
