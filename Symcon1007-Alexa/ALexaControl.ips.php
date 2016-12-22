<?
	IPSUtils_Include ("AlexaConfig.ips.php");

	GLOBAL $AlexaConfig;

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
	GLOBAL $AlexaConfig;
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
		
		}
		
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
	
	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameFS20 [". $deviceId . "] Wert: [" .$value ."]" );
	
	$component =  new IPSComponentSwitch_FS20($deviceId);
	$component->SetState($value);
	
	}

function ModulNameALLNET($device,$value)
	{
	GLOBAL $debug;
	
	IPSUtils_Include ('IPSComponent.class.php', 'IPSLibrary::app::core::IPSComponent');
	IPSUtils_Include ('IPSComponentSwitch_ALLNET.class.php', 'IPSLibrary::app::core::IPSComponent::IPSComponentSwitch');

	$deviceId = $device[8];
	
	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameALLNET [". $deviceId . "] Wert: [" .$value ."]" );
	
	$component =  new IPSComponentSwitch_ALLNET($deviceId);
	$component->SetState($value);
	
	}


	
function ModulNameIPSLight($device,$value)
	{
	GLOBAL $debug;

	$lichtname = $device[8];
	$lichttype = false;
	
	IPSUtils_Include ('IPSLight.inc.php', 'IPSLibrary::app::modules::IPSLight');

	$LightArray = IPSLight_GetLightConfiguration();
	foreach( $LightArray as $Light )
		{
		if ( $Light[0] == $lichtname )
			{ 
			$lichttype =  $Light[2];
			break;
			}	
		}

	if ( $debug ) IPS_LogMessage(basename(__FILE__), "ModulNameIPSLight [". $lichtname . "] Wert: [" .$value ."] Type : [".$lichttype."]" );

	if ( $lichttype == "Switch" )
		{
		IPSLight_SetSwitchByName($lichtname, $value);
		}
		

	if ( $lichttype == "Dimmer" )
		{
		if ( $value == 0 OR $value == 1 )
			{
			IPSLight_DimAbsoluteByName($lichtname,100);
			IPSLight_SetSwitchByName($lichtname, $value);		
			}
		else
			{
			IPSLight_DimAbsoluteByName($lichtname,$value);			
			}			
		}


	if ( $lichttype == "RGB" )
		{
		$lightManager = new IPSLight_Manager();
		
		if ( $value == 0 OR $value == 1)
			{
			IPSLight_SetSwitchByName($lichtname, $value);		

			
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
	IPSUtils_Include ("AlexaConfig.ips.php");

	$json = generateJSONForDiscoveredAlexaDevices();
	
	echo $json;

	}

function generateJSONForDiscoveredAlexaDevices()
	{
	
	GLOBAL $AlexaConfig;
	
	$json = "[";

	foreach($AlexaConfig as $key => $value)
		{
		$json .= generateJSONForDevice($key) . ",";
		}
	return substr($json, 0, strlen($json)-1)."]";

	}



function generateJSONForDevice($key)
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