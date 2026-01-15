<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	$checkedId					=	0;
	$errorMsg					=	"";
	$checkedMessage				=	"";
	$orderAddress				=	"";
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_GET['checkedId']))
	{
		$checkedId				=	(int)$_GET['checkedId'];

		$query					=	"SELECT * FROM checked_customer_orders WHERE checkedId=$checkedId";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row				=	mysql_fetch_assoc($result);
			$checkedMessage		=	stripslashes($row['checkedMessage']);
			$orderId			=	$row['orderId'];

			$orderAddress		=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$orderId"),0);

			$orderAddress		=	stripslashes($orderAddress);
		}
	}
?>
<html>
<head>
<title>Edit checked message for order - <?php echo $orderAddress;?></title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
	<center>
		<script type="text/javascript">
			function validMessage()
			{
				//return true;
				form1 = document.changeMsg;
				if(form1.checkedMessage.value == "")
				{
					alert("Please enter message !!");
					form1.checkedMessage.focus();
					return false;
				}
				
			}
			function reflectChange()
			{
				window.opener.location.reload();
			}
		</script>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="textstyle1">
					<b>EDIT CHECKED MESSAGE FOR ORDER - <?php echo $orderAddress;?></b>
				</td>
			</tr>
		</table>
		<?php 
			if(isset($_REQUEST['formSubmitted']))
			{
				extract($_REQUEST);
				$checkedMessage	=	trim($checkedMessage);
				$checkedMessage	=	makeDBSafe($checkedMessage);
				if(empty($checkedMessage))
				{
					$errorMsg				=	"Please enter message !!";
				}
				if(empty($errorMsg))
				{
					dbQuery("UPDATE checked_customer_orders SET checkedMessage='$checkedMessage' WHERE checkedId=$checkedId");

					echo "<script type='text/javascript'>reflectChange();</script>";

					echo "<script>window.close();</script>";
				}
			}
		?>
		<form name="changeMsg" action="" method="POST" enctype="multipart/form-data" onsubmit="return validMessage();">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="error" colspan="3">
						<b><?php echo $errorMsg;?></b>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<textarea name="checkedMessage" cols="65" rows="15" style="border: 2px solid #333333"><?php echo $checkedMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td height="8"></td>
				</tr>
				<tr>
					<td align="center" colspan="3">
						<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
						<input type='hidden' name='formSubmitted' value='1'>
					</td>
				</tr>
			</table>
		</form>
		<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
	</center>
</body>
</html>