<?php 
function getTeamsInfo($sportID, $leagueID) {
	global $prizesTable, $teamsTable, $leaguesTable, $playersTable, $seasonsTable;
	$teamNum = 0;
	if($sportID != 0) {
		$sportFilter = " AND league_sport_id = $sportID";
	} else {
		$sportFilter = '';
	}
	if($leagueID != 0) {
		$leagueFilter = " AND league_id = $leagueID";
	} else {
		$leagueFilter = '';
	}
	
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable 
		INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_is_captain = 1 AND season_available_score_reporter = 1 AND team_num_in_league > 0 $sportFilter $leagueFilter
		ORDER BY league_sport_id ASC, league_day_number ASC, team_name ASC") 
		or die('ERROR getting team data - '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$userID = $teamArray['team_managed_by_user_id'];
		$teamsArray[$userID] = new Team();
		$teamsArray[$userID]->teamID= $teamArray['team_id'];
		$teamsArray[$userID]->teamName = $teamArray['team_name'];
		$teamsArray[$userID]->teamLeagueName = shortenName($teamArray['league_name']).' '.dayString($teamArray['league_day_number']);
		$teamsArray[$userID]->teamSportID = $teamArray['team_sport_id'];
		$teamsArray[$userID]->teamUserID = $teamArray['team_managed_by_user_id'];
		$teamsArray[$userID]->teamCapID = $teamArray['player_id'];
		$teamsArray[$userID]->teamCapName = $teamArray['player_firstname'].' '.$teamArray['player_lastname'];
		$teamsArray[$userID]->teamCapEmail = $teamArray['player_email'];
	}
	return $teamsArray;
}

function getPastWinnerIDs() {
	global $prizesTable, $teamsTable;
	$pastWinnerIDs = array();
	$teamsQuery = mysql_query("SELECT * FROM $prizesTable 
		INNER JOIN $teamsTable ON $teamsTable.team_id = $prizesTable.prize_team_id") 
		or die('ERROR getting past winners - '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
			array_push($pastWinnerIDs, $teamArray['prize_team_user_id']);
	}
	return $pastWinnerIDs;
}

function removePastWinners($pastWinnerIDs, $teamsArray) {
	for($i=0; $i < count($pastWinnerIDs); $i++) {
		if(isset($teamsArray[$pastWinnerIDs[$i]]->teamID)) {
			//print '-'.$teamsArray[$pastWinnerIDs[$i]]->teamName.' '.$teamsArray[$pastWinnerIDs[$i]]->teamUserID.'<br />';
			$teamsArray[$pastWinnerIDs[$i]]->teamID = 0;
		}
	}
}

function getCurrentWinners($sortBy, $timeFrame) {
	global $prizesTable, $teamsTable, $leaguesTable, $playersTable, $seasonsTable;
	$teamNum = 0;
	
	if($sortBy == 'League') {
		$orderBy = 'league_name ASC,';
	} else if ($sortBy == 'Prize') {
		$orderBy = 'prize_description ASC,';
	} else {
		$orderBy = '';
	}
	if($timeFrame != 0) {
		$timeFilter = 'AND prize_time_frame = '.$timeFrame;
		
	} else {
		$timeFilter = 'AND 1=0';
	}
	
	$teamsQuery = mysql_query("SELECT * FROM $prizesTable 
		INNER JOIN $teamsTable ON $teamsTable.team_id = $prizesTable.prize_team_id
		INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_is_captain = 1 AND season_available_score_reporter = 1 AND team_num_in_league > 0 $timeFilter
		ORDER BY $orderBy league_sport_id ASC, league_day_number ASC, team_name ASC") 
		or die('ERROR getting team data - '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$userID = $teamArray['team_managed_by_user_id'];
		$teamsArray[$userID] = new Team();
		$teamsArray[$userID]->teamID= $teamArray['team_id'];
		$teamsArray[$userID]->teamName = $teamArray['team_name'];
		$teamsArray[$userID]->teamLeagueName = shortenName($teamArray['league_name']).' '.dayString($teamArray['league_day_number']);
		$teamsArray[$userID]->teamSportID = $teamArray['team_sport_id'];
		$teamsArray[$userID]->teamUserID = $teamArray['team_managed_by_user_id'];
		$teamsArray[$userID]->teamCapID = $teamArray['player_id'];
		$teamsArray[$userID]->teamCapName = $teamArray['player_firstname'].' '.$teamArray['player_lastname'];
		$teamsArray[$userID]->teamCapEmail = $teamArray['player_email'];
		$teamsArray[$userID]->teamPrizeDD = getPrizeDD($teamsArray[$userID]->teamID);
		$teamsArray[$userID]->teamPrizeString = getPrizeString($teamsArray[$userID]->teamID);
	}
	return $teamsArray;
} 

function moveTeams($prizeTime) {
	global $prizesTable, $teamsTable, $userTable;
	$numTeams = 0;
	if($prizeTime ==0) {
		print 'ERROR - No time frame selected<br />';
		return 0;
	}
	if(isset($_POST['moveTeam'])) {
		foreach($_POST['moveTeam'] as $teamID) {
			$teamQuery = mysql_query("SELECT * FROM $teamsTable 
				INNER JOIN $userTable ON $userTable.user_id = $teamsTable.team_managed_by_user_id WHERE team_id = $teamID") 
				or die('ERROR getting team data - '.$teamID.' - '.mysql_error());
			$teamArray = mysql_fetch_array($teamQuery);
			$playerName = $teamArray['user_firstname'].' '.$teamArray['user_lastname'];
			$playerEmail = $teamArray['user_email'];
			$userID = $teamArray['user_id'];
			mysql_query("INSERT INTO $prizesTable (prize_winner_name, prize_winner_email, prize_show_name, prize_sent, 
				prize_team_user_id, prize_team_id, prize_time_frame) VALUES ('$playerName', '$playerEmail', 1, 0, $userID, 
				$teamID, $prizeTime)") or die('ERROR inserting team into prizes db - '.mysql_error());
			$numTeams++;
		}
		print $numTeams.' team(s) added to prizes database<br />';
	} else {
		print 'No teams selected<br />';
	}
}

function moveWinners() {
	global $prizesTable;
	$numTeams = 0;
	
	if(isset($_POST['moveWinner'])) {
		foreach($_POST['moveWinner'] as $teamID) {
			mysql_query("DELETE FROM $prizesTable WHERE prize_team_id = $teamID")
				or die('ERROR removing winners from prizes db - '.mysql_error());
			$numTeams++;
		}
		print $numTeams.' team(s) removed from prizes database<br />';
	} else {
		print 'No teams selected<br />';
	}
}

function updatePrizes() {
	global $prizesTable;
	for($i = 0; $i < count($_POST['winnerTeamID']); $i++) {
		$prize = mysql_escape_string($_POST['teamPrize'][$i]);
		$teamID = $_POST['winnerTeamID'][$i];
		mysql_query("UPDATE $prizesTable SET prize_description = '$prize' WHERE prize_team_id = $teamID") 
			or die('ERROR updating prizes db - '.mysql_error());
	}
}


function shortenName($name) {
	$searchString = array('Competitive', 'Intermediate', 'Recreational', 'Division');
	$replaceString = array('Comp', 'Inter', 'Rec', '');
	$newName = str_replace($searchString, $replaceString, $name);	
	return $newName;
}



?>