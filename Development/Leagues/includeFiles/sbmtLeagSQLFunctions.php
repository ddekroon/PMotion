<?php

function createLeague($sportID, $seasonID) {
	global $leagueName, $dayNumber, $regFee, $numMatches, $numGames, $hasTies, $hasPractices, $maxPoints, $curWeek;
	global $allowIndividuals, $regAvailable, $teamsBeforeWaiting, $maxTeams, $askForScores, $leaguesTable, $sortByPercent;
	global $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $playoffWeek, $individualFee, $picLink, $malesFull, $femalesFull;
	global $teamsFull, $showStatic;

	$leagueQuery = mysql_query("SELECT MAX(league_id) as maxnum FROM $leaguesTable");
	$leagueArray = mysql_fetch_array($leagueQuery);
	$leagueID = $leagueArray['maxnum'] + 1;
	
	$escapedLeagueName = mysql_real_escape_string($leagueName);
	
	mysql_query("INSERT INTO $leaguesTable (league_id, league_name, league_season_id, league_sport_id, league_day_number,
		league_registration_fee, league_ask_for_scores, league_num_of_matches, league_num_of_games_per_match, league_has_ties, 
		league_has_practice_games, league_max_points_per_game, league_show_cancel_default_option, league_send_late_email, 
		league_week_in_score_reporter, league_week_in_standings, league_sort_by_win_pct, league_allows_individuals, 
		league_available_for_registration, league_num_teams_before_waiting, league_maximum_teams, league_num_days_spirit_hidden, 
		league_show_spirit_hour, league_hide_spirit_hour, league_playoff_week, league_pic_link, 
		league_individual_registration_fee, league_full_teams, league_full_individual_females, league_full_individual_males, league_show_static_schedule) 
		VALUES ($leagueID, '$escapedLeagueName', $seasonID, $sportID, $dayNumber, $regFee, $askForScores, $numMatches, $numGames, $hasTies,
		$hasPractices, $maxPoints, 0, 1, $curWeek, $curWeek, $sortByPercent, $allowIndividuals, $regAvailable, 
		$teamsBeforeWaiting, $maxTeams, $daysSpiritHidden, $showSpiritHour, $hideSpiritHour, $playoffWeek, '$picLink', 
		$individualFee, $teamsFull, $femalesFull, $malesFull, $showStatic)") or die ('Error creating new league - '.mysql_error());
	
	return $leagueID;	
}

function updateLeague($sportID, $seasonID, $leagueID) {
	global $leagueName, $dayNumber, $regFee, $numMatches, $numGames, $hasTies, $hasPractices, $maxPoints, $curWeek;
	global $allowIndividuals, $regAvailable, $teamsBeforeWaiting, $maxTeams, $askForScores, $leaguesTable, $sortByPercent;
	global $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $playoffWeek, $individualFee, $picLink, $malesFull, $femalesFull;
	global $teamsFull, $showStatic;
	
	$escapedLeagueName = mysql_real_escape_string($leagueName);
	
	mysql_query("UPDATE $leaguesTable SET league_id = $leagueID, league_name = '$escapedLeagueName', league_season_id= $seasonID,
		league_sport_id = $sportID, league_day_number = $dayNumber, league_registration_fee = $regFee, 
		league_ask_for_scores = $askForScores, league_num_of_matches = $numMatches, league_num_of_games_per_match = $numGames, 
		league_has_ties = $hasTies, league_has_practice_games = $hasPractices, league_max_points_per_game = $maxPoints,
		league_show_cancel_default_option = 0, league_send_late_email = 1 , league_week_in_score_reporter = $curWeek, 
		league_week_in_standings = $curWeek, league_sort_by_win_pct = $sortByPercent, 
		league_allows_individuals = $allowIndividuals, league_available_for_registration = $regAvailable,
		league_num_teams_before_waiting = $teamsBeforeWaiting, league_maximum_teams = $maxTeams, 
		league_num_days_spirit_hidden = $daysSpiritHidden, league_show_spirit_hour = $showSpiritHour, 
		league_hide_spirit_hour = $hideSpiritHour, league_playoff_week = $playoffWeek, 
		league_individual_registration_fee = $individualFee, league_full_teams = $teamsFull, league_show_static_schedule = $showStatic,
		league_full_individual_males = $malesFull, league_full_individual_females = $femalesFull, league_pic_link = '$picLink'	
		WHERE league_id = $leagueID") or die ('Error updating league - '.mysql_error());
}

//checks if the current team being submitted 
function checkForUpdate($sportID, $seasonID) {
	global $leagueName, $leaguesTable, $dayNumber;
	$leagueNameString = mysql_escape_string($leagueName);
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_sport_id = $sportID AND league_season_id = $seasonID AND league_name = '$leagueNameString' 
		AND league_day_number = $dayNumber") or die('ERROR checking if this is an update - '.mysql_error());
	if($leagueArray = mysql_fetch_array($leagueQuery)) {
		return $leagueArray['league_id'];
	} else {
		return 0;
	}
}

function deleteLeague($leagueID) {
	global $leaguesTable;
	mysql_query("UPDATE $leaguesTable SET league_season_id = 0 WHERE league_id = $leagueID") or die('ERROR deleting league - '.mysql_error());
	print 'League '.$leagueID.' deleted<br />';
}
