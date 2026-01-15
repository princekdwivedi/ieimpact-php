<?php
	$customerOwnSateZone			=	$a_usaProvinces[$state];
	list($customerOwnSateName,$customerOwnSateTimeZone)	= explode("|",$customerOwnSateZone);	

	$convertTocustomerOwnZone		=	 $a_timeZoneStandardState[$customerOwnSateTimeZone];

	$convertedCustomerZone			= getTimeZoneConverterFromIST(CURRENT_DATE_INDIA,CURRENT_TIME_INDIA,$convertTocustomerOwnZone);

	list($convertedCustomerOwnDate,$convertedCustomerOwnTime)  =	 explode("|",$convertedCustomerZone);
	
	$estimatedDateTimeOfTheOrder    =	getNextCalculatedHours($convertedCustomerOwnDate,$convertedCustomerOwnTime,STANDRAD_ORDER_COMPLETE_TIME_HOURS);

	list($estimatedOrderDate,$estimatedOrderTime)	=	explode("=",$estimatedDateTimeOfTheOrder);

	$estimatedTimeOfTheOrder	    =	"By ".showTimeFormat($estimatedOrderTime)." Hrs on ".showDate($estimatedOrderDate);

	$estimatedDateTimeOfTheRushOrder=	getNextCalculatedHours($convertedCustomerOwnDate,$convertedCustomerOwnTime,RUSH_ORDER_COMPLETE_TIME_HOURS);

	list($estimatedRushOrderDate,$estimatedRushOrderTime)	=	explode("=",$estimatedDateTimeOfTheRushOrder);

	$estimatedTimeOfTheRushOrder	=	"By ".showTimeFormat($estimatedRushOrderTime)." Hrs on ".showDate($estimatedRushOrderDate);


	$estimatedISTDateTimeOfOrder	 =	getNextCalculatedHours($nowDateIndia,$nowTimeIndia,STANDRAD_ORDER_COMPLETE_TIME_HOURS);

	list($estimatedIstOrderDate,$estimatedIstOrderTime)	=	explode("=",$estimatedISTDateTimeOfOrder);

	$estimatedISTDateTimeOfRusheOrder =	getNextCalculatedHours($nowDateIndia,$nowTimeIndia,RUSH_ORDER_COMPLETE_TIME_HOURS);

	list($estimatedIstRushOrderDate,$estimatedIstRushOrderTime)	=	explode("=",$estimatedISTDateTimeOfRusheOrder);

?>