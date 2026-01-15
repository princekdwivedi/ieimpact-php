<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/admin/includes/common-array.php");
	include(SITE_ROOT	."/classes/pagingclass.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$pagingObj			=	new Paging();
	$rec				=	"";
	$searchText			=	"";
	$search				=	"";
	$whereClause		=	"";
	$orderBy			=	"firstName";
	$queryString		=	"";
	$andClause			=	" AND isActive=1";
	$searchByText		=	"";
	
	if(isset($_GET['searchText']))
	{
		$searchText	=	trim($_GET['searchText']);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/assign-pdf-clients.php?search=$searchText");
		exit();
	}
	if(isset($_GET['search']))
	{
		$search	=	$_GET['search'];
		if(!empty($search))
		{
			$whereClause	=	"WHERE fullName LIKE '%$search%'";
			
			$queryString	=	"&search=".$search;
			$searchText		=	$search;
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript">
$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-employee.php", {width: 265,selectFirst: false});
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
</script>
	<table width="98%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" class='heading'>ASSIGN EMPLOYEE TO PDF CLIENTS</td>
		</tr>
	</table>
	<br>
		<form name="searchForm" action=""  method="GET" onsubmit="return search();">
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<tr>
				<td width="23%" class="smalltext2"><b>SEARCH AN EMPLOYEE BY NAME</b></td>
				<td width="2%" class="smalltext2"><b>:</b></td>
				<td width="27%">
					<input type='text' name="searchText" size="40" value="<?php echo $searchText;?>" id="searchName">
				</td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='searchFormSubmit' value='1'>
				</td>
			</tr>
			<tr>
				<td colspan='5' height="5">
				</td>
			</tr>
		</table>
	</form>
<?php
	if(!empty($search))
	{
		if(isset($_REQUEST['recNo']))
		{
			$recNo		    =	(int)$_REQUEST['recNo'];
		}
		if(empty($recNo))
		{
			$recNo	=	0;
		}
		
		$start					  =	0;
		$recsPerPage	          =	20;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details";
		$pagingObj->selectColumns = "*";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/assign-pdf-clients.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
		?>
			<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
				<tr>
					<td colspan='5' class="linkstyle4">
						<?php echo $searchByText;?>
					</td>
				</tr>
				<tr>
					<td colspan='5' class="">
						<hr size='1' width='100%' color='#428484'>
					</td>
				</tr>
				<tr>
					<td width='4%' class='text'>Sr.No</td>
					<td width='13%' class='text'>Name</td>
					<td width='18%' class='text'>Email</td>
					<td width='25%' class='text'>Address</td>
					<td class='text'>Action</td>
				</tr>
				<tr>
					<td colspan='5'>
						<hr size='1' width='100%' color='#428484'>
					</td>
				</tr>
		<?php
			$i	=	0;
			while($row	=   mysql_fetch_assoc($recordSet))
			{
				$i++;
				$employeeId				=	$row['employeeId'];
				$firstName				=	$row['firstName'];
				$lastName				=	$row['lastName'];
				$address				=	$row['address'];
				$email					=	$row['email'];
				$isActive				=	$row['isActive'];
				$employeeName			=	$firstName." ".$lastName;
				$employeeName			=	ucwords($employeeName);
			?>
			<tr>
				<td class="smalltext2" valign="top">
					<?php echo $i.")";?>
				</td>
				<td class="smalltext2" valign="top">
					<?php
						echo $employeeName;
					?>
				</td>
				<td class="smalltext2" valign="top">
					<?php
						echo $email;
					?>
				</td>
				<td class="smalltext2" valign="top">
					<?php
						echo $address;
					?>
				</td>
				<td valign="top" class="smalltext">
					<a href="<?php echo SITE_URL_EMPLOYEES?>/assign-employee-pdf.php?ID=<?php echo $employeeId;?>" class='linkstyle3'>Add-Edit Pdf Client</a>&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<hr size="1" width="100%" color="#428484">
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
			echo "<br><center><font class='error'><b>No employee available !!</b></font></center>";
		}
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/show-assigned-pdf-employees.php");
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>