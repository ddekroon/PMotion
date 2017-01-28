<?php

function checkCreateSeasons($curYear) {
	global $seasonsTable, $dbConnection, $container;
	$seasonQuery = "SELECT * FROM $seasonsTable WHERE season_year = $curYear";
	if(!($result = $dbConnection->query($seasonQuery)))
		$container->printError('ERROR getting old season count - '.$dbConnection->error);
		
	if($result->num_rows == 0) { //Are no seasons for current year (according to date('Y'))
		$seasonQuery = "SELECT * FROM $seasonsTable WHERE season_year = $curYear - 1"; 
		if(!($result = $dbConnection->query($seasonQuery)))
			$container->printError('ERROR getting old season count - '.$dbConnection->error);
		$seasonNum = 0;
		while($seasonArray = $result->fetch_object()) {
			$seasonObj = getSeasonData($seasonArray->season_id);
			createSeason($seasonObj, $curYear);
			$seasonNum++;
		}
		$container->printSuccess('Seasons for year '.$curYear.' automatically created, '.$seasonNum.' seasons in total');
	}
}

function getLeaguesData($seasonID) {
	global $leaguesTable, $sportsTable;
	$leaguesArray = array();
	$leaguesQuery = mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
		WHERE league_season_id = $seasonID 
		ORDER BY league_sport_id ASC, league_day_number ASC, league_name ASC") or die('ERROR getting leagues - '.mysql_error());
	$leagueNum = 0;
	$lastSportID = 0;
	while($leagueArray = mysql_fetch_array($leaguesQuery)) {
		$sportID = $leagueArray['sport_id'];
		if($sportID != $lastSportID) {
			$lastSportID = $sportID;
			$leagueNum = 0;
		}
		$leaguesArray[$sportID][$leagueNum] = new League();	
		$leaguesArray[$sportID][$leagueNum]->leagueID = $leagueArray['league_id'];
		$leaguesArray[$sportID][$leagueNum]->leagueName = shortenName($leagueArray['league_name']).' '.dayString($leagueArray['league_day_number']);
		$leaguesArray[$sportID][$leagueNum]->leagueCost = $leagueArray['league_registration_fee'];
		$leaguesArray[$sportID][$leagueNum]->leagueSportName = $leagueArray['sport_name'];
		$leaguesArray[$sportID][$leagueNum]->leagueSportID = $leagueArray['league_sport_id'];
		$leaguesArray[$sportID][$leagueNum]->leagueFullTeams = $leagueArray['league_full_teams'];
		$leaguesArray[$sportID][$leagueNum]->leagueFullMales = $leagueArray['league_full_individual_males'];
		$leaguesArray[$sportID][$leagueNum]->leagueFullFemales = $leagueArray['league_full_individual_females'];
		$leagueNum++;
	}
	return $leaguesArray;
}

function shortenName($name) {
	$searchString = array('Competitive', 'Intermediate', 'Recreational', 'Division');
	$replaceString = array('Comp', 'Inter', 'Rec', 'Div');
	$newName = str_replace($searchString, $replaceString, $name);	
	return $newName;
}

