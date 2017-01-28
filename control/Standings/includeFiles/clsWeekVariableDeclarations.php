<?php 

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($dateID = $_GET['dateID']) == '') {
	$dateQuery = mysql_query("SELECT date_id FROM $datesTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $datesTable.date_season_id 
		WHERE date_sport_id = league_sport_id AND date_day_number = league_day_number 
		AND date_week_number = league_week_in_score_reporter AND league_id = $leagueID ORDER BY date_week_number")
		or die('ERROR getting default date - '.mysql_error());
	$dateArray = mysql_fetch_array($dateQuery);
	$dateID = $dateArray['date_id'];
}

function getPostData($leagueID) {
	global $postTeams, $teamNames, $numGames, $numMatches;
	$numGames = $_POST['numGames'];
	$numMatches = $_POST['numMatches'];
	for($i=0;$i<count($_POST['teamID']); $i++) {
		$postTeams[$i] = new Team();
		$teamID = $_POST['teamID'][$i];
		$postTeams[$i]->teamID = $teamID;
		$postTeams[$i]->teamOppTeamID1 = $_POST['oppTeamID1'][$i];
		$postTeams[$i]->teamOppTeamID2 = $_POST['oppTeamID2'][$i];
		$counter = 0;
		for($j = 0; $j < $numMatches; $j++) {
			for($k=0; $k<$numGames; $k++) {
				$postTeams[$i]->teamOppSubmission[$j][$k] = $_POST['result'][$teamID][$counter++];
			}
		}
	}	
}

function getWeekDD($dateID, $leagueID) {
	global $datesTable, $leaguesTable;
	$datesDropDown = '<option value=0>-- Weeks --</option>';
	if($leagueID > 0) {
		$datesQuery=mysql_query("SELECT date_id, date_description FROM $datesTable 
			INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $datesTable.date_season_id
			WHERE $leaguesTable.league_id = $leagueID AND $datesTable.date_day_number = $leaguesTable.league_day_number
			AND date_sport_id = league_sport_id ORDER BY date_week_number ASC") or die('ERROR getting weeks '.mysql_error());
		while($date = mysql_fetch_array($datesQuery)) {
			if($date['date_id']==$dateID){
				$datesDropDown.="<option selected value= $date[date_id]>$date[date_description]</option><BR>";
			}else{
				$datesDropDown.="<option value= $date[date_id]>$date[date_description]</option>";
			}
		}
	}
	return $datesDropDown;
}

function getOppTeamDD($leagueID, $oppTeamID) {
	global $teamsTable;
	$teamsDropDown = '';

	//teams in dropdown
	$teamsQuery=mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE (team_league_id = $leagueID AND team_num_in_league > 0) 
		OR team_id = 1 ORDER BY team_num_in_league ASC, team_id DESC");
	while($team = mysql_fetch_array($teamsQuery)) {
		//print $oppTeamID[$i*$games].'<br />';
		if($team['team_id']==$oppTeamID) {
			$teamsDropDown.= "<option selected value=$team[team_id]>$team[team_name]</option>";
		} else {
			$teamsDropDown.= "<option value=$team[team_id]>$team[team_name]</option>";
		}
	}
	return $teamsDropDown;
}

function getResultDD($result) {
	$resultDD = '<option ';
	$resultDD .= $result==0?'selected':'';
	$resultDD .= ' value=0>No Result</option>';
	$resultDD .= '<option ';
	$resultDD .= $result==1?'selected':'';
	$resultDD .= ' value=1>Won</option>';	
	$resultDD .= '<option ';
	$resultDD .= $result==2?'selected':'';
	$resultDD .= ' value=2>Lost</option>';
	$resultDD .= '<option ';
	$resultDD .= $result==3?'selected':'';
	$resultDD .= ' value=3>Tied</option>';	
	$resultDD .= '<option ';
	$resultDD .= $result==4?'selected':'';
	$resultDD .= ' value=4>Cancelled</option>';
	$resultDD .= '<option ';
	$resultDD .= $result==5?'selected':'';
	$resultDD .= ' value=5>Practice</option>';
	return $resultDD;
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