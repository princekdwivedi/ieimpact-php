<?php
	if(!empty($s_hasPdfAccess) || empty($s_departmentId ))
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
?>