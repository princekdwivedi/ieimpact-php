<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .	"/includes/top.php");
	$diplayData 				=	false;
	$M_D_5_ORDERID				=	ORDERID_M_D_5;
	$M_D_5_ID					=	ID_M_D_5;
	$encodeID 					=	"";
	if(isset($_GET[$M_D_5_ORDERID]))
	{
		$encodeID		        =	$_GET[$M_D_5_ORDERID];
	}
	$showCaptcha				=	1;
	$errorMsg					=	"";
	$currentDateTime            =	CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA;
	$oneHrBeforeTime 			=	date('Y-m-d H:i:s', strtotime("-1 hours", strtotime($currentDateTime)));

	if(isset($_SESSION['showResult']) && $_SESSION['showResult'] == 1){
		$showCaptcha		    =	0;
		unset($_SESSION['showResult']);
	}

	function getCurlData($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
		$curlData = curl_exec($curl);
		curl_close($curl);
		return $curlData;
	}

	if(!empty($encodeID) && $encodeID == $M_D_5_ID){
		if($showCaptcha	==	1){
			if(isset($_REQUEST['formsubmitted']))
			{
				extract($_REQUEST);
				if(isset($_POST['g-recaptcha-response']))
				{			
					$recaptcha  =  $_POST['g-recaptcha-response'];

					$google_url = "https://www.google.com/recaptcha/api/siteverify";
					
					$verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=".GOOGLE_RECAPTCHA_SECRET."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR'];
					$resp  =  getCurlData($verify_url);
					$res   =  json_decode($resp, TRUE);
										
					if(!$res['success'])
					{
						
						$errorMsg 	=	"Please marked the security captcha.";
					}
					else{
						$_SESSION['showResult'] = 1;
						$showCaptcha = 0;
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/server-usage.php?".$M_D_5_ORDERID."=".$M_D_5_ID);
						exit();
					}
				}
			}
	?>
		<form name="validateUser" action="" method="POST">
			<table width="30%" align="center" border="0">
				<?php
					if(!empty($errorMsg)){
						echo "<tr><td colspan='2' class='error2'><b>".$errorMsg."</b></td></tr>";
					}
				?>
				<tr>
					<td align="right" with="50%">
						<div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_RECAPTCHA_SECRET_KEY; ?>"></div>
						<script type="text/javascript"
								src="https://www.google.com/recaptcha/api.js?hl=en">
						</script>
					</td>
					<td>
						<input type="image" name="login" SRC="<?php echo SITE_URL;?>/images/submit.jpg" BORDER="0" ALT="">
						<input type="hidden" name="formsubmitted" value ="1">&nbsp;
					</td>
				</tr>
			</table>
		</form>			
	<?php
		}
		else{
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
		?>
		<table width="40%%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
			<tr>
				<td class="textstyle3" colspan="5"><b><font color="#ff0000">SERVER USAGE</font></b></td>
			</tr>
			<tr>
				<td class="title1" colspan="5" height="10">&nbsp;</td>
			</tr>
		    <tr>
				<td width="35%" class="smalltext23" valign="top">Alias Name</td>
				<td width="35%" class="smalltext23" valign="top">Total</td>
				<td class="smalltext23" valign="top">Percentage</td>
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
					
					if(!in_array($employeeId,$employee_logged_in)){
						if(array_key_exists($loginIP,$list_of_ips_employee)){
						
							$second_array 	=	$list_of_ips_employee[$loginIP];
							//pr($second_array);
							if(!in_array($employeeId,$second_array)){
								$list_of_ips_employee[$loginIP][$employeeId] = $employeeId;
							}
						}
						else{
			                $list_of_ips_employee[$loginIP][$employeeId]    = $employeeId;
						}

						$employee_logged_in[] = $employeeId;
					}

				}

			}

			//pr($list_of_ips_employee);
			if(!empty($list_of_ips_employee) && count($list_of_ips_employee) > 0){
				$totalEmployeeLoggedIn=	count($employee_logged_in);
				foreach($list_of_ips_employee as $t_loginIp => $employee_data){
					$aliasName 		  =	"N/A";
					if(!empty($t_loginIp) && array_key_exists($t_loginIp,$existing_ip_alias)){
						$aliasName    =	$existing_ip_alias[$t_loginIp];
					}

					$total 			  =  count($employee_data);
					$percentage 	  =  $total/$totalEmployeeLoggedIn;
					$percentage       =  $percentage*100;
					$percentage       =  round($percentage,2);

					
			?>
			<tr>
				<td class='smalltext22' valign='top'>
					<b><?php echo $aliasName;?></b>
				</td>
				<td class='smalltext22' valign='top'>
					<b><?php echo $total;?></b>
				</td>	
				<td class='smalltext22' valign='top'>
					<b><?php echo $percentage;?></b>
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
				echo "<tr><td colspan='5' style='text-align:center;' class='error' height='200'><b>No Record Found !!</b></td></tr>";
			}
			echo "</table>";
		}

		
	}
	else{
		echo "KASE1";
		/*ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();*/
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>