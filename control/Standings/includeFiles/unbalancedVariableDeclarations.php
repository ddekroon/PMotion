<?php 

function updateDatabase() {
	global $scoreSubmissionsTable;
	if(isset($_POST['remove'])) {
		$submissionCount = 0;
		foreach($_POST['remove'] as $removeID) {
			$submissionQuery = mysql_query("SELECT score_submission_team_id,score_submission_opp_team_id,score_submission_date_id
											FROM $scoreSubmissionsTable WHERE score_submission_id = $removeID") 
				or die('ERROR getting score submission - '.mysql_error());
			$submissionArray = mysql_fetch_array($submissionQuery);
			$teamID = $submissionArray['score_submission_team_id'];
			$oppTeamID = $submissionArray['score_submission_opp_team_id'];
			$dateID = $submissionArray['score_submission_date_id'];
			mysql_query("UPDATE $scoreSubmissionsTable SET score_submission_dont_show = 1 
				WHERE (score_submission_team_id = $teamID AND score_submission_opp_team_id = $oppTeamID
				AND score_submission_date_id = $dateID) OR score_submission_id = $removeID") 
				or die('ERROR updating score submissions '.mysql_error());
			$submissionCount++;
		}
		print 'Spirit\'s updated, '.$submissionCount.' submissions affected';
	} else {
		print 'No submissions selected';
	}
}

//Gets all teams in the season available in the score reporter. Also gets team PRACTICE (teamID=1). Stores them all in a global variable '$team'
function getTeamsDatabaseInfo() {
	global $team, $teamsTable, $leaguesTable, $seasonsTable, $playersTable, $seasonID;
	
	$teamsQuery = mysql_query("SELECT season_id, team_id, team_name, team_wins, team_losses, team_ties, league_id, league_sport_id, league_name, league_day_number, player_email, 
		team_most_recent_week_submitted, league_week_in_score_reporter, league_num_of_matches, league_num_of_games_per_match FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
		WHERE player_is_captain = 1 AND (team_num_in_league > 0 || team_id = 1) 
		AND season_available_score_reporter = 1
		ORDER BY league_day_number ASC, league_name ASC, team_num_in_league ASC") or die('ERROR getting teams - '.mysql_error());
	$numTeams = 0;
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		if($numTeams == 0) {
			$seasonID = $teamArray['season_id'];
		}
		$team[$teamArray['team_id']] = new Team();
		$team[$teamArray['team_id']]->teamID = $teamArray['team_id'];
		$team[$teamArray['team_id']]->teamName = $teamArray['team_name'];
		$team[$teamArray['team_id']]->teamStndWins = $teamArray['team_wins'];
		$team[$teamArray['team_id']]->teamStndLosses = $teamArray['team_losses'];
		$team[$teamArray['team_id']]->teamStndTies = $teamArray['team_ties']; 
		$team[$teamArray['team_id']]->teamLeagueID = $teamArray['league_id'];
		$team[$teamArray['team_id']]->teamSportID = $teamArray['league_sport_id'];
		$team[$teamArray['team_id']]->teamLeagueName = $teamArray['league_name'].' - '.dayString($teamArray['league_day_number']);
		$team[$teamArray['team_id']]->teamCptnEmail = $teamArray['player_email'];
		$team[$teamArray['team_id']]->teamWeekInScoreReporter = $teamArray['team_most_recent_week_submitted'];
		$team[$teamArray['team_id']]->leagueWeekInScoreReporter = $teamArray['league_week_in_score_reporter'];
		$team[$teamArray['team_id']]->leagueNumMatches = $teamArray['league_num_of_matches'];
		$team[$teamArray['team_id']]->leagueNumGames = $teamArray['league_num_of_games_per_match'];
		$numTeams++;
	}

	return $numTeams;
}

function getResultsDatabaseInfo($numTeams) {
	global $team, $submission;
	global $scoreSubmissionsTable, $teamsTable, $leaguesTable, $seasonsTable, $seasonID;
	
	$submissionQuery = mysql_query ("SELECT score_submission_team_id, score_submission_opp_team_id, score_submission_result FROM $scoreSubmissionsTable 
		INNER JOIN $teamsTable ON $scoreSubmissionsTable.score_submission_opp_team_id = $teamsTable.team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		WHERE score_submission_ignored = 0 AND (league_season_id = $seasonID || league_season_id = 0)
		ORDER BY score_submission_date_id DESC, score_submission_team_id ASC, score_submission_ignored ASC") 
		or die('ERROR getting submission data - '.mysql_error());
	
	while($submissionArray=mysql_fetch_array($submissionQuery)){
		$teamID = $submissionArray['score_submission_team_id'];
		$oppTeamID = $submissionArray['score_submission_opp_team_id'];
		$submissionResult = $submissionArray['score_submission_result'];
		if($team[$teamID]->teamName == '') { //keeps junk values for teams no longer in the league out
			continue;
		}

		if($submissionResult == 1) {
			$team[$oppTeamID]->teamOppLosses++;
			$team[$teamID]->teamWins++;
		} else if($submissionResult == 2) {
			$team[$oppTeamID]->teamOppWins++;
			$team[$teamID]->teamLosses++;
		} else if($submissionResult == 3) {
			$team[$oppTeamID]->teamOppTies++;
			$team[$teamID]->teamTies++;
		} else if($submissionResult == 4) {
			$team[$teamID]->teamCancels++;
		} else if($submissionResult == 5) {
			$team[$teamID]->teamPractices++;
		}

		$team[$teamID]->teamSubmissions++;
		$team[$oppTeamID]->teamOppSubmissions++;
	} 
	return $numSubmissions;
}

/*Gets ALL the scheduled match information. Stores it in team array. This means it gets every scheduled match for every team in every week.
function getScheduledMatchesInfo() {
	global $scheduledMatchesTable, $leaguesTable, $seasonsTable, $team;
	
	$matchesQuery = mysql_query("SELECT * FROM $scheduledMatchesTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $scheduledMatchesTable.scheduled_match_league_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE season_available_score_reporter = 1 ORDER BY scheduled_match_team_id_1 ASC, scheduled_match_id ASC") 
		or die('ERROR getting matches - '.mysql_error());
	$rowNum = 1;
	while($matchArray = mysql_fetch_array($matchesQuery)) {
		$team1 = $matchArray['scheduled_match_team_id_1'];
		$team2 = $matchArray['scheduled_match_team_id_2'];
		$dateID = $matchArray['scheduled_match_date_id'];
		if($team[$team1]->teamOppTeam1[$dateID] == '') {
			$team[$team1]->teamOppTeam1[$dateID] = $team2;
			$team[$team1]->teamOppTeam2[$dateID] = 0;
		} else if($team[$team1]->teamOppTeam2[$dateID] == 0 && $team[$team1]->teamOppTeam1[$dateID] != $team2) {
			$team[$team1]->teamOppTeam2[$dateID] = $team2;
		}
		if($team[$team2]->teamOppTeam1[$dateID] == '') {
			$team[$team2]->teamOppTeam1[$dateID] = $team1;
			$team[$team2]->teamOppTeam2[$dateID] = 0;
		} else if($team[$team2]->teamOppTeam2[$dateID] == 0 && $team[$team2]->teamOppTeam1[$dateID] != $team1) {
			$team[$team2]->teamOppTeam2[$dateID] = $team1;
		}
		$rowNum++;
	}
}*/