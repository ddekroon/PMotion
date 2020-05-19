<?php

abstract class Includes_PaymentMethods {
	
	public static function getMethods() {
		return [
			'N/A',
			'I will send an email money transfer to dave@perpetualmotion.org',
			'I will mail cheque to Perpetual Motion\'s home office',
			'I will bring cash/cheque to Perpetual Motion\'s home office',
			'I will bring cash/cheque to registration night'
		];
	}
	
	public static function getMethodByOrdinal($ordinal) {
		
		if($ordinal < 0 || $ordinal >= count(Includes_PaymentMethods::getMethods())) {
			return "";
		}
		
		return Includes_PaymentMethods::getMethods()[$ordinal];
	}
}