function updateData() {
	global $leaguesTable, $seasonsTable, $sportsTable, $seasonID, $dbConnection, $container;
	
	if(($availableReg = $_POST['availableRegistration']) == '') {
		$availableReg = 0;
	}
	if(($availableSR = $_POST['availableScoreReporter']) == '') {
		$availableSR = 0;
	}
	if(($numWeeks = $_POST['numWeeks']) == '') {
		$numWeeks = 0;
	}
	$seasonName = mysql_escape_string($_POST['seasonName']);
	
	$registrationOpensDate = date('Y-m-d', mktime(0, 0, 0, $_POST['monthRegOpen'], $_POST['dayRegOpen'], $_POST['yearRegOpen']));
	if(isset($_POST['monthConfDue'])) {
		$confirmationDueBy = date('Y-m-d', mktime(0, 0, 0, $_POST['monthConfDue'], $_POST['dayConfDue'], $_POST['yearConfDue']));
	} else {
		$confirmationDueBy = '0000-00-00';
	}
	$registrationDueBy = date('Y-m-d', mktime(0, 0, 0, $_POST['monthRegDue'], $_POST['dayRegDue'], $_POST['yearRegDue']));
	$registrationUpUntil = date('Y-m-d', mktime(0, 0, 0, $_POST['monthRegUpUntil'], $_POST['dayRegUpUntil'], $_POST['yearRegUpUntil']));
	$regBySport = $_POST['regBySportOn'];
	
	$seasonString = "UPDATE $seasonsTable SET season_name = '$seasonName', season_available_registration = $availableReg,
		season_num_weeks = $numWeeks, season_available_score_reporter = $availableSR, season_registration_opens_date = 
		'$registrationOpensDate', season_confirmation_due_by = '$confirmationDueBy', 
		season_registration_due_by = '$registrationDueBy', season_registration_up_until = '$registrationUpUntil', 
		season_registration_by_sport = $regBySport WHERE season_id = $seasonID"; 
	//print $seasonString.'<br />';
	mysql_query($seasonString) or die('ERROR updating season - '.mysql_error());

	$insertQuery = "UPDATE $sportsTable SET sport_registration_due_date = ? WHERE sport_id = ?";
	if (!($stmt = $dbConnection->prepare($insertQuery))) echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	if (!$stmt->bind_param("si", $regBySportDate, $i)) echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

	for($i = 1; $i <= count($_POST['monthRegDueSport']); $i++) {
		$regBySportDate = date('Y-m-d', mktime(0, 0, 0, $_POST['monthRegDueSport'][$i], $_POST['dayRegDueSport'][$i], $_POST['yearRegDueSport'][$i]));
		if (!$stmt->execute()) echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$fullTeams = 0;
	$fullMales = 0;
	$fullFemales = 0;
	
	for($i = 0; $i < count($_POST['leagueID']); $i++) {
		$leagueID = $_POST['leagueID'][$i];
		
		if(count($_POST['fullTeams']) > 0) {
			in_array($leagueID, $_POST['fullTeams']) ? $fullTeams = 1 : $fullTeams = 0;
		}
		if(count($_POST['fullMales']) > 0) {
			in_array($leagueID, $_POST['fullMales'])? $fullMales = 1: $fullMales = 0;
		}
		if(count($_POST['fullFemales']) > 0) {
			in_array($leagueID, $_POST['fullFemales'])? $fullFemales = 1: $fullFemales = 0;
		}
		
		mysql_query("UPDATE $leaguesTable SET league_full_teams = $fullTeams, league_full_individual_males = $fullMales, 
			league_full_individual_females = $fullFemales WHERE league_id = $leagueID") 
			or die('ERROR updating league - '.mysql_error());
	}
	
	for($j = 0; $j < count($_POST['pastLeagueID']); $j++) {
		$leagueObj = getLeague($_POST['pastLeagueID'][$j]);
		createLeague($leagueObj, $seasonID);
	}

	$container->printSuccess('Leagues updated, '.$i.' leagues updated, '.$j.' leagues created');
	return 0;	
}

function getSeasonData($seasonID) {
	global $seasonsTable;
	$pastYearQuery = mysql_query("SELECT * FROM $seasonsTable WHERE season_id = $seasonID") 
		or die('ERROR getting past years - '.mysql_error());
	while($seasonArray = mysql_fetch_array($pastYearQuery)) {
		$seasonObj = new Season();
		$seasonObj->seasonID = $seasonArray['season_id'];
		$seasonObj->seasonName = $seasonArray['season_name'];
		$seasonObj->seasonYear = $seasonArray['season_year'];
		$seasonObj->seasonNumWeeks = $seasonArray['season_num_weeks'];
		$seasonObj->seasonAvailableRegistration = $seasonArray['season_available_registration'];
		$seasonObj->seasonAvailableScoreReporter = $seasonArray['season_available_score_reporter'];
		$seasonObj->seasonRegistrationOpensDate = $seasonArray['season_registration_opens_date'];
		$seasonObj->seasonConfirmationDueBy = $seasonArray['season_confirmation_due_by'];
		$seasonObj->seasonRegistrationDueBy = $seasonArray['season_registration_due_by'];
		$seasonObj->seasonRegistrationUpUntil = $seasonArray['season_registration_up_until'];
		$seasonObj->regBySport = $seasonArray['season_registration_by_sport'];
	}
	return $seasonObj;
}

function getSportData($seasonObj) {
	global $sportsTable, $container, $dbConnection;
	
	$sports = array();
	$sportsQuery = "SELECT sport_id, sport_name, sport_registration_due_date FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id";
	if(!($result = $dbConnection->query($sportsQuery))) $container->printError('Error getting sports - '.$dbConnection->error);
	
	while($sport = $result->fetch_object()) {
		$sportID = $sport->sport_id;
		$sports[$sportID]['sportID'] = $sportID;
		$sports[$sportID]['name'] = $sport->sport_name;
		$sports[$sportID]['regDueBy'] = $sport->sport_registration_due_date;
	}
	return $sports;
}

function createSeason($oldSeasonObj, $curYear) {
	global $seasonsTable;
	$seasonQuery = mysql_query("SELECT MAX(season_id) as maxSeason FROM $seasonsTable") 
		or die('ERROR getting max season - '.mysql_error());
	$seasonArray = mysql_fetch_array($seasonQuery);
	$maxSeasonID = $seasonArray['maxSeason'];
	
	$seasonName = mysql_escape_string($oldSeasonObj->seasonName);
	$newSeasonID = $maxSeasonID + 1;
	$numWeeks = $oldSeasonObj->seasonNumWeeks;
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
		season_registration_up_until) VALUES ($newSeasonID, '$seasonName', $curYear, 0, 0, '$seasonRegistrationOpensDate', 
		'$seasonConfirmationDueBy', '$seasonRegistrationDueBy', '$seasonRegistrationUpUntil')") 
		or die('ERROR creating new season - '.mysql_error());
}

function getLeague($pastLeagueID) {
	global $seasonsTable, $leaguesTable, $sqlStrings;
	$pastYearQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $pastLeagueID") 
		or die('ERROR getting past years - '.mysql_error());
	while($pastLeagueArray = mysql_fetch_array($pastYearQuery)) {
		$league = new League();
		$league->leagueID = $pastLeagueArray['league_id'];
		$league->leagueName = $pastLeagueArray['league_name'];
		$league->leagueSeasonID = $pastLeagueArray['league_season_id'];
		$league->leagueSportID = $pastLeagueArray['league_sport_id'];
		$league->leagueDayNumber = $pastLeagueArray['league_day_number'];
		$league->leagueRegistrationFee = $pastLeagueArray['league_registration_fee'];
		$league->leagueAskForScores = $pastLeagueArray['league_ask_for_scores'];
		$league->leagueNumOfMatches = $pastLeagueArray['league_num_of_matches'];
		$league->leagueNumOfGamesPerMatch = $pastLeagueArray['league_num_of_games_per_match'];
		$league->leagueHasTies = $pastLeagueArray['league_has_ties'];
		$league->leagueHasPracticeGames = $pastLeagueArray['league_has_practice_games'];
		$league->leagueMaxPointsPerGame = $pastLeagueArray['league_max_points_per_game'];
		$league->leagueShowCancelDefaultOption = $pastLeagueArray['league_show_cancel_default_option'];
		$league->leagueSendLateEmail = $pastLeagueArray['league_send_late_email'];
		$league->leagueHideSpiritHour = $pastLeagueArray['league_hide_spirit_hour'];
		$league->leagueShowSpiritHour = $pastLeagueArray['league_show_spirit_hour'];
		$league->leagueNumDaysSpiritHidden = $pastLeagueArray['league_num_days_spirit_hidden'];
		$league->leagueWeekInScoreReporter = $pastLeagueArray['league_week_in_score_reporter'];
		$league->leagueWeekInStandings = $pastLeagueArray['league_week_in_standings'];
		$league->leagueSortByWinPct = $pastLeagueArray['league_sort_by_win_pct'];
		$league->leagueShowSpirit = $pastLeagueArray['league_show_spirit'];
		$league->leagueAllowsIndividuals = $pastLeagueArray['league_allows_individuals'];
		$league->leagueAvailableForRegistration = $pastLeagueArray['league_available_for_registration'];
		$league->leagueNumTeamsBeforeWaiting = $pastLeagueArray['league_num_teams_before_waiting'];
		$league->leagueMaximumTeams = $pastLeagueArray['league_maximum_teams'];
		$league->leaguePlayoffWeek = $pastLeagueArray['league_playoff_week'];
		$league->leagueIndividualRegistrationFee = $pastLeagueArray['league_individual_registration_fee'];
		$league->leaguePicLink = $pastLeagueArray['league_pic_link'];
		$league->leagueScheduleLink = $pastLeagueArray['league_schedule_link'];
	}
	return $league;
}

function createLeague($oldLeagueObj, $curSeasonID) {
	global $leaguesTable, $pastSeasonYear, $curYear;
	
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
	$picLink = preg_replace("/$pastSeasonYear/", "$curYear", $oldLeagueObj->leaguePicLink);
	$scheduleLink = preg_replace("/$pastSeasonYear/", "$curYear", $oldLeagueObj->leagueScheduleLink);
	$leagueFullMales = 0;
	$leagueFullFemales = 0;
	$leagueFullTeams = 0;

	$insertString = "INSERT INTO $leaguesTable (league_name, league_season_id, league_sport_id, league_day_number, 
		league_registration_fee, league_ask_for_scores, league_num_of_matches, league_num_of_games_per_match, league_has_ties, 
		league_has_practice_games, league_max_points_per_game, league_show_cancel_default_option, league_send_late_email, 
		league_hide_spirit_hour, league_show_spirit_hour, league_num_days_spirit_hidden, league_week_in_score_reporter, 
		league_week_in_standings, league_sort_by_win_pct, league_show_spirit, league_allows_individuals, 
		league_available_for_registration, league_num_teams_before_waiting, league_maximum_teams, league_playoff_week, 
		league_individual_registration_fee, league_pic_link, league_schedule_link, league_full_teams, league_full_individual_males, 
		league_full_individual_females) VALUES ('$name', $curSeasonID, $sportID, 
		$dayNumber, $registrationFee, $askForScores, $numOfMatches, $numOfGamesPerMatch, $hasTies, $hasPracticeGames, 
		$maxPointsPerGame, $showCancelDefaultOption, $sendLateEmail, $hideSpiritHour, $showSpiritHour, $numDaysSpiritHidden, 
		0, 0, $sortByWinPct, $showSpirit, $allowIndividuals, 0, $maxBeforeWaiting, $maxTeams, $playoffWeek, 
		$individualRegistrationFee, '$picLink', '$scheduleLink', $leagueFullTeams, $leagueFullMales, $leagueFullFemales)";
	//print $insertString.'<br />';
	mysql_query($insertString) or die('ERROR creating new season - '.mysql_error());
}



