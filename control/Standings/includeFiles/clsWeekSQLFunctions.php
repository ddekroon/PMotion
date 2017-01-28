<?php

function updateWeek($leagueID, $dateID) {
	global $teamsTable, $leaguesTable, $postTeams, $numGames, $numMatches, $dateID;
	global $scoreSubmissionsTable, $teamsTable, $datesTable, $container;
	
	$dateArray = mysql_fetch_array(mysql_query("SELECT date_week_number FROM $datesTable WHERE date_id = $dateID"));
	$dateWeek = $dateArray['date_week_number'];
	
	for($i = 0; $i < count($postTeams); $i++) {
		$teamID = $postTeams[$i]->teamID;
		$oppTeam[0] = $postTeams[$i]->teamOppTeamID1;
		$oppTeam[1] = $postTeams[$i]->teamOppTeamID2;
		for($j = 0; $j < $numMatches; $j++) {
			for($k=0; $k < $numGames; $k++) {
				$result = $postTeams[$i]->teamOppSubmission[$j][$k];
				$scoreString = "INSERT INTO $scoreSubmissionsTable (score_submission_team_id, score_submission_opp_team_id, score_submission_date_id, score_submission_submitter_name, score_submission_result, score_submission_ignored, score_submission_datestamp, score_submission_dont_show, score_submission_is_phantom) VALUES ($teamID, $oppTeam[$j], $dateID, '".$_SESSION['username']."', $result, 0, NOW(), 0, 0)";
				if($result == 1) {
					$standingsFilter = ", team_wins = team_wins + 1 ";
				} else if($result == 2) {
					$standingsFilter = ", team_losses = team_losses + 1 ";
				} else if($result == 3) {
					$standingsFilter = ", team_ties = team_ties + 1 ";
				} else {
					$standingsFilter = '';
				}
				$teamString = "UPDATE $teamsTable SET team_most_recent_week_submitted = $dateWeek $standingsFilter WHERE team_id = $teamID";
				//print $scoreString.'<br />';
				//print $teamString.'<br />';
				if($result > 0 && $result < 6) {
					mysql_query($scoreString) or die($container->printError('Error inserting score submission - '.mysql_error()));
					mysql_query($teamString) or die($container->printError('Error updating team - '.$teamID.' - '.mysql_error()));
				}
			}
		}
	}
	$container->printSuccess('Successfully inputted scores');
}

function getGlobalVariables($leagueID) {
	global $teamsTable, $leaguesTable, $allTeamIDs, $numMatches, $numGames;
	global $closedWins, $closedTies, $closedLosses, $leagueWins, $leagueTies, $leagueLosses;
	$allTeamIDs = array();
	$teamNames = array();
	$leagueWins = 0;
	$leagueLosses = 0;
	$leagueTies = 0;
	$closedWins = 0;
	$closedLosses = 0;
	$closedTies = 0;
	$teamsQuery=mysql_query("SELECT team_id,team_name,league_num_of_matches,league_num_of_games_per_match,team_wins,team_losses,team_ties FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		WHERE (team_league_id = $leagueID OR team_id = 1) AND team_num_in_league > 0") 
		or die($container->printError('Error getting teams'.mysql_error()));
	$i=0;
	while($teamArray = mysql_fetch_array($teamsQuery)){
		$teamNames[$teamArray['team_id']] = $teamArray['team_name'];
		array_push($allTeamIDs, $teamArray['team_id']);
		$numMatches = $teamArray['league_num_of_matches'];
		$numGames = $teamArray['league_num_of_games_per_match'];
		$leagueWins += $teamArray['team_wins'];
		$leagueLosses += $teamArray['team_losses'];
		$leagueTies += $teamArray['team_ties'];
		$closedWins += $teamArray['team_wins'];
		$closedLosses += $teamArray['team_losses'];
		$closedTies += $teamArray['team_ties'];
	}
	return $teamNames;	
}

