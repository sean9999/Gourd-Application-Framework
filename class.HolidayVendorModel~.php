<?php
class HolidayVendorModel {

	private $defaultData;
	public $fieldset;
	
	function HolidayVendorModel($jsondata = null){
		
		if(($jsondata == null) && isset($_POST)){
			$this->defaultDataFromPost();
		}else{
			$this->defaultDataFromJson($jsondata);
		}
		$this->buildFieldset();
			
		
	}
	
	private function buildFieldset(){
		
		$this->fieldset = array( 
				'dealername' 		=> array('label' => 'Dealer Name', 							'type' => 'text', 				'value' => (!empty($this->defaultData['Dealer Name'])) ? $this->defaultData['Dealer Name'] : '', 'validation' => 'required'),
				'websiteurl' 		=> array('label' => 'Website URL', 							'type' => 'text', 				'value' => (!empty($this->defaultData['Website URL'])) ? $this->defaultData['Website URL'] : '', 'validation' => 'required'),
				'province'			=> array('label' => 'Province', 							'type' => 'select[multiple]', 	'value' => (!empty($this->defaultData['Province'])) ? $this->defaultData['Province'] : '', 'validation' => 'required'),
				'logoen' 			=> array('label' => 'Logo[en]', 							'type' => 'text', 				'value' => (!empty($this->defaultData['Logo']['en'])) ? $this->defaultData['Logo']['en'] : '', 'validation' => 'required'),
				'logofr' 			=> array('label' => 'Logo[fr]', 							'type' => 'text', 				'value' => (!empty($this->defaultData['Logo']['fr'])) ? $this->defaultData['Logo']['fr'] : '', 'validation' => 'required'),
				'product1en' 		=> array('label' => 'EOS Rebel T3 (English URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['1']['en'])) ? $this->defaultData['Products']['1']['en'] : ''),
				'product1fr' 		=> array('label' => 'EOS Rebel T3 (French URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['1']['fr'])) ? $this->defaultData['Products']['1']['fr'] : ''),
				'product2en' 		=> array('label' => 'PowerShot A2300 (English URL)', 		'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['2']['en'])) ? $this->defaultData['Products']['2']['en'] : ''),
				'product2fr' 		=> array('label' => 'PowerShot A2300 (French URL)', 		'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['2']['fr'])) ? $this->defaultData['Products']['2']['fr'] : ''),
				'product3en' 		=> array('label' => 'EOS Rebel T4i (English URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['3']['en'])) ? $this->defaultData['Products']['3']['en'] : ''),
				'product3fr' 		=> array('label' => 'EOS Rebel T4i (French URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['3']['fr'])) ? $this->defaultData['Products']['3']['fr'] : ''),
				'product4en' 		=> array('label' => 'EOS Rebel T3i (English URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['4']['en'])) ? $this->defaultData['Products']['4']['en'] : ''),
				'product4fr' 		=> array('label' => 'EOS Rebel T3i (French URL)', 			'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['4']['fr'])) ? $this->defaultData['Products']['4']['fr'] : ''),
				'product5en' 		=> array('label' => 'PowerShot ELPH 520 HS (English URL)', 	'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['5']['en'])) ? $this->defaultData['Products']['5']['en'] : ''),
				'product5fr' 		=> array('label' => 'PowerShot ELPH 520 HS (French URL)', 	'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['5']['fr'])) ? $this->defaultData['Products']['5']['fr'] : ''),
				'product6en' 		=> array('label' => 'EOS 7D (English URL)', 				'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['6']['en'])) ? $this->defaultData['Products']['6']['en'] : ''),
				'product6fr' 		=> array('label' => 'EOS 7D (French URL)', 					'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['6']['fr'])) ? $this->defaultData['Products']['6']['fr'] : ''),
				'product7en' 		=> array('label' => 'PowerShot SX500 IS (English URL)', 	'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['7']['en'])) ? $this->defaultData['Products']['7']['en'] : ''),
				'product7fr' 		=> array('label' => 'PowerShot SX500 IS (French URL)', 		'type' => 'text', 				'value' => (!empty($this->defaultData['Products']['7']['fr'])) ? $this->defaultData['Products']['7']['fr'] : '')
				);
		
		
	}
	
	private function defaultDataFromJson($jsondata){
	
	
		$this->defaultData = $jsondata;//json_decode($jsondata, true);
	
	}
	
	private function defaultDataFromPost(){
	
		$jsonobj = json_decode($this->getJsonFromPost($_POST), true);
		$this->defaultData = $jsonobj;//json_decode($jsondata, true);
	
	}
	
	public function isValidPost($post){
	
		$validflag = true;
		
		if(empty($post['dealername'])) $validflag=false;
		if(empty($post['websiteurl'])) $validflag=false;
		if(empty($post['province'])) $validflag=false;
		if(empty($post['logoen'])) $validflag=false;
		if(empty($post['logofr'])) $validflag=false;
		return $validflag;
	
	}
	
	public function getJsonFromPost($post){
	
		$prodarr = array();
		$logoarr = array();
		$provarr = (!empty($post['province'])) ? $post['province'] : array();
		$jsonstring = "{";
		$firstiteration = true;
		foreach($post as $field => $value){
			
			if($value!=''){
			
				$fieldname = $field;
				
				//check for product fields
				$pos = strpos($field, "product");
				if($pos!==false){
				
					$prodarr[$field] = $value;
					$fieldname = "product";
				}

				//check for logo fields
				$pos = strpos($field, "logo");
				if($pos!==false){
				
					$logoarr[$field] = $value;
					$fieldname = "logo";
					
				}

				switch($fieldname){
					
					case 'province':
					//don't add to json yet
					break;
					case 'product':
					//don't add to json yet
					break;
					case 'logo':
					//don't add to json yet
					break;
					default:
					if($firstiteration==false) $jsonstring .= ",";
					$jsonstring .= '"'.$this->fieldset[$field]['label'].'":"'.$value.'"';
					
					break;
					
				}
				
				$firstiteration=false;
				
			}
			
		}
		
		if(count($provarr)>0){
			//let's create the "Province" property
			$jsonstring .= ",";
			$jsonstring .= '"Province":{';
			$firstiteration = true;
			foreach($provarr as $index => $value){
				if($firstiteration==false) $jsonstring .= ",";
				$jsonstring .= '"'.$index.'":"'.$value.'"';
				$firstiteration = false;
									
			}
			$jsonstring .= "}";//close province object
		}//end if province array not empty

		
		if(count($logoarr)>0){
			//let's create the "Logo" property
			$jsonstring .= ",";
			$jsonstring .= '"Logo":{';
			$firstiteration = true;
			foreach($logoarr as $field => $value){
				
				$lang = substr($field, -2);
				if($firstiteration==false) $jsonstring .= ",";
				if($lang=="en") $jsonstring .= '"en":"'.$value.'"';
				if($lang=="fr") $jsonstring .= '"fr":"'.$value.'"';
				
				$firstiteration=false;
									
			}
			$jsonstring .= "}";//close logo object
		}//end if logo array not empty

		
		if(count($prodarr)>0){
			//let's create the "Products" property
			$jsonstring .= ",";
			$jsonstring .= '"Products":{';
			//$newarr["Products"] = $prodarr;
		
			$ind = '';
			foreach($prodarr as $field => $value){
				
				
				$nextind = substr($field, -3, 1);
				$lang = substr($field, -2);
				if($ind!=$nextind){
					if($ind!='') $jsonstring .= "}, ";
					$jsonstring .= '"'.$nextind.'": {';
					if($lang=="en") $jsonstring .= '"en":"'.$value.'"';
					if($lang=="fr") $jsonstring .= '"fr":"'.$value.'"';
				}else{
					$jsonstring .= ",";
					if($lang=="en") $jsonstring .= '"en":"'.$value.'"';
					if($lang=="fr") $jsonstring .= '"fr":"'.$value.'"';
					
				}
				
				$ind = $nextind;
									
			}
			$jsonstring .= "}";//close last product index object
			$jsonstring .= "}";//close product object
		}//end if product array not empty
		$jsonstring .= "}";//close json object
		
		return $jsonstring;
	
	}


}


?>