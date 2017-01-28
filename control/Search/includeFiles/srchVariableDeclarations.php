<?php function getTeamsData($searchString, $isActive) {
	global $teamsTable, $team, $playersTable, $leaguesTable, $userTable, $seasonsTable;
	$teamIDArray = array();
	
	if($isActive == 1 ) { //active search
		$timeFilter = 'AND (season_available_score_reporter = 1 OR season_available_registration = 1)';
	} else { //archived search
		$timeFilter = 'AND season_available_score_reporter = 0 AND season_available_registration = 0';
	}
	$toReplace = array('Recreational', 'Intermediate', 'Competitive');
	$replaceWith = array('Rec', 'Inter', 'Comp');
	$searchString = mysql_escape_string($searchString);
	
	$teamsQuery = mysql_query("SELECT DISTINCT team_id, team_name, league_id, league_sport_id, league_name, league_day_number, season_name, season_year, user_username, team_id
		FROM $teamsTable 
		INNER JOIN $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
		INNER JOIN $userTable ON $userTable.user_id = $teamsTable.team_managed_by_user_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE (user_username LIKE '$searchString' OR team_name LIKE '$searchString') AND team_num_in_league > 0  
		$timeFilter ORDER BY team_name") or die($container->printError('ERROR getting teams '.mysql_error()));
		$teamNum = 0;
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$captainQuery = mysql_query("SELECT player_firstname, player_lastname, 
		player_email, player_phone FROM $playersTable WHERE player_team_id = ".$teamArray['team_id']." 
			AND player_is_captain = 1") or die('ERROR getting cptn - '.mysql_error());
		$captainArray = mysql_fetch_array($captainQuery);
		$team[$teamNum] = new Team();	
		$team[$teamNum]->teamID = $teamArray['team_id'];
		$team[$teamNum]->teamName = $teamArray['team_name'];
		$team[$teamNum]->teamLeagueID = $teamArray['league_id'];
		$team[$teamNum]->teamSportID = $teamArray['league_sport_id'];
		$leagueName = str_replace($toReplace, $replaceWith, $teamArray['league_name']);
		$team[$teamNum]->teamLeagueName = $leagueName.' - '.dayString($teamArray['league_day_number'])
			.' - '.$teamArray['season_name'].' '.substr($teamArray['season_year'], 2, 2);
		$team[$teamNum]->teamCaptainName = $captainArray['player_firstname'].' '.$captainArray['player_lastname'];
		$team[$teamNum]->teamCaptainEmail = $captainArray['player_email'];
		$team[$teamNum]->teamCaptainPhoneNum = $captainArray['player_phone'];
		$team[$teamNum]->teamUserName = $teamArray['user_username'];
		array_push($teamIDArray, $teamArray['team_id']);
		$teamNum++;
	}
	$teamCaptainQuery = mysql_query("SELECT team_id, team_name, league_id, league_sport_id, league_name, league_day_number, season_name, season_year, user_username, team_id, player_firstname, 
		player_lastname, player_email, player_phone  FROM $teamsTable 
		INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id 
		INNER JOIN $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
		INNER JOIN $userTable ON $userTable.user_id = $teamsTable.team_managed_by_user_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE (CONCAT(player_firstname,' ', player_lastname) LIKE '$searchString' 
		OR player_email LIKE '$searchString') $timeFilter AND player_is_captain = 1 AND team_num_in_league > 0 ") 
		or die($container->printError('ERROR gettin teams with captains matching - '.mysql_error()));
	while($teamArray = mysql_fetch_array($teamCaptainQuery)) {	
		if(!in_array($teamArray['team_id'], $teamIDArray)) {
			$team[$teamNum] = new Team();	
			$team[$teamNum]->teamID = $teamArray['team_id'];
			$team[$teamNum]->teamName = $teamArray['team_name'];
			$team[$teamNum]->teamLeagueID = $teamArray['league_id'];
			$team[$teamNum]->teamSportID = $teamArray['league_sport_id'];
			$leagueName = str_replace($toReplace, $replaceWith, $teamArray['league_name']);
			$team[$teamNum]->teamLeagueName = $leagueName.' - '.dayString($teamArray['league_day_number'])
			.' - '.$teamArray['season_name'].' '.substr($teamArray['season_year'], 2, 2);
			$team[$teamNum]->teamCaptainName = $teamArray['player_firstname'].' '.$teamArray['player_lastname'];
			$team[$teamNum]->teamCaptainEmail = $teamArray['player_email'];
			$team[$teamNum]->teamCaptainPhoneNum = $teamArray['player_phone'];
			$team[$teamNum]->teamUserName = $teamArray['user_username'];
			$teamNum++;
		}
	}

	return $teamNum;
}

function getPlayersData($searchString, $isActive) {
	global $teamsTable, $player, $leaguesTable, $userTable, $seasonsTable, $playersTable, $container;
	
	if($isActive == 1 ) { //active search
		$timeFilter = 'AND (season_available_score_reporter = 1 OR season_available_registration = 1)';
	} else { //archived search
		$timeFilter = 'AND season_available_score_reporter = 0 AND season_available_registration = 0';
	}
	$toReplace = array('Recreational', 'Intermediate', 'Competitive');
	$replaceWith = array('Rec', 'Inter', 'Comp');
	$searchString = mysql_escape_string($searchString);
	
	$playersQuery = mysql_query("SELECT DISTINCT player_id, player_firstname, player_lastname, team_name, league_name, league_day_number, season_name, player_email, team_id, league_id, league_sport_id FROM $playersTable 
		INNER JOIN $teamsTable ON $playersTable.player_team_id = $teamsTable.team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE (team_name LIKE '$searchString' OR CONCAT(player_firstname,' ',player_lastname) 
		LIKE '$searchString' OR player_email LIKE '$searchString') AND team_num_in_league > 0  
		$timeFilter ORDER BY player_lastname") or die($container->printError('ERROR getting players '.mysql_error()));
	$playerNum = 0;
	while($playerArray = mysql_fetch_array($playersQuery)) {
		$player[$playerNum] = new Player();	
		$player[$playerNum]->playerID = $playerArray['player_id'];
		$player[$playerNum]->playerFirstName = $playerArray['player_firstname'];
		$player[$playerNum]->playerLastName = $playerArray['player_lastname'];
		$player[$playerNum]->playerTeamName = $playerArray['team_name'];
		$leagueName = str_replace($toReplace, $replaceWith, $playerArray['league_name']);
		$player[$playerNum]->playerLeagueName = $leagueName.' - '.dayString($playerArray['league_day_number'])
			.' - '.$playerArray['season_name'];
		$player[$playerNum]->playerEmail = $playerArray['player_email'];
		$player[$playerNum]->playerTeamID = $playerArray['team_id'];
		$player[$playerNum]->playerLeagueID = $playerArray['league_id'];
		$player[$playerNum]->playerSportID = $playerArray['league_sport_id'];
		$playerNum++;
	}

	return $playerNum;
}

function getUserData($userEmail) {
	global $userTable;
	$userNum = 0;
	
	$userQuery = mysql_query("SELECT user_id, user_firstname, user_lastname, user_email, user_username FROM $userTable WHERE user_email LIKE '$userEmail'") 
		or die('ERROR getting user - '.mysql_error());
	while($userArray = mysql_fetch_array($userQuery)) {
		$user[$userNum] = new Player();
		$user[$userNum]->playerID = $userArray['user_id'];
		$user[$userNum]->playerFirstName = $userArray['user_firstname'];
		$user[$userNum]->playerLastName = $userArray['user_lastname'];
		$user[$userNum]->playerEmail = $userArray['user_email'];
		$user[$userNum]->playerUserName = $userArray['user_username'];
		$userNum++;
	}
	return $user;
}

function formatPhoneNumber($strPhone) {
	$strPhone = ereg_replace("[^0-9]",'', $strPhone);
	if (strlen($strPhone) != 10) {
		return $strPhone;
	}

	$strArea = substr($strPhone, 0, 3);
	$strPrefix = substr($strPhone, 3, 3);
	$strNumber = substr($strPhone, 6, 4);

	$strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;

	return ($strPhone);
}