<script type="text/javascript">
	function checkForNumber()
	{
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;

		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k<48||k>57)
		{
			return false;
		}
		if((k >= 65 && 90 >= k) || (k >= 97 && 122 >= k))
		{
			return false;
		}
	}

	function validQuestion(){
		form1	=	document.addEditQueAns;

		if(form1.question.value == "" || form1.question.value == " " || form1.question.value == "&nbsp;"){
			alert("Please enter question.");
			form1.question.focus();
			return false;
		}
		if(form1.descriptions.value == "" || form1.descriptions.value == " " || form1.descriptions.value == "&nbsp;"){
			alert("Please enter help text.");
			form1.descriptions.focus();
			return false;
		}
		
		/*if(form1.questionWeightage.value == "" || form1.questionWeightage.value == " " || form1.questionWeightage.value == "&nbsp;" || form1.questionWeightage.value == "0"){
			alert("Please enter question weightage.");
			form1.questionWeightage.focus();
			return false;
		}*/
	}
	function deleteQuestion(questionAnswerId,recNo)
	{
		var confirmation = window.confirm("Are you sure to delete thi question & answers?");
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES?>/add-employee-test-questions.php?questionAnswerId='+questionAnswerId+"&recNo="+recNo+"&isDelete=1";
		}
	}
</script>
<form name="addEditQueAns" action=""  method="POST" onsubmit="return validQuestion();">
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td colspan="12">
				<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d"><?php echo $headText;?></font>
			</td>
		</tr>
		<?php
			if(!empty($errorMsg)){
				echo "<tr><td colspan='5' class='error'><b>".$errorMsg."</b></td></tr>";
			}
		?>
		<tr>
			<td width="15%" class="smalltext23"><b>Question Category</b></td>
			<td width="2%" class="smalltext23"><b>:</b></td>
			<td>
				<select name="questionCategory" style="height:32px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;">
					<?php
						foreach($answers_option_list as $k=>$v){
							$select			=	"";
							if($v           ==  $questionCategory){
								$select		=	"selected";
							}

							echo "<option value='$v' $select>$v</option>";
						}
					?>
				</select>&nbsp;<font class="error">[Note:Please select atleast one right answer]</font>
			</td>
		</tr>
		<tr>
			<td class="smalltext23"><b>Question</b></td>
			<td class="smalltext23"><b>:</b></td>
			<td>
				<input type='text' name="question" value="<?php echo $question;?>" style="height:32px;width:800px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;">
			</td>
		</tr>
		<!--<tr>
			<td width="12%" class="textstyle"><b>Question Weightage</b></td>
			<td width="2%" class="textstyle"><b>:</b></td>
			<td>
				<input type='text' name="questionWeightage" value="<?php echo $questionWeightage;?>" style="height:32px;width:100px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;" onkeypress="return checkForNumber();">&nbsp;<font class="smalltext2">[Total marks of this question e.g - 50]</font>
			</td>
		</tr>-->
		<tr>
			<td colspan="3" class="smalltext23"><b>Answers</b></td>
		</tr>
		<?php
			foreach($answers_option_list as $k=>$option){
				$check1	=	"";
				$check2	=	"checked";

				$answer			=	"";
				if(array_key_exists($k,$answer_list)){
					 $answer    =	$answer_list[$k];
				}

				$weightage		=	"";
				if(array_key_exists($k,$weightage_list)){
					 $weightage =	$weightage_list[$k];
				}

				if(array_key_exists($k,$right_answer)){
					 $checkvalue   =	$right_answer[$k];
					 if($checkvalue== 1){
						$check1	   =	"checked";
						$check2	   =	"";
					 }
				}
		?>
			<tr>
				<td colspan="3" class="smalltext1">Option <b><?php echo $option;?>)</b>&nbsp;<input type='text' name="answer_list[<?php echo $k;?>]" value="<?php echo $answer;?>" style="height:32px;width:700px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;">&nbsp;<!--&nbsp;Weightage&nbsp;<input type='text' name="weightage_list[<?php echo $k;?>]" value="<?php echo $weightage;?>" style="height:32px;width:100px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;"  onkeypress="return checkForNumber();">&nbsp;&nbsp;<font class="smalltext2">&nbsp;&nbsp;-->Is Right Answer <input type="radio" name="right_answer[<?php echo $k;?>]" value="1" <?php echo $check1;?>>Yes&nbsp;<input type="radio" name="right_answer[<?php echo $k;?>]" value="0" <?php echo $check2;?>>No</td>
			</tr>
		<?php
			}	
		?>
		<tr>
			<td class="smalltext23" valign="top"><b>Help Me Text</b>&nbsp;</td>
			<td class="smalltext23" valign="top"><b>:</b></td>
			<td valign="top">
				<textarea name="descriptions" rows="4" style="width:450px;padding:2px 3px;font-family:arial;font-size:15px;color:#333333;text-decoration:none;border: 1px solid #bebebe;"><?php echo $descriptions;?></textarea>
			</td>
		</tr>
		<tr>
			<td align='center' colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>