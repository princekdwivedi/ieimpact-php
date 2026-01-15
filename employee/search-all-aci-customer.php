<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	if(!isset($_SESSION['hasManagerAccess']))
	{
		if(isset($_SESSION['searchCustomersForNonManager']))
		{
			$searchCustomersForNonManager	=	$_SESSION['searchCustomersForNonManager'];
		}
		else
		{
			$serachEndClause	=	"";
		}
	}
	else
	{
		$serachEndClause	=	"";
	}

	if(isset($_SESSION['employeeId'])){
		
		$searchName		=   strtolower($_GET["q"]);
		$searchName		=   makeDBSafe($searchName);

		$query	=	"SELECT firstName,lastName FROM members WHERE memberType='".CUSTOMERS."' AND (firstName LIKE '%$searchName%' OR lastName LIKE '%$searchName%') AND appraisalSoftwareType=2";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while ($row = @mysqli_fetch_array($result)) 
			{
				$firstName		=	$row['firstName'];
				$lastName		=   $row['lastName'];
				$customerName	=	$firstName." ".$lastName;
				$customerName	=	ucwords($customerName);

				echo $customerName."\n";
			}
		}
	}

?>