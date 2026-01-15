<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	$donotShowTestQuestionTop		=	1;
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	include(SITE_ROOT				.   "/classes/email-templates.php");
	include(SITE_ROOT				.   "/classes/pagingclass1.php");
	$employeeObj					=	new employee();
	$emailObj						=	new emails();
	$pagingObj						=   new Paging();
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	$answers_option_list			=   array("1"=>"A","B","C","D");
	$errorMsg                       =   "";
	$wrongAnswerText                =   "";
	$showEnterInterface				=	"none";
	$falseSelectedQId               =   0;
	$showHelpText					=	"none";
	
	if(!isset($_SESSION['showTestQuestionId']) && isset($_SESSION['falseSelectedId']) && $_SESSION['falseSelectedAnsId']){
		$wrongAnswerText            =   "<font style='color:#ff0000;font-size:16px;font-weight:bold;'>Oops wrong answer</font>";
		$showEnterInterface		    =	"";
		$falseSelectedQId           =   1;
		$showHelpText				=	"";
	}
	else{
		if(isset($_SESSION['isSetWrongAnswer'])){
			$wrongAnswerText            =   "<font style='color:#ff0000;font-size:16px;font-weight:bold;'>Oops wrong answer</font>";
			$showEnterInterface		    =	"";
			$falseSelectedQId           =   1;
			$showHelpText				=	"";
		}
	}


	/*if(!isset($_SESSION['showTestQuestionId']) && !isset($_SESSION['success'])){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/

	$cookieName						=	"remembertestIDs".$s_employeeId;
	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}
	$wrongSelectedAns				=	0;
	if(isset($_SESSION['falseSelectedId'])){
		$recNo						=	0;
	}
	//pr($_SESSION);

?>
<style>
	@import url(https://fonts.googleapis.com/css?family=Cabin:700);

	/* HTML5 Boilerplate accessible hidden styles */
	[type="radio"] {
	  border: 0; 
	  clip: rect(0 0 0 0); 
	  height: 1px; margin: -1px; 
	  overflow: hidden; 
	  padding: 0; 
	  position: absolute; 
	  width: 1px;
	}

	/* One radio button per line */
	label {
	  display: block;
	  cursor: pointer;
	  line-height: 2.5;
	  font-size: 1.3em;
	}

	[type="radio"] + span {
	  display: block;
	}

	/* the basic, unchecked style */
	[type="radio"] + span:before {
	  content: '';
	  display: inline-block;
	  width: 1em;
	  height: 1em;
	  vertical-align: -0.25em;
	  border-radius: 1em;
	  border: 0.125em solid #fff;
	  box-shadow: 0 0 0 0.15em #000;
	  margin-right: 0.75em;
	  transition: 0.5s ease all;
	}

	/* the checked style using the :checked pseudo class */
	[type="radio"]:checked + span:before {
	  background: green;
	  box-shadow: 0 0 0 0.25em #000;
	}

	/* never forget focus styling */
	[type="radio"]:focus + span:after {
	  content: '\0020\2190';
	  font-size: 1.5em;
	  line-height: 1;
	  vertical-align: -0.125em;
	}

	/* Nothing to see here. */
	

	fieldset {
	  font-size: 1em;
	  border: 2px solid #000;
	  padding: 2em;
	  border-radius: 0.5em;
	}

	legend {
	  color: #fff;
	  background: #000;
	  padding: 0.25em 1em;
	  border-radius: 1em;
	}

	.p {
	  text-align: center;
	  font-size: 14px;
	  padding-top: 120px;
	}
	/*
		https://www.formget.com/css-background-opacity/
		http://www.aorank.com/tutorial/background_opacity/background_opacity.html
	*/
	#main{
		position:relative;
		width:800px;
		height:300px;
		top:0px;
		left:10px;
	}
	#first
	{
	position:absolute;
	left:10px;
	}
	#second
	{
	position:absolute;
	left:0px;
	}
	#third
	{
	position:relative;
	left:5px;
	}
	#container{	
		background-image: url('http://www.aorank.com/tutorial/background_opacity/images/wood1.jpg');
		opacity:0.2;
		width:200px;
		height:150px;
		border:1px solid #0099CC;
	}
	#container1{	
		background-image: url('http://www.aorank.com/tutorial/background_opacity/images/wood1.jpg');
		opacity:0.6;
		width:200px;
		height:150px;
		border:1px solid #0099CC;
	}
	#container2{	
		background-image: url('../images/wood1.jpg');
		opacity:1;
		width:1200px;
		height:50px;
		border:1px solid #0099CC;
	}
	label
	{
	  font-family: 'Open Sans', sans-serif;
	  
	}
	#lab1,#lab2,#lab3
	{
		position:absolute;
		top:2px;
		left:10px;
		font-size: 1.75em;
		color:#ffffff;
	}

