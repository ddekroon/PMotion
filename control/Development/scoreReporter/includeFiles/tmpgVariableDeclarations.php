<?php 

function getMatchData($teamObjs, $teamID) {
	global $scoreSubmissionsTable, $datesTable, $scheduledMatchesTable, $leaguesTable, $teamsTable, $venuesTable;
	global $seasonsTable, $dbConnection;
	
	if(!($datesQuery = $dbConnection->query("SELECT * FROM $scheduledMatchesTable 
		INNER JOIN $datesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id
		INNER JOIN $leaguesTable ON $scheduledMatchesTable.scheduled_match_league_id = $leaguesTable.league_id 
		INNER JOIN $venuesTable ON $venuesTable.venue_id = $scheduledMatchesTable.scheduled_match_field_id
		WHERE (scheduled_match_team_id_2 = $teamID OR scheduled_match_team_id_1 = $teamID) ORDER BY date_week_number ASC, 
		scheduled_match_time ASC"))) {
		print "Error getting matches ".$dbConnection->error;
		exit(0);
	}
	$lastOppTeam = 0;
	$matchNumDay = 0;
	$lastGameTime = 0;
	$lastDate = 0;
	$matchNum = 1;
	
	while($matchNode = $datesQuery->fetch_object()) {
		if($matchNode->scheduled_match_team_id_1 == $teamID) {
			$curOppTeamID = $matchNode->scheduled_match_team_id_2;
			$shirtColour = 'Dark';
		} else {
			$curOppTeamID = $matchNode->scheduled_match_team_id_1;
			$shirtColour = 'Light';
		}

		if($curOppTeamID != $lastOppTeam || $matchNode->date_id != $lastDate || $matchNode->scheduled_match_time != $lastGameTime) {
			if($lastDate != 0 && $lastDate == $matchNode->date_id) { //same date, next match
				$matchNumDay++;
			} else { //assuming different date
				$matchNumDay = 0;
			}
			$matchObj[$matchNode->date_id][$matchNumDay] = new Match();
			$matchObj[$matchNode->date_id][$matchNumDay]->matchNum = $matchNum++;
			$matchObj[$matchNode->date_id][$matchNumDay]->oppTeamID = $curOppTeamID;
			$matchObj[$matchNode->date_id][$matchNumDay]->dateID = $matchNode->date_id;
			$matchObj[$matchNode->date_id][$matchNumDay]->gameDate = $matchNode->date_description;
			$matchObj[$matchNode->date_id][$matchNumDay]->matchField = $matchNode->venue_short_show_name;
			$matchObj[$matchNode->date_id][$matchNumDay]->matchGameTime = $matchNode->scheduled_match_time;
			$matchObj[$matchNode->date_id][$matchNumDay]->matchFieldLink = $matchNode->venue_link;
			$matchObj[$matchNode->date_id][$matchNumDay]->oppTeamName = $teamObjs[$curOppTeamID]->team_name;
			$matchObj[$matchNode->date_id][$matchNumDay]->matchShirtColour = $shirtColour;
			$lastOppTeam = $curOppTeamID;
			$lastGameTime = $matchNode->scheduled_match_time;
			$lastDate = $matchNode->date_id;
		}
	}
	$datesQuery->close();
	
	if(!($teamScoreSubmissionsQuery = $dbConnection->query("SELECT date_id, score_submission_opp_team_id,
		score_submission_is_phantom, score_submission_result, date_description
		FROM $scoreSubmissionsTable 
		INNER JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
		WHERE score_submission_team_id = $teamID AND score_submission_ignored = 0
		ORDER BY date_week_number ASC, score_submission_is_phantom ASC, score_submission_id ASC"))) {
		print 'ERROR getting score submissions - '.$dbConnection->error;
		exit(0);
	}
	$submissionNum = 0;
	$gameNum = 0;
	$lastDateID = 0;
	$lastOppTeamID = 0;
	while($scoreSubmission = $teamScoreSubmissionsQuery->fetch_object()) {
		$dateID = $scoreSubmission->date_id;
		$oppTeamID = $scoreSubmission->score_submission_opp_team_id;
		if($dateID != $lastDateID) {
			$matchNumDay = 0;
			$gameNum = 0;
			$lastDateID = $dateID;
			$lastOppTeamID = $oppTeamID;
		} else if($oppTeamID != $lastOppTeamID) {
			$matchNumDay++;
			$lastOppTeamID = $oppTeamID;
			$gameNum = 0;
		} else {
			$gameNum++;
		}
	    if(isset($matchObj[$dateID][$matchNumDay])) {
			$matchObj[$dateID][$matchNumDay]->gameResults[$gameNum] = $scoreSubmission->score_submission_result;
			$matchObj[$dateID][$matchNumDay]->oppTeamID = $oppTeamID;
			if($oppTeamID != 1) {
				$matchObj[$dateID][$matchNumDay]->standingsString = $teamObjs[$oppTeamID]->getFormattedStandings();
				$matchObj[$dateID][$matchNumDay]->oppTeamName = $teamObjs[$oppTeamID]->team_name;
			} else {
				$matchObj[$dateID][$matchNumDay]->oppTeamName = 'PRACTICE';
			}
	    }
	}
	$teamScoreSubmissionsQuery->close();
	return $matchObj;
}

function getTeamsData($teamID) {
	global $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable, $dbConnection;
	
	$queryString = "SELECT
		team_id, team_league_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted, team_pic_name,
		team_wins * 2 + team_ties as team_points, $leaguesTable.*,
		SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
		FROM (SELECT * FROM $teamsTable WHERE team_league_id = (SELECT team_league_id FROM $teamsTable WHERE team_id = $teamID)
			AND ((team_num_in_league > 0 AND team_dropped_out = 0) OR team_id = 1)) as teamTable
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
		INNER JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id AND (score_submission_ignored = 0
		OR score_submission_is_phantom = 1))
		INNER JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
		(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
		AND spirit_score_ignored = 0)
		INNER JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
		GROUP BY team_id";
	if(!($teamQuery = $dbConnection->query($queryString))) { 
		print 'ERROR getting team data - '.$dbConnection->error;
		exit(0);
	} else if($teamQuery->num_rows == 0) {
		print 'Error, bad team';
		exit(0);
	}
		
	while($teamObj = $teamQuery->fetch_object()) {
		$teamObjs[$teamObj->team_id] = new Team($teamObj);
		$leagueID = $teamObj->team_league_id;
	}
	
	$splitString = mysql_query("SELECT league_is_split FROM $leaguesTable WHERE league_id = $leagueID");
	$splitQuery = mysql_fetch_array($splitString); 
	$checkSplit = $splitQuery['league_is_split'];

	if($checkSplit == 1) {
		$newLeagueID = $leagueID + 1;
		$newLeagueID2 = $leagueID - 1;
		
		$queryString = "SELECT
			team_id, team_league_id, team_name, team_wins, team_losses, team_ties, team_most_recent_week_submitted, team_pic_name,
			team_wins * 2 + team_ties as team_points, $leaguesTable.*,
			SUM(spirit_score_edited_value) / COUNT(spirit_score_edited_value) as team_spirit_average
			FROM (SELECT * FROM $teamsTable WHERE ((team_league_id = $newLeagueID) OR (team_league_id = $newLeagueID2))
				AND ((team_num_in_league > 0 AND team_dropped_out = 0) OR team_id = 1)) as teamTable
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = teamTable.team_league_id
			INNER JOIN $scoreSubmissionsTable ON ($scoreSubmissionsTable.score_submission_opp_team_id = teamTable.team_id AND (score_submission_ignored = 0
			OR score_submission_is_phantom = 1))
			INNER JOIN $spiritScoresTable ON ($spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id AND 
			(spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0
			AND spirit_score_ignored = 0)
			INNER JOIN $datesTable ON (date_id = score_submission_date_id AND date_week_number < league_playoff_week)
			GROUP BY team_id";
		if(!($teamQuery = $dbConnection->query($queryString))) { 
			print 'ERROR getting team data - '.$dbConnection->error;
			exit(0);
		}
		
		while($teamObj = $teamQuery->fetch_object()) {
			$teamObjs[$teamObj->team_id] = new Team($teamObj);
		}
	}
	
	$teamQuery->close();
	return array('teamObjs' => $teamObjs, 'leagueID' => $leagueID);
}

function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
}?>