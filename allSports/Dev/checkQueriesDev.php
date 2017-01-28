<?php 

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/stndFormFunctions.php');
require_once('includeFiles/stndVariableDeclarations.php');
require_once('includeFiles/stndOtherFunctions.php');

$leagueID=1233;

$leagueObj = query("SELECT * FROM $leaguesTable 
	INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	LEFT JOIN $datesTable ON ($datesTable.date_day_number = league_day_number AND league_season_id = date_season_id AND date_sport_id = league_sport_id
		AND date_week_number = league_week_in_standings)
	WHERE league_id = $leagueID");

global $dbConnection, $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable;
global $teamMaxWeek;

$queryString = "SELECT
	team_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted,
	team_wins * 2 + team_ties as team_points,
	SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
	FROM (SELECT * FROM $teamsTable where team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0) as teamTable
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
	LEFT JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id 
	AND (score_submission_ignored = 0 OR score_submission_is_phantom = 1))
	LEFT JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
	(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
	AND spirit_score_ignored = 0)
	LEFT JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
	GROUP BY team_id";
if(!($teamsQuery = $dbConnection->query($queryString))) { 
	print 'ERROR getting team objects - '.$dbConnection->error;
	exit(0);
} else if($teamsQuery->num_rows == 0) {
	print 'Error, no teams';
	exit(0);
}

?>