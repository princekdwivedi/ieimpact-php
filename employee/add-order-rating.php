<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$orderId			=	0;
	$rateChoose			=	0;
	$rateGivenText		=	"";
	
	if(isset($_GET['orderId']) && isset($_GET['rateChoose']))
	{
		$orderId			=	$_GET['orderId'];
		$rateChoose			=	$_GET['rateChoose'];
		$rateGivenText		=	$a_ratingByQa[$rateChoose];
	}

	if(!empty($orderId) && !empty($rateChoose))
	{
?>
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="75%">
			<table width="100%" align="center" border="0" cellpadding="1" cellspacing="1">
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td width="26%" class="smalltext2" valign="top"><b>Selected Rating</td>
					<td width="2%" class="smalltext2" valign="top" align="center"><b> : </td>
					<td width="10%" class="smalltext2" valign="top">&nbsp;
						<?php
							for($i=1;$i<=$rateChoose;$i++)
							{
								echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
							}
						?>
					</td>
					<td class="smalltext2" valign="top" width="10%">
						<b><?php echo $rateGivenText;?></b>
						<input type="hidden" name="selectedRate" value="<?php echo $rateChoose;?>">
					</td>
					<td>	
						<img src="<?php echo SITE_URL;?>/images/close.gif" border="0" onclick="showHideRate(0)">
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top"><b>Comments (If Any)</b></td>
					<td class="smalltext2" valign="top" align="center"><b> : </td>
					<td class="smalltext2" colspan="3">
						<input type="text" name="qaRateMessage" value="" size="50" maxlength="100" style="border:1px solid #333333">
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php
	}		
?>
	