<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	$docTitle					=	"Add Feedback";
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/classes/validate-fields.php");
?>
<br />
<iframe src="https://docs.google.com/forms/d/11sKDMOwD_P368lqBM2jfz0SnI-lXDg4D67wTrAfKi2w/viewform?embedded=true" width="760" height="2700" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
<br /><br />
<?php	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");

?>