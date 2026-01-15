<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$pagingObj						=	new Paging();
	$link							=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
		if(!empty($recNo))
		{
			$link					=	"?recNo=".$recNo;
		}
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}

	$employeeObj					=	new employee();
	$whereClause					=	"WHERE isActive=1 AND hasPdfAccess=1";
	$andCaluse						=	"";
	$andCaluse1						=	"";
	$queryString					=	"";
	$orderBy						=	"totalOrderQaDone DESC";

	if(isset($_REQUEST['formSubmitted']))
	{
		//pr($_REQUEST);
		$a_markUnmarkAccess		=	$_POST['markUnmarkAccess'];
		foreach($a_markUnmarkAccess as $empId=>$action)
		{
			if($action			==	3)
			{
				dbQuery("UPDATE employee_details SET hasQaDoneAccess=1,hasAllQaAccess=1 WHERE employeeId=$empId");
			}
			else
			{
				$hasQaDoneAccess	=	1;
				if($action			==	2)
				{
					$hasQaDoneAccess=	0;
				}
				dbQuery("UPDATE employee_details SET hasQaDoneAccess=$hasQaDoneAccess,hasAllQaAccess=0 WHERE employeeId=$empId");
			}
		}
		$_markUnmarkMessageAccess	=	$_POST['markUnmarkMessageAccess'];
		foreach($_markUnmarkMessageAccess as $empId=>$action1)
		{
			if($action1				==	2)
			{
				$action1			=	0;
			}
			dbQuery("UPDATE employee_details SET hasverificationAccess=$action1 WHERE employeeId=$empId");
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/allow-qa-access.php".$link);
		exit();
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<link href="<?php echo SITE_URL;?>/css/thickbox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_URL;?>/script/thickbox-big.js" type="text/javascript"></script>
<form name="allowDisallowqaAccess" action="" method="POST">
	<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td colspan="10" class='smalltext23'><b>ENABLE DISABLE PDF EMPLOYEES QA ACCESS</b></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr height='25' class='rwcolor'>
			<td width='3%' class='smalltext12'>&nbsp;</td>
			<td class='smalltext12' width='22%'>Enable/Disable Qa Access</td>
			<td class='smalltext12' width='22%'>Has Verification Access</td>
			<td width='25%' class='smalltext12'>&nbsp;Name</td>
			<td class='smalltext12'>Processed</td>
		</tr>
	<?php
		$start					  =	0;
		$recsPerPage	          =	150;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andCaluse;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details";
		$pagingObj->selectColumns = "employeeId,fullName,hasQaDoneAccess,totalOrderProcessedDone,totalOrderQaDone,shiftType,hasAllQaAccess,hasverificationAccess";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/allow-qa-access.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i		   = $recNo;
			while($row				=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$employeeId					=	$row['employeeId'];
				$fullName					=	stripslashes($row['fullName']);
				$hasQaDoneAccess			=	$row['hasQaDoneAccess'];
				$totalProcessOrders			=	$row['totalOrderProcessedDone'];
				$totalQaDoneOrders			=	$row['totalOrderQaDone'];
				$shiftType					=	$row['shiftType'];
				$hasAllQaAccess				=	$row['hasAllQaAccess'];
				$hasverificationAccess		=	$row['hasverificationAccess'];

				$shiftText			=	"";
				if($shiftType		==	2)
				{
					$shiftText		=	"&nbsp;<font class='smalltext'>(<font color='#ff0000'>Night Shift</font>)</font>";
				}

				$check1				=	"";
				$check2				=	"checked";
				$check3				=	"";
				$text				=	"Enable";
				$text1				=	"<font color='#ff0000'>Disable</font>";
				$text2				=	"All QA";

				$check4				=	"";
				$check5				=	"checked";
				$text4				=	"Yes";
				$text5				=	"<font color='#ff0000'>No</font>";

				if($hasverificationAccess	==	1)
				{
					$check4			=	"checked";
					$check5			=	"";
					$text4			=	"<font color='#ff0000'>Yes</font>";
					$text5			=	"No";

				}

				if($hasAllQaAccess	==	1)
				{
					$check1			=	"";
					$check2			=	"";
					$check3			=	"checked";
					$text			=	"Enable";
					$text1			=	"Disable";
					$text2			=	"<font color='#ff0000'>All QA</font>";
				}
				else
				{
					if($hasQaDoneAccess	==	1)
					{
						$check1			=	"checked";
						$check2			=	"";
						$check3			=	"";
						$text			=	"<font color='#ff0000'>Enable</font>";
						$text1			=	"Disable";
						$text2			=	"All QA";
					}
				}

						
			$bgColor			=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor		=   "class='rwcolor2'";
			}
	?>
	<tr height='25' <?php echo $bgColor;?>>
		<td class='smalltext21' valign="top">&nbsp;<?php echo $i.")";?></td>
		<td class='smalltext21'>
			<input type="radio" name="markUnmarkAccess[<?php echo $employeeId;?>]" value='1' <?php echo $check1;?>><?php echo $text;?> &nbsp;
			<input type="radio" name="markUnmarkAccess[<?php echo $employeeId;?>]" value='2' <?php echo $check2;?>><?php echo $text1;?>&nbsp;
	
			<input type="radio" name="markUnmarkAccess[<?php echo $employeeId;?>]" value='3' <?php echo $check3;?>><?php echo $text2;?>
		</td>
		<td class='smalltext21'>
			<input type="radio" name="markUnmarkMessageAccess[<?php echo $employeeId;?>]" value='1' <?php echo $check4;?>><?php echo $text4;?>&nbsp; 
			<input type="radio" name="markUnmarkMessageAccess[<?php echo $employeeId;?>]" value='2' <?php echo $check5;?>><?php echo $text5;?>
		</td>
		<td valign="top" class="smalltext21">
			<a href="<?php echo SITE_URL_EMPLOYEES?>/display-an-employee-details.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=500&width=700" title='' class='thickbox'/><font class='form_links2'><?php echo $fullName?></font></a><?php echo $shiftText;?>
		</td>
		<td valign="top" class="smalltext21">
			<?php echo $totalProcessOrders;?>
		</td>
		
	</tr>
	<?php
			}
			echo "<tr><td colspan='9'><table width='100%'><tr><td height='2'></td></tr><tr><td align='right'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr><tr><td height='2'></td></tr></table></td></tr>";
		}
	?>
	<tr>
		<td colspan="2">
			<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
			<input type="hidden" name="formSubmitted" value="1">
		</td>
	</tr>
	</table>
</form>
<?php


	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>