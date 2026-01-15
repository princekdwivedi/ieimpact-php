<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	include(SITE_ROOT			.   "/classes/pagingclass.php");
	$pagingObj		            =	new Paging();
	$employeeObj	            =	new employee();
	$currentDate 	            =	CURRENT_DATE_INDIA;
	$currentDateTime            =	CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA;
	$yesterdayDate              =   date('Y-m-d', strtotime("-1 day", strtotime($currentDate)));

	$oneHrBeforeTime 			=	date('Y-m-d H:i:s', strtotime("-1 hours", strtotime($currentDateTime)));

	$twoHrBeforeTime 			=	date('Y-m-d H:i:s', strtotime("-2 hours", strtotime($currentDateTime)));

	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");

	/*if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/

	$pdfEmployees				              =	$employeeObj->getAllPdfEmployees();
	$all_pdfEmployees			              =	array();
	$all_pdfEmployees_ids		              =	array();
	while($row 					              =	mysqli_fetch_assoc($pdfEmployees)){
		$t_employeeId                         =  $row['employeeId'];
		$all_pdfEmployees[$t_employeeId]      =  stripslashes($row['firstName']." ".$row['lastName']);
		$all_pdfEmployees_ids[$t_employeeId]  = $t_employeeId;       
	}
	$all_pdfEmployees_ids                     = implode(",",$all_pdfEmployees_ids);

	$existing_ip_alias			=	array();	
	$query						=	"SELECT * FROM office_ip_addresses_list";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result)){
			$ipAddress			=	stripslashes($row['ipAddress']);
			$aliasName			=	stripslashes($row['aliasName']);			
			if(!empty($aliasName)){
				$existing_ip_alias[$ipAddress] =	$aliasName;
			}
		}
	}

	/*$text = "EMPLOYEE IP SPECIFIC LOGIN DETAIL ON ".showDate($currentDate)." (<a href='".SITE_URL_EMPLOYEES."/employee-login-ip-specific.php?hour=1' class='link_style10'>Last One Hour</a>) &nbsp; (<a href='".SITE_URL_EMPLOYEES."/employee-login-ip-specific.php?hour=2' class='link_style10'>Last Two Hours</a>)";

	$operationClause    =	" AND loginDate='".$currentDate."'";*/

	$text = "SERVER USES BY EMPLOYEES LAST 1 HR";

	$operationClause    =	" AND concat(loginDate,' ',loginTime) >= '".$oneHrBeforeTime."'";

	if(isset($_GET['hour'])){
		$hour           =	$_GET['hour'];
		if($hour 		==	1){
			
			$text = "EMPLOYEE IP SPECIFIC LOGIN DETAIL FOR LAST TWO HOURS (<a href='".SITE_URL_EMPLOYEES."/employee-login-ip-specific.php' class='link_style10'>Last One Hour</a>)";
			$operationClause    =	" AND concat(loginDate,' ',loginTime) >= '".$twoHrBeforeTime."'";
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<link href="<?php echo SITE_URL;?>/css/thickbox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_URL;?>/script/thickbox-big.js" type="text/javascript"></script>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3" colspan="5"><b><font color="#ff0000"><?php echo $text;?></font></b></td>
	</tr>
	<tr>
		<td class="title1" colspan="5" height="10">&nbsp;</td>
	</tr>
    <tr>
		<td width="12%" class="smalltext23" valign="top">IP</td>
		<td width="8%" class="smalltext23" valign="top">Alias Name</td>
		<td width="4%" class="smalltext23" valign="top">Total</td>
		<td class="smalltext23" valign="top">Employees</td>
	</tr>
	<tr>
		<td colspan="7">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
<?php
	$list_of_ips_employee  =   array();
	$employee_logged_in    =   array();


	$query				   =   "SELECT * FROM track_employee_active_on_website WHERE CONCAT(activeDate,' ',activeTime) >= '$oneHrBeforeTime' ORDER BY trackId DESC";
	$result 			   =   dbQuery($query);
	if(mysqli_num_rows($result)){
		while($row 			=	mysqli_fetch_assoc($result)){
			$employeeId     =	$row['employeeId'];
			$loginIP 		=	$row['employeeIP'];
			$loginDate 		=	$row['activeDate'];
			$loginTime 		=	$row['activeTime'];
			$employeeName	=	$all_pdfEmployees[$employeeId];

			
			if(!in_array($employeeId,$employee_logged_in)){
				if(array_key_exists($loginIP,$list_of_ips_employee)){
				
					$second_array 	=	$list_of_ips_employee[$loginIP];
					//pr($second_array);
					if(!in_array($employeeId,$second_array)){
						$list_of_ips_employee[$loginIP][$employeeId] = $employeeName."<=>".$loginDate."<=>".$loginTime;
					}
				}
				else{
	                $list_of_ips_employee[$loginIP][$employeeId]    = $employeeName."<=>".$loginDate."<=>".$loginTime;
				}

				$employee_logged_in[] = $employeeId;
			}

		}

	}
	//pr($list_of_ips_employee);
	if(!empty($list_of_ips_employee) && count($list_of_ips_employee) > 0){
		foreach($list_of_ips_employee as $t_loginIp => $employee_data){
			$aliasName 		  =	"N/A";
			if(!empty($t_loginIp) && array_key_exists($t_loginIp,$existing_ip_alias)){
				$aliasName    =	$existing_ip_alias[$t_loginIp];
			}
			
	?>
	<tr>
		<td class='smalltext22' valign='top'>
			<b><?php echo $t_loginIp;?></b>
		</td>
		<td class='smalltext22' valign='top'>
			<b><?php echo $aliasName;?></b>
		</td>
		<td class='smalltext22' valign='top'>
			<b><?php echo count($employee_data);?></b>
		</td>
		<td  valign='top'>
			<?php
				$count		=	0;
				$count1 	=	0;
				if(!empty($employee_data) && count($employee_data) > 0)	{
			?>
			<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<?php
						foreach($employee_data as $employeeId=>$value){

							list($employeeName,$t_loginDate,$t_loginTime) = explode("<=>",$value);
							$count++;
							$count1++;

					?>
					<td width='25%' class='smalltext22'>
						<?php echo $count1.")"?>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/employee-last-ten-login.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=400&width=400" title='' class='thickbox'/><font class='link_style6'><?php echo getSubstring($employeeName,15);?></font></a>&nbsp;(<?php echo getHoursBetweenDates($t_loginDate,CURRENT_DATE_INDIA,$t_loginTime,CURRENT_TIME_INDIA);?>)
					</td>
					<?php
							if($count == 4){
								$count= 0;
								echo "</tr><tr>";
							}
						}
						if($count1 < 4){
							for($i=$count1;$i<=4;$i++){
								echo "<td>&nbsp;</td>";
							}
						}
					?>
				</tr>
			</table>
			<?php
				}
				else{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan='7'><hr size='1' size='100%' color='#bebebe'></td>
    </tr>
	<?php
	    }
	}
	else
	{
		echo "<tr><td colspan='5' style='text-align:center;' class='error' height='200'><b>No Logged In Found !!</b></td></tr>";
	}
echo "</table>";
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>