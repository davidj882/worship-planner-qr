</div> <!-- END CONTENT DIV -->
<hr/>

<div id="footer">
<?php

if(isset($_REQUEST['printable']))
{
  $printable = "yes";
}else{
  $printable = "no";
}

if($printable == "yes"){
	echo("\n</body></html>");
}else{
echo("<br><small><i><a href='".$PHP_SELF."?".$QUERY_STRING."&printable=yes'>View Printer-Friendly Version</a></i></small><br>");
echo("<br><br><small><small>Powered by <a href='http://code.google.com/p/worship-planner-qr/'>Worship Planner QR</a> 1.1\n");

	if($myWP->userIsAdmin())
	{
?>
	<br/><br/>
	<!-- Paypal button -->
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
	">
	</form>
	<!-- End Paypal button -->
	<small><em>Only admins see this button</em></small>


<?php
	}
?>
</div> <!-- END FOOTER DIV -->
</div> <!-- END WRAPPER DIV -->
</body>
</html>

<?php
}
?>