<?php

function updateSpiritForTie($team1ID, $team2ID, $leagueID)
{	
	global $container;
	
	// Gets all the teams points, ids, names, and spirits
	$teamOne = getTeamData($leagueID, $team1ID);
	$teamTwo = getTeamData($leagueID, $team2ID);

	// Will stop if the teams are not tied
	if(($teamOne->teamPoints != $teamTwo->teamPoints) || $teamOne->teamSpiritAverage != $teamTwo->teamSpiritAverage)
	{
		$teamsNotTied = 0;
	}
	else
	{
		// adjust the new spirit to add in from old one
		$teamsNotTied = 1;		
		$teamOnePhantom = $teamOne->teamSpiritAverage + 0.01;
		$teamTwoPhantom = $teamTwo->teamSpiritAverage - 0.01;
	}

	// If they are tied submitt new spirit 
	if($teamsNotTied != 0)
	{
		submitNewSpirit($teamOnePhantom, $team1ID, $leagueID);
		submitNewSpirit($teamTwoPhantom, $team2ID, $leagueID);
		$container->printSuccess('Standings Updated!');
	}
	else
	{
		$container->printError('Error - Teams are not tied');	
	}
}

function getTeamData($leagueID, $teamID)
{
	global $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable;
	
	// Database call to get team info
	$teamsQuery=mysql_query("SELECT team_name, team_id, team_wins, team_losses, team_ties, team_most_recent_week_submitted, team_final_position, team_final_spirit_position FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0 AND team_id = $teamID") 
		or die('ERROR getting teams'.mysql_error());

	while($teamArray = mysql_fetch_array($teamsQuery))
	{ 
		$team = new Team();
		$team->teamName = $teamArray['team_name'];
		$team->teamID = $teamArray['team_id'];
		$team->teamWins = $teamArray['team_wins'];
		$team->teamLosses = $teamArray['team_losses'];
		$team->teamTies = $teamArray['team_ties'];
		$team->teamSubmittedWeek = $teamArray['team_most_recent_week_submitted'];
		$team->teamPoints = ($team->teamWins *2) + ($team->teamTies);
		$teamID = $team->teamID;
		
		// Database call to get teams spirit info
		$spiritQuery = mysql_query("SELECT spirit_score_edited_value FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable ON 
			$scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
			INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
			INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_opp_team_id
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
			WHERE team_id = $teamID AND spirit_score_ignored = 0 AND spirit_score_edited_value > 0 
			AND date_week_number < league_playoff_week") 
		or die ('ERROR getting spirits '.mysql_error());
			
		while($spiritArray = mysql_fetch_array($spiritQuery)) 
		{
			$team->teamSpiritNumbers++;
			$team->teamSpiritTotal = $team->addSpirit($spiritArray['spirit_score_edited_value']);
		}
		$team->teamSpiritAverage = $team->getSpiritAverage();
	}
	
	return $team;
}

function submitNewSpirit($phantomScore, $teamID, $leagueID)
{
	global $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable;
	global $container;
	
	if($teamID != 0)
	{	
		// Gets the newest score submission number
		$submissionQuery = mysql_query("SELECT MAX(score_submission_id) as maxSubScore FROM $scoreSubmissionsTable") 
			or die('ERROR getting new score submission number - '.mysql_error());
		
		$submissionArray = mysql_fetch_array($submissionQuery);
		$newScoreSubmissionNum = $submissionArray['maxSubScore'] + 1;
	
		// Gets the newest spirit score ID
		$spiritQuery = mysql_query("SELECT MAX(spirit_score_id) as maxID FROM $spiritScoresTable") 
			or die('ERROR getting new spirit score number - '.mysql_error());
			
		$spiritArray = mysql_fetch_array($spiritQuery);
		$newSpiritIDNum = $spiritArray['maxID'] + 1;
		
		// Adds information into the scoreSubmissionTable
		mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, score_submission_opp_team_id, 
			score_submission_date_id, score_submission_submitter_name, score_submission_result, score_submission_ignored,  score_submission_datestamp,
			score_submission_is_phantom) 
			VALUES ($newScoreSubmissionNum, 0, $teamID, 0, 'ADMIN-TieBreak', 0, 1, NOW(), 1)") 
		or die ('Error with inserting into score submission db - '.mysql_error());
				
		// Adds information into the spiritScoresTable		
		mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, spirit_score_ignored, 
			spirit_score_dont_show, spirit_score_edited_value, spirit_score_is_admin_addition) VALUES ($newScoreSubmissionNum, $phantomScore, 
			0, 1, $phantomScore, 1)") 
		or die('spirit score insert - '.mysql_error());		
		
	}
	// if there was some error (although basically impossible to if you have gotten this far...)
	else 
	{		
		if($teamID == 0)
		{
			$container->printError('Cannot create spirit score - No team selected');
		}
		else if ($phantomScore == 0)
		{
			$container->printError('Cannot create spirit score - Phantom Value = 0');
		}
	}	
}
?>