</style>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
	function showDesc(isRightAnswer,boxId)
	{	
	
		var rightWrongText	=	"";
		var fontcolor		=	"";
		
		if(isRightAnswer    ==  "0"){
			rightWrongText	=	"Oops wrong answer";
			//document.getElementById('showHelpTextId').style.display  = 'inline';
			fontcolor		=	"#ff0000";
			document.getElementById('showEnterHelpMeAns').style.display  = 'inline';
			document.getElementById('showWithDisabled').value = 1;
			document.getElementById('showingNextLink').style.display  = 'none';
			
			if(boxId == 1){
				document.getElementById('pretty2').setAttribute("disabled","disabled");
				document.getElementById('pretty3').setAttribute("disabled","disabled");
				document.getElementById('pretty4').setAttribute("disabled","disabled");
			}
			else if(boxId == 2){
				document.getElementById('pretty1').setAttribute("disabled","disabled");
				document.getElementById('pretty3').setAttribute("disabled","disabled");
				document.getElementById('pretty4').setAttribute("disabled","disabled");
			}
			else if(boxId == 3){
				document.getElementById('pretty1').setAttribute("disabled","disabled");
				document.getElementById('pretty2').setAttribute("disabled","disabled");
				document.getElementById('pretty4').setAttribute("disabled","disabled");
			}
			else if(boxId == 4){
				document.getElementById('pretty1').setAttribute("disabled","disabled");
				document.getElementById('pretty2').setAttribute("disabled","disabled");
				document.getElementById('pretty3').setAttribute("disabled","disabled");
			}
			commonFunc('<?php echo SITE_URL_EMPLOYEES;?>/set-wrong-test-question.php?setBox=','setWrongAnswer',boxId);

		}
		else{
			rightWrongText	=	"Wow its right answer";
			//document.getElementById('showHelpTextId').style.display  = 'none';
			fontcolor		=	"#007138";
			document.getElementById('showEnterHelpMeAns').style.display  = 'none';
		}
		document.getElementById('showHelpTextId').style.display  = 'inline';
		
		document.getElementById('showRightWrongAns').innerHTML = "<font style='color:"+fontcolor+";font-size:16px;font-weight:bold;'>"+rightWrongText+"</font>";
			
	}
</script>

