<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	
	
?>

<iframe src="https://docs.google.com/forms/d/1g6V8RJ3eytMAJrhEBeWOl1dpwe9Eo14QvEezApYa73g/viewform?embedded=true" width="760" height="650" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>

<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>