<?php

function getPlayerData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction) {
	global $playersTable, $leaguesTable, $seasonsTable, $sportsTable, $teamsTable, $playerArray, $container, $dbConnection;
	
	$checkAddresses = array();
	
	$sportID != 0?$sportClause = "sport_id = $sportID AND ":$sportClause = '';
	if($leagueID != 0) {
		$leagueClause = "league_id = $leagueID AND ";
	} else {
		$leagueClase = '';
	}
	if($dayNumber != 0) {
		$dayClause = "league_day_number = $dayNumber AND ";
	} else {
		$dayClause = '';
	}
	if($seasonID != 0) {
		$seasonClause = "season_id = $seasonID";
	} else {
		$seasonClause = "season_available_score_reporter = 1";
	}
	
	if(strlen($orderBy) > 1) {
		$orderClause = "ORDER BY $orderBy $direction, team_id ASC, player_id ASC";
	} else {
		$orderClause = 'ORDER BY team_num_in_league ASC, team_id ASC, player_id ASC';
	}

	$playersQuery = ("SELECT player_firstname, player_lastname, player_email, team_name, league_id, player_id, sport_id, league_id, league_name, league_day_number, team_id 
		FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE $sportClause $leagueClause $dayClause $seasonClause AND team_num_in_league > 0 $orderClause");
	if(!($result = $dbConnection->query($playersQuery))) $container->printError('ERROR getting players - '.$dbConnection->error);
	$numPlayers = 0;
	while($player = $result->fetch_object()) {
		$checkString = $player->player_firstname.' '.$player->player_lastname.' '.$player->player_email.' '.$player->team_name.' '.$player->league_id;
		if(strlen($player->team_name) > 1 && filter_var($player->player_email, FILTER_VALIDATE_EMAIL) &&
			!in_array($checkString, $checkAddresses)) {
			$playerArray[$numPlayers] = new Player();
			$playerArray[$numPlayers]->playerID = $player->player_id;
			$playerArray[$numPlayers]->playerTeamName = $player->team_name;
			$playerArray[$numPlayers]->playerFirstName = $player->player_firstname;
			$playerArray[$numPlayers]->playerLastName = $player->player_lastname;
			$playerArray[$numPlayers]->playerEmail = $player->player_email;
			$playerArray[$numPlayers]->playerSportID = $player->sport_id;
			$playerArray[$numPlayers]->playerLeagueID = $player->league_id;
			$playerArray[$numPlayers]->playerLeagueName = $player->league_name;
			$playerArray[$numPlayers]->playerGameDay = $player->league_day_number;
			$playerArray[$numPlayers]->playerTeamID = $player->team_id;
			$numPlayers++;
			array_push($checkAddresses, $checkString);
		}
	}
	return $numPlayers;

}

function getCaptainData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction) {
	global $playersTable, $leaguesTable, $seasonsTable, $sportsTable, $teamsTable, $playerArray, $container, $dbConnection;
	$filterChosen = 1;
	
	$checkAddresses = array();
	
	if($sportID != 0) {
		$sportClause = "sport_id = $sportID AND ";
	} else {
		$sportClause = '';
	}
	if($leagueID != 0) {
		$leagueClause = "league_id = $leagueID AND ";
	} else {
		$leagueClase = '';
	}
	if($dayNumber != 0) {
		$dayClause = "league_day_number = $dayNumber AND ";
	} else {
		$dayClause = '';
	}
	if($seasonID != 0) {
		$seasonClause = "season_id = $seasonID";
	} else {
		$seasonClause = "season_available_score_reporter = 1";
	}
	if($seasonID == 0 && $dayNumber == 0 && $leagueID == 0 && $sportID == 0) {
		$filterChosen = 0;
	}
	
	if($filterChosen == 1) {
		if(strlen($orderBy) > 1) {
			$orderClause = "ORDER BY $orderBy $direction";
		} else {
			$orderClause = '';
		}
		$playersQuery = "SELECT player_firstname, player_lastname, player_email, team_name, league_id, player_id, sport_id, league_id, league_name, league_day_number, team_id, team_late_email_allowed
			FROM $playersTable 
			INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
			INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
			INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
			WHERE $sportClause $leagueClause $dayClause $seasonClause AND team_num_in_league > 0 
			AND player_is_captain = 1 $orderClause";
		if(!($result = $dbConnection->query($playersQuery))) 
			$container->printError('ERROR getting players - '.$dbConnection->error);
			
		$numPlayers = 0;
		
		while($player = $result->fetch_object()) {
			$checkString = $player->player_firstname.' '.$player->player_lastname.' '.$player->player_email.' '.$player->team_name.' '.$player->league_id;
		if(strlen($player->team_name) > 1 && filter_var($player->player_email, FILTER_VALIDATE_EMAIL) &&
			!in_array($checkString, $checkAddresses)) {
				$playerArray[$numPlayers] = new Player();
				$playerArray[$numPlayers]->playerID = $player->player_id;
				$playerArray[$numPlayers]->playerTeamName = $player->team_name;
				$playerArray[$numPlayers]->playerFirstName = $player->player_firstname;
				$playerArray[$numPlayers]->playerLastName = $player->player_lastname;
				$playerArray[$numPlayers]->playerEmail = $player->player_email;
				$playerArray[$numPlayers]->playerSportID = $player->sport_id;
				$playerArray[$numPlayers]->playerLeagueID = $player->league_id;
				$playerArray[$numPlayers]->playerLeagueName = $player->league_name;
				$playerArray[$numPlayers]->playerGameDay = $player->league_day_number;
				$playerArray[$numPlayers]->playerTeamID = $player->team_id;				
				$playerArray[$numPlayers]->lateEmailAllowed = $player->team_late_email_allowed;
				
				$numPlayers++;
				array_push($checkAddresses, $checkString);
			}
		}
		return $numPlayers;
	} else {
		return 0;
	}

} 

