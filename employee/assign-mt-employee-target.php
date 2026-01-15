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
	$andClause					=	"";
	$link						=	"";

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
		$link					=	"?recNo=".$recNo;
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}
		
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);


	$a_employeeDetails			=	array();
	$a_employeeTargets			=	array();
	$a_employeeAchieved			=	array();
	$isAlreadyHaveRecords		=	0;
	$showingMonth				=	$currentM;
	$showingYear				=	$currentY;
	
	$whereClause				=	"WHERE departmentId=1 AND employee_details.isActive=1";
	$andClause					=	"";
	$queryString				=	"";
	$orderBy					=	"firstName";

	if(isset($_GET['showingMonth']))
	{
		$showingMonth			=	$_GET['showingMonth'];
	}
	if(isset($_GET['showingYear']))
	{
		$showingYear			=	$_GET['showingYear'];
	}

	$nonLeadingZeroMonth		=	$showingMonth;
	if($showingMonth < 10 && strlen($showingMonth) > 1)
	{
		$nonLeadingZeroMonth	=	substr($showingMonth,1);
	}
	$showingMonthText			=	$a_month[$showingMonth];
	$queryString				=	"&showingMonth=".$showingMonth."&showingYear=".$showingYear;
	$alphaLinkQueryString		=	"&showingMonth=".$showingMonth."&showingYear=".$showingYear;
	$alpha						=	"";

	if(isset($_GET['alpha']))
	{
		$alpha					=	$_GET['alpha'];
		if(!empty($_GET['alpha']))
		{
			$andClause			=	" AND employee_details.fullName LIKE '$alpha%'";
			$queryString	   .=   "&alpha=".$alpha;
		}
	}


	$alphabetLinkPage			=	SITE_URL_EMPLOYEES."/assign-mt-employee-target.php";


	if(isset($_GET['showingMonth']))
	{
		$showingMonth			=	$_GET['showingMonth'];
	}
	if(isset($_GET['showingYear']))
	{
		$showingYear			=	$_GET['showingYear'];
	}

	$nonLeadingZeroMonth		=	$showingMonth;
	if($showingMonth < 10 && strlen($showingMonth) > 1)
	{
		$nonLeadingZeroMonth	=	substr($showingMonth,1);
	}

	
	$query						=	"SELECT * FROM mt_employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$showingYear ORDER BY employeeName";
	$result						=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		while($row				=	mysql_fetch_assoc($result))
		{
			$employeeId			=	$row['employeeId'];
			$linesTarget		=	$row['processedTarget'];
			$targetAchieved		=	$row['targetAchieved'];

			$a_employeeTargets[$employeeId] =	$linesTarget;
			$a_employeeAchieved[$employeeId]=	$targetAchieved;
		}
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		
		$a_target	=	$_POST['target'];
		
		foreach($a_target as $employeeId=>$target)
		{
			$employeeName		=	$employeeObj->getEmployeeName($employeeId);

			if(empty($target))
			{
				$target			=	0;
			}

			$isExistsRecords	=	@mysql_result(dbQuery("SELECT employeeid FROM mt_employee_target WHERE employeeId=$employeeId AND targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY"),0);

			if(empty($isExistsRecords))
			{
				$query			=	"INSERT INTO mt_employee_target SET employeeId=$employeeId,employeeName='$employeeName',processedTarget=$target,targetMonth=$nonLeadingZeroMonth,targetYear=$showingYear,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,addedFromIP='".VISITOR_IP_ADDRESS."'";
				dbQuery($query);
			}
			else
			{
				$query			=	"UPDATE mt_employee_target SET processedTarget=$target,updatedOn='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."',updatedBy=$s_employeeId,updatedFromIP='".VISITOR_IP_ADDRESS."' WHERE employeeId=$employeeId AND targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY";
				dbQuery($query);
			}
			
		}

		$redLink		=	$link.$queryString;
		if(empty($link))
		{
			$redLink	=	"?".substr($queryString,1);
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/assign-mt-employee-target.php".$redLink);
		exit();
	}
	
