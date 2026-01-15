<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT		    . "/classes/pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}
	$queryString					=	"";
?>
<table cellpadding="0" cellspacing="0" width='70%'align="left" border='0'> 
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td class="textstyle3" colspan="5">
			EMPLOYEES INTERNET SPEED 
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
<?php
	
	$start					  =	0;
	$recsPerPage	          =	100;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  = $recNo;
	$pagingObj->whereClause   =	"";
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	"mbps DESC";
	$pagingObj->table		  =	"employee_internet_speed INNER JOIN employee_details ON employee_internet_speed.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_internet_speed.*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/employees-internet-speed.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{

		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i					  =	$recNo;
?>

	
	<tr bgcolor="#373737" height="30">
		<td width="2%" style="text-align:center;" class="smalltext12">&nbsp;</td>
		<td width="25%" style="text-align:left;" class="smalltext12">Employee Name</td>
		<td width="15%" style="text-align:center;" class="smalltext12">KBPS/SECONDS</td>
		<td width="15%" style="text-align:center;" class="smalltext12">MBPS/SECONDS</td>
		<td style="text-align:left;" class="smalltext12">DATE & TIME</td>
	</tr>
	<?php
		while($row1					=   mysqli_fetch_assoc($recordSet))
		{
			
			$employeeId				=	$row1['employeeId'];
			$employeeName			=	stripslashes($row1['fullName']);
			$mbps			        =	stripslashes($row1['mbps']);
			$kbps			        =	stripslashes($row1['kbps']);
			$addedOn			    =	$row1['addedOn'];
			$addedTime			    =	$row1['addedTime'];
			
			$dateTime 				=	showFullDateFullTime($addedOn,$addedTime);
				
			$i++;
			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
		?>
		<tr height="23" <?php echo $bgColor;?>>
			<td class="smalltext2"><?php echo $i;?>)</td>
			<td class="smalltext2"><?php echo $employeeName;?></td>
			<td class="textstyle1"style="text-align:center;"><?php echo $kbps;?></td>
			<td class="textstyle1"style="text-align:center;"><?php echo $mbps;?></td>
			<td class="textstyle1"><?php echo $dateTime;?></td>
		</tr>
		<?php
			}
		
	 ?>
	 <tr>
		<td colspan="15%" style="text-align:right"><?php echo $pagingObj->displayPaging($queryString);?>&nbsp;&nbsp;</td>
	 </tr>
</table>
<?php
	}
	else{
		echo "<tr><td align='center' class='error2' colspan='5'><b>No Records Found</b></td></tr>";
	}
	echo "</table>";

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>