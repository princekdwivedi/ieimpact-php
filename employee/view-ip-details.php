<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$ip			=	"";

	if(isset($_GET['ip']))
	{
		$ip		=	$_GET['ip'];

		$registeredIp					=	$ip;
		$registeredIpCountry			=	"";
		$registeredIpRegion				=	"";
		$registeredIpCity				=	"";
		$registeredIpZipCode			=	"";
		$registeredIpLatitude			=	"";
		$registeredIpLongitude			=	"";
		$registeredIpISP				=	"";

		if($ipLattitudeLocationCity		=	getIpAddressDetailsFunction($registeredIp))
		{
			$registeredIpCountry		=	$ipLattitudeLocationCity[3];
			$registeredIpRegion			=	$ipLattitudeLocationCity[5];
			$registeredIpCity			=	$ipLattitudeLocationCity[6];
			$registeredIpZipCode		=	$ipLattitudeLocationCity[7];
			$registeredIpLatitude		=	$ipLattitudeLocationCity[8];
			$registeredIpLongitude		=	$ipLattitudeLocationCity[9];
			$registeredIpISP			=	$ipLattitudeLocationCity[10];
		}
	}
?>
<html>
<head>
	<TITLE>&nbsp;</TITLE>
	<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
	<script type="text/javascript">
		function reflectChange()
		{
			window.opener.location.reload();
		}
	</script>
	<center>
		<table width="98%" align="center" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" class="textstyle1" valign="top"><b>View IP Look Up</b></td>
			</tr>
			<?php
				if(!empty($ip))
				{
			?>
			<tr>
				<td width="25%" class="textstyle" valign="top"><b>IP Address</b></td>
				<td width="1%" class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIp;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Region</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpRegion;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP City</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpCity;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Country</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpCountry;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Zip Code</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpZipCode;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Latitude</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpLatitude;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Longitude</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpLongitude;?>
				</td>
			</tr>
			<tr>
				<td class="textstyle" valign="top"><b>IP Service Provider</b></td>
				<td class="textstyle1" valign="top">:</td>
				<td class="textstyle1">
					<?php echo $registeredIpISP;?>
				</td>
			</tr>
			<?php
				}
				else
				{
			?>
			<tr>
				<td height="100" class="error"><b>Please Provide an IP address</b></td>
			</tr>
			<?php
				}
			?>
		</table>
	</center>
</body>
</html>