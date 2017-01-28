<?php

//This function expedites the process of querying a database
function query($query_string){
	$quer_line=mysql_query($query_string) or die("TEST ".mysql_error());
	$array_line=mysql_fetch_array($quer_line);
	return ($array_line);
}

//Puts inputted data into score comments, score submission, and spirit scores databases.
//Checks if data has already been enetered for the week, if so ignore is set.
function updateScores($editedGames, $teamCount){
	
	global $scoreSubmissionsTable, $spiritScoresTable, $teamsTable, $leaguesTable, $datesTable, $dateID;
	global $dayNumber, $matches, $games, $teamName,  $dayNumber, $seasonID, $oppTeamsDropDown, $dbTeamSpiritID, $teamNote;
	global $teamScoreSubmissionID, $teamOppTeamID, $teamGameResult, $teamSpiritSubmission, $teamID, $leagueID, $container;

	//Inserts data into scores comments, score submissions, and spirit scores database. SS and comments are connected to first game 
	//of beach volleyball score submissions. New score submission made for each new game submission.
	for($k=0;$k<$teamCount;$k++) {
		$gameNum = 0;
		$teamEdited = 0;
		for($i=0;$i<$matches;$i++) {
			$matchEdited = 0;
			for($j=0;$j<$games;$j++) {
				$teamOppTeamIDString = $teamOppTeamID[$k][$i];
				$teamGameResultString = $teamGameResult[$k][$gameNum];
				$teamScoreSubmissionIDString = $teamScoreSubmissionID[$k][$gameNum];
				if($editedGames[$k][$gameNum] == 1 || $editedGames[$k][$gameNum] == 2) { //1 means SS or teamID was changed, 2 means result was changed
					$matchEdited = 1;
					$teamEdited = 1;
					mysql_query("UPDATE $scoreSubmissionsTable SET score_submission_opp_team_id = $teamOppTeamIDString, 
						score_submission_submitter_name = 'ADMIN-Change', score_submission_result = $teamGameResultString,
						score_submission_datestamp = NOW(), notes = '$teamNote[$k]' WHERE score_submission_id = $teamScoreSubmissionIDString")
						or die ('Error with updating score submission db - '.mysql_error());
				} else if($editedGames[$k][$gameNum] == 3) { //means there was a change but used to be all 0's, now insert
					$matchEdited = 2;
					$teamEdited = 1;
					$submissionArray = query("SELECT MAX(score_submission_id) as maxnum FROM $scoreSubmissionsTable");
					$teamScoreSubmissionID[$k][$gameNum] = $submissionArray['maxnum']+1;
					$newScoreSubmissionNum = $teamScoreSubmissionID[$k][$gameNum];
					mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, score_submission_opp_team_id, 
						score_submission_date_id, score_submission_submitter_name, score_submission_result, score_submission_datestamp, notes) 
						VALUES ($newScoreSubmissionNum, $teamID[$k], $teamOppTeamIDString, $dateID, 'ADMIN-Insert', 
						$teamGameResultString, NOW(), '$teamNote[$k]')") or die ('Error with inserting into score submission db - '.mysql_error());
				}
				if ($j ==0) {
					$teamSpiritSubmissionString = $teamSpiritSubmission[$k][$i];
					$teamScoreSubmissionIDString = $teamScoreSubmissionID[$k][$i*$games];

					if ($teamGameResultString==4) { // if the game was set as cancelled, erase spirit score submission - does not work for some cases, unsure of why
						$check = mysql_query("UPDATE $spiritScoresTable SET spirit_score_value = 0, spirit_score_edited_value = 0 WHERE spirit_score_score_submission_id = $teamScoreSubmissionIDString") or die('spirit score insert - '.mysql_error());
						
					}
					else { // else, update spirit scores as normal
						if($matchEdited == 1) {	
							mysql_query("UPDATE $spiritScoresTable SET spirit_score_edited_value = $teamSpiritSubmissionString, spirit_score_dont_show = 1
								WHERE spirit_score_score_submission_id = $teamScoreSubmissionIDString") or die('spirit score insert - '.mysql_error());
						}
						
						if(($dbTeamSpiritID[$k][$i] == 0 || $matchEdited == 2) && $teamSpiritSubmissionString != 0) {
							mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, spirit_score_ignored, 
							spirit_score_dont_show, spirit_score_edited_value) VALUES ($teamScoreSubmissionIDString, $teamSpiritSubmissionString, 0, 1, $teamSpiritSubmissionString)")
							or die('spirit score insert - '.mysql_error());
						}
					}
				}
				$gameNum++;
			}
		}
		if($teamEdited == 1) {
			$dateArray = query("SELECT date_week_number FROM $datesTable WHERE date_id = $dateID");
			$dateWeek = $dateArray['date_week_number'];
			$submissionEntered=mysql_query("UPDATE $teamsTable INNER JOIN $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id
				SET team_most_recent_week_submitted = league_week_in_score_reporter WHERE team_id = $teamID[$k]
				AND league_week_in_score_reporter = $dateWeek") or die('ERROR updating the week submitted '.mysql_error()); //sets the week submitted for the team to current
			setLeagueWeek($leagueID);
		}
	}
	$container->printSuccess('Score submissions successfully changed');
}//End updateStandings

function updateStandings($editedGames, $teamCount) {
	global $teamsTable, $teamGameResult, $dbTeamGameResult, $teamID, $matches, $games;
	
	for($k=0;$k<$teamCount;$k++) {
		for ($i = 0;$i<$matches*$games; $i++) {
			if($editedGames[$k][$i] == 2) {
				if ($dbTeamGameResult[$k][$i] == 1) {
					mysql_query("UPDATE $teamsTable SET team_wins = team_wins-1 WHERE team_id = $teamID[$k]");
				} else if ($dbTeamGameResult[$k][$i] == 2) {
					mysql_query("UPDATE $teamsTable SET team_losses = team_losses-1 WHERE team_id = $teamID[$k]");
				} else if ($dbTeamGameResult[$k][$i] == 3) {
					mysql_query("UPDATE $teamsTable SET team_ties = team_ties-1 WHERE team_id = $teamID[$k]");
				}
			}
			if($editedGames[$k][$i] == 2 || $editedGames[$k][$i] == 3) {
				if ($teamGameResult[$k][$i] == 1) {
					mysql_query("UPDATE $teamsTable SET team_wins = team_wins+1 WHERE team_id = $teamID[$k]");
				} else if ($teamGameResult[$k][$i] == 2) {
					mysql_query("UPDATE $teamsTable SET team_losses = team_losses+1 WHERE team_id = $teamID[$k]");
				} else if ($teamGameResult[$k][$i] == 3) {
					mysql_query("UPDATE $teamsTable SET team_ties = team_ties+1 WHERE team_id = $teamID[$k]");
				}
			}
		}
	}
}

function setLeagueWeek($leagueID) {
	global $leaguesTable, $teamsTable, $datesTable, $scheduledMatchesTable;

	$leagueArray = query("SELECT league_week_in_score_reporter FROM $leaguesTable WHERE league_id = $leagueID");
	$leagueWeek = $leagueArray['league_week_in_score_reporter'];
	
	$teamQuery = mysql_query("SELECT team_most_recent_week_submitted, team_name FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0");
	$weekChecker = 1;
	while($teamArray = mysql_fetch_array($teamQuery)) {
		if($leagueWeek > $teamArray['team_most_recent_week_submitted']&& strlen($teamArray['team_name']) > 1) {
			$weekChecker = 0;
		}
	}
	
	if ($weekChecker == 1) { //all of the teams have submitted, check the time and day to see if score reporter should be moved ahead.	
		$dateQuery = query("SELECT scheduled_match_time, date_day_of_year_num FROM $datesTable INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $datesTable.date_season_id
			INNER JOIN $scheduledMatchesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id
			WHERE $datesTable.date_week_number = $leaguesTable.league_week_in_score_reporter + 1 AND $datesTable.date_day_number = $leaguesTable.league_day_number
			ORDER BY $scheduledMatchesTable.scheduled_match_time ASC LIMIT 1 ");
		
		$matchTime = $dateQuery['scheduled_match_time'];
		$dateDayOfYear = $dateQuery['date_day_of_year_num'];
		$curDayOfYear = date('z');
		$curTime = intval(date('Gi'));

		if ($curDayOfYear > $dateDayOfYear || ($curDayOfYear == $dateDayOfYear && $curTime >= $matchTime)) { //if it is a correct time to switch the week in score reporter
			mysql_query("UPDATE $leaguesTable SET league_week_in_score_reporter = league_week_in_score_reporter + 1,
				league_show_cancel_default_option = 0 WHERE league_id = $leagueID") or die('ERROR setting new week '.mysql_error());
		}
	}
}
?>