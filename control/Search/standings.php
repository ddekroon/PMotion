<?php date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/includeFiles/teamClass.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/includeFiles/stndFormFunctions.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/includeFiles/stndVariableDeclarations.php');

if($leagueID == 0) {
	print 'No League Specified<br />';
	exit(0);
}

if(isset($_POST['submitStandings'])) {
	global $teamsTable;
	for($i = 0; $i < count($_POST['teamID']); $i++) {
		$teamID = $_POST['teamID'][$i];
		$teamWins = $_POST['teamWins'][$i];
		$teamLosses = $_POST['teamLosses'][$i];
		if(($teamTies = $_POST['teamTies'][$i]) == '') {
			$teamTies = 0;
		}
		if($teamID != '' && $teamWins != '' && $teamLosses != '') {
			mysql_query("UPDATE $teamsTable SET team_wins = $teamWins, team_losses = $teamLosses, team_ties = $teamTies
				WHERE team_id = $teamID") or die('ERROR updating team - '.mysql_error());	
		}
	}
	print 'Standings Updated<br />';
}

function query($query_string){
	$quer_line=mysql_query($query_string);
	$array_line=mysql_fetch_array($quer_line);
	return ($array_line);
}	

function getWeekday($leagueDayNum) {
	if ($leagueDayNum ==1) {
		return 'Monday';
	} else if ($leagueDayNum == 2) {
		return 'Tuesday';
	} else if ($leagueDayNum == 3) {
		return 'Wednesday';
	} else if ($leagueDayNum == 4) {
		return 'Thursday';
	} else if ($leagueDayNum == 5) {
		return 'Friday';
	} else if ($leagueDayNum == 6) {
		return 'Saturday';
	} else if ($leagueDayNum == 0) {
		return 'Sunday';
	}
}

// VARIABLE DECLARATIONS
$leagueArray=query("SELECT league_name, sport_name, league_day_number, league_num_days_spirit_hidden, league_hide_spirit_hour, league_has_ties, league_sort_by_win_pct, 	
	season_available_score_reporter, league_week_in_standings, league_playoff_week, league_season_id, season_name
	FROM $leaguesTable INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id WHERE league_id = $leagueID");
$leagueName = $leagueArray['league_name'];
$sportName = $leagueArray['sport_name'];
$dayNum = $leagueArray['league_day_number'];
$leagueDayNum = $leagueArray['league_day_number'];
$leagueDaysHidden = $leagueArray['league_num_days_spirit_hidden'];
$hideTime = $leagueArray['league_hide_spirit_hour'];
$showTime = $leagueArray['league_show_spirit_hour'];
$leagueHasTies = $leagueArray['league_has_ties'];
$sortByPercent = $leagueArray['league_sort_by_win_pct'];
$seasonAvailable = $leagueArray['season_available_score_reporter'];
$leagueWeekNum = $leagueArray['league_week_in_standings'];
$playoffWeek = $leagueArray['league_playoff_week'];
$seasonID = $leagueArray['league_season_id'];
$seasonName = $leagueArray['season_name'];
$leagueDayString = getWeekday($leagueDayNum % 7);
$leagueShowDayString = getWeekday(($leagueDayNum+2) % 7);
$totalWins = 0;
$totalLosses = 0;
$totalTies = 0;

$teamsQuery=mysql_query("SELECT team_name,team_id,team_wins,team_losses,team_ties,team_most_recent_week_submitted,team_final_position,team_final_spirit_position, team_dropped_out FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0") 
	or die('ERROR getting teams'.mysql_error());
$i=0;
$teamMaxWeek = 0;
while($teamArray = mysql_fetch_array($teamsQuery)){ //goes through the db and selects all of each team's data for display in the standings
	$team[$i] = new Team();
	$team[$i]->teamName = $teamArray['team_name'];
	$team[$i]->teamID = $teamArray['team_id'];
	$team[$i]->teamWins = $teamArray['team_wins'];
	$totalWins += $team[$i]->teamWins;
	$team[$i]->teamLosses = $teamArray['team_losses'];
	$totalLosses += $team[$i]->teamLosses;
	$team[$i]->teamTies = $teamArray['team_ties'];
	$totalTies += $team[$i]->teamTies;
	$team[$i]->teamSubmittedWeek = $teamArray['team_most_recent_week_submitted'];
	$team[$i]->teamDroppedOut = $teamArray['team_dropped_out'];
	$team[$i]->teamPoints = $team[$i]->getPoints();
	$team[$i]->teamFinalPosition = $teamArray['team_final_position'];
	$team[$i]->teamFinalSpirit = $teamArray['team_final_spirit_position'];
	$teamID = $team[$i]->teamID;

	$spiritQuery = mysql_query("SELECT date_week_number,score_submission_opp_team_id,spirit_score_edited_value,spirit_score_value FROM $spiritScoresTable 
		INNER JOIN $scoreSubmissionsTable ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
		INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
		WHERE (score_submission_opp_team_id = $teamID OR score_submission_team_id = $teamID) AND spirit_score_ignored = 0 
		AND (spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0") 
		or die ('ERROR getting spirits '.mysql_error());

	while($spiritArray = mysql_fetch_array($spiritQuery)) {
		if($spiritArray['date_week_number'] < $playoffWeek) {
			if($spiritArray['score_submission_opp_team_id'] == $teamID) {
				$team[$i]->teamSpiritNumbers++;
				$team[$i]->teamSpiritTotal = $team[$i]->addSpirit($spiritArray['spirit_score_edited_value']);
			} else {
				$team[$i]->teamSpiritGivenNumbers++;
				$team[$i]->teamSpiritGivenTotal = $team[$i]->addGivenSpirit($spiritArray['spirit_score_value']);
			}
		}
	}
	$team[$i]->teamSpiritAverage = $team[$i]->getSpiritAverage();
	
	$team[$i]->teamPointsAvailable = $team[$i]->getAvailablePoints();
	$team[$i]->teamWinPercent = $team[$i]->getWinPercent();
	
	$team[$i]->setTitleSize();
	
	if ($team[$i]->teamSubmittedWeek > $teamMaxWeek ) {
		$teamMaxWeek = $team[$i]->teamSubmittedWeek;
	}
	$i++;
}

if($leagueWeekNum < 50) {
	if ($sortByPercent == 0) {
		usort($team, "comparePoints");
	} else {
		usort($team, "comparePercent");
	}
} else {
	usort($team, "comparePosition");
}
$teamCount = $i;

if ($teamMaxWeek > $leagueWeekNum) {
	$leagueWeekNum = $teamMaxWeek;
	mysql_query("UPDATE $leaguesTable SET league_week_in_standings = $leagueWeekNum WHERE league_id = $leagueID") or die('ERROR updating league week '.mysql_error());
}
$dateArray = query("SELECT date_description FROM $datesTable WHERE date_day_number = $leagueDayNum AND date_week_number = $leagueWeekNum AND date_season_id = $seasonID");
$dateDescription = $dateArray['date_description']; ?>

<div class='tableData'>
	<table>
		<?php printLeagueHeader($leagueHasTies, $sortByPercent);
		for($i=0;$i<$teamCount;$i++) {
			//if a teams name is blank then it gets skipped, made so Dave can keep team #'s in the schedule the same if he deletes a team after a week
			if(strlen($team[$i]->teamName) > 1 && $team[$i]->teamDroppedOut != 1) { 
				printLeagueNode($team[$i], $i);
			}
		}
		printTotals($totalWins, $totalLosses, $totalTies, $team);
		printBottomButton(); ?>
	</table>
</div>