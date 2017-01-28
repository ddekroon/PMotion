<?php 

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($activeTabIndex = $_GET['activeTabIndex']) == '') {
	$activeTabIndex = 0;
}

function getTeamsData($sortByPercent, $leagueID) {
	global $team, $teamsTable, $spiritScoresTable, $scoreSubmissionsTable, $datesTable, $leaguesTable;
	$teamsQuery=mysql_query("SELECT team_name,team_id,team_wins,team_losses,team_ties,team_most_recent_week_submitted,team_final_position,team_final_spirit_position FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0
		AND team_dropped_out = 0") or die('ERROR getting teams'.mysql_error());
	$teamCount=0;
	while($teamArray = mysql_fetch_array($teamsQuery)){ //goes through the db and selects all of each team's data for display in the standings
		$team[$teamCount] = new Team();
		$team[$teamCount]->teamName = $teamArray['team_name'];
		$team[$teamCount]->teamID = $teamArray['team_id'];
		$team[$teamCount]->teamWins = $teamArray['team_wins'];
		$team[$teamCount]->teamLosses = $teamArray['team_losses'];
		$team[$teamCount]->teamTies = $teamArray['team_ties'];
		$team[$teamCount]->teamSubmittedWeek = $teamArray['team_most_recent_week_submitted'];
		$team[$teamCount]->teamPoints = $team[$teamCount]->getPoints();
		$team[$teamCount]->teamFinalPosition = $teamArray['team_final_position'];
		$team[$teamCount]->teamFinalSpiritPosition = $teamArray['team_final_spirit_position'];
		$teamID = $team[$teamCount]->teamID;
		
		$spiritQuery = mysql_query("SELECT spirit_score_edited_value FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable ON 
			$scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
			INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
			INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_opp_team_id
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
			WHERE team_id = $teamID AND spirit_score_ignored = 0 AND spirit_score_edited_value > 0 
			AND date_week_number < league_playoff_week") or die ('ERROR getting spirits '.mysql_error());
		while($spiritArray = mysql_fetch_array($spiritQuery)) {
			$team[$teamCount]->teamSpiritNumbers++;
			$team[$teamCount]->teamSpiritTotal = $team[$teamCount]->addSpirit($spiritArray['spirit_score_edited_value']);
		}
		$team[$teamCount]->teamSpiritAverage = $team[$teamCount]->getSpiritAverage();
		
		$team[$teamCount]->teamPointsAvailable = $team[$teamCount]->getAvailablePoints();
		$team[$teamCount]->teamWinPercent = $team[$teamCount]->getWinPercent();
		
		$team[$teamCount]->setTitleSize();
		$teamCount++;
	}
	
	if($team[0]->teamFinalPosition == 0) {
		if ($sortByPercent == 0) {
			usort($team, "comparePoints");
		} else {
			usort($team, "comparePercent");
		}
	} else {
		usort($team, "comparePosition");
	}
	
	for($j= 0; $j< $teamCount; $j ++) {
		if($team[$j]->teamFinalPosition == 0) {
			$team[$j]->teamNumDropDown = getTeamNumDD($j+1, $teamCount);
			$spiritNum = 1;
			$curSpiritArray = array();
			//This figures out what spirit rank the current team is. It compares it to all the other spirits and if a certain 
			//spirit is higher, it means the current team spirit is one rank lower. It also keeps a tally of all the spirits so 
			//that when a duplicate spirit shows up, the teams rank isn't incremented.
			for($k=0;$k<$teamCount;$k++) {
				if($team[$k]->teamSpiritAverage > $team[$j]->teamSpiritAverage && !in_array($team[$k]->teamSpiritAverage, $curSpiritArray)) {
					$spiritNum++;
					array_push($curSpiritArray, $team[$k]->teamSpiritAverage);
				}
			}		 
			$team[$j]->teamSpiritDropDown = getTeamNumDD($spiritNum, $teamCount);
		} else {
			$team[$j]->teamNumDropDown = getTeamNumDD($team[$j]->teamFinalPosition, $teamCount);
			$team[$j]->teamSpiritDropDown = getTeamNumDD($team[$j]->teamFinalSpiritPosition, $teamCount);
		}
	}
	return $teamCount;
}

function loadPlayoffScoreSubmissions($sportID, $leagueID) {
	global $leaguesTable, $teamsTable, $scoreSubmissionsTable, $datesTable, $spiritScoresTable, $games, $matches;
	//League based variable
	$leagueArray = mysql_fetch_array(mysql_query("SELECT league_num_of_matches,league_num_of_games_per_match,league_season_id,league_playoff_week,league_sort_by_win_pct FROM $leaguesTable WHERE league_id = $leagueID"));
	$matches = $leagueArray['league_num_of_matches'];
	$games = $leagueArray['league_num_of_games_per_match'];
	$seasonID = $leagueArray['league_season_id'];
	$playoffWeek = $leagueArray['league_playoff_week'];
	$sortByPercent = $leagueArray['league_sort_by_win_pct'];
	
	$teamNames = array();
	$teamsQuery = mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 
			ORDER BY team_num_in_league ASC") or die('ERROR getting teams '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$teamNames[$teamArray['team_id']] = $teamArray['team_name'];
	}
	
	
	if ($leagueID != 0) {
		$teamsQuery = mysql_query("SELECT team_id,team_name,team_wins,team_losses,team_ties FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 
			AND team_dropped_out = 0 ORDER BY team_num_in_league ASC") or die('ERROR getting teams '.mysql_error());
		$teamCounter=0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$team[$teamCounter] = new Team();
			$team[$teamCounter]->teamID = $teamArray['team_id'];
			$team[$teamCounter]->teamName = $teamArray['team_name'];
			$team[$teamCounter]->teamWins = $teamArray['team_wins'];
			$team[$teamCounter]->teamLosses = $teamArray['team_losses'];
			$team[$teamCounter]->teamTies = $teamArray['team_ties'];
			$team[$teamCounter]->teamPoints = $team[$teamCounter]->getPoints();
			$team[$teamCounter]->teamPointsAvailable = $team[$teamCounter]->getAvailablePoints();
			$team[$teamCounter]->teamWinPercent = $team[$teamCounter]->getWinPercent();
			$teamID = $team[$teamCounter]->teamID;
			$spiritQuery = mysql_query("SELECT spirit_score_edited_value FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable 
				ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
				INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
				INNER JOIN $teamsTable ON $scoreSubmissionsTable.score_submission_opp_team_id = $teamsTable.team_id
				INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
				WHERE team_id = $teamID AND spirit_score_ignored = 0 
				AND (spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND 
				spirit_score_edited_value > 0 AND date_week_number < league_playoff_week") 
				or die ('ERROR getting spirits '.mysql_error());
			while($spiritArray = mysql_fetch_array($spiritQuery)) {
				$team[$teamCounter]->teamSpiritNumbers++;
				$team[$teamCounter]->teamSpiritTotal = $team[$teamCounter]->addSpirit($spiritArray['spirit_score_edited_value']);
			}
			$team[$teamCounter]->teamSpiritAverage = $team[$teamCounter]->getSpiritAverage();
			
			$teamIDNumber = $team[$teamCounter]->teamID; //queries give troubles when comparing to array elements
			$matchesArray = mysql_query("SELECT score_submission_result,score_submission_score_us,score_submission_score_them,score_submission_opp_team_id FROM $scoreSubmissionsTable INNER JOIN $datesTable
				ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
				WHERE score_submission_team_id = $teamIDNumber AND score_submission_ignored = 0 AND date_week_number >= $playoffWeek
				ORDER BY score_submission_id ASC") or die ("Error: ".mysql_error());
			$gameNum = 0;
			for($i=0;$i<$matches;$i++) {
				for($j=0;$j<$games;$j++) {
					if($scoreNode = mysql_fetch_array($matchesArray)) {
						$team[$teamCounter]->gameResult[$gameNum] = $scoreNode['score_submission_result'];
						$team[$teamCounter]->scoreUs[$gameNum] = $scoreNode['score_submission_score_us'];
						$team[$teamCounter]->scoreThem[$gameNum] = $scoreNode['score_submission_score_them'];
						$team[$teamCounter]->oppTeamID[$gameNum] = $scoreNode['score_submission_opp_team_id'];
						$team[$teamCounter]->oppTeamName[$gameNum] = $teamNames[$team[$teamCounter]->oppTeamID[$gameNum]];
					} else {
						$team[$teamCounter]->gameResult[$gameNum] = 0;
						$team[$teamCounter]->scoreUs[$gameNum] = 0;
						$team[$teamCounter]->scoreThem[$gameNum] = 0;
						$team[$teamCounter]->oppTeamID[$gameNum] = 0;
						$team[$teamCounter]->oppTeamName[$gameNum] = '';
					}
					$gameNum++;
				}
			}
			$teamCounter++;
		}
		if ($sortByPercent == 0) {
			usort($team, "comparePoints");
		} else {
			usort($team, "comparePercent");
		}
		return $team;
	} else {
		return 0;
	}
}

/** ALL THESE ARE USED TO SORT TEAM BY RANK **/

function getHeadToHead($teamOne, $teamTwo) {
	global $scoreSubmissionsTable;
	$submissionsQuery = mysql_query("SELECT score_submission_result FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamOne 
		AND score_submission_opp_team_id = $teamTwo AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$winLoss = 0;
	while($submission = mysql_fetch_array($submissionsQuery)) {
		if($submission['score_submission_result'] == 2) {
			$winLoss++;
		} else if ($submission['score_submission_result'] == 1) {
			$winLoss--;
		} 
	}
	return $winLoss;
}

function getCommonPlusMinus($teamOne, $teamTwo) {
	global $scoreSubmissionsTable;
	$oppTeamOne = array();
	$teamOneFor = array();
	$teamOneAgainst = array();
	$plusMinusOne = 0;
	$plusMinusTwo = 0;
	$submissionsQuery = mysql_query("SELECT score_submission_opp_team_id,score_submission_score_us,score_submission_score_them FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamOne 
		AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$count = 0;
	while($submission = mysql_fetch_array($submissionsQuery)) {
		$oppTeamOne[$count] = $submission['score_submission_opp_team_id'];
		$teamOneFor[$count] = $submission['score_submission_score_us'];
		$teamOneAgainst[$count++] = $submission['score_submission_score_them'];
	}
		
	$submissionsQuery = mysql_query("SELECT score_submission_opp_team_id,score_submission_score_us,score_submission_score_them FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamTwo
		AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	
	while($submission = mysql_fetch_array($submissionsQuery)) {
		for($i = 0; $i < count($oppTeamOne); $i++) {
			if($submission['score_submission_opp_team_id'] == $oppTeamOne[$i]) {
				$plusMinusOne += $submission['score_submission_score_us'] - $submission['score_submission_score_them'];
				$plusMinusTwo += $teamOneFor[$i] - $teamOneAgainst[$i];
			}
		}
	}
	return $plusMinusOne - $plusMinusTwo;
}

function comparePoints($a, $b) {
    if ($a->teamPoints == $b->teamPoints) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			$headToHead = getHeadToHead($a->teamID, $b->teamID);
			if($headToHead == 0) {
				$commonPlusMinus = getCommonPlusMinus($a->teamID, $b->teamID);
			} else {
				return $headToHead;
			}
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->teamPoints > $b->teamPoints) ? -1 : 1;
}

function comparePercent($a, $b) {
	global $scoreSubmissionsTable;
	if ($a->teamWinPercent == $b->teamWinPercent) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			$submissionsQuery = mysql_query("SELECT score_submission_result FROM $scoreSubmissionsTable WHERE score_submission_team_id = ".$a->teamID." AND score_submission_opp_team_id = ".$b->teamID."
				AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") or die('ERROR getting score submissions - '.mysql_error());
			$winLoss = 0;
			while($submission = mysql_fetch_array($submissionsQuery)) {
				if($submission['score_submission_result'] == 1) {
					$winLoss++;
				} else if ($submission['score_submission_result'] == 2) {
					$winLoss--;
				} 
			}
			return $winLoss;
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->teamWinPercent > $b->teamWinPercent) ? -1 : 1;
}

function comparePosition($a, $b) {
    if ($a->teamFinalPosition == $b->teamFinalPosition) {
        return 0;
    }
    return ($a->teamFinalPosition < $b->teamFinalPosition) ? -1 : 1;
}

function getTeamNumDD($teamNumInLeague, $teamNum) {
	$teamNumDD = '';
	for($i=1;$i<=$teamNum;$i++) {
		if($i==$teamNumInLeague){
			$teamNumDD.="<option selected value=$i>$i</option>";
		}else{
			$teamNumDD.="<option value=$i>$i</option>";
		}
	}
	return $teamNumDD;
} ?>