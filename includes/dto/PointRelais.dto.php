<?php 

class ParcelShop {

	public $ParcelShopId;
	public $Name;
	public $Adress1;
	public $Adress2;
	public $PostCode;
	public $City;
	public $CountryCode;
	
	public $Latitude;
	public $Longitude;
	
	public $LocalisationDetails;
	public $OpeningHours;
	public $ClosedPeriods;
	
	public $ActivityCode;
	
	public $PictureUrl;
	public $MapUrl;
	

}

class TimeSlot {
	public $OpenAMAt;
	public $CloseAMAt;
	public $OpenPMAt;
	public $ClosePMAt;
	public $Closed;
}

class Period {
	public $From;
	public $To;
}

class Adress {
	public $Adress1;
	public $Adress2;
	public $Adress3;
	public $Adress4;
	public $PostCode;
	public $City;
	public $CountryCode;
	public $PhoneNumber;
	public $PhoneNumber2;
	public $Email;
	public $Language;
}

class ShipmentInfo {
	public $Mode;
	public $ParcelShopId;
	public $ParcelShopContryCode;
}

class Parcel {
	public $Content;
	public $WeightInGr;
	public $LengthInCm;
}

class ShipmentData {
	public  $DeliveryMode;
	public  $CollectMode;
	
	public  $Sender;
	public  $Recipient;
	
	public 	$InternalCustomerReference;
	public 	$InternalOrderReference;
	public  $DeliveryInstruction;
	public  $CommentOnLabel;
	
	public  $Parcels;
	
	public  $CostOnDelivery;
	public  $CostOnDeliveryCurrency;
	public  $Value;
	public  $ValueCurrency;
	
	public  $InsuranceLevel;	
}

class RegisteredShipmentData {
	public  $BrandCode;
	public  $Success;
	public  $Messages;
	
	public  $ShipmentNumber;
	
	public  $TrackingLink;
	public  $LabelLink;
	
	
}



class RegisteredParcelStatusData {
	public  $Message;
	public  $Code;
	public  $Severity;
}
?>