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
	$form						=	SITE_ROOT_EMPLOYEES."/forms/assign-work.php";
	$assignedWorkId				=	0;
	$text						=	"Assign A Work To Employee";
	$platform					=	"";
	$customerId					=	0;
	$employeeId					=	0;
	$comments					=	"";
	$direct1					=	"";
	$direct2					=	"";
	$indirect1					=	"";
	$indirect2					=	"";
	$qa1						=	"";
	$qa2						=	"";
	$audit1						=	"";
	$audit2						=	"";
	$totalLinesAssigned			=	0;
	$success					=	"";
	$successText				=	"added";

	$hasUploadedFile			=	0;
	$uploadedFileName			=	"";
	$fileExtension				=	"";
	$isEdit						=	false;

	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);

		return $fileName;
	}

	if(isset($_GET['ID']))
	{
		$assignedWorkId	=	(int)$_GET['ID'];
		$query			=	"SELECT * FROM assign_employee_works WHERE assignedWorkId=$assignedWorkId AND status != 2";
		$result	=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$isEdit			=	true;
			$row			=	mysql_fetch_assoc($result);
			$platform		=	$row['platform'];
			$customerId		=	$row['customerId'];
			$employeeId		=	$row['employeeId'];
			
			$direct1		=	$row['direct1'];
			$direct2		=	$row['direct2'];
			$indirect1		=	$row['indirect1'];
			$indirect2		=	$row['indirect2'];
			$qa1			=	$row['qa1'];
			$qa2			=	$row['qa2'];
			$audit1			=	$row['audit1'];
			$audit2			=	$row['audit2'];
			$comments		=	$row['comments'];
			$hasUploadedFile=	$row['hasUploadedFile'];
			$uploadedFileName=	$row['uploadedFileName'];


			if(empty($direct1))
			{
				$direct1				=	"";
			}
			if(empty($direct2))
			{
				$direct2				=	"";
			}
			if(empty($indirect1))
			{
				$indirect1				=	"";
			}
			if(empty($indirect2))
			{
				$indirect2				=	"";
			}
			if(empty($qa1))
			{
				$qa1					=	"";
			}
			if(empty($qa2))
			{
				$qa2					=	"";
			}
			if(empty($audit1))
			{
				$audit1					=	"";
			}
			if(empty($audit2))
			{
				$audit2					=	"";
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	
	if(isset($_GET['success']))
	{
		$success	=	$_GET['success'];
		if($success	==	2)
		{
			$successText		=	"edited";
		}
	}
	elseif(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$direct1					=	trim($direct1);
		$direct2					=	trim($direct2);
		$indirect1					=	trim($indirect1);
		$indirect2					=	trim($indirect2);
		$qa1						=	trim($qa1);
		$qa2						=	trim($qa2);
		$audit1						=	trim($audit1);
		$audit2						=	trim($audit2);
		$comments					=	trim($comments);
		if(empty($direct1))
		{
			$direct1				=	0;
		}
		if(empty($direct2))
		{
			$direct2				=	0;
		}
		if(empty($indirect1))
		{
			$indirect1				=	0;
		}
		if(empty($indirect2))
		{
			$indirect2				=	0;
		}
		if(empty($qa1))
		{
			$qa1					=	0;
		}
		if(empty($qa2))
		{
			$qa2					=	0;
		}
		if(empty($audit1))
		{
			$audit1					=	0;
		}
		if(empty($audit2))
		{
			$audit2					=	0;
		}
		$validator ->checkField($platform,"","Please Select A Platform !!");
		$validator ->checkField($customerId,"","Please Select A Client !!");
		if(empty($assignedWorkId))
		{
			if(empty($_FILES['assignFile']['name']))
			{
				$validator ->setError("Please upload file name !!");
			}
		}
		if(!empty($_FILES['assignFile']['name']))
		{
			$uploadedFileName   =   $_FILES['assignFile']['name'];
			$fileExtension	    =	findexts($uploadedFileName);
			if($fileExtension != "xlsx")
			{
				$validator ->setError("Please Only Upload .xlsx file !!");
			}
		}

		$validator ->checkField($comments,"","Please Enter Comments !!");
		if(empty($direct1) && empty($direct2) && empty($indirect1) && empty($indirect2) && empty($qa1) && empty($qa2) && empty($audit1) && empty($audit2))
		{
			$validator ->setError("Please Enter Lines !!");
		}
		$validator ->checkField($platform,"","Please Select A Platform !!");
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			$totalLinesAssigned	=	$direct1+$direct2+$indirect1+$indirect2+$qa1+$qa2+$audit1+$audit2;
			$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,direct1=$direct1,direct2=$direct2,indirect1=$indirect1,indirect2=$indirect2,qa1=$qa1,qa2=$qa2,audit1=$audit1,audit2=$audit2,totalLinesAssigned=$totalLinesAssigned,comments='$comments'";
			if(empty($assignedWorkId))
			{
				$query	=	"INSERT INTO assign_employee_works".$optionQuery.",assignedOn='".CURRENT_DATE_INDIA."',assignedBy=$s_employeeId";
				mysql_query($query);
				$assignedWorkId	=	mysql_insert_id();
			}
			else
			{
				$query	=	"UPDATE assign_employee_works".$optionQuery." WHERE assignedWorkId=$assignedWorkId AND status != 2";
				mysql_query($query);
			}
			
			if(!empty($_FILES['assignFile']['name']))
			{
				$uploadedFileName   =   $_FILES['assignFile']['name'];
				$fileExtension	    =	findexts($uploadedFileName);

				mysql_query("UPDATE assign_employee_works SET hasUploadedFile=1,uploadedFileName='$uploadedFileName',fileExtension='$fileExtension' WHERE assignedWorkId=$assignedWorkId");
			}


			$link	=	"";
			if($isEdit)
			{
				$link	=	"?employeeId=$employeeId";
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/manage-assign-work.php".$link);
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