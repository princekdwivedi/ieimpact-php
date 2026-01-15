<?php
	$query	=	"SELECT employee_breaks.*,firstName,lastName FROM employee_breaks INNER JOIN employee_details ON employee_breaks.employeeId=employee_details.employeeId WHERE employee_details.isActive=1 AND breakDate='".CURRENT_DATE_INDIA."' AND hasPdfAccess=1 ORDER BY breakTime DESC";
	
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
<tr>
	<td colspan="5" class="hometext"><b>VIEW EMPLOYEES BREAK TIME ON <?php echo showDate($nowDateIndia);?></b></td>
</tr>
<tr>
	<td width="7%" class="textstyle"><b>Sr. No</b></td>
	<td width="25%" class="textstyle"><b>Name</b></td>
	<td class="textstyle" width="15%"><b>From</b></td>
	<td class="textstyle" width="15%"><b>To</b></td>
	<td class="textstyle"><b>Reason</b></td>
</tr>
<tr>
	<td colspan="5">
		<div style='border:0px solid #ff0000;overflow:auto;height:180px'>
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<?php
					$i=	0;
					while($row					=	mysql_fetch_assoc($result))
					{
						$i++;
						$employeeId				=	$row['employeeId'];
						$breakDate				=	showDate($row['breakDate']);
						$breakFinsheddate		=	showDate($row['breakFinsheddate']);
						$breakTime				=	date("H:i",strtotime($row['breakTime']));
						$breakFinishedTime		=	date("H:i",strtotime($row['breakFinishedTime']));
						$breakTakingFor			=	stripslashes($row['breakTakingFor']);
						$firstName				=	stripslashes($row['firstName']);
						$lastName				=	stripslashes($row['lastName']);
						$employeeName			=	$firstName." ".$lastName;
				?>
				<tr>
					<td class="textstyle" valign="top" width="6%" ><?php echo $i;?>)</td>
					<td class="textstyle" valign="top" width="25%"><?php echo $employeeName;?></td>
					<td class="textstyle" valign="top"  width="15%"><?php echo $breakTime;?>Hrs</td>
					<td class="textstyle" valign="top" width="15%"><?php echo $breakFinishedTime;?>Hrs</td>
					<td class="textstyle" valign="top"><?php echo nl2br($breakTakingFor);?></td>
				</tr>
				<tr>
					<td colspan="7">
						<hr size="1" width="100%" color="#e4e4e4">
					</td>
				</tr>
				<?php
					}
				?>
			</table>
		</div>
	</td>
</table>
<?php
	}
?>