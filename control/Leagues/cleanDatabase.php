<?php /*****************************************
File: cleanDatabase.php
Creator: Derek Dekroon
Created: August 13/2013
Program that deletes score submissions/scheduled matches from past seasons. Those databases tend to get super full and the information is useless after 2 years. This program isn't linked in the control panel. There's a cron on Dave's computer 
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php'); 
$container = new Container('Clean the database');

$curYear = date('Y');

/************* Scheduled Matches ******************/

$scheduledMatchesQuery = mysql_query("SELECT * FROM $scheduledMatchesTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $scheduledMatchesTable.scheduled_match_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY scheduled_match_id ASC LIMIT 1") 
	or die($container->printError('Error getting scheduled matches data - '.mysql_error()));
$scheduledMatchArray = mysql_fetch_array($scheduledMatchesQuery);
$firstID = $scheduledMatchArray['scheduled_match_id'];
	
	
$scheduledMatchesQuery = mysql_query("SELECT * FROM $scheduledMatchesTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $scheduledMatchesTable.scheduled_match_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY scheduled_match_id DESC LIMIT 1") 
	or die($container->printError('Error getting scheduled matches data - '.mysql_error()));
$scheduledMatchArray = mysql_fetch_array($scheduledMatchesQuery);
$lastID = $scheduledMatchArray['scheduled_match_id'];

if($firstID != '' && $lastID != '') {
	$matchQuery = mysql_query("DELETE FROM $scheduledMatchesTable 
		WHERE schedule_match_id >= $firstID && schedule_match_id <= $lastID")
		or die($container->printError('Error deleting scheduled matches - '.mysql_error()));
} else {
	$container->printInfo('No scheduled matches to delete');
}

/************* Spirit Scores ******************/

$spiritQuery = mysql_query("SELECT * FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable 
	ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY spirit_score_id ASC LIMIT 1") 
	or die($container->printError('Error getting spirit score submissions data - '.mysql_error()));
$spiritArray = mysql_fetch_array($spiritQuery);
$firstSpiritID = $spiritArray['spirit_score_id'];

$spiritQuery = mysql_query("SELECT * FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable 
	ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY spirit_score_id DESC LIMIT 1") 
	or die($container->printError('Error getting spirit score submissions data - '.mysql_error()));
$spiritArray = mysql_fetch_array($spiritQuery);
$lastSpiritID = $spiritArray['spirit_score_id'];

if($firstSpiritID != '' && $lastSpiritID != '') {
	$spiritQuery = mysql_query("DELETE FROM $spiritScoresTable 
		WHERE spirit_score_id >= $firstSpiritID && spirit_score_id <= $lastSpiritID")
		or die($container->printError('Error deleting spirit scores - '.mysql_error()));
	$container->printInfo(mysql_affected_rows($spiritQuery).' Spirit score submissions deleted');
} else {
	$container->printInfo('No spirits to delete');
}

/************* Submission Comments ******************/

$commentQuery = mysql_query("SELECT * FROM $scoreCommentsTable INNER JOIN $scoreSubmissionsTable 
	ON $scoreSubmissionsTable.score_submission_id = $scoreCommentsTable.comment_score_submission_id
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY comment_id ASC LIMIT 1") 
	or die($container->printError('Error getting score comments submissions data - '.mysql_error()));
$commentArray = mysql_fetch_array($commentQuery);
$firstCommentID = $commentArray['comment_id'];

$commentQuery = mysql_query("SELECT * FROM $scoreCommentsTable INNER JOIN $scoreSubmissionsTable 
	ON $scoreSubmissionsTable.score_submission_id = $scoreCommentsTable.comment_score_submission_id
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY comment_id DESC LIMIT 1") 
	or die($container->printError('Error getting score comments submissions data - '.mysql_error()));
$commentArray = mysql_fetch_array($commentQuery);
$lastCommentID = $commentArray['comment_id'];

if($firstCommentID != '' && $lastCommentID != '') {
	$matchQuery = mysql_query("DELETE FROM $scoreCommentsTable 
		WHERE comment_id >= $firstCommentID && comment_id <= $lastCommentID")
		or die($container->printError('Error deleting score comments - '.mysql_error()));
	$container->printInfo(mysql_affected_rows($scoreQuery).' Submission comments deleted');
} else {
	$container->printInfo('No score comments to delete');
}

/************* Score Submissions ******************/

$scoreSubmissionQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable 
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY score_submission_id ASC LIMIT 1") 
	or die($container->printError('Error getting score submissions data - '.mysql_error()));
$scheduledMatchArray = mysql_fetch_array($scoreSubmissionQuery);
$firstScoreID = $scheduledMatchArray['score_submission_id'];

$scoreSubmissionQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable 
	INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE season_year = $curYear - 2 ORDER BY score_submission_id DESC LIMIT 1") 
	or die($container->printError('Error getting score submissions data - '.mysql_error()));
$scheduledMatchArray = mysql_fetch_array($scoreSubmissionQuery);
$lastScoreID = $scheduledMatchArray['score_submission_id'];

if($firstScoreID != '' && $lastScoreID != '') {
	$matchQuery = mysql_query("DELETE FROM $scoreSubmissionsTable 
		WHERE score_submission_id >= $firstScoreID && score_submission_id <= $lastScoreID")
		or die($container->printError('Error deleting score submissions - '.mysql_error()));
	$container->printInfo(mysql_affected_rows($scoreQuery).' Score submissions deleted');
} else {
	$container->printInfo('No score submissions to delete');
}

sendEmailsNotSMTP(array('dave@perpetualmotion.org'), 'info@perpetualmotion.org', 'Clean Database Script', 
	'Clean database script was run');
exit(0);