function getWeekData($leagueID, $dateID) {
	global $team, $teamsTable, $leaguesTable, $scoreSubmissionsTable, $teamIDs, $teamNames, $datesTable;
	$teamsSubmitted = array();
	$teamIDsQuery = mysql_query("SELECT DISTINCT score_submission_team_id FROM $scoreSubmissionsTable 
		INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id 
		WHERE team_league_id = $leagueID AND team_dropped_out = 0 AND team_num_in_league > 0 AND score_submission_date_id = $dateID ORDER BY team_num_in_league ASC") 
		or die($container->
		printError('Error getting teams'.mysql_error()));
	while($teamArray = mysql_fetch_array($teamIDsQuery)) {
		$teamsSubmitted[] = $teamArray['score_submission_team_id'];
	}
	
	$teamsQuery=mysql_query("SELECT team_id,team_name, team_wins,team_losses,team_ties,team_most_recent_week_submitted FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $datesTable ON $datesTable.date_sport_id = $leaguesTable.league_sport_id
		WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND date_id = $dateID AND league_id = $leagueID 
		AND team_dropped_out = 0") 
		or die($container->printError('Error getting teams'.mysql_error()));
	$teamNum=0;
	while($teamArray = mysql_fetch_array($teamsQuery)){ //goes through the db and selects all of each team's data for display in the standings
		$teamID = $teamArray['team_id'];
		if(!in_array($teamID, $teamsSubmitted)) {
			$team[$teamID] = new Team();
			$team[$teamID]->teamName = $teamArray['team_name'];
			$team[$teamID]->teamID = $teamArray['team_id'];
			$team[$teamID]->teamWins = $teamArray['team_wins'];
			$team[$teamID]->teamLosses = $teamArray['team_losses'];
			$team[$teamID]->teamTies = $teamArray['team_ties'];
			$team[$teamID]->teamSubmittedWeek = $teamArray['team_most_recent_week_submitted'];
			$team[$teamID]->teamPoints = $team[$teamID]->getPoints();
			$team[$teamID]->teamPointsAvailable = $team[$teamID]->getAvailablePoints();
			$team[$teamID]->teamWinPercent = $team[$teamID]->getWinPercent();
			$teamIDs[$teamNum] = $teamID;
			$teamNum++;
		}
	}
}

function getScheduledMatchesInfo($leagueID, $dateID) {
	global $scheduledMatchesTable, $datesTable, $leaguesTable, $team, $teamIDs;
	
	$matchesQuery = mysql_query("SELECT scheduled_match_team_id_1,scheduled_match_team_id_2,scheduled_match_date_id FROM $scheduledMatchesTable 
		INNER JOIN $datesTable ON $datesTable.date_id = $scheduledMatchesTable.scheduled_match_date_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $scheduledMatchesTable.scheduled_match_league_id 
		WHERE date_id = $dateID AND scheduled_match_league_id = $leagueID 
		ORDER BY scheduled_match_time ASC") or die($container->printError('Error getting matches - '.mysql_error()));
	$rowNum = 0;
	while($matchArray = mysql_fetch_array($matchesQuery)) {
		$team1 = $matchArray['scheduled_match_team_id_1'];
		$team2 = $matchArray['scheduled_match_team_id_2'];
		$dateID = $matchArray['scheduled_match_date_id'];
		if(in_array($team1, $teamIDs)) {
			if($team[$team1]->teamOppTeamID1 == '') {
				$team[$team1]->teamOppTeamID1 = $team2;
				$team[$team1]->teamOppTeamID2 = 0;
			} else if($team[$team1]->teamOppTeamID2 == 0 && $team[$team1]->teamOppTeamID1 != $team2) {
				$team[$team1]->teamOppTeamID2 = $team2;
			}
		}
		if(in_array($team2, $teamIDs)) {
			if($team[$team2]->teamOppTeamID1 == '') {
				$team[$team2]->teamOppTeamID1 = $team1;
				$team[$team2]->teamOppTeamID2 = 0;
			} else if($team[$team2]->teamOppTeamID2 == 0 && $team[$team2]->teamOppTeamID1 != $team1) {
				$team[$team2]->teamOppTeamID2 = $team1;
			}
		}
		$rowNum++;
	}
	return $rowNum;
}

function getOppSubmissions($leagueID, $dateID) {
	global $allTeamIDs, $numGames, $team, $teamIDs;
	global $scoreSubmissionsTable, $teamsTable, $leaguesTable, $seasonsTable, $datesTable;
	global $closedWins, $closedTies, $closedLosses;
	$numSubmissions = 0;
	$gameNum = 0;
	$submission = array();
	
	foreach($teamIDs as $teamID) {
		foreach($allTeamIDs as $oppTeamID) {
			for($i = 0; $i < $numGames; $i++) {
				$team[$teamID]->teamOppSubmission[$oppTeamID][$i] = 0;
			}
		}
	}

	$submissionQuery = mysql_query ("SELECT score_submission_team_id,score_submission_opp_team_id,score_submission_result FROM $scoreSubmissionsTable 
		INNER JOIN $teamsTable ON $scoreSubmissionsTable.score_submission_opp_team_id = $teamsTable.team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
		WHERE score_submission_ignored = 0 AND league_id = $leagueID AND score_submission_dont_show = 0
		AND date_id = $dateID
		ORDER BY score_submission_date_id DESC, score_submission_id ASC") 
		or die($container->printError('Error getting submission data - '.mysql_error()));
	
	while($submissionArray=mysql_fetch_array($submissionQuery)){
		$teamID = $submissionArray['score_submission_team_id'];
		$oppTeamID = $submissionArray['score_submission_opp_team_id'];
		$submissionResult = $submissionArray['score_submission_result'];
		
		if(in_array($oppTeamID, $teamIDs)) {
			if($gameNum == 0 && $numGames == 2) {
				$gameNum = 1;
			} else {
				$gameNum = 0;
			}
			//print $teamID.' '.$oppTeamID.' '.$submissionResult.'<br />';
			if($submissionResult == 2) {
				$team[$oppTeamID]->teamOppSubmission[$teamID][$gameNum] = 1;
				$closedWins += 1;
			} else if($submissionResult == 1) {
				$team[$oppTeamID]->teamOppSubmission[$teamID][$gameNum] = 2;
				$closedLosses += 1;
			} else if($submissionResult == 3) {
				$team[$oppTeamID]->teamOppSubmission[$teamID][$gameNum] = 3; 
				$closedTies += 1;
			} else {
				$team[$oppTeamID]->teamOppSubmission[$teamID][$gameNum] = 0; 
			}
		}
	}
}?>