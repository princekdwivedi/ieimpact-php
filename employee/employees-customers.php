<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	if(isset($_SESSION['employeeId']))
	{
		$s_employeeId	=	$_SESSION['employeeId'];
	}
	else
	{
		$s_employeeId	=	0;
	}
	if(isset($_SESSION['hasManagerAccess']))
	{
		$s_hasManagerAccess	=	$_SESSION['hasManagerAccess'];
	}
	else
	{
		$s_hasManagerAccess	=	0;;
	}
	$searchCustomerName		=   strtolower($_GET["q"]);
	$a_searchEmployees		=	array();
	
	if(isset($_SESSION['hasManagerAccess']) && $_SESSION['hasManagerAccess'] == 1)
	{
		$query		=	"SELECT firstName,lastName FROM pdf_clients_employees INNER JOIN members ON pdf_clients_employees.customerId=members.memberId WHERE (firstName LIKE '%$searchCustomerName%' OR lastName LIKE '%$searchCustomerName%') GROUP BY lastName";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			while($row			=	mysql_fetch_assoc($result))
			{
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$name			=	$firstName." ".$lastName;
				
				$a_searchEmployees[]	=	$name;
			}
		}
	}
	else
	{
		$query		=	"SELECT firstName,lastName FROM pdf_clients_employees INNER JOIN members ON pdf_clients_employees.customerId=members.memberId WHERE (firstName LIKE '%$searchCustomerName%' OR lastName LIKE '%$searchCustomerName%') AND pdf_clients_employees.employeeId=$s_employeeId GROUP BY lastName";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			while($row			=	mysql_fetch_assoc($result))
			{
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$name			=	$firstName." ".$lastName;
				
				$a_searchEmployees[]	=	$name;
			}
		}
	}

	if(!empty($a_searchEmployees))
	{
		foreach($a_searchEmployees as $k=>$v)
		{
			echo $v."\n";
		}
	}

?>