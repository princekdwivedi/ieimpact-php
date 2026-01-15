<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");

	$searchdate					=	date("d-m-Y");
	$t_searchdate				=	date("Y-m-d");

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if(!empty($searchdate))
		{
			list($d,$m,$y)		=	explode("-",$searchdate);
			$t_searchdate		=	$y."-".$m."-".$d;
		}
	}
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();
	$formSearch					=  SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<form name="searchCheckedOrders" action="" method="POST">
	<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td class="heading3" colspan="3">
				:: CHECK CURRENT ORDER STATUS on <?php echo showDate($t_searchdate);?> ::
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<?php 
					include($formSearch);
				?>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td width="8%" class="smalltext2">
				<b>Select Date :</b>
			</td>
			<td width="10%">
				<input type="text" name="searchdate" value="<?php echo $searchdate;?>" class="textbox" id="from" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('from','ddmmyyyy')"><img src="<?php echo SITE_URL;?>/images/cal.gif" width="16" heWight="16" border="0" alt="Pick a date"></a>
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
</form>
<br />
<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<?php
		$query	=	"SELECT * FROM time_to_time_performence WHERE date='$t_searchdate'";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
	?>
	<tr bgcolor="#373737" height="20">
		<td class="smalltext8" width="7%">&nbsp;<b>Time</b></td>
		<td width="7%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=1' target='_blank' class="link_style33">New Orders</a></td>
		<td width="7%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=5' target='_blank' class="link_style33">In Attention</a></td>
		<td width="6%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=2' target='_blank' class="link_style33">In Process</a></td>
		<td width="11%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=4' target='_blank' class="link_style33">Uncompleted Orders</a></td>
		<td width="10%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchUnchecked=1' target='_blank' class="link_style33">Unchecked for files</a></td>
		<td width="8%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchExceedTat=1' target='_blank' class="link_style33">Exceeded TAT</a></td>
		<td width="10%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=4&searchRushSketch=1' target='_blank' class="link_style33">Uncompleted Rush</a></td>
		<td width="11%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1#second' target='_blank' class="link_style33">Messages not replied</a></td>
		<td width="10%"><a href='<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1#third' target='_blank' class="link_style33">Ratings not replied</a></td>
		<td ><a href='<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1#fifth' target='_blank' class="link_style33">Email messages not replied</a></td>
	</tr>

	<?php
			$count						=	0;
			while($row					=	mysqli_fetch_assoc($result))
			{
				$count++;
				$notAcceptedOrders		=	$row['notAcceptedOrders'];	
				$needAttentionOrders	=	$row['needAttentionOrders'];
				$inProcessedOrders		=	$row['inProcessedOrders'];
				$unCompletedOrders		=	$row['unCompletedOrders'];
				$unCheckedOrders		=	$row['unCheckedOrders'];
				$exceedTat				=	$row['exceedTat'];
				$messagesNotReplied		=	$row['messagesNotReplied'];
				$ratingMsgUnreplied		=	$row['ratingMsgUnreplied'];
				$emailMesgNotReplied	=	$row['emailMesgNotReplied'];
				$unrepliedRushOrders	=	$row['unrepliedRushOrders'];
				$time					=	showTimeFormat($row['time']);


				$bgColor				=	"class='rwcolor1'";
				if($count%2==0)
				{
					$bgColor			=   "class='rwcolor2'";
				}

	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext17">&nbsp;<b><?php echo $time;?> Hrs IST</b></td>
		<td>
			<b><?php echo $notAcceptedOrders;?></b>
		</td>
		<td>
			<b><?php echo $needAttentionOrders;?></b>
		</td>
		<td>
			<b><?php echo $inProcessedOrders;?></b>
		</td>
		<td>
			<b><?php echo $unCompletedOrders;?></b>
		</td>
		<td>
			<b><?php echo $unCheckedOrders;?></b>
		</td>
		<td>
			<b><?php echo $exceedTat;?></b>
		</td>
		<td>
			<b><?php echo $unrepliedRushOrders;?></b>
		</td>
		<td>
			<b><?php echo $messagesNotReplied;?></b>
		</td>
		<td>
			<b><?php echo $ratingMsgUnreplied;?></b>
		</td>
		<td>
			<b><?php echo $emailMesgNotReplied;?></b>
		</td>
	</tr>
	<?php
			}
		}
		else
		{
	?>
	<tr>
		<td colspan="8" height="200" class="error" style="text-align:center"><b>NO RECORD FOUND</b></td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>