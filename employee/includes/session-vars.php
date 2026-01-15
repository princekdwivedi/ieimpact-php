<?php
	$s_employeeId					=	0;
	$s_employeeName					=	"";
	$s_employeeEmail				=	"";
	$s_hasManagerAccess				=	0;
	$s_departmentId					=	0;
	$s_hasPdfAccess					=	0;
	$s_isInBreak					=	0;
	$s_breakId						=	0;
	$s_searchingPdfOrderType		=	0;
	$s_iasHavingAllQaAccess			=	0;
	$s_isHavingVerifyAccess			=	0;
	$s_hasAdminAccess				=	0;
	$s_showQuestionnaire			=	0;
	$a_hardcodeManagers		        =	array("3","587","137","8", "340");
	$a_hardcodeTopManagers		    =	array("3","137","637","340");
	$a_managersTestQuestionAccess	=	array("3","587","137","8","117","946","637");

	if(isset($_SESSION['employeeId']) && !empty($_SESSION['employeeId']) && !isset($_SESSION['hasAdminAccess']) && in_array($_SESSION['employeeId'],$a_hardcodeManagers))
	{
		$_SESSION['hasAdminAccess']	=	1;
	}

	$currrentTimeStamp	           =	strtotime(date('Y-m-d H:i:s'));

	
	if(isset($_SESSION['pageViewedTime']))
	{

		$diff	=  $nowIndiaTimeStamp - $_SESSION['pageViewedTime'];

		$min	=	$diff/60;

		if($min > 510 && $s_employeeId != 351)
		{
			if(isset($_SESSION['employeeId']))
			{
				if(isset($_SESSION['employeeLoginSessionTrackId']))
				{
					
					dbQuery("UPDATE employee_login_track SET loginOutDate='".CURRENT_DATE_INDIA."',loginOutTime='".CURRENT_TIME_INDIA."',loginOutIP='".VISITOR_IP_ADDRESS."' WHERE trackId='".$_SESSION['employeeLoginSessionTrackId']."' AND employeeId='".$_SESSION['employeeId']."'");
					
					unset($_SESSION['employeeLoginSessionTrackId']);
				}
				unset($_SESSION['employeeId']);

				if(isset($_SESSION['employeeName']))
				{
					unset($_SESSION['employeeName']);
				}
				if(isset($_SESSION['employeeEmail']))
				{
					unset($_SESSION['employeeEmail']);
				}
				if(isset($_SESSION['hasManagerAccess']))
				{
					unset($_SESSION['hasManagerAccess']);
				}
				if(isset($_SESSION['departmentId']))
				{
					unset($_SESSION['departmentId']);
				}
				if(isset($_SESSION['hasPdfAccess']))
				{
					unset($_SESSION['hasPdfAccess']);
				}
				if(isset($_SESSION['iasHavingAllQaAccess']))
				{
					unset($_SESSION['iasHavingAllQaAccess']);
				}
				if(isset($_SESSION['isHavingVerifyAccess']))
				{
					unset($_SESSION['isHavingVerifyAccess']);
				}
				if(isset($_SESSION['hasAdminAccess']))
				{
					unset($_SESSION['hasAdminAccess']);
				}
				if(isset($_SESSION['showQuestionnaire']))
				{
					unset($_SESSION['showQuestionnaire']);
				}
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	$_SESSION['pageViewedTime'] = $nowIndiaTimeStamp;

	if(isset($_SESSION['employeeId']))
	{
		$s_employeeId	=	$_SESSION['employeeId'];
	}
	if(isset($_SESSION['employeeName']))
	{
		$s_employeeName	=	$_SESSION['employeeName'];
	}
	if(isset($_SESSION['employeeEmail']))
	{
		$s_employeeEmail=	$_SESSION['employeeEmail'];
	}
	if(isset($_SESSION['hasManagerAccess']))
	{
		$s_hasManagerAccess	=	$_SESSION['hasManagerAccess'];
	}
	if(isset($_SESSION['hasPdfAccess']))
	{
		$s_hasPdfAccess	=	$_SESSION['hasPdfAccess'];
		$_SESSION['departmentId']	=	0;
	}
	if(isset($_SESSION['departmentId']))
	{
		$s_departmentId	=	$_SESSION['departmentId'];
	}
	
	
	if(isset($_SESSION['searchingPdfOrderType']))
	{
		$s_searchingPdfOrderType	=	$_SESSION['searchingPdfOrderType'];
	}

	if(isset($_SESSION['iasHavingAllQaAccess']))
	{
		$s_iasHavingAllQaAccess		=	$_SESSION['iasHavingAllQaAccess'];
	}
	if(isset($_SESSION['isHavingVerifyAccess']))
	{
		$s_isHavingVerifyAccess		=	$_SESSION['isHavingVerifyAccess'];
	}
	if(isset($_SESSION['hasAdminAccess']))
	{
		$s_hasAdminAccess			=	$_SESSION['hasAdminAccess'];
	}	

	if(isset($_SESSION['showQuestionnaire']))
	{
		$s_showQuestionnaire		=	$_SESSION['showQuestionnaire'];
	}
/*	if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
		pr($_SESSION);
	}*/
?>