function getAgentData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction) {
	global $playersTable, $leaguesTable, $seasonsTable, $sportsTable, $individualsTable, $playerArray, $container, $dbConnection;
	
	$checkAddresses = array();
	
	if($sportID != 0) {
		$sportClause = "sport_id = $sportID AND ";
	} else {
		$sportClause = '';
	}
	if($leagueID != 0) {
		$leagueClause = "league_id = $leagueID AND ";
	} else {
		$leagueClase = '';
	}
	if($dayNumber != 0) {
		$dayClause = "league_day_number = $dayNumber AND ";
	} else {
		$dayClause = '';
	}	
	
	if(strlen($orderBy) > 1) {
		$orderClause = "$orderBy $direction, individual_small_group_id ASC";
	} else {
		$orderClause = 'individual_small_group_id ASC';
	}
	
	$playersQuery = "SELECT player_firstname, player_lastname, player_email, team_name, league_id, player_id, sport_id, league_id, league_name, league_day_number, team_id, 
		individual_small_group_id FROM $playersTable 
		INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $individualsTable.individual_preferred_league_id
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE $sportClause $leagueClause $dayClause season_id = $seasonID ORDER BY $orderClause";
	if(!($result = $dbConnection->query($playersQuery))) $container->printError('ERROR getting players - '.$dbConnection->error);
	$numPlayers = 0;
	while($player = $result->fetch_object()) {
		$checkString = $player->player_firstname.' '.$player->player_lastname.' '.$player->player_email.' '
			.$player->league_id;
			
		if(filter_var($player->player_email, FILTER_VALIDATE_EMAIL) && !in_array($checkString, $checkAddresses)) {
			$playerArray[$numPlayers] = new Player();
			$playerArray[$numPlayers]->playerID = $player->player_id;
			$playerArray[$numPlayers]->playerFirstName = $player->player_firstname;
			$playerArray[$numPlayers]->playerLastName = $player->player_lastname;
			$playerArray[$numPlayers]->playerEmail = $player->player_email;
			$playerArray[$numPlayers]->playerSportID = $player->sport_id;
			$playerArray[$numPlayers]->playerLeagueID = $player->league_id;
			$playerArray[$numPlayers]->playerLeagueName = $player->league_name;
			$playerArray[$numPlayers]->playerGameDay = $player->league_day_number;
			$playerArray[$numPlayers]->playerGroupNum = $player->individual_small_group_id;
			$numPlayers++;
			array_push($checkAddresses, $checkString);
		}
	}
	return $numPlayers;

}

function getPastTournamentData($tourneyID, $year, $orderBy, $direction) {
	global $tournamentPlayersTable, $tournamentsTable, $tournamentTeamsTable, $playerArray, $container, $dbConnection;
	$andYear = '';
	
	$curPlayerEmails = getCurPlayersList($tourneyID);
	$oldEmails = array();
	
	if($year != 0) {
		$yearClause = " YEAR(tournament_player_created_date) = $year AND";
	} else {
		$yearClause = '';
	}
	if($tourneyID != 0) {
		$tourneyClause = " tournament_player_tournament_id = $tourneyID AND ";
	} else {
		$sportClause = '';
	}
	
	if(strlen($orderBy) > 1) {
		$orderClause = "$orderBy $direction, ";
	} else {
		$orderClause = '';
	}
	
	
	$playersQuery = "SELECT tournament_player_id, tournament_player_firstname, tournament_player_lastname, tournament_player_email, tournament_player_tournament_id, 
		tournament_player_created_date
		FROM $tournamentPlayersTable INNER JOIN $tournamentsTable ON
		$tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id WHERE $tourneyClause 
		$yearClause $tournamentPlayersTable.tournament_player_tournament_num_running < 
		$tournamentsTable.tournament_num_running ORDER BY $orderClause tournament_player_tournament_num_running DESC, 
		tournament_player_created_date DESC";
	if(!($result = $dbConnection->query($playersQuery))) $container->printError('ERROR getting players - '.$dbConnection->error);
	$numPlayers = 0;
	while($player = $result->fetch_object()) {
		if(!in_array(strtolower($player->tournament_player_email), $curPlayerEmails) 
			&& !in_array(strtolower($player->tournament_player_email),$oldEmails)) {
			$playerArray[$numPlayers] = new Player();
			$playerArray[$numPlayers]->playerID = $player->tournament_player_id;
			$playerArray[$numPlayers]->playerFirstName = $player->tournament_player_firstname;
			$playerArray[$numPlayers]->playerLastName = $player->tournament_player_lastname;
			$playerArray[$numPlayers]->playerEmail = strtolower($player->tournament_player_email);
			array_push($oldEmails, $playerArray[$numPlayers]->playerEmail);
			$playerArray[$numPlayers]->playerTournamentID = $player->tournament_player_tournament_id;
			$playerDate = explode('-', $player->tournament_player_created_date);
			$playerArray[$numPlayers]->playerYear = $playerDate[0];
			$teamID = $player->tournament_player_team_id;
			if($teamID != 0) {
				$teamQuery = "SELECT tournament_team_name FROM $tournamentTeamsTable WHERE tournament_team_id = $teamID LIMIT 1";
				if(!($teamResult = $dbConnection->query($teamQuery))) 
					$container->printError('ERROR getting team name - '.$dbConnection->error);
				$teamObj = $teamResult->fetch_object();
				$playerArray[$numPlayers]->playerTeamName = $teamObj->tournament_team_name;
			}
			$numPlayers++;
		}
	}
	return $numPlayers;
}

function getCurPlayersList($tourneyID) {
	global $tournamentPlayersTable, $tournamentsTable, $container, $dbConnection;
	$curPlayers = array();
	
	$playersQuery = "SELECT tournament_player_email FROM $tournamentPlayersTable INNER JOIN $tournamentsTable ON
		$tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id WHERE 
		$tournamentPlayersTable.tournament_player_tournament_num_running = $tournamentsTable.tournament_num_running
		AND tournament_player_tournament_id = $tourneyID";
	if(!($result = $dbConnection->query($playersQuery))) $container->printError('ERROR getting cur Teams - '.$dbConnection->error);
	while($playerArray = $result->fetch_object()) {
		array_push($curPlayers, strtolower($playerArray->tournament_player_email));
	}
	return $curPlayers;
}?>