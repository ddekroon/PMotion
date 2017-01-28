<?
        
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

function getTeamsData($leagueID) {
	global $teamsTable, $team;
	
    if($leagueID == 0) {
        return;
    }
	$numTeams = 0;
	
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC") or die('ERROR getting team players '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
	    $team[$numTeams] = new Team();
		$team[$numTeams]->teamID = $teamArray['team_id'];
		$team[$numTeams]->teamName = $teamArray['team_name'];
		$team[$numTeams]->teamWins = $teamArray['team_wins'];
		$team[$numTeams]->teamLosses = $teamArray['team_losses'];
		$team[$numTeams]->teamTies = $teamArray['team_ties'];
		$numTeams++;
	}
	return $numTeams;
}

function getSportDD($sportID) {
	global $sportsTable;
	$sportDropDown ='';
	//leagues in dropdown
	$sportsQuery=mysql_query("SELECT * FROM $sportsTable ORDER BY sport_id ASC") or die("ERROR getting sports DD ".mysql_error());
	while($sport = mysql_fetch_array($sportsQuery)) {
		if($sport['sport_id']==$sportID){
			$sportDropDown.="<option selected value= $sport[sport_id]>$sport[sport_name]</option><BR>";
		}else{
			$sportDropDown.="<option value= $sport[sport_id]>$sport[sport_name]</option>";
		}
	}
	return $sportDropDown;
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
}

function getLeagueDD($leagueID, $sportID) {
	global $leaguesTable, $seasonsTable;
	$leagueDropDown = '';
	$pastLeagueID = 0;
	//leagues in dropdown
	$leaguesQuery=mysql_query("SELECT * FROM $leaguesTable INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
					   WHERE ($seasonsTable.season_available_score_reporter = 1 OR $seasonsTable.season_available_registration = 1) 
					   AND league_sport_id = $sportID ORDER BY season_id DESC, league_day_number ASC, league_name ASC") or die("ERROR getting leagues DD ".mysql_error());
	while($league = mysql_fetch_array($leaguesQuery)) {
		$dayNumber = dayString($league['league_day_number']);
		if($league['season_id'] != $pastLeagueID && $pastLeagueID != 0) {
			$leagueDropDown.="<option value=0>---------$league[season_name]---------</option>";
		}
		$pastLeagueID = $league['season_id'];
		if($league['league_id']==$leagueID){
			$leagueDropDown.="<option selected value= $league[league_id]>$league[league_name] - $dayNumber</option><BR>";
		}else{
			$leagueDropDown.="<option value= $league[league_id]>$league[league_name] - $dayNumber</option>";
		}
	}
	return $leagueDropDown;
}