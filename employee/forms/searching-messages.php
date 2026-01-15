<!-- <script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" /> -->

<script type="text/javascript">
	$().ready(function() {
		$("#searchNameForMessages").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 340,selectFirst: false});
	});

	function validFormSubmit()
	{
		form1	=	document.searchMesagesOf;
		if(form1.showingForMember.value == "" || form1.showingForMember.value == "0")
		{
			alert("Please enter customer name.");
			form1.showingForMember.focus();
			return false;
		}
	}

</script>
<?php
	$actionPath 	=	"https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<form name="searchMesagesOf" action="<?php echo $actionPath;?>"  method="GET" onsubmit="return validFormSubmit()">
	<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
	    <tr>
			<td width="14%" class="textstyle1"><font color="#ff0000;"><b>Search By Customer : </b></font></td>
			<td width="30%">
				<input type='text' name="showingForMember" size="40" value="<?php echo $showingForMember;?>" id="searchNameForMessages" style="border:1px solid #4d4d4d;height:25px;font-size:15px;" tabindex=3>
			</td>
			<td align="left">
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<input type='hidden' name='<?php echo $hiddenSearchName;?>' value='<?php echo $hiddenSearchValue;?>'>
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
