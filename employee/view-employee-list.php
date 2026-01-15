<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");

	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");

	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT				.	"/includes/send-mail.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	 "/includes/set-variables.php");
	
	$employeeObj					=	new employee();

	$pagingObj						=	new Paging();

	$whereClause					=	"WHERE (hasPdfAccess=1 OR enrollAs='pdf')";
	$orderBy						=	"firstName";
	$queryString					=	"";
	$andClause						=	"";
	$searchBy						=	"1";
	$searchByName					=	"";
	$searchText						=	"";
	$a_searchEmployeeType			=	array("1"=>"ALL","2"=>"ACTIVE","3"=>"INACTIVE","4"=>"MANAGERS","5"=>"NIGHT SHIFT");

	$alphabetLinkPage				=	SITE_URL_EMPLOYEES."/view-employee-list.php";
	$alphaLinkQueryString			=	"";
	
	if(isset($_GET['searchText']))
	{
		$searchText	    =	$_GET['searchText'];
		if(!empty($searchText))
		{
			$andClause	   .=	" AND fullName LIKE '%$searchText%'";
				
			$queryString	.=	"&searchText=".$searchText;
		}
	}
	if(isset($_GET['searchBy']))
	{
		$searchBy			=	$_GET['searchBy'];
		if(!empty($searchBy))
		{
			if($searchBy	==	 2)
			{
				$andClause	.=	" AND isActive=1";
			}
			elseif($searchBy	==	 3)
			{
				$andClause	.=	" AND isActive=0";
			}
			elseif($searchBy	==	 4)
			{
				$andClause	.=	" AND isManager=1";
			}
			elseif($searchBy	==	 5)
			{
				$andClause	.=	" AND isNightShiftEmployee=1";
			}
			$queryString	.=	"&searchBy=".$searchBy;
			
		}
	}
	
	if(isset($_GET['alpha']) && $_GET['alpha'] != "")
	{
		$alpha				=	$_GET['alpha'];
		$andClause		   .=	" AND fullName LIKE '$alpha%'";
		$queryString	   .=	"&alpha=$alpha";
	}
	else
	{
		$alpha				=	"";	
	}
	$alphaLinkQueryString	=   "&searchBy=".$searchBy;

	if(isset($_REQUEST['recNo']))
	{
		$recNo				=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo				=	0;
	}

	$searchGetToken			=	$queryString;
	if(!empty($recNo))
	{
		$searchGetToken		=	"&recNo=".$recNo;
	}

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	(int)$_GET['employeeId'];
		if(!empty($employeeId))
		{
			if($result					=   $employeeObj->getActiveDeactiveEmployeeDetails($employeeId))
			{
				$row					=	mysqli_fetch_assoc($result);
				$email					=	$row['email'];
				$employeeName			=	stripslashes($row['fullName']);
				$securityCode   		=   $row['securityCode'];
				$employeeRegisteredOn 	=	$row['addedOn'];

				/////////////////////////// DEACTIVATE AN EMPLOYEE ///////////////////
				if(isset($_GET['deactivateEmp']) && $_GET['deactivateEmp'] == 1)
				{
					$exactOriginalEmail			=	$email;
					$email						=	$email.".OLD";
					$deactivatedDate			=	CURRENT_DATE_INDIA;
					
					/****************************** SENDING FEEDBACK EMAIL ************************/
					include(SITE_ROOT			.   "/classes/email-templates.php");
					$emailObj					=	new emails();

					$managerEmployeeEmailSubject=	"Please explain briefly about your experience with ieIMPACT ";
					
					
					$body						=	"Dear ".$employeeName.",<br />Explain your experience with ieIMPACT. Please <a href='".SITE_URL_EMPLOYEES."/exit-employee-feedback.php' target='_blank'>click here</a> to share your experince.<br /><br />";

					
					$a_templateData				=	array("{bodyMatter}"=>$body);
					$managerEmployeeFromName	=	"ieIMPACT";			

					$uniqueTemplateName			=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
					$toEmail					=	$exactOriginalEmail;
					//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					/*******************************************************************************/

					dbQuery("UPDATE employee_details SET isActive=0,email='$email',exactOriginalEmail='$exactOriginalEmail',deactivatedBy='$s_employeeId',deactivatedDate='".CURRENT_DATE_INDIA."' WHERE employeeId=$employeeId");

					dbQuery("INSERT INTO employee_activate_deactivate_tracking SET employeeId=$employeeId,hadPdfAccess=1,exactRegisteredOn='$employeeRegisteredOn',activeDeactivedOn='".CURRENT_DATE_INDIA."',performOpration='deactive',performBy=$s_employeeId");					

					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
		
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
				    exit();
				}

				/////////////////////////// DEACTIVATE AN EMPLOYEE ///////////////////
				if(isset($_GET['activateEmp']) && $_GET['activateEmp'] == 1)
				{
					$exactOriginalEmail			=	stripslashes($row['exactOriginalEmail']);
					
					if(!empty($exactOriginalEmail))
					{
						dbQuery("UPDATE employee_details SET email='$exactOriginalEmail',exactOriginalEmail='',isActive=1,hasPdfAccess=1,lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."',deactivatedDate='' WHERE employeeId=$employeeId");
					}
					else
					{
						dbQuery("UPDATE employee_details SET isActive=1,hasOutsideLoginAccess=0,receivePdfEmails=1,hasPdfAccess=1,lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."',deactivatedDate='' WHERE employeeId=$employeeId");

						$employeeName 		= makeDBSafe($employeeName);

						list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);
						$checkTargetCurrentMonth		    =	$currentM;
						if($checkTargetCurrentMonth < 10 && strlen($checkTargetCurrentMonth) > 1)
						{
							$checkTargetCurrentMonth        =	substr($checkTargetCurrentMonth,1);
						}						

						dbQuery("INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$employeeName',processedTarget=10,qaTarget=10,targetMonth=$checkTargetCurrentMonth,targetYear=$currentY,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=0");

						$from			=	"hr@ieimpact.com";
						$fromName		=	"HR ieIMPACT ";
		

						$templateId		=	ADMINISTRATOR_SENDING_ACTIVATE_EMPLOYEES;
						$mailSubject	=	"Activated your employee account in ieIMPACT";

						$a_templateData	=	array("{employeeName}"=>$employeeName,"{employeeEmail}"=>$email);

						sendTemplateMail($from, $fromName, $email, $mailSubject, $templateId, $a_templateData);
					}

					dbQuery("INSERT INTO employee_activate_deactivate_tracking SET employeeId=$employeeId,hadPdfAccess=1,exactRegisteredOn='$employeeRegisteredOn',activeDeactivedOn='".CURRENT_DATE_INDIA."',performOpration='active',performBy=$s_employeeId");

					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				/////////////////////////// MARK EMPLOYEE AS MANAGER ///////////////////
				if(isset($_GET['activateManager']) && $_GET['activateManager'] == 1)
				{
					dbQuery("UPDATE employee_details SET isManager=1 WHERE employeeId=$employeeId");

					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				/////////////////////////// UNMARK EMPLOYEE AS MANAGER ///////////////////
				if(isset($_GET['deactivateManager']) && $_GET['deactivateManager'] == 1)
				{
					dbQuery("UPDATE employee_details SET isManager=0 WHERE employeeId=$employeeId");
					
					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				/////////////////////////// CHANGE EMPLOYEE SHIFT ///////////////////
				if(isset($_GET['changeShift']) && ($_GET['changeShift'] == 1 || $_GET['changeShift'] == 0))
				{
					$changeShift 	=	$_GET['changeShift'];
					dbQuery("UPDATE employee_details SET isNightShiftEmployee=$changeShift WHERE employeeId=$employeeId");
					
					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				/////////////////////////// ALLOW REMOVE OUTSIDE LOGIN ///////////////////
				if(isset($_GET['setoutsidelogin']))
				{
					$hasOutsideLoginAccess	=	$_GET['setoutsidelogin'];
					//dbQuery("UPDATE employee_details SET hasOutsideLoginAccess=$hasOutsideLoginAccess WHERE employeeId=$employeeId");

					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				///////////////// SENDING EMPLOYEE SECURITY CODE IN HR EMAIL////////////////
				if(isset($_GET['sendSecurityToken']) && $_GET['sendSecurityToken'] == 1)
				{
					$emailMessage = "The new security token of ".$employeeName."'s (PDF Employee) is - <b>".$securityCode."</>";

					$from			=	"hr@ieimpact.com";
					$fromName		=	"HR ieIMPACT";
					$mailSubject	=	$subject;
					$templateId		=	ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
					$mailSubject    =   $employeeName." new security token.";

					$a_templateData	=	array("{employeeName}"=>"HR","{message}"=>$emailMessage);

					sendTemplateMail($from, $fromName, "hr@ieimpact.com", $mailSubject, $templateId, $a_templateData);

					$link		 =	"";

					if(!empty($queryString))
					{
						$link	 =	$queryString;
					}
					if(!empty($recNo))
					{
						$link	.=	"&recNo=".$recNo;
					}
					if(!empty($link))
					{
						$link	=	"?".$link;
					}
	
					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();

				}

				///////////////// REMOVE EXISTING FACEBOOK ACCOUNTS DETAILS////////////////
				if(isset($_GET['removeFacebook']) && $_GET['removeFacebook'] == 1)
				{
					dbQuery("UPDATE employee_details SET facebookId='',facebookEmailId='' WHERE employeeId=$employeeId");

					ob_clean();
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-list.php".$link."#".$employeeId);
					exit();
				}

				
			}
		}
	}		
	
	

	
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type='text/javascript'>
	$().ready(function() {
		$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-employee.php", {width: 265,selectFirst: false});
	});
		
	function openPrintExcelWindow(pageUrl,extra)
	{
		path = pageUrl+extra;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}

	function activeDeactiveEmployee(employeeId,type,searchtext)
	{
		if(type == 1)
		{
			var confirmation = window.confirm("Are you sure to deactivate this employee?");
			if(confirmation == true)
			{
				window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&deactivateEmp=1'+searchtext;
			}
		}
		else
		{
			var confirmation = window.confirm("Are you sure to activate this employee?");
			if(confirmation == true)
			{
				window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&activateEmp=1'+searchtext;
			}
		}
	}

	function chnageOutsideLogin(employeeId,type,searchtext){
		if(type == 1)
		{
			var confirmation = window.confirm("Are you sure to give outside login for the employee?");
		}
		else
		{
			var confirmation = window.confirm("Are you sure to remove outside login for the employee?");
		}
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&setoutsidelogin='+type+searchtext;
		}
	}

	function activeDeactiveManager(employeeId,type,searchtext)
	{
		if(type == 1)
		{
			var confirmation = window.confirm("Are you sure to mark this employee as manager?");
			if(confirmation == true)
			{
				window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&activateManager=1'+searchtext;
			}
		}
		else
		{
			var confirmation = window.confirm("Are you sure to remove this employee as manager?");
			if(confirmation == true)
			{
				window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&deactivateManager=1'+searchtext;
			}
		}
	}

	function sendEmployeeSecurityToken(employeeId,searchtext)
	{
		var confirmation = window.confirm("Are you sure to send this employee's security token in HR email?");
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&sendSecurityToken=1'+searchtext;
		}
	}	

	function removeFacebookDetails(employeeId,searchtext)
	{
		var confirmation = window.confirm("Are you sure to remove existing Facebbok details so that employee can use another Facebbok account to login?");
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&removeFacebook=1'+searchtext;
		}
	}	

	function changeEmployeeShift(employeeId,changeShift,changeTo,searchtext)
	{
		var confirmation = window.confirm("Are you sure to mark as "+changeTo+"?");
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php?employeeId='+employeeId+'&changeShift='+changeShift+searchtext;
		}
	}	


	
</script>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="55%" class="smalltext23"><b>VIEW EMPLOYEE'S : </b>
				<?php
					foreach($a_searchEmployeeType as $k=>$v)
					{
						$checked	=	"";
						if($searchBy==	$k){
							$checked=	"checked";
						}

						echo "<input type='radio' name='searchBy' value='$k' onclick='document.searchForm.submit();' $checked>$v&nbsp;";
					}
				?></b>
			</td>
			<td width="19%" class="textstyle3"><b>OR SEARCH AN EMPLOYEE</b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td width="22%">
				<input type='text' name="searchText" size="40" value="<?php echo $searchText;?>" id="searchName" class="form_text" style="width:260px;">
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<tr>
			<td colspan="4">
				<?php
					$printUrl	=	SITE_URL_EMPLOYEES."/excel-employee-details.php";
					$printUrl1	=	SITE_URL_EMPLOYEES."/excel-employee-details.php?allInactive=1";
				?>
				<a onclick="openPrintExcelWindow('<?php echo $printUrl?>','')" class='link_style10' style="cursor:pointer;" title="PRINT ALL EMPLOYEE DETAILS">VIEW ALL ACTIVE EMPLOYEES IN EXCEL SHEET</a>&nbsp;|&nbsp;<a onclick="openPrintExcelWindow('<?php echo $printUrl1?>','')" class='link_style10' style="cursor:pointer;" title="PRINT ALL EMPLOYEE DETAILS">VIEW ALL INACTIVE-EMPLOYEES IN EXCEL SHEET</a>
			</td>
		</tr>
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<tr>
			<td colspan="5">
				<?php
					include(SITE_ROOT_EMPLOYEES . "/includes/alphabets.php");
				?>
			</td>
		</tr>
	</table>
</form>
<link href="<?php echo SITE_URL;?>/css/thickbox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_URL;?>/script/thickbox-big.js" type="text/javascript"></script>
<?php
	$start					  =	0;
	$recsPerPage	          =	20;//how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_details";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-employee-list.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
	<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr height='25' bgcolor="#373737">
			<td width='3%' class='smalltext12'>ID</td>
			<td width='20%' class='smalltext12'>Name</td>
			<td width='20%' class='smalltext12'>Email</td>
			<td width='5%' class='smalltext12'>Status</td>
			<td width='7%' class='smalltext12'>Reg. On</td>
			<td width='10%' class='smalltext12'>Referred By</td>
			<td width='7%' class='smalltext12'>City</td>
			<td width='9%' class='smalltext12'>Phone</td>
			<td class='smalltext12'>Action</td>
		</tr>
	<?php
		$i							=	$recNo;
		while($row					=   mysqli_fetch_assoc($recordSet))
		{
			$i++;

			$employeeId				=	$row['employeeId'];
			$employeeName			=	ucwords(stripslashes($row['fullName']));
			$city					=	$row['city'];
			$email					=	$row['email'];
			$mobile					=	$row['mobile'];
			$password				=	$row['password'];
			$hasPdfAccess			=	$row['hasPdfAccess'];
			$isActive				=	$row['isActive'];
			$referredBy				=	$row['referredBy'];
			$referredBy				=	$row['referredBy'];
			$addedOn				=	showDate($row['addedOn']);
			$isManager				=	$row['isManager'];
			$hasOutsideLoginAccess	=	$row['hasOutsideLoginAccess'];
			$isNightShiftEmployee   =	$row['isNightShiftEmployee'];
			$facebookId             =	$row['facebookId'];
			$facebookEmailId        =   $row['facebookEmailId'];

			$shiftText				=	"&nbsp;(Day Shift)";
			$changeShiftText		=	"Night Shift";
			$changeShiftType		=	1;
			if($isNightShiftEmployee==  1){
				$shiftText			=	"&nbsp;(Night Shift)";
				$changeShiftText	=	"Day Shift";
				$changeShiftType	=	0;
			}


			$outsideAccessDisplay		=	"<font color='#ff0000;'>No</font>&nbsp;(<a onClick=\"chnageOutsideLogin($employeeId,1,'$searchGetToken');\" class='form_links2' style='cursor:pointer;'>Change</a>)";
			if($hasOutsideLoginAccess	==	1){
				$outsideAccessDisplay	=	"<font color='green;'>Yes</font>&nbsp;(<a onclick=\"chnageOutsideLogin($employeeId,0,'$searchGetToken');\" class='form_links2' style='cursor:pointer;'>Change</a>)";
			}
			
			if(empty($referredBy))
			{
				$referredBy			=	"<font color='#ff0000;'>None</font>";
			}
			
			$phoneText				=	$mobile;	
			
			$statusText				=	"<font color='#ff0000;'>In-Active</font>";
			$activeInactiveText		=	"Activate";
			$activationType			=	2;
			if($isActive			==	1)
			{
				$statusText			=	"<font color='#00F078;'>Active</font>";
				$activeInactiveText	=	"De-Activate";
				$activationType		=	1;
			}

			$managersText			=	"";
			$activeInactiveMngr		=	"Mark manager";
			$activationTypeMngr		=	1;
			if($isManager			==	1)
			{
				$managersText		=	"&nbsp;<font color='#ff0000;'>(Mangr.)</font>";
				$activeInactiveMngr	=	"Rmv manager";
				$activationTypeMngr	=	2;
			}

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}

			
	?>
		<tr <?php echo $bgColor;?> height="30">
			<td class="smalltext1" valign="top" style="text-align:right">
				<a name="<?php echo $employeeId;?>"></a>&nbsp;<?php echo $employeeId;?>)&nbsp;
			</td>
			<td class="smalltext2" valign="top">
				<a onclick="openPrintExcelWindow('<?php echo $printUrl?>','?ID=<?php echo $employeeId;?>')" class='form_links2' style="cursor:pointer;" title=""><?php echo $employeeName.$managersText.$shiftText;?></a>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo "<a href='mailto:$email' class='form_links2' title='$email'>".$email."</a>";
					if(!empty($facebookEmailId) && $isActive == 1){
						echo "<br /><b>Facebook Email </b>- ".$facebookEmailId;
					}
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $statusText;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $addedOn;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $referredBy;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $city;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $phoneText;
				?>
			</td>
			<td valign="top" align="right" class="smalltext1">
				<?php
					if($isActive == 1)	{
				?>
				<a href="<?php echo SITE_URL_EMPLOYEES?>/view-an-employee.php?ID=<?php echo $employeeId;?>" class='form_links2'>Edit</a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/display-an-employee-details.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=500&width=700" title='' class='thickbox'/><font class='form_links2'>View</font></a>&nbsp;|&nbsp;<a onclick="activeDeactiveEmployee(<?php echo $employeeId;?>,<?php echo $activationType;?>,'<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;"><?php echo $activeInactiveText;?></a>&nbsp;|&nbsp;<a onclick="activeDeactiveManager(<?php echo $employeeId;?>,<?php echo $activationTypeMngr;?>,'<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;"><?php echo $activeInactiveMngr;?></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/add-employee-shift-timing.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=200&width=700" title='' class='thickbox'/><font class='form_links2'>Shift</font></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/add-employee-outside-login.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=300&width=700" title='' class='thickbox'/><font class='form_links2'>Outside Login</font></a>&nbsp;|&nbsp;<a onclick="sendEmployeeSecurityToken(<?php echo $employeeId;?>,'<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;">Security Token</a>&nbsp;|&nbsp;<a onclick="changeEmployeeShift(<?php echo $employeeId;?>,<?php echo $changeShiftType;?>,'<?php echo $changeShiftText;?>', '<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;"><?php echo $changeShiftText;?></a>
				<?php
						if(!empty($facebookId) && !empty($facebookEmailId)){				?>
							&nbsp;|&nbsp;<a onclick="removeFacebookDetails(<?php echo $employeeId;?>,'<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;">Rmv Facebook Details</a>
				<?php
						}
					}
					else{
				?>
				<a onclick="activeDeactiveEmployee(<?php echo $employeeId;?>,<?php echo $activationType;?>,'<?php echo $searchGetToken;?>');" class='form_links2' style="cursor:pointer;"><?php echo $activeInactiveText;?></a>
				<?php
					}
				?>
			</td>

		</tr>
	<?php
		}
		echo "</table>";
		echo "<br /><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Active Employee Available Now </b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>