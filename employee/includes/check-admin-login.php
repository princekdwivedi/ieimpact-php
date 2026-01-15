<?php
	if(empty($s_hasAdminAccess))
	{
		if(!isset($urlRef))
		{
			$urlRef	=	SITE_URL_EMPLOYEES;
		}
		
		ob_clean();
		header("Location: ".$urlRef);
		exit();
	}
?>