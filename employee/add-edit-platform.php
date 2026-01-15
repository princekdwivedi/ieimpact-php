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
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$platfromId					=	0;
	$platfromName				=	"";
	$t_platfromName				=	"";
	$departmentId				=	1;
	$departmentText				=	"";
	$text						=	"Add New Platform";

	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-edit-platform.php";
	

	if(isset($_GET['ID']))
	{
		$platfromId		=	(int)$_GET['ID'];
		$query			=	"SELECT * FROM platform_clients WHERE platfromId=$platfromId AND parentId=0";
		$result	=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$platfromName	=	$row['name'];
			$departmentId	=	$row['departmentId'];
			$t_platfromName	=	$platfromName;
			$departmentText	=	$a_department[$departmentId];
			$text			=	"Edit Platform Name";
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$platfromName	=	trim($platfromName);

		$validator ->checkField($platfromName,"","Please Enter Platform Name !!");
		if(!empty($platfromName) && $platfromName != $t_platfromName)
		{
			if($result	=	$employeeObj->getExistingPlatform($platfromName,$departmentId))
			{
				$validator ->setError("This platform is exists for  the department !!");
			}
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			if(empty($platfromId))
			{
				$query	=	"INSERT INTO platform_clients SET name='$platfromName',departmentId=$departmentId,addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."'";
				mysql_query($query);
			}
			else
			{
				$query	=	"UPDATE platform_clients SET name='$platfromName' WHERE departmentId=$departmentId AND platfromId=$platfromId AND parentId=0";
				mysql_query($query);
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/add-edit-platform.php");
			exit();
		}
		else
		{
			echo $errorMsg	 =	$validator ->getErrors();
			include($form);
		}
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>