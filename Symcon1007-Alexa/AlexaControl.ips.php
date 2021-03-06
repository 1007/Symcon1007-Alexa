<?

	$debug = true;

	$control = $_GET['control'];
	
	if ( $control == "discover" )
		{
		AlexaControlDiscover();
		return;
		}

	if ( $control == "control" )
		{
		$deviceId = $_GET['id'];
		$value    = $_GET['value'];
		
		AlexaControlControl($deviceId,$value);
		return;
		}

	if ( $control == "policy" )
		{
		AlexaControlPolicy();
		return;
		}

	if ( $control == "terms" )
		{
		AlexaControlTerms();
		return;
		}

	if ( $control == "login" )
		{
		AlexaControlLogin();
		return;
		}

	if ( $control == "token" )
		{
		AlexaControlToken();
		return;
		}


function AlexaControlControl($deviceId,$value)
	{	
	
	GLOBAL $debug;
	
	$device = $AlexaConfig[$deviceId];
	
	if ( $debug ) IPS_LogMessage(basename(__FILE__), "Incoming Control DeviceID:[". $deviceId . "] Wert: [" .$value ."] Name :[".$device[1]."]" );


	switch ($device[1])	
		{
			case  "FS20 Schalter"	:	ModulNameFS20($device,$value);
										break;
			case  "ALLNET Schalter"	:	ModulNameALLNET($device,$value);
										break;		
			case "IPSLight"			:  	ModulNameIPSLight($device,$value);
										break;
			case "VARIABLE"			:  	ModulNameVariable($device,$value);
										break;
			case "SCRIPT"			  :  	ModulNameScript($device,$value);
										break;
		
		}
		
	}


function ModulNameScript($device,$value)	
  {
  $deviceId = $device[8];
  
  IPS_RunScript($deviceId);
  }
  
  
function ModulNameVariable($device,$value)
	{
	$deviceId = $device[8];

	SetValue($deviceId, $value);	
	}


	
function ModulNameFS20($device,$value)
	{
	GLOBAL $debug;
	
	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');
	IPSUtils_Include ('IPSComponentSwitch_FS20.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	$deviceId = $device[8];
	
	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameFS20 [". $deviceId . "] Zustand: [" .$value ."]" );
	
  FS20_SwitchMode($deviceId,$value);
  
	}

function ModulNameALLNET($device,$value)
	{
	GLOBAL $debug;
	
	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');
	IPSUtils_Include ('IPSComponentSwitch_ALLNET.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	$deviceId = $device[8];
	
	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameALLNET [". $deviceId . "] Wert: [" .$value ."]" );
	
  ALL_SwitchMode($deviceId,$value);

	
	}


	
function ModulNameIPSLight($device,$value)
	{
	GLOBAL $debug;

	$lichtid   = $device[8];
	$lichttype = false;
	
	IPSUtils_Include ('IPSLight.inc.php', 'IPSLibrary::app::modules::IPSLight');

	$LightArray = IPSLight_GetLightConfiguration();

  $lichtname = IPS_GetName($lichtid);
  $parent    = IPS_GetParent($lichtid);
 
  
	foreach( $LightArray as $Light )
		{ 
		if ( $Light[0] == $lichtname )
			{ 
			$lichttype =  $Light[2];
			break;
			}	
		}


	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameIPSLight [". $lichtid . "] Wert: [" .$value ."] Type : [".$lichttype."]".$levelid );

	if ( $lichttype == "Switch" )
		{
		IPSLight_SetSwitch($lichtid, $value);
		}
		

	if ( $lichttype == "Dimmer" )
		{
		if ( $value == 0 OR $value == 1 )
			{
			IPSLight_SetDimmerAbs($levelid,100);
			IPSLight_SetSwitch($lichtid, $value);		
			}
		else
			{
			IPSLight_SetDimmerAbs($levelid,$value);			
			}			
		}


	if ( $lichttype == "RGB" )
		{
		$lightManager = new IPSLight_Manager();
		
		if ( $value == 0 OR $value == 1)
			{
			IPSLight_SetSwitch($lichtid, $value);		

			
			//$farbe = 0;
			
			//$id = $lightManager->GetColorIdByName($lichtname);
			//$id = $lightManager->SetRGB($id,$farbe);
			}
			
		
		
		}
	}
	

//******************************************************************************
//	Alexa Control Discover
//******************************************************************************
function AlexaControlDiscover()
	{

	$json = generateJSONForDiscoveredAlexaDevices();
	
	echo $json;

	}

function generateJSONForDiscoveredAlexaDevices()
	{
	$guid = "{2C8C1801-D0FD-4424-A4DD-92CAD9D3AF3F}";
	$modul = IPS_GetInstanceListByModuleID($guid);
	
	$parent = $modul[0];
	
	$configid = IPS_GetVariableIDByName("Alexa Konfiguration", $parent);
	
	$json = GetValue($configid);
	
	$configarray = json_decode($json,true);
	
	//print_r($configarray);
	
	
	$json = "[";

	foreach($configarray as $key => $value)
		{
		$json .= generateJSONForDevice($key,$value) . ",";
		}
	return substr($json, 0, strlen($json)-1)."]";

	}

function generateJSONForDevice($key,$value)
	{
	define("ACTION_ALL"		, "[ \"turnOn\", \"turnOff\", \"setPercentage\", \"incrementPercentage\", \"decrementPercentage\" ]");	
	define("ACTION_SWITCH"	, "[ \"turnOn\", \"turnOff\" ]");	

	echo $key;
	
	print_r($value);
	
	
	$applianceId = $key;

	$manufacturerName 			= $value['vendor'];
	$modelName 					= $value['type'];
	$version 					= "0.0.1";
	$friendlyName 				= $value['alexaname'];
	$friendlyDescription		= $value['alexaname'];
	$isReachable        		= true;
	$actions            		= ACTION_ALL;   
	$additionalApplianceDetails = "IPSymcon Modul";

	$json = "{
		\"applianceId\":\"".$key."\",
		\"manufacturerName\":\"".$manufacturerName."\",
		\"modelName\":\"".$modelName."\",
		\"version\":\"".$version."\",
		\"friendlyName\":\"".$friendlyName."\",
		\"friendlyDescription\":\"".$friendlyDescription."\",
		\"isReachable\":\"".$isReachable."\",
		\"actions\":".$actions .",
		\"additionalApplianceDetails\":{
		\"extraDetail1\":\"".$additionalApplianceDetails."\"
		   
		}
	}";
	$json = str_replace("\t", "", $json);
	$json = str_replace("\r\n", "", $json);

	IPS_LogMessage(basename(__FILE__), "JSON:" .$json);

	return $json;

	}	

