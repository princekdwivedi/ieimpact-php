<?php
	if(empty($s_employeeId))
	{
		if(isset($urlRef))
		{
			$urlRef	=	SITE_URL_EMPLOYEES."/index.php?urlRef=".$urlRef;
		}
		else
		{
			$urlRef	=	SITE_URL_EMPLOYEES;
		}
		ob_clean();
		header("Location: ".$urlRef);
		exit();
	}
	else
	{
		$a_notInIds		=	array("449");
		if(isset($_SESSION['mtemployeeId']) && !empty($_SESSION['mtemployeeId']) && $s_employeeId != "449")
		{
			ob_clean();
			header("Location: ".SITE_URL_MTEMPLOYEES);
			exit();
		}
	}
	
?>