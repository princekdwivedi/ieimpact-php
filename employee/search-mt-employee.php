<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	if(!isset($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
		
	if(isset($_SESSION['employeeId'])){
		
		$searchName		=   strtolower($_GET["q"]);
		$searchName		=   makeDBSafe($searchName);

		$query	=	"SELECT fullName FROM employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId WHERE employee_shift_rates.departmentId=1 AND fullName LIKE '%$searchName%' AND isActive=1";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while ($row = @mysqli_fetch_assoc($result)) 
			{
				$fullName		=	stripslashes($row['fullName']);
				$fullName		=	ucwords($fullName);

				echo $fullName."\n";
			}
		}
	}

?>