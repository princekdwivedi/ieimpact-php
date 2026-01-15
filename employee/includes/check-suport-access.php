<?php
	$supportAccessFor	=	"";
	$a_supportAccessFor	=	"";
	$query			=	"SELECT * FROM assign_support WHERE employeeId=$s_employeeId AND assignedForIds <> ''";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$row				=	mysql_fetch_assoc($result);
		$assignedForIds		=	$row['assignedForIds'];
		$supportAccessFor	=	$assignedForIds;
		$pos				=	strpos($assignedForIds, ",");
		if($pos == true)
		{
			$a_supportAccessFor		=	explode(",",$assignedForIds);
		}
		else
		{
			$a_supportAccessFor		=	array($assignedForIds);
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
?>