<table width="98%" action="" border="0" cellpadding="2" cellspacing="2">		
	<?php
		if(isset($_REQUEST['formSubmitted'])){
			extract($_REQUEST);
			//pr($_REQUEST);
			//die();
			if(isset($_POST['answerGiven'])){
				$a_answerGiven	    =   $_POST['answerGiven'];
				$a_rightAnswerGiven	=	$_POST['rightAnswerGiven'];
				$a_askingQuestion	=	$_POST['askingQuestion'];
				$typeHelpText		=	stripslashes($_POST['typeHelpText']);

				$questionId			=	$a_askingQuestion[0];
				$answerId			=	$a_answerGiven[0];

			
				if(array_key_exists($answerId,$a_rightAnswerGiven) && $a_rightAnswerGiven[$answerId] != 1){
					if(empty($typeHelpText)){
						$errorMsg	=	"Please type help text exactly.";
					}
					else{
						$existingHelpText	=	$employeeObj->getSingleQueryResult("SELECT description FROM employee_test_questions WHERE questionAnswerId=$questionId AND description <> ''","description");
						if(!empty($existingHelpText)){
							$existingHelpText=	stripslashes($existingHelpText);
							$typeHelpText    =  strtolower($typeHelpText);
							$existingHelpText=  strtolower($existingHelpText);

							if($typeHelpText != $existingHelpText){
								$errorMsg	  =	"Please type exact help text.";
							}
						}
					}
				}
			

				if(empty($errorMsg)){
					
					if(array_key_exists($answerId,$a_rightAnswerGiven) && $a_rightAnswerGiven[$answerId] == 1){
						dbQuery("UPDATE employee_details SET testScore=testScore+2 WHERE employeeId=$s_employeeId");
					}
					else{
						$totalExistingPoints=	$employeeObj->getSingleQueryResult("SELECT testScore FROM employee_details WHERE employeeId=$s_employeeId","testScore");
						if($totalExistingPoints >= 10){
							dbQuery("UPDATE employee_details SET testScore=testScore-10 WHERE employeeId=$s_employeeId");
						}
					}
					

					/*$remembertestIDs	    =   array();
					if(isset($_COOKIE[$cookieName]))
					{
						$remembertestIDs	=	json_decode($_COOKIE[$cookieName], true);
					}
					$remembertestIDs[]		=	$questionId;
					$domain                 =   REMEMBER_PASSWORD_ON_SITE;

					setcookie($cookieName,json_encode($remembertestIDs), time()+60*60*24*150, '/', $domain);*/

					if(isset($_SESSION['falseSelectedId']) && $_SESSION['falseSelectedAnsId']){
						unset($_SESSION['falseSelectedId']);
						unset($_SESSION['falseSelectedAnsId']);
					}

					if(isset($_SESSION['isSetWrongAnswer'])){
						unset($_SESSION['isSetWrongAnswer']);
					}
					
					if(isset($_SESSION['showTestQuestionId'])){

						unset($_SESSION['showTestQuestionId']);

						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES);
						exit();
					}
					else{

						$recNo	=	$recNo+1;

						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/employee-test-questions.php?recNo=".$recNo);
						exit();
					}
				}
				else{
					if($falseSelectedQId				== 1){
						$wrongSelectedAns				=	$answerId;
						$_SESSION['falseSelectedId']	=	$questionId;
						$_SESSION['falseSelectedAnsId'] =   $answerId;
						$wrongAnswerText                =   "<font style='color:#ff0000;font-size:16px;font-weight:bold;'>Oops wrong answer</font>";
						$showEnterInterface				=	"";
						$showHelpText					=	"";
					}
					
				}
			}
			else{
				$errorMsg		=	"Please answer at least one question";
			}
	}
?>
<form name="answerTest" action="" method="POST">
	<table width="98%" action="" border="0" cellpadding="2" cellspacing="2">
	<?php
		if(!empty($errorMsg)){
	?>
	<tr>
		<tr>
			<td colspan="2" class="textstyle3">
				<font color="#ff0000;"><?php echo $errorMsg;?></font>
			</td>
		</tr>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
<?php
	}
