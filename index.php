<?php
require("header.php");


if($myWP->userIsLoggedIn())
{
	echo("<h1>Welcome, ".$myWP->getUserName()."!</h1>");
	echo WELCOME_MESSAGE;
	?>
	<!-- ADD YOUR WELCOME MESSAGE BELOW -->

	
	
	<!--  STOP EDITING HERE -->
	<br>
	<small>
	<i>You are logged in with
	<?php
		if($myWP->userIsAdmin()) echo(" administrative ");
		if($myWP->userIsLeader()) echo(" worship leader ");
		if($myWP->userIsUser()) echo(" normal user ");
	?>
	privileges</i><br>
	</small>
	<?php
} // close authorization if block

require("footer.php");
?>