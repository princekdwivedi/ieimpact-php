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
	$whereClause		=	" WHERE memberType='".CUSTOMERS."'";
	$orderBy			=	"firstName";
	$queryString		=	"";
	$andClause			=	"";
	$searchByText		=	"";
	$showForm			=	false;
	$assigningCustomerId=	0;
	$assignCustomerName	=	"";
	$a_pdfEmployees		=	array();
	$searchCustomerBy	=	0;

	$display			=	"";
	$display1			=	"none";
	$display2			=	"none";
	$display3			=	"none";
	$display4			=	"none";



	$query				=	"SELECT * FROM employee_details WHERE isActive=1 AND hasPdfAccess=1 ORDER BY firstName";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row		=	mysqli_fetch_assoc($result))
		{
			$employeeId			=	$row['employeeId'];
			$firstName			=	stripslashes($row['firstName']);
			$lastName			=	stripslashes($row['lastName']);
			$receivePdfEmails	=	$row['receivePdfEmails'];
			$maximumOrdersAccept=	$row['maximumOrdersAccept'];

			$employeeName		=	$firstName." ".$lastName;

			$a_pdfEmployees[$employeeId]	=	$employeeName."<=>".$receivePdfEmails."<=>".$maximumOrdersAccept;
		}
	}
	//pr($a_pdfEmployees);
	
	if(isset($_REQUEST['searchFormSubmit']))
	{
		extract($_REQUEST);
		pr($_REQUEST);
		//die();
		if($searchCustomerBy	==	0)
		{
			$searchText			=	trim($searchText);
		}
		elseif($searchCustomerBy==	1)
		{
			$searchText			=	trim($searchText1);
		}
		elseif($searchCustomerBy==	2)
		{
			$searchText			=	trim($searchText2);
		}
		elseif($searchCustomerBy==	3)
		{
			$searchText			=	trim($searchText3);
		}
		elseif($searchCustomerBy==	4)
		{
			$searchText			=	trim($searchText4);
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/assign-pdf-client-to-employee.php?searchCustomerBy=$searchCustomerBy&search=$searchText");
		exit();
	}

	if(isset($_GET['ID']))
	{
		$assigningCustomerId	=	trim($_GET['ID']);

		$query		=	"SELECT * FROM members".$whereClause." AND memberId=$assigningCustomerId";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row			=	mysqli_fetch_assoc($result);
			$t_firstName	=	stripslashes($row['firstName']);
			$t_lastName		=	stripslashes($row['lastName']);

			$assignCustomerName	=	$t_firstName." ".substr($t_lastName, 0, 1);
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	if(isset($_GET['search']))
	{
		$search	=	$_GET['search'];
		if(!empty($search))
		{
			$showForm			=	true;
			$pos				=	strpos($search, " ");
			if($pos				==  true)
			{
				
				$firstName		=	substr($search,0,$pos);
				$lastName		=	substr($search,$pos+1);

				$andClause		=	" AND (firstname LIKE '%$firstName%' OR lastName LIKE '%$lastName%')";
			}
			else
			{
				$andClause		=	" AND (firstname LIKE '%$search%')";
			}
		}
	}
	if(isset($_GET['searchCustomerBy']))
	{
		$searchCustomerBy		    =	$_GET['searchCustomerBy'];
		if(!empty($searchCustomerBy))
		{
			$andClause		       .=	" AND appraisalSoftwareType=$searchCustomerBy";

			if($searchCustomerBy	==	1)
			{
				$display			=	"none";
				$display1			=	"";
				$display2			=	"none";
				$display3			=	"none";
				$display4			=	"none";
			}
			elseif($searchCustomerBy==	2)
			{
				$display			=	"none";
				$display1			=	"none";
				$display2			=	"";
				$display3			=	"none";
				$display4			=	"none";
			}
			elseif($searchCustomerBy==	3)
			{
				$display			=	"none";
				$display1			=	"none";
				$display2			=	"none";
				$display3			=	"";
				$display4			=	"none";
			}
			elseif($searchCustomerBy==	4)
			{
				$display			=	"none";
				$display1			=	"none";
				$display2			=	"none";
				$display3			=	"none";
				$display4			=	"";
			}
		}
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
$().ready(function() {
	$("#searchName1").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-wintotal-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName2").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-aci-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName3").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-clickforms-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName4").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-appraise-customer.php", {width: 290,selectFirst: false});
});
function checkAppraiserType(flag)
{
	//alert(flag);
	if(flag	==	1)
	{
		document.getElementById('showAllCustomer').style.display = 'none';
		document.getElementById('showWinTotalCustomer').style.display = 'inline';
		document.getElementById('showACICustomer').style.display = 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display = 'none';
	}
	else if(flag	==	2)
	{
		document.getElementById('showAllCustomer').style.display = 'none';
		document.getElementById('showWinTotalCustomer').style.display = 'none';
		document.getElementById('showACICustomer').style.display = 'inline';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display = 'none';
	}
	else if(flag	==	3)
	{
		document.getElementById('showAllCustomer').style.display = 'none';
		document.getElementById('showWinTotalCustomer').style.display = 'none';
		document.getElementById('showACICustomer').style.display = 'none';
		document.getElementById('showClickformsCustomer').style.display = 'inline';
		document.getElementById('showAppraiseCustomer').style.display = 'none';
	}
	else if(flag	==	4)
	{
		document.getElementById('showAllCustomer').style.display = 'none';
		document.getElementById('showWinTotalCustomer').style.display = 'none';
		document.getElementById('showACICustomer').style.display = 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display = 'inline';
	}
	else
	{
		document.getElementById('showAllCustomer').style.display = 'inline';
		document.getElementById('showWinTotalCustomer').style.display = 'none';
		document.getElementById('showACICustomer').style.display = 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display = 'none';
	}
}
function search()
{
	form1	=	document.searchForm;
	if(form1.searchText.value == "")
	{
		alert("Please Enter Customer Name !!");
		form1.searchText.focus();
		return false;
	}
}

function checkedChild(flag)
{
	mainBox	=   document.getElementById('mainEmployeeId'+flag);
	if(mainBox.checked == true)
	{
		document.getElementById('child'+flag).checked  = true;
		document.getElementById('childx'+flag).checked = true;
	}
	else
	{
		document.getElementById('child'+flag).checked  = false;
		document.getElementById('childx'+flag).checked = false;
	}
}
function removedCheckedChild(flag)
{
	if(document.getElementById('deleteEmployeeId'+flag).checked == true)
	{
		document.getElementById('child'+flag).checked			= false;
		document.getElementById('childx'+flag).checked			= false;
		document.getElementById('mainEmployeeId'+flag).checked  = false
	}
	else
	{
		document.getElementById('child'+flag).checked		   = true;
		document.getElementById('childx'+flag).checked		   = true;
		document.getElementById('mainEmployeeId'+flag).checked = true
	}
}
</script>
	<table width="98%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" class='heading'>ASSIGN PDF CUSTOMER TO EMPLOYEES</td>
		</tr>
	</table>
	<br>
		<form name="searchForm" action=""  method="POST">
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<tr>
				<td width="18%" class="smalltext2"><b>SEARCH A CUSTOMER OF</b></td>
				<td width="2%" class="smalltext2"><b>:</b></td>
				<td width="14%">
					<select name="searchCustomerBy" onclick="return checkAppraiserType(this.value);">
						<option value="0">All</option>
						<?php
							foreach($a_appraisalSoftware as $key=>$value)
							{
								$select		=	"";
								if($searchCustomerBy	==	$key)
								{
									$select	=	"selected";
								}
								echo "<option value='$key' $select>$value</option>";
							}
						?>
					</select>
				</td>
				<td width="15%" class="smalltext2"><b>BY CUSTOMER NAME</b></td>
				<td width="2%" class="smalltext2"><b>:</b></td>
				<td width="30%">
					<div id="showAllCustomer" style="display:<?php echo $display;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type='text' name="searchText" size="50" value="<?php echo $search;?>" id="searchName">
							</td>
						</tr>
						</table>
					</div>
					<div id="showWinTotalCustomer" style="display:<?php echo $display1;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type='text' name="searchText1" size="50" value="<?php echo $search;?>" id="searchName1">
							</td>
						</tr>
						</table>
					</div>
					<div id="showACICustomer" style="display:<?php echo $display2;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type='text' name="searchText2" size="50" value="<?php echo $search;?>" id="searchName2">
							</td>
						</tr>
						</table>
					</div>
					<div id="showClickformsCustomer" style="display:<?php echo $display3;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type='text' name="searchText3" size="50" value="<?php echo $search;?>" id="searchName3">
							</td>
						</tr>
						</table>
					</div>
					<div id="showAppraiseCustomer" style="display:<?php echo $display4;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type='text' name="searchText4" size="50" value="<?php echo $search;?>" id="searchName4">
							</td>
						</tr>
						</table>
					</div>
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
	<br><br>
<?php
	
	if($showForm	==	 true)
	{
		$query		=	"SELECT memberId,firstName,lastName,appraisalSoftwareType FROM members".$whereClause.$andClause;
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
	?>
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<?php
			$i					=	0;
			while($row			=	mysqli_fetch_assoc($result))
			{
				$i++;
				$memberId		=	$row['memberId'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
				$appraisalText	=	$a_appraisalSoftware[$appraisalSoftwareType];

				$customerName	=	$firstName." ".substr($lastName, 0, 1);
	?>
	<tr>
		<td class="smalltext2" width="5%"><b><?php echo $i;?>)</b></td>
		<td class="smalltext2" width="25%"><b><?php echo $customerName;?></b></td>
		<td class="error" width="25%"><b><?php echo $appraisalText;?></b></td>
		<td class="smalltext2"><b>
			<?php 
				if($memberId == $assigningCustomerId)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/assign-pdf-client-to-employee.php?searchCustomerBy=$searchCustomerBy&search=$search&ID=$memberId' class='link_style4'>Currently Assigning</a>";
				}
				else
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/assign-pdf-client-to-employee.php?searchCustomerBy=$searchCustomerBy&search=$search&ID=$memberId' class='link_style3'>Assign Now</a>";
				}
			?>
		</b></td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php
			}
	?>
	</table>
	<?php
		if(!empty($assigningCustomerId))
		{

			if(isset($_REQUEST['formSubmitted1']))
			{
				extract($_REQUEST);
				//pr($_REQUEST);
				//die();

				if(isset($_POST['pdfEmployeeId']))
				{
					$a_pdfEmployeeId	=	$_POST['pdfEmployeeId'];
				}
				else
				{
					$a_pdfEmployeeId	=	array();
				}
				//pr($a_pdfEmployeeId);

				if(isset($_POST['alreadyAssign']))
				{
					$a_alreadyAssign	=	$_POST['alreadyAssign'];
				}
				else
				{
					$a_alreadyAssign	=	array();
				}

				if(isset($_POST['allPdfEmployees']))
				{
					$a_allPdfEmployees	=	$_POST['allPdfEmployees'];
				}
				else
				{
					$a_allPdfEmployees	=	array();
				}
				if(isset($_POST['maximumOrdersAccept']))
				{
					$a_maximumOrdersAccept	=	$_POST['maximumOrdersAccept'];
				}
				else
				{
					$a_maximumOrdersAccept	=	array();
				}
				if(isset($_POST['hasEmailAccess']))
				{
					$a_hasEmailAccess		=	$_POST['hasEmailAccess'];
				}
				else
				{
					$a_hasEmailAccess	=	array();
				}
				if(isset($_POST['hasReplyAccess']))
				{
					$a_hasReplyAccess	=	$_POST['hasReplyAccess'];
				}
				else
				{
					$a_hasReplyAccess	=	array();
				}
				if(isset($_POST['hasQaAccess']))
				{
					$a_hasQaAccess		=	$_POST['hasQaAccess'];
				}
				else
				{
					$a_hasQaAccess		=	array();
				}
				if(isset($_POST['isDelete']))
				{
					$a_isDelete			=	$_POST['isDelete'];
				}
				else
				{
					$a_isDelete			=	array();
				}

				if(empty($a_pdfEmployeeId))
				{
					dbQuery("DELETE FROM pdf_clients_employees WHERE customerId=$assigningCustomerId");
				}
				else
				{
					
					foreach($a_allPdfEmployees as $employeeId=>$v)
					{
						if(array_key_exists($employeeId,$a_isDelete))
						{
							dbQuery("DELETE FROM pdf_clients_employees WHERE employeeId=$employeeId");

							dbQuery("UPDATE employee_details SET hasPdfAccess=0,receivePdfEmails=0,maximumOrdersAccept=0 WHERE employeeId=$employeeId");
						}
						else
						{
							if(array_key_exists($employeeId,$a_pdfEmployeeId))
							{
								$hasReplyAccess	=	$a_hasReplyAccess[$employeeId];
								$hasQaAccess	=	$a_hasQaAccess[$employeeId];
								if(empty($hasReplyAccess))
								{
									$hasReplyAccess	=	0;
								}
								if(empty($hasQaAccess))
								{
									$hasQaAccess	=	0;
								}
								if(!empty($hasReplyAccess) || !empty($hasQaAccess))
								{
									$assignedId		=	$a_alreadyAssign[$employeeId]; 
									if(empty($assignedId))
									{
										dbQuery("INSERT INTO pdf_clients_employees SET customerId=$assigningCustomerId,employeeId=$employeeId,hasReplyAccess=$hasReplyAccess,hasQaAccess=$hasQaAccess");	
									}
									else
									{
										dbQuery("UPDATE pdf_clients_employees SET hasReplyAccess=$hasReplyAccess,hasQaAccess=$hasQaAccess WHERE customerId=$assigningCustomerId AND employeeId=$employeeId AND assignedId=$assignedId");	
									}
								}
								$maximumOrderToAccept	=	$a_maximumOrdersAccept[$employeeId];
								$iaReceivedEmail		=	$a_hasEmailAccess[$employeeId];
								if(empty($iaReceivedEmail))
								{
									$iaReceivedEmail	=	0;
								}

								dbQuery("UPDATE employee_details SET receivePdfEmails=$iaReceivedEmail,maximumOrdersAccept=$maximumOrderToAccept WHERE employeeId=$employeeId");
							}
							else
							{
								$assignedId		=	$a_alreadyAssign[$employeeId]; 
								if(!empty($assignedId))
								{
									dbQuery("DELETE FROM pdf_clients_employees WHERE employeeId=$employeeId AND customerId=$assigningCustomerId AND assignedId=$assignedId");
								}
							}
						}
					}
				}
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/assign-pdf-client-to-employee.php?searchCustomerBy=$searchCustomerBy&search=$search&ID=$assigningCustomerId");
				exit();
			}	
	?>
	<br>
	<form  name='assignEmployee' method='POST' action="">
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<tr>
				<td colspan="10" class="heading1">
					Assigning Employee To Customer : <font color="#ff0000"><?php echo $assignCustomerName;?></font>
				</td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<tr>
				<td width="5%" class="smalltext2"><b>Sr No.</b></td>
				<td width="20%" class="smalltext2"><b>Employee name</b></td>
				<td width="10%" class="smalltext2"><b>FIRST LEVEL</b></td>
				<td class="smalltext2" width="5%"><b>QA</b></td>
				<td width="15%" class="smalltext2"><b>Allow Maximum Order</b></td>
				<td width="20%" class="smalltext2"><b>Stop Receiving any Emails</b></td>
				<td class="smalltext2"><b>Delete From Pdf Customers</b></td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<?php
				$k		=	0;
				foreach($a_pdfEmployees as $employeeId=>$value)
				{
					$k++;
					list($employeeName,$hasPdfEmailsAccess,$maximumOrder) =	explode("<=>",$value);
					$checkedEmailReceive	=	"";
					$stopEmailText			=	"Click to stop";
					if($hasPdfEmailsAccess  == 1)
					{
						$stopEmailText		=	"Unmark to received";
						$checkedEmailReceive=	"checked";
					}
										
					$query		=	"SELECT * FROM pdf_clients_employees WHERE employeeId=$employeeId AND customerId=$assigningCustomerId";
					$result		=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$row				=	mysqli_fetch_assoc($result);
						$assignedId			=	$row['assignedId'];
						$hasReplyAccess		=	$row['hasReplyAccess'];
						$hasQaAccess		=	$row['hasQaAccess'];
						$alreadyAssignedId	=	$assignedId;

						if($hasReplyAccess	==	1)
						{
							$checkedReplyAccess	=	"checked";
						}
						else
						{
							$checkedReplyAccess	=	"";
						}
						if($hasQaAccess	==	1)
						{
							$checkedQaAccess	=	"checked";
						}
						else
						{
							$checkedQaAccess	=	"";
						}
						if($hasReplyAccess	==	1 || $hasQaAccess	==	1)
						{
							$checkedEmployee	=	"checked";
						}
						else
						{
							$checkedEmployee	=	"";
						}
					}
					else
					{
						$checkedEmployee	=	"";
						$checkedReplyAccess	=	"";
						$checkedQaAccess	=	"";
						$alreadyAssignedId	=	0;
					}
			?>
			<tr>
				<td class="smalltext2"><?php echo $k;?>)</td>
				<td class="smalltext2">
					<input type="checkbox" name="pdfEmployeeId[<?php echo $employeeId;?>]" value="<?php echo $employeeId;?>" <?php echo $checkedEmployee;?> id="mainEmployeeId<?php echo $employeeId;?>" onclick="return checkedChild(<?php echo $employeeId;?>)">&nbsp;
					<?php echo $employeeName;?>
					<input type="hidden" name="alreadyAssign[<?php echo $employeeId;?>]" value="<?php echo $alreadyAssignedId;?>">
					<input type="hidden" name="allPdfEmployees[<?php echo $employeeId;?>]" value="<?php echo $employeeId;?>">
				</td>
				<td class="smalltext2">
					<input type="checkbox" name="hasReplyAccess[<?php echo $employeeId;?>]" value="1" <?php echo $checkedReplyAccess;?> id="child<?php echo $employeeId;?>">&nbsp;
				</td>
				<td class="smalltext2">
					<input type="checkbox" name="hasQaAccess[<?php echo $employeeId;?>]" value="1" <?php echo $checkedEmployee;?> id="childx<?php echo $employeeId;?>">&nbsp;
				</td>
				<td class="smalltext2">
					<select name="maximumOrdersAccept[<?php echo $employeeId;?>]">
						<option value="0">Unlimited</option>
						<?php
							for($i=1;$i<=15;$i++)
							{
								$select		=	"";
								if($maximumOrder == $i)
								{
									$select		=	"selected";
								}
								echo "<option value='$i' $select>$i</option>";
							}
						?>
					</select>
				</td>
				<td class="smalltext2">
					<input type="checkbox" name="hasEmailAccess[<?php echo $employeeId;?>]" value="1" <?php echo $checkedEmailReceive;?>><?php echo $stopEmailText;?>
				</td>
				<td class="smalltext2">
					<input type="checkbox" name="isDelete[<?php echo $employeeId;?>]" value="1" id="deleteEmployeeId<?php echo $employeeId;?>" onclick="return removedCheckedChild(<?php echo $employeeId;?>)"> Delete Employee From Pdf Customers
				</td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td>
					&nbsp;
				</td>
				<td colspan="4">
					<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
					<input type="hidden" name="formSubmitted1" value="1">
				</td>
			</tr>
		</table>
	</form>
	<?php
			}
		}
		else
		{
			echo "<br><center><font class='heading1'>No customer found, try some other name !!</font></center>";
		}
	}
	else
	{
		echo "<br><center><font class='heading1'>Please select a customer !!</font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>