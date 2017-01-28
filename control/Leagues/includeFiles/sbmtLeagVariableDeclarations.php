<?php 
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
			stripslashes($value);
        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

function loadDefaults($sportID, $seasonID) {
	global $sportsTable, $seasonsTable, $askForScores, $numMatches, $numGames, $hasTies, $maxPoints, $regAvailable;
	global $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $allowIndividuals, $playoffWeek, $picLink, $sortByPercent;
	
	if ($sportID != 0 && $seasonID != 0) {
		$sportsQuery = mysql_query("SELECT * FROM $sportsTable WHERE sport_id = $sportID");
		$sportsArray = mysql_fetch_array($sportsQuery);
		$seasonsQuery = mysql_query("SELECT * FROM $seasonsTable WHERE season_id = $seasonID");
		$seasonsArray = mysql_fetch_array($seasonsQuery);
		
		$askForScores = $sportsArray['sport_default_ask_for_scores'];
		$numMatches = $sportsArray['sport_default_num_of_matches'];
		$numGames = $sportsArray['sport_default_num_games_per_match'];
		$hasTies = $sportsArray['sport_default_has_ties'];
		$maxPoints = $sportsArray['sport_default_max_points_per_game'];
		$sortByPercent = $sportsArray['sport_default_sort_by_pct'];
		$seasonNameArray = explode(' ', $seasonsArray['season_name']);
		
		if($seasonNameArray[1] == 'I' || $seasonNameArray[1] == 'II') {
			$picLinkSeasonName = $seasonNameArray[0].$seasonNameArray[1].'Teams';
		} else {
			$picLinkSeasonName = $seasonNameArray[0].'Teams';
		}
		$picLink = $sportsArray['sport_default_pic_link'].$seasonsArray['season_year'].'/'.$picLinkSeasonName;
		$daysSpiritHidden = 2;
		$hideSpiritHour = 18;
		$showSpiritHour = 15;
		$regAvailable = 1;
		$allowIndividuals = 1;
		$playoffWeek = 8;
	}
}

function getSubmittedData() {
	global $leagueName, $dayNumber, $regFee, $numMatches, $numGames, $hasTies, $hasPractices, $maxPoints, $curWeek;
	global $allowIndividuals, $regAvailable, $teamsBeforeWaiting, $maxTeams, $askForScores, $sortByPercent;
	global $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $playoffWeek, $individualFee, $picLink, $teamsFull, $malesFull;
	global $femalesFull, $showStatic;
	
	$leagueName = $_POST['leagueName'];
	$dayNumber = $_POST['dayNumber'];
	if(($regFee = $_POST['regFee']) == '') {
		$regFee = 0;
	}
	$numMatches = $_POST['numMatches'];
	$numGames = $_POST['numGames'];
	$hasTies = $_POST['hasTies'];
	$hasPractices = $_POST['hasPractices'];
	$maxPoints = $_POST['maxPoints'];
	$curWeek = $_POST['curWeek'];
	$allowIndividuals = $_POST['allowIndividuals'];
	$regAvailable = $_POST['regAvailable'];
	$teamsBeforeWaiting = $_POST['teamsBeforeWaiting'];
	$maxTeams = $_POST['maxTeams'];
	$askForScores = $_POST['askForScores'];
	$sortByPercent = $_POST['sortByPercent'];
	$daysSpiritHidden = $_POST['daysSpiritHidden'];
	$hideSpiritHour = $_POST['hideSpiritHour'];
	$showSpiritHour = $_POST['showSpiritHour'];
	$playoffWeek = $_POST['playoffWeek'];
	if(($individualFee = $_POST['individualFee']) == '') {
		$individualFee = 0;
	}
	if(($teamsFull = $_POST['teamsFull']) == '') {
		$teamsFull = 0;
	}
	if(($femalesFull = $_POST['femalesFull']) == '') {
		$femalesFull = 0;
	}
	if(($malesFull = $_POST['malesFull']) == '') {
		$malesFull = 0;
	}
	$picLink = $_POST['leaguePicLink'];
	if(($showStatic = $_POST['showStatic']) == '') {
		$showStatic = 0;
	}
}

function loadDatabaseValues($leagueID) {
	global $leagueName, $dayNumber, $regFee, $numMatches, $numGames, $hasTies, $hasPractices, $maxPoints, $curWeek;
	global $allowIndividuals, $regAvailable, $teamsBeforeWaiting, $maxTeams, $askForScores, $leaguesTable, $sortByPercent;
	global $sportID, $seasonID, $daysSpiritHidden, $hideSpiritHour, $showSpiritHour, $individualFee, $picLink, $playoffWeek;
	global $teamsFull, $malesFull, $femalesFull, $showStatic;
	
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
	$leagueArray = mysql_fetch_array($leagueQuery);
	$sportID = $leagueArray['league_sport_id'];
	$seasonID = $leagueArray['league_season_id'];
	$leagueName = $leagueArray['league_name'];
	$dayNumber = $leagueArray['league_day_number'];
	$regFee = $leagueArray['league_registration_fee'];
	$numMatches = $leagueArray['league_num_of_matches'];
	$numGames = $leagueArray['league_num_of_games_per_match'];
	$hasTies = $leagueArray['league_has_ties'];
	$hasPractices = $leagueArray['league_has_practice_games'];
	$maxPoints = $leagueArray['league_max_points_per_game'];
	$curWeek = $leagueArray['league_week_in_score_reporter'];
	$allowIndividuals = $leagueArray['league_allows_individuals'];
	$regAvailable = $leagueArray['league_available_for_registration'];
	$teamsBeforeWaiting = $leagueArray['league_num_teams_before_waiting'];
	$maxTeams = $leagueArray['league_maximum_teams'];
	$askForScores = $leagueArray['league_ask_for_scores'];
	$sortByPercent = $leagueArray['league_sort_by_win_pct'];
	$daysSpiritHidden = $leagueArray['league_num_days_spirit_hidden'];
	$hideSpiritHour = $leagueArray['league_hide_spirit_hour'];
	$showSpiritHour = $leagueArray['league_show_spirit_hour'];
	$individualFee = $leagueArray['league_individual_registration_fee'];
	$picLink = $leagueArray['league_pic_link'];
	$teamsFull = $leagueArray['league_full_teams'];
	$malesFull = $leagueArray['league_full_individual_males'];
	$femalesFull = $leagueArray['league_full_individual_females'];
	$playoffWeek = $leagueArray['league_playoff_week'];
	$showStatic = $leagueArray['league_show_static_schedule'];
}