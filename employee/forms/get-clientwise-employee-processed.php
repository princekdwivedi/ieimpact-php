<script type="text/javascript">
	function search()
	{
		//return true;
		form1 = document.searchFormReport;
		if(form1.selectedCustomer.value == "0" && form1.selctedEmployee.value == "0")
		{
			alert("Please select a customer or an employee.");
			form1.selectedCustomer.focus();
			return false;
		}
	}
</script>
<form name="searchFormReport" action=""  method="GET" onsubmit="return search();">
	<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td colspan="8" class="textstyle3"><b>GET CLIENTWISE/EMPLOYEEWISE MONTHLY COMPLETED ORDERS TOTAL</b></td>
		</tr>
		<tr>
			<td width="12%" class="textstyle3">FOR CUSTOMER</td>
			<td width="15%">
				<select name="selectedCustomer" style="width:200px;">
					<option value="0">Select</option>
					<?php
						foreach($all_customers as $id=>$name){
							$select 	=	"";
							if($id      == $selectedCustomer){
								$select =	"selected";
							}

							echo "<option value='$id' $select>$name</option>";
						}
					?>
				</select>
			</td>
			<td width="12%" class="textstyle3">BY EMPLOYEE</td>
			<td width="15%">
				<select name="selctedEmployee">
					<option value="0">Select</option>
					<?php
						foreach($all_employees as $id=>$name){
							$select 	=	"";
							if($id      == $selctedEmployee){
								$select =	"selected";
							}

							echo "<option value='$id' $select>$name</option>";
						}
					?>
				</select>
			</td>
			<td width="3%" class="textstyle3">Type</td>
			<td width="15%">				
				<?php
					foreach($search_type as $id=>$name){
						$checked 	=	"";
						if($id      == $searchFor){
							$checked=	"checked";
						}

						echo "<input type='radio' name='searchFor' value='$id' $checked>".$name."&nbsp;";
					}
				?>
				
			</td>
			<td width="6%" class="textstyle3">Month/Year</td>
			<td width="10%">
			    <select name="selectMonth">
					<?php
						foreach($a_month as $id=>$name){
							$select 	=	"";
							if($id      == $selectMonth){
								$select =	"selected";
							}

							echo "<option value='$id' $select>$name</option>";
						}
					?>
				</select>/		
				<select name="selectYear">
					<?php
						for($i=2018;$i<=date('Y');$i++){
							$select 	=	"";
							if($i       == $selectYear){
								$select =	"selected";
							}

							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>		
			</td>
			<td>
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>
	</table>
</form>