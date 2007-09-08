<?php
require("header.php");
?>
<h2>Compose Group Email</h2>

<?php
$email_subject = $_REQUEST["Subject"];
$date = $_POST["thedate"];
echo("<p><strong>Email Subject: $email_subject</strong></p><br/>\n");
echo("<form action='sendMail.php' method='post'>\n");
echo("<p><strong>Message will be sent to:<br/></strong>\n");
$num_results = $_REQUEST["numEmails"];
for($i=0; $i<$num_results; $i++){
	$fieldname = "Email$i";
	$fieldvalue = $_REQUEST["$fieldname"];
	echo("$fieldvalue <br/>\n");
	echo("<input type='hidden' name='$fieldname' value='$fieldvalue'>\n");
}
echo("<br/></p>");
echo("Sender's Email Address: &nbsp;<input type='text' name='From' size=35/><br/><br/>\n");
echo("<textarea rows=15 cols=60 name='Body'>\n");
echo("Enter text of email here \n</textarea><br/><br/>\n");

echo("<input type='hidden' name='date' value='$date'>\n");
echo("<input type='hidden' name='numEmails' value='$num_results'>\n");
echo("<input type='hidden' name='Subject' value='$email_subject'>\n");
echo("<input type='submit' name='submit' value='Send Message'\n");
echo("</form>");
require("footer.php");
?>