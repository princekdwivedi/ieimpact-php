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
	include(SITE_ROOT			. "/classes/email-templates.php");
	$emailObj					= new emails();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$searchCustomer				=	"";
	$t_searchCustomer			=	"";

	if(isset($_GET['searchCustomer'])){
		$searchCustomer		    =	trim($_GET['searchCustomer']);
		if(!empty($searchCustomer)){
			$t_searchCustomer	=	$searchCustomer;

			if(isset($_GET['sendEmail']) && $_GET['sendEmail'] == 1 && isset($_GET['memberId'])){
				$memberId		    =	trim($_GET['memberId']);
				if(!empty($memberId)){
					$query	=	"SELECT completeName,email,phone,directLoginCode,directLoginExpired FROM members WHERE memberId=$memberId";
					$result =	dbQuery($query);
					if(mysqli_num_rows($result)){
						$row				=	mysqli_fetch_assoc($result);
						$completeName		=	stripslashes($row['completeName']);
						$email				=	$row['email'];
						$directLoginCode    =	$row['directLoginCode'];
						$directLoginExpired =	$row['directLoginExpired'];
						$currentDateTime	=	$nowDateIndia." ".$nowTimeIndia;
						
						$createLink	    =	1;

						if(!empty($directLoginCode) && $directLoginExpired > $currentDateTime){
							$createLink	=	0;
						}

						if($createLink == 1){
							$fifteenMinutePlusTime	=	date('Y-m-d H:i:s', strtotime("+15 minutes", strtotime($currentDateTime)));

							$directLoginCode  = md5($memberId).rand('aaaaa', 'zzzzz').substr(md5(microtime()+rand()+date('s')),0,40);


							dbQuery("UPDATE members SET directLoginCode='$directLoginCode',directLoginExpired='$fifteenMinutePlusTime' WHERE memberId=$memberId");
						}
						try{
							////////////////////// EMAIL PART //////////////////////////////////////
							$directLoginLink			=	SITE_URL_MEMBERS."/direct-website-login.php?".ID_M_D_5."=".$directLoginCode;
							$a_templateData				=	array("{membername}"=>$completeName, "{directLoginLink}"=>$directLoginLink);
							$uniqueTemplateName			=	"TEMPLATE_SENDING_CUSTOMER_DIRECT_LINK_LOGIN";			
							$toEmail					=	$email;
							$setThisEmailReplyToo	    =	"john@ieimpact.com";
                            $setThisEmailReplyTooName   =	"John";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
							//die("KASE1");
							$_SESSION['successLinkSent']=	$completeName;
						}
						catch(Exception $e){
							//$error = $e->getMessage();
							//die($error);
						}
						
						 
						
					}
				}
				/////////////////////////////////////////////////
				ob_clean();
				header("Location:".SITE_URL_EMPLOYEES."/sending-customer-direct-login.php?searchCustomer=".$searchCustomer);
				exit();
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
	
	$("#customer").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 270,selectFirst: false});
});
function resendLink(searchCustomer,memberId)
{

	var confirmation = window.confirm("Are you sure to send direct login link?");
	
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/sending-customer-direct-login.php?memberId="+memberId+"&searchCustomer="+searchCustomer+"&sendEmail=1";
	}
}

</script>
<form name="searchDeleteOrderForm" action=""  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="40%" class="nextText">SEARCH CUSTOMER TO SENDING WEBISTE DIRECT LOGIN :</td>
			<td width="20%" class="nextText">
				<input type='text' name="searchCustomer" size="31" value="<?php echo $t_searchCustomer;?>" id="customer"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<br />
<table width="30%" align="left" border="0" cellpadding="4" cellspacing="4">
<?php
	if(isset($_SESSION['successLinkSent'])){
?>
<tr>
	<td colspan="5" class="error">
		Successfully sent to - <?php echo $_SESSION['successLinkSent'];?>
	</td>
</tr>
<?php	
		unset($_SESSION['successLinkSent']);
	}
	if(!empty($searchCustomer)){
		$searchCustomer	=	makeDBSafe($searchCustomer);
		$query		    =	"SELECT memberId,completeName,firstName,lastName FROM members WHERE completeName LIKE '%".$searchCustomer."%'";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result)){
			$count		=	0;
			while($row		    =	mysqli_fetch_assoc($result)){
				$count++;
				$memberId       =   $row['memberId'];
				$firstName		=	stripslashes($row['firstName']);
			    $lastName		=	stripslashes($row['lastName']);
				$completeName   =   $firstName." ".substr($lastName, 0, 1);
	?>
	<tr>
		<td width="8%">&nbsp;<?php echo $count;?>)</td>
		<td width="40%">&nbsp;<?php echo $completeName;?></td>
		<td>&nbsp;<a onclick="resendLink('<?php echo $completeName;?>',<?php echo $memberId;?>)" class="link_style26" style="cursor:pointer;"><u>Send Direct Login Email</u></a></td>
	</tr>
	<?php
			}
		}
		else{
			echo "<tr><td height='200'></td></tr>";
		}

   }
   else{
		echo "<tr><td height='200'></td></tr>";
	}
   echo "</table>";
	

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>