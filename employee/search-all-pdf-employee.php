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

		$query	=	"SELECT fullName FROM employee_details WHERE (firstName LIKE '%$searchName%' OR lastName LIKE '%$searchName%') AND isActive=1 AND hasPdfAccess=1";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while ($row = @mysqli_fetch_array($result)) 
			{
				$fullName		=	stripslashes($row['fullName']);
				$employeeName	=	ucwords($fullName);

				echo $fullName."\n";
			}
		}
	}

?>