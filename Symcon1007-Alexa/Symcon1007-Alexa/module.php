<?
	class Alexa extends IPSModule
	 {
		


    //**************************************************************************
    //
    //**************************************************************************    
    public function Create()
      {
      //Never delete this line!
      parent::Create();
      
      $this->RegisterPropertyBoolean("Logging", false);  
      
      $this->RegisterPropertyInteger("countinstance", 10);
      
      for ($i=1; $i<=400; $i++)
			 {
				$this->RegisterPropertyInteger("objectidinstance".$i, 0);
				$this->RegisterPropertyString("alexaname".$i, "");
				$this->RegisterPropertyInteger("vendor".$i, 0);
				$this->RegisterPropertyInteger("type".$i, 0);
			}

      }
    

    //**************************************************************************
    //
    //**************************************************************************    
		public function ApplyChanges()
		  {
			//Never delete this line!
			parent::ApplyChanges();

      //$id = $this->RegisterVariableInteger("name"    , "Geschlecht","WITHINGS_M_Gender",2);
      
      $script = $_IPS['SELF'];
      $script = "[".$script."]"; 
      $this->Logging($script);
       
      $sid = $this->RegisterScript("Hook", "Hook", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/Symcon1007-Alexa/Symcon1007-Alexa/ALexaControl.ips.php\");\n");
			$this->RegisterHook("/hook/alexacontrol", $sid);
	    IPS_SetHidden($sid, true);
 			$configid = $this->RegisterVariableString("AlexaConfig", "Alexa Konfiguration");
			IPS_SetHidden($configid, true);
			
			$this->ValidateConfiguration();


      $this->Logging("ApplyChanges");
      $this->SetStatus(102);
      //Update
     	$this->Update();

       }

    //**************************************************************************
    //
    //**************************************************************************    
    public function Update()
      {

 	      $source = IPS_GetKernelDir() ."/modules/Symcon1007-Alexa/Symcon1007-Alexa/Symcon1007-AlexaConfig.ips.php";
	     $target = IPS_GetKernelDir() ."/scripts/Symcon1007-AlexaConfig.ips.php";

	     if ( !file_exists($target) )
		        copy($source,$target);

      $this->Logging("Update Config");
                
      return true;
      }
      
      
    //**************************************************************************
    //
    //**************************************************************************    
    public function Destroy()
      {
      
       
      //Never delete this line!
      parent::Destroy();
      }




    //**************************************************************************
    //
    //**************************************************************************    
		public function RequestAction($Ident, $Value)
		  {
			

		
		  }


 

		
    //**************************************************************************
    //  Logging
    //**************************************************************************    
    private function Logging($text)
      {
      if ( $this->ReadPropertyBoolean("Logging") == false )
        return;
      
      $file = 'Alexa.log';
      
      $ordner = IPS_GetLogDir() . "Symcon1007-Alexa/";
      if ( !is_dir ( $ordner ) )
		    mkdir($ordner,0777,true); // Ordner erstellen

      if ( !is_dir ( $ordner ) )
	     return;
  
	   $time = date("d.m.Y H:i:s");
	   $logdatei = IPS_GetLogDir() . "Symcon1007-Alexa/" . $file;
	   $datei = fopen($logdatei,"a+");
	   fwrite($datei, $time ." ". $text . chr(13).chr(10));
	   fclose($datei);

      }

    
    


  
  //****************************************************************************
  
 private function RegisterHook($Hook, $TargetID) {
			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
			if(sizeof($ids) > 0) {
				$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
				$found = false;
				foreach($hooks as $index => $hook) {
					if($hook['Hook'] == "/hook/alexacontrol") {
						if($hook['TargetID'] == $TargetID)
							return;
						$hooks[$index]['TargetID'] = $TargetID;
						$found = true;
					}
				}
				if(!$found) {
					$hooks[] = Array("Hook" => "/hook/alexacontrol", "TargetID" => $TargetID);
				}
				IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
				IPS_ApplyChanges($ids[0]);
			}
		}

		private function ValidateConfiguration()
		{
			$change = false;
			
			$countinstance = $this->ReadPropertyInteger("countinstance");
			if($countinstance == 0)
			{
				$errorid = 208;
				$this->SetStatus($errorid); //Amazon Echo Select min one instance
				//break;
			}
			if($countinstance > 400)
					$countinstance = 400;
			$vendorcheck = false;
			$alexanamecheck = false;
			$objectidinstancecheck = false;
			$typecheck = false;
			// Echo instances
			for ($i=1; $i<=$countinstance; $i++)
			{
				${"objectidinstance".$i} = $this->ReadPropertyInteger('objectidinstance'.$i);
				${"alexaname".$i} = $this->ReadPropertyString('alexaname'.$i);
				${"vendor".$i} = $this->ReadPropertyInteger('vendor'.$i);
				${"type".$i} = $this->ReadPropertyInteger('type'.$i);
				//Vendorcheck
        IPS_LogMessage("Vedorcheck", ${"vendor".$i}) ;
				if(${"vendor".$i} === 0)
				{
					$errorid = 300+$i;
          IPS_LogMessage("Vedorcheck", $errorid ) ;
					$this->SetStatus($errorid); // Amazon Alexa : select a vendor , errorid 301 - 700
					break;
				}
				else
				{
					$vendorcheck = true;
				}	
				//check Alexa Name
				if (${"alexaname".$i} === "")
				{
					$errorid = 700+$i;
					$this->SetStatus($errorid); // Amazon Alexa :  missing value, enter value in field alexa name, errorid 701 - 1100
					break;
				}
				else
				{
					$alexanamecheck = true;
				}
				//check instance
				if (${"objectidinstance".$i} === 0)
				{
					$errorid = 1100+$i;
					$this->SetStatus($errorid); // Amazon Alexa : select a instance , errorid 1101 - 1500
					break;
				}
				else
				{
					$objectidinstancecheck = true;
				}
				//check Alexa type
				if (${"type".$i} === 0)
				{
					$errorid = 1500+$i;
					$this->SetStatus($errorid); // Amazon Alexa : select a type , errorid 1501 - 1900
					break;
				}
				else
				{
					$typecheck = true;
				}
			}

			if ($objectidinstancecheck == true && $alexanamecheck == true && $vendorcheck == true && $typecheck == true) // OK
			{
				$this->WriteConfig();
				$this->SetStatus(102);
			}
		}	
		
		public function WriteConfig()
		{
			$countinstance = $this->ReadPropertyInteger("countinstance");
			$config = array();
			// Echo instances
			for ($i=1; $i<=$countinstance; $i++)
			{
        if ($this->ReadPropertyString('alexaname'.$i) == "" )
          continue;
				${"objectidinstance".$i} = $this->ReadPropertyInteger('objectidinstance'.$i);
				${"alexaname".$i} = $this->ReadPropertyString('alexaname'.$i);
				${"vendor".$i} = $this->ReadPropertyInteger('vendor'.$i);
				${"type".$i} = $this->ReadPropertyInteger('type'.$i);
				$config[$i] = array("alexaname" => ${"alexaname".$i}, "vendor" => ${"vendor".$i}, "type" => ${"type".$i}, "instance" => ${"objectidinstance".$i});  
			}
			$configjson = json_encode($config);
			SetValue($this->GetIDForIdent("AlexaConfig"), $configjson) ;
		}
		
	

//******************************************************************************
//  Formerstellung
//******************************************************************************
		//Configuration Form
		public function GetConfigurationForm()
		{
			$countinstance = $this->ReadPropertyInteger("countinstance");
			$formhead = $this->FormHead();
			$formstatus = $this->FormStatus();
			$formalexa = $this->FormAlexa($countinstance);
			
			$formelementsend = '{ "type": "Label", "label": "__________________________________________________________________________________________________" }';
			if($countinstance == 0)// keine Auswahl
			 {
				return	'{ '.$formhead.'],'.$formstatus.' }';
			 }
			
			elseif ($countinstance > 0) // Alexa Auswahl
			{   IPS_LogMessage(basename(__FILE__), $formstatus);
				//$formactions = $this->FormActions($countinstance);
				return	'{ '.$formhead.','.$formalexa.$formelementsend.'],'.$formstatus.' }';
			}
		}


		protected function FormHead()
		{
			$form = '"elements":
			[
				{ "type": "Label", "label": "Konfiguration für Amazon Echo nach IP-Symcon" }
		
				';
			
			return $form;
		}

		protected function FormActions($countrequestvars)
		{			
			$form = '"actions": [
			{ "type": "Label", "label": "Write Configuration as JSON" },
			{ "type": "Button", "label": "Write Config", "onClick": "AmazonEcho_WriteConfig($id);" },
			{ "type": "Label", "label": "______________________________________________________________________________________________________" }]';
			return  $form;
		}

		protected function FormStatus()
		{
			$form = '"status":
            [
                {
                    "code": 101,
                    "icon": "inactive",
                    "caption": "creating instance."
                },
				{
                    "code": 102,
                    "icon": "active",
                    "caption": "configuration created."
                },
				'.$this->FormStatusErrorVendor().'
                {
                    "code": 104,
                    "icon": "inactive",
                    "caption": "interface closed."
                },
				'.$this->FormStatusErrorAlexaName().'
				{
                    "code": 201,
                    "icon": "inactive",
                    "caption": "select number of values in module."
                },
				'.$this->FormStatusErrorType().'
				{
                    "code": 206,
                    "icon": "error",
                    "caption": "field must not be empty."
                },
				'.$this->FormStatusErrorInstance().'
				{
                    "code": 208,
                    "icon": "error",
                    "caption": "Select min one instance."
                }
			
            ]';
            
             
			return $form;
		}


		protected function FormStatusErrorVendor() // errorid 301 - 700
		{
			$form = "";
			for ($i=1; $i<=400; $i++)
			{
				$errorid = 300+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "Amazon Alexa: select a vendor for vendor '.$i.'."
                },'; 
			}
			return $form;
		}
		
		protected function FormStatusErrorAlexaName() // errorid 701 - 1100
		{
			$form = "";
			for ($i=1; $i<=400; $i++)
			{
				$errorid = 700+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "Amazon Alexa: missing value, enter value in field alexa name '.$i.'"
                },'; 
			}
			return $form;
		}
		
		protected function FormStatusErrorType() // errorid 1101 - 1500
		{
			$form = "";
			for ($i=1; $i<=400; $i++)
			{
				$errorid = 1100+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "Amazon Alexa: select a type for type '.$i.'."
                },'; 
			}
			return $form;
		}
		
		protected function FormStatusErrorInstance() // errorid 1501 - 1900
		{
			$form = "";
			for ($i=1; $i<=400; $i++)
			{
				$errorid = 1500+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "Amazon Alexa: select an instance for instance '.$i.'."
                },'; 
			}
			return $form;
		}
		
		protected function FormAlexa($countinstance)
		{
			$form = '{ "type": "Label", "label": "____________________________________________________________________________________________" },'
			.$this->FormAlexaInstances($countinstance);
			return $form;
		}
		
		protected function FormAlexaInstances($countinstance)
		{
			if ($countinstance > 0)
			{
				if($countinstance > 400)
				$countinstance = 400;
				$form = "";
				for ($i=1; $i<=$countinstance; $i++)
				{
					
					$form .= '{ "name": "alexaname'.$i.'", "type": "ValidationTextBox", "caption": "'.$i.'. Alexa Name" },';
         $form .= '{ "type": "Select", "name": "vendor'.$i.'", "caption": "'.$i.'. Hersteller",
								"options": [
									{ "label": "Auswahl Hersteller", "value": 0 },
									{ "label": "IPSymcon", "value": 1 },
									{ "label": "ELV", "value": 2 },
									{ "label": "ALLNET", "value": 3 }
									]
								},';					
          $form .= '{ "type": "Select", "name": "type'.$i.'", "caption": "'.$i.'. Device Type ",
								"options": [
									{ "label": "Auswahl Type", "value": 0 },
									{ "label": "Variable", "value": 1 },
									{ "label": "Script", "value": 2 },
									{ "label": "FS20 Schalter", "value": 3 } 
									]
								},';
					 
 					$form .= '{ "type": "SelectInstance", "name": "objectidinstance'.$i.'", "caption": "'.$i.'. Instance" },';
 
									
				}
				
			}
			else
			{
				$form = "";
			}
			return $form;
		}

//******************************************************************************

}

    
  
?>
