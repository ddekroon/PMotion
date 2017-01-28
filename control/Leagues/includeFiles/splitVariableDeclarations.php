<?php 
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID= 0;
}

$sportsDD = getSportDD($sportID);
$leaguesDD = getLeaguesDD($sportID, -1, $leagueID);

function getTeamsData($leagueID) {
	global $teamsTable, $leaguesTable, $team, $spiritScoresTable, $scoreSubmissionsTable, $leagueNameOne, $leagueNameTwo;
	
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID") 
		or die('ERROR getting league data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	$leagueNameOne = $leagueArray['league_name'];
	$leagueNameTwo = $leagueArray['league_name'];
	$leagueSortByPct = $leagueArray['league_sort_by_win_pct'];
	
	
	$teamQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0") 
		or die('ERROR getting num teams - '.mysql_error());
	$numTeams = 0;
	while($newTeam = mysql_fetch_array($teamQuery)) {
		$team[$numTeams] = new Team();
		$team[$numTeams]->teamName = $newTeam['team_name'];
		$team[$numTeams]->teamID = $newTeam['team_id'];
		$team[$numTeams]->teamWins = $newTeam['team_wins'];
		$team[$numTeams]->teamLosses = $newTeam['team_losses'];
		$team[$numTeams]->teamTies = $newTeam['team_ties'];
		$team[$numTeams]->teamPoints = $team[$numTeams]->getPoints();
		$team[$numTeams]->teamPointsAvailable = $team[$numTeams]->getAvailablePoints();
		$teamID = $team[$numTeams]->teamID;
	
		$spiritQuery = mysql_query("SELECT * FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable 
			ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
			WHERE score_submission_opp_team_id = $teamID AND spirit_score_ignored = 0 AND (spirit_score_edited_value > 3.5 
			OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) AND spirit_score_edited_value > 0") 
			or die ('ERROR getting spirits '.mysql_error());
		while($spiritArray = mysql_fetch_array($spiritQuery)) {
			$team[$numTeams]->teamSpiritNumbers++;
			$team[$numTeams]->teamSpiritTotal = $team[$numTeams]->addSpirit($spiritArray['spirit_score_edited_value']);
		}
		$team[$numTeams]->teamSpiritAverage = $team[$numTeams]->getSpiritAverage();
		
		if ($leagueSortByPct == 0) {
			usort($team, "comparePoints");
		} else {
			usort($team, "comparePercent");
		}
		$numTeams++;
	}
	return $numTeams;
}

function getPostData() {
	global $teamID, $teamNewLeague, $teamName, $leagueNameOne, $leagueNameTwo;
	
	$leagueNameOne = $_POST['newLeagueOneName'];
	$leagueNameTwo = $_POST['newLeagueTwoName'];
	
	$numTeams = $_POST['numTeams'];
	for($i=0;$i<$numTeams;$i++) {
		$teamName[$i] = $_POST['teamName'][$i];
		$teamNewLeague[$i] = $_POST['teamNewLeague'][$i];
		$teamID[$i] = $_POST['teamID'][$i];
	}
	return $numTeams;
}

function getTeamDD($leagueID, $teamNum) {
	global $teamsTable;
	$teamsDropDown = '';
	$curTeamNum = 0;

	//teams in dropdown
	$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC, team_id DESC");
	while($team = mysql_fetch_array($teamsQuery)) {
		if($teamNum==$curTeamNum) {
			$teamsDropDown.=  "<option selected value=$team[team_id]>$team[team_name]</option>";
		} else {
			$teamsDropDown.=  "<option value=$team[team_id]>$team[team_name]</option>";
		}
		$curTeamNum++;
	}
	return $teamsDropDown;
}

function comparePoints($a, $b) {
    if ($a->getPoints() == $b->getPoints()) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			return 0;
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->getPoints() > $b->getPoints()) ? -1 : 1;
}

function comparePercent($a, $b) {
	if ($a->getWinPercent() == $b->getWinPercent()) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			return 0;
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->getWinPercent() > $b->getWinPercent()) ? -1 : 1;
}