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
		
		$searchName				=   strtolower($_GET["q"]);
		if (strlen($searchName) != strlen(utf8_decode($searchName))) {
			$isEnglish 			  =  0;
		} else {
			$isEnglish 			  =  1;
		}
		if($isEnglish  == 1){

			$searchName				=   makeDBSafe($searchName);

			$query					=	"SELECT completeName FROM members WHERE memberType='".CUSTOMERS."' AND completeName LIKE '%$searchName%' AND isActiveCustomer=1 AND isTestAccount=0";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row = mysqli_fetch_assoc($result)) 
				{
					$customerName	=	ucwords(stripslashes($row['completeName']));
					echo $customerName."\n";
				}
			}
		}
		else{
			echo "Please use proper text to search\n";
		}
	}

?>