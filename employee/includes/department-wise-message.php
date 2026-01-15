<br>
<?php
	$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=$s_departmentId ORDER BY displayFrom DESC";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$departmentText	=	$a_department[$s_departmentId];
?>
<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0' style="border:1px solid #FF5959">
<tr bgcolor="#FF5959">
	<td class="text4"><?php echo $departmentText;?> EMPLOYEE NOTICE BOARD</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<?php
		while($row	=	mysql_fetch_assoc($result))
		{
			$title					=	$row['title'];
			$message				=	$row['message'];
			$addedOn				=	showDate($row['addedOn']);
			
			$title					=	stripslashes($title);
			$message				=	stripslashes($message);
			$message				=	nl2br($message);

			$addedByName			=	stripslashes($row['addedByName']);

			if(empty($addedByName))
			{
				$addedByName		=	"Rishi Jindal";
			}


?>
<tr>
	<td class="title1">
		<?php echo $title;?>
	</td>
</tr>
<tr>
	<td class="textstyle">
		<p align="justify">
			<?php echo $message;?>
		</p>
	</td>
</tr>
<tr>
	<td class="smalltext1" align="right">
		<b>FROM : <?php echo $addedByName;?> <!-- (<a href="mailto:rishi@ieimpact.com" class="link_style7">rishi@ieimpact.com</a>) --> On <?php echo $addedOn;?></b>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
<tr>
	<td>
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		}
		echo "</table>";
	}

	$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=$s_departmentId AND isDeleted=0 ORDER BY displayFrom DESC";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<br>
	<table width='100%'  cellpadding='3' cellspacing='0' border='0' style="border:1px solid #ff0000">
		<tr bgcolor="#ff0000">
			<td class="text4" colspan="2">
				<marquee direction="left" behavior="alternate" scrollAmount=10>
					IMPORTANT MESSAGES FOR <?php echo strtoupper($s_employeeName);?>
				</marquee>

			</td>
		</tr>
		<tr>
			<td height="5" colspan="2"></td>
		</tr>
		<?php
				while($row	=	mysql_fetch_assoc($result))
				{
					$messageId			=	$row['messageId'];
					$title				=	$row['title'];
					$message			=	$row['message'];
					$addedOn			=	showDate($row['addedOn']);
					
					$title					=	stripslashes($title);
					$message				=	stripslashes($message);
					$message				=	nl2br($message);
					$isRead		=	$row['isRead'];
					$isReplied	=	$row['isReplied'];
					$readOn		=	$row['readOn'];

					$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
		?>
		<tr>
			<td class="title1">
				<?php echo $title;?>
			</td>
		</tr>
		<tr>
			<td class="smalltext2">
				<?php echo $message;?>
			</td>
		</tr>
		<tr>
			<td class="title2"  align="right">
				Notice On 
				<?php 
					echo $addedOn;
					if($isRead == 1 && $readOn != "0000-00-00")
					{
						echo "&nbsp;&nbsp;|&nbsp;&nbsp;Read On ".showDate($readOn);
					}
				?>&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class="title2" align="right">
				<?php 
					if($isRead == 0)
					{
						echo "<a href='javascript:readDeleteNotice($messageId,1)' class='link_style7'>Mark As Read</a>";
					}
					else
					{
						echo "<a href='javascript:readDeleteNotice($messageId,2)' class='link_style7'>Delete</a>";
					}
					if($isReplied	==	0)
					{
						echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:openWindow($messageId)' class='link_style7'>Reply To This Notice</a>";
					}
				?>
				&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr size="1" width="100%" color="#e4e4e4">
			</td>
		</tr>
		<?php
		}
		echo "</table>";
	}
	if(!empty($s_hasPdfAccess))
	{
	//******************************Messages for pdf employee*********************//
	$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=3 ORDER BY displayFrom DESC";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0' style="border:1px solid #FF5959">
<tr bgcolor="#FF5959">
	<td class="text4">PDF EMPLOYEE NOTICE BOARD</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<?php
		while($row	=	mysql_fetch_assoc($result))
		{
			$title					=	$row['title'];
			$message				=	$row['message'];
			$addedOn				=	showDate($row['addedOn']);
			
			$title					=	stripslashes($title);
			$message				=	stripslashes($message);
			$message				=	nl2br($message);

			$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
?>
<tr>
	<td class="title1">
		<?php echo $title;?>
	</td>
</tr>
<tr>
	<td class="textstyle">
		<p align="justify">
			<?php echo $message;?>
		</p>
	</td>
</tr>
<tr>
	<td class="smalltext1" align="right">
		<b>FROM : <?php echo $addedByName;?> <!-- (<a href="mailto:rishi@ieimpact.com" class="link_style7">rishi@ieimpact.com</a>) --> On <?php echo $addedOn;?></b>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
<tr>
	<td>
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		}
		echo "</table>";
	}

	$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=3 AND isDeleted=0 ORDER BY displayFrom DESC";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<br>
	<table width='100%'  cellpadding='3' cellspacing='0' border='0' style="border:1px solid #ff0000">
		<tr bgcolor="#ff0000">
			<td class="text4" colspan="2">
				<marquee direction="left" behavior="alternate" scrollAmount=10>
					IMPORTANT MESSAGES FOR <?php echo strtoupper($s_employeeName);?>
				</marquee>

			</td>
		</tr>
		<tr>
			<td height="5" colspan="2"></td>
		</tr>
		<?php
				while($row	=	mysql_fetch_assoc($result))
				{
					$messageId			=	$row['messageId'];
					$title				=	$row['title'];
					$message			=	$row['message'];
					$addedOn			=	showDate($row['addedOn']);
					
					$title					=	stripslashes($title);
					$message				=	stripslashes($message);
					$message				=	nl2br($message);
					$isRead		=	$row['isRead'];
					$isReplied	=	$row['isReplied'];
					$readOn		=	$row['readOn'];
		?>
		<tr>
			<td class="title1">
				<?php echo $title;?>
			</td>
		</tr>
		<tr>
			<td class="smalltext2">
				<?php echo $message;?>
			</td>
		</tr>
		<tr>
			<td class="title2"  align="right">
				Notice On 
				<?php 
					echo $addedOn;
					if($isRead == 1 && $readOn != "0000-00-00")
					{
						echo "&nbsp;&nbsp;|&nbsp;&nbsp;Read On ".showDate($readOn);
					}
				?>&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class="title2" align="right">
				<?php 
					if($isRead == 0)
					{
						echo "<a href='javascript:readDeleteNotice($messageId,1)' class='link_style7'>Mark As Read</a>";
					}
					else
					{
						echo "<a href='javascript:readDeleteNotice($messageId,2)' class='link_style7'>Delete</a>";
					}
					if($isReplied	==	0)
					{
						echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:openWindow($messageId)' class='link_style7'>Reply To This Notice</a>";
					}
				?>
				&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr size="1" width="100%" color="#e4e4e4">
			</td>
		</tr>
		<?php
		}
		echo "</table>";
	}
}
?>