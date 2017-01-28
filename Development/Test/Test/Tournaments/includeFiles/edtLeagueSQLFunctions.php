<?php

function createNewPlayer($playerObj, $tourneyID, $tourneyObj) {
	global $tournamentPlayersTable;
	$error = '';

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
	$playerPaid = $playerObj->playerPaid;
	$playerCard = $playerObj->playerCard;
	if($playerCard > 300) {
		$playerGender = 'F';
	} else if($playerCard < 300 && $playerCard > 100) {
		$playerGender = 'M';
	} else {
		$error.='No card selected<br />';
	}
	$leagueID = $playerObj->playerLeagueID;
	$playerNote = mysql_escape_string($playerObj->playerNote);
	$numRunning = $tourneyObj->tourneyNumRunning;
	
	if(strlen($error) < 2) {
		$playerNumQuery = mysql_query("SELECT MAX(tournament_player_id) as maxNum FROM $tournamentPlayersTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$playerID = $playerArray['maxNum'] +1;
		$tourneyObj->tourneyIsLeagues == 1?$leagueFilter = 'tournament_player_num_in_league':$leagueFitler = 'tournament_player_num_in_tournament';
		$playerNumQuery = mysql_query("SELECT MAX($leagueFilter) as maxNum FROM $tournamentPlayersTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$playerNum = $playerArray['maxNum'] +1;
		
		mysql_query("INSERT INTO $tournamentPlayersTable (tournament_player_id, tournament_player_tournament_id, tournament_player_league_id, $leagueFilter, tournament_player_firstname, 
			tournament_player_lastname, tournament_player_gender, tournament_player_paid, tournament_player_note, tournament_player_card, tournament_player_tournament_num_running) 
			VALUES ($playerID, $tourneyID, $leagueID, $playerNum, '$playerFirstName', '$playerLastName', '$playerGender', $playerPaid, '$playerNote', $playerCard, $numRunning)") 
			or die('ERROR adding new player '.mysql_error());
	} else {
		print $error;
	}
	
}

function deleteTeams() {
	global $tournamentTeamsTable;
	
	if (isset($_POST['teamDelete'])) {
		$effectedCount = 0;
		foreach($_POST['teamDelete'] as $teamID) {
			mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_num_in_tournament = 0, tournament_team_num_in_league = 0 WHERE tournament_team_id = $teamID")
				or die('ERROR deleting team '.$teamID.' - '.mysql_error());
			$effectedCount++;
		}
		print 'Deletions complete, '.$effectedCount.' teams deleted.';
	} else {
		print 'No teams selected to delete';
	}
	
} 

function deletePlayers() {
	global $tournamentPlayersTable;
	
	if (isset($_POST['playerDelete'])) {
		$effectedCount = 0;
		foreach($_POST['playerDelete'] as $playerID) {
			if($playerID != '') {
				mysql_query("UPDATE $tournamentPlayersTable SET tournament_player_num_in_tournament = 0, tournament_player_num_in_league = 0, tournament_player_card = 0
					WHERE tournament_player_id = $playerID") or die('ERROR deleting players '.mysql_error());
				$effectedCount++;
			}
		}
		print 'Deletions complete, '.$effectedCount.' players effected.';
	} else {
		print 'No players selected to delete';
	}
	
} 

function changeCardPlayerInfo() {
	global $tournamentPlayersTable;

	for($i=0;$i<count($_POST['playerID']);$i++) {
		$playerID = $_POST['playerID'][$i];
		$playerPaid = $_POST['playerPaid'][$i];
		$playerNewCard = $_POST['playerCardDD'][$i];
		mysql_query("UPDATE $tournamentPlayersTable SET tournament_player_paid = $playerPaid, tournament_player_card = $playerNewCard WHERE tournament_player_id = $playerID")
			or die('ERROR updating player - '.$playerID.' - '.mysql_error());
	}
	print 'Players updated';
}

function changeTeamInfo($tourneyObj, $tourneyID) {
	global $tournamentTeamsTable;
	$numAffected = 0;
	$isLeagues = $tourneyObj->tourneyIsLeagues;
	$numRunning = $tourneyObj->tourneyNumRunning;
	
	for($i=0;$i<count($_POST['teamID']); $i++) {
		$teamID = $_POST['teamID'][$i];
		$teamName = mysql_escape_string($_POST['teamName'][$i]);
		$teamPaid = $_POST['teamPaid'][$i];
		$teamNum = $_POST['teamNumDD'][$i];
		$teamRating = $_POST['teamRatingDD'][$i];
		$teamNote = mysql_escape_string($_POST['teamNote'][$i]);
		$teamExtra = mysql_escape_string($_POST['teamExtra'][$i]);
		if($isLeagues == 1) {
			$numUpdate = "tournament_team_num_in_league = $teamNum";
			$numInLeague = $teamNum;
			$numInTournament = 0;
		} else {
			$numUpdate = "tournament_team_num_in_tournament = $teamNum";
			$numInLeague = 0;
			$numInTournament = $teamNum;
		}
		
		if($teamID > 0 && strlen($teamName) > 0) {
			mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_name = '$teamName', tournament_team_paid = $teamPaid, $numUpdate, 
				tournament_team_rating = $teamRating, tournament_team_note = '$teamNote', tournament_team_tournament_num_running = $numRunning, tournament_team_extra_field = '$teamExtra'
				WHERE tournament_team_id = $teamID") or die('ERROR updating teams '.mysql_error());
			$numAffected++;
		} else if ($teamID == 0 && strlen($teamName) > 0) {
			$maxTeamIDArray = mysql_query("SELECT MAX(tournament_team_id) as maxNum FROM $tournamentTeamsTable");
			$maxNumArray = mysql_fetch_array($maxTeamIDArray);
			$teamID = $maxNumArray['maxNum'] +1;
			
			mysql_query("INSERT INTO $tournamentTeamsTable (tournament_team_id, tournament_team_tournament_id, tournament_team_league_id, tournament_team_name, tournament_team_paid, 
				tournament_team_num_in_league, tournament_team_num_in_tournament, tournament_team_rating, tournament_team_note, tournament_team_tournament_num_running, tournament_team_created) VALUES 
				($teamID, $tourneyID, $leagueID, '$teamName', $teamPaid, $numInLeague, $numInTournament, $teamRating, '$teamNote', $numRunning, NOW())") 
				or die('ERROR changing name '.mysql_error());
			$numAffected++;
		}
	}
	print $numAffected.' teams effected';
} ?>