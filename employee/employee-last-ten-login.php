<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-pdf-login.php");	
	include(SITE_ROOT_EMPLOYEES		. "/classes/employee.php");
	include(SITE_ROOT				. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/common-array.php");	
	include(SITE_ROOT				. "/includes/send-mail.php");
	$employeeObj					= new employee();
	$employeeId						= 0;
	$errorMsg						= "";

	/*if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/

	if(isset($_GET['ID']))
	{
		$employeeId					=	$_GET['ID'];
		$query						=	"SELECT * FROM employee_details WHERE employeeId=$employeeId AND isActive=1";
		$result						=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$fullName				=	stripslashes($row['fullName']);
			$email					=	stripslashes($row['email']);
		}
	}

	$existing_ip_alias			   =	array();	
	$query						   =	"SELECT * FROM office_ip_addresses_list";
	$result						   =	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				   =	mysqli_fetch_assoc($result)){
			$ipAddress			   =	stripslashes($row['ipAddress']);
			$aliasName			   =	stripslashes($row['aliasName']);			
			if(!empty($aliasName)){
				$existing_ip_alias[$ipAddress] =	$aliasName;
			}
		}
	}
?>
<html>
<head>
<TITLE></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<script type="text/javascript">
	function reflectChange()
	{
		parent.location.reload();
	}
</script>
</head>
	<body>
			<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
				<tr>
					<td colspan="4" class="smalltext23"><b>View Last 10 login For - <?php echo $fullName;?></b></td>
				</tr>
				<tr>
					<td width="25%" class="smalltext23" valign="top">IP</td>
					<td width="25%" class="smalltext23" valign="top">Alias Name</td>
					<td class="smalltext23" valign="top">Login Date/Time</td>
				</tr>
				<tr>
					<td colspan="7">
						<hr size="1" width="100%" color="#e4e4e4">
					</td>
				</tr>
				<?php
					$query				   =   "SELECT loginIP,loginDate,loginTime FROM employee_login_track  WHERE employeeId=$employeeId ORDER BY trackId DESC LIMIT 10";
					$result 			   =   dbQuery($query);
					if(mysqli_num_rows($result)){
						while($row 			=	mysqli_fetch_assoc($result)){
							$loginDate      =	$row['loginDate'];
							$loginTime      =	$row['loginTime'];
							$t_loginIp 		=	$row['loginIP'];

							$aliasName 		=	"N/A";
							if(!empty($t_loginIp) && array_key_exists($t_loginIp,$existing_ip_alias)){
								$aliasName  =	$existing_ip_alias[$t_loginIp];
							}
					?>
					<tr>
						<td class='smalltext22' valign='top'>
							<?php echo $t_loginIp;?>
						</td>
						<td class='smalltext22' valign='top'>
							<?php echo $aliasName;?>
						</td>
						<td class='smalltext22' valign='top'>
							<?php echo getHoursBetweenDates($loginDate,CURRENT_DATE_INDIA,$loginTime,CURRENT_TIME_INDIA);?>
						</td>
					</tr>
					<?php
						}
					}
					else{
				?>
				<tr>
					<td colspan="7" class="error">
						Sorry No rcord found
					</td>
				</tr>
				<?php
					}
				?>				
			</table>
		</form>
	</body>
</html>