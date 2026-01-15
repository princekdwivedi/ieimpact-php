<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	echo date("Y-m-d H:i:s");
	die();
	

	echo "PAN = ".validatePan("WF23G");
	echo "<br />AADHAAR = ".isAadharValid("5235235");
?>