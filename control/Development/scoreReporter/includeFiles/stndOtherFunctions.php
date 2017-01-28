<?php 

function query($query_string){
	global $dbConnection;
	
	if(!($query = $dbConnection->query($query_string))) {
		print 'ERROR - '.$dbConnection->error;
	}
	$dbObj = $query->fetch_object();
	$query->close();
	return $dbObj;
}

function getWeekday($leagueDayNum) {
	$leagueDayNum = $leagueDayNum % 7;
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

function getHeadToHead($teamOne, $teamTwo) {
	global $scoreSubmissionsTable, $dbConnection;
	$query = "SELECT SUM(score_submission_score_us) - SUM(score_submission_score_them) as score_differential
		FROM $scoreSubmissionsTable
		WHERE score_submission_ignored = 0 AND score_submission_team_id = $teamOne AND score_submission_opp_team_id = $teamTwo
		GROUP BY score_submission_team_id";
	if(!($submissionsQuery = $dbConnection->query($query) )) {
		print 'ERROR getting score submissions - '.$dbConnection->error;
		exit(0);
	}
	$submission = $submissionsQuery->fetch_object();
	$submissionsQuery->close();
	return $submission->score_differential;
}

function getCommonPlusMinus($teamOne, $teamTwo) {
	global $scoreSubmissionsTable, $dbConnection;
	$oppTeamOne = array();
	$teamOneFor = array();
	$teamOneAgainst = array();
	$plusMinusOne = 0;
	$plusMinueTwo = 0;
	$query = "SELECT score_submission_team_id,
		SUM(score_submission_score_us - score_submission_score_them) as common_score_differential
		FROM (SELECT score_submission_team_id, score_submission_score_us, score_submission_score_them
			FROM $scoreSubmissionsTable
			WHERE score_submission_ignored = 0 AND (score_submission_team_id = $teamOne OR score_submission_team_id = $teamTwo) 
			) as sub_table
		GROUP BY score_submission_team_id";
	if(!($submissionsQuery = $dbConnection->query($query))) {
		print 'ERROR getting score submissions - '.$dbConnection->error;
	}
	while($submission = $submissionsQuery->fetch_object()) {
		if($submission->score_submission_team_id = $teamOne) {
			$diffOne = $submission->common_score_differential;
		} else {
			$diffTwo = $submission->common_score_differential;
		}
	}
	$submissionsQuery->close();
	return $diffOne - $diffTwo;
}

function comparePoints($a, $b) {
    if ($a->getPoints() == $b->getPoints()) {
        if($a->team_spirit_average == $b->team_spirit_average) {
			$headToHead = getHeadToHead($a->team_id, $b->team_id);
			if($headToHead == 0) {
				return getCommonPlusMinus($a->team_id, $b->team_id);
			} else {
				return $headToHead;
			}
		} else {
			return ($a->team_spirit_average > $b->team_spirit_average) ? -1 : 1;
		}	
    }
    return ($a->getPoints() > $b->getPoints()) ? -1 : 1;
}

function comparePercent($a, $b) {
	global $scoreSubmissionsTable;
	if ($a->getWinPercent() == $b->getWinPercent()) {
        if($a->team_spirit_average == $b->team_spirit_average) {
			$headToHead = getHeadToHead($a->team_id, $b->team_id);
			if($headToHead == 0) {
				return getCommonPlusMinus($a->team_id, $b->team_id);
			} else {
				return $headToHead;
			}
		} else {
			return ($a->team_spirit_average > $b->team_spirit_average) ? -1 : 1;
		}	
    }
    return ($a->getWinPercent() > $b->getWinPercent()) ? -1 : 1;
}

function comparePosition($a, $b) {
    if ($a->team_final_position == $b->team_final_position) {
        return 0;
    }
    return ($a->team_final_position < $b->team_final_position) ? -1 : 1;
}

function compareSpirit($a, $b) {
    if ($a->team_final_spirit_position == $b->team_final_spirit_position) {
        return 0;
    }
    return ($a->team_final_spirit_position < $b->team_final_spirit_position) ? -1 : 1;
} 

function getActiveTeams($leagueID) {
	global $dbConnection, $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable;
	global $teamMaxWeek;

	$queryString = "SELECT
		team_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted,
		team_wins * 2 + team_ties as team_points,
		SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
		FROM (SELECT * FROM $teamsTable where team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0) as teamTable
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
		INNER JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id 
		AND (score_submission_ignored = 0 OR score_submission_is_phantom = 1))
		INNER JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
		(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
		AND spirit_score_ignored = 0)
		INNER JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
		GROUP BY team_id";
	if(!($teamsQuery = $dbConnection->query($queryString))) { 
		print 'ERROR getting team objects - '.$dbConnection->error;
		exit(0);
	} else if($teamsQuery->num_rows == 0) {
		print 'Error, no teams';
		exit(0);
	}
		
	$teamMaxWeek = 0;
	while($teamObj = $teamsQuery->fetch_object()){ 
		$teams[] = new Team($teamObj);
		if ($teamObj->team_most_recent_week_submitted > $teamMaxWeek ) {
			$teamMaxWeek = $teamObj->team_most_recent_week_submitted;
		}
	}
	$teamsQuery->close();
	return $teams;	
}

function getFinishedTeams($leagueID) {
	global $dbConnection, $teamsTable;
	
	$query = "SELECT team_name, team_final_position, team_final_spirit_position, team_id 
		FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0";
		
	if(!($teamsQuery = $dbConnection->query($query))) { 
		print 'ERROR getting teams '.$dbConnection->error;
		exit(0);
	}
	while($teamObj = $teamsQuery->fetch_object()){ 
		$teams[] = $teamObj;
	}
	return $teams;	
}

?>