<?php function createNewLeague($leagueID, $newName) {
	global $leaguesTable;
	
	$maxLeagueQuery = mysql_query("SELECT MAX(league_id) as maxLeague FROM $leaguesTable") 
		or die('ERROR getting max - '.mysql_error());
	$maxLeagueArray = mysql_fetch_array($maxLeagueQuery);
	$newLeagueID = $maxLeagueArray['maxLeague'] + 1;
	$name = mysql_escape_string($newName);
	
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID") 
		or die('ERROR getting past years - '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	
	$seasonID = $leagueArray['league_season_id'];
	$sportID = $leagueArray['league_sport_id'];
	$dayNumber = $leagueArray['league_day_number'];
	$registrationFee = $leagueArray['league_registration_fee'];
	$askForScores = $leagueArray['league_ask_for_scores'];
	$numOfMatches = $leagueArray['league_num_of_matches'];
	$numOfGamesPerMatch = $leagueArray['league_num_of_games_per_match'];
	$hasTies = $leagueArray['league_has_ties'];
	$hasPracticeGames = $leagueArray['league_has_practice_games'];
	$maxPointsPerGame = $leagueArray['league_max_points_per_game'];
	$showCancelDefaultOption = $leagueArray['league_show_cancel_default_option'];
	$sendLateEmail = $leagueArray['league_send_late_email'];
	$hideSpiritHour = $leagueArray['league_hide_spirit_hour'];
	$showSpiritHour = $leagueArray['league_show_spirit_hour'];
	$numDaysSpiritHidden = $leagueArray['league_num_days_spirit_hidden'];
	$weekInScoreReporter = $leagueArray['league_week_in_score_reporter'];
	$weekInStandings = $leagueArray['league_week_in_standings'];
	$sortByWinPct = $leagueArray['league_sort_by_win_pct'];
	$showSpirit = $leagueArray['league_show_spirit'];
	$allowIndividuals = $leagueArray['league_allows_individuals'];
	$availableForRegistration = $leagueArray['league_available_for_registration'];
	$maxBeforeWaiting = $leagueArray['league_num_teams_before_waiting'];
	$maxTeams = $leagueArray['league_maximum_teams'];
	$playoffWeek = $leagueArray['league_playoff_week'];
	$individualRegistrationFee = $leagueArray['league_individual_registration_fee'];
	$picLink = $leagueArray['league_pic_link'];
	$scheduleLink = $leagueArray['league_schedule_link'];
	if($weekInScoreReporter <= 1) {
		$isSplit = 0;
		$splitWeek = 0;
	} else {
		$isSplit = 1;
		$splitWeek = $weekInScoreReporter;
	}
	
	mysql_query("INSERT INTO $leaguesTable (league_id, league_name, league_season_id, league_sport_id, league_day_number, 
		league_registration_fee, league_ask_for_scores, league_num_of_matches, league_num_of_games_per_match, league_has_ties, 
		league_has_practice_games, league_max_points_per_game, league_show_cancel_default_option, league_send_late_email, 
		league_hide_spirit_hour, league_show_spirit_hour, league_num_days_spirit_hidden, league_week_in_score_reporter, 
		league_week_in_standings, league_sort_by_win_pct, league_show_spirit, league_allows_individuals, 
		league_available_for_registration, league_num_teams_before_waiting, league_maximum_teams, league_playoff_week, 
		league_individual_registration_fee, league_pic_link, league_schedule_link, league_is_split, league_split_week) 
		VALUES ($newLeagueID, '$name', $seasonID, 
		$sportID, $dayNumber, $registrationFee, $askForScores, $numOfMatches, $numOfGamesPerMatch, $hasTies, $hasPracticeGames, 
		$maxPointsPerGame, $showCancelDefaultOption, $sendLateEmail, $hideSpiritHour, $showSpiritHour, $numDaysSpiritHidden, 
		$weekInScoreReporter, $weekInStandings, $sortByWinPct, $showSpirit, $allowIndividuals, 0, $maxBeforeWaiting, $maxTeams, 
		$playoffWeek, $individualRegistrationFee, '$picLink', '$scheduleLink', $isSplit, $splitWeek)") 
		or die('ERROR creating new league - '.mysql_error());
		
	return $newLeagueID;
}

function setLeagueValues($leagueID, $leagueIDOne, $leagueIDTwo) {
	global $leaguesTable;
	mysql_query("UPDATE $leaguesTable SET league_season_id = 0 WHERE league_id = $leagueID") 
		or die('ERROR changing old leagues season - '.mysql_error());
}