?>
	<tr>			
		<td valign="top"  colspan="2" class="textstyle3">
			
			<b>Your Test Score: <?php echo $employeeOwnTestScore;?></b>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<?php

		$whereClause			  = "WHERE parentId=0";
		$andClause				  = "";
		$orderBy				  = "addedTime, addedOn";
		$queryString			  =	"";
		if(isset($_SESSION['showTestQuestionId'])){
			$andClause			  = " AND questionAnswerId=".$_SESSION['showTestQuestionId'];
		}
		elseif(isset($_SESSION['falseSelectedId'])){
			$andClause			  = " AND questionAnswerId=".$_SESSION['falseSelectedId'];
		}


		$start					  =	0;
		$recsPerPage	          =	1;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   = $whereClause.$andClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_test_questions";
		$pagingObj->selectColumns = "*";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/employee-test-questions.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords){

			$pagingObj->setPageNo();
			$recordSet		=   $pagingObj->getRecords();

			$i	=	$recNo;
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$questionAnswerId		=	$row['questionAnswerId'];
				$question				=	stripslashes($row['question']);
				$weightage				=	stripslashes($row['weightage']);
				$questionCategory		=	stripslashes($row['questionCategory']);
				$description			=	stripslashes($row['description']);
		?>
		<input type="hidden" name="askingQuestion[]" value="<?php echo $questionAnswerId;?>">
		<tr>			
			<td valign="top"  colspan="2" class="textstyle3">
				<!--<div id="third"><p id="container2"></p>
					<label id="lab3"><?php echo $i.")&nbsp;".$question;?></label>
				</div>-->
				<?php echo $i.")&nbsp;".$question;?>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class='textstyle1' valign="top">
							<?php
								$query1	=	"SELECT * FROM employee_test_questions WHERE parentId=$questionAnswerId AND answer <> '' ORDER BY questionAnswerId";
								$result1=	dbQuery($query1);
								if(mysqli_num_rows($result1)){
									$count1			=	0;
									while($row1		=	mysqli_fetch_assoc($result1)){
										$count1++;

										$answer			=	stripslashes($row1['answer']);
										$answer_id		=	$row1['questionAnswerId'];
										$isRightAnswer	=	$row1['isRightAnswer'];

										$checkBox		=	"";
										$disabled		=	"";
										if(isset($_SESSION['falseSelectedId']) && $_SESSION['falseSelectedId'] == $questionAnswerId){
											
											if(isset($_SESSION['falseSelectedAnsId']) && $answer_id  != $_SESSION['falseSelectedAnsId']){
												$disabled   =	"disabled";
											}
											else{
												$checkBox	=	"checked";
											}
										}
										else{
											if(isset($_SESSION['isSetWrongAnswer'])){
												if($_SESSION['isSetWrongAnswer'] == $count1){
													$checkBox	=	"checked";
												}
												else{
													$disabled   =	"disabled";
												}
											}
										}

										
										
								?>
								<label for="pretty<?php echo $count1;?>">
									<input type="radio" value="<?php echo $answer_id;?>" name="answerGiven[]" <?php echo $disabled;?> <?php echo $checkBox;?> id="pretty<?php echo $count1;?>" onclick="showDesc(<?php echo $isRightAnswer;?>, <?php echo $count1;?>);"> <span><?php echo "<b>".$answers_option_list[$count1].")</b>&nbsp; ".$answer;?></span><input type="hidden" name="rightAnswerGiven[<?php echo $answer_id;?>]" value="<?php echo $isRightAnswer;?>">
								</label>
								<?php
									}
								}
							?>
						</td>						
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="showRightWrongAns"><?php echo $wrongAnswerText;?></div>
			</td>						
		</tr>
		<tr>
			<td colspan="2">
				<div id="showHelpTextId" style="display:<?php echo $showHelpText;?>;" onmousedown='return false;' onselectstart='return false;'>
					<?php
						if(!empty($description)){		
					?>
					<table width="80%" action="" border="0" cellpadding="2" cellspacing="2">
						<tr>
							<td colspan="3" class="smalltext21">
								 <font style="text-align:jutify;font-size:16px"><b><u>Help Me:</u></b>&nbsp;<?php echo nl2br($description);?></font>
							</td>
						</tr>						
					</table>	
					<?php
						}	
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="showEnterHelpMeAns" style="display:<?php echo $showEnterInterface;?>">
					<table width="80%" action="" border="0" cellpadding="2" cellspacing="2">
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td colspan="3" class="smalltext22">
								 <font style="text-align:jutify;font-size:16px"><b>Type Above Help Text Exactly</u></b></font>
							</td>
						</tr>
						<tr>
							<td>
								<textarea name="typeHelpText" rows="4" cols="70" style="color:#333333;font-size:16px;font-family:verdana;border:2px solid #4f4f4f;" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off></textarea>
							</td>
						</tr>
					</table>
				
				</div>
			</td>						
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<?php
				
				
			}
		?>
		<tr>
			<td width="10%">
				<div id="setWrongAnswer"></div>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
				<input type='hidden' id="showWithDisabled" name='falseSelectedQId' value='<?php echo $falseSelectedQId;?>'>
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
			<td id="showingNextLink">
				<?php 
					if($falseSelectedQId != 1){
						echo $pagingObj->displayPaging($queryString);
					}
				?>
			</td>
		</tr>
		<?php		
		}
		else{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
			
	?>
	</table>
</form>
<?php
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>