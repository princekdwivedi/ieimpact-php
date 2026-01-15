<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$revertLink				=	"";

	if(isset($_REQUEST['recNo']))
	{
		$recNo				=	(int)$_REQUEST['recNo'];
		$revertLink			=	"&recNo=".$recNo;
	}
	if(empty($recNo))
	{
		$recNo				=	0;
	}
	
	$showHideAllOption		=	true;
	$searchText				=	"";
	$whereClause			=	"WHERE isActive=1 AND hasPdfAccess=1";
	$a_orderBy 				=	array("Newest Employee" => "employeeId DESC", "Oldest Employee" => "employeeId ASC", "Name Ascending" => "firstName ASC", "Name Descending" => "firstName DESC", "All Manager" => "isManager DESC", "Test Questionnaire" => "showQuestionnaire DESC", "Locked Accounts" => "isLocked DESC");

	$queryString			=	"";
	$andClause				=	"";
	$search					=	"";
	
	$orderBy				=	"employeeId DESC";
	$displayOrder			=	"employeeId DESC";
	if(isset($_GET['displayOrder'])){
		$displayOrder 		=	trim($_GET['displayOrder']);
		if(!empty($displayOrder)){
			$orderBy 	    =	$displayOrder;
			$queryString   .=   "&displayOrder=".$displayOrder; 
		}
		
	}


	

	$employeeImagePath		=	SITE_ROOT."/files/employee-images/";
	$employeeImageUrl		=	SITE_URL."/files/employee-images/";

	$alphabetLinkPage		=	SITE_URL_EMPLOYEES."/view-employee-details.php";
	$alphaLinkQueryString	=	"";
	if(isset($_GET['searchText']))
	{
		$searchText			=	trim($_GET['searchText']);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-employee-details.php?search=$searchText");
		exit();
	}
	if(isset($_GET['search']))
	{
		$search	=	$_GET['search'];
		if(!empty($search))
		{
			$andClause		=	" AND fullName LIKE '%$search%'";
				
			$queryString   .=	"&search=".$search;
			$revertLink	   .=	"&search=".$search;
			$searchText		=	$search;
		}
	}
	if(isset($_GET['alpha']) && $_GET['alpha'] != "")
	{
		$alpha				=	$_GET['alpha'];
		$andClause		   .=	" AND fullName LIKE '$alpha%'";
		$queryString		=	"&alpha=$alpha";
		$revertLink	       .=	"&alpha=$alpha";
	}
	else
	{
		$alpha				=	"";	
	}

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	(int)$_GET['employeeId'];
		if(!empty($employeeId))
		{
			if($result			=   $employeeObj->getActiveDeactiveEmployeeDetails($employeeId))
			{
				$row			=	mysqli_fetch_assoc($result);
				$email			=	$row['email'];
				$employeeName	=	stripslashes($row['fullName']);

				/////////////////////////// MARK EMPLOYEE AS MANAGER ///////////////////
				if(isset($_GET['lockUnlock']))
				{
					$lockUnlock	 =	(int)$_GET['lockUnlock'];
					if(!empty($lockUnlock)){
						if($lockUnlock		==	1){
							dbQuery("UPDATE employee_details SET isLocked=1,triedFailCount=6,lockedDate='".CURRENT_DATE_INDIA."',lockedTime='".CURRENT_TIME_INDIA."',lockedFromIP='".VISITOR_IP_ADDRESS."',countFailLogin=1 WHERE employeeId=$employeeId AND isLocked=0");
						}
						elseif($lockUnlock	==	2){
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
						}
					}
					
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
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-details.php".$link."#".$employeeId);
					exit();
				}

				////////////////////////// SHOW HIDE Questionnaire ////////////////////////
				if(isset($_GET['showHideQuestionnaire']))
				{
					$showHideQuestionnaire	 =	$_GET['showHideQuestionnaire'];
					
					if($showHideQuestionnaire==	1){
						dbQuery("UPDATE employee_details SET showQuestionnaire=1 WHERE employeeId=$employeeId AND showQuestionnaire=0");
					}
					else{
						dbQuery("UPDATE employee_details SET showQuestionnaire=0 WHERE employeeId=$employeeId AND showQuestionnaire=1");
					}
					
					
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
					header("Location:".SITE_URL_EMPLOYEES."/view-employee-details.php".$link."#".$employeeId);
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
		$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-employee.php", {width: 265,selectFirst: false});
	});
	function search()
	{
		form1	=	document.searchForm;
		if(form1.searchText.value == "")
		{
			alert("Please Enter Name !!");
			form1.searchText.focus();
			return false;
		}
	}
	function downloadProfilePhoto(id)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/download-profile-photo.php?ID='+id;
	}
	function openPrintExcelWindow(pageUrl,extra)
	{
		path = pageUrl+extra;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}

	function lockUnloakAccount(employeeId,lockUnlock,revertLink)
	{
		if(lockUnlock == 1)
		{
			var confirmation = window.confirm("Are you sure to lock this employee's account for next 60 minutes?");
		}
		else
		{
			var confirmation = window.confirm("Are you sure to unlock this employee?");
		}
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php?employeeId='+employeeId+'&lockUnlock='+lockUnlock+revertLink;
		}
	}

	function addRemoveQuestionnaire(employeeId,showHide,revertLink)
	{
		if(showHide == 1)
		{
			var confirmation = window.confirm("Are you sure to show Questionnaires for the employee?");
		}
		else
		{
			var confirmation = window.confirm("Are you sure to remove Questionnaires for the employee?");
		}
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php?employeeId='+employeeId+'&showHideQuestionnaire='+showHide+revertLink;
		}
	}

	function redirectPage1(displayOrder, alpha, revertLink){
	
        window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php?displayOrder='+displayOrder+alpha+revertLink;
    }
</script>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="5" cellspacing="5" width='98%'align="center" border='0'>
		<tr>
			<td width="27%" class="textstyle3"><b>VIEW ALL ACTIVE EMPLOYEE'S LIST</b></td>
			<td colspan="2" class="textstyle3"><b>SHORT BY : </b>
				<select name="displayOrder"  onChange="redirectPage1(this.value, '', '<?php echo $revertLink;?>')"; class="form_text" style="height:25px;">
					<?php
						foreach($a_orderBy as $kk=>$vv){
							$select 	=	"";
							if($vv      == $displayOrder){
								$select =	"selected";
							}

							echo "<option value='$vv' $select>$kk</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="textstyle3"><b>SEARCH AN EMPLOYEE : </b></td>
			<td width="20%">
				<input type='text' name="searchText" size="40" value="<?php echo $searchText;?>" id="searchName" class="form_text" style="width:260px;">
			</td>			
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
		<!--<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<tr>
			<td colspan="4">
				<?php
					$printUrl	=	SITE_URL_EMPLOYEES."/excel-employee-details.php";
				?>
				<a onclick="openPrintExcelWindow('<?php echo $printUrl?>','')" class='link_style10' style="cursor:pointer;" title="PRINT ALL EMPLOYEE DETAILS">VIEW ALL EMPLOYEES IN EXCEL SHEET</a>
				
			</td>
		</tr>-->
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
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-employee-details.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
	<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr height='25' bgcolor="#373737">
			<td width='3%' class='smalltext12'>&nbsp;ID</td>
			<td width='14%' class='smalltext12' style="text-align:center;">Image</td>
			<td width='15%' class='smalltext12'>Name</td>
			<td width='22%' class='smalltext12'>Email</td>
			<td width='10%' class='smalltext12'>Mobile</td>
			<td width='15%' class='smalltext12'>City/State</td>
			<td class='smalltext12'></td>
		</tr>
	<?php
		$i							=	$recNo;
		while($row					=   mysqli_fetch_assoc($recordSet))
		{
			$i++;

			$employeeId				=	$row['employeeId'];
			$employeeName			=	stripslashes($row['fullName']);
			$address				=	stripslashes($row['address']);
			$email					=	$row['email'];
			$facebookEmailId     	=	$row['facebookEmailId'];
			$mobile					=	$row['mobile'];
			$employeeName			=	ucwords(stripslashes($employeeName));
			$hasProfilePhoto 		=	$row['hasProfilePhoto'];
			$profilePhotoExt		=	stripslashes($row['profilePhotoExt']);
			$gender					=	$row['gender'];
			$hasAllQaAccess			=	$row['hasAllQaAccess'];
			$hasPdfAccess			=	$row['hasPdfAccess'];
			$hasPdfAccess			=	$row['hasPdfAccess'];
			$isLocked				=	$row['isLocked'];
			$showQuestionnaire		=	$row['showQuestionnaire'];
			$city					=	stripslashes($row['city']);
			$state					=	stripslashes($row['state']);
			$genderImg				=	"male_icon.png";

			$baseEmployeeId			=	base64_encode($employeeId);
			$md5EmployeeId			=	md5($employeeId);

			if($gender				==	"F")
			{
				$genderImg			=	"female_icon.png";
			}

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}

			$lockUnloaktext			=	"Lock Account";
			$lockUnlock				=	1;
			if($isLocked			==	1){
				$lockUnloaktext		=	"Unlock Account";
				$lockUnlock			=	2;
			}

			$showHideQuestionnaireText	=	"Show Questionnaire";
			$showHideQuestionnaire      =   1;
			if($showQuestionnaire       ==  1){
				$showHideQuestionnaireText	=	"Hide Questionnaire";
			    $showHideQuestionnaire      =   0;
			}

			
	?>
		<tr <?php echo $bgColor;?> height="30">
			<td class="smalltext2" valign="top">
				&nbsp;<?php echo $employeeId.")";?><a name="<?php echo $employeeId;?>"></a>
			</td>
			<td valign="top" align="center">
				<?php
						if(!empty($hasProfilePhoto) && !empty($profilePhotoExt))
						{
							$displayThumbImage	=	$baseEmployeeId."_".$md5EmployeeId.".$profilePhotoExt";
					?>
					<center><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-profile-photos.php?ID=<?php echo $employeeId;?>&ext=<?php echo $profilePhotoExt;?>" border="0" title="<?php echo $employeeName;?>"  height="150" width="150"><br>
					<br>(<a onclick='downloadProfilePhoto(<?php echo $employeeId;?>)' class='link_style6' style="cursor:pointer;" title="Download Profile Photo">Download</a>)</center>
					<?php
						}
						else
						{
							echo "<center><img src='".SITE_URL."/images/".$genderImg."'></center>";
						}
					?>
			</td>
			<td class="smalltext2" valign="top">
				<a onclick="openPrintExcelWindow('<?php echo $printUrl?>','?ID=<?php echo $employeeId;?>')" class='form_links2' style="cursor:pointer;" title=""><?php echo $employeeName;?></a>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo "<a href='mailto:$email' class='form_links2'>$email</a>";
					if(!empty($facebookEmailId)){
						echo "<br /><b>Facebook Email </b>- ".$facebookEmailId;
					}
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $mobile;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $city."/".$state;
				?>
			</td>
			<td valign="top" align="right" class="smalltext1"><a href="<?php echo SITE_URL_EMPLOYEES?>/view-an-employee.php?ID=<?php echo $employeeId;?>" class='form_links2'>Edit</a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/display-an-employee-details.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=500&width=700" title='' class='thickbox'/><font class='form_links2'>View</font></a>&nbsp;|&nbsp;<a onclick="lockUnloakAccount(<?php echo $employeeId;?>,<?php echo $lockUnlock;?>,'<?php echo $revertLink;?>');" style="cursor:pointer;" class='form_links2'><?php echo $lockUnloaktext;?></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/change-employee-password.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=400&width=600" title='' class='thickbox'/><font class='form_links2'>Change Password</font></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/employee-last-ten-login.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=400&width=400" title='' class='thickbox'/><font class='form_links2'>Last 10 Login</font></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES?>/download-employee-files.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=400&width=600" title='' class='thickbox'/><font class='form_links2'>Employee Files Added</font></a>&nbsp;|&nbsp;<a onclick="addRemoveQuestionnaire(<?php echo $employeeId;?>,<?php echo $showHideQuestionnaire;?>,'<?php echo $revertLink;?>');" style="cursor:pointer;" class='form_links2'><?php echo $showHideQuestionnaireText;?></a>
			</td>
		</tr>
	<?php
		}
		echo "</table>";
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Active Employee Available Now </b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>