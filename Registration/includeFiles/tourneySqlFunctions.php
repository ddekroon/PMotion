<?php
function registerTeam($teamObj, $playerObj, $tourneyObj, $extraPlayersObj) {
	global $tournamentTeamsTable, $tournamentPlayersTable;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$tourneyID = $tourneyObj->tourneyID;
	$teamName=addslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	$comments=addslashes(html_entity_decode($teamObj->teamComments, ENT_QUOTES));
	$teamPayMethod = $teamObj->teamPayMethod;
	$teamRating = $teamObj->teamRating;
	$leagueID = $teamObj->teamLeagueID;
	$extraField = addslashes(html_entity_decode($playerObj->playerExtraData, ENT_QUOTES));
	$capFirst=addslashes(html_entity_decode($playerObj->playerFirstName, ENT_QUOTES));
	$capLast=addslashes(html_entity_decode($playerObj->playerLastName, ENT_QUOTES));
	$capSex = $playerObj->playerGender;
	$capEmail=addslashes(html_entity_decode($playerObj->playerEmail, ENT_QUOTES));
	$capPhone=preg_replace("/\D/",'',$playerObj->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	$capRating = $playerObj->playerRating;
	$capAddress = addslashes(html_entity_decode($playerObj->playerAddress, ENT_QUOTES));
	$capCity = addslashes(html_entity_decode($playerObj->playerCity, ENT_QUOTES));
	$capPostalCode = addslashes(html_entity_decode($playerObj->playerPostalCode, ENT_QUOTES));
	$capProvince = addslashes(html_entity_decode($playerObj->playerProvince, ENT_QUOTES));
	$capHearMethod = $playerObj->playerHearMethod;
	$capHearText = addslashes(html_entity_decode($playerObj->playerHearOtherText, ENT_QUOTES));
	$numRunning = $tourneyObj->tourneyNumRunning;
	$teamNum = getTeamNum($tourneyObj, $teamObj);
	$isWaiting = $tourneyObj->tourneyIsFull[$leagueID];
	
	//gets new team id number
	$maxTeamIDArray = mysql_query("SELECT MAX(tournament_team_id) as maxNum FROM $tournamentTeamsTable");
	$maxNumArray = mysql_fetch_array($maxTeamIDArray);
	$teamID = $maxNumArray['maxNum'] +1;	
	
	$teamInsert="INSERT INTO $tournamentTeamsTable (tournament_team_id, tournament_team_tournament_id, tournament_team_league_id, 
		tournament_team_name, tournament_team_num_in_league, tournament_team_num_in_tournament, tournament_team_rating, 
		tournament_team_created, tournament_team_payment_method, tournament_team_note, tournament_team_extra_field, 
		tournament_team_tournament_num_running, tournament_team_is_waiting) VALUES ($teamID, $tourneyID, $leagueID, '$teamName', 
		$teamNum, $teamNum, $teamRating, NOW(), $teamPayMethod, '$comments', '$extraField', $numRunning, $isWaiting)";
	mysql_query($teamInsert)or die("The 'insert into $tournamentTeamsTable' query threw an error: ".mysql_error());
	$playerInsert="INSERT INTO $tournamentPlayersTable (tournament_player_tournament_id, tournament_player_team_id, 
		tournament_player_firstname, tournament_player_lastname, tournament_player_email, tournament_player_skill, 
		tournament_player_phone, tournament_player_gender, tournament_player_address, tournament_player_province,  
		tournament_player_postal_code, tournament_player_city, tournament_player_tournament_num_running,
		tournament_player_hear_method, tournament_player_other_text, tournament_player_created_date, tournament_player_league_id) 
		VALUES ($tourneyID, $teamID, '$capFirst', '$capLast', '$capEmail', $capRating, $capPhone, '$capSex', '$capAddress', 
		'$capProvince', '$capPostalCode', '$capCity', $numRunning, $capHearMethod, '$capHearText', NOW(), $leagueID)";
	mysql_query($playerInsert) or die("The 'insert into $tournamentPlayersTable' query threw an error: ".mysql_error());
	insertPlayerAddressDB($capFirst, $capLast, $capAddress);
	
	if($tourneyObj->tourneyRegIsPlayers == 1) {
		for($i = 0; $i < $tourneyObj->tourneyRegNumPlayers; $i++) {
			if(strlen($extraPlayersObj[$i]->playerFirstName) > 0) {
				$playerFirst=addslashes(html_entity_decode($extraPlayersObj[$i]->playerFirstName, ENT_QUOTES));
				$playerLast=addslashes(html_entity_decode($extraPlayersObj[$i]->playerLastName, ENT_QUOTES));
				$playerSkill = $extraPlayersObj[$i]->playerRating;
				$playerInsert="INSERT INTO $tournamentPlayersTable (tournament_player_tournament_id, tournament_player_team_id, tournament_player_firstname, tournament_player_lastname, tournament_player_league_id, 
					tournament_player_skill, tournament_player_tournament_num_running, tournament_player_created_date) VALUES ($tourneyID, $teamID, '$playerFirst', '$playerLast', $leagueID, $playerSkill, $numRunning, NOW())";
				mysql_query($playerInsert) or die("The 'insert extra players into $tournamentPlayersTable' query threw an error: ".mysql_error());
			}
		}
	}
	return $teamID;
}

function getTeamNum($tourneyObj, $teamObj) {
	global $tournamentTeamsTable;
	if($tourneyObj->tourneyIsLeagues == 1) {
		$maxTeamNumArray = mysql_query("SELECT MAX(tournament_team_num_in_league) as highestTeamNum FROM $tournamentTeamsTable 
			WHERE tournament_team_tournament_id = ".$tourneyObj->tourneyID." AND tournament_team_tournament_num_running = ".$tourneyObj->tourneyNumRunning." 
			AND tournament_team_league_id = ".$teamObj->teamLeagueID) or die('ERROR getting team num in league - '.mysql_error());
		$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
		$teamNum = $maxTeamNum['highestTeamNum'] +1;
	} else {
		$maxTeamNumArray = mysql_query("SELECT MAX(tournament_team_num_in_tournament) as highestTeamNum FROM $tournamentTeamsTable 
			WHERE tournament_team_tournament_id = ".$tourneyObj->tourneyID." AND tournament_team_tournament_num_running = ".$tourneyObj->tourneyNumRunning)
			or die('ERROR getting team num in tournament - '.mysql_error());
		$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
		$teamNum = $maxTeamNum['highestTeamNum'] +1;
	}
	return $teamNum;
}

function registerPlayer($teamObj, $playerObj, $tourneyObj, $extraPlayersObj) {
	global $tournamentPlayersTable;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$tourneyID = $tourneyObj->tourneyID;
	$comments=addslashes(html_entity_decode($teamObj->teamComments, ENT_QUOTES));
	$playerPayMethod = $teamObj->teamPayMethod;
	$playerRating = $playerObj->playerRating;
	$leagueID = $teamObj->teamLeagueID;
	$extraField = addslashes(html_entity_decode($playerObj->playerExtraData, ENT_QUOTES));
	$capFirst=addslashes(html_entity_decode($playerObj->playerFirstName, ENT_QUOTES));
	$capLast=addslashes(html_entity_decode($playerObj->playerLastName, ENT_QUOTES));
	$capSex = $playerObj->playerGender;
	$capEmail=addslashes(html_entity_decode($playerObj->playerEmail, ENT_QUOTES));
	$capSkill = $playerObj->playerRating;
	$capPhone=preg_replace("/\D/",'',$playerObj->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	$capAddress = addslashes(html_entity_decode($playerObj->playerAddress, ENT_QUOTES));
	$capCity = addslashes(html_entity_decode($playerObj->playerCity, ENT_QUOTES));
	$capPostalCode = addslashes(html_entity_decode($playerObj->playerPostalCode, ENT_QUOTES));
	$capProvince = addslashes(html_entity_decode($playerObj->playerProvince, ENT_QUOTES));
	$capCard = $playerObj->playerCard;
	$numRunning = $tourneyObj->tourneyNumRunning;
	if($capSex == 'M') {
		$isWaiting = $tourneyObj->tourneyIsFullMale[$leagueID];
	} else {
		$isWaiting = $tourneyObj->tourneyIsFullFemale[$leagueID];
	}
	
	//gets new team id number
	$maxPlayerIDArray = mysql_query("SELECT MAX(tournament_player_id) as maxNum FROM $tournamentPlayersTable");
	$maxNumArray = mysql_fetch_array($maxPlayerIDArray);
	$playerID = $maxNumArray['maxNum'] +1;	

	$playerInsert="INSERT INTO $tournamentPlayersTable (tournament_player_id, tournament_player_tournament_id, tournament_player_card, tournament_player_firstname, 
		tournament_player_lastname, tournament_player_email, tournament_player_skill, tournament_player_phone, tournament_player_gender, tournament_player_address,  
		tournament_player_province,tournament_player_postal_code, tournament_player_city, tournament_player_league_id, tournament_player_tournament_num_running, tournament_player_created_date, tournament_player_is_waiting) VALUES ($playerID, $tourneyID, 
		$capCard, '$capFirst', '$capLast', '$capEmail', $capSkill, '$capPhone', '$capSex', '$capAddress', '$capProvince', '$capPostalCode', '$capCity', $leagueID, $numRunning, NOW(), $isWaiting)";
	mysql_query($playerInsert) or die("The 'insert into $tournamentPlayersTable' query threw an error: ".mysql_error());
	
	if($tourneyObj->tourneyRegIsPlayers == 1) {
		for($i = 0; $i < $tourneyObj->tourneyRegNumPlayers; $i++) {
			$playerFirst=addslashes(html_entity_decode($extraPlayersObj[$i]->playerFirstName, ENT_QUOTES));
			$playerLast=addslashes(html_entity_decode($extraPlayersObj[$i]->playerLastName, ENT_QUOTES));
			$playerSkill = $extraPlayersObj[$i]->playerRating;
			$playerInsert="INSERT INTO $tournamentPlayersTable (tournament_player_tournament_id, tournament_player_team_id, tournament_player_firstname, tournament_player_lastname, 
				tournament_player_skill, tournament_player_league_id, tournament_player_created_date) VALUES ($tourneyID, $teamID, '$playerFirst', '$playerLast', $playerSkill, $leagueID, NOW())";
			mysql_query($playerInsert) or die("The 'insert into $tournamentPlayersTable' query threw an error: ".mysql_error());
		}
	}
	return $playerID;
}

function insertPlayerAddressDB($playerFirst, $playerLast, $playerEmail) {
	
	if(filter_var($playerEmail, FILTER_VALIDATE_EMAIL)) {
		$numPeople = mysql_num_rows(mysql_query("SELECT * FROM addressdatabase WHERE EmailAddress = '$playerEmail'"));
		if($numPeople == 0) {
			mysql_query("INSERT INTO addressdatabase (FirstName, LastName, EmailAddress) VALUES ('$playerFirst', '$playerLast', '$playerEmail')") 
				or die('ERROR putting email in - '.mysql_error());
		}
	}
}

//returns 2 if the league is full, 1 if the user will be put on waiting, 0 if they're fine to register
function checkLeaguesFull($leagueID) {
	global $leaguesTable, $teamsTable;
	
	//gets new team number in league
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
	$leagueArray = mysql_fetch_array($leagueQuery);
	$numUntilWaiting = $leagueArray['league_num_teams_before_waiting'];
	$leagueFull = $leagueArray['league_maximum_teams'];

	//gets new team number in league
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_finalized = 1");
	$numTeamsRegistered = mysql_num_rows($teamsQuery);

	if ($numTeamsRegistered >= $leagueFull && $leagueFull != 0) {
		return 2;
	} else if ($numTeamsRegistered >= $numUntilWaiting && $leagueFull != 0) {
		return 1;
	} else {
		return 0;
	}
}

//This function formats a phone number into the proper display. Makes "5198234502" or anything similar appear as "(519)-823-4502"
//How it works:
//- Chops phone number input into 3 sections: area code, prefix (first 3 digits), and number (last 4 digits)
function formatPhoneNumber($strPhone){
        $strPhone = ereg_replace("[^0-9]",'', $strPhone);
        if (strlen($strPhone)!= 10){
                return $strPhone;
        }
        $strArea = substr($strPhone, 0, 3);
        $strPrefix = substr($strPhone, 3, 3);
        $strNumber = substr($strPhone, 6, 4);
        $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;
        return ($strPhone);
}