?>
<form name="changeCheckTarget" action="" method="GET">
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td class="smalltext24" colspan="5">
				<b>ASSIGN MT EMPLOYEES THEIR TARGET FOR <?php echo $showingMonthText.",".$showingYear;?></b> 
			</td>
		</tr>
		<tr>
			<td class="smalltext24" width="10%">
				Change It To : 
			</td>
			<td class="smalltext24" width="6%">
				<select name="showingMonth"> 
					<?php
						foreach($a_month as $k=>$v)
						{
							$select		=	"";
							if($k		==	$showingMonth)
							{
								$select	=	"selected";
							}
							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<td class="smalltext24" width="5%">
				<select name="showingYear"> 
					<?php
						$start			=	"2014";
						$end			=	date('Y');
						for($i=$start;$i<=$end;$i++)
						{
							$select		=	"";
							if($i		==	$showingYear)
							{
								$select	=	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td class="smalltext24">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<?php	
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  = $recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_shift_rates INNER JOIN employee_details ON employee_shift_rates.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_shift_rates.employeeId,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/assign-mt-employee-target.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<script type="text/javascript">
	 function checkForNumber()
	 {
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k >= 48 && k <= 57 )
		{
			return true;
		}
		else
		{
			return false;
		}
	 }
</script>
<br />
<form name="addEditTraget" action="" method="POST">
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td width="2%">&nbsp;</td>
			<td width="25%" class="smalltext24"><b>Employee Name</b></td>
			<td width="15%" class="smalltext24"><b>Target</b></td>
			<td class="smalltext24"><b>Target Achieved</b></td>
		</tr>	
		<tr>
			<td colspan="4">
				<?php
					include(SITE_ROOT_EMPLOYEES	. "/includes/alphabets.php");	
				?>
			</td>
		</tr>
		<?php
			$count						=	$recNo;
			while($row					=   mysql_fetch_assoc($recordSet))
			{
				$count++;	
				$employeeId				=	$row['employeeId'];
				$fullName				=	stripslashes($row['fullName']);

				$target					=	"";
				if(array_key_exists($employeeId,$a_employeeTargets))
				{
					$target				=	$a_employeeTargets[$employeeId];
				}
				$achived				=	"";
				if(array_key_exists($employeeId,$a_employeeAchieved))
				{
					$achived			=	$a_employeeAchieved[$employeeId];
				}
				if(empty($target))
				{
					$target				=	"";
				}
		?>
		<tr>
			<td class="smalltext2" valign="top"><?php echo $count;?>)</td>
			<td class="smalltext23" valign="top"><?php echo $fullName;?></td>
			<td class="smalltext23" valign="top">
				<input type="text" name="target[<?php echo $employeeId;?>]" size="10" value="<?php echo $target;?>" onKeyPress="return checkForNumber();" maxlength="8" style="border:1px solid #000000;color:#4d4d4d;font-family:verdana;font-size:14px;height:20px;">
			</td>
			<td class="smalltext23" valign="top">
				<?php
					if(!empty($achived))
					{
						$percentageAchived		=  "";
						if($achived			   >=  $target)
						{						
							$percentageAchived   =  "(100%)";
						}
						else
						{
							$percentageAchived	=	$achived/$target;
							$percentageAchived	=	$percentageAchived*100;
							$percentageAchived	=	"(".round($percentageAchived,2)."%)";
								
						}
						echo $achived."&nbsp;".$percentageAchived;
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<?php
					if($currentM	==	$showingMonth && $currentY   ==  $showingYear)
					{
	
				?>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
				<?php
					}	
				?>
			</td>
		</tr>
		<?php
			echo "<tr><td height='7'></td></tr><tr><td align='right' colspan='15'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr>";	
		?>
	</table>
</form>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
