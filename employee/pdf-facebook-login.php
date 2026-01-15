<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");

	include(SITE_ROOT_EMPLOYEES .	"/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES .	"/includes/common-array.php");
	include(SITE_ROOT			.   "/classes/email-templates.php");
	$emailObj					=	new emails();
	$errorMsg					=	"";
	$errorMsgWithHelp    		=	"";
	$facebook_login_error 		=	0;


	if(isset($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
	}

	if(isset($_SESSION['mtemployeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_MTEMPLOYEES);
		exit();
	}

	if(!isset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/pdf-login.php");
		exit();
	}
	else{
		$verifiedEmployeeId 	=	$_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID'];
		$query 					=	"SELECT employeeId,fullName,isActive,isManager,hasPdfAccess,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,password,facebookId FROM employee_details  WHERE employeeId=$verifiedEmployeeId AND hasPdfAccess=1 AND isActive=1";
		$result					      =	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					  =	mysqli_fetch_assoc($result);
			$employeeId				  =	$row['employeeId'];
			$fullName				  =	stripslashes($row['fullName']);
			$isActive				  =	$row['isActive'];
			$dbPassword				  =	$row['password'];
			$isManager				  =	$row['isManager'];
			$hasPdfAccess			  =	$row['hasPdfAccess'];
			$isOutsideCountryEmployee =	$row['isOutsideCountryEmployee'];
			$shiftType				  =	$row['shiftType'];
			$hasAllQaAccess			  =	$row['hasAllQaAccess'];
			$isLocked				  =	$row['isLocked'];
			$triedFailCount			  =	$row['triedFailCount'];
			$lockedDate				  =	$row['lockedDate'];
			$lockedTime				  =	$row['lockedTime'];
			$lockedFromIP			  =	$row['lockedFromIP'];
			$hasverificationAccess	  =	$row['hasverificationAccess'];
			$showQuestionnaire		  = $row['showQuestionnaire'];
			$facebookId		          = $row['facebookId'];

			include_once(SITE_ROOT	    .   "/Facebook/autoload.php");
			/*$fb 		                =   new Facebook\Facebook(array(
			    'app_id'                => '307211674239541', // Replace with your app id
			    'app_secret'            => 'fe4da22856bbf260082beb2a89160dab',  // Replace with your app secret
			    'default_graph_version' => 'v3.2',
			));*/
      
			$fb 		                =   new Facebook\Facebook(array(
			    'app_id'                => '355824524526802', // Replace with your app id
			    'app_secret'            => 'f3f77e67020aea5379ae18c1a2591fc6',  // Replace with your app secret
			    'default_graph_version' => 'v3.2',
			));
			 
			$helper 			        =  $fb->getRedirectLoginHelper();

			$facebook_permissions       =  array('scope' => 'email'); // Optional permissions

			$facebook_login_url         =  $helper->getLoginUrl('https://secure.ieimpact.com/employee/login.php', $facebook_permissions);

		}
		else{
			unset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']);

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/pdf-login.php");
			exit();
		}
	}

	if(isset($_SESSION['facebook_login_error']))
	{
		$facebook_login_error = $_SESSION['facebook_login_error'];
		if(!is_numeric($facebook_login_error)){
			$errorMsg			=	$facebook_login_error;
		}
		else{			
			$errorMsgWithHelp   =	$facebook_login_error;
		}
		unset($_SESSION['facebook_login_error']);

		//////////////////// SENDING EMAIL WITH ERROR DETAILS IF GETTING FACEBOOK ERROR AND LOGIN INTO EMPLOYEE AREA////////
		/*if(!is_numeric($facebook_login_error)){
			///////////////////////////////////// LOGIN INTO EMPLOYEE AREA WITH AN ERROR FROM FACEBOOK ////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////
			$_SESSION['employeeId']   =	$verifiedEmployeeId;
			$_SESSION['employeeName'] =	$fullName;
			$_SESSION['employeeEmail']=	$email;
			if($shiftType	==	2)
			{
				$_SESSION['isNightShiftEmployee']	=	1;
			}
			if($isManager	== 1)
			{
				$_SESSION['hasManagerAccess'] =	$isManager;
			}
			
			$_SESSION['hasPdfAccess'] =	1;
			
			if(!empty($hasAllQaAccess))
			{
				$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
			}
			if(!empty($hasverificationAccess))
			{
				$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
			}
			$_SESSION['departmentId'] =	2;
			$_SESSION['showQuestionnaire']        =	$showQuestionnaire;
			
			dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0,lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$verifiedEmployeeId");			

			$end_time_stamp         =    time();
            $time_taken             =    $end_time_stamp-$_SESSION['FACEBOOK_LOGIN_PROCESSED_ON'];

            unset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']);
            unset($_SESSION['FACEBOOK_LOGIN_PROCESSED_ON']);

			dbQuery("INSERT INTO employee_login_track SET employeeId=$verifiedEmployeeId,loginDate='".CURRENT_DATE_INDIA."',loginTime='".CURRENT_TIME_INDIA."',loginIP='$employeeLoginFromIP',start_time_stamp='$start_time_stamp',end_time_stamp='$end_time_stamp',time_taken='$time_taken'");

			$employeeLoginSessionTrackId			   = mysqli_insert_id($db_conn);

			$redirectToEmployee  = SITE_URL_EMPLOYEES."/employee-details.php";


			$_SESSION['employeeLoginSessionTrackId']   =	$employeeLoginSessionTrackId;

			if($passwordChangeOn	==	"0000-00-00")
			{
				$_SESSION['forceResetPassword']	=	1;

				$redirectToEmployee  = SITE_URL_EMPLOYEES."/change-password.php";
			}
			elseif($passwordChangeOn	!=	"0000-00-00")
			{
				$fixedSixtyDaysOldDate		=	getPreviousGivenDate(CURRENT_DATE_INDIA,60);
				if($fixedSixtyDaysOldDate   >   $passwordChangeOn)
				{
					$_SESSION['forceResetPassword']	=	1;
				
					$redirectToEmployee  = SITE_URL_EMPLOYEES."/change-password.php";
				}
				else
				{					

					$redirectToEmployee  = SITE_URL_EMPLOYEES."/employee-details.php";
				}
			}

			////////////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////
			$body			  =	 "<table width='98%' align='center' cellpadding='4' cellspacing='4' border='0'><tr><td align='left' colspan='3'><font size='3px' face='verdana' color='#387070'>".$fullName." login into employee area with a Facebook error on ".showDate($nowDateIndia)." at ".$nowTimeIndia." IST</font></td></tr>";

			$body			 .=	"<tr><td width='20%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'><b>Error</b> </font></td><td width='4%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'><b>:</b></font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".nl2br($errorMsg)."</font></td></tr>";
			
			$body			 .=	"<tr><td width='20%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'><b>IP Addreess</b> </font></td><td width='4%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'><b>:</b></font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".VISITOR_IP_ADDRESS."</font></td></tr>";

			$body			 .=	"</table>";

			$managerEmployeeFromName     =  "ieIMPACT";
			$managerEmployeeEmailSubject =  $fullName." login with a Facebook error";
			$a_templateData		         =	array("{bodyMatter}"=>$body);
			$toEmail			         =	"hemant.jindal@gmail.com"; 
			$managerEmployeeFromBcc		 =  "gaurabsiva1@gmail.com";
			$uniqueTemplateName	         =	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
			/////////////////////////////////////////////////////////////////////////////////////
			ob_clean();
			header("Location: ".$redirectToEmployee);
			exit();
		}*/

	}

?>
<table width="60%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td height="30">&nbsp;</td>
	</tr>
	<?php
		if(!empty($errorMsg)){
	?>
	<tr>
		<td colspan="3" class="error"><b><?php echo $errorMsg;?></b></td>
	</tr>
	<?php	
		}
		elseif(!empty($errorMsgWithHelp)){
	?>
	<tr>
		<td colspan="3" class="error"><b>We are not able to get your emaill address from Facebook. Please change your Facebook setting. Here are some options how to change it. 1) <a href="https://developers.facebook.com/docs/facebook-login/permissions/overview/" target="_blank" class="link_style14">Option One</a>&nbsp;&nbsp; 2) <a href="https://www.imore.com/how-to-revoke-facebook-app-permissions" target="_blank" class="link_style14">Option Two</a></b></td>
	</tr>
	<?php
		}		
	?>
	<tr>
		<td colspan="3" class="textstyle3" style="text-align:center"><b>Welcome <?php echo $fullName;?> to ieIMPACT PDF Employee Secton.</b></td>
	</tr>
	<tr>
		<td colspan="3" class="smalltext24" style="text-align:center">Click the below button to complete the login process with your Facebook account.</td>
	</tr>
	<tr>
		<td height="10">&nbsp;</td>
	</tr>
	<tr>
		<td style="text-align:center">
			<a href="<?php echo htmlspecialchars($facebook_login_url); ?>"><img SRC="<?php echo SITE_URL;?>/images/facebook-login-button.jpg" WIDTH="400" BORDER="0" ALT="" title="Sign in with Facebook"></a>
		</td>
	</tr>
	<tr>
		<td height="10">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="3" class="smalltext22" style="text-align:center">[<b><u>Note</u><font color="#ff0000;">*</font>:</b> You cannot change your Facebook account details in future.]</td>
	</tr>
	<tr>
		<td height="150">&nbsp;</td>
	</tr>
</table>
<?php	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>