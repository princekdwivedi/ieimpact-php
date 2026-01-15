<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();

	$query	=  "select employee_works.employeeId from employee_works inner join employee_shift_rates on employee_works.employeeId=employee_shift_rates.employeeId where employee_shift_rates.departmentId=2 and employee_works.assignedWorkId=0 group by employee_works.employeeId";
	$result		=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		echo "<br>case1";
		while($row			=	mysql_fetch_assoc($result))
		{
			$employeeId		=	$row['employeeId'];
			
			$query1			=  "select platform from employee_works where assignedWorkId=0 AND employeeId=$employeeId group by platform AND isTransferred=0";
			$result1		=	mysql_query($query1);
			if(mysql_num_rows($result1))
			{
				echo "<br>case2";
				while($row1			=	mysql_fetch_assoc($result1))
				{
					$platform		=	$row1['platform'];

					$query2			=  "select customerId from employee_works where assignedWorkId=0 AND employeeId=$employeeId AND platform=$platform group by customerId";
					$result2		=	mysql_query($query2);
					if(mysql_num_rows($result2))
					{
						echo "<br>case3";
						while($row2			=	mysql_fetch_assoc($result2))
						{
							$customerId		=	$row2['customerId'];

							$startingDate	=	@mysql_result(mysql_query("SELECT workedOn FROM employee_works WHERE assignedWorkId=0 AND employeeId=$employeeId AND customerId=$customerId AND platform=$platform ORDER BY workId LIMIT 1"),0);

							$completedDate	=	@mysql_result(mysql_query("SELECT workedOn FROM employee_works WHERE assignedWorkId=0 AND employeeId=$employeeId AND customerId=$customerId AND platform=$platform ORDER BY workId DESC LIMIT 1"),0);

							$query3		=	"SELECT SUM(directLevel1) AS totalDirectLevel1,SUM(directLevel2) AS totalDirectLevel2,SUM(indirectLevel1) AS totalIndirectLevel1,SUM(indirectLevel2) AS totalIndirectLevel2,SUM(qaLevel1) AS totalQaLevel1,SUM(qaLevel2) AS totalQaLevel2,SUM(auditLevel1) AS totalAuditLevel1,SUM(auditLevel2) AS totalAuditLevel2  FROM employee_works WHERE employeeId=$employeeId AND customerId=$customerId AND platform=$platform AND assignedWorkId=0";
							$result3	=	mysql_query($query3);
							if(mysql_num_rows($result3))
							{
								echo "<br>case4";
								$row3				=	mysql_fetch_assoc($result3);
								$totalDirectLevel1	=	$row3['totalDirectLevel1'];
								$totalDirectLevel2	=	$row3['totalDirectLevel2'];
								$totalIndirectLevel1=	$row3['totalIndirectLevel1'];
								$totalIndirectLevel2=	$row3['totalIndirectLevel2'];
								$totalQaLevel1		=	$row3['totalQaLevel1'];
								$totalQaLevel2		=	$row3['totalQaLevel2'];
								$totalAuditLevel1	=	$row3['totalAuditLevel1'];
								$totalAuditLevel2	=	$row3['totalAuditLevel2'];
			
								if(empty($totalDirectLevel1))
								{
									$totalDirectLevel1	=	0;
								}
								if(empty($totalDirectLevel2))
								{
									$totalDirectLevel2	=	0;
								}
								if(empty($totalIndirectLevel1))
								{
									$totalIndirectLevel1	=	0;
								}
								if(empty($totalIndirectLevel2))
								{
									$totalIndirectLevel2	=	0;
								}
								if(empty($totalQaLevel1))
								{
									$totalQaLevel1	=	0;
								}
								if(empty($totalQaLevel2))
								{
									$totalQaLevel2	=	0;
								}
								if(empty($totalAuditLevel1))
								{
									$totalAuditLevel1	=	0;
								}
								if(empty($totalAuditLevel2))
								{
									$totalAuditLevel2	=	0;
								}
								$totalLines		=	0;
								$totalLines		=	$totalLines+$totalDirectLevel1+$totalDirectLevel2+$totalIndirectLevel1+$totalIndirectLevel2+$totalQaLevel1+$totalQaLevel2+$totalAuditLevel1+$totalAuditLevel2;

								if(!empty($totalLines))
								{
									echo "<br>case5";
									echo "<br><br>".$query4	=	"INSERT INTO assign_employee_works SET employeeId=$employeeId,platform=$platform,customerId=$customerId,comments='',direct1=$totalDirectLevel1,direct2=$totalDirectLevel2,indirect1=$totalIndirectLevel1,indirect2=$totalIndirectLevel2,qa1=$totalQaLevel1,qa2=$totalQaLevel2,audit1=$totalAuditLevel1,audit2=$totalAuditLevel2,totalLinesAssigned=$totalLines,assignedOn='$startingDate',status=2,acceptedOn='$startingDate',completedOn='$completedDate',assignedBy=3";
									//mysql_query($query4);
									//$assignedWorkId		=	//mysql_insert_id();

									echo "<br><br>".$query5	=	"UPDATE employee_works SET assignedWorkId=7,isTransferred=1 WHERE  employeeId=$employeeId AND customerId=$customerId AND platform=$platform AND assignedWorkId=0";
									//mysql_query($query5);
								}
							}
						}
					}
				}
			}
		}

		echo "success";
	}
	else
	{
		echo "no record available";
	}
?>