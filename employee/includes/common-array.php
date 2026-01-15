<?php
	//This array is for displaying country names in member's section

	$a_countries	=	Array(
						""=> "SELECT COUNTRY",
						"IN"=> "INDIA",
						"CA"=> "CANADA",
						"US"=> "UNITED STATES",
						"PH"=> "PHILIPPINES",
						"RU"=> "RUSSIA"
					);

	//Platform array for employee
	$a_platform		=	array("1"=>"Dictaphone","2"=>"Escription","3"=>"Netcare","4"=>"Properties","5"=>"PDF Reports");

	$a_platform1	=	array("1"=>"Utah","2"=>"Yakima","3"=>"Allina","4"=>"Richardson","5"=>"Brazosport","6"=>"APD","7"=>"Alice Hyde","8"=>"Kaleida","9"=>"UPMC","10"=>"Clarian","11"=>"Winchester");

	$a_platform2	=	array("1"=>"Desert","2"=>"Silverspring","3"=>"Sarasota","4"=>"SMHC","5"=>"Hahnemann");

	$a_platform3	=	array("1"=>"Salem","2"=>"Holycross");

	$a_platform4	=	array("1"=>"Red Zone","2"=>"Green Zone","3"=>"Blue Zone","4"=>"Black Zone","5"=>"White Zone","6"=>"Gold Zone","6"=>"Silver Zone");

	$a_platform5	=	array("1"=>"Cathy","2"=>"Chas","3"=>"Ron","4"=>"Rob");

	$a_identityProof=	array("1"=>"Driving License","2"=>"Domicile Certificate","3"=>"Ration Card ","4"=>"Voter ID card");

	$a_department=	array("1"=>"MT","2"=>"REV");
	//$a_newDepartment=	array("1"=>"MT","2"=>"REV","3"=>"PDF");
	$a_newDepartment=	array("1"=>"MT","3"=>"PDF");

	$a_shift	 =	array("1"=>"Morning","2"=>"Evening");

	$a_month	 =	array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");

	$a_days	 =	array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20","21"=>"21","22"=>"22","23"=>"23","24"=>"23","24"=>"24","25"=>"25","26"=>"26","27"=>"27","28"=>"28","29"=>"29","30"=>"30","31"=>"31");

	$a_viewDictaphoneReport	=	array("1"=>"Transcription (SINGLE)","2"=>"VRE","3"=>"QA","4"=>"Lines pended for Blanks/Technical Issues");

	$a_viewPropertiesReport	=	array("1"=>"Direct","2"=>"Indirect","3"=>"QA","4"=>"Post Audit");

	
	$a_weekDaysText		=	array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");

	$a_displayReport		=	array("1"=>"All","2"=>"Total Money","3"=>"Total Lines");

	$a_inetExtEmployee		=	array("1"=>"Internal","2"=>"Extrenal");

	$a_employeeWorksFor		=	array("1"=>"DSP","2"=>"NDSP","3"=>"PARTIAL");

	$a_salaryPaidTrough		=	array("1"=>"CASH","2"=>"CHEQUE","3"=>"ONLINE NET BANKING","4"=>"BY HAND");

	$a_ratingByQa			=	array("1"=>"Awful","2"=>"Poor","3"=>"Fair","4"=>"Good","5"=>"Exellent");

	$a_mainManagerEmail		=	array("2"=>"hemant@ieimpact.net|Hemant Jindal", "3"=>"hr@ieimpact.com|HR", "4"=>"dilber@ieimpact.com|Dilber");


	$a_employeeHighestQualifications	=	array("1"=>"Phd/Doctorate","2"=>"Masters","3"=>"Graduate","4"=>"12th Standard","5"=>"10th Standard","6"=>"Others");

	$a_employeeQualificationsStatus	=	array("1"=>"Regular Passed Out","2"=>"Regular Pursuing","3"=>"Correspondence Passed Out","4"=>"Correspondence Pursuing");


	$a_monthDateText			=	array();
	$a_monthDateText[1]			=	"1st";
	$a_monthDateText[2]			=	"2nd";
	$a_monthDateText[3]			=	"3rd";
	$a_monthDateText[4]			=	"4th";
	$a_monthDateText[5]			=	"5th";
	$a_monthDateText[6]			=	"6th";
	$a_monthDateText[7]			=	"7th";
	$a_monthDateText[8]			=	"8th";
	$a_monthDateText[9]			=	"9th";
	$a_monthDateText[10]		=	"10th";
	$a_monthDateText[11]		=	"11th";
	$a_monthDateText[12]		=	"12th";
	$a_monthDateText[13]		=	"13th";
	$a_monthDateText[14]		=	"14th";
	$a_monthDateText[15]		=	"15th";
	$a_monthDateText[16]		=	"16th";
	$a_monthDateText[17]		=	"17th";
	$a_monthDateText[18]		=	"18th";
	$a_monthDateText[19]		=	"19th";
	$a_monthDateText[20]		=	"20th";
	$a_monthDateText[21]		=	"21st";
	$a_monthDateText[22]		=	"22nd";
	$a_monthDateText[23]		=	"23rd";
	$a_monthDateText[24]		=	"24th";
	$a_monthDateText[25]		=	"25th";
	$a_monthDateText[26]		=	"26th";
	$a_monthDateText[27]		=	"27th";
	$a_monthDateText[28]		=	"28th";
	$a_monthDateText[29]		=	"29th";
	$a_monthDateText[30]		=	"30th";
	$a_monthDateText[31]		=	"31st";

	$a_attendanceMarked			=	array();
	$a_attendanceMarked[0]		=	"Absent|A";
	$a_attendanceMarked[1]		=	"Present|P";
	$a_attendanceMarked[2]		=	"Half|HD";
	$a_attendanceMarked[3]		=	"Leave|L";
	$a_attendanceMarked[4]		=	"Holiday|H";
	$a_attendanceMarked[5]		=	"Sunday|S";


	$isLeapYear					=	date("Y")%4;
	$febMonthDays				=	28;
	if($isLeapYear==0)
	{
		$febMonthDays			=	29;
	}

	$a_daysInMonth				=	array();
	$a_daysInMonth[1]			=	"31";
	$a_daysInMonth[2]			=	$febMonthDays;
	$a_daysInMonth[3]			=	"31";
	$a_daysInMonth[4]			=	"30";
	$a_daysInMonth[5]			=	"31";
	$a_daysInMonth[6]			=	"30";
	$a_daysInMonth[7]			=	"31";
	$a_daysInMonth[8]			=	"31";
	$a_daysInMonth[9]			=	"30";
	$a_daysInMonth[10]			=	"31";
	$a_daysInMonth[11]			=	"30";
	$a_daysInMonth[12]			=	"31";

	$a_invesmentDetails	    =	array();
	$a_invesmentDetails[1]  =  "House Rent Payable - |0|N|0|0";
	$a_invesmentDetails[2]  =  "Landlord Name and Addresss|1|TA|0|0";
	$a_invesmentDetails[3]  =  "PAN # of Landlord|1|T|0|0";
	$a_invesmentDetails[4]  =  "Upload Rent Slips and Rent Paid Proof in the shape of Bank Statement and rent slip signed by Landlord ****No Cash Receipts Please|2|F|0|1";
	$a_invesmentDetails[5]  =  "Home Loan Interest-  Limit 2,00,000|0|N|0|0"; 
	$a_invesmentDetails[6]  =  "Bank/Institution Name from where home loan is taken|1|T|0|0";
	$a_invesmentDetails[7]  =  "Total Interest Paid in Financial Year|1|T|0|1";
	$a_invesmentDetails[8]  =  "Upload Bank Certificate For Paid Amount of Interest and Principal during the Financial Year|2|F|0|0";

	$a_invesmentDetails[9]   =  "INVESTMENTS - Limit 150,000|0|N|0|0";
	$a_invesmentDetails[10]  =  "(A) PENSION SCHEME INVESTMENTS|1|TF|80 CCC|1";
	$a_invesmentDetails[11]  =  "(B) HOUSING LOAN PRINCIPAL REPAYMENT|1|TF|80 CC|1";
	$a_invesmentDetails[12]  =  "(C) PPF - PUBLIC PROVIDENT FUND|1|TF|80 CC|1";
	$a_invesmentDetails[13]  =  "(D) HOME LOAN ACCOUNT OF NATIONAL HOUSING BANK|1|TF|80 CC|1";
	$a_invesmentDetails[14]  =  "(E) LIC - LIFE INSURANCE PREMIUM DIRECTLY PAID BY EMPLOYEE|1|TF|80 CC|1";
	$a_invesmentDetails[15]  =  "(F) ULIP 1971-ULIP LINKED INSURANCE PLAN FROM UTI|1|TF|80 CC|1";
	$a_invesmentDetails[16]  =  "(G) NSC - NATIONAL SAVING CERTIFICATE|1|TF|80 CC|1";
	$a_invesmentDetails[17]  =  "(H) DEPOSIT UNDER POST OFFICE SAVING BANK (CTD) RULES, 1959|1|TF|80 CC|1";
	$a_invesmentDetails[18]  =  "(I)  NSS - NATIONAL SAVING SCHEME|1|TF|80 CC|1";
	$a_invesmentDetails[19]  =  "(J) UTI - RETIREMENT BENEFIT PLAN|1|TF|80 CC|1";
	$a_invesmentDetails[20]  =  "(K) INFRASTRUCTURE INVESTMENT - NOTIFIED U/S 10 (23D)|1|TF|80 CC|1";
	$a_invesmentDetails[21]  =  "(L) MUTUAL FUNDS - NOTIFIED UNDER CLAUSE 23D OF SECTION 10|1|TF|80 CC|1";
	$a_invesmentDetails[22]  =  "(M) LESS - EQUITY LINK SAVING SCHEME OF MUTUAL FUNDs|1|TF|80 CC|1";
	$a_invesmentDetails[23]  =  "(N) TUITION FEES FOR FULL TIME EDUCATION TO INDIAN SCHOOL, UNIVERSITY|1|TF|80 CC|1";
	$a_invesmentDetails[24]  =  "(O) Contributory Provindent Fund (C. P. F.) maintined with in University by CPF Trust|1|TF|80 CC|1";
	$a_invesmentDetails[25]  =  "(P) Fixed Deposits in Banks (Period as per Income Tax Guidelines)|1|TF|80 CC|1";
	$a_invesmentDetails[26]  =  "(Q) Mediclaim Policy & Health Insurance|1|TF|80 CD|1";

	$a_employeesFilesUpoadingTypes    = array();
	$a_employeesFilesUpoadingTypes[1] = "Identity Proof File";
	$a_employeesFilesUpoadingTypes[2] = "Pan Card File";
	$a_employeesFilesUpoadingTypes[3] = "Compliance Form File";
    $a_employeesFilesUpoadingTypes[4] = "Resume File";
    $a_employeesFilesUpoadingTypes[5] = "Profile Photo File";
    $a_employeesFilesUpoadingTypes[6] = "Residence Proof File";
    $a_employeesFilesUpoadingTypes[7] = "Agreement File";
    $a_employeesFilesUpoadingTypes[8] = "Appointment File";
    $a_employeesFilesUpoadingTypes[9] = "Employee Agreement File";
    $a_employeesFilesUpoadingTypes[10]= "Cancelled Cheque File";
    $a_employeesFilesUpoadingTypes[11]= "Resigned File File";
    $a_employeesFilesUpoadingTypes[12]= "Form Eleven File";
    $a_employeesFilesUpoadingTypes[13]= "Form Eleven Revised File";
?>