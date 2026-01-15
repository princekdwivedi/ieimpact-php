<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	include(SITE_ROOT				.  "/classes/validate-fields.php");



	if(!in_array($s_employeeId,$a_managersTestQuestionAccess)){
				
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}


	$link							=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
		if(!empty($recNo))
		{
			$link					=	"?recNo=".$recNo;
		}
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}
	
	$pagingObj						=	new Paging();
	$validator					    =   new validate();
	$employeeObj					=	new employee();
	$validator						=  new validate();
	$questionAnswerId				=	0;
	$question						=  "";
	$questionWeightage				=  "";
	$errorMsg						=  "";
	$descriptions					=  "";
	$form							=  SITE_ROOT_EMPLOYEES . "/forms/add-edit-employee-test-questions.php";
	$headText						=  ":: Add a Question & its Answer ::";
	$answers_option_list			=  array("1"=>"A","B","C","D");
	$answer_list					=  array();
	$weightage_list					=  array();
	$right_answer					=  array();
	$questionCategory				=  "A";

	if(isset($_GET['questionAnswerId'])){
		$questionAnswerId		=	(int)$_GET['questionAnswerId'];
		if(!empty($questionAnswerId)){
			$query	=	"SELECT * FROM employee_test_questions WHERE questionAnswerId=$questionAnswerId AND parentId=0";
			$result=	dbQuery($query);
			if(mysqli_num_rows($result)){
				$headText			=  ":: Edit a Question & its Answer ::";
				$row				=	mysqli_fetch_assoc($result);
				$question			=  stripslashes($row['question']);
				$questionWeightage	=  stripslashes($row['weightage']);
				$descriptions	    =  stripslashes($row['description']);
				$questionCategory	=  stripslashes($row['questionCategory']);

				if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1){
					dbQuery("DELETE FROM employee_test_questions WHERE parentId=$questionAnswerId");

					dbQuery("DELETE FROM employee_test_questions WHERE questionAnswerId=$questionAnswerId AND parentId=0");

					
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES ."/add-employee-test-questions.php?recNo=$recNo#questionAnswerId");
					exit();

				}

				$query1			    =	"SELECT * FROM employee_test_questions WHERE parentId=$questionAnswerId AND answer <> '' ORDER BY questionAnswerId";
				$result1			=	dbQuery($query1);
				if(mysqli_num_rows($result1)){
					$countnn		=	0;
					while($row1		=	mysqli_fetch_assoc($result1)){
						$countnn++;
						$answer_list[$countnn]		=	stripslashes($row1['answer']);
						$weightage_list[$countnn]   =	$row1['weightage'];
						$right_answer[$countnn]     =	$row1['isRightAnswer'];		
					}
				}
			}
			
			
		}
	}

	if(isset($_REQUEST['formSubmitted'])){
		extract($_REQUEST);
		//pr($_REQUEST);
		$question				=  stripslashes($question);
		$questionWeightage		=  stripslashes($questionWeightage);

		$t_question				=	makeDBSafe($question);
		//$t_questionWeightage	=	makeDBSafe($questionWeightage);
		$t_descriptions	        =	makeDBSafe($descriptions);

		$validator ->checkField($question,"","Please enter question.");
       // $validator ->checkField($questionWeightage,"","Please enter question weightage.");
		$answer_list            = $_POST['answer_list'];
		//$weightage_list         = $_POST['weightage_list'];
		$right_answer           = $_POST['right_answer'];
		$ishavingAnyAnswer		=	0;
		foreach($answer_list as $k=>$answer){
			if(!empty($answer)){
				$ishavingAnyAnswer++;
			}
		}

		$totalRightAnswer		=	0;

		foreach($right_answer as $kk=>$vv){
			if($vv	==	1){
				$totalRightAnswer = $totalRightAnswer+1;
			}
		}
		if($ishavingAnyAnswer < 2){
			$validator->setError("Please enter at least 2 answers.");
		}
		if(empty($totalRightAnswer)){
			$validator->setError("Please select a right answer.");
		}
		elseif($totalRightAnswer > 1){
			$validator->setError("Please select only one right answer.");
		}
		$validator ->checkField($descriptions,"","Please enter help text.");
		$dataValid		 =	$validator ->isDataValid();
		if($dataValid)
		{
			if(empty($questionAnswerId)){
				dbQuery("INSERT INTO employee_test_questions SET question='$t_question',questionCategory='$questionCategory',description='$t_descriptions',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");

				$questionAnswerId	=	mysqli_insert_id($db_conn);
			}
			else{
				dbQuery("UPDATE employee_test_questions SET question='$t_question',questionCategory='$questionCategory',description='$t_descriptions' WHERE questionAnswerId=$questionAnswerId");
			}

			dbQuery("DELETE FROM employee_test_questions WHERE parentId=$questionAnswerId AND answer <> '' ");

			foreach($answer_list as $k=>$answer){
				if(!empty($answer)){
					$t_answer	 = makeDBSafe($answer);
					//$weightage   = $weightage_list[$k];
					$rightAnswer = $right_answer[$k];
					if(empty($weightage)){					
						$weightage=	0;
					}

					dbQuery("INSERT INTO employee_test_questions SET questionCategory='$questionCategory',answer='$t_answer',parentId=$questionAnswerId,isRightAnswer=$rightAnswer,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
				}
			}

			

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/add-employee-test-questions.php?recNo=$recNo#$questionAnswerId");
			exit();

		}
		else{
			$errorMsg	 =	$validator ->getErrors();
		}
	}

	include($form);

	$whereClause			  = "WHERE parentId=0";
	$orderBy				  = "addedTime DESC, addedOn DESC";
	$queryString			  =	"";


	$start					  =	0;
	$recsPerPage	          =	10;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   = $whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_test_questions";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/add-employee-test-questions.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords){

		$pagingObj->setPageNo();
		$recordSet		=   $pagingObj->getRecords();
?>
<br />
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="12">
			<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">Existing Questions</font>
		</td>
	</tr>
	<tr>
		<td width="4%" class="smalltext23"><b>Sr No</b></td>
		<td width="31%" class="smalltext23"><b>Question</b></td>
		<td width="30%" class="smalltext23"><b>Answers</b></td>
		<td width="30%" class="smalltext23"><b>Help Text</b></td>
		<td class="smalltext23"><b>Action</b></td>
	</tr>
	<tr>
		<td colspan="5"><hr style="height: 1px; color: #bebebe; width: 100% " /></td>
	</tr>
	<?php
		$i	=	$recNo;
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$questionAnswerId		=	$row['questionAnswerId'];
			$question				=	stripslashes($row['question']);
			$weightage				=	stripslashes($row['weightage']);
			$questionCategory		=	stripslashes($row['questionCategory']);
			$description			=	stripslashes($row['description']);
			
			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
	?>
	<tr height='20' <?php echo $bgColor;?>>
		<td class='smalltext23' valign="top">
			&nbsp;<?php echo $i.")";?><a name="<?php echo $questionAnswerId;?>"></a>
		</td>
		<td class='smalltext23' valign="top">
			<?php echo $question."&nbsp;(".$questionCategory.")";?>
		</td>
		<td valign="top">
			<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
				<?php
					$query1	=	"SELECT * FROM employee_test_questions WHERE parentId=$questionAnswerId AND answer <> '' ORDER BY questionAnswerId";
					$result1=	dbQuery($query1);
					if(mysqli_num_rows($result1)){
						$count1			=	0;
						while($row1		=	mysqli_fetch_assoc($result1)){
							$count1++;

							$answer		    =	stripslashes($row1['answer']);
							$t_weightage    =	$row1['weightage'];
							$t_isRightAnswer=	$row1['isRightAnswer'];

							if($t_isRightAnswer == 1){
								$answer     =   "<font style='color:#ff0000;'>".$answer."</font>";
							}
							
					?>
					<tr><td class='smalltext23' valign="top"><b><?php echo $answers_option_list[$count1];?>)</b>&nbsp;<?php echo $answer;?></td></tr>
					<?php
						}
					}
				?>
			</table>
		</td>
		<td class='smalltext2' valign="top">
			<?php echo $description;?>
		</td>
		<td class='smalltext2' valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-employee-test-questions.php?questionAnswerId=<?php echo $questionAnswerId;?>&recNo=<?php echo $recNo;?>" class='link_style6'>Edit</a>&nbsp;|&nbsp;<a onclick="deleteQuestion(<?php echo $questionAnswerId;?>,<?php echo $recNo;?>)" class='link_style6' style="cursor:pointer;">Delete</a>&nbsp;
		</td>
	</tr>	
	<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td colspan='9'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
	?>
</table>
<?php
	}
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>