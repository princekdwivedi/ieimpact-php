<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$customerId					=	0;
	$parentId					=	0;
	$customerName				=	"";
	$t_customerName				=	"";
	$departmentId				=	1;
	$departmentText				=	"";
	$platfromName				=	"";
	$text						=	"Add New Client";
	$text1						=	"";
	$showForm					=	false;
	if(isset($_GET['parentId']))
	{
		$parentId				=	(int)$_GET['parentId'];
		if(!empty($parentId))
		{
			if($platfromName	=	$employeeObj->getPlatformName($parentId))
			{
				$showForm		=	true;
				$text1			=	" In Platform ".$platfromName;
			}
		}
	}
?>
<form name="selectParent" action="" method="GET">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' width="15%">Platform</td>
		<td width="2%" class="title">:</td>
		<td>
			<select name="parentId" onchange="document.selectParent.submit()">
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getAllPlatform())
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_parentId		=	$row['platfromId'];
							$t_parentName	=	$row['name'];

							$select		 =	"";
							if($t_parentId == $parentId)
							{
								$select	 =	"selected";
							}
							echo "<option value='$t_parentId' $select>$t_parentName</option>";
						}
					}
				?>
			</select>
		</td>
	</tr>
</table>
</form>

<?php
	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-edit-customer.php";
	

	if(isset($_GET['ID']))
	{
		$customerId		=	(int)$_GET['ID'];
		$query			=	"SELECT * FROM platform_clients WHERE parentId=$parentId AND customerId=$customerId";
		$result	=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$customerName	=	$row['name'];
			$departmentId	=	$row['departmentId'];
			$t_customerName	=	$customerName;
			$departmentText	=	$a_department[$departmentId];
			$text			=	"Edit Client Name";
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	if($showForm)
	{
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$customerName	=	trim($customerName);

			$validator ->checkField($customerName,"","Please Enter Client Name !!");
			if(!empty($customerName) && $customerName != $t_customerName)
			{
				if($result	=	$employeeObj->getExistingCustomer($customerName,$parentId))
				{
					$validator ->setError("This customer is exists for  the platform !!");
				}
			}
			$dataValid	 =	$validator ->isDataValid();
			if($dataValid)
			{
				$departmentId	=	@mysql_result(mysql_query("SELECT departmentId FROM platform_clients WHERE platfromId=$parentId AND parentId=0"),0);

				if(empty($customerId))
				{
					$t_customerId		=	@mysql_result(mysql_query("SELECT customerId FROM platform_clients WHERE parentId=$parentId AND customerId <> 0 ORDER BY customerId DESC LIMIT 1 "),0);
					if(empty($t_customerId))
					{
						$t_customerId	=	1;
					}
					else
					{
						$t_customerId	=	$t_customerId+1;
					}
					$query	=	"INSERT INTO platform_clients SET name='$customerName',departmentId=$departmentId,customerId=$t_customerId,parentId=$parentId,addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."'";
					mysql_query($query);
				}
				else
				{
					$query	=	"UPDATE platform_clients SET name='$customerName' WHERE customerId=$customerId AND parentId=$parentId";
					mysql_query($query);
				}
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES ."/add-edit-clients.php?parentId=$parentId");
				exit();
			}
			else
			{
				echo $errorMsg	 =	$validator ->getErrors();
				include($form);
			}
		}
		else
		{
			include($form);
		}
	}
	else
	{
		echo "<br><br><center><font class='error'><b>Please Select A Paltform</b></font></center><br><br><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>