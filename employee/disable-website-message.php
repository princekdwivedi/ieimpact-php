<?php
	session_start();
	ob_start();
	error_reporting(E_ALL);
	include("../root.php");

	$query		=	"SELECT * FROM disable_website WHERE isActive=1 AND (disableEmployee=1 OR disableEmployeeCustomer=1)";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row				=	mysqli_fetch_assoc($result);
		$displayMessage		=	stripslashes($row['displayMessage']);
		$fromDate			=	showDate($row['desabledFrom']);
		$fromTime			=	date("H:i",strtotime($row['desabledFromTime']));

		$siteUrl			=	SITE_URL;
		$displayWesbiteName	=	stringReplace("https://", "", $siteUrl);
?>
<html>
	<head>
		<title><?php echo $displayWesbiteName;?> is under mainatanence</title>
		<LINK REL="SHORTCUT ICON" HREF="<?php echo SITE_URL;?>/favicon.ico">
	</head>
	<body bgcolor='#ffffff'>
		<div style='margin:5% auto;width:900px;'>
			<table cellpadding='4' cellspacing='0' width='99%'>
				<tr>
					<td align='center'><img src='https://secure.ieimpact.com/images/templatemo_logo.png' alt="ieIMPACT"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td align='center'>
						<font style='font-family:verdana;font-size:19px;color:#333333;'>
							<b><?php echo nl2br($displayMessage);?></b>
						</font>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td align='center'><img src='<?php echo SITE_URL;?>/images/under-maintenance.gif'></td>
				</tr>
			</table>
		</div>
	</body>
</html>
<?php
	}
	else
	{

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

?>