function generateJSONForDiscoveredAlexaDevicesmitConfig()
	{
	
	GLOBAL $AlexaConfig;
	
	$json = "[";

	foreach($AlexaConfig as $key => $value)
		{
		$json .= generateJSONForDevice($key) . ",";
		}
	return substr($json, 0, strlen($json)-1)."]";

	}

function generateJSONForDevicemitKonfig($key)
	{
	GLOBAL $AlexaConfig;
		
	$applianceId = $key;

	$manufacturerName 			= $AlexaConfig[$key][0];
	$modelName 					= $AlexaConfig[$key][1];
	$version 					= $AlexaConfig[$key][2];
	$friendlyName 				= $AlexaConfig[$key][3];
	$friendlyDescription		= $AlexaConfig[$key][4];
	$isReachable        		= $AlexaConfig[$key][5];
	$actions            		= $AlexaConfig[$key][6];   
	$additionalApplianceDetails = $AlexaConfig[$key][7];

	$json = "{
		\"applianceId\":\"".$key."\",
		\"manufacturerName\":\"".$manufacturerName."\",
		\"modelName\":\"".$modelName."\",
		\"version\":\"".$version."\",
		\"friendlyName\":\"".$friendlyName."\",
		\"friendlyDescription\":\"".$friendlyDescription."\",
		\"isReachable\":\"".$isReachable."\",
		\"actions\":".$actions .",
		\"additionalApplianceDetails\":{
		\"extraDetail1\":\"".$additionalApplianceDetails."\"
		   
		}
	}";
	$json = str_replace("\t", "", $json);
	$json = str_replace("\r\n", "", $json);

	IPS_LogMessage(basename(__FILE__), "JSON:" .$json);

	return $json;

	}	
	
	
//******************************************************************************
//	Alexa Control Policy
//******************************************************************************
function AlexaControlPolicy()	
	{
	echo "Privacy Policy for IP Symcon Skill";
	}
	
//******************************************************************************
//	Alexa Control Terms
//******************************************************************************
function AlexaControlTerms()	
	{
	echo "Terms of use";
	}		

//******************************************************************************
//	Alexa Control Login
//******************************************************************************
function AlexaControlLogin()	
	{
	echo "AlexaControlLogin to IP Symcon";
	echo "<br />";
	echo "<a href=\"" . $_GET['redirect_uri'] . "?code=123456789" . "&state=" . $_GET['state'] . "\">Connect my IPS with Alexa</a>";
	echo "<br />";
	}		

//******************************************************************************
//	Alexa Control Token
//******************************************************************************
function AlexaControlToken()	
	{
	echo '{ "access_token": "some_random_sequence", "token_type": "bearer" }';
	}		


?>





?>