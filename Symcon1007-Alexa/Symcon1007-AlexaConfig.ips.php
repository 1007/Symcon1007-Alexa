<?
//******************************************************************************
//	Konfiguration :
//		applianceId = array_key
//		manufacturerName
//		modelName
//		version
//		friendlyName
//		friendlyDescription
//		isReachable
//		actions
//		additionalApplianceDetails
//      IPSymcon ID / DeviceName
//******************************************************************************

	define("ACTION_ALL"		, "[ \"turnOn\", \"turnOff\", \"setPercentage\", \"incrementPercentage\", \"decrementPercentage\" ]");	
	define("ACTION_SWITCH"	, "[ \"turnOn\", \"turnOff\" ]");	
	

	GLOBAL 	$AlexaConfig;
	$AlexaConfig = array (
	
	//IPSymcon Variable
	array("IPSYMCON","VARIABLE"			, "1.0.0", "Wohnung"					,"Wohnung in Wachmodus schalten"	,"true",ACTION_ALL		,"Beschreibung" 	, 47488	),

	//IPSymcon Script
	array("IPSYMCON","SCRIPT"			, "1.0.0", "Test"						,"Test"								,"true",ACTION_ALL		,"Beschreibung" 	, 20888	),
		
	// Schalter
	array("ELV"		,"FS20 Schalter"	, "1.0.0", "Drucker"					,"Drucker im Arbeitszimmer"			,"true",ACTION_SWITCH	,"Beschreibung"		, 30543 ),

	array("ALLNET"	,"ALLNET Schalter"	, "1.0.0", "Ladestation"				,"Ladestation"						,"true",ACTION_SWITCH	,"Beschreibung"		, 51987 ),
	
	// IPSLight
	array("IPSYMCON","IPSLight"			, "1.0.0", "Deckenlicht Wohnzimmer"		,"Deckenlicht Wohnzimmer"			,"true",ACTION_ALL		,"Beschreibung"		, 48016 ),
	array("IPSYMCON","IPSLight"			, "1.0.0", "Deckenlicht Esszimmer"		,"Deckenlicht Esszimmer"			,"true",ACTION_ALL		,"Beschreibung"		, 16378 ),

	//




					);

//******************************************************************************


?>