<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_SUPPORT	. "/classes/support.php");
	$employeeObj				=  new employee();
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$suppurtObj					=  new support();
	$a_supportParent			=  array();
	include(SITE_ROOT_EMPLOYEES . "/includes/check-suport-access.php");
	if($result					=	$suppurtObj->getSupportCategory())
	{
		while($row		=	mysql_fetch_assoc($result))
		{
			$t_parentId	=	$row['categoryId'];
			$t_parentName	=	stripslashes($row['categoryName']);
			$a_supportParent[$t_parentId]		=	$t_parentName;
		}
	}
	$whereClause				= "WHERE parentId IN ($supportAccessFor)";
	$orderBy					= "supportId DESC";
	$queryString				= "";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<?php
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"support_master";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-supports.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>
<tr>
	<td class="smalltext2" width="5%"><b>Sr. No</b></td>
	<td class="smalltext2" width="15%"><b>Requested By</b></td>
	<td class="smalltext2" width="20%"><b>Email</b></td>
	<td class="smalltext2" width="20%"><b>Support Need In</b></td>
	<td class="smalltext2" width="20%"><b>Category</b></td>
	<td class="smalltext2" width="10%"><b>Added On</b></td>
	<td class="smalltext2">&nbsp;<b>Status</b></td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" bgcolor="#e4e4e4">
	</td>
</tr>
<?php
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$supportId			=	$row['supportId'];
			$parentId			=	$row['parentId'];
			$categoryId			=	$row['categoryId'];
			$name				=	stripslashes($row['name']);
			$email				=	stripslashes($row['email']);
			$addedOn			=	showDate($row['addedOn']);
			$status				=	$row['status'];
			$parentName			=	$a_supportParent[$parentId];
			$categoryName		=	$suppurtObj->getSupportCategoryName($categoryId);
			$statusText			=	"New";
			if($status			==	1)
			{
				$statusText		=	"Accepted";
			}
	?>
	<tr>
		<td class="textstyle"><?php echo $i;?>)</td>
		<td class="textstyle"><a href="<?php echo SITE_URL_EMPLOYEES;?>/support-order-details.php?ID=<?php echo $supportId;?>" class='link_style12'><?php echo $name;?></a></td>
		<td class="textstyle"><?php echo $email;?></td>
		<td class="textstyle"><?php echo $parentName;?></td>
		<td class="textstyle"><?php echo $categoryName;?></td>
		<td class="textstyle"><?php echo $addedOn;?></td>
		<td class="error"><?php echo $statusText;?></td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" bgcolor="#e4e4e4">
		</td>
	</tr>
	<?php
		}
		echo "<tr><td colspan='10'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
	}
	else
	{
		echo "<tr><td class='error' height='200' align='center'><b>NO SUPPORT AVAILABLE !!</b></td></tr>";
	}
	echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>