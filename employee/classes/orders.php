<?php
	class orders
	{
		//Function To Accepet A Customer Order
		function acceptCustomerOrder($orderId,$memberId,$employeeId)
		{
			$query	=	"SELECT orderId FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND status=0";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$employeeName	=	$this->getQueryResult("SELECT fullName FROM employee_details WHERE employeeId=$employeeId","fullName");
				$employeeName	=	makeDBSafe($employeeName);
				
				dbQuery("UPDATE members_orders SET status=1,acceptedBy=$employeeId,acceeptedByName='$employeeName',assignToEmployee='".CURRENT_DATE_INDIA."',assignToTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$memberId");
				
				$performedTask	=	"Accepting New Order ID - ".$orderId;
				
				$this->trackEmployeeWork($orderId,$employeeId,$performedTask);
				$this->deductOrderRelatedCounts('newOrders');

				return true;
			}
			else
			{
				return false;
			}
		}
		//Function to tracking employees order works
		function trackEmployeeWork($orderId,$employeeId,$performedTask)
		{
			$performedTask	=	makeDBSafe($performedTask);
			dbQuery("INSERT INTO order_employee_works SET orderId=$orderId,performedTask='$performedTask',employeeId=$employeeId,date='".CURRENT_DATE_INDIA."',time='".CURRENT_TIME_INDIA."',ip='".VISITOR_IP_ADDRESS."'");
			
			return true;
		}
		//Function Get A Customer Order Details
		function getOrderDetails($orderId,$memberId)
		{
			$andClause	    =	"";
			if($orderId    !=  "187118"){
				$andClause	=	" AND members_orders.isVirtualDeleted =0";
			}
			
			$query	=	"SELECT members_orders.*,firstName,lastName,completeName,email,state,secondaryEmail,folderId,appraisalSoftwareType,noEmails,refferedBy,splInstructionToEmployee,easyNQuickInstructionsDone,splInstructionOfCustomer,addedInstructionsOn,phone,state,isReplyFileInEmail,phone,isOptedForSms,instructionsUpdatedOn,totalOrdersPlaced,isVocalCustomer,uniqueEmailCode,specialRateCustomer,isAllowedRewards,members.aLaModeCustomerID,ratingFileExt,ratingFileName,hasUploadedRatingFile,rateGiven,members.aLaModeCustomerID as customerAlaModeId,members.averageTimeTaken,needTatExplanation FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$memberId".$andClause;
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function To Get A Order Status
		function getOrderStatus($orderId,$memberId)
		{
			$status 	=	$this->getQueryResult("SELECT status FROM members_orders WHERE orderId=$orderId AND memberId=$memberId","status");

			return $status;
		}
		//Function To Get A reply Order Details
		function getReplyOrderDetails($orderId,$memberId)
		{
			$query	=	"SELECT * FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasRepliedFileUploaded=1";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function marked as order complted time from placed to cpmpleted
		function calculateOrderCompletedTat($orderId)
		{
			$isBeforeOrLateTime	=	1;//1 for before or on time and 2 is for Delayed time
			$differenceTiming	=	0;//Timing difference
			$completedMin		=	0;//Completed in mint
			
			$query		=	"SELECT employeeWarningDate,employeeWarningTime,orderAddedOn,orderAddedTime FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row				=	mysqli_fetch_assoc($result);
				$employeeWarningDate=	$row['employeeWarningDate'];
				$employeeWarningTime=	$row['employeeWarningTime'];
				$orderAddedOn		=	$row['orderAddedOn'];
				$orderAddedTime		=	$row['orderAddedTime'];
				
				$currentDate		=	CURRENT_DATE_INDIA;
				$currentTime		=	CURRENT_TIME_INDIA;

				$completedMin		=	timeBetweenTwoTimes($orderAddedOn,$orderAddedTime,$currentDate,$currentTime);

				if($currentDate < $employeeWarningDate)
				{
					$differenceTiming		=	timeBetweenTwoTimes($currentDate,$currentTime,$employeeWarningDate,$employeeWarningTime);
				}
				elseif($currentDate			==	$employeeWarningDate)
				{
					if($currentTime			<=  $employeeWarningTime)
					{
						$differenceTiming	=	timeBetweenTwoTimes($currentDate,$currentTime,$employeeWarningDate,$employeeWarningTime);
					}
					else
					{
						$differenceTiming	=	timeBetweenTwoTimes($employeeWarningDate,$employeeWarningTime,$currentDate,$currentTime);

						 $isBeforeOrLateTime=	2;
					}
				}
				else
				{
					$differenceTiming		=	timeBetweenTwoTimes($employeeWarningDate,$employeeWarningTime,$currentDate,$currentTime);

					$isBeforeOrLateTime		=	2;
				}
			}

			$differenceTiming	=	round($differenceTiming,2);
			$completedMin		=	round($completedMin,2);
			
			dbQuery("UPDATE members_orders SET isAddedTatTiming=1,isCompletedOnTime=$isBeforeOrLateTime,beforeAfterTimingMin='$differenceTiming',orderCompletedTat='$completedMin' WHERE orderId=$orderId");


			return true;
		}

		//Function To Marked Order As Qa Done
		function markOrderQaDone($orderId,$replyId,$memberId,$employeeId,$employeeName='')
		{
			$query	=	"SELECT replyId FROM members_orders_reply INNER JOIN members_orders ON members_orders_reply.orderId=members_orders.orderId WHERE members_orders_reply.memberId=$memberId AND hasRepliedFileUploaded=1 AND hasQaDone=0 AND status=1 AND members_orders_reply.replyId=$replyId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				dbQuery("UPDATE members_orders_reply SET hasQaDone=1,qaDoneBy=$employeeId,qaDoneOn='".CURRENT_DATE_INDIA."',qaDoneTime='".CURRENT_TIME_INDIA."',employeeId=$employeeId,orderCompletedOn='".CURRENT_DATE_INDIA."',orderCompletedTime='".CURRENT_TIME_INDIA."' WHERE replyId=$replyId AND orderId=$orderId AND memberId=$memberId AND hasQaDone=0");

				dbQuery("UPDATE members_orders SET status=2,orderCompletedOn='".CURRENT_DATE_INDIA."',orderCompletedEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',orderCompletedEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',qaDoneById=$employeeId,qaDoneByName='$employeeName',completedTime='".CURRENT_TIME_INDIA."',completedTimeEst='".CURRENT_TIME_CUSTOMER_ZONE."' WHERE orderId=$orderId AND memberId=$memberId");
				
				$performedTask	=	"Qa Done And Marked As Compelted For Order ID - ".$orderId;
				
				$this->trackEmployeeWork($orderId,$employeeId,$performedTask);
				$this->calculateOrderCompletedTat($orderId);

				dbQuery("UPDATE employee_details SET totalOrderQaDone=totalOrderQaDone+1 WHERE employeeId=$employeeId");

				if($acceptedBy	=	$this->getOrderAcceptedBY($orderId,$memberId))
				{
					dbQuery("UPDATE employee_details SET totalOrderProcessedDone=totalOrderProcessedDone+1 WHERE employeeId=$acceptedBy");
				}

				return true;
			}
			else
			{
				return false;
			}
		}
		//Function to get a order feedback details
		function getOrderFeedbackDetails($orderId,$memberId)
		{
			$query	=	"SELECT * FROM other_order_files WHERE orderId=$orderId AND memberId=$memberId AND uploadingFor=2";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get existing messages
		function getOrderMessages($orderId,$memberId)
		{
			$query		=	"SELECT members_employee_messages.*,fullName from members_employee_messages LEFT JOIN employee_details ON members_employee_messages.messageRepliedMarkedBy=employee_details.employeeId WHERE orderId=$orderId AND memberId=$memberId ORDER BY members_employee_messages.addedOn DESC,members_employee_messages.addedTime DESC";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get emails of all the managers
		function getAllMangersEmails()
		{
			$a_email	=	array();
			$query		=	"SELECT email,firstName,lastName FROM members WHERE memberType='".MANAGERS."' AND isActiveCustomer=1 AND isJunkMember=0 ORDER BY firstName";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$email		=	$row['email'];
					$firstName	=	$row['firstName'];
					$lastName	=	$row['lastName'];


					$memberName	=	$firstName." ".$lastName;
					$memberName	=	ucwords($memberName);

					$a_email[]	=	$email."|".$memberName;
				}
				return $a_email;

			}
			else
			{
				return false;
			}
		}

		//Function to get emails of all the managers
		function getMangersOnlyEmails()
		{
			$a_email	=	array();
			$query		=	"SELECT email,firstName,lastName FROM members WHERE memberType='".MANAGERS."' AND isActiveCustomer=1 AND isJunkMember=0 ORDER BY firstName";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$email		=	$row['email'];
				
					$a_email[]	=	$email;
				}
				$emails			=	implode(",",$a_email);

				return $emails;

			}
			else
			{
				return false;
			}
		}

		//Function to check a order accepted by
		function getOrderAcceptedBY($orderId,$memberId)
		{
			$query		=	"SELECT acceptedBy FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND status <> 0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$acceptedBy	=	$row['acceptedBy'];

				return $acceptedBy;
			}
			else
			{
				return false;
			}
		}
		//Function to check a order accepted by
		function getOrderQaBY($orderId,$memberId)
		{
			$query		=	"SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasQaDone=1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$qaDoneBy	=	$row['qaDoneBy'];

				return $qaDoneBy;
			}
			else
			{
				return false;
			}
		}
		//Function to check whether file is replied or not for a order
		function getRepliedStatus($orderId,$memberId)
		{
			$query		=	"SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$hasRepliedFileUploaded	=	$row['hasRepliedFileUploaded'];

				return $hasRepliedFileUploaded;
			}
			else
			{
				return false;
			}
		}
		//Function to get all the customers link
		function getAllCustomersNames()
		{
			$a_allCustomersName	=	array();
			$query		=	"SELECT completeName,memberId FROM members WHERE isJunkMember=0 AND isActiveCustomer=1 ORDER BY firstName";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row				=	mysqli_fetch_assoc($result))
				{
					$memberId			=	$row['memberId'];
					$completeName		=	stripslashes($row['completeName']);

					$a_allCustomersName[$memberId]	=	$completeName;
				}

				return $a_allCustomersName;
			}
			else
			{
				return false;
			}
		}
		//Function to get all pdf employees first name
		function getAllPdfEmployeesFirstNames()
		{			
			$a_employees=	array();
			$query		=	"SELECT employeeId,firstName FROM employee_details WHERE hasPdfAccess=1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$employeeId					=	$row['employeeId'];
					$firstName					=	stripslashes($row['firstName']);

					$a_employees[$employeeId]	=	$firstName;
				}
			}
			return $a_employees;
		}
		//Function to check accepetd and didnt replied any order file yet
		function checkAcceptedReplyOrder($employeeId)
		{
			$a_pendingIds	=	array();
			$query		=	"SELECT orderId FROM members_orders where acceptedBy=$employeeId and status=1 AND isDeleted=0 AND isVirtualDeleted=0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];
					$a_pendingIds[$orderId]	=	$orderId;
				}
				$totalPendings		=	count($a_pendingIds);
				$pendingIds			=	implode(",",$a_pendingIds);

				$totalReplied		=	$this->getQueryResult("SELECT COUNT(replyId) as total FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND hasQaDone=0 AND orderId IN ($pendingIds)","total");
				if(empty($totalReplied))
				{
					$totalReplied	=	0;
				}

				$availablePending	=	$totalPendings-$totalReplied;
				if(!empty($availablePending))
				{
					return $availablePending;
				}
				else
				{
					return false;
				}

			}
			else
			{
				return false;
			}
		}
		//Function to unaccept customer order
		function unacceptCustomerOrder($orderId,$memberId)
		{
			$query	=	"SELECT orderId,acceptedBy FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND status=1";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row				=	mysqli_fetch_assoc($result);
				$existingAccepted	=	$row['acceptedBy'];
				$hasReplied		    =	$this->getQueryResult("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId","hasRepliedFileUploaded");
				if(empty($hasReplied))
				{
					$hasReplied		=	0;
				}
				if(empty($hasReplied))
				{
					dbQuery("UPDATE members_orders SET status=0,acceptedBy=0,acceeptedByName='',assignToEmployee='0000-00-00',assignToTime='00:00:00',isReadInstructions=0,isReadEmployeeNote=0 WHERE orderId=$orderId AND memberId=$memberId AND status=1");

					dbQuery("DELETE FROM order_tat_explanation WHERE orderId=$orderId");
					
					$performedTask	=	"Unaccepting Order ID - ".$orderId;
					if(empty($existingAccepted)){
						$existingAccepted	=	0;
					}
				
					//dbQuery("UPDATE orders_related_counts SET newOrders=newOrders+1");
					
					$this->trackEmployeeWork($orderId,$existingAccepted,$performedTask);

					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		//Function to get total orders for an employee
		function getCustomerTotalOrders($memberId)
		{
			$total 	=	$this->getQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId","total");

			return $total;
		}

		//Function to get total completed orders for an employee
		function getCustomerTotalCompletedOrders($memberId)
		{
			$total 	=	$this->getQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId AND status IN (2,5,6)","total");

			return $total;
		}
		
		//Function to get customer's feedback text
		function getFeedbackText()
		{
			$a_existingRatings	=	array();
			$query				=	"SELECT * FROM feedback_rate_text WHERE feedbackText <> '' ORDER BY feedbackTextId";
			$result				=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$feedbackTextId	=	$row['feedbackTextId'];
					$feedbackText	=	stripslashes($row['feedbackText']);
					$a_existingRatings[$feedbackTextId]	=	$feedbackText;
				}
				return $a_existingRatings;
			}
			else
			{
				return false;
			}
		}
		//Function to get qa rate details
		function getOrderQaRate($orderId)
		{
			$query	=	"SELECT * FROM employee_miscellaneous_details WHERE orderId=$orderId";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to check order checked or not
		function isOrderChecked($orderId)
		{
			$query	=	"SELECT * FROM checked_customer_orders WHERE orderId=$orderId";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the new customers
		function getAllNewCustomers($searchOrderUpto)
		{
			$a_newCustomers	=	array();
			$query	=	"SELECT count(orderId) as TotalOrder,memberId FROM members_orders GROUP BY memberId HAVING TotalOrder < $searchOrderUpto";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			=	mysqli_fetch_assoc($result))
				{
					$memberId					=	$row['memberId'];
					$a_newCustomers[$memberId]	=	$memberId;
				}
				$a_newCustomers	=	implode(",",$a_newCustomers);

				return $a_newCustomers;
			}
			else
			{
				return false;
			}
		}
		//Function to get all the trial customers
		function getAllTrialCustomers($searchFrom,$searchOrderUpto)
		{
			$a_trailCustomers	=	array();
			$query	=	"SELECT count(orderId) as TotalOrder,memberId FROM members_orders GROUP BY memberId HAVING TotalOrder >= $searchFrom AND TotalOrder <= $searchOrderUpto";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			=	mysqli_fetch_assoc($result))
				{
					$memberId		=	$row['memberId'];
					$a_trailCustomers[$memberId]	=	$memberId;
				}
				$a_trailCustomers	=	implode(",",$a_trailCustomers);

				return $a_trailCustomers;
			}
			else
			{
				return false;
			}
		}
		//Function to get log prep order employee details
		function employeeLogPrepOrdersDetails($orderId) 
		{
			$query	= "SELECT * FROM employee_log_prep_orders WHERE orderId=$orderId";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get previous order messages
		function previousOrdersMessages($orderId,$memberId,$limit,$addedOn="")
		{
			$andClause		=	"";
			if($addedOn != NULL && $addedOn != "0000-00-00")
			{
				$andClause	=	" AND addedOn >= '$addedOn'";
			}
			$query	=	"SELECT * FROM members_employee_messages WHERE orderId <> $orderId AND messageBy='".CUSTOMERS."' AND memberId=$memberId AND message <> '' AND orderId < $orderId AND isDeleted=0 AND isVirtualDeleted=0 AND orderId <> 0".$andClause." ORDER BY addedOn DESC LIMIT $limit";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get previous order messages
		function previousAllOrdersMessages($memberId,$limit)
		{
			$query	=	"SELECT * FROM members_general_messages WHERE memberId=$memberId AND messageRelatedOrder='' AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND message <> '' ORDER BY generalMsgId DESC LIMIT $limit";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get previous order qa comments to employee
		function previousOrdersQaComments($orderId,$memberId,$acceptedBy,$limit)
		{
			$query	=	"SELECT employee_miscellaneous_details.*,members_orders.memberId FROM employee_miscellaneous_details INNER JOIN  members_orders ON employee_miscellaneous_details.orderId=members_orders.orderId WHERE employee_miscellaneous_details.orderId <> $orderId AND qaRateMessage <> '0' AND employee_miscellaneous_details.orderId < $orderId AND employee_miscellaneous_details.orderId <> 0 AND members_orders.acceptedBy=$acceptedBy ORDER BY miscellaneousId LIMIT $limit";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get previous order qa comments to employee with message
		function previousOrdersQaCommentsWithMessage($orderId,$memberId,$acceptedBy,$limit)
		{
			$query	=	"SELECT employee_miscellaneous_details.*,members_orders.memberId FROM employee_miscellaneous_details INNER JOIN  members_orders ON employee_miscellaneous_details.orderId=members_orders.orderId WHERE employee_miscellaneous_details.orderId <> $orderId AND qaRateMessage <> '0' AND employee_miscellaneous_details.orderId < $orderId AND employee_miscellaneous_details.orderId <> 0 AND members_orders.acceptedBy=$acceptedBy AND qaRateMessage <> '' ORDER BY miscellaneousId LIMIT $limit";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//function to get employee messages
		function getOrderEmployeeMessages($orderId)
		{
			$query		=	"SELECT employee_order_customer_messages.*,fullName from employee_order_customer_messages INNER JOIN employee_details ON employee_order_customer_messages.messageBy=employee_details.employeeId WHERE messageType=1 AND messageFor=$orderId ORDER BY messageId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//function to get previous employees messages
		function previousOrdersEmployeesMessages($orderId,$memberId,$limit)
		{
			$query	=	"SELECT employee_order_customer_messages.*,orderAddress,fullName FROM employee_order_customer_messages INNER JOIN members_orders ON employee_order_customer_messages.messageFor=members_orders.orderId INNER JOIN employee_details ON employee_order_customer_messages.messageBy=employee_details.employeeId WHERE messageFor <> $orderId AND memberId=$memberId AND messageFor < $orderId AND isDeleted=0 AND isVirtualDeleted=0 AND orderId <> 0 AND messageType=1 ORDER BY messageId LIMIT $limit";
			$result	=  dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get customers last orders ratings
		function previousOrdersCustomerRatingComments($orderId,$memberId,$limit,$rateGivenOn="")
		{
			$andClause	=	"";
			if($rateGivenOn != NULL && $rateGivenOn != "0000-00-00")
			{
				$andClause	=	" AND rateGivenOn >= '$rateGivenOn'";
			}
			$query		=	"SELECT orderId,orderAddress,rateGiven,memberRateMsg,rateGivenOn,rateGivenTime FROM members_orders WHERE orderId <> $orderId AND memberId=$memberId AND rateGiven <> 0".$andClause." AND isRateCountingEmployeeSide='yes' ORDER  BY rateGivenOn DESC LIMIT  $limit";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get is having customer order messages
		function isHavingOrderMessages($memberId,$orderId)
		{
			$total		=	$this->getQueryResult("SELECT COUNT(*) as total FROM members_employee_messages WHERE messageBy='".CUSTOMERS."' AND memberId=$memberId AND orderId=$orderId","total");
			if(empty($total))
			{
				$total	=	0;
			}
			return $total;
		}
		//Function to get all the new messages received in last 30 minitues by customer
		function getCustomersMostRecentMessages($addedOn,$addedTime,$maxSearchMemberOrderId=0)
		{
			if(empty($maxSearchMemberOrderId)){
				$maxSearchMemberOrderId =	MAX_SEARCH_MEMBER_ORDERID;
			}
			$a_customersHavingMessages	=	array();
			$query						=	"SELECT memberId FROM members_employee_messages  WHERE members_employee_messages.orderId > ".$maxSearchMemberOrderId." AND messageBy='".CUSTOMERS."' AND addedOn >= '$addedOn' AND addedTime >= '$addedTime' AND isVirtualDeleted=0 AND isRepliedMessage=0 GROUP BY memberId ORDER BY addedTime";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			= mysqli_fetch_assoc($result))
				{
					$memberId		=	$row['memberId'];
					$completeName	=	$this->getQueryResult("SELECT completeName FROM members WHERE memberId=$memberId","completeName");

					$t_completeName	=	$completeName;
					$completeName	=	stripslashes($completeName);
					
					$a_customersHavingMessages[$memberId]	=	"<a href='".SITE_URL_EMPLOYEES."/pdf-customer-messages.php?showingForMember=$t_completeName&unrepliedMsg=1#second' class='link_style14'>".$completeName."</a>";
				}
				
			}
			$query1					=	"SELECT memberId FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0 AND status=0 AND addedOn >= '$addedOn' AND addedTime >= '$addedTime' AND employeeSendingFirstMsg=0 GROUP BY memberId ORDER BY addedTime";
			$result1				=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{
				while($row1			= mysqli_fetch_assoc($result1))
				{
					$memberId1		=	$row1['memberId'];
					$completeName1	=	$this->getQueryResult("SELECT completeName FROM members WHERE memberId=$memberId1","completeName");


					$t_completeName1=	$completeName1;
					$completeName1	=	stripslashes($completeName1);
						
					$a_customersHavingMessages[$memberId1]	=	"<a href='".SITE_URL_EMPLOYEES."/pdf-customer-messages.php?showingForMember=$t_completeName1&unrepliedGeneralMsg=1#fifth' class='link_style14'>".$completeName1."</a> <font color='#ff000;'>[Gen Msg.]</font>";
				}
				
			}

			if(!empty($a_customersHavingMessages))
			{
				return $a_customersHavingMessages;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the new messages received in last 30 minitues by customer
		function getCustomersMostRecentMessagesNew($addedOn,$addedTime)
		{
			$a_customersHavingMessages	=	array();
			$query						=	"SELECT members_employee_messages.memberId,completeName FROM members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId WHERE members_employee_messages.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND messageBy='".CUSTOMERS."' AND members_employee_messages.addedOn >= '$addedOn' AND members_employee_messages.addedTime >= '$addedTime' AND isVirtualDeleted=0 AND isRepliedMessage=0 GROUP BY members_employee_messages.memberId ORDER BY members_employee_messages.addedTime";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			= mysqli_fetch_assoc($result))
				{
					$memberId		=	$row['memberId'];
					$completeName	=	$row['completeName'];

					$t_completeName	=	$completeName;
					$completeName	=	stripslashes($completeName);
					
					$a_customersHavingMessages[$memberId]	=	"<a href='".SITE_URL_EMPLOYEES."/pdf-customer-messages.php?showingForMember=$t_completeName&unrepliedMsg=1#second' class='link_style14'>".$completeName."</a>";
				}
				
			}
			$query1					=	"SELECT members_general_messages.memberId,completeName FROM members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0 AND status=0 AND members_general_messages.addedOn >= '$addedOn' AND members_general_messages.addedTime >= '$addedTime' AND employeeSendingFirstMsg=0 GROUP BY members_general_messages.memberId ORDER BY members_general_messages.addedTime";
			$result1				=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{
				while($row1			= mysqli_fetch_assoc($result1))
				{
					$memberId1		=	$row1['memberId'];
					$completeName1	=	$row['completeName'];

					$t_completeName1=	$completeName1;
					$completeName1	=	stripslashes($completeName1);
						
					$a_customersHavingMessages[$memberId1]	=	"<a href='".SITE_URL_EMPLOYEES."/pdf-customer-messages.php?showingForMember=$t_completeName1&unrepliedGeneralMsg=1#fifth' class='link_style14'>".$completeName1."</a> <font color='#ff000;'>[Gen Msg.]</font>";
				}
				
			}

			if(!empty($a_customersHavingMessages))
			{
				return $a_customersHavingMessages;
			}
			else
			{
				return false;
			}
		}
		//Function to get all the new messages received in last 30 minitues by customer
		function getLastOrderMessagesByCustomers($addedOn,$addedTime)
		{
			$a_customersHavingMessages	=	array();
			$query						=	"SELECT members_employee_messages.memberId,completeName FROM members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId WHERE messageBy='".CUSTOMERS."' AND members_employee_messages.addedOn >= '$addedOn' AND members_employee_messages.addedTime >= '$addedTime' AND isVirtualDeleted=0 GROUP BY members_employee_messages.memberId ORDER BY members_employee_messages.addedTime";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	= mysqli_fetch_assoc($result))
				{
					$memberId		=	$row['memberId'];
					$completeName	=	stripslashes($row['completeName']);
					
					$a_customersHavingMessages[$memberId]	=	$completeName;
				}
				
			}
			$query					=	"SELECT members_general_messages.memberId,completeName FROM members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0 AND members_general_messages.status=0 AND members_general_messages.addedOn >= '$addedOn' AND members_general_messages.addedTime >= '$addedTime' GROUP BY members_general_messages.memberId ORDER BY members_general_messages.addedTime";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	= mysqli_fetch_assoc($result))
				{
					$memberId		=	$row['memberId'];
					$completeName	=	stripslashes($row['completeName']);
					
					$a_customersHavingMessages[$memberId]	=	$completeName."[Gen Msg.]";
				}
				
			}

			if(!empty($a_customersHavingMessages))
			{
				return $a_customersHavingMessages;
			}
			else
			{
				return false;
			}
		}
		//New order messages received if any within time limit
		function getLastOrderMessages($orderId,$memberId,$addedOn,$addedTime)
		{
			$query		=	"SELECT messageId FROM members_employee_messages WHERE messageBy='".CUSTOMERS."' AND addedOn >= '$addedOn' AND addedTime >= '$addedTime' AND isVirtualDeleted=0 AND memberId=$memberId AND orderId=$orderId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Old order messages received if any within time limit
		function getOrderOldMessages($orderId,$memberId,$addedOn,$addedTime)
		{
			$query		=	"SELECT messageId FROM members_employee_messages WHERE messageBy='".CUSTOMERS."' AND addedOn <= '$addedOn' AND addedTime <= '$addedTime' AND isVirtualDeleted=0 AND memberId=$memberId AND orderId=$orderId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Order internal messages received if any within time limit
		function getOrderInternalMessages($orderId,$addedOn)
		{
			$query		=	"SELECT messageId FROM employee_order_customer_messages WHERE messageType=1 AND addedOn <= '$addedOn' AND messageFor=$orderId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the order ids of the employee processed order
		function getEmployeesProcessedOrder($employeeId,$memberId)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders WHERE memberId=$memberId AND status <> 0 AND acceptedBy = $employeeId";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the order ids of the employee processed order
		function getEmployeesOwnProcessedOrder($employeeId)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders WHERE status <> 0 AND acceptedBy = $employeeId";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}

		
		//Function to get all the order ids of the employee processed order
		function totalPocessedOrderQAIds($employeeId,$andCaluse)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders_reply WHERE qaAcceptedBy=$employeeId".$andCaluse;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the order ids of the employee processed order
		function getCustomerOrderProcessedByEmployee($employeeId,$memberId,$andClause)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders WHERE memberId=$memberId AND status <> 0 AND acceptedBy = $employeeId".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}

		
		//Function to get all the order ids of the employee QA processed order
		function totalPocessedOrderIds($employeeId,$andCaluse)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders WHERE acceptedBy=$employeeId".$andCaluse;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}

		//Function to get all the order ids of the employee QA processed order
		function totalPocessedQAOrderIds($employeeId,$andCaluse)
		{
			$a_allOrders	=	array();
			$query			=	"SELECT orderId FROM members_orders WHERE acceptedBy=$employeeId AND status=2".$andCaluse;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row['orderId'];

					$a_allOrders[$orderId]	=	$orderId;
				}
				$a_allOrders				=	implode(",",$a_allOrders);

				return $a_allOrders;
			}
			else
			{
				return false;
			}
		}


		//Get Customer Name
		function getActiaveCustomerName($memberId)
		{
			$query		=	"SELECT firstName,lastName FROM members WHERE memberType='".CUSTOMERS."' AND isActiveCustomer = 1 AND memberId=$memberId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);

				$customerName	=	$firstName." ".$lastName;

				return $customerName;
			}
			else
			{
				return false;
			}
		}
		// Function to get the total unmarked rating orders
		function getTotalUnrepliedratedOrders($fromdate,$toDate,$employeeId)
		{
			$a_totalUnRepliedQa	=	array();
			$isCheckExplain		=	1;
			$andClause			=	"";
			$andClause1			=	"";
			$andClause2			=	"";

			$query		=	"SELECT * FROM asking_employee_explanation_on_ratings";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$askingForExplanation	=	$row['askingForExplanation'];
				$askingExplanationFrom	=	$row['askingExplanationFrom'];
				$explanationOnRating	=	$row['explanationOnRating'];

				if($askingForExplanation== 2)
				{
					$isCheckExplain		=	0;
				}
				else
				{
					$andClause			=	" AND rateGiven IN ($explanationOnRating)";
					if($askingExplanationFrom	==	1)
					{
						$andClause1		=	" AND acceptedBy=$employeeId";
						$andClause2		=	" AND hasRatingExplanation=1";
					}
					elseif($askingExplanationFrom	==	2)
					{
						$andClause1		=	" AND qaDoneBy=$employeeId";
						$andClause2		=	" AND hasRatingQaExplanation=1";
					}
					elseif($askingExplanationFrom	==	3)
					{
						$andClause1		=	" AND (acceptedBy=$employeeId OR qaDoneBy=$employeeId)";
						$andClause2		=	" AND (hasRatingExplanation=0 OR hasRatingQaExplanation=0)";
					}
				}
			}
			
			if($isCheckExplain	==	1)
			{
				$query			=	"SELECT members_orders.orderId,acceptedBy,qaDoneBy,hasRatingExplanation,hasRatingQaExplanation FROM members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId WHERE hasRatingExplanation=0 AND rateGivenOn >='$fromdate' AND rateGivenOn <= '$toDate'".$andClause.$andClause1." ORDER BY rateGivenOn";
				$result			=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row	=	mysqli_fetch_assoc($result))
					{
						$orderId				=	$row['orderId'];
						$orderProcessedBy		=	$row['acceptedBy'];
						$orderCompleteddBy		=	$row['qaDoneBy'];
						$hasRatingExplanation	=	$row['hasRatingExplanation'];
						$hasRatingQaExplanation	=	$row['hasRatingQaExplanation'];

						if($askingExplanationFrom	==	3)
						{
							if($orderProcessedBy == $employeeId && $orderCompleteddBy == $employeeId)
							{
								if($hasRatingExplanation	==	0 && $hasRatingQaExplanation == 0)
								{
									$a_totalUnRepliedQa[$orderId]=	$orderId;
								}
							}
							elseif($orderProcessedBy == $employeeId && $orderCompleteddBy != $employeeId && $hasRatingExplanation==	0)
							{
								$a_totalUnRepliedQa[$orderId]	=	$orderId;
							}
							elseif($orderProcessedBy != $employeeId && $orderCompleteddBy == $employeeId && $hasRatingQaExplanation	==	0)
							{
								$a_totalUnRepliedQa[$orderId]	=	$orderId;
							}
						}
						else
						{
							$a_totalUnRepliedQa[$orderId]	=	$orderId;
						}
					}

					return $a_totalUnRepliedQa;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		//Function to check is a comment required on order
		function isRequiredOnRatedComment($orderId,$employeeId)
		{
			$isExistRatings	=	$this->getQueryResult("SELECT members_orders.orderId FROM members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId WHERE rateGiven IN (1,2) AND (acceptedBy=$employeeId OR qaDoneBy=$employeeId) AND members_orders.orderId=$orderId","orderId");

			if(empty($isExistRatings))
			{
				$isExistRatings	=	"";
			}

			return $isExistRatings;
		}
		//Function to get the comments on rated order
		function getOrderEmployeeRatedCommentMessages($orderId)
		{
			$query		=	"SELECT * FROM reply_on_orders_rates WHERE orderId=$orderId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to mark qa rate checklist checked by employee
		function setQAChecklistMarked($orderId,$employeeId,$a_readChecklist)
		{
			dbQuery("DELETE FROM qa_marked_checklist WHERE orderId=$orderId");
			foreach($a_readChecklist as $k=>$v)
			{
				list($value,$checklistId)	=	explode("|",$v);

				dbQuery("INSERT INTO qa_marked_checklist SET orderId=$orderId,employeeId=$employeeId,checkedValue=$value,checklistId=$checklistId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}

			return true;
		}

		//Function to mark employee rate checklist checked by employee
		function setProcessEmployeeChecklistMarked($orderId,$employeeId,$a_readChecklist)
		{
			dbQuery("DELETE FROM process_employee_marked_checklist WHERE orderId=$orderId");
			foreach($a_readChecklist as $k=>$v)
			{
				list($value,$checklistId)	=	explode("|",$v);

				dbQuery("INSERT INTO process_employee_marked_checklist SET orderId=$orderId,employeeId=$employeeId,checkedValue=$value,checklistId=$checklistId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}

			return true;
		}

		//Function to get orders checked qa cheecklist
		function getQAChecklistMarked($orderId)
		{
			$a_checklist=	array();
			$query		=	"SELECT * FROM qa_marked_checklist WHERE orderId=$orderId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$checklistId	=	$row['checklistId'];
					$checkedValue	=	$row['checkedValue'];

					$a_checklist[$checklistId]	=	$checkedValue;
				}
				return $a_checklist;
			}
			else
			{
				return false;
			}
		}
		//Function to get orders checked process employee cheecklist
		function getProcesedEmployeeChecklistMarked($orderId)
		{
			$a_checklist=	array();
			$query		=	"SELECT * FROM process_employee_marked_checklist WHERE orderId=$orderId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$checklistId	=	$row['checklistId'];
					$checkedValue	=	$row['checkedValue'];

					$a_checklist[$checklistId]	=	$checkedValue;
				}
				return $a_checklist;
			}
			else
			{
				return false;
			}
		}
		//Function to get orders checked qa cheecklist
		function getAllQaCheckList()
		{
			$a_checklist=	array();
			$query		=	"SELECT * FROM qa_checklist ORDER BY checklistQaTitle";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$checklistId		=	$row['checklistId'];
					$checklistQaTitle	=	stripslashes($row['checklistQaTitle']);

					$a_checklist[$checklistId]	=	$checklistQaTitle;
				}
				return $a_checklist;
			}
			else
			{
				return false;
			}
		}
		//Function to get orders checked qa cheecklist with answer
		function getAllQaCheckListWithAnswer()
		{
			$a_checklist=	array();
			$query		=	"SELECT * FROM qa_checklist ORDER BY checklistQaTitle";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$checklistId		=	$row['checklistId'];
					$checklistQaTitle	=	stripslashes($row['checklistQaTitle']);
					$answer				=	$row['answer'];

					$a_checklist[$checklistId]	=	$checklistQaTitle."<=>".$answer;
				}
				return $a_checklist;
			}
			else
			{
				return false;
			}
		}
		// Function to check any exist md5 order files
		function checkRepliedFileChecksum($fileChecksumHas,$uploadingFileSize,$orderFileSize,$orderId)
		{			
			$foundError		=	"";
			if($uploadingFileSize == $orderFileSize)
			{
				$foundError = "<b>YOU CANNOT UPLOAD :</b> Number of Bytes of this file match exactly. You are trying to upload the same file customer sent. Please upload new file.";
			}
			else{
				$totalExistsTemplateFile    =	$this->getQueryResult("SELECT COUNT(fileId) as total FROM order_all_files WHERE orderId=$orderId AND uploadingType=1 AND uploadingFor=1","total");

				if($totalExistsTemplateFile == 1){
					////////////////// ONLY CHECK IF CUSTOMER HAS UPLOADED SINGLE TEMPLATE FILE ////
					$isSameFileSizeAsCustomer	=	$this->getQueryResult("SELECT fileId FROM order_all_files WHERE orderId=$orderId AND uploadingType=1 AND uploadingFor=1 AND fileChecksumHas='$fileChecksumHas'","fileId");
					if(!empty($isSameFileSizeAsCustomer)){
						$foundError = "<b>YOU CANNOT UPLOAD :</b> This file you are trying to upload is exactly the same as sent by customer for this order.";
					}
				}
			}
			return $foundError;
			
		}
		// Function to check any exist md5 order files
		function checkExistingMd5HasOrderReplyFile($orderReplyFileMd5HasSize)
		{
			$foundMd5FileFize		=	"";
			$query					=	"SELECT orderId,memberId,orderAddress,orderAddedOn FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderFileMd5HasSize='$orderReplyFileMd5HasSize' AND orderFileMd5HasSize <> '' AND isDeleted=0 AND isVirtualDeleted=0 ORDER BY orderId DESC LIMIT 1";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$orderId				=	$row['orderId'];
				$memberId				=	$row['memberId'];
				$orderAddress			=	stripslashes($row['orderAddress']);

				$customerCompleteName	=	$this->getQueryResult("SELECT completeName FROM members WHERE memberId=$memberId","completeName");
				$customerCompleteName	=	stripslashes($customerCompleteName);

				$foundMd5FileFize	=	"<b>YOU CANNOT UPLOAD :</b> This file you are trying to upload <br>is exactly the same as sent by customer for order # <b>".$orderAddress."</b> by <b>".$customerCompleteName."</b>.";

			}
			else
			{
				$query						=	"SELECT orderId,memberId FROM members_orders_reply WHERE replyId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderReplyFileMd5HasSize='$orderReplyFileMd5HasSize' AND orderReplyFileMd5HasSize <> '' AND isDeleted=0 AND isVirtualDeleted=0 ORDER BY replyId DESC LIMIT 1";
				$result						=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row					=	mysqli_fetch_assoc($result);
					$orderId				=	$row['orderId'];
					$memberId				=	$row['memberId'];
					$orderAddress			=	$this->getQueryResult("SELECT orderAddress FROM members_orders WHERE orderId=$orderId AND memberId=$memberId","orderAddress");
					$orderAddress			=	stripslashes($orderAddress);
					$customerCompleteName	=	$this->getQueryResult("SELECT completeName FROM members WHERE memberId=$memberId","completeName");
					$customerCompleteName	=	stripslashes($customerCompleteName);

					$foundMd5FileFize		=	"This order file is already available for reply order - ".$orderAddress;

					$foundMd5FileFize		=	"<b>Duplicate Files detected :</b> This file you are trying to upload is exactly the same as sent to <b>".$customerCompleteName."</b> for order # <b>".$orderAddress."</b>. <br>Either this file is wrong or that file is wrong. Please check both files.";

				}
				else
				{
					$query						=	"SELECT orderId,memberId,fileId FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND fileChecksumHas='$orderReplyFileMd5HasSize' AND fileChecksumHas <> '' AND isDeleted=0 ORDER BY fileId DESC LIMIT 1";
					$result						=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$row					=	mysqli_fetch_assoc($result);
						$orderId				=	$row['orderId'];
						$memberId				=	$row['memberId'];
						$orderAddress			=	$this->getQueryResult("SELECT orderAddress FROM members_orders WHERE orderId=$orderId AND memberId=$memberId","orderAddress");
						$orderAddress			=	stripslashes($orderAddress);
						$customerCompleteName	=	$this->getQueryResult("SELECT completeName FROM members WHERE memberId=$memberId","completeName");
						$customerCompleteName	=	stripslashes($customerCompleteName);

						$foundMd5FileFize		=	"This order file is already available for reply order - ".$orderAddress;

						$foundMd5FileFize		=	"<b>Duplicate Files detected :</b> This file you are trying to upload is exactly the same as sent to <b>".$customerCompleteName."</b> for order # <b>".$orderAddress."</b>. <br>Either this file is wrong or that file is wrong. Please check both files.";

					}
				}
			}
			return $foundMd5FileFize;
		}

		//Get file cron downloaded status
		function getFileCronDownloadedStatus($orderId,$memberId,$fileType,$otherId)
		{
					
			$isDownloaded	=	$this->getQueryResult("SELECT isDownloaded FROM cron_transfer_order_files  WHERE orderId=$orderId AND memberId=$memberId AND fileType='$fileType' AND otherId=$otherId","isDownloaded");

			if(!empty($isDownloaded))
			{
				//$text		=	"(Successfully downloaded)";
				$text		=	"&nbsp;";
			}
			else
			{
				$text		=	"(File in Queue)";
			}

			return $text;
		}
		//Get file cron downloaded status for new multiple files
		function getMultipleFileCronDownloadedStatus($orderId,$memberId,$fileId)
		{
					
			$isDownloaded	=	$this->getQueryResult("SELECT isDownloaded FROM cron_transfer_order_files  WHERE orderId=$orderId AND memberId=$memberId AND fileId='$fileId' AND isNewMultipleOrderSystem=1","isDownloaded");


			if(!empty($isDownloaded))
			{
				//$text		=	"(Successfully downloaded)";
				$text		=	"&nbsp;";
			}
			else
			{
				$text		=	"(File in Queue)";
			}

			return $text;
		}
		//Add need attention order sms if available
		function addNeedAttentionOrderSms($cancelled,$smsReferenceID,$orderId,$customerId,$employeeId,$smsMessageID,$queued,$smsError,$smsMesseSent,$sentSmsToPhone,$isNeedAttentionOrder,$statusCode=0)
		{
			global $db_conn;

			$t_statusCode     = 0;
			if(!empty($statusCode) && is_numeric($statusCode)){
				$t_statusCode = $statusCode;
			}

			dbQuery("INSERT INTO order_messages_sms SET cancelled='$cancelled',orderId=$orderId,customerId=$customerId,employeeId=$employeeId,smsMessageID='$smsMessageID',queued='$queued',smsError='$smsError',smsMesseSent='$smsMesseSent',sentSmsToPhone='$sentSmsToPhone',isSendingSms=1,isNeedAttentionOrder=$isNeedAttentionOrder,sentMessageEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',sentMessageEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',sendingFromIP='".VISITOR_IP_ADDRESS."',smsReferenceID='$smsReferenceID',statusCode=$t_statusCode");

			$newSmsID	=	mysqli_insert_id($db_conn);
			
			return $newSmsID;
		}
		//Function to add employee order message as SMS
		function addOrderMessageSms($cancelled,$smsReferenceID,$orderId,$customerId,$employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$customerMobileNo)
		{
			global $db_conn;
			
			dbQuery("INSERT INTO order_messages_sms SET cancelled='$cancelled',orderId=$orderId,customerId=$customerId,employeeId=$employeeId,smsMessageID='$smsMessageID',queued='$queued',smsError='$smsError',smsMesseSent='$smsMessage',sentSmsToPhone='$customerMobileNo',isSendingSms=1,isEmployeeMessageOrder=1,sentMessageEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',sentMessageEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',sendingFromIP='".VISITOR_IP_ADDRESS."',smsReferenceID='$smsReferenceID'");

			$newSmsID	=	mysqli_insert_id($db_conn);
			
			return $newSmsID;
		}

		//Function to add employee order message as SMS
		function addOrderMessageSmsNew($smsReferenceID,$orderId,$customerId,$employeeId,$smsMessageID,$smsMessage,$customerMobileNo,$statusCode)
		{
			global $db_conn;

			$t_statusCode     = 0;
			if(!empty($statusCode) && is_numeric($statusCode)){
				$t_statusCode = $statusCode;
			}
			
			dbQuery("INSERT INTO order_messages_sms SET orderId=$orderId,customerId=$customerId,employeeId=$employeeId,smsMessageID='$smsMessageID',smsMesseSent='$smsMessage',sentSmsToPhone='$customerMobileNo',isSendingSms=1,isEmployeeMessageOrder=1,sentMessageEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',sentMessageEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',sendingFromIP='".VISITOR_IP_ADDRESS."',smsReferenceID='$smsReferenceID',statusCode=$t_statusCode");

			$newSmsID	=	mysqli_insert_id($db_conn);
			
			return $newSmsID;
		}

		//Function to get employee order message SMS status
		function getOrderMessagesDeliveryStatus($orderId,$customerId)
		{
			$delivery_status 		   =	array();
			$a_smsMessageStatusCodes   =    cdyneStatusCodes();

			//pr($a_smsMessageStatusCodes);

			$query 	= "SELECT smsId,statusCode FROM order_messages_sms WHERE orderId=$orderId AND customerId=$customerId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$smsId	    =	$row['smsId'];
					$statusCode	=	$row['statusCode'];

					$statusMessageCode     =	 "<font color='green'> and successfully delivered.</font>";

					if($statusCode == 0 || $statusCode == 200 || $statusCode == 201 || $statusCode == 202 || !array_key_exists($statusCode,$a_smsMessageStatusCodes)){
						$statusMessageCode =	 "<font color='green'> and successfully delivered.</font>";
					}
					elseif(array_key_exists($statusCode,$a_smsMessageStatusCodes)){
						$message  = $a_smsMessageStatusCodes[$statusCode];

						$statusMessageCode     =  "<font color='red'> but can't deliver SMS due to - ".$message."</font>";
					}
					$delivery_status[$smsId]   =  $statusMessageCode;					
				}
				return $delivery_status;
			}
			
			return $delivery_status;
		}

		//Function to get new unreplied message of customer
		function getAllNewUnrepliedMessages()
		{
			$totalMessageOrders		=	$this->getQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE isHavingOrderNewMessage=1 AND isVirtualDeleted=0 AND isDeleted=0","total");
			if(empty($totalMessageOrders))
			{
				$totalMessageOrders	=	0;
			}
			return $totalMessageOrders;
		}

		//Function to get new unreplied order ids of customer 
		function getAllNewUnrepliedOrderIds()
		{
			$query	=	"SELECT orderId FROM members_orders WHERE isHavingOrderNewMessage=1 AND isVirtualDeleted=0 AND isDeleted=0";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$a_orders		=	array();
				while($row		=	mysqli_fetch_assoc($result))
				{
					$orderId	=	$row['orderId'];
					$a_orders[$orderId]		=	$orderId;
				}
				return $a_orders;
			}
			else
			{
				return false;
			}
		}

		//Function to get total unreplied orders messages
		function getAllTotalUnrepliedOrderMessage()
		{
			global $db_conn;
			$totalMessageOrders	=	0;
			if($a_orders		=	$this->getAllNewUnrepliedOrderIds())
			{
				$a_allUnrepliedOrders	=	implode(",",$a_orders);
				
				$query		=	"SELECT COUNT(*) FROM members_employee_messages WHERE isVirtualDeleted =0 AND messageBy='".CUSTOMERS."' AND orderId IN ($a_allUnrepliedOrders) GROUP BY orderId";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$totalMessageOrders	=	mysqli_affected_rows($db_conn);
				}

				if(empty($totalMessageOrders))
				{
					$totalMessageOrders	=	0;
				}
				
			}
			return $totalMessageOrders;
		}

		//Function to get total unreplied orders messages
		function getTotalUnrepliedOrderMessage()
		{					
			$totalMessageOrders		=	$this->getQueryResult("SELECT COUNT(*) as total FROM customer_orders_messages_counts INNER JOIN members_employee_messages ON customer_orders_messages_counts.orderId=members_employee_messages.orderId WHERE members_employee_messages.isVirtualDeleted =0 AND messageBy='1' AND isRepliedToEmail=0","total");

			

			if(empty($totalMessageOrders))
			{
				$totalMessageOrders	=	0;
			}
			
			return $totalMessageOrders;
		}

		//Function to get total unreplied orders rating messages
		function getAllTotalUnrepliedRatingMessage()
		{
			$totalRatingOrders		=	$this->getQueryResult("SELECT COUNT(*) as total FROM all_unreplied_rating","total");
			if(empty($totalRatingOrders))
			{
				$totalRatingOrders	=	0;
			}
			return $totalRatingOrders;
		}
		//Function to get total unchecked members orders
		function getAllTotalUncheckedOrders()
		{
			$totalOrders		=	$this->getQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderId > '".MAX_SEARCH_MEMBER_ORDERID."' AND isVirtualDeleted =0 AND isOrderChecked=0 AND isNotVerfidedEmailOrder=0 AND status IN (0,1)","total");
			if(empty($totalOrders))
			{
				$totalOrders	=	0;
			}
			return $totalOrders;
		}

		//Function to get total unchecked members orders
		function getFirstThirtyNewOrders()
		{
			$a_orders	=	array();
			$query		=	"SELECT orderId FROM members_orders WHERE members_orders.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND members_orders.isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0 AND status=0 ORDER BY employeeWarningDate,employeeWarningTime LIMIT 0,30";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$orderId			    =	$row['orderId'];
					$a_orders[$orderId]	    =	$orderId;
				}
			}
			return $a_orders;
		}

		//Function to get total unchecked members orders
		function getByPassTatExplanationCustomers()
		{
			$a_customers	=	array();
			$query		    =	"SELECT memberId FROM members WHERE needTatExplanation=0";
			$result		    =	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$memberId			    =	$row['memberId'];
					$a_customers[$memberId]	=	$memberId;
				}
			}
			return $a_customers;
		}

		//Function to get total exceeded tat orders
		function getAllTotalExceedTatOrders()
		{
			$checkTimeExceed=	CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA;

			$totalOrders	=	$this->getQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderId > '".MAX_SEARCH_MEMBER_ORDERID."' AND isVirtualDeleted=0 AND isHavingEstimatedTime=1 AND status IN (0,1) AND CONCAT(employeeWarningDate, ' ', employeeWarningTime) < '$checkTimeExceed'","total");
			if(empty($totalOrders))
			{
				$totalOrders	=	0;
			}
		
			return $totalOrders;
		}

		//Function to get total incomplted orders
		function getAllTotalIncompltedOrders()
		{
			$threeMonthOldDate =	date('Y-m-d', strtotime("-3 month", strtotime(CURRENT_DATE_INDIA)));
			$twoDaysOldDate    =	date('Y-m-d', strtotime("-48 hour", strtotime(CURRENT_DATE_INDIA)));

			$totalOrders	   =	$this->getQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderAddedOn > '".$threeMonthOldDate."' AND orderAddedOn < '".$twoDaysOldDate."' AND isVirtualDeleted=0 AND status IN (0,1,3)","total");
			if(empty($totalOrders))
			{
				$totalOrders	=	0;
			}
		
			return $totalOrders;
		}

		//Function to get total unreplied orders general messages
		function getAllTotalUnrepliedGeneralMessage()
		{
			$totalGeneralMsg	=	$this->getQueryResult("SELECT COUNT(*) as total FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0 AND employeeSendingFirstMsg=0","total");

			
			if(empty($totalGeneralMsg))
			{
				$totalGeneralMsg=	0;
			}
			return $totalGeneralMsg;
		}

		//Function to get total unreplied orders general messages
		function getAllGeneralMessage()
		{
			$totalGeneralMsg	=	$this->getQueryResult("SELECT COUNT(*) as total FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0","total");
			if(empty($totalGeneralMsg))
			{
				$totalGeneralMsg=	0;
			}
			return $totalGeneralMsg;
		}


		//Function to get total unreplied orders general messages with customer
		function getAllUnrepliedGeneralMessageCustomers()
		{
			$a_messages	=	array();
			$query		=	"SELECT * FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0 AND employeeSendingFirstMsg=0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$generalMsgId			=	$row['generalMsgId'];
					$memberId				=	$row['memberId'];
					$a_messages[$memberId]	=	$memberId;
				}
			}
			return $a_messages;
		}
		//Function Get A Customer Order Details
		function getCompletedOrderDetails($orderId,$memberId)
		{
			$query	=	"SELECT members_orders.*,completeName FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$memberId AND members_orders.isVirtualDeleted=0 AND members_orders.status IN (2,5,6)";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get post audit file details
		function getPostAuditOrderDetails($orderId)
		{
			$query		=	"SELECT * FROM orders_post_audit_details WHERE orderId=$orderId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get customer order files
		function getMultipleOrderFiles($orderId,$memberId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND memberId=$memberId AND orderId=$orderId AND uploadingFor=$uploadingFor AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get order message file
		function getOrdereMessageFile($orderId,$messageId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$messageId AND orderId=$orderId AND uploadingFor=$uploadingFor AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to delete order any types of file type
		function deleteReplyFilesToEmployee($orderId,$replyOrderId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyOrderId AND uploadingFor=$uploadingFor AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$excatFileNameInServer	=	$row['excatFileNameInServer'];
				$fileId					=	$row['fileId'];

				if(file_exists($excatFileNameInServer))
				{
					unlink($excatFileNameInServer);
				}

				dbQuery("DELETE FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND uploadingFor=$uploadingFor AND fileId=$fileId AND isDeleted=0".$andClause);

				return true;
			}
			else
			{
				return false;
			}
		}
		//Function to get customer order files
		function getOrderMultipleFiles($orderId,$replyOrderId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyOrderId AND uploadingFor=$uploadingFor AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to delete order any types of file type
		function deleteProcessOrderFile($orderId,$fileId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND uploadingFor=$uploadingFor AND fileId=$fileId AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$excatFileNameInServer	=	$row['excatFileNameInServer'];

				if(file_exists($excatFileNameInServer))
				{
					unlink($excatFileNameInServer);
				}

				dbQuery("DELETE FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND uploadingFor=$uploadingFor AND fileId=$fileId AND isDeleted=0".$andClause);

				return true;
			}
			else
			{
				return false;
			}
		}
		//Function to get employee replied order file name
		function getEmployeeFileNameWithExtSize($orderId,$replyOrderId,$uploadingFor,$uploadingType=0)
		{
			$andClause		=	"";
			if(!empty($uploadingType))
			{
				$andClause	=	" AND uploadingType=".$uploadingType;
			}
			$query			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyOrderId AND uploadingFor=$uploadingFor AND isDeleted=0".$andClause;
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				
				$row				=	mysqli_fetch_assoc($result);
				$fileId				=	$row['fileId'];
				$uploadingFileName	=	stripslashes($row['uploadingFileName']);
				$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
				$uploadingFileSize	=	$row['uploadingFileSize'];

				$fileName			=	$fileId."|".$uploadingFileName.".".$uploadingFileExt."|".$uploadingFileSize;

				return $fileName;
			}
			else
			{
				return false;
			}
		}
		//Function to check need attention
		function isOrderWasInNeedAttention($orderId)
		{
			$query		=	"SELECT * FROM order_attention WHERE orderId=$orderId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get customer order general message sending from email files
		function getCustomerGeneralMessageEmailFiles($memberId,$parentId)
		{
			$a_files					=	array();
			$query						=	"SELECT * FROM customer_general_message_files WHERE parentId=$parentId AND memberId=$memberId";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row				=	mysqli_fetch_assoc($result))
				{
					$fileId				=	$row['fileId'];
					$fileName			=	stripslashes($row['fileName']);
					$fileExt			=	stripslashes($row['fileExt']);
					$fileSize			=	$row['fileSize'];

					$a_files[$fileId]	=	$fileName.".".$fileExt."|".$fileSize;

				}

				return $a_files;
			}
			else
			{
				return false;
			}
		}
		//Function to get all the un replied general  messages
		function getCustomerUnRepliedGeneralMessages($memberId)
		{
			$query		=	"SELECT * FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0 AND memberId=$memberId AND employeeSendingFirstMsg=0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get new multiple system files names and files details
		function getExactMultipleOrderFiles($orderId,$fileId)
		{
			$query						=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0 AND fileId=$fileId";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$downloadPath			=	$row['excatFileNameInServer'];
				$uploadingFileExt		=	$row['uploadingFileExt'];
				$uploadingFileName		=	$row['uploadingFileName'];
				$mimeTypeField			=	$row['uploadingFileType'];
				$uploadingFileSize		=	$row['uploadingFileSize'];

				$name					=	$downloadPath."<=>".$uploadingFileExt."<=>".$uploadingFileName."<=>".$mimeTypeField."<=>".$uploadingFileSize;

				return $name;
			}
			else
			{
				return false;
			}
		}
		//Function to get orders employee checked tabs
		function getEmployeesClickedTabs($orderId,$employeeId)
		{
			$a_clickedTabs				=	array();
			$query						=	"SELECT * FROM employee_clicked_order_tabs WHERE orderId=$orderId AND employeeId=$employeeId AND orderId > ".MAX_SEARCH_MEMBER_ORDERID."";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row				=	mysqli_fetch_assoc($result))
				{
					$tabId				=	$row['tabId'];
				
					$a_clickedTabs[$tabId]	=	$tabId;
				}
			}
			else
			{			
				dbQuery("INSERT INTO employee_clicked_order_tabs SET orderId=$orderId,employeeId=$employeeId,tabId=1,clickedOn='".CURRENT_DATE_INDIA."',clickedTime='".CURRENT_TIME_INDIA."'");

				$a_clickedTabs[1]	=	1;
			}
			return $a_clickedTabs;
		}
		//Function to update order clikec tabs
		function updateEmployeesClickedTabs($orderId,$tabId,$employeeId)
		{
			$isExixtsOrderTab	=	$this->getQueryResult("SELECT orderId FROM employee_clicked_order_tabs WHERE  orderId=$orderId AND employeeId=$employeeId AND tabId=$tabId","orderId");

			if(empty($isExixtsOrderTab))
			{
				dbQuery("INSERT INTO employee_clicked_order_tabs SET orderId=$orderId,employeeId=$employeeId,tabId=$tabId,clickedOn='".CURRENT_DATE_INDIA."',clickedTime='".CURRENT_TIME_INDIA."'");
			}
			return true;
		}

		////Function to deducts counts on order related
		function deductOrderRelatedCounts($type){
			
			$query	=	"SELECT * FROM orders_related_counts WHERE ".$type." <> 0";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				//dbQuery("UPDATE orders_related_counts SET ".$type."=".$type."-1");
			}
			

			return true;
		}

		//////Fuction to get single RESULT //////
		function getQueryResult($query,$param){
			
			$query;
			$retrnResult	=	"";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$retrnResult=   $row[$param];
			}
			return $retrnResult;
		}

		// Function to check is a la carte order
		function isALaCarteOrder($memberId,$orderId)
		{
			$return =   0;
			$query	=	"SELECT checklistId FROM la_carte_orders_checkfiled WHERE memberId=$memberId AND orderId=$orderId LIMIT 1";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$return =   1;
			}
			return $return;
		} 

		// Function to check is a la carte order
		function getLaCartePrices($memberId=0)
		{
			$a_laCartePrices 	  =	 array();
			
			$query	              =	 "SELECT * FROM la_carte_orders_prices WHERE memberId=0 ORDER BY displayOrder ASC";
			if(!empty($memberId)){
				$query	          =	 "SELECT * FROM la_carte_orders_prices WHERE (memberId=0 OR memberId=$memberId) AND isExpired=0 ORDER BY displayOrder ASC";
			}
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row = mysqli_fetch_assoc($result)){
					$t_priceId 	   =	$row['priceId'];
					$t_price 	   =	displayMoneyExpo($row['price']);
					$t_priceText   =	stripslashes($row['priceText']);

					$a_laCartePrices[$t_priceId]  = $t_price."|".$t_priceText;
				}
			}
			return $a_laCartePrices;
		} 

		// Function to update customer order avearge timings
		function updateOrderAverageTiming($lastCalculatedOrderID,$memberId,$timeSpentEmployee,$existingTimeSpentEmployee,$isEditedProcess)
		{
			$timeSpentEmployee     =    round($timeSpentEmployee);

			$query	               =	"SELECT * FROM customer_orders_average_timing WHERE memberId=$memberId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row 			  = mysqli_fetch_assoc($result);
				$totalTime        = $row['totalTimeTaken'];
				$totalOrders      = $row['totalOrders'];

				if($totalOrders   >= 10){
					/////////////// WE ARE ONLY KEEPING LAST 10 ORDERS TOTAL TIME SPENT ///////
					$query1	          = "SELECT timeSpentEmployee,orderId FROM members_orders_reply WHERE memberId=$memberId AND timeSpentEmployee <> 0 ORDER BY orderId DESC LIMIT 10";
					$totalTime        =  0;
					$result1          =  dbQuery($query1);
					if(mysqli_num_rows($result1)){
				        while($row1   = mysqli_fetch_assoc($result1)){				        	
				        	$timeSpentEmployee     = $row1['timeSpentEmployee'];
				        	if($timeSpentEmployee > 200){
				        		$timeSpentEmployee =	200;
				        	}
				        	$totalTime = $totalTime+$timeSpentEmployee;
				        }
				        $totalTime        =  round($totalTime);
				        $averageTiming    =	 round($totalTime/10);						

			        	dbQuery("UPDATE customer_orders_average_timing SET totalTimeTaken=$totalTime,averageTimeTaken=$averageTiming,totalOrders=10,lastUpdatedDate='".CURRENT_DATE_INDIA."',lastUpdatedTime='".CURRENT_TIME_INDIA."',lastCalculatedOrderID=$lastCalculatedOrderID WHERE memberId=$memberId");
				    }
				}
				else{
					/////////////// IF TOTAL ORDERS ARE LESS THAN 10 /////////////////////////

					if(empty($isEditedProcess)){
						$totalOrders  = $totalOrders+1;
					}
					else{
						if($totalTime > $existingTimeSpentEmployee){
							$totalTime= $totalTime-$existingTimeSpentEmployee;
						}
						else{
							$totalTime= 0;
						}
					}
					$totalTime    	  =  $totalTime+$timeSpentEmployee;
					$averageTiming    =	 round($totalTime/$totalOrders);
					$totalTime        =  round($totalTime);

			        dbQuery("UPDATE customer_orders_average_timing SET totalTimeTaken=$totalTime,averageTimeTaken=$averageTiming,totalOrders=$totalOrders,lastUpdatedDate='".CURRENT_DATE_INDIA."',lastUpdatedTime='".CURRENT_TIME_INDIA."',lastCalculatedOrderID=$lastCalculatedOrderID WHERE memberId=$memberId");
			    }

			}
			else{
				$averageTiming  = $timeSpentEmployee;
				
 				dbQuery("INSERT INTO customer_orders_average_timing SET memberId='$memberId',totalTimeTaken=$timeSpentEmployee,averageTimeTaken=$timeSpentEmployee,totalOrders=1,lastUpdatedDate='".CURRENT_DATE_INDIA."',lastUpdatedTime='".CURRENT_TIME_INDIA."',lastCalculatedOrderID=$lastCalculatedOrderID");
			}
			dbQuery("UPDATE members SET averageTimeTaken=$averageTiming WHERE memberId=$memberId");

			return true;
		} 

		//Function to get is there any TAT Explanation
		function isHavingTATExplanation($orderId)
		{
			$query1	          = "SELECT * FROM order_tat_explanation WHERE orderId=$orderId";
			$result1          =  dbQuery($query1);
			if(mysqli_num_rows($result1)){
		        return $result1;
		    }
		    else{
		    	return false;
		    }
		}

		// Function to add new order into track list
		function addOrderTracker($employeeId,$orderId,$orderAddress,$actionPerformed,$operationType,$memberId=0)
		{
			$existingOrderId=	$this->getQueryResult("SELECT orderId FROM order_history WHERE orderId=$orderId","orderId");
			

			if(!empty($existingOrderId) ){
				$orderAddress   =  makeDBSafe($orderAddress);
				$actionPerformed=  makeDBSafe($actionPerformed);
				$operationType  =  makeDBSafe($operationType);
				
				dbQuery("INSERT INTO order_history SET orderId=$orderId,employeeId=$employeeId,memberId=$memberId,orderAddress='$orderAddress',operationType='$operationType',operationDate='".CURRENT_DATE_INDIA."',operationTime='".CURRENT_TIME_INDIA."',actionPerformed='$actionPerformed',operationIp='".VISITOR_IP_ADDRESS."'");
			}			

			return true;
		}

		// Function to add new order into track list
		function addOrderTrackList($memberId,$employeeId,$orderId,$orderAddress,$actionPerformed,$operationType)
		{
			$orderAddress   =  makeDBSafe($orderAddress);
			$actionPerformed=  makeDBSafe($actionPerformed);
			$operationType  =  makeDBSafe($operationType);
			
			dbQuery("INSERT INTO order_history SET orderId=$orderId,memberId=$memberId,employeeId=$employeeId,orderAddress='$orderAddress',operationType='$operationType',operationDate='".CURRENT_DATE_INDIA."',operationTime='".CURRENT_TIME_INDIA."',actionPerformed='$actionPerformed',operationIp='".VISITOR_IP_ADDRESS."'");

			return true;
		}  
	}

	
?>
