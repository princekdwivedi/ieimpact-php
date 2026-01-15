<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);	
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	include(SITE_ROOT			. "/classes/email-templates.php");
	$showHideBrowseOption		=	false;
	$validator					=	new validate();
	$emailObj					=  new emails();
	$filePath					=	SITE_ROOT_FILES."/files/member-identity/";
	$errorMsg					=	"";
	$uploadingError				=	"";
	
	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);

		return $fileName;
	}

	$form			=	SITE_ROOT_EMPLOYEES  . "/forms/add-invesment.php";
	

	$query			=	"SELECT totalTaxExemption,taxRateApproximately,totalTax FROM employee_details WHERE employeeId=$s_employeeId";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$row					=	mysql_fetch_assoc($result);
		$totalTaxExemption		=	stripslashes($row['totalTaxExemption']);
		$taxRateApproximately	=	stripslashes($row['taxRateApproximately']);
		$totalTax				=	$row['totalTax'];

		if(empty($totalTaxExemption)){
			$totalTaxExemption  =	"";
		}
		if(empty($taxRateApproximately)){
			$taxRateApproximately =	"";
		}
		if(empty($totalTax)){
			$totalTax			=	"";
		}
	}

	$a_descriptions	=	array();
	$a_amounts		=	array();
	$a_existsArray	=	array();
	$a_existsFiles	=	array();
	$a_existsPath	=	array();

	$query			=	"SELECT sectionId,descriptions,amount FROM employee_tax_declaration_details WHERE employeeId=$s_employeeId";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		while($row			=	mysql_fetch_assoc($result)){
			$sectionId		=	$row['sectionId'];
			$descriptions	=	stripslashes($row['descriptions']);
			$amount		    =	$row['amount'];
			if(empty($amount)){
				$amount		=	"";
			}

			$a_descriptions[$sectionId]			=	$descriptions;
			$a_amounts[$sectionId]				=	$amount;
			$a_existsArray[]					=	$sectionId;
		}
	}

	$query			=	"SELECT * FROM employee_tax_declaration_files WHERE employeeId=$s_employeeId";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		while($row			=	mysql_fetch_assoc($result)){
			$sectionId		=	$row['sectionId'];
			$fileName	    =	stripslashes($row['fileName']);
			$fileExt		=	$row['fileExt'];
			$a_existsPath[$sectionId]	=	$row['filePath'];
			
			$completeFileName	=	$fileName.".".$fileExt;

			$a_existsFiles[$sectionId]			=	$completeFileName;
		}
	}

	if(isset($_GET['sectionId']) && isset($_GET['isDeleteInvestment']) && $_GET['isDeleteInvestment'] == 1 && array_key_exists($_GET['sectionId'],$a_existsPath)){
		
		$existingFileName=	$a_existsPath[$_GET['sectionId']];
		$fullFilePath	 =  $filePath.$existingFileName;
		if(file_exists($fullFilePath)){
			@unlink($fullFilePath);
		}

		dbQuery("DELETE FROM employee_tax_declaration_files WHERE employeeId=$s_employeeId AND sectionId=".$_GET['sectionId']);
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/add-manage-tax-certificates.php");
		exit();
	}


	if(isset($_REQUEST['formSubmitted'])){
	
		extract($_REQUEST);
		//pr($_FILES);
		//die();

		$a_inputFileds			=	$_POST['inputFileds'];
		$a_descriptions			=	$_POST['description'];
		$a_amounts				=	$_POST['amount'];

		$totalTaxExemption		=	trim($totalTaxExemption);
		$taxRateApproximately	=	trim($taxRateApproximately);
		$totalTax				=	trim($totalTax);
		

		$isExistsDescription	=	0;
		$isExistsAmounts	    =	0;
		$isExistsFile	        =	0;

		foreach($a_descriptions as $kk=>$vv){
			if(!empty($vv)){
				$isExistsDescription	=	1;
				break;
			}
		}

		foreach($a_amounts as $kk=>$vv){
			if(!empty($vv)){
				$isExistsAmounts		=	1;
				break;
			}
		}

		if(!empty($_FILES['taxfiles']['name']))
		{
			$isExistsFile		=	1;
		}
		else{
			$isExistsFile		=	0;
		}

		if(!empty($a_existsFiles)){
			$isExistsFile		=	1;
		}

		if(!empty($a_descriptions)){
			$isExistsDescription		=	1;
		}

		if(!empty($a_amounts)){
			$isExistsAmounts		=	1;
		}

		if(empty($isExistsDescription))
		{
			$validator->setError("Enter at least one descriptions.");
		}
		if(empty($isExistsAmounts))
		{
			$validator->setError("Enter at least one amount.");
		}
		/*if(empty($isExistsFile))
		{
			$validator->setError("Upload at least one file.");
		}*/
		$validator ->checkField($totalTaxExemption,"","Please enter total tax exemption.");
		$validator ->checkField($taxRateApproximately,"","Please enter taxation rate approximately.");
		$validator ->checkField($totalTax,"","Please enter your total tax.");
		if(!empty($_FILES['taxfiles']['name']))
		{
			$a_fileNames		=	$_FILES['taxfiles']['name'];
			$a_fileSizes		=	$_FILES['taxfiles']['size'];
			$a_fileTypes		=	$_FILES['taxfiles']['type'];
			$a_fileTempNames	=	$_FILES['taxfiles']['tmp_name'];

			foreach($a_fileNames as $key => $photoName)
			{	
				
				if(!empty($photoName))
				{
					$t_mainText		=	$a_invesmentDetails[$key];
					list($t_text,$t_level,$t_type,$t_underSection,$t_isreqAmount) = explode("|",$t_mainText);
					$size				=	$a_fileSizes[$key];
					$type				=	$a_fileTypes[$key];
					$temp				=	$a_fileTempNames[$key];
					$ext				=	findexts($photoName);
					$photoName			=	getFileName($photoName);

					if($ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg' || $ext == 'png' || $ext == 'doc' || $ext == 'docx' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'pdf')
					{
						
					}
					else{
						$uploadingError	.=	$t_text." file is not a valid uploading file.<br />";
					}
				}				

			}
		}
		if(!empty($uploadingError)){
			$uploadingError	=	$uploadingError."<br /><b>Only .jpg/.gif/.png/.docx/.xls/.pdf files are  allowed</b>";

			$validator->setError($uploadingError);
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
				
			foreach($a_inputFileds as $k=>$v){
				
				if(array_key_exists($k,$a_descriptions)){
					$descriptions	=	makeDBSafe($a_descriptions[$k]);
				}
				else{
					$descriptions	=	"";
				}
				if(array_key_exists($k,$a_amounts)){
					$amount			=	makeDBSafe($a_amounts[$k]);
				}
				else{
					$amount			=   0;
				}

				//if(!empty($descriptions) || !empty($amount)){

					if(!empty($a_existsArray) && in_array($k,$a_existsArray)){
						dbQuery("UPDATE employee_tax_declaration_details SET descriptions='$descriptions',amount='$amount',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIp='".VISITOR_IP_ADDRESS."' WHERE sectionId=$k AND employeeId=$s_employeeId");
					}
					else{
						dbQuery("INSERT INTO employee_tax_declaration_details SET descriptions='$descriptions',amount='$amount',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIp='".VISITOR_IP_ADDRESS."',sectionId=$k,employeeId=$s_employeeId");

					}
				//}
			}

			dbQuery("UPDATE employee_details SET totalTaxExemption='$totalTaxExemption',taxRateApproximately='$taxRateApproximately',totalTax='$totalTax' WHERE employeeId=$s_employeeId");

			
			if(!empty($_FILES['taxfiles']['name']))
			{
				$a_fileNames		=	$_FILES['taxfiles']['name'];
				$a_fileSizes		=	$_FILES['taxfiles']['size'];
				$a_fileTypes		=	$_FILES['taxfiles']['type'];
				$a_fileTempNames	=	$_FILES['taxfiles']['tmp_name'];

				foreach($a_fileNames as $key => $photoName)
				{
					if(!empty($photoName))
					{
						$size				=	$a_fileSizes[$key];
						$type				=	$a_fileTypes[$key];
						$temp				=	$a_fileTempNames[$key];
						$ext				=	findexts($photoName);
						$photoName			=	getFileName($photoName);

						if($ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg' || $ext == 'png' || $ext == 'doc' || $ext == 'docx' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'pdf')
					    {
							if(!empty($a_existsPath) && array_key_exists($key,$a_existsPath))
							{
								$existingFilePath	=	$a_existsPath[$key];
								$fullFilePath	=  $filePath.$existingFilePath;
								if(file_exists($fullFilePath)){
									@unlink($fullFilePath);
								}
								
								
								$t_photoName	=	makeDBSafe($photoName);

								$filePathUrl	=	$s_employeeId."_".$key."_".$photoName.".".$ext;
								
								dbQuery("UPDATE employee_tax_declaration_files SET fileName='$t_photoName',fileExt='$ext',fileSize='$size',fileMimeType='$type',filePath='$filePathUrl',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIp='".VISITOR_IP_ADDRESS."' WHERE sectionId=$key AND employeeId=$s_employeeId");	
								
								@move_uploaded_file($temp,$filePath.$filePathUrl);
							}
							else{
								$t_photoName	=	makeDBSafe($photoName);

								$filePathUrl	=	$s_employeeId."_".$key."_".$photoName.".".$ext;
								
								dbQuery("INSERT INTO employee_tax_declaration_files SET fileName='$t_photoName',fileExt='$ext',fileSize='$size',fileMimeType='$type',filePath='$filePathUrl',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIp='".VISITOR_IP_ADDRESS."',sectionId=$key,employeeId=$s_employeeId");	
								
								@move_uploaded_file($temp,$filePath.$filePathUrl);
							}
						}
					}				

				}
			}

			//////////////////////////////////////////////////////////////////////////////////////
			///// SENDING EMAIL TO ADMIN WITH USERS ADDED TAX INVESTMENT DETAILS AND FILE ////////
			$a_employeeDescriptions =	array();
			$a_employeeAmount		=	array();

			$query					=	"SELECT sectionId,descriptions,amount FROM employee_tax_declaration_details WHERE employeeId=$s_employeeId";
			$result					=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				while($row			=	mysql_fetch_assoc($result)){
					$sectionId		=	$row['sectionId'];
					$descriptions	=	stripslashes($row['descriptions']);
					$amount		    =	$row['amount'];
					if(empty($amount)){
						$amount		=	"";
					}

					$a_employeeDescriptions[$sectionId]	=	$descriptions;
					$a_employeeAmount[$sectionId]		=	$amount;
				}
			}

			$table	=	"<table width='98%' align='center' cellpadding='2' cellspacing='2' border='1'><tr><td colspan='4'><b>".$s_employeeName." (PDF Department) INCOME TAX INVESTMENT DECLARATION FORM FOR THE YEAR 2016-2017</b></td></tr><tr><td colspan='6'><hr size='1' width='100%' bgcolor='#bebebe;'></td></tr>";

			$table .=   "<tr>
							<td width='30%' class='title'><b>Type</b></td>
							<td width='25%' class='title'><b>Details</b></td>
							<td width='9%' class='title'><b>Under Section</b></td>
							<td width='9%' class='title'><b>Amount</b></td>
						</tr>";
						foreach($a_invesmentDetails as $key=>$value){
							$mainText		=	$a_invesmentDetails[$key];

							$level			=	0;
							$type			=	"";
							$isreqAmount	=	"";

							list($text,$level,$type,$underSection,$isreqAmount) = explode("|",$mainText);

							if($level		==	0){
								$text		=	"<font class='textstyle2'><b>$text</b></font>";
							}
							elseif($level	==	1){
								$text		=	"<font class='smalltext2'>$text</font>";
							}
							else{
								$text		=	"<font class='smalltext7'><font color='#ff0000;'>$text</font></font>";
							}

							if($level		==	0 && $key != 1){
							
								$table .= "<tr>
											<td colspan='6'>
												<hr size='1' width='100%' bgcolor='#bebebe;'>
											</td>
										</tr>";
					
							}

							$existingDescriptions		=	"";
							if(empty($underSection)){
								$underSection			=	"";
							}
							$existingAmount		=	"";
							if(array_key_exists($key,$a_employeeAmount)){
								$existingAmount	=	$a_employeeAmount[$key];
								if(!empty($existingAmount))
								{
									$existingAmount		=	"&#8377;".$existingAmount;
								}
							}
							
							if($type == "T" || $type == "TF" || $type == "TA"){
								if(!empty($a_descriptions) && count($a_employeeDescriptions) > 0 && array_key_exists($key,$a_employeeDescriptions)){
									$existingDescriptions	=	$a_employeeDescriptions[$key];
								}
							}
					
							$table     .=  "<tr>
												<td valign='top'>".$text."</td>
												<td valign='top'>".$existingDescriptions."</td>	
												<td valign='top'>".$underSection."</td>	
												<td valign='top'>".$existingAmount."</td>
										   </tr>";
													
													
				
							}
			   $table .= "<tr>
								<td colspan='6'>
									<hr size='1' width='100%' bgcolor='#bebebe;'>
								</td>
							</tr>";

			   $table .=   "<tr>
							<td colspan='3'' class='smalltext7' style='text-align:right;'><b>Total Tax Exemption</b>&nbsp;&nbsp;</td>
							<td class='title'><b>&#8377;".$totalTaxExemption."</b></td>
						</tr>";

			   $table .=   "<tr>
							<td colspan='3'' class='smalltext7' style='text-align:right;'><b>Taxation Rate Approximately</b>&nbsp;&nbsp;</td>
							<td class='title'><b>&#8377;".$taxRateApproximately."</b></td>
						</tr>";

			   $table .=   "<tr>
							<td colspan='3'' class='smalltext7' style='text-align:right;'><b>Total Tax</b>&nbsp;&nbsp;</td>
							<td  class='title'><b>&#8377;".$totalTax."</b></td>
						</tr>";

				$table .=   "</table>";

				$a_attachmentPath		=	array();
				$a_attachmentType		=	array();
				$a_attachmentName		=	array();
				$hasAttachment		    =	0;


				$query					=	"SELECT * FROM employee_tax_declaration_files WHERE employeeId=$s_employeeId";
				$result					=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					while($row			=	mysql_fetch_assoc($result)){
						$sectionId		=	$row['sectionId'];
						$fileName	    =	stripslashes($row['fileName']);
						$fileExactPath  =	stripslashes($row['filePath']);
						$fileExt		=	$row['fileExt'];
						$fileMimeType	=	$row['fileMimeType'];

						$mainText1		=	$a_invesmentDetails[$sectionId];
						list($text,$level,$type,$underSection,$isreqAmount) = explode("|",$mainText1);
					
						$a_attachmentPath[]		=	$filePath.$fileExactPath;
						$a_attachmentType[]		=	$fileMimeType;
						$a_attachmentName[]		=	$text.".".$fileExt;
					}
					$hasAttachment				=	1;
				}
			
				$a_templateData			=	array("{bodyMatter}"=>$table);
				$managerEmployeeFromName=	"EMPLOYEE INCOME TAX INVESTMENT DECLARATION";

				$managerEmployeeEmailSubject	=	$s_employeeName."  (PDF Department) INCOME TAX INVESTMENT DECLARATION FORM FOR THE YEAR 2016-2017";

				$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
				$toEmail				=	"hr@ieimpact.com";
				$managerEmployeeFromCc  =	"rishi@ieimpact.com";
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

			//////////////////////////////////////////////////////////////////////////////////////
			$_SESSION['successTax']	=	1;
	
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/add-manage-tax-certificates.php#message");
			exit();
		}
		else{
			$errorMsg	 =	$validator ->getErrors();
		}
	}

	include($form);
	
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>