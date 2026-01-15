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
		
		$searchOrder		=   strtolower($_GET["q"]);
		$searchOrder		=   makeDBSafe($searchOrder);

		$query					=	"SELECT orderAddress FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderAddress LIKE '%$searchOrder%' AND isVirtualDeleted=0 AND isDeleted=0 AND status IN (2,5,6)";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while ($row			=	@mysqli_fetch_array($result)) 
			{		
				echo stripslashes($row['orderAddress'])."\n";
			}
		}
	}
	
?>