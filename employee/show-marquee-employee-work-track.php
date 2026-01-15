<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	//ini_set('display_errors', 1);	
	
?>
<!--<div id="divID" style="border:1px solid #ff0000;">)-->
<marquee behavior="scroll" direction="up" style="height:200px" scrolldelay="300"  id="mymarquee">
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">

<?php
	//echo "KASE2-".date("H:i:s");
	$query	=	"SELECT memberId,orderAddress,orderAddedOn,orderAddedTime,fullName,order_employee_works.* FROM order_employee_works INNER JOIN members_orders ON order_employee_works.orderId=members_orders.orderId INNER JOIN employee_details ON order_employee_works.employeeId=employee_details.employeeId WHERE members_orders.isVirtualDeleted=0 ORDER BY trackId DESC LIMIT 10";
	$result							=   dbQuery($query);
	if(mysql_num_rows($result)){
		$i							=	0;
		while($row					=   mysql_fetch_assoc($result))
		{
			$i++;

			$employeeId				=	$row['employeeId'];
			$memberId				=	$row['memberId'];
			$orderId				=	$row['orderId'];
			$employeeName			=	stripslashes($row['fullName']);
			$orderAddress			=	stripslashes($row['orderAddress']);
			$performedTask			=	stripslashes($row['performedTask']);
			$date					=	showDateMonth($row['date']);
			$time					=	showTimeFormat($row['time']);
			$orderAddedOn			=	showDateMonth($row['orderAddedOn']);
			$orderAddedTime			=	showTimeFormat($row['orderAddedTime']);

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}
	?>
	<tr <?php echo $bgColor;?> height="30">
		<td class="smalltext2" valign="top">
			&nbsp;<?php echo $i.")";?>
		</td>
		<td valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>" class='link_style6'><?php echo $orderAddress;?></a>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $orderAddedOn.",".$orderAddedTime;?>
		</td>
		<td valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $employeeId;?>&showingEmployeeOrder=1" class='link_style6'><?php echo $employeeName;?></a>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $performedTask;?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $date.",".$time;?>
		</td>
	</tr>
	<?php
		}
	}
?>
</table>
</marquee>
<!--</div>


 <script type="text/javascript"       src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript">
var auto_refresh = setInterval(
function () {
    $('#divID').load('<?php echo SITE_URL_EMPLOYEES;?>/show-marquee-employee-work-track.php');
}, 16000);
    </script>-->
