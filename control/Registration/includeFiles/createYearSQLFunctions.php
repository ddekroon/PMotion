<?php 
		


function getLeagues($copyYear, $sqlFilter) {
	global $seasonsTable, $leaguesTable, $sqlStrings;
	$pastYearQuery = mysql_query("SELECT * FROM $seasonsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $seasonsTable.season_id WHERE season_year = $copyYear 
		$sqlStrings[$sqlFilter] ORDER BY season_id ASC, league_sport_id ASC, league_day_number ASC") 
		or die('ERROR getting past years - '.mysql_error());
	$leagueNum = 0;
	while($pastYearArray = mysql_fetch_array($pastYearQuery)) {
		$league[$leagueNum] = new League();
		$league[$leagueNum]->seasonObj->seasonID = $pastYearArray['season_id'];
		$league[$leagueNum]->seasonObj->seasonName = $pastYearArray['season_name'];
		$league[$leagueNum]->seasonObj->seasonYear = $pastYearArray['season_year'];
		$league[$leagueNum]->seasonObj->seasonAvailableRegistration = $pastYearArray['season_available_registration'];
		$league[$leagueNum]->seasonObj->seasonAvailableScoreReporter = $pastYearArray['season_available_score_reporter'];
		$league[$leagueNum]->seasonObj->seasonRegistrationOpensDate = $pastYearArray['season_registration_opens_date'];
		$league[$leagueNum]->seasonObj->seasonConfirmationDueBy = $pastYearArray['season_confirmation_due_by'];
		$league[$leagueNum]->seasonObj->seasonRegistrationDueBy = $pastYearArray['season_registration_due_by'];
		$league[$leagueNum]->seasonObj->seasonRegistrationUpUntil = $pastYearArray['season_registration_up_until'];
		$league[$leagueNum]->leagueID = $pastYearArray['league_id'];
		$league[$leagueNum]->leagueName = $pastYearArray['league_name'];
		$league[$leagueNum]->leagueSeasonID = $pastYearArray['league_season_id'];
		$league[$leagueNum]->leagueSportID = $pastYearArray['league_sport_id'];
		$league[$leagueNum]->leagueDayNumber = $pastYearArray['league_day_number'];
		$league[$leagueNum]->leagueRegistrationFee = $pastYearArray['league_registration_fee'];
		$league[$leagueNum]->leagueAskForScores = $pastYearArray['league_ask_for_scores'];
		$league[$leagueNum]->leagueNumOfMatches = $pastYearArray['league_num_of_matches'];
		$league[$leagueNum]->leagueNumOfGamesPerMatch = $pastYearArray['league_num_of_games_per_match'];
		$league[$leagueNum]->leagueHasTies = $pastYearArray['league_has_ties'];
		$league[$leagueNum]->leagueHasPracticeGames = $pastYearArray['league_has_practice_games'];
		$league[$leagueNum]->leagueMaxPointsPerGame = $pastYearArray['league_max_points_per_game'];
		$league[$leagueNum]->leagueShowCancelDefaultOption = $pastYearArray['league_show_cancel_default_option'];
		$league[$leagueNum]->leagueSendLateEmail = $pastYearArray['league_send_late_email'];
		$league[$leagueNum]->leagueHideSpiritHour = $pastYearArray['league_hide_spirit_hour'];
		$league[$leagueNum]->leagueShowSpiritHour = $pastYearArray['league_show_spirit_hour'];
		$league[$leagueNum]->leagueNumDaysSpiritHidden = $pastYearArray['league_num_days_spirit_hidden'];
		$league[$leagueNum]->leagueWeekInScoreReporter = $pastYearArray['league_week_in_score_reporter'];
		$league[$leagueNum]->leagueWeekInStandings = $pastYearArray['league_week_in_standings'];
		$league[$leagueNum]->leagueSortByWinPct = $pastYearArray['league_sort_by_win_pct'];
		$league[$leagueNum]->leagueShowSpirit = $pastYearArray['league_show_spirit'];
		$league[$leagueNum]->leagueAllowsIndividuals = $pastYearArray['league_allows_individuals'];
		$league[$leagueNum]->leagueAvailableForRegistration = $pastYearArray['league_available_for_registration'];
		$league[$leagueNum]->leagueNumTeamsBeforeWaiting = $pastYearArray['league_num_teams_before_waiting'];
		$league[$leagueNum]->leagueMaximumTeams = $pastYearArray['league_maximum_teams'];
		$league[$leagueNum]->leaguePlayoffWeek = $pastYearArray['league_playoff_week'];
		$league[$leagueNum]->leagueIndividualRegistrationFee = $pastYearArray['league_individual_registration_fee'];
		$league[$leagueNum]->leaguePicLink = $pastYearArray['league_pic_link'];
		$league[$leagueNum]->leagueScheduleLink = $pastYearArray['league_schedule_link'];
		$leagueNum++;
	}
	return $league;
}

function createNewYear($oldLeagues, $createYear) {
	global $seasonsTable, $leaguesTable;
	$lastSeasonChecked = 0;
	for($i = 0; $i < count($oldLeagues); $i++) {
		if($oldLeagues[$i]->seasonObj->seasonID != $lastSeasonChecked) {
			$lastSeasonChecked = $oldLeagues[$i]->seasonObj->seasonID;
			$checkArray = checkSeasonExists($oldLeagues[$i]->seasonObj, $createYear);
			$seasonExists = $checkArray[0];
			$newSeasonID = $checkArray[1];
			if($seasonExists == 0) {
				createSeason($newSeasonID, $oldLeagues[$i]->seasonObj, $createYear);
				print 'SeasonID '.$newSeasonID.' Created<br />';
			} else {
				print 'SeasonID '.$newSeasonID.' Already Exists<br />';
			}
			createLeagues($oldLeagues, $i, $newSeasonID, $createYear, '');
		}
	}

} 

function createSummer($oldLeagues, $createYear) {
	global $seasonsTable, $leaguesTable;
	$lastSeasonChecked = 0;
	for($i = 0; $i < count($oldLeagues); $i++) {
		if($oldLeagues[$i]->seasonObj->seasonID != $lastSeasonChecked) {
			$lastSeasonChecked = $oldLeagues[$i]->seasonObj->seasonID;
			$summerObj = new Season();
			$summerObj->seasonName = 'Summer';
			$checkArray = checkSeasonExists($summerObj, $createYear);
			$seasonExists = $checkArray[0];
			$newSeasonID = $checkArray[1];
			if($seasonExists == 0) {
				createSeason($newSeasonID, $oldLeagues[$i]->seasonObj, $createYear);
				print 'SeasonID '.$newSeasonID.' Created<br />';
			} else {
				print 'SeasonID '.$newSeasonID.' Already Exists<br />';
			}
			createLeagues($oldLeagues, $i, $newSeasonID, $createYear, 'Summer');
		}
	}

} 

function checkSeasonExists($seasonObj, $createYear) {
	global $seasonsTable;
	$oldSeasonName = $seasonObj->seasonName;
	$seasonQuery = mysql_query("SELECT * FROM $seasonsTable WHERE season_name LIKE '%$oldSeasonName%' 
		AND season_year LIKE '%$createYear%'") or die('ERROR getting old season - '.mysql_error());
		
	if(mysql_num_rows($seasonQuery) == 0) { //hasn't been created
		$seasonQuery = mysql_query("SELECT MAX(season_id) as maxSeason FROM $seasonsTable") 
			or die('ERROR getting max seasonID - '.mysql_error());
		$seasonArray = mysql_fetch_array($seasonQuery);
		$newSeasonID = $seasonArray['maxSeason'] + 1;
		return array(0, $newSeasonID);
	} else {
		$seasonArray = mysql_fetch_array($seasonQuery);
		return array(1, $seasonArray['season_id']);
	}
}

function createSeason($newSeasonID, $oldSeasonObj, $createYear) {
	global $seasonsTable;
	$seasonName = $oldSeasonObj->seasonName;
	
	$tokens = explode('-', $oldSeasonObj->seasonRegistrationOpensDate);
	$seasonRegistrationOpensDate = date('Y-m-d', mktime(0, 0, 0, $tokens[1], $tokens[2], $tokens[0]+1));
	$tokens = explode('-', $oldSeasonObj->seasonConfirmationDueBy);
	$seasonConfirmationDueBy = date('Y-m-d', mktime(0, 0, 0, $tokens[1], $tokens[2], $tokens[0]+1));
	$tokens = explode('-', $oldSeasonObj->seasonRegistrationDueBy);
	$seasonRegistrationDueBy = date('Y-m-d', mktime(0, 0, 0, $tokens[1], $tokens[2], $tokens[0]+1));
	$tokens = explode('-', $oldSeasonObj->seasonRegistrationUpUntil);
	$seasonRegistrationUpUntil = date('Y-m-d', mktime(0, 0, 0, $tokens[1], $tokens[2], $tokens[0]+1));
	mysql_query("INSERT INTO $seasonsTable (season_id, season_name, season_year, season_available_registration, 
		season_available_score_reporter, season_registration_opens_date, season_confirmation_due_by, season_registration_due_by,
		season_registration_up_until) VALUES ($newSeasonID, '$seasonName', $createYear, 0, 0, '$seasonRegistrationOpensDate', 
		'$seasonConfirmationDueBy', '$seasonRegistrationDueBy', '$seasonRegistrationUpUntil')") 
		or die('ERROR creating new season - '.mysql_error());
}

function createLeagues($oldLeagues, $i, $curSeasonID, $createYear) {
	global $leaguesTable, $seasonsTable, $createLeagueIDs;
	$pastSeasonID = $oldLeagues[$i]->seasonObj->seasonID;
	for($j = $i; $oldLeagues[$j]->seasonObj->seasonID == $pastSeasonID; $j++) {
		if(in_array($oldLeagues[$j]->leagueID, $createLeagueIDs)) {
			$leagueName = mysql_escape_string($oldLeagues[$j]->leagueName);
			$leagueDay = $oldLeagues[$j]->leagueDayNumber;
			$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_name LIKE '%$leagueName%' AND
				league_day_number = $leagueDay AND league_season_id = $curSeasonID") 
				or die('ERROR comparing old data - '.mysql_error());
			if(mysql_num_rows($leagueQuery) > 0) { //league exists
				print 'League '.$oldLeagues[$j]->leagueName.' already exists<br />';
			} else {
				createLeague($oldLeagues[$j], $curSeasonID, $createYear);
				print 'Created league '.$oldLeagues[$j]->leagueName.'<br />';
			}
		}
	}
}

function createLeague($oldLeagueObj, $curSeasonID, $createYear, $newSeasonName) {
	global $leaguesTable;
	$copyYear = $oldLeagueObj->seasonObj->seasonYear;
	$pastSeasonName =  $oldLeagueObj->seasonObj->seasonName;
	$name = mysql_escape_string($oldLeagueObj->leagueName);
	$sportID = $oldLeagueObj->leagueSportID;
	$dayNumber = $oldLeagueObj->leagueDayNumber;
	$registrationFee = $oldLeagueObj->leagueRegistrationFee;
	$askForScores = $oldLeagueObj->leagueAskForScores;
	$numOfMatches = $oldLeagueObj->leagueNumOfMatches;
	$numOfGamesPerMatch = $oldLeagueObj->leagueNumOfGamesPerMatch;
	$hasTies = $oldLeagueObj->leagueHasTies;
	$hasPracticeGames = $oldLeagueObj->leagueHasPracticeGames;
	$maxPointsPerGame = $oldLeagueObj->leagueMaxPointsPerGame;
	$showCancelDefaultOption = $oldLeagueObj->leagueShowCancelDefaultOption;
	$sendLateEmail = $oldLeagueObj->leagueSendLateEmail;
	$hideSpiritHour = $oldLeagueObj->leagueHideSpiritHour;
	$showSpiritHour = $oldLeagueObj->leagueShowSpiritHour;
	$numDaysSpiritHidden = $oldLeagueObj->leagueNumDaysSpiritHidden;
	$sortByWinPct = $oldLeagueObj->leagueSortByWinPct;
	$showSpirit = $oldLeagueObj->leagueShowSpirit;
	$allowIndividuals = $oldLeagueObj->leagueAllowsIndividuals;
	$maxBeforeWaiting = $oldLeagueObj->leagueNumTeamsBeforeWaiting;
	$maxTeams = $oldLeagueObj->leagueMaximumTeams;
	$playoffWeek = $oldLeagueObj->leaguePlayoffWeek;
	$individualRegistrationFee = $oldLeagueObj->leagueIndividualRegistrationFee;
	$picLink = preg_replace("/$copyYear/", "$createYear", $oldLeagueObj->leaguePicLink);
	$scheduleLink = preg_replace("/$copyYear/", "$createYear", $oldLeagueObj->leagueScheduleLink);
	if(strlen($newSeasonName > 1)) {
		$picLink = preg_replace("/$pastSeasonName/", "$newSeasonName", $picLink);
		$scheduleLink = preg_replace("/$pastSeasonName/", "$newSeasonName", $scheduleLink);
	}

	mysql_query("INSERT INTO $leaguesTable (league_name, league_season_id, league_sport_id, league_day_number, 
		league_registration_fee, league_ask_for_scores, league_num_of_matches, league_num_of_games_per_match, league_has_ties, 
		league_has_practice_games, league_max_points_per_game, league_show_cancel_default_option, league_send_late_email, 
		league_hide_spirit_hour, league_show_spirit_hour, league_num_days_spirit_hidden, league_week_in_score_reporter, 
		league_week_in_standings, league_sort_by_win_pct, league_show_spirit, league_allows_individuals, 
		league_available_for_registration, league_num_teams_before_waiting, league_maximum_teams, league_playoff_week, 
		league_individual_registration_fee, league_pic_link, league_schedule_link) VALUES ('$name', $curSeasonID, $sportID, 
		$dayNumber, $registrationFee, $askForScores, $numOfMatches, $numOfGamesPerMatch, $hasTies, $hasPracticeGames, 
		$maxPointsPerGame, $showCancelDefaultOption, $sendLateEmail, $hideSpiritHour, $showSpiritHour, $numDaysSpiritHidden, 
		0, 0, $sortByWinPct, $showSpirit, $allowIndividuals, 0, $maxBeforeWaiting, $maxTeams, $playoffWeek, 
		$individualRegistrationFee, '$picLink', '$scheduleLink')") or die('ERROR creating new season - '.mysql_error());
}?>