<table width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td colspan="5" class="textstyle1">
			<b>Current Month Works Total</b>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<?php
		if($s_departmentId == 1)
		{
			$totalMtLinesMoney		=	$employeeObj->getCurrentMonthMTLinesMoney($s_employeeId,$today_month,$today_year);

			list($mtLines,$mtMoney)	=	explode("=",$totalMtLinesMoney);
		?>
		<tr>
			<td width="25%" class="smalltext2"><b>Total Lines : </b></td>
			<td width="2%">&nbsp;</td>
			<td width="15%" class="error"><b><?php echo $mtLines;?></b></td>
			<td width="2%">&nbsp;</td>
			<td width="25%" class="smalltext2"><b>Total Money : </b></td>
			<td width="2%">&nbsp;</td>
			<td class="error"><b><?php echo $mtMoney;?></b></td>
		</tr>
	<?php
		}	
		if(!empty($s_hasPdfAccess))
		{
			
			$query				=	"SELECT processedDone,qaDone FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$s_employeeId";
			$result				=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row			=	mysql_fetch_assoc($result);
				$processedDone	=	$row['processedDone'];
				$qaDone			=	$row['qaDone'];
		?>
		<tr>
			<td colspan="8">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<tr>
			<td colspan="10">
				<table width="100%" align="center" cellpadding="2" cellspacing="2">
					<tr>
						<td width="38%" class="smalltext2"><b>Total Processed Order</b></td>
						<td width="2%" class="smalltext2">:</td>
						<td class="error"><b><?php echo $processedDone;?></b></td>
					</tr>
					<tr>
						<td class="smalltext2"><b>Total QA Done</b></td>
						<td class="smalltext2">:</td>
						<td class="error"><b><?php echo $qaDone;?></b></td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
			}
		}
	?>
	<tr>
		<td height="10"></td>
	</tr>
</table>