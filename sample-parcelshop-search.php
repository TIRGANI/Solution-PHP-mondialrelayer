<?php
	require_once('includes/MondialRelay.API.Class.php');
	
	
	//We declare the client
	$MRService = new MondialRelayWebAPI();
	
	//set the credentials
	$MRService->_Api_CustomerCode 	= "BDTEST  ";
	$MRService->_Api_BrandId 		= "11";
	$MRService->_Api_SecretKey  	= "MondiaL_RelaY_44";
	$MRService->_Api_User 	    	= "BDTEST@business-api.mondialrelay.com";
	$MRService->_Api_Password 		= "]dx1SP9aSrMs)faK]jXa";
	$MRService->_Api_Version 		= "2.0";
	
	$MRService->_Debug = false;

	/*
	//-------------------------------------------------
	//Parcel Shop Search Sample
	//-------------------------------------------------
	*/
	//Basic Search for parcel Shops arround the post code 59000  
	$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","59000");
	

	echo "<pre>";
	print_r($myParcelShopSearchResults);
	echo "<pre>";

	//The Same Search with a delivery Mode restriction (24R)
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","78510","","","","","",1);
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","63300","24L");
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("BE","7000",	"24R",0);
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","59200","24X");
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","59200","24L");
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","59200","DRI");
	//$myParcelShopSearchResults= $MRService->SearchParcelShop("FR","59200","24X", 70000);
	
	//-------------------------------------------------
	//Get a ParcelShop Details Sample
	//-------------------------------------------------
	$myParcelShopDetails = $MRService->GetParcelShopDetails($myParcelShopSearchResults[0]->CountryCode,$myParcelShopSearchResults[0]->ParcelShopId);
	echo "<pre>";
	print_r($myParcelShopDetails);
	echo "<pre>";

	

?>