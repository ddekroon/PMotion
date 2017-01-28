<?php

//This function expedites the process of querying a database
function query($query_string){
	$quer_line=mysql_query($query_string) or die("TEST ".mysql_error());
	$array_line=mysql_fetch_array($quer_line);
	return ($array_line);
}

function setLeagueWeek($leagueID) {
	global $leaguesTable, $teamsTable, $datesTable, $scheduledMatchesTable, $seasonsTable;
	$leagueArray = query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
	$leagueWeek = $leagueArray['league_week_in_score_reporter'];
	$leagueSport = $leagueArray['league_sport_id'];
	$leagueSeason = $leagueArray['league_season_id'];
	$leagueDay = $leagueArray['league_day_number'];
	$dateChangeTime = $leagueArray['league_hide_spirit_hour'];
	
	/*$teamQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 
		AND team_dropped_out = 0") or die('ERROR getting teams to check which week - '.mysql_error());
	$weekChecker = 1;
	while($teamArray = mysql_fetch_array($teamQuery)) {
		if($leagueWeek > $teamArray['team_most_recent_week_submitted']) {
			$weekChecker = 0;
		}
	}
	if ($weekChecker == 1) { //all of the teams have submitted, check the time and day to see if score reporter should be moved ahead. */
	$dateQuery = mysql_query("SELECT * FROM $datesTable 
		INNER JOIN $scheduledMatchesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id 
		WHERE (date_week_number = $leagueWeek + 1 OR date_week_number = $leagueWeek + 2) AND date_sport_id = $leagueSport
		AND date_season_id = $leagueSeason AND date_day_number = $leagueDay ORDER BY date_day_of_year_num ASC")
		or die('ERROR getting dates '.mysql_error());
	if(mysql_num_rows($dateQuery) == 0) {
		$dateQuery = mysql_query("SELECT * FROM $datesTable WHERE date_week_number = $leagueWeek + 1 AND date_sport_id = 
			$leagueSport AND date_season_id = $leagueSeason AND date_day_number = $leagueDay") 
			or die('ERROR getting date 2 - '.mysql_error());
	}
	$dateArray = mysql_fetch_array($dateQuery);
	$dateDayOfYear = $dateArray['date_day_of_year_num'];
	$nextWeek = $dateArray['date_week_number'];
	$curDayOfYear = date('z');
	$curTime = intval(date('G'));
	
	//print("Date: " . $dateDayOfYear . " " . $curDayOfYear);
	//if it is a correct time to switch the week in score reporter
	if ($dateDayOfYear != '' && $curDayOfYear > $dateDayOfYear || ($curDayOfYear == $dateDayOfYear && $curTime >= $dateChangeTime)) { 
		mysql_query("UPDATE $leaguesTable SET league_week_in_score_reporter = $nextWeek,
			league_show_cancel_default_option = 0 WHERE league_id = $leagueID") 
			or die('ERROR setting new week '.mysql_error());
		$leagueWeek = $nextWeek;
	}
	//}
	return $leagueWeek;
}

//Checks to see if the team has already submitted a score by comparing most recent week submitted and league week in score reporter
function checkSubmitWeek($teamID, $dateID) {
	global $teamsTable,  $scoreSubmissionsTable;
	$teamQuery = mysql_query("SELECT * FROM $teamsTable 
		INNER JOIN $scoreSubmissionsTable ON $scoreSubmissionsTable.score_submission_team_id = $teamsTable.team_id 
		WHERE team_id = $teamID AND score_submission_date_id = $dateID AND score_submission_ignored = 0")
		or die('ERROR checking for submissions - '.mysql_error());
	
	$numSubmissions = mysql_num_rows($teamQuery);

	if ($numSubmissions == 0) {
		return 0;
	} else {
		return 1;
	}
}

//Puts inputted data into score comments, score submission, and spirit scores databases.
//Checks if data has already been enetered for the week, if so ignore is set.
function submitScores(){
	
	global $scoreSubmissionsTable, $leaguesTable, $teamsTable, $scoreCommentsTable, $seasonsTable, $spiritScoresTable;
	global $teamID, $leagueID, $dateID, $actualWeekDate, $dayOfYear, $isPlayoffs;
	global $oppTeamID, $scoreUs, $scoreThem, $gameResults, $spiritScores, $matchComments, $submitName, $submitEmail, $matches, $games;

	$ignored = checkSubmitWeek($teamID, $dateID); //0 for hasnt, 1 for has
	updateCaptain($teamID, $submitName, $submitEmail);
	$gameNum = 0;

	$submissionArray = query("SELECT MAX(score_submission_id) as maxnum FROM $scoreSubmissionsTable");
	$newSubmissionNum = $submissionArray['maxnum'];
	
	$escapedName = mysql_real_escape_string($submitName);
	$escapedEmail = mysql_real_escape_string($submitEmail);
	
	//submits teamsTable update first in case teams refresh half way through. Emails get sent to admins first so we know 
	//scores that were attempted to be submitted
	$submissionEntered=mysql_query("UPDATE $teamsTable INNER JOIN $leaguesTable ON $teamsTable.team_league_id = 
		$leaguesTable.league_id SET team_most_recent_week_submitted = league_week_in_score_reporter WHERE team_id = $teamID") 
		or die('ERROR updating teamsTable - '.mysql_error()); //sets the week submitted for the team to current
	
	//Inserts data into scores comments, score submissions, and spirit scores database. SS and comments are connected to 
	//second game of beach volleyball score submissions. New score submission made for each game.
	for($i=0;$i<$matches;$i++) {
		$escapedComments[$i] = mysql_real_escape_string($matchComments[$i]);
		for($j=0;$j<$games;$j++) {
			if ($oppTeamID[$i] == 1) {
				$gameResults[$gameNum] = 5;
			}
			$newSubmissionNum++;
			mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, 
				score_submission_opp_team_id, score_submission_date_id, score_submission_submitter_name, 
				score_submission_submitter_email, score_submission_result, score_submission_score_us, score_submission_score_them,
				score_submission_ignored, score_submission_datestamp) VALUES ($newSubmissionNum, $teamID, $oppTeamID[$i], $dateID,
				'$escapedName', '$escapedEmail', $gameResults[$gameNum], $scoreUs[$gameNum], $scoreThem[$gameNum], $ignored, 
				NOW())") or die ('Error with inserting into score submission db - '.mysql_error());
			if ($j == 0) { //connects spirit submission and comments with the first score submission
				if($spiritScores[$i] != 0 && $gameResults[$gameNum]!=4) {
					mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, 
						spirit_score_ignored, spirit_score_edited_value) VALUES ($newSubmissionNum, $spiritScores[$i], $ignored, 
						$spiritScores[$i])") or die('spirit score insert - '.mysql_error());
				}
				if (strlen($matchComments[$i]) > 2) {
					mysql_query("INSERT INTO $scoreCommentsTable (comment_score_submission_id, comment_value) VALUES
						($newSubmissionNum, '$escapedComments[$i]')") or die('comments insert - '.mysql_error());
				}	
			}
			$gameNum++;
		}
	}
	return $ignored;
}//End updateStandings

function updateStandings($teamID, $matches, $games, $gameResults) {
	global $teamsTable;
	$gameNum = 0;
	for ($i = 0;$i<$matches; $i++) {
		for ($j=0;$j<$games;$j++) {
			if ($gameResults[$gameNum] == 1) {
				mysql_query("UPDATE $teamsTable SET team_wins = team_wins+1 WHERE team_id = $teamID");
			} else if ($gameResults[$gameNum] == 2) {
				mysql_query("UPDATE $teamsTable SET team_losses = team_losses+1 WHERE team_id = $teamID");
			} else if ($gameResults[$gameNum] == 3) {
				mysql_query("UPDATE $teamsTable SET team_ties = team_ties+1 WHERE team_id = $teamID");
			}
			$gameNum++;
		}
	}
}

function updateCaptain($teamID, $capName, $capEmail) {
	global $teamsTable, $playersTable;
	
	$playerQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID ORDER BY player_is_captain DESC")
		or die('ERROR getting player info for captains - '.mysql_error());
	while($playerArray = mysql_fetch_array($playerQuery)){
		if($playerArray['player_is_captain'] == 1) { //already has a captain
			return 1;
		} else {
			if(strtolower($capName) == strtolower($playerArray['player_firstname'].' '.$playerArray['player_lastname']) || strtolower($capEmail) == strtolower($playerArray['player_email'])) {
				$playerID = $playerArray['player_id'];
				mysql_query("UPDATE $playersTable SET player_is_captain = 1 WHERE player_id = $playerID") 
					or die('error updating captain - '.mysql_error());
			}
		}
	}
} ?>