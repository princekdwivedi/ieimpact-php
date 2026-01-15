<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	if(!empty($s_employeeId))
	{

		$orderId					=	0;
		if(isset($_GET['orderId']))
		{
			$orderId				=	$_GET['orderId'];
		}

		$query		=	"SELECT * FROM members_orders  WHERE orderId=$orderId";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row					=	mysql_fetch_assoc($result);
			$status					=	$row['status'];
			$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
			$customerEstimatedDate	=	$row['customerEstimatedDate'];
			$customerEstimatedTime	=	$row['customerEstimatedTime'];
			$employeeEstimatedDate	=	$row['employeeEstimatedDate'];
			$employeeEstimatedTime	=	$row['employeeEstimatedTime'];
			$employeeWarningDate	=	$row['employeeWarningDate'];
			$employeeWarningTime	=	$row['employeeWarningTime'];

			if($isHavingEstimatedTime		==	1)
			{
				if($nowDateIndia < $employeeWarningDate)
				{
					$diffMin				=	timeBetweenTwoTimes($nowDateIndia,$nowTimeIndia,$employeeWarningDate,$employeeWarningTime);

					$diffHrsMin				=	getHours($diffMin);

					$expctDelvText			=	$diffHrsMin." Hrs left to complete this order. Expected delivery at ".showTimeFormat($employeeWarningTime)." Hrs on ".showDate($employeeWarningDate);
				}
				elseif($nowDateIndia		== $employeeWarningDate)
				{
					if($nowTimeIndia	   <= $employeeWarningTime)
					{
						$diffMin			=	timeBetweenTwoTimes($nowDateIndia,$nowTimeIndia,$employeeWarningDate,$employeeWarningTime);

						$diffHrsMin			=	getHours($diffMin);

						$expctDelvText		=	$diffHrsMin." Hrs left to complete this order. Expected delivery at ".showTimeFormat($employeeWarningTime)." Hrs on ".showDate($employeeWarningDate);
					}
					else
					{
						$diffMin			=	timeBetweenTwoTimes($employeeWarningDate,$employeeWarningTime,$nowDateIndia,$nowTimeIndia);

						$diffHrsMin			=	getHours($diffMin);

						$expctDelvText		=	"Exceeded ".$diffHrsMin." Hrs of complete this order deadline. Expected delivery was at ".showTimeFormat($employeeWarningTime)." Hrs on ".showDate($employeeWarningDate);
					}
				}
				else
				{
					echo "<br>KASE4";
					$diffMin			=	timeBetweenTwoTimes($employeeWarningDate,$employeeWarningTime,$nowDateIndia,$nowTimeIndia);

					$diffHrsMin			=	getHours($diffMin);

					$expctDelvText		=	"Exceeded ".$diffHrsMin." Hrs of complete this order deadline. Expected delivery was at ".showTimeFormat($employeeWarningTime)." Hrs on ".showDate($employeeWarningDate);
				}
			}
		}
	?>
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<font style="font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold"><?php echo $expctDelvText;?></font>
			</td>
		</tr>
	</table>
<?php
	}		
?>