<?php 

function checkErrors($leagueID, $playerArray, $payMethod) {
	$error = '';
	
	$playerPhone=preg_replace("/\D/",'',$playerArray[0]->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	
	if($leagueID == 0) {
		$error.='Please enter a league for preffered league 1<br />';
	}
	if(strlen($playerArray[0]->playerFirstName) < 3 || strlen($playerArray[0]->playerFirstName) > 20) {
		 $error.='Please enter your first name between 3 and 20 characters<br />';
	}
	if(strlen($playerArray[0]->playerLastName) < 3 || strlen($playerArray[0]->playerLastName) > 30) {
		 $error.='Please enter your last name between 3 and 30 characters<br />';
	}
	if(!filter_var($playerArray[0]->playerEmail, FILTER_VALIDATE_EMAIL) || strlen($playerArray[0]->playerEmail) < 2) {
		 $error.='Please enter your valid email<br />';
	}
	if(!(strlen($playerPhone) == 10 || strlen($playerPhone) == 11 && $playerPhone[0] == 1)) {
		 $error.='Please enter your valid phone number<br />';
	}
	if(strlen($playerArray[1]->playerFirstName) > 2 && !filter_var($playerArray[1]->playerEmail, FILTER_VALIDATE_EMAIL)) {
		 $error.='Please enter a valid second player/contact email<br />';
	}
	if($payMethod == 0) {
	    $error.='Please enter a payment method<br />';
	}
	print $error;
	if(strlen($error) > 0) {
		return 0;
	} else {
		return 1;
	}
}