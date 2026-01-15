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
	$employeeObj					=	new employee();
	$searchEmployeeId    			=	0;

	

	$a_employeeList					=	array();
	if($result						=   $employeeObj->getAllPdfEmployees()){
		while($row					=   mysqli_fetch_assoc($result)){
			$t_employeeId			=   $row['employeeId'];
			$t_firstName			=   stripslashes($row['firstName']);
			$t_lastName				=   stripslashes($row['lastName']);

			$a_employeeList[$t_employeeId] = $t_firstName." ".$t_lastName;
		}
	}


	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}

	$whereClause		=	"WHERE hasPdfAccess=1 AND isActive=1 AND totalTax <> 0";
	$orderBy			=	"firstName DESC";
	$queryString		=	"";
	$link				=	"?recNo=".$recNo;
	$month				=	"";
	$year				=	"";
	$andClause			=	"";

	if(isset($_GET['searchEmployeeId'])){
		$searchEmployeeId     =	$_GET['searchEmployeeId'];
		if(!empty($searchEmployeeId)){
			$queryString .=	"&searchEmployeeId=".$searchEmployeeId;
			$andClause	 .=	" AND employee_details.employeeId=".$searchEmployeeId;
		}
	}
	
?>
<script type = "text/javascript">
	function checkValid()
	{
		form1	=  document.searchEmployees;
		if(form1.searchEmployeeId.value	==	0 || form1.searchEmployeeId.value	==	"" || form1.searchEmployeeId.value	==	" "){
			alert("Please select an employee.");
			form1.searchEmployeeId.focus();
			return false;
		}
	}
	function viewInvestment(employeeId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/download-investmeent-files.php?employeeId="+employeeId;
		prop = "toolbar=no,scrollbars=yes,width=1000,height=800,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
<form name="searchEmployees" action="" method="GET" onsubmit="return checkValid();">
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td class="smalltext23" colspan="8">
				<b>PDF EMPLOYEES INVESTMENT FILES DETAILS</b>
			</td>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>
		<tr>
			<td width="7%" class="smalltext23">
				<b>Employee</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="20%">
				<select name="employeeId">
					<option value="0">All</option>
					<?php
						foreach($a_employeeList as $k=>$v){
							$select		= "";
							if($k		== $employeeId){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>			
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>		
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_details";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/employees-investment-details.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
	<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr height='25' bgcolor="#373737">
			<td width='5%' class='smalltext12' valign="top">&nbsp;Sr.No</td>
			<td width='20%' class='smalltext12' valign="top">Employee Name</td>
			<td width='20%' class='smalltext12' valign="top">Total Tax Exemption</td>
			<td width='20%' class='smalltext12' valign="top">Taxation Rate Approximately</td>
			<td width='20%' class='smalltext12' valign="top">Total Tax</td>
			<td class='smalltext12' valign="top">&nbsp;</td>
		</tr>
<?php
		$i	=	0;
		while($row			=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$employeeId				=	$row['employeeId'];
			$fullName				=	stripslashes($row['fullName']);
			$totalTaxExemption		=	stripslashes($row['totalTaxExemption']);
			$taxRateApproximately	=	stripslashes($row['taxRateApproximately']);
			$totalTax				=	$row['totalTax'];

			$bgColor		=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor	=	"class='rwcolor2'";
			}
?>
	<tr <?php echo $bgColor;?> height="25">
		<td class="smalltext22" valign="top">
			&nbsp;<?php echo $i.")";?>
		</td>
		<td class="smalltext22" valign="top">
			<?php echo $fullName;?>
		</td>
		<td class="smalltext22" valign="top">
			<?php echo $totalTaxExemption."/-";?>
		</td>
		<td class="smalltext22" valign="top">
			<?php echo $taxRateApproximately."/-";?>
		</td>
		<td class="smalltext22" valign="top">
			<?php echo $totalTax."/-";?>
		</td>
		<td>
			<a onclick="viewInvestment(<?php echo $employeeId;?>)" class="link_style5" style="cursor:pointer;">View ALL</a>
		</td>
	</tr>
<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td style='text-align:right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
?>
	</table>
<?php

	}
	else{
		echo "<br/><br /><br/><br /><br/><br /><center><font class='error'><b>No record found.</b></font></center><br/><br /><br/><br /><br/><br />";
	}

	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>