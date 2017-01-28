<?php 

function updateStandings($teamID, $gameResults) {
	global $teamsTable;

	if ($gameResults == 1) {
		mysql_query("UPDATE $teamsTable SET team_wins = team_wins-1 WHERE team_id = $teamID");
	} else if ($gameResults == 2) {
		mysql_query("UPDATE $teamsTable SET team_losses = team_losses-1 WHERE team_id = $teamID");
	} else if ($gameResults == 3) {
		mysql_query("UPDATE $teamsTable SET team_ties = team_ties-1 WHERE team_id = $teamID");
	}
		
}

function deleteScoreSubmissions($teamID, $leagueID) {
    global $scoreSubmissionsTable, $spiritScoresTable, $leaguesTable, $teamsTable;
	$leagueQuery = mysql_query("SELECT league_num_of_games_per_match FROM $leaguesTable WHERE league_id = $leagueID")
		or die('ERROR gettin league data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	$numGames = $leagueArray['league_num_of_games_per_match'];
	
	$delSubmissions = $_POST['deleteSubmissionID'];
    if(isset($_POST['deleteSubmission'])) {
        foreach($_POST['deleteSubmission'] as $deleteNum) {
			for($i = 0; $i < $numGames; $i++) {
           		$deleteID = $delSubmissions[$deleteNum + $i];
				$getResult = mysql_query("SELECT score_submission_result FROM $scoreSubmissionsTable WHERE score_submission_id = $deleteID");
				$result = mysql_fetch_array($getResult);

				updateStandings($teamID,$result['score_submission_result']);
				
				mysql_query("DELETE FROM $scoreSubmissionsTable WHERE score_submission_id = $deleteID") 
                	or die('ERROR deleting score submission' + mysql_error());
            	mysql_query("DELETE FROM $spiritScoresTable WHERE spirit_score_score_submission_id = $deleteID") 
               		or die('ERROR deleting spirit score submission' + mysql_error());
			}
        }
    } else {
        print "No items selected for deletion";
    }
}

function inDev($oldWeek, $teamID) { //should roll back team_most_recent_week_submitted when deleting score submission but might call some bugs
	
	global $teamsTable;
	
	$resetWeek = mysql_query("UPDATE $teamsTable SET (team_most_recent_week_submitted = team_most_recent_week_submitted - 1) WHERE team_id = $teamID;");
}

function getPlayerData($sportID, $leagueID, $teamID, $seasonID) {
	global $playersTable, $leaguesTable, $seasonsTable, $sportsTable, $teamsTable, $playerArray, $container, $dbConnection;
	
	$checkAddresses = array();
	
	$sportID != 0?$sportClause = "sport_id = $sportID AND ":$sportClause = '';
	if($leagueID != 0) {
		$leagueClause = "league_id = $leagueID AND ";
	} else {
		$leagueClase = '';
	}
	if($seasonID != 0) {
		$seasonClause = "season_id = $seasonID";
	} else {
		$seasonClause = "season_available_score_reporter = 1";
	}

	$playersQuery = "SELECT player_id, team_name, player_firstname, player_lastname, player_email, team_id FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id WHERE player_team_id = $teamID";
	if(!($result = $dbConnection->query($playersQuery))) $container->printError('ERROR getting players - '.$dbConnection->error);
	$numPlayers = 0;
	while($player = $result->fetch_object()) {
		$playerArray[$numPlayers] = new Player();
		$playerArray[$numPlayers]->playerID = $player->player_id;
		$playerArray[$numPlayers]->playerTeamName = $player->team_name;
		$playerArray[$numPlayers]->playerFirstName = $player->player_firstname;
		$playerArray[$numPlayers]->playerLastName = $player->player_lastname;
		$playerArray[$numPlayers]->playerEmail = $player->player_email;
		$playerArray[$numPlayers]->playerTeamID = $player->team_id;
		$numPlayers++;
	}
	return $numPlayers;

}

function getTeamsData($teamID) {
	global $teamsTable, $leaguesTable, $teamObjs, $leagueID;
	
	$teamsQuery = mysql_query("SELECT league_id,league_sport_id,league_season_id FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		WHERE team_id = $teamID") or die('ERROR getting leagueID - '.mysql_error());
	$teamArray = mysql_fetch_array($teamsQuery);
	$leagueID = $teamArray['league_id'];
	$sportID = $teamArray['league_sport_id'];
	$seasonID = $teamArray['league_season_id'];

	//This query is kindve weird, i can't get the teams by league because if they get split some of the teams that the current team played may not be in the same
	// league as it anymore. So I just get them all by sport and season.
	$teamsQuery = mysql_query("SELECT team_id,team_name FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		WHERE (league_sport_id = $sportID AND league_season_id = $seasonID AND team_num_in_league > 0) OR team_id=1") or die('ERROR getting teams in league - '.mysql_error());
	$numTeams = 0;
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$teamObjs[$numTeams] = new Team();
		$teamObjs[$numTeams]->teamID = $teamArray['team_id'];
		$teamObjs[$numTeams]->teamName = $teamArray['team_name'];
		$numTeams++;	
	}
	
	return $numTeams;
}

function getSubmissionData($teamID) {
	global $scoreSubmissionsTable, $datesTable, $submission, $spiritScoresTable, $scoreCommentsTable;
	
	$teamScoreSubmissionsQuery = mysql_query("SELECT score_submission_id, score_submission_opp_team_id, score_submission_submitter_name, score_submission_submitter_email, 	
	score_submission_result, score_submission_score_them, score_submission_score_us, date_description, date_id, score_submission_datestamp, date_week_number FROM $scoreSubmissionsTable INNER 
	JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
		WHERE score_submission_team_id = $teamID AND score_submission_ignored = 0 ORDER BY date_week_number ASC, score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$submissionNum = 0;
	while($scoreSubmission = mysql_fetch_array($teamScoreSubmissionsQuery)) {
		$submission[$submissionNum] = new Submission();
		$submission[$submissionNum]->submissionID = $scoreSubmission['score_submission_id'];
		$submission[$submissionNum]->oppTeamID = $scoreSubmission['score_submission_opp_team_id'];
		$submission[$submissionNum]->submitterName = $scoreSubmission['score_submission_submitter_name'];
		$submission[$submissionNum]->submitterEmail = $scoreSubmission['score_submission_submitter_email'];
		$submission[$submissionNum]->result = $scoreSubmission['score_submission_result'];
		$submission[$submissionNum]->scoreThem = $scoreSubmission['score_submission_score_them'];
		$submission[$submissionNum]->scoreUs = $scoreSubmission['score_submission_score_us'];
		$submission[$submissionNum]->gameDate = $scoreSubmission['date_description'];
		$submission[$submissionNum]->dateID = $scoreSubmission['date_id'];
		$submission[$submissionNum]->submittedDate = $scoreSubmission['score_submission_datestamp'];
		$submission[$submissionNum]->weekNum = $scoreSubmission['date_week_number'];

		$scoreSubmissionNum = $submission[$submissionNum]->submissionID;
		$spiritQuery = mysql_query("SELECT spirit_score_value, spirit_score_id FROM $spiritScoresTable WHERE spirit_score_score_submission_id = $scoreSubmissionNum
			AND spirit_score_ignored = 0") or die('ERROR getting spirit - '.mysql_error());
		if($spiritArray = mysql_fetch_array($spiritQuery)) {
			$submission[$submissionNum]->spiritValue = $spiritArray['spirit_score_value'];
			$submission[$submissionNum]->spiritID = $spiritArray['spirit_score_id'];
			$submission[$submissionNum]->isSpirit = 1;
		}
		$commentQuery = mysql_query("SELECT comment_value FROM $scoreCommentsTable WHERE comment_score_submission_id = $scoreSubmissionNum") 
			or die('ERROR getting comments - '.mysql_error());
		if($commentArray = mysql_fetch_array($commentQuery)) {
			$submission[$submissionNum]->commentValue = $commentArray['comment_value'];
			$submission[$submissionNum]->isComment = 1;
		}
		
		$submissionNum++;
	}
	return $submissionNum;
}

function getOppSubmissionData($teamID) {
	global $scoreSubmissionsTable, $datesTable, $oppSubmission, $spiritScoresTable, $scoreCommentsTable;
	
	$teamScoreSubmissionsQuery = mysql_query("SELECT score_submission_id, score_submission_team_id, score_submission_submitter_name, score_submission_submitter_email, 	
	score_submission_result, score_submission_score_them, score_submission_score_us, date_description, date_id, score_submission_datestamp, date_week_number
	FROM $scoreSubmissionsTable INNER JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
		WHERE score_submission_opp_team_id = $teamID AND score_submission_ignored = 0 ORDER BY date_week_number ASC, score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$oppSubmissionNum = 0;
	while($scoreSubmission = mysql_fetch_array($teamScoreSubmissionsQuery)) {
		$oppSubmission[$oppSubmissionNum] = new Submission();
		$oppSubmission[$oppSubmissionNum]->submissionID = $scoreSubmission['score_submission_id'];
		$oppSubmission[$oppSubmissionNum]->teamID = $scoreSubmission['score_submission_team_id'];
		$oppSubmission[$oppSubmissionNum]->submitterName = $scoreSubmission['score_submission_submitter_name'];
		$oppSubmission[$oppSubmissionNum]->submitterEmail = $scoreSubmission['score_submission_submitter_email'];
		$oppSubmission[$oppSubmissionNum]->result = $scoreSubmission['score_submission_result'];
		$oppSubmission[$oppSubmissionNum]->scoreThem = $scoreSubmission['score_submission_score_them'];
		$oppSubmission[$oppSubmissionNum]->scoreUs = $scoreSubmission['score_submission_score_us'];
		$oppSubmission[$oppSubmissionNum]->gameDate = $scoreSubmission['date_description'];
		$oppSubmission[$oppSubmissionNum]->submittedDate = $scoreSubmission['score_submission_datestamp'];
		$oppSubmission[$oppSubmissionNum]->weekNum = $scoreSubmission['date_week_number'];
		
		$submissionNum = $oppSubmission[$oppSubmissionNum]->submissionID;
		$spiritQuery = mysql_query("SELECT spirit_score_value FROM $spiritScoresTable WHERE spirit_score_score_submission_id = $submissionNum 
			AND spirit_score_ignored = 0") or die('ERROR getting spirit - '.mysql_error());
		if($spiritArray = mysql_fetch_array($spiritQuery)) {
			$oppSubmission[$oppSubmissionNum]->spiritValue = $spiritArray['spirit_score_value'];
			$oppSubmission[$oppSubmissionNum]->isSpirit = 1;
		}
		$commentQuery = mysql_query("SELECT comment_value FROM $scoreCommentsTable WHERE comment_score_submission_id = $submissionNum") 
			or die('ERROR getting opponent comments - '.mysql_error());
		if($commentArray = mysql_fetch_array($commentQuery)) {
			$oppSubmission[$oppSubmissionNum]->commentValue = $commentArray['comment_value'];
			$oppSubmission[$oppSubmissionNum]->isComment = 1;
		}
		$oppSubmissionNum++;
	}
	return $oppSubmissionNum;
} ?>