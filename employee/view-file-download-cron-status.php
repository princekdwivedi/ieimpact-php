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
	include(SITE_ROOT_EMPLOYEES	.   "/classes/orders.php");
	$orderObj					=   new orders();
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	
	if(empty($s_hasManagerAccess) || empty($s_hasAdminAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

?>
	<table width="98%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td colspan="10" class="heading">CHECK LATEST FTP STATUS</td>
		</tr>
		<tr>
			<td width="5%" class="smalltext23"><b>SR NO.</b></td>
			<td width="15%" class="smalltext23"><b>DATE(EST)</b></td>	
			<td width="15%" class="smalltext23"><b>CUSTOMER NAME</b></td>
			<td width="30%" class="smalltext23"><b>ORDER ADDRESS</b></td>
			<td width="30%" class="smalltext23"><b>FILE NAME</b></td>
			<td class="smalltext23"><b>STATUS</b></td>	
		</tr>
		<tr>
			<td colspan="8"><hr size="1" width="100%" color="#bebebe"></td>
		</tr>
		<?php
			$count 	=	0;
			$query	=	"SELECT cron_download_mohali_server_status.*,completeName,uploadingFileName,uploadingFileExt FROM cron_download_mohali_server_status INNER JOIN members ON cron_download_mohali_server_status.memberId=members.memberId INNER JOIN order_all_files ON cron_download_mohali_server_status.fileId=order_all_files.fileId ORDER BY cron_download_mohali_server_status.fileId DESC";
			$result = dbQuery($query);
			if(mysqli_num_rows($result)){
				while($row = mysqli_fetch_assoc($result)){
				$count++;
				$estDate 	=	 showDate($row['estDate']);
				$estTime 	=	 $row['estTime'];
				$status 	=	 $row['status'];
				$memberId   =    $row['memberId'];
				$orderId    =    $row['orderId'];
				$completeName    =    stripslashes($row['completeName']);
				$orderAddress    =    getSubstring(stripslashes($row['orderAdderss']),50);


				$errorText  =    "<font color='red'>Not Downloaded</font>";
				if($status  == 1){
					$errorText  =    "<font color='green'>Downloaded</fonnt>";
				}
		?>
		<tr>				
			<td class="smalltext2"><?php echo $count;?>)</td>
			<td class="smalltext2"><?php echo $estDate."/".showTimeFormat($estTime);;?></td>
			<td class="smalltext2">
				<?php 
					echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$memberId' class='link_style16' style='cursor:pointer;'>$completeName</a>";
				?>
			</td>
			<td class="smalltext2">
				<?php 
					echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$memberId' class='link_style12'>$orderAddress</a>";
				?>
			</td>
			<td class="smalltext2"><?php echo getSubstring(stripslashes($row['uploadingFileName']),40).".".stripslashes($row['uploadingFileExt']);?></td>
			<td class="smalltext23"><?php echo $errorText;?></td>
		</tr>
		<tr>
			<td colspan="5"></td>
		</tr>
		<?php
			}
		}
		?>	
	</table>
	
<?php

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>