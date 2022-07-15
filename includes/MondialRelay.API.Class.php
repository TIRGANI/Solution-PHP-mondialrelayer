<?php 
require_once('lib/nusoap.php');
require_once('dto/PointRelais.dto.php');
require_once('helpers/ParcelShopHelper.Class.php');
require_once('helpers/ApiHelper.Class.php');

/**
 * API Mondial Relay
 */
class MondialRelayWebAPI {
	
	/**
	 * URL du web service Mondial Relay
	 * @var string
	 * @access private
	 */
    public $_APIEndPointUrlV1 = "http://api.mondialrelay.com/";

	/**
	 * URL du web service Mondial Relay
	 * @var string
	 * @access private
	 */
    public $_APIEndPointUrlV2 = "https://connect-api.mondialrelay.com/";
	
	/**
	 * Mondial relay Customer ID (Brand ID)
	 * @var string
	 * @access private
	 */
	public $_APINumericBrandId;
	
	/**
	 * API File Endpoint
	 * @var string
	 * @access private
	 */
	private $_APIFileEndPointV1 = "Web_Services.asmx?WSDL";
	
	/**
	 * Nusoap Soap client isntance
	 * @var nusoap_client
	 * @access private
	 */
	private $_SoapClient ;
	/**
	 * Mondial Relay Customer Extranet Root Url
	 * @var string
	 * @access private
	 */
	private $_MRConnectUrl = "http://connect.mondialrelay.com";
	/**
	 * Mondial Relay Stickers Root URL
	 * @var string
	 * @access private
	 */
	private $_MRStickersUrl = "http://www.mondialrelay.com";
	/**
	 * API version (V1.0 par défaut sinon V2.0)
	 * @var string
	 * @access public
	 */
	public $_Api_Version 	= "1.0";
	
	/**
	 * API login for API V1
	 * @var string
	 * @access public
	 */
	public $_Api_CustomerCode 	= "";
	
	/**
	 * API password for API V1
	 * @var string
	 * @access public
	 */
	public $_Api_SecretKey= "";
	
	/**
	 * API user for API V2  
	 * @var string
	 * @access public
	 */	
	public $_Api_User 	= "";
	
	/**
	 * API password for API V2 
	 * @var string
	 * @access public
	 */
	 public $_Api_Password = "";
	
	/**
	 * Debug mode enabled or not
	 * @var boolean
	 * @access private
	 */
	public $_Debug = false;
	
	/**
	* constructor
	*
	* @param    string $ApiEndPointUrl Mondial Relay API EndPoint
	* @param    string $ApiLogin Mondial Relay API Login (provided by your technical contact)
	* @param    string $ApiPassword Mondial Relay API Password (provided by your technical contact)
	* @param    string $ApiBrandId Mondial Relay API Numeric Brand ID (2 digits) (provided by your technical contact)
	* @access   public
	*/
	public function __construct() { 
		$this->_SoapClient = new nusoap_client($this->_APIEndPointUrlV1 . $this->_APIFileEndPointV1, true);
		$this->_SoapClient->soap_defencoding = 'utf-8';
	} 
	

    public function __destruct(){
       
    }
	
	/**
	* Search parcel Shop Arround a postCode according to filters
	*
	* @param    string $CountryCode Country Code (ISO) of the post code
	* @param    string $PostCode Post Code arround which you want to find parcel shops
	* @param    string $DeliveryMode Optionnal - Delivery Mode Code Filter (3 Letter code, 24R, DRI). Will restrict the results to parcelshops available with this delivery Mode
	* @param    int $ParcelWeight Optionnal - Will restrict results to parcelshops compatible with the parcel Weight in gramms specified
	* @param    int $ParcelShopActivityCode Optionnal - Will restrict results to parcelshops regarding to their actity code
	* @param    int $SearchDistance Optionnal - Will restrict results to parcelshops in the perimeter specified in km
	* @param    int $SearchOpenningDelay Optionnal - If you intend to give us the parcel in more than one day, you can specified a delay in order to filter ParcelShops according to their oppening periods
	* @access   public
	* @return   Array of parcelShop
	*/
    public function SearchParcelShop($CountryCode, $PostCode, $DeliveryMode = "", $ParcelWeight = "", $ParcelShopActivityCode="",$SearchDistance="",$SearchOpenningDelay = "") {
        
		$params = array(
			'Enseigne'	=> str_pad($this->_Api_CustomerCode,8),
			'Pays'	=> $CountryCode,
			'Ville'	=> "",
			'CP'	=> $PostCode,
			'Taille'	=> "",
			'Poids'	=> $ParcelWeight,
			'Action'	=> $DeliveryMode,
			'RayonRecherche'	=> $SearchDistance,
			'TypeActivite'	=> $ParcelShopActivityCode,
			'DelaiEnvoi' => $SearchOpenningDelay
		);
		
		$result = $this->CallWebApi("WSI3_PointRelais_Recherche", $this->AddSecurityCode($params));
		
		foreach($result["WSI3_PointRelais_RechercheResult"]["PointsRelais"]["PointRelais_Details"] as $val){
			$parcelShopArray[] = ParcelShopHelper::ParcelShopResultToDTO($val);
		}
		
		return $parcelShopArray;
    }
	
	/**
	* get the parcel shop datas (adress, openning, geodata, picture url, ...)
	*
	* @param    string $CountryCode Country Code (ISO) of the post code
	* @param    string $ParcelShopId parcel Shop ID
	* @access   public
	* @return   ParcelShop
	*/
	public function GetParcelShopDetails($CountryCode, $ParcelShopId) {
        $params = array(
			'Enseigne'	=> $this->_Api_CustomerCode,
			'Pays'	=> $CountryCode,
			'NumPointRelais' => $ParcelShopId
		);
		
		$result = $this->CallWebApi("WSI3_PointRelais_Recherche", $this->AddSecurityCode($params));
		//print_r($result);

		//transformation en dto
		$parcelShopArray = ParcelShopHelper::ParcelShopResultToDTO($result["WSI3_PointRelais_RechercheResult"]["PointsRelais"]["PointRelais_Details"]);
		
		return $parcelShopArray;
	
	}
	
	public function CreateShipment($ShipmentDetails){
		if($this->_Api_Version == "2.0"){
			return $this->CreateShipmentV2($ShipmentDetails);
		}else{
			return $this->CreateShipmentV1($ShipmentDetails);
		}
	}
	
	/**
	* register a shipment in our system
	*
	* @param    string $ShipmentDetails Shipment datas
	* @param    string $ReturnStickers (optionnal) default is TRUE, will return a stickers url id true
	* @access   public
	* @return   shipmentResult
	* @todo : better result output
	*/
    public function CreateShipmentV2($ShipmentDetails, $outputFormat = "10x15", $outputType ="PdfUrl") {
		
		 $xml = new DOMDocument( "1.0", "utf-8" );
		
		$xml_ShipmentCreationRequest = $xml->createElement( "ShipmentCreationRequest");
		$xml_ShipmentCreationRequest->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
		$xml_ShipmentCreationRequest->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml_ShipmentCreationRequest->setAttribute('xmlns', 'http://www.example.org/Request');
		
		
		
		$xml_context = $xml->createElement( "Context" );
		$xml_context->appendChild($xml->createElement( "Login", $this->_Api_User ));
		$xml_context->appendChild($xml->createElement( "Password", $this->_Api_Password ));
		$xml_context->appendChild($xml->createElement( "CustomerId", $this->_Api_CustomerCode ));
		$xml_context->appendChild($xml->createElement( "Culture", "fr-FR" ));
		$xml_context->appendChild($xml->createElement( "VersionAPI", "1.0" ));
		
		$xml_ShipmentCreationRequest->appendChild($xml_context);
		
		$xml_OutputOptions = $xml->createElement( "OutputOptions" );
		$xml_OutputOptions->appendChild($xml->createElement( "OutputFormat", $outputFormat ));
		$xml_OutputOptions->appendChild($xml->createElement( "OutputType", $outputType ));
		$xml_ShipmentCreationRequest->appendChild($xml_OutputOptions);
		
		$xml_ShipmentsList = $xml->createElement( "ShipmentsList");
		$xml_Shipment = $xml->createElement( "Shipment" );
		
		$xml_Shipment->appendChild($xml->createElement( "OrderNo", $ShipmentDetails->InternalOrderReference ));
		$xml_Shipment->appendChild($xml->createElement( "CustomerNo",$ShipmentDetails->InternalCustomerReference) );
		$xml_Shipment->appendChild($xml->createElement( "ParcelCount",count($ShipmentDetails->Parcels) ));
		$xml_Shipment->appendChild($xml->createElement( "DeliveryInstruction",$ShipmentDetails->DeliveryInstruction));
		
		$xml_Shipment->appendChild($this->buildModeNode("DeliveryMode",$ShipmentDetails->DeliveryMode,$xml));
		$xml_Shipment->appendChild($this->buildModeNode("CollectionMode",$ShipmentDetails->CollectMode,$xml));
		
		if($ShipmentDetails->CostOnDelivery > 0){
			$xml_Shipment->appendChild( $this->buildOptionNode("CRT", $ShipmentDetails->CostOnDelivery, $xml));
		}
		if($ShipmentDetails->InsuranceLevel > 0){
			$xml_Shipment->appendChild( $this->buildOptionNode("ASS", $ShipmentDetails->InsuranceLevel, $xml));
		}
		
		$xml_Shipment->appendChild($this->buildOptionNode("LNG", $ShipmentDetails->Recipient->Language, $xml));
		
		$xml_Parcels= $xml_Shipment->appendChild($xml->createElement( "Parcels"));
		
		//autant que de colis
		foreach ($ShipmentDetails->Parcels as $parcel){
			$xml_Parcels->appendChild($this->buildParcelNode($parcel->Content,$parcel->WeightInGr,$parcel->LengthInCm ,$xml ));
	    }
		
		
		#sender 
		$xml_Shipment->appendChild($this->buildAdressNode("Sender", $ShipmentDetails->Sender,$xml));
		$xml_Shipment->appendChild($this->buildAdressNode("Recipient",$ShipmentDetails->Recipient,$xml));


		
		$xml_ShipmentsList->appendChild($xml_Shipment);
		$xml_ShipmentCreationRequest->appendChild($xml_ShipmentsList);
		
		$xml->appendChild($xml_ShipmentCreationRequest);
		
		$result = $this->callRestAPI($this->_APIEndPointUrlV2."/api/shipment",$xml->saveXML());
		
		$output = new RegisteredShipmentData();
		
		if(count($result->StatusList->Status) > 0){
			
			$output->Success = false;		
			foreach($result->StatusList->Status as $status){
					$message = new RegisteredParcelStatusData();
					$message->Message = (string)$status->attributes()->Message;
					$message->Code = (string)$status->attributes()->Code;
					$message->Severity = (string)$status->attributes()->Level;
					$output->Messages[]  = $message;
			}
			
		}else{
		
			$output->BrandCode = substr($this->_Api_CustomerCode,0,2);
			$output->Success = true;
			$output->Messages = "";
			

			foreach($result->ShipmentsList->Shipment[0]->LabelList->Label[0]->RawContent->LabelValues as $labelvalue){
				if($labelvalue->attributes()->Key == "MR.Expedition.NumeroExpedition"){
					$output->ShipmentNumber =  (string)  $labelvalue->attributes()->Value;
				}
			}
			$output->TrackingLink = $this->GetShipmentPermalinkTracingLink($output->ShipmentNumber);
			$output->LabelLink  =(string) $result->ShipmentsList->Shipment[0]->LabelList->Label[0]->Output;
			
			foreach( $result->ShipmentsList->Shipment[0]->LabelList->Barcodes as $cab){
				$output->Parcels[]->CAB = (string) $cab->Barcodes->Barcode->attributes()->Value;
			}
		}		
		
		return $output;
	}
	
	
	private function buildOptionNode($code, $value, $xml){
		
		$xml_Option = $xml->createElement( "Option" );
		$xml_Option->setAttribute( "Key", $code ); 
		$xml_Option->setAttribute( "Value", $value ); 	
		
		return $xml_Option;
	}
	
	private function buildParcelNode($content, $weight, $length, $xml){
		
		$xml_Parcel = $xml->createElement( "Parcel" );
		$xml_Parcel->appendChild($xml->createElement( "Content",$content ));

		$xml_Parcel_Weight = $xml->createElement( "Weight" );
		$xml_Parcel_Weight->setAttribute( "Value", $weight ); 
		$xml_Parcel_Weight->setAttribute( "Unit", "gr" ); 
		$xml_Parcel->appendChild($xml_Parcel_Weight);		
		
		return $xml_Parcel;
	}
	
	private function buildModeNode($type,$mode, $xml){
		$xml_Mode = $xml->createElement( $type );
		$xml_Mode->setAttribute( "Mode",  $mode->Mode ); 
		//si présence d'un point relais
		if($mode->ParcelShopId != null){
			$xml_Mode->setAttribute( "Location", $mode->ParcelShopContryCode . $mode->ParcelShopId);
		}
		
		return $xml_Mode;
	}
	
	private function buildAdressNode ($type, $adress, $xml){
		$xml_Parcel_Sender = $xml->createElement($type);
		
		$xml_Adress = $xml->createElement( "Address" );
		$xml_Adress->appendChild($xml->createElement( "Title","" ));
		$xml_Adress->appendChild($xml->createElement( "Firstname",$adress->Adress1 ));
		$xml_Adress->appendChild($xml->createElement( "Lastname","" ));
		$xml_Adress->appendChild($xml->createElement( "Streetname",$adress->Adress3 ));
		$xml_Adress->appendChild($xml->createElement( "HouseNo","" ));
		$xml_Adress->appendChild($xml->createElement( "CountryCode",$adress->CountryCode ));
		$xml_Adress->appendChild($xml->createElement( "PostCode",$adress->PostCode ));
		$xml_Adress->appendChild($xml->createElement( "City",$adress->City ));
		$xml_Adress->appendChild($xml->createElement( "AddressAdd1",$adress->Adress2 ));
		$xml_Adress->appendChild($xml->createElement( "AddressAdd2","" ));
		$xml_Adress->appendChild($xml->createElement( "AddressAdd3",$adress->Adress4 ));
		$xml_Adress->appendChild($xml->createElement( "PhoneNo",$adress->PhoneNumber ));
		$xml_Adress->appendChild($xml->createElement( "MobileNo",$adress->PhoneNumber2 ));
		$xml_Adress->appendChild($xml->createElement( "Email",$adress->Email ));
		
		$xml_Parcel_Sender->appendChild($xml_Adress);
		
		return $xml_Parcel_Sender;
	}
	private function callRestAPI($url,$xml){

		if($this->_Debug){
			echo '<div style="border:solid 1px #ddd;font-family:verdana;padding:5px">';
			echo '<h2>Request</h2>';
			echo '<pre>';
			print_r(htmlentities($xml));
			print_r(new SimpleXMLElement($xml));
			echo '</pre>';
			echo '<pre>';
		}

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-Type: text/xml','Accept: application/xml',
				'content' => $xml
			)
		);
		$context  = stream_context_create($opts);
		
		$result = file_get_contents($url, false, $context);
		
		if($this->_Debug){
			echo '<div style="border:solid 1px #ddd;font-family:verdana;padding:5px">';
			echo '<h2>Response</h2>';
			echo '<pre>';
	
			print_r(htmlentities($result));
			print_r(new SimpleXMLElement($result));
			echo '</pre>';
			echo '</div>';
		}
		
		return  new SimpleXMLElement($result);

	}
	
	
	
	/**
	* register a shipment in our system
	*
	* @param    string $ShipmentDetails Shipment datas
	* @param    string $ReturnStickers (optionnal) default is TRUE, will return a stickers url id true
	* @access   public
	* @return   shipmentResult
	* @todo : better result output
	*/
    public function CreateShipmentV1($ShipmentDetails) {
		//calcul du poids total
		$ReturnStickers = true;
		$WeightInGr =0;
		foreach($ShipmentDetails->Parcels as $parcel) {
			$WeightInGr += $parcel->WeightInGr;
		}

	   $params = array(
			 'Enseigne'		=> str_pad($this->_Api_CustomerCode,8),
			 'ModeCol'		=> $ShipmentDetails->CollectMode->Mode ,
			 'ModeLiv'		=> $ShipmentDetails->DeliveryMode->Mode,
			 'NDossier'		=> $ShipmentDetails->InternalOrderReference,
			 'NClient'		=> $ShipmentDetails->InternalCustomerReference,
			 'Expe_Langage'	=> $ShipmentDetails->Sender->Language,
			 'Expe_Ad1'		=> $ShipmentDetails->Sender->Adress1,
			 'Expe_Ad2'		=> $ShipmentDetails->Sender->Adress2,	
			 'Expe_Ad3'		=> $ShipmentDetails->Sender->Adress3,
			 'Expe_Ad4'		=> $ShipmentDetails->Sender->Adress4,
			 'Expe_Ville'	=> $ShipmentDetails->Sender->City,
			 'Expe_CP'		=> $ShipmentDetails->Sender->PostCode,
			 'Expe_Pays'	=> $ShipmentDetails->Sender->CountryCode,
			 'Expe_Tel1'	=> $ShipmentDetails->Sender->PhoneNumber,
			 'Expe_Tel2'	=> $ShipmentDetails->Sender->PhoneNumber2,
			 'Expe_Mail'	=> $ShipmentDetails->Sender->Email,
			 
			 'Dest_Langage'	=> $ShipmentDetails->Recipient->Language,
			 'Dest_Ad1'		=> $ShipmentDetails->Recipient->Adress1,
			 'Dest_Ad2'		=> $ShipmentDetails->Recipient->Adress2,
			 'Dest_Ad3'		=> $ShipmentDetails->Recipient->Adress3,
			 'Dest_Ad4'		=> $ShipmentDetails->Recipient->Adress4,
			 'Dest_Ville'	=> $ShipmentDetails->Recipient->City,
			 'Dest_CP'		=> $ShipmentDetails->Recipient->PostCode,
			 'Dest_Pays'	=> $ShipmentDetails->Recipient->CountryCode,
			 'Dest_Tel1'	=> $ShipmentDetails->Recipient->PhoneNumber,
			 'Dest_Tel2'	=> $ShipmentDetails->Recipient->PhoneNumber2,  	
			 'Dest_Mail'	=> $ShipmentDetails->Recipient->Email,
			 
			 
			 'Poids'		=> $WeightInGr,
			 'Longueur'		=> "",
			 'Taille'		=> "",
			 'NbColis'		=> count($ShipmentDetails->Parcels),
			 'CRT_Valeur'	=> $ShipmentDetails->CostOnDelivery,
			 'CRT_Devise'	=> $ShipmentDetails->CostOnDeliveryCurrency,
			 'Exp_Valeur'	=> $ShipmentDetails->Value,
			 'Exp_Devise'	=> $ShipmentDetails->ValueCurrency,
			 
			 'COL_Rel_Pays'	=> $ShipmentDetails->CollectMode->ParcelShopContryCode,
			 'COL_Rel'		=> $ShipmentDetails->CollectMode->ParcelShopId,
			 
			 'LIV_Rel_Pays'	=> $ShipmentDetails->DeliveryMode->ParcelShopContryCode,
			 'LIV_Rel'		=> $ShipmentDetails->DeliveryMode->ParcelShopId,
			 

			 'Assurance'	=> $ShipmentDetails->InsuranceLevel,
			 'Instructions'	=> $ShipmentDetails->DeliveryInstruction
			);
	
	$params = $this->AddSecurityCode($params);
	
	//$params['Texte'] = $ShipmentDetails->CommentOnLabel;

	$result = $this->CallWebApi("WSI2_CreationEtiquette", $params);
	
	$output = new RegisteredShipmentData();
	
	if($result["WSI2_CreationEtiquetteResult"]["STAT"] != "0"){
			
			$output->Success = false;		
			
			$message = new RegisteredParcelStatusData();
			$message->Message = ApiHelper::GetStatusCode($result);
			$message->Code = $result["WSI2_CreationEtiquetteResult"]["STAT"];
			$message->Severity = "Error";
			$output->Messages[]  = $message;
			
	}else{
	
		$output->BrandCode = substr($this->_Api_CustomerCode,0,2);
		$output->Success = true;
		$output->Messages = "";	
		$output->ShipmentNumber = $result['WSI2_CreationEtiquetteResult']['ExpeditionNum'];
		$output->TrackingLink = $this->GetShipmentPermalinkTracingLink($output->ShipmentNumber);
		$output->LabelLink  =$this->BuildStickersLink($result);
		
	
	}	
	
	return $output;
    }
	
	/**
	* get a parcel status
	*
	* @param    int $ShipmentNumber Shipment number(8 digits)
	* @access   public
	* @return   shipmentStatus
	*/
	public function GetShipmentStatus($ShipmentNumber){
		die ("Not implemented yet !");
	}
	

	/**
	* get a secure link to the professional parcel informations mondial relay extranet 
	*
	* @param    int $ShipmentNumber Shipment number(8 digits)
	* @param    string $UserLogin Login to connect to the system
	* @access   public
	* @return   string
	*/
	public function GetShipmentConnectTracingLink($ShipmentNumber,$UserLogin){
		$Tracing_url = "/".trim(strtoupper($this->_Api_CustomerCode))."/Expedition/Afficher?numeroExpedition=".$ShipmentNumber;
		return $this->_MRConnectUrl.$this->AddConnectSecurityParameters($Tracing_url,$UserLogin);
	}
	
	/**
	* get a secure link to the professional parcel informations mondial relay extranet 
	*
	* @param    int $ShipmentNumber Shipment number(8 digits)
	* @param    string $UserLogin Login to connect to the system
	* @access   public
	* @return   string
	*/
	public function GetShipmentPermalinkTracingLink($ShipmentNumber,$language="fr",$country="fr"){
	
		$Tracing_url = "http://www.mondialrelay.fr/public/permanent/tracking.aspx?ens=".$this->_Api_CustomerCode.$this->_Api_BrandId."&exp=".$ShipmentNumber."&pays=".$country."&language=".$language;
		$Tracing_url .= $this->AddPermalinkSecurityParameters($ShipmentNumber);
		if($this->_Debug){
			echo "<br/>Permalink pour expé <b>".$this->_APILogin."/".$ShipmentNumber."</b> langue <b>".$language."</b>, pays <b>".$country."</b> : ".$Tracing_url ."<hr/>";
		}
		
		return $Tracing_url;
	}
	
	/**
	* add the security signature to the extranet url request
	*
	* @param    string $UrlToSecure Url 
	* @param    string $UserLogin Login to connect to the system
	* @access   private
	* @return   string
	*/
	private function AddConnectSecurityParameters($UrlToSecure, $UserLogin){
		$UrlToSecure = $UrlToSecure."&login=".$UserLogin."&ts=".time();
		$UrlToEncode = $this->_APIPassword."_".$UrlToSecure ;
		
		return $UrlToSecure."&crc=".strtoupper(md5($UrlToEncode));
	}
	
	/**
	* add the security signature to the permalink url request
	*
	* @param    string $UrlToSecure Url 
	* @param    string $UserLogin Login to connect to the system
	* @access   private
	* @return   string
	*/
	private function AddPermalinkSecurityParameters($Chaine){
		$UrlToSecure = "<".$this->_Api_CustomerCode.$this->_APINumericBrandId.">".$Chaine."<".$this->_Api_SecretKey.">";
		if($this->_Debug){
			echo "<br/>Chaine à encode : ".htmlentities($UrlToSecure) . " - MD5 Calculé : ".strtoupper(md5($UrlToSecure));
		}
		return "&crc=".strtoupper(md5($UrlToSecure));
	}
	
	/**
	* add the security signature to the soap request
	*
	* @param    string $ParameterArray Soap Parameters Request to secure 
	* @param    boolean $ReturnArray Optionnal, False if you just want to output the security string
	* @access   private
	* @return   string
	*/
	private function AddSecurityCode($ParameterArray, $ReturnArray = true){
		
		$secString = "";
		foreach($ParameterArray as $prm){
				$secString .= $prm;
		}	
		
		
		
		if($ReturnArray){
			$ParameterArray['Security'] = strtoupper(md5(utf8_decode($secString.$this->_Api_SecretKey)));
			return $ParameterArray;
		}else{
			return strtoupper(md5($secString.$this->_Api_SecretKey));
		}	
	}
	
	/**
	* perform a call to the mondial relay API
	*
	* @param    string $methodName Soap Method to call
	* @param    $ParameterArray Soap parameters array
	* @access   private
	*/
	private function CallWebApi($methodName,$ParameterArray){
		$result = $this->_SoapClient->call($methodName, $ParameterArray, $this->_APIEndPointUrlV1 , $this->_APIEndPointUrlV1 . $methodName);
		
		
		// Display the request and response
		if($this->_Debug){
			echo '<div style="border:solid 1px #ddd;font-family:verdana;padding:5px">';
			echo '<h1>Method '.$methodName.'</h1>';
			echo '<div>'. ApiHelper::GetStatusCode($result) .'</div>';
			echo '<h2>Request</h2>';
			echo '<pre>';
			print_r($ParameterArray);
			echo '</pre>';
			echo '<pre>' . htmlspecialchars($this->_SoapClient->request, ENT_QUOTES) . '</pre>';
			echo '<h2>Response</h2>';
			echo '<pre>';
			print_r($result);
			echo '</pre>';
			echo '<pre>' . htmlspecialchars($this->_SoapClient->response, ENT_QUOTES) . '</pre>';
			
			
			
			echo '</div>';

		}
		
		
		
		return $result;
	}
	
	/**
	* Build a link to download the stickers 
	* from a web service call result
	*
	* @param    service result $StickersResult 
	* @access   public
	*/
	public function BuildStickersLink($StickersResult){
		
		return $this->_MRStickersUrl . $StickersResult['WSI2_CreationEtiquetteResult']['URL_Etiquette'];
	}
	
	
}
?>