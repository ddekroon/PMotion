<?php

function changeDBTeamName($teamID, $newTeamName) {
	global $tournamentTeamsTable;
	$newTeamName = mysql_escape_string($newTeamName);
	
	mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_name = '$newTeamName' WHERE tournament_team_id = $teamID") or die('ERROR changing name '.mysql_error());
	print 'Name changed successfully';
}

function fixTeamNumbers($leagueID, $teamID) {
	global $tournamentTeamsTable, $container;
	$teamNum = 0;
	$deleteNum = 0;
	$teamsQuery = mysql_query("SELECT * FROM $tournamentTeamsTable WHERE tournament_team_league_id = $leagueID AND tournament_team_num_in_league > 0 ORDER BY tournament_team_num_in_league")
		or die('ERROR getting teams to change #s'.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$teamIDArray[$teamNum] = $teamArray['tournament_team_id'];
		$teamNumInLeague[$teamNum] = $teamArray['tournament_team_num_in_league'];
		if($teamIDArray[$teamNum] == $teamID) {
			$deleteNum = $teamNumInLeague[$teamNum];
		}
		$teamNum++;
	}
	
	if($deleteNum != 0) {
		for($i=0; $i< $teamNum; $i++) {
			if($teamNumInLeague[$i]> $deleteNum) {
				mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_num_in_league = tournament_team_num_in_league-1 WHERE tournament_team_id = $teamIDArray[$i]") 
					or die('ERROR updating team numbers '.mysql_error());
			}
		}
	} else {
		$container->printError('Error changing team nums in league, current team to deleteID not in teams database');
	}
}

function changeDBLeague($teamID, $newLeagueID, $leagueID) {
	global $tournamentTeamsTable, $tournamentsTable, $container;
	
	fixTeamNumbers($leagueID, $teamID);
	$maxQuery = mysql_query("SELECT MAX(tournament_team_num_in_league) AS maxNum FROM $tournamentTeamsTable 
		INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = $tournamentTeamsTable.tournament_team_tournament_id
		WHERE tournament_team_league_id = $newLeagueID AND tournament_team_tournament_num_running = tournament_num_running") 
		or die($container->printError('ERROR getting new team num in league '.mysql_error()));
	$maxArray = mysql_fetch_array($maxQuery);
	$newNum = $maxArray['maxNum'] + 1;
	
	mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_league_id = $newLeagueID, tournament_team_num_in_league = $newNum WHERE tournament_team_id = $teamID") 
		or die('ERROR changing league '.mysql_error());
	$container->printSuccess('Team league changed successfully');
}

function changeDBTeamWaiting($teamID, $isWaiting, $tourneyObj) {
	global $container, $tournamentTeamsTable, $tournamentsTable;
	
	if($isWaiting == 1) { //team is moved to waiting list
		mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_is_waiting = 1 WHERE tournament_team_id = $teamID");
		$container->printSuccess('Team moved to waiting list');
	} else { //moving to the league
		$tourneyID = $tourneyObj->tourneyID;
		$tourneyNumRunning = $tourneyObj->tourneyNumRunning;
		if($tourneyObj->tourneyIsLeagues == 1) {
			$teamNumFilter = 'tournament_team_num_in_league';
		} else {
			$teamNumFilter = 'tournament_team_num_in_tournament';
		}
	
		$teamQuery = mysql_query("SELECT * FROM $tournamentTeamsTable WHERE tournament_team_id = $teamID");
		$teamArray = mysql_fetch_array($teamQuery);
		$teamLeagueID = $teamArray['tournament_team_league_id'];
	
		$infoQuery = mysql_query("SELECT tournament_is_leagues, tournament_num_teams, COUNT(tournament_team_id) as numTeams FROM $tournamentsTable
			INNER JOIN $tournamentTeamsTable ON $tournamentTeamsTable.tournament_team_tournament_id = $tournamentsTable.tournament_id
			WHERE tournament_id = $tourneyID AND tournament_team_tournament_num_running = $tourneyNumRunning AND tournament_team_num_in_tournament > 0
			AND tournament_team_league_id = $teamLeagueID AND tournament_team_is_waiting = 0") 
			or die($container->printError('ERROR getting league data - '.mysql_error()));
		$data = mysql_fetch_array($infoQuery);
		$numTeamsAllowedArray = explode('%', $data['tournament_num_teams']);
		$numTeamsAllowed = $numTeamsAllowedArray[$teamLeagueID];
		
		if($data['numTeams'] >= $numTeamsAllowed) {
			$container->printError('ERROR, tournament already full, please increase allowed number of teams in league/tournament');
		} else {
			$newTeamNum = $data['numTeams'] + 1;
			mysql_query("UPDATE $tournamentTeamsTable SET tournament_team_is_waiting = 0, $teamNumFilter = $newTeamNum WHERE tournament_team_id = $teamID");
			$container->printSuccess('Team registered/moved to teams entered list');
		}
	}
}


function createPlayer($tourneyID, $leagueID, $teamID) {
	global $tournamentPlayersTable, $container;
	$error = '';
	
	if (($playerFirstName = $_POST['newPlayerFirstName']) == '') {
		$error.='No First Name Specified<br />';
	} else {
		$playerFirstName = mysql_escape_string($playerFirstName);
	}
	if (($playerLastName = $_POST['newPlayerLastName']) == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$playerLastName = mysql_escape_string($playerLastName);
	}
	$playerGender = $_POST['newPlayerGender'];
	if(($playerEmail = $_POST['newPlayerEmail']) != '') {
		if (filter_var( $playerEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$playerEmail = mysql_escape_string($playerEmail);	
		} else {
			$error.='Email not valid<br />';
		}
	}
	$playerNote = mysql_escape_string($_POST['newPlayerNote']);

	if(strlen($error) < 2) {
		$playerNumQuery = mysql_query("SELECT MAX(tournament_player_id) as maxNum FROM $tournamentPlayersTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$playerID = $playerArray['maxNum'] +1;
		
		mysql_query("INSERT INTO $tournamentPlayersTable (tournament_player_id, tournament_player_tournament_id, tournament_player_league_id, tournament_player_team_id, 
			tournament_player_firstname, tournament_player_lastname, tournament_player_email, tournament_player_gender, tournament_player_note) 
			VALUES ($playerID, $tourneyID, $leagueID, $teamID, '$playerFirstName', '$playerLastName', '$playerEmail', '$playerGender', '$playerNote')") 
			or die('ERROR adding new player '.mysql_error());
	} else {
		$container->printError($error);
	}
		
}

function deletePlayers() {
	global $tournamentPlayersTable, $container;
	
	if (isset($_POST['playerCheck'])) {
		$effectedCount = 0;
		foreach($_POST['playerCheck'] as $playerID) {
			if($playerID != 0) {
				mysql_query("DELETE FROM $tournamentPlayersTable WHERE tournament_player_id = $playerID") or die('ERROR deleting players '.mysql_error());
				$effectedCount++;
			}
		}
		$container->printSuccess('Deletions complete, '.$effectedCount.' players effected.');
	} else {
		$container->printError('No players selected to delete');
	}
	
} ?>