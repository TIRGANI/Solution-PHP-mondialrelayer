<?php 

class ApiHelper {
	
	public static function GetStatusCode($result){
		$_status[0] = "Successfull operation";
		$_status[1] = "Incorrect merchant";
		$_status[2] = "Merchant number empty";
		$_status[3] = "Incorrect merchant account number";
		$_status[4] = "";
		$_status[5] = "Incorrect Merchant shipment reference";
		$_status[6] = "";
		$_status[7] = "Incorrect Consignee reference";
		$_status[8] = "Incorrect password or hash";
		$_status[9] = "Unknown or not unique city";
		$_status[10] = "Incorrect type of collection";
		$_status[11] = "Point Relais® collection number incorrect";
		$_status[12] = "Point Relais® collection country.incorrect";
		$_status[13] = "Incorrect type of delivery";
		$_status[14] = "Incorrect delivery Point Relais® number";
		$_status[15] = "Point Relais delivery country.incorrect";
		$_status[16] = "";
		$_status[17] = "";
		$_status[18] = "";
		$_status[19] = "";
		$_status[20] = "Incorrect parcel weight";
		$_status[21] = "Incorrect developped lenght (length + height)";
		$_status[22] = "Incorrect parcel size";
		$_status[23] = "";
		$_status[24] = "Incorrect shipment number";
		$_status[25] = "Not enougth money on your acount to register this shipment";
		$_status[26] = "Incorrect assembly time";
		$_status[27] = "Incorrect mode of collection or delivery";
		$_status[28] = "Incorrect mode of collection";
		$_status[29] = "Incorrect mode of delivery";
		$_status[30] = "Incorrect address (L1)";
		$_status[31] = "Incorrect address (L2)";
		$_status[32] = "";
		$_status[33] = "Incorrect address (L3)";
		$_status[34] = "Incorrect address (L4)";
		$_status[35] = "Incorrect city";
		$_status[36] = "Incorrect zipcode";
		$_status[37] = "Incorrect country";
		$_status[38] = "Incorrect phone number";
		$_status[39] = "Incorrect e-mail";
		$_status[40] = "Missing parameters";
		$_status[41] = "";
		$_status[42] = "Incorrect COD value";
		$_status[43] = "Incorrect COD currency";
		$_status[44] = "Incorrect shipment value";
		$_status[45] = "Incorrect shipment value currency";
		$_status[46] = "End of shipments number range reached";
		$_status[47] = "Incorrect number of parcels";
		$_status[48] = "Multi-Parcel not permitted at Point Relais®";
		$_status[49] = "Incorrect action";
		$_status[50] = "";
		$_status[51] = "";
		$_status[52] = "";
		$_status[53] = "";
		$_status[54] = "";
		$_status[55] = "";
		$_status[56] = "";
		$_status[57] = "";
		$_status[58] = "";
		$_status[59] = "";
		$_status[60] = "Incorrect text field (this error code has no impact)";
		$_status[61] = "Incorrect notification request";
		$_status[62] = "Incorrect extra delivery information";
		$_status[63] = "Incorrect insurance";
		$_status[64] = "Incorrect assembly time";
		$_status[65] = "Incorrect appointement";
		$_status[66] = "Incorrect take back";
		$_status[67] = "Incorrect latitude";
		$_status[68] = "Incorrect longitude";
		$_status[69] = "Incorrect merchant code";
		$_status[70] = "Incorrect Point Relais® number";
		$_status[71] = "Incorrect Nature de point de vente non valide";
		$_status[72] = "";
		$_status[73] = "";
		$_status[74] = "Incorrect language";
		$_status[75] = "";
		$_status[76] = "";
		$_status[77] = "";
		$_status[78] = "Incorrect country of collection";
		$_status[79] = "Incorrect country of delivery";
		$_status[80] = "Tracking code : Recorded parcel";
		$_status[81] = "Tracking code : Parcel in process at Mondial Relay";
		$_status[82] = "Tracking code : Delivered parcel";
		$_status[83] = "Tracking code : Anomaly";
		$_status[84] = "(Reserved tracking code)";
		$_status[85] = "(Reserved tracking code)";
		$_status[86] = "(Reserved tracking code)";
		$_status[87] = "(Reserved tracking code)";
		$_status[88] = "(Reserved tracking code)";
		$_status[89] = "(Reserved tracking code)";
		$_status[90] = "";
		$_status[91] = "";
		$_status[92] = "";
		$_status[93] = "No information given by the sorting plan. If you want to do a collection or delivery at Point Relais, please check it is avalaible.".
						"If you want to do a home delivery, please check if the zipcode exists.";
		$_status[94] = "Unknown parcel";
		$_status[95] = "Merchant account not activated";
		$_status[96] = "";
		$_status[97] = "Incorrect security key";
		$_status[98] = "Generic error (Incorrect parameters)";
		
		$_status[99] = "Generic error of service system";
		
		$result = current($result);
		reset($result);
		$statusDescription = "The service returned a " . $result['STAT'] . " Code : " .$_status[$result['STAT']] . ".";
		
		return $statusDescription;
	}


}
?>