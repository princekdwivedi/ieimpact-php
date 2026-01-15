<?php
    if(VISITOR_IP_ADDRESS	!=	"73.15.50.126"){
		$query		=	"SELECT * FROM disable_website WHERE isActive=1 AND (disableEmployee=1 OR disableEmployeeCustomer=1)";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/disable-website-message.php");
			exit();
		}
	}
?>