<?php

function updatePlayer($playerID) {
	global $playersTable, $individualsTable, $userTable, $container;
	global $player;
	global $teamID, $newTeamID, $leagueID, $newLeagueID;

	$error = '';

	if($playerID == 0) {
		$error.='No player specified<br />';
	}
	
	if ($player->playerFirstName == '') {
		$error.='No First Name Specified<br />';
	} else {
		$playerFirstName = mysql_escape_string($player->playerFirstName);
	}
	if ($player->playerLastName == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$playerLastName = mysql_escape_string($player->playerLastName);
	}
	
	if($player->playerEmail != '') {
		if (filter_var($player->playerEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$playerEmail = mysql_escape_string($player->playerEmail);	
		} else {
			$error.='Email not valid<br />';
		}
	}

	if($player->playerUserEmail != '') {
		if (filter_var($player->playerUserEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$playerUserEmail = mysql_escape_string($player->playerUserEmail);	
			
		} else {
			$error.='User email not valid<br />';
		}
	} else {
		$playerUserEmail = 'N/A';
	}
	if(($playerUserID = $player->playerUserID) == '') {
		$playerUserID = 0;
	}
	$playerNote = mysql_escape_string($player->playerNote);
	$playerNote = trim(preg_replace('/\s\s+/', ' ', $playerNote));
	if (strlen($playerNote)==15) {
		$playerNote = "";
	}
	$playerIsCaptain = $player->playerIsCaptain;
	$playerGender = $player->playerGender;
	$playerPhone = $player->playerPhone;
	if($leagueID != $newLeagueID) {
		$leagueID = $newLeagueID;
	}
	if($teamID != $newTeamID) {
		$teamID = $newTeamID;
	}
	
	if(strlen($error) < 2) {
		if($playerIsCaptain == 1) {
			mysql_query("UPDATE $playersTable SET player_is_captain = 0 WHERE player_id = $playerID") 
				or die('ERROR setting captain to 0 '.mysql_error());
			mysql_query("UPDATE $userTable SET user_email = '$playerUserEmail' WHERE user_id = $playerUserID") 
				or die('ERROR setting user email - '.mysql_error());
		}
		mysql_query("UPDATE $playersTable SET player_team_id = " . ($teamID > 0 ? $teamID : "NULL") . ", player_firstname = '$playerFirstName', 
			player_lastname = '$playerLastName', player_email = '$playerEmail', player_sex = '$playerGender', player_phone = '$playerPhone',
			player_note = '$playerNote', player_is_captain = $playerIsCaptain WHERE player_id = $playerID") 
			or die('ERROR updating player '.mysql_error());
		mysql_query("UPDATE $individualsTable SET individual_preferred_league_id = $leagueID 
			WHERE individual_player_id = $playerID") or die('ERROR updating individual '.mysql_error());
		if($_POST['moveGroup'] != 0 && $player->playerIndividualGroup != 0) {
			mysql_query("UPDATE $playersTable 
				INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id 
				SET player_team_id = $teamID, individual_preferred_league_id = $leagueID 
				WHERE individual_small_group_id = ".$player->playerIndividualGroup) 
				or die('ERROR updating players '.mysql_error());
		}
		$container->printSuccess('Player Updated Successfully');
	} else {
		print $container->printError($error);
	}
		
}