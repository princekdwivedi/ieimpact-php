<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES ."/classes/employee.php");
	$employeeObj =	new employee();
	$parentId	 =	"";
	if(isset($_GET['parentId']))
	{
		$parentId=	$_GET['parentId'];
	}
	if(!empty($parentId) && $parentId > 0)
	{
		echo "<select name='customerId' class='form_text_email'>";
		echo "<option value=''>Select</option>";
		if($result = $employeeObj->getPlatformClients($parentId))
		{
			while($row	=	mysql_fetch_assoc($result))
			{
				$customerId		=	$row['customerId'];
				$customerName	=	$row['name'];
				
				echo "<option value='$customerId'>$customerName</option>";
			}
		}
		echo "</select>";
	}
	else
	{
		echo "<select name='customerId' class='form_text_email'>";
		echo "<option value=''>Select</option>";
		echo "</select>";
	}
?>