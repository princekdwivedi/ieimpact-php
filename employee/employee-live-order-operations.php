<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$pagingObj					=	new Paging();

	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$searchOrder		    =	"";
	$searchEmployeeId 		=	0;
	$t_searchOrder		    =	"";
	$forFromDate 			=	date('d-m-Y');
	$t_forFromDate          =   "";
	$toDate 				=	"";
	$t_toDate 				=	"";
	$allPdfEmployees 		=	$employeeObj->getAllPdfEmployees();
	$all_employeesList 		=	array();
	if(mysqli_num_rows($allPdfEmployees)){
		while($row1         =   mysqli_fetch_assoc($allPdfEmployees)){
			$employeeId 	=	$row1['employeeId'];
			
			$all_employeesList[$employeeId] = stripslashes($row1['firstName'])." ".stripslashes($row1['lastName']);
		}
	}

	

	if(isset($_REQUEST['recNo']))
	{
		$recNo				=	(int)$_REQUEST['recNo'];
		$revertLink			=	"&recNo=".$recNo;
	}
	if(empty($recNo))
	{
		$recNo				=	0;
	}

	if(isset($_GET['forFromDate'])){
		$forFromDate 		=	trim($_GET['forFromDate']);		
	}
	if(!empty($forFromDate)){
		list($d,$m,$y)  =   explode("-",$forFromDate);
		$t_forFromDate  =   $y."-".$m."-".$d;
	}

	if(isset($_GET['toDate'])){
		$toDate 		    =	trim($_GET['toDate']);		
	}
	if(!empty($toDate)){
		list($td,$tm,$ty)  =   explode("-",$toDate);
		$t_toDate	=	$ty."-".$tm."-".$td;
	}
	
	$whereClause			=	"WHERE members_orders.isVirtualDeleted=0";
	$orderBy				=	"trackId DESC";
	$queryString			=	"";
	$andClause				=	"";
	if(isset($_GET['searchOrder'])){
		$searchOrder		=	trim($_GET['searchOrder']);
		if(!empty($searchOrder)){
			$t_searchOrder	=	$searchOrder;
			$t_searchOrder  =	stringReplace("<=>","#",$searchOrder);
			$andClause	    =	" AND orderAddress='$t_searchOrder'";
			$queryString	=	"&orderAddress=".$searchOrder;			
		}
	}

	if(isset($_GET['searchEmployeeId'])){
		$searchEmployeeId	=	trim($_GET['searchEmployeeId']);
		if(!empty($searchEmployeeId)){
			$andClause	   .=	" AND order_employee_works.employeeId=$searchEmployeeId";
			$queryString   .=	"&searchEmployeeId=".$searchEmployeeId;			
		}
	}

	$dateClause 			=	"";

	if(!empty($t_forFromDate)){		
		$dateClause	   	   =	" AND order_employee_works.date='".$t_forFromDate."'";
		$queryString      .=	"&forFromDate=".$forFromDate;
	}

	if(!empty($t_forFromDate) && !empty($t_toDate)){		
		$dateClause	   	   =	" AND order_employee_works.date >='".$t_forFromDate."' AND order_employee_works.date <='".$t_toDate."'";
		$queryString      .=	"&toDate=".$toDate;	
	}

	if(!empty($dateClause)){
		$andClause	      .=   $dateClause;
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 365,selectFirst: false});
});
    /*function validForm()
	{
		form1	=	document.searchEmployeeWork;
		if((form1.searchOrder.value == "" || form1.searchOrder.value == "0" || form1.searchOrder.value == " ") &&  form1.searchEmployeeId.value == "0")
		{
			alert("Please enter order address or select an employee");
			return false;
		}
	}*/

</script>
<form name="searchEmployeeWork" action=""  method="GET">
	<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="18%" class="smalltext2"><b>EMPLOYEE WORKS ORDERS </b></td>
			<td width="20%">
				<input type='text' name="searchOrder" size="27" value="<?php echo $searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;" placeholder="Please enter order address to search">
			</td>
			<td width="7%" class="smalltext2"><b>EMPLOYEE</b></td>
			<td width="15%">
				<select name="searchEmployeeId" style="border:1px solid #4d4d4d;font-size:15px;">
					<option value="0">Select</option>
					<?php
						foreach($all_employeesList as $kk=>$vv){
							$select 	=	"";
							if($kk      ==   $searchEmployeeId){
								$select =	"selected";
							}

							echo "<option value='$kk' $select>$vv</option>";
						}
					?>
				</select>
			</td>
			<td width="11%" class="smalltext2"><b>FROM/FOR DATE :</b></td>
			<td width="11%" >
				<input type="text" name="forFromDate" value="<?php echo $forFromDate;?>" id="date1" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date1','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="6%" class="smalltext2"><b>TO DATE :</b></td>
			<td width="16%" >
				<input type="text" name="toDate" value="<?php echo $toDate;?>" id="date3" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date3','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td>
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<br />
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
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
	$pagingObj->table		  =	"order_employee_works INNER JOIN members_orders ON order_employee_works.orderId=members_orders.orderId INNER JOIN employee_details ON order_employee_works.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "memberId,orderAddress,orderAddedOn,orderAddedTime,fullName,order_employee_works.*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/employee-live-order-operations.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
	<tr height='25' bgcolor="#373737">
		<td width='5%' class='smalltext12'>&nbsp;Sr No</td>
		<td width='25%' class='smalltext12'>Order Address</td>
		<td width='10%' class='smalltext12'>Placed Time (IST)</td>
		<td width='12%' class='smalltext12'>Employee</td>
		<td width='40%' class='smalltext12'>Performed Task</td>
		<td class='smalltext12'>Date&Time (IST)</td>
	</tr>
	<?php
		$i							=	$recNo;
		while($row					=   mysqli_fetch_assoc($recordSet))
		{
			$i++;

			$employeeId				=	$row['employeeId'];
			$memberId				=	$row['memberId'];
			$orderId				=	$row['orderId'];
			$employeeName			=	stripslashes($row['fullName']);
			$orderAddress			=	stripslashes($row['orderAddress']);
			$performedTask			=	stripslashes($row['performedTask']);
			$date					=	showDateMonth($row['date']);
			$time					=	showTimeFormat($row['time']);
			$orderAddedOn			=	showDateMonth($row['orderAddedOn']);
			$orderAddedTime			=	showTimeFormat($row['orderAddedTime']);

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}
	?>
	<tr <?php echo $bgColor;?> height="30">
		<td class="smalltext2" valign="top">
			&nbsp;<?php echo $i.")";?>
		</td>
		<td valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>" class='link_style6'><?php echo $orderAddress;?></a>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $orderAddedOn.",".$orderAddedTime;?>
		</td>
		<td valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $employeeId;?>&showingEmployeeOrder=1" class='link_style6'><?php echo $employeeName;?></a>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $performedTask;?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $date.",".$time;?>
		</td>
	</tr>
	<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td colspan='6' style='text-align:right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
	}
	else{
		echo "<tr><td colspan='6' style='text-align:center' height='250' class='error2'>Sorry No Record Found.</td></tr>";
	}
    echo "</table>";
	

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>