<?php

abstract class Includes_HeardAboutUsMethods {
	
	public static function getMethods() {
		return [
			'N/A',
			'Google/Internet Search',
			'Facebook Page',
			'Kijiji Ad',
			'Returning Player',
			'From A Friend',
			'Restaurant Advertisement',
			'The Guelph Community Guide',
			'Other'
		];
	}
	
	public static function getMethodByOrdinal($ordinal) {
		
		if($ordinal < 0 || $ordinal >= count(Includes_HeardAboutUsMethods::getMethods())) {
			return "";
		}
		
		return Includes_HeardAboutUsMethods::getMethods()[$ordinal];
	}
}

