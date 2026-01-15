<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	$customerId					=	0;
	$updateQuickCheck			=	0;
	$markedQuickInsDone			=	"";

	if(isset($_SESSION['hasManagerAccess']) && isset($_SESSION['employeeId']))
	{
		if(isset($_GET['customerId'])){
			$customerId			=	$_GET['customerId'];
		}
		if(isset($_GET['updateQuickCheck'])){
			$updateQuickCheck	=	$_GET['updateQuickCheck'];
		}
		if(isset($_GET['markedQuickInsDone'])){
			$markedQuickInsDone	=	$_GET['markedQuickInsDone'];
		}

		if(!empty($customerId) && $updateQuickCheck == 1 && !empty($markedQuickInsDone)){
			dbQuery("UPDATE members SET easyNQuickInstructionsDone='$markedQuickInsDone' WHERE memberId=$customerId AND easyNQuickInstructionsDone=''");
?>
		EasyNQuick instructions Done : <b><?php echo ucwords($markedQuickInsDone);?></b>
<?php
		}
	}

?>