<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES ."/classes/employee.php");
	$employeeObj =	new employee();
	$platform	 =	"";
	if(isset($_GET['platform']))
	{
		$platform=	$_GET['platform'];
	}
	if(!empty($platform) && $platform > 0)
	{
		echo "<select name='customerId'>";
		echo "<option value=''>Select</option>";
		$temp			=	"a_platform$platform";
		$a_platform		=	$$temp;
		foreach($a_platform as $key=>$value)
		{
			echo "<option value='$key'>$value</option>";
		}
		echo "</select>";
	}
	else
	{
		echo "<select name='customerId'>";
		echo "<option value=''>Select</option>";
		echo "</select>";
	}
?>