<table width="100%" border="0" cellpadding="0" cellspacing="3" bgcolor="#FFFFFF">
<tr >
<?php
	$allLinkQuestion	=	"";
	if(isset($alphaLinkQueryString) && !empty($alphaLinkQueryString))
	{
		$allLinkQuestion=	"?".$alphaLinkQueryString;
	}
	if(isset($_GET['alpha']))
	{
		$alpha	=	$_GET['alpha'];
	}
	if(!isset($alphabetLinkPage))
	{
		$alphabetLinkPage	=	SITE_URL_EMPLOYEES;
			
	}
	if(!isset($alpha))
	{
		$alpha		=	"";
		$allClass	=	"class='alphaLink3'";
		$allBg		=	"bgcolor='#ffecef''";
	}
	else
	{
		$allClass	=	"class='alphaLink'";	
		$allBg		=	"bgcolor='#f3f3f3''";
	}
?>
<td height='25' bgcolor='#E00024' width="5%" style="text-align:center;">
	<table width='100%' cellpadding='0' cellspacing='0'>
			<tr>
				<td style="text-align:center;"  bgcolor='#ffecef' height='21' witdh='100%'>
					<a href="<?php echo $alphabetLinkPage.$allLinkQuestion;?>" <?php echo $allClass; $allBg?>>All</a>
				</td>
			</tr>
	</table>
</td>
<?php
	for($i=65;$i<=90;$i++)
	{
		$t_alpha = chr($i);
		$bgcolor	= "";
		if($t_alpha == $alpha && (!isset($_GET['alpha'])))
		{
			$bgcolor	= "bgcolor='#ffecef'";
			$class		= "class='alphaLink3'";
		}
		elseif(isset($_GET['alpha']) && $t_alpha == $_GET['alpha'])
		{
			$bgcolor	= "bgcolor='#ffecef''";
			$class		= "class='alphaLink3'";
		}
		else
		{
			$bgcolor	= "bgcolor='#f3f3f3'";
			$class		= "class='alphaLink'";
		}
		?>
		<td height='25' bgcolor='#E00024'>
			<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<?php
						echo "<td style='text-align:center;'  $bgcolor height='21' witdh='100%'>";
						echo "<a href='".$alphabetLinkPage."?alpha=$t_alpha".$alphaLinkQueryString."' $class>$t_alpha</a></td></tr>";
						echo "</table></td>";
			
	}
?>
</tr></table>