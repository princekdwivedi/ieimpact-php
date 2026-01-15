<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	include(SITE_ROOT	.	"/includes/common-array.php");
	session_start();

	if(isset($_SESSION['employeeId'])){
		
		$searchName				=   strtolower($_GET["q"]);
		$searchName				=	strtoupper($_GET["q"]);

		$a_serachedvalue		=	array();

		foreach($a_usaProvinces as $key=>$value)
		{
			if(!empty($key) && $value != "SELECT STATES")
			{
				list($stateName,$stateTimeZone)			=	explode("|",$value);

				if(strstr($stateName, $searchName))
				{
					$a_serachedvalue[]		=	$stateName;
				}
			}
		}
		
		if(!empty($a_serachedvalue))
		{
			foreach($a_serachedvalue as $k=>$v)
			{
				echo $v."\n";
			}
		}
	}

?>