<?php 

class ParcelShopHelper {
	
	public static function ParcelShopResultToDTO($result){
		
		
		$output = new ParcelShop();
		
		$output->ParcelShopId = $result["Num"];
		$output->Name = $result["LgAdr1"];
		$output->Adress1= $result["LgAdr3"];
		$output->Adress2= $result["LgAdr4"];
		$output->PostCode= $result["CP"];
		$output->City= $result["Ville"];
		$output->CountryCode= $result["Pays"];
		
		$output->Latitude= $result["Latitude"];
		$output->Longitude= $result["Longitude"];
		
		$output->LocalisationDetails= $result["Localisation1"] . " " . $result["Localisation2"];
		
		$output->ClosedPeriods;
		
		
		$output->OpeningHours["Monday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Lundi"]);
		$output->OpeningHours["Tuesday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Mardi"]);
		$output->OpeningHours["Wednesday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Mercredi"]);
		$output->OpeningHours["Thirsday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Jeudi"]);
		$output->OpeningHours["Friday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Vendredi"]);
		$output->OpeningHours["Saturday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Samedi"]);
		$output->OpeningHours["Sunday"] = ParcelShopHelper::TimeSlotResultToDTO($result["Horaires_Dimanche"]);
		
		
		$output->ActivityCode= $result["TypeActivite"];
		
		$output->PictureUrl= $result["URL_Photo"];
		$output->MapUrl= $result["URL_Plan"];
		
		return $output;
	}
	
	public static function TimeSlotResultToDTO($TimeSlot){

		
		$output = new TimeSlot();
	    $output->OpenAMAt = $TimeSlot["string"][0];
		$output->CloseAMAt = $TimeSlot["string"][1];
	    $output->OpenPMAt = $TimeSlot["string"][2];
		$output->ClosePMAt= $TimeSlot["string"][3];		
		$output->Closed;
		
		return $output;
	}

}
?>