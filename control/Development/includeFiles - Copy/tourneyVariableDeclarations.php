<?php //Magic quotes by default is on, this function takes out all backslashes in the superglobal variables
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
			stripslashes($value);
        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

if(($tourneyID = $_GET['tournamentID']) == ''){
    $tourneyID = 0;
}

function declareTournamentVariables($tourneyID) {
	global $tournamentsTable;
	
	if($tourneyID == 0) {
		return;
	}
	$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable WHERE tournament_id = $tourneyID") or die('ERROR getting tourney info - '.mysql_error());
	$tourneyArray = mysql_fetch_array($tourneyQuery);
	$tourneyObj = new Tournament();
	$tourneyObj->tourneyID = $tourneyArray['tournament_id'];
	$tourneyObj->tourneyName = $tourneyArray['tournament_name'];
	$tourneyObj->tourneyRegistrationFee = explode('%', $tourneyArray['tournament_registration_fee']);
	$tourneyObj->tourneyIsCards = $tourneyArray['tournament_is_cards'];
	$tourneyObj->tourneyIsLeagues = $tourneyArray['tournament_is_leagues'];
	if($tourneyObj->tourneyIsLeagues == 1) {
		$tourneyObj->tourneyNumLeagues = $tourneyArray['tournament_num_leagues'];
		$tourneyObj->tourneyLeagueNames = explode('%', $tourneyArray['tournament_league_names']);
	} else {
		$tourneyObj->tourneyNumLeagues = 1;
		$tourneyObj->tourneyLeagueNames[0] = $tourneyObj->tourneyName;
	}
	$tourneyObj->tourneyIsTeams = $tourneyArray['tournament_is_teams'];
	$tourneyObj->tourneyNumTeams = explode('%', $tourneyArray['tournament_num_teams']);
	$tourneyObj->tourneyIsPlayers = $tourneyArray['tournament_is_players'];
	$tourneyObj->tourneyNumPlayers = explode('%', $tourneyArray['tournament_num_players']);
	$tourneyObj->tourneyIsExtraField = $tourneyArray['tournament_is_extra_field'];
	$tourneyObj->tourneyExtraFieldName = $tourneyArray['tournament_extra_field_name'];
	$tourneyObj->tourneyNumRedCards = explode('%', $tourneyArray['tournament_num_red_cards']);
	$tourneyObj->tourneyNumBlackCards = explode('%', $tourneyArray['tournament_num_black_cards']);
	$tourneyObj->tourneyDateClosed = $tourneyArray['tournament_registration_closed'];
	$tourneyObj->tourneyDatePlayed = $tourneyArray['tournament_game_day'];
	$tourneyObj->tourneyNumDays = $tourneyArray['tournament_num_days'];
	$tourneyObj->tourneyNumRunning = $tourneyArray['tournament_num_running'];
	$tourneyObj->logoLink = $tourneyArray['tournament_logo_link'];
	$tourneyObj->tourneyRegIsPlayers = $tourneyArray['tournament_registration_is_extra_players'];
	$tourneyObj->tourneyRegNumPlayers = $tourneyArray['tournament_registration_num_extra_players'];
	if($tourneyObj->tourneyIsPlayers == 1|| $tourneyObj->tourneyIsCards == 1) {
		$tourneyObj->tourneyIsFull = explode('%', $tourneyArray['tournament_is_full']);
		$i = 0;
		foreach($tourneyObj->tourneyIsFull as $genderIsFull) {
			$genderTokens = explode('-', $genderIsFull);
			$tourneyObj->tourneyIsFullMale[$i] = $genderTokens[0];
			$tourneyObj->tourneyIsFullFemale[$i++] = $genderTokens[1];
		}
	} else {
		$tourneyObj->tourneyIsFull = explode('%', $tourneyArray['tournament_is_full']);
	}
	return $tourneyObj;
}

function formatTitle($oldTitle) {
	$newTitle = '';
	$dontCapitalizeArray = array('of', 'a', 'an', 'the', 'but', 'or', 'not', 'yet', 'at', 'on', 'in', 'over', 'above', 'under', 'below', 'behind', 'next', 'to', 'beside', 'by', 'amung',
								 'between', 'by', 'till', 'since', 'during', 'for', 'throughout', 'to', 'and',);
	
	$teamNameArray = explode(' ', $oldTitle);
	foreach($teamNameArray as $teamName) {
		if(strtolower($teamName) == 'fc') {
			$newTitle .= 'FC ';
		} else if(!in_array(strtolower($teamName), $dontCapitalizeArray)) {
			$newTitle .= ucfirst(strtolower($teamName)).' ';
		} else {
			$newTitle.= strtolower($teamName).' ';
		}
	}
	
	// Capitalize First Letter
	
	$newTitle{0} = strtoupper($newTitle{0});
	
	return substr($newTitle, 0, -1);
}

function getSubmittedValues() {
	global $player, $extraPlayersObj;
	$team = new Team();
	$team->teamLeagueID = $_POST['leagueID'];

	$team->teamComments = $_POST['teamComments'];
	
	$team->teamName = formatTitle($_POST['teamName']);
	$team->teamIsRegistered = $_POST['isRegistered'];
	$team->teamPayMethod = $_POST['payMethod'];
	$team->teamRating = $_POST['teamRating'];
	
	//---Captain's Information (First/Last Name, Email, Phone Number, Sex)---\\
	$player = new Player();
	
	$firstNameArray = explode(' ', $_POST['capFirst']);
	foreach($firstNameArray as $firstName) {
		$player->playerFirstName .= ucfirst(strtolower($firstName)).' ';
	}
	$player->playerFirstName = substr($player->playerFirstName, 0, -1);
	
	$lastNameArray = explode(' ', $_POST['capLast']);
	foreach($lastNameArray as $lastName) {
		$player->playerLastName .= ucfirst(strtolower($lastName)).' ';
	}
	$player->playerLastName = substr($player->playerLastName, 0, -1);
	
	$player->playerEmail = $_POST['capEmail'];
	if(($player->playerPhone = $_POST['capPhone']) == '') {
		$player->playerPhone = 0;
	}
	$player->playerGender = $_POST['capSex'];
	$player->playerAddress = $_POST['capAddress'];
	$player->playerCity = $_POST['capCity'];
	$player->playerProvince = $_POST['capProvince'];
	$player->playerPostalCode = $_POST['capPostalCode'];
	$player->playerExtraData = $_POST['extraData'];
	if(($player->playerCard = $_POST['cardsDropDown']) == '') {
		$player->playerCard = 0;
	}
	if(($player->playerRating = $_POST['capRating']) == '') {
		if(($player->playerRating = $_POST['teamRating']) == '') {
			$player->playerRating = 0;
		}
	}
	$player->playerHearMethod = $_POST['aboutUsMethod'];
	$player->playerHearOtherText = $_POST['aboutUsTextBox']; 
	
	for($i = 0; $i<count($_POST['playerFirstName']); $i++) {
		if(strlen($_POST['playerFirstName'][$i]) >= 1) {
			 $extraPlayersObj[$i] = new Player();
			 $extraPlayersObj[$i]->playerFirstName = $_POST['playerFirstName'][$i];
			 $extraPlayersObj[$i]->playerLastName = $_POST['playerLastName'][$i];
			 $extraPlayersObj[$i]->playerRating = $_POST['playerRating'][$i];
		}
	}

	return $team;
}

function getDivisionDD($tourneyObj, $teamObj) {
	$divisionDropDown = '<option selected value=10000>--Division--</option>';
	//divisions in dropdown
	for($i=0;$i<$tourneyObj->tourneyNumLeagues; $i++) {
		$i == $teamObj->teamLeagueID? $selectFilter = 'selected': $selectFilter = '';
		$divisionDropDown.="<option $selectFilter value=$i>".$tourneyObj->tourneyLeagueNames[$i].' - ($'.$tourneyObj->tourneyRegistrationFee[$i].' CAD)';
		if($tourneyObj->tourneyIsPlayers == 1|| $tourneyObj->tourneyIsCards == 1) {
			$tourneyObj->tourneyIsFull = explode('%', $tourneyArray['tournament_is_full']);
			$divisionDropDown.= $tourneyObj->tourneyIsFullMale[$i] == 1?' (Male Full)':'';
			$divisionDropDown.= $tourneyObj->tourneyIsFullFemale[$i] == 1?' (Female Full)':'';
			if($tourneyObj->tourneyIsFullMale[$i] == 1 || $tourneyObj->tourneyIsFullFemale[$i] == 1) {
				$divisionDropDown.= ' - waiting list';
			}
		} else {
			$divisionDropDown.= $tourneyObj->tourneyIsFull[$i] == 1?'Full - waiting list':'';
		}
		$divisionDropDown.=  '</option>';
	}
	return $divisionDropDown;
}

function getPayInfoDD($payMethod) {
	$payMethodString[1] = 'I will send an email money transfer to dave@perpetualmotion.org';
	$payMethodString[2] = 'I will mail cheque to Perpetual Motion\'s home office';
	$payMethodString[3] = 'I will bring cash/cheque to Perpetual Motion\'s home office';
	$payInfoDropDown = '';
	
	$payInfoDropDown.= "<option value=0>Choose Payment Method</option>";
		for($x=1; $x<4;$x++) {
			if ($x == $payMethod) {
				$payInfoDropDown.= "<option selected value=$x>$payMethodString[$x]</option>";
			} else {
				$payInfoDropDown.= "<option value=$x>$payMethodString[$x]</option>";
			}
		}
	return $payInfoDropDown;
}

function getAboutUsDD($aboutUsMethod) {
	$aboutUsMethodString[1] = 'Google/Internet Search';
	$aboutUsMethodString[2] = 'Facebook Page';
	$aboutUsMethodString[3] = 'Kijiji Ad';
	$aboutUsMethodString[4] = 'Returning Tournament Player';
	$aboutUsMethodString[5] = 'Play in Perpetual Leagues';
	$aboutUsMethodString[6] = 'From A Friend';
	$aboutUsMethodString[7] = 'Restaurant Advertisement';
	$aboutUsMethodString[8] = 'The Guelph Community Guide';
	$aboutUsMethodString[9] = 'Other';
	$aboutUsDropDown = '';
	
	$aboutUsDropDown.= "<option value=0>Choose Method</option>";
		for($x=1; $x<= count($aboutUsMethodString);$x++) {
			if ($x == $aboutUsMethod) {
				$aboutUsDropDown.= "<option selected value=$x>$aboutUsMethodString[$x]</option>";
			} else {
				$aboutUsDropDown.= "<option value=$x>$aboutUsMethodString[$x]</option>";
			}
		}
	return $aboutUsDropDown;
}

function getPlayerCardDD($playerCard, $numCards, $tourneyObj, $leagueID) {
	global $tournamentPlayersTable;
	$takenCardsArray = array();

	if($playerCard - 300 < 0) {
		$firstCardValue = 101;
		$secondCardValue = 201;
	} else {
		$firstCardValue = 301;
		$secondCardValue = 401;
	}
	$playerQuery = mysql_query("SELECT * FROM $tournamentPlayersTable WHERE tournament_player_tournament_id = ".$tourneyObj->tourneyID." 
		AND tournament_player_tournament_num_running = ".$tourneyObj->tourneyNumRunning." AND tournament_player_card >= $firstCardValue AND tournament_player_league_id = $leagueID
		AND tournament_player_card <= $secondCardValue + 100") or die('ERROR getting players for cards - '.mysql_error());
	while($playerArray = mysql_fetch_array($playerQuery)) {
		array_push($takenCardsArray, $playerArray['tournament_player_card']);	
	}
	
	$playerCardDD = '';
	for($j=$firstCardValue;$j < $firstCardValue + $numCards;$j++) {
		if($j == $playerCard && !in_array($j, $takenCardsArray)){
			$playerCardDD.="<option selected value=$j>".getCardString($j).'</option>';
		}else if (!in_array($j, $takenCardsArray)) {
			$playerCardDD.="<option value=$j>".getCardString($j).'</option>';
		}
	}
	for($j=$secondCardValue;$j < $secondCardValue + $numCards;$j++) {
		if($j == $playerCard && !in_array($j, $takenCardsArray)){
			$playerCardDD.="<option selected value=$j>".getCardString($j).'</option>';
		}else if (!in_array($j, $takenCardsArray)) {
			$playerCardDD.="<option value=$j>".getCardString($j).'</option>';
		}
	}
	return $playerCardDD;
}

function getCardString($playerCard) {
	for($i=0;$i<4;$i++) {
		$playerCard = $playerCard - 100;
		if($playerCard < 100) {
			if($i == 0) {
				$cardSuit = 'Spades';
			} else if($i == 1) {
				$cardSuit = 'Clubs';
			} else if($i == 2) {
				$cardSuit = 'Hearts';
			} else if($i == 3) {
				$cardSuit = 'Diamonds';
			}
			$i=500;
		}
	}
	if($playerCard == 1) {
		return 'Ace of '.$cardSuit;
	} else if($playerCard < 11) {
		return $playerCard.' of '.$cardSuit;
	} else if($playerCard == 11) {
		return 'Jack of '.$cardSuit;
	} else if($playerCard == 12) {
		return 'Queen of '.$cardSuit;
	} else if($playerCard == 13) {
		return 'King of '.$cardSuit;
	} else {
		$cardValue = $playerCard - 3;
		return $cardValue.' of '.$cardSuit;
	}
}?>