<?php

function updatePlayer($playerID, $playerObj) {
	global $tournamentPlayersTable;
	$error = '';

	if($playerID == 0) {
		$error.='No player specified<br />';
	}
	if ($playerObj->playerFirstName == '') {
		$error.='No First Name Specified<br />';
	} else {
		$playerFirstName = mysql_escape_string($playerObj->playerFirstName);
	}
	if ($playerObj->playerLastName == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$playerLastName = mysql_escape_string($playerObj->playerLastName);
	}
	$playerGender = $playerObj->playerGender;
	
	if($playerObj->playerEmail != '') {
		if (filter_var($playerObj->playerEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$playerEmail = mysql_escape_string($playerObj->playerEmail);	
		} else {
			$error.='Email not valid<br />';
		}
	}
	$playerNote = mysql_escape_string($playerObj->playerNote);
	
	if(strlen($error) < 2) {
		mysql_query("UPDATE $tournamentPlayersTable SET tournament_player_firstname = '$playerFirstName', tournament_player_lastname = '$playerLastName', 
			tournament_player_email = '$playerEmail', tournament_player_gender = '$playerGender', tournament_player_note = '$playerNote' WHERE tournament_player_id = $playerID") 
			or die('ERROR updating player '.mysql_error());
		print 'Player updated';
	} else {
		print $error;
	}
		
}