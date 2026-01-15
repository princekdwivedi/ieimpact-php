<?php
	if(!isset($searchOrderType))
	{
		$searchOrderType	=	 0;
	}
	if(!isset($searchOrder))
	{
		$searchOrder		=	"";
	}
	if(!isset($searchName))
	{
		$searchName			=	"";
	}
?>


<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 365,selectFirst: false});
});

$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 290,selectFirst: false});
});


function checkForNumber()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else
	{
		return false;
	}
 }

 function checkForString()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	if(k >= 65 && k <= 90 )
	{
		return true;
	}
	if(k >= 97 && k <= 122 )
	{
		return true;
	}
	if(k == 32 )
	{
		return true;
	}
	else
	{
		return false;
	}
 }
 function pageRedirectIntoUrl(url)
 {
	location.href   = url;
 }
</script>
<form name="searchPdfOrderForm" action="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	   	<tr>
			<td width="6%" class="textstyle1">TYPE</td>
			<td width="2%" class="textstyle1">:</td>
			<td class="smalltext2" width="40%">
				<?php
					foreach($a_searchOrderType as $k=>$v)
					{
						$checked	=	"";
						if($k		==	$searchOrderType)
						{
							$checked=	"checked";
						}

						echo "<input type='radio' name='searchOrderType' value='$k' $checked>$v&nbsp;";
					}
				?>
			</td>
			<td class="textstyle1" width="10%">ADDRESS</td>
			<td class="textstyle1" width="2%">:</td>
			<td class="smalltext2" valign="top">
				<input type='text' name="searchOrder" size="51" value="<?php echo $t_searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
						
			</td>
		</tr>
		<tr>
			<td colspan="8" height="8"></td>
		</tr>
		<tr>
			<?php
				if(isset($_SESSION['hasManagerAccess']) && !empty($s_hasManagerAccess))
				{
			?>
			<td class="textstyle1"><?php echo $textRed;?></td>
			<td class="textstyle1">:</td>
			<td class="smalltext2">
				<input type='text' name="searchText" size="40" value="<?php echo $searchName;?>" id="searchName" onkeypress="return checkForString();" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<?php
				}
			?>
			<td class="textstyle1">DELIVERY</td>
			<td class="textstyle1">:</td>
			<td class="smalltext2" colspan="4">
			<?php
				foreach($a_searchRushSketch as $k=>$v)
				{
					$checked	=	"";
					if($k		==	$searchRushSketch)
					{
						$checked=	"checked";
					}

					echo "<input type='radio' name='searchRushSketch' value='$k' $checked>$v&nbsp;";
				}
			?>

		</td>
		</tr>
		<tr>
			<td colspan="8" height="6"></td>
		</tr>
		<tr>
			<td colspan="6">
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<?php
					if($showSubmittedResult == false)
					{
				?>
				<img src="<?php echo SITE_URL;?>/images/reset-small.png" border="0" onClick="document.searchPdfOrderForm.reset()" style="cursor:pointer;" title="Reset">
				<?php
					}
					else
					{
				?>
				<img src="<?php echo SITE_URL;?>/images/reset-small.png" border="0" onClick="pageRedirectIntoUrl('<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php')" style="cursor:pointer;" title="Reset">
				<?php
					}
				?>
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="8" height="8"></td>
		</tr>
	</table>
</form>