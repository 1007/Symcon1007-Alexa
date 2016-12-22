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
      $this->RegisterPropertyBoolean("Modulaktiv", true);  

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



}

    
  
?>
