<?php
	require_once('includes/MondialRelay.API.Class.php');
	
	
	//We declare the client
	$MRService = new MondialRelayWebAPI();
	
	
	//set the credentials
	$MRService->_Api_CustomerCode 	= "BDTEST  ";
	$MRService->_Api_BrandId 		= "11";
	$MRService->_Api_SecretKey  	= "*****";
	$MRService->_Api_User 	    	= "BDTEST@business-api.mondialrelay.com";
	$MRService->_Api_Password 		= "****";
	$MRService->_Api_Version 		= "1.0";
	
	
	$MRService->_Debug = false;
	
	//set the merchant adress
	//sender adress
	$merchantAdress = new Adress();
	$merchantAdress->Adress1 = "My book shop";
	$merchantAdress->Adress2 = "";
	$merchantAdress->Adress3 = "10 rue des écoles";
	$merchantAdress->Adress4 = "";
	$merchantAdress->PostCode = "59000";
	$merchantAdress->City = "Lille";
	$merchantAdress->CountryCode = "FR";
	$merchantAdress->PhoneNumber = "+33300000000" ;
	$merchantAdress->PhoneNumber2 ="";
	$merchantAdress->Email = "hello@mybookshop.com";
	$merchantAdress->Language = "FR";
	
	
	//-------------------------------------------------
	//Shipment Creation Sample
	//-------------------------------------------------
	//Create a new shipment object
	$myShipment = new ShipmentData();

	//set the delivery options
	$myShipment->DeliveryMode = new ShipmentInfo()  ;
	$myShipment->DeliveryMode->Mode = "LDP";
	//parcel Shop ID when required
	$myShipment->DeliveryMode->ParcelShopId = "066974";
	$myShipment->DeliveryMode->ParcelShopContryCode = "FR";
	
	//set the pickup options
	$myShipment->CollectMode = new ShipmentInfo() ;
	$myShipment->CollectMode->Mode = "CCC";
	//parcel Shop ID when required
	//$myShipment->CollectMode->ParcelShopId = "066974";
	//$myShipment->CollectMode->ParcelShopContryCode = "FR";
	
	$myShipment->InternalOrderReference = "592268872383";
	$myShipment->InternalCustomerReference ="LBG";
	
	//sender adress with the previsously declarated adress
	$myShipment->Sender = $merchantAdress;
	
	//recipient adress
	$myShipment->Recipient = new Adress()  ;
		$myShipment->Recipient->Adress1 = "Robin Mince";
		$myShipment->Recipient->Adress2 = "Résidence des champs";
		$myShipment->Recipient->Adress3 = "18 rue basse";
		$myShipment->Recipient->Adress4 = "";
		$myShipment->Recipient->PostCode = "75001";
		$myShipment->Recipient->City = "Paris";
		$myShipment->Recipient->CountryCode = "FR";
		$myShipment->Recipient->PhoneNumber = "+33300000000" ;
		$myShipment->Recipient->PhoneNumber2 = "+33600000000";
		$myShipment->Recipient->Email = "client@yopmail.com";
		$myShipment->Recipient->Language = "FR";
	
	//shipment datas
	$myShipment->DeliveryInstruction= "" ;
	$myShipment->CommentOnLabel= "" ;
	
	//parcel declaration (one item per parcel)
	$myShipment->Parcels[0] = new Parcel();
	$myShipment->Parcels[0]->WeightInGr = 1000;
	$myShipment->Parcels[0]->Content = "books ";
	
	$myShipment->Parcels[1] = new Parcel();
	$myShipment->Parcels[1]->WeightInGr = 2000;
	$myShipment->Parcels[1]->Content = "pencils and paints ";
	
	$myShipment->InsuranceLevel="";
	
	$myShipment->CostOnDelivery= 0 ;
	$myShipment->CostOnDeliveryCurrency= "EUR" ;
	$myShipment->Value= 0 ;
	$myShipment->ValueCurrency= "EUR";
	
	//Create the shipment
	//this will return the stickers URL and Shipment number to track the parcel
	
	//creation with Internationnal API
	$ShipmentDatas = $MRService->CreateShipment($myShipment);
	
	print_r($ShipmentDatas);
	echo '<a href="'.$ShipmentDatas->LabelLink.'" >Download Stickers</a>';
	

	

?>