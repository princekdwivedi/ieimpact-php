<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-site-maintanence.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT				.	"/includes/send-mail.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	 "/includes/set-variables.php");

	$employeeObj					=	new employee();
	$shiftFrom						=	"";
	$shiftTo						=	"";
	$shiftFromHrs					=	"";
	$shiftFromMinitue				=	"";
	$sfiftToHrs						=	"";
	$shiftToMinitue					=	"";
	$errorMsg						=   "";
	
	if(isset($_GET['ID']))
	{
		$employeeId					=	(int)$_GET['ID'];
		if(!empty($employeeId))
		{
			$query					=	"SELECT fullName,bypassOtp,hasOutsideLoginAccess FROM employee_details WHERE employeeId=$employeeId";
			$result					=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$bypassOtp				=	$row['bypassOtp'];
				$fullName				=	stripslashes($row['fullName']);
				$hasOutsideLoginAccess	=	$row['hasOutsideLoginAccess'];
				$updateText				=   "Currently employee allow to login outside office IP.";
				$updateText1			=   "Currently employee don't need OTP to login outside office IP.";
				$updateText2			=   "Don't allow to login outside office IP.";
				$updateText3			=   "Make need OTP to login outside office IP.";
				$updateValue			=	0;
				$updateValue1			=	0;
				if($hasOutsideLoginAccess ==  0)
				{
					$updateText			=   "Currently employee not allow to login outside office IP.";
					$updateText2		=   "Allow to login outside office IP.";
					$updateValue		=	1;
				}
				if($bypassOtp           ==  0)
				{
					$updateText1		=   "Currently employee need OTP to login outside office IP.";
					$updateText3		=   "Make no need OTP to login outside office IP.";
					$updateValue1		=	1;
				}
			}
		}
	}

	
?>
<html>
<head>
<TITLE></TITLE>
<script type="text/javascript">
	function reflectChange()
	{
		parent.location.reload();
	}
</script>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
	<body>
		<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="2" class='smalltext23'><b>Manage Outside Login For <?php echo $fullName;?></b></td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
		</table>
		<?php
			if(isset($_REQUEST['updateFormSubmit']))
			{
				extract($_REQUEST);
				if(isset($_POST['allowOutside']) || isset($_POST['bypassOtp'])){
					
					if(isset($_POST['allowOutside'])){
						$allowOutside = $_POST['allowOutside'];
						dbQuery("UPDATE employee_details SET hasOutsideLoginAccess=$allowOutside WHERE employeeId=$employeeId");
					}
					if(isset($_POST['bypassOtp'])){
						$bypassOtp = $_POST['bypassOtp'];
						dbQuery("UPDATE employee_details SET bypassOtp=$bypassOtp WHERE employeeId=$employeeId");
					}
					
					echo "<script type='text/javascript'>reflectChange();</script>";
				}
				else{
					$errorMsg	=   "Please select at least one option.";
				}
					
			}
		?>
		<br />
		<form  name='addEditOutsideLogin' method='POST' action="">
			<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<?php
					if(!empty($errorMsg)){
				?>
				<tr>
					<td class="error" colspan="3"><?php echo $errorMsg;?></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td class="smalltext23" colspan="3"><?php echo $updateText;?></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="smalltext23" colspan="3"><?php echo $updateText1;?></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="smalltext23" width="2%"><input type="checkbox" name="allowOutside" value="<?php echo $updateValue;?>"></td>
					<td class="smalltext22">
						<?php
							echo $updateText2
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="smalltext23"><input type="checkbox" name="bypassOtp" value="<?php echo $updateValue1;?>"></td>
					<td class="smalltext22">
						<?php
							echo $updateText3
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">
						<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
						<input type='hidden' name='updateFormSubmit' value='1'>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>