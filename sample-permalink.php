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
	$MRService->_Api_Version 		= "2.0";
	
	$MRService->_Debug = false;
	

	
	/*
	//-------------------------------------------------
	//get a tracking link 
	//-------------------------------------------------
	*/

	$link = $MRService->GetShipmentPermalinkTracingLink("50000763");

	echo "<a href='".$link."'>".$link."</a>";	
?>