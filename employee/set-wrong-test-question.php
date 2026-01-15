<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	$setBox		=   $_GET["setBox"];
	$_SESSION['isSetWrongAnswer'] = $setBox;
	
?>