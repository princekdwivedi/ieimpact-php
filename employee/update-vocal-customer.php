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
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$employeeObj				=	new employee();

	$pagingObj					=	new Paging();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo			=	(int)$_REQUEST['recNo'];
	}
	else
	{
		$recNo			=	0;
	}
	
	
	$pagingObj			=	new Paging();
	$rec				=	"";
	$searchText			=	"";
	$search				=	"";
	$whereClause		=	" WHERE memberType='".CUSTOMERS."' AND isActiveCustomer=1";
	$orderBy			=	"firstName";
	$queryString		=	"";
	$andClause			=	"";
	$andClause1			=	"";
	$searchByName		=	"";
	$searchCustomerBy	=	"";
	$a_searchCustomers	=	array(""=>"All","yes"=>"All Vocal Customers","no"=>"All Non Vocal Customers");

	if(isset($_GET['searchCustomerBy']) && $_GET['searchCustomerBy'] != "")
	{
		$searchCustomerBy	=	$_GET['searchCustomerBy'];
		if(!empty($searchCustomerBy) && array_key_exists($searchCustomerBy,$a_searchCustomers)){
			$andClause		=	" AND isVocalCustomer='".$searchCustomerBy."'";
			$queryString	=	"&searchCustomerBy=".$searchCustomerBy;
		}
	}

	if(isset($_GET['searchByName']) && $_GET['searchByName'] != "")
	{
		$searchByName		=	$_GET['searchByName'];
		if(!empty($searchByName)){
			

			$andClause		=	" AND completeName='$searchByName'";
			$queryString	=	"&searchByName=".$searchByName;
		}
	}

	$redirectLink		=	"?recNo=".$recNo;
	if(!empty($queryString))
	{
		$redirectLink  .=	$queryString;
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		$marked_unmarked	=	$_POST['mark_vocal'];
		foreach($marked_unmarked as $memberId=>$value){
			dbQuery("UPDATE members SET isVocalCustomer='$value' WHERE memberId=$memberId");
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/update-vocal-customer.php".$redirectLink);
		exit();
	}
?>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
	<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
	</script>
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

	<script type="text/javascript">
		$().ready(function() {
			$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 290,selectFirst: false});
		});
	</script>
	<table width="98%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" class='smalltext23'><b>ADD/MANAGE VOCAL CUSTOMERS</b></td>
		</tr>
	</table>
	<br>
		<form name="searchForm" action=""  method="GET">
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<!--<tr>
				<td width="13%" class="smalltext23"><b>VIEW CUSTOMERS </b></td>
				<td width="2%" class="smalltext23"><b>:</b></td>
				<td width="32%" class="smalltext23">
				
					<?php
						foreach($a_searchCustomers as $key=>$value)
						{
							$checked				=	"";
							if($searchCustomerBy	==	$key)
							{
								$checked	=	"checked";
							}
							echo "<input type='radio' name='searchCustomerBy' value='$key' $checked onclick='document.searchForm.submit();'>$value&nbsp;";
						}
					?>
					
				</td>-->
				<td width="17%" class="smalltext23"><b>SEARCH CUSTOMER NAME</b></td>
				<td width="2%" class="smalltext23"><b>:</b></td>
				<td width="20%">
					<input type='text' name="searchByName" size="50" value="<?php echo $searchByName;?>" id="searchName" style="height:22px;width:250px;style:border 1px solid #333333;">
				</td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL;?>/images/search-button.gif" border="0">
					<input type='hidden' name='searchFormSubmit' value='1'>
				</td>
			</tr>
			<tr>
				<td colspan='5' height="5">
				</td>
			</tr>
		</table>
	</form>
	<br><br>
	<form name="updateVocals" action=""  method="POST">
	<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
<?php
	if(!empty($searchByName)){
		$start					  =	0;
		$recsPerPage	          =	50;//how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members";
		$pagingObj->selectColumns = "*";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/update-vocal-customer.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
		?>
		
			<tr height='25' bgcolor="#373737">
				<td width='3%' class='smalltext12'>&nbsp;</td>
				<td width='30%' class='smalltext12'>Customer Name</td>
				<!--<td width='10%' class='smalltext12'>Customer Since</td>
				<td width='10%' class='smalltext12'>Total Orders</td>-->
				<td width='18%' class='smalltext12'>Is Currently Vocal Customer</td>
				<td class='smalltext12'>Action</td>			
			</tr>
		<?php
			$i								=	$recNo;
			while($row						=   mysql_fetch_assoc($recordSet))
			{
				$i++;

				$memberId					=	$row['memberId'];
				$customerName				=	ucwords(stripslashes($row['completeName']));
				$totalOrdersPlaced			=	$row['totalOrdersPlaced'];
				$addedOn					=	showDate($row['addedOn']);
				$isVocalCustomer			=	$row['isVocalCustomer'];

				$vocalText					=	"<font color='red'>No</font>";
				$checked1					=	"";
				$checked2					=	"checked";
				if($isVocalCustomer			==	'yes'){
					$vocalText				=	"<font color='green'>Yes</font>";
					$checked1				=	"checked";
					$checked2				=	"";
				}


				
				$bgColor				=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=   "class='rwcolor2'";
				}

				

				
		?>
			<tr <?php echo $bgColor;?> height="30">
				<td class="smalltext23" valign="top" style="text-align:right">
					<?php echo $i;?>)&nbsp;
				</td>
				<td class="smalltext23" valign="top">
					<?php echo $customerName;?></a>
				</td>
				<!--<td class="smalltext23" valign="top">
					<?php
						echo $addedOn;
					?>
				</td>
				<td class="smalltext23" valign="top">
					<?php
						echo $totalOrdersPlaced;
					?>
				</td>-->
				<td class="smalltext23"  style="text-align:center">
					<?php
						echo $vocalText;
					?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<input type="radio" name="mark_vocal[<?php echo $memberId;?>]" value="yes" <?php echo $checked1;?>><font color='red'>Vocal Customer</font>&nbsp;
					<input type="radio" name="mark_vocal[<?php echo $memberId;?>]" value="no" <?php echo $checked2;?>><font color='green'>Not Vocal Customer</font>
				</td>
			</tr>
		<?php
			}
		?>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td colspan="5" style='text-align:left;'>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='redirectLink' value='<?php echo $redirectLink;?>'>
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		<?php
			echo "<tr><td colspan='8'><table width='100%'><tr><td style='text-align:right;'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
		}
		else
		{
			echo "<tr><td colspan='5' height='200' style='text-align:center;' class='error'><center><b>No Record Found.</b></td></tr>";
		}
				
	}
	else{
		echo "<tr><td colspan='5' height='200' style='text-align:center;' class='error'><center><b>Search a Customer.</b></td></tr>";
	}
	echo "</table></form>";

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>