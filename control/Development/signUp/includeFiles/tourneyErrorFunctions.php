<?php 
function checkTeamErrors($tourneyObj, $teamObj, $playerObj) {
	$errorMessage = '';

	if($teamObj->teamLeagueID == 10000) { 
		$errorMessage .= "<br />Please select a league from the dropdown list."; 
	}
	$errorMessage .= checkPlayer($playerObj, 0);
	
	if(!(strlen($teamObj->teamName) > 2 && strlen($teamObj->teamName) < 41)) { 
		$errorMessage .= "<br />Please enter a team name between 3 and 40 characters in length."; 
	}
	
	if(!(strlen($teamObj->teamComments) > 2 && $teamObj->teamComments < 1001) && $teamObj->teamComments != 0) { 
		$errorMessage .= "<br />Please enter a comment between 3 and 1000 characters."; 
	}
	if($teamObj->teamPayMethod == 0) {
		$errorMessage .= "<br />Please enter a payment method."; 
	}
	
	if(strlen($errorMessage) > 2) {
		print $errorMessage;
		return 0;
	} else {
		return 1;
	}
} 

function checkCardErrors($tourneyObj, $teamObj, $playerObj) { 
	$errorMessage = '';

	if($teamObj->teamLeagueID == 10000) { 
		$errorMessage .= "<br />Please select a league from the dropdown list."; 
	} else {
		if($playerObj->playerCard == 0) {
			$errorMessage  .= '<br />Please select a card from the dropdown list.';
		} else if($playerObj->playerCard < 200) {
			if($playerObj->playerCard - 100 > $tourneyObj->tourneyNumBlackCards[$teamObj->teamLeagueID]) {
				$errorMessage .= '<br />Invalid card choice for this league, please enable javascript and choose another card';
			}
		} else if($playerObj->playerCard < 300) {
			if($playerObj->playerCard - 200 > $tourneyObj->tourneyNumBlackCards[$teamObj->teamLeagueID]) {
				$errorMessage .= '<br />Invalid card choice for this league, please enable javascript and choose another card';
			}
		} else if($playerObj->playerCard < 400) {
			if($playerObj->playerCard - 300 > $tourneyObj->tourneyNumRedCards[$teamObj->teamLeagueID]) {
				$errorMessage .= '<br />Invalid card choice for this league, please enable javascript and choose another card';
			}
		} else if($playerObj->playerCard < 500) {
			if($playerObj->playerCard - 400 > $tourneyObj->tourneyNumRedCards[$teamObj->teamLeagueID]) {
				$errorMessage .= '<br />Invalid card choice for this league, please enable javascript and choose another card';
			}
		}
	}
	$errorMessage .= checkPlayer($playerObj, 1);
	if(!(strlen($teamObj->teamComments) > 2 && $teamObj->teamComments < 1001) && $teamObj->teamComments != 0) { 
		$errorMessage .= "<br />Please enter a comment between 3 and 1000 characters."; 
	}
	if($teamObj->teamPayMethod == 0) {
		$errorMessage .= "<br />Please enter a payment method."; 
	}


	if(strlen($errorMessage) > 2) {
		print $errorMessage;
		return 0;
	} else {
		return 1;
	}
}

function checkPlayerErrors($tourneyObj, $teamObj, $playerObj) { 
	$errorMessage = '';

	if($teamObj->teamLeagueID == 10000) { 
		$errorMessage .= "<br />Please select a league from the dropdown list."; 
	}
	$errorMessage .= checkPlayer($playerObj, 1);
	if(!(strlen($teamObj->teamComments) > 2 && $teamObj->teamComments < 1001) && $teamObj->teamComments != 0) { 
		$errorMessage .= "<br />Please enter a comment between 3 and 1000 characters."; 
	}
	if($teamObj->teamPayMethod == 0) {
		$errorMessage .= "<br />Please enter a payment method."; 
	}

	if(strlen($errorMessage) > 2) {
		print $errorMessage;
		return 0;
	} else {
		return 1;
	}
}

function checkPlayer($playerObj, $playerNum) {
	$errorMessage = '';
	if($playerNum == 0) {
		$playerMessage = 'the captain\'s';
	} else if ($playerNum = 1) {
		$playerMessage = 'your';
	}
	
	if(!(strlen($playerObj->playerFirstName) > 2 && strlen($playerObj->playerFirstName) < 21)) { 
		$errorMessage .= "<br />Please enter $playerMessage first name between 3 and 20 characters in length."; 
	}
	if(!(strlen($playerObj->playerLastName) > 2 && strlen($playerObj->playerLastName) < 31)) { 
		$errorMessage .= "<br />Please enter $playerMessage last name between 3 and 30 characters in length."; 
	}
	if($playerObj->playerGender == '') { 
		$errorMessage .= "<br />Please enter $playerMessage gender."; 
	}
	if(!filter_var($playerObj->playerEmail, FILTER_VALIDATE_EMAIL)) { 
		$errorMessage .= "<br />Please enter $playerMessage valid email."; 
	}
	return $errorMessage;
}?>