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
	$year						=  $searchYear = date('Y');
	
	if(isset($_GET['searchYear'])){
		$searchYear				=	$year = $_GET['searchYear'];
	}

	////////////////////////////// FETCHED MONTHWISE DATA ////////////////////////////
	$a_monthwiseEmpData	=	array();
	$query				=	"SELECT * FROM employee_rating_score WHERE year=$year AND scoreType='individual' AND employeeId=$s_employeeId order by month ";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result)){
			$t_employeeId			=	$row['employeeId'];
			$t_forMonth				=	$row['month'];
			$t_indScore				=	$row['score'];
			$t_totalAwfulPoor		=	$row['totalAwfulPoor'];
			$t_totalProcessedOrders	=	$row['totalProcessedOrders'];
		
			$a_monthwiseEmpData[$t_forMonth] = $t_indScore."|".$t_totalAwfulPoor."|".$t_totalProcessedOrders;
		}
	}

?>
<form name="serachRatingByMonth" action="" method="GET">
	<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle3" width="22%">
				VIEW MONTHWISE YOUR SCORE
			</td>
			<td>
				<select name="searchYear" onchange="document.serachRatingByMonth.submit();">
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
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
</form>
<br />
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'> 
	<tr>
		<td width="6%" class="textstyle1" style="text-align:left;"><b>Month</b></td>
		<td width="8%" class="textstyle1" style="text-align:left;"><b>Processed</b></td>
		<td width="8%" class="textstyle1" style="text-align:left;"><b>Awful+Poor</b></td>
		<td class="textstyle1" style="text-align:left;"><b>Score</b></td>
	</tr>	
	<tr>
		<td height="5"></td>
	</tr>
		<?php
			foreach($a_month as $monthNo=>$showMonth){
				$c_month	=	$monthNo;
				if($monthNo < 10)
				{
					$c_month=	substr($monthNo,1);
				}

				$score		=	"-";
				$awfulPoor	=	"-";
				$totalOrders=   "-";

				if(array_key_exists($c_month,$a_monthwiseEmpData)){
					$displayMonthwise = $a_monthwiseEmpData[$c_month];
					list($score,$awfulPoor,$totalOrders) = explode("|",$displayMonthwise);
					$score	=	$score."%";
				}				
		?>
		<tr>
			<td class="textstyle1" style="text-align:left;"><?php echo $showMonth;?></b></td>
			<td class="textstyle1" style="text-align:left;"><?php echo $totalOrders;?></td>
			<td class="textstyle1" style="text-align:left;"><?php echo $awfulPoor;?></td>
			<td class="textstyle1" style="text-align:left;"><?php echo $score;?></td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<?php
			}
		?>

	</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>