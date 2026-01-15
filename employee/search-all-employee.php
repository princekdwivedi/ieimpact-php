<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	//ini_set('display_errors', '1');
	require_once("../root.php");
	session_start();

	if(!isset($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
		
	if(isset($_SESSION['employeeId'])){
		
		$searchName				=   strtolower($_GET["q"]);
		$searchName				=   makeDBSafe($searchName);

		$query					=	"SELECT fullName FROM employee_details WHERE (firstName LIKE '%$searchName%' OR lastName LIKE '%$searchName%') AND (hasPdfAccess=1 OR enrollAs='pdf')";
		$result	=	dbQuery($query);
		if(@mysqli_num_rows($result))
		{
			while ($row 		= @mysqli_fetch_assoc($result)) 
			{
				$fullName		=	stripslashes($row['fullName']);
				$fullName		=	ucwords($fullName);

				echo $fullName."\n";
			}
		}
	}

?>