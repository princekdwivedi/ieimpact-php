<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT		    . "/classes/pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}

	list($year,$t_month,$days)	=	explode("-",$nowDateIndia);

	$searchMonth		=	$t_month;
	$searchYear			=	$year;

	if(isset($_GET['searchMonth']) && isset($_GET['searchYear'])){
		$searchMonth	=	$t_month    = $_GET['searchMonth'];
		$searchYear		=	$year = $_GET['searchYear'];
	}

	$month				=	$t_month;
	if($t_month < 10)
	{
		$month			=	substr($t_month,1);
	}

	$monthText			=	$a_month[$t_month];

	$whereClause		=   "WHERE month=$t_month AND year=$year";
	$orderBy			=	"totalProcessedOrders DESC";
	$andClause			=	"";
	
	$shortBy            =   2;
	
	if(isset($_GET['shortBy'])){
		$shortBy		=	$_GET['shortBy'];
	}

	if($shortBy			== 2){
		$shortByAverage	=	"arsort";
		$averageShortBy	=	"&shortBy=1";
	}
	else{
		$shortByAverage	=	"asort";
		$averageShortBy	=	"&shortBy=2";
	}
	$queryString		=	"&shortBy=".$shortBy;
	


	/////////////////////////////// FETCHED MONTHWISE DATA ////////////////////////////
	$a_monthwiseEmpData	=	array();
	$query				=	"SELECT * FROM employee_rating_score WHERE year=$year AND scoreType='individual' order by month ";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row			=	mysqli_fetch_assoc($result)){
			$t_employeeId	=	$row['employeeId'];
			$t_forMonth		=	$row['month'];
			$t_indScore	    =	$row['score'];
			$a_monthwiseEmpData[$t_forMonth][$t_employeeId] = $t_indScore;
		}
	}

?>
<form name="serachRatingByMonth" action="" method="GET">
	<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle3" width="32%">
				VIEW MONTHWISE EMPLOYEE/TEAM SCORE
			</td>
			<td width="15%">
				<select name="searchMonth">
					<?php
						foreach($a_serachMonths as $kk=>$vv){
							$select		    =	"";
							if($searchMonth ==	$kk){
								$select		=	"selected";
							}

							echo "<option value='$kk' $select>$vv</option>";
						}
					?>
				</select>--
				<select name="searchYear">
				<?php
					$fromYear	=	"2016";
					$toYear		=	date('Y');
					for($i=$fromYear;$i<=$toYear;$i++){
						$select		    =	"";
						if($searchYear  ==	$i){
							$select		=	"selected";
						}

						echo "<option value='$i' $select>$i</option>";
					}
				?>
				</select>
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	100;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  = $recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_rating_score INNER JOIN employee_details ON employee_rating_score.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_rating_score.*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-team-score.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{

		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i					  =	$recNo;

		$query				    = "SELECT * FROM employee_rating_score ".$whereClause." AND scoreType='team'";
		$result				     =  dbQuery($query);
		$row				     =  mysqli_fetch_assoc($result);
		$t_totalAwful			 =	stripslashes($row['totalAwful']);
		$t_totalPoor			 =	stripslashes($row['totalPoor']);
		$t_totalAwfulPoor		 =	stripslashes($row['totalAwfulPoor']);
		$t_totalProcessedOrders  =	stripslashes($row['totalProcessedOrders']);
		$t_score	             =	stripslashes($row['score']);


		$a_employeeScoreAverage  =  array();

		$queryToGetAverageScore     =   "SELECT SUM(score) as totalAverageScore,count(scoreId) as totalAverageMonth,employeeId FROM employee_rating_score GROUP BY employeeId"; 
		$averageResult				=   dbQuery($queryToGetAverageScore);
		if(mysqli_num_rows($averageResult)){
			while($averageResultRow	=	mysqli_fetch_assoc($averageResult)){
				$totalAverageScore  =   $averageResultRow['totalAverageScore'];
				$totalAverageMonth  =   $averageResultRow['totalAverageMonth'];
				$score_employee     =   $averageResultRow['employeeId'];

				$average            =   $totalAverageScore/$totalAverageMonth;
				$average            =   round($average,2);	

				$a_employeeScoreAverage[$score_employee] = 	$average;
			}
						
		}

?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'> 
	<tr>
		<td colspan="20" class="smalltext24"><b>Team score for <?php echo $a_serachMonths[$searchMonth].",".$searchYear;?> - Total Awful : <?php echo $t_totalAwful;?>&nbsp;|&nbsp;Total Poor : <?php echo $t_totalPoor;?>&nbsp;|&nbsp;Awful+Poor : <?php echo $t_totalAwfulPoor;?>&nbsp;|&nbsp;Total Orders : <?php echo $t_totalProcessedOrders;?>&nbsp;|&nbsp;Score : <?php echo $t_score;?>%</b></td>
	<tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr bgcolor="#373737" height="30">
		<td width="2%" style="text-align:center;" class="smalltext12">&nbsp;</td>
		<td width="15%" style="text-align:left;" class="smalltext12">Score For <?php echo $monthText.",".$year;?></td>
		<td width="7%" style="text-align:center;" class="smalltext12">Awful+Poor</td>
		<td width="8%" style="text-align:center;" class="smalltext12">Total Orders</td>
		<td width="5%" style="text-align:left;" class="smalltext12">Score</td>
		<td width="1%" style="text-align:left;" class="smalltext12">-</td>
		<?php
			foreach($a_month as $monthNo=>$showMonth){
		?>
		<td width="5%" style="text-align:center;" class="smalltext12"><?php echo $showMonth;?></td>
		<?php
			}
		?>
		<td style="text-align:center;" class="smalltext12"><a href="<?php echo SITE_URL_EMPLOYEES.'/view-team-score.php?'.$averageShortBy?>" class="link_style33">Average</td>

	</tr>
	<?php
		$a_employeeArray1			=	array();
		$a_employeeArray2			=	array();
		$a_employeeArray3			=	array();
		while($row1					=   mysqli_fetch_assoc($recordSet))
		{
			
			$employeeId				=	$row1['employeeId'];
			$employeeName			=	stripslashes($row1['fullName']);
			$scoreType			    =	stripslashes($row1['scoreType']);
			$totalAwful			    =	stripslashes($row1['totalAwful']);
			$totalPoor			    =	stripslashes($row1['totalPoor']);
			$totalAwfulPoor			=	stripslashes($row1['totalAwfulPoor']);
			$totalProcessedOrders	=	stripslashes($row1['totalProcessedOrders']);
			$score	                =	stripslashes($row1['score']);

			$a_employeeArray1[$employeeId] = $employeeName."<=>".$totalAwfulPoor."<=>".$totalProcessedOrders."<=>".$score;
	
				
			foreach($a_month as $monthNo=>$showMonth){
				$c_month	=	$monthNo;
				if($monthNo < 10)
				{
					$c_month=	substr($monthNo,1);
				}
				
				if(array_key_exists($c_month,$a_monthwiseEmpData)){
					
					$a_month_score = $a_monthwiseEmpData[$c_month];
					if(array_key_exists($employeeId,$a_month_score)){
						$displayMonthwise = $a_month_score[$employeeId]."%";
					}
					else{
						$displayMonthwise = "-";
					}
				}
				else{
					$displayMonthwise = "-";
				}

				$a_employeeArray2[$employeeId][$showMonth] = $displayMonthwise;
				
		
			}
			$average				=	"0.00%";
			
			if(!empty($a_employeeScoreAverage) && array_key_exists($employeeId,$a_employeeScoreAverage)){
		
				$average            =   $a_employeeScoreAverage[$employeeId];
			}
			$a_employeeArray3[$employeeId] = $average;
			
		}
		if(!empty($a_employeeArray1) && count($a_employeeArray1) > 0){
			$shortByAverage($a_employeeArray3, SORT_NUMERIC);

			foreach($a_employeeArray3 as $employeeId=>$average){
				$i++;
				$bgColor				=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=   "class='rwcolor2'";
				}
				$value					=	$a_employeeArray1[$employeeId];
				list($employeeName,$totalAwfulPoor,$totalProcessedOrders,$score)	=	explode("<=>",$value);

				$month_data				=	$a_employeeArray2[$employeeId];
		?>
		<tr height="23" <?php echo $bgColor;?>>
			<td class="smalltext2"><?php echo $i;?>)</td>
			<td class="smalltext2"><?php echo $employeeName;?></td>
			<td class="textstyle1"style="text-align:center;"><?php echo $totalAwfulPoor;?></td>
			<td class="textstyle1"style="text-align:center;"><?php echo $totalProcessedOrders;?></td>
			<td class="textstyle1"><?php echo $score;?>%</td>
			<td class="textstyle1">-</td>
			<?php
				foreach($month_data as $dd=>$mm){
			?>
			<td style="text-align:center;" class="smalltext2"><?php echo $mm;?></td>
			<?php
				}	
			?>
			<td style="text-align:center;" class="smalltext2"><?php echo $average;?>%</td>
		</tr>
		<?php
			}
		}
	 ?>
	 <tr>
		<td colspan="15%" style="text-align:right"><?php echo $pagingObj->displayPaging($queryString);?>&nbsp;&nbsp;</td>
	 </tr>
</table>
<?php
	}
	else{
		echo "<table width='22%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>No Records Found</b></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>