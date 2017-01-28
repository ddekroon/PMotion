<?php //Magic quotes by default is on, this function takes out all backslashes in the superglobal variables
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
			stripslashes($value);
        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
        
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}

if($teamID != 0) {
    $teamQuery = mysql_query("SELECT * FROM $teamsTable 
        INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id WHERE team_id = $teamID") 
        or die('ERROR getting team data - '.mysql_error());
    $teamArray = mysql_fetch_array($teamQuery);
    $teamWeek = $teamArray['team_most_recent_week_submitted'];
    $teamPicLink = $teamArray['league_pic_link'];
	$teamDroppedOut = $teamArray['team_dropped_out'];
	$teamName = $teamArray['team_name'];
}

function getTeamPlayerData($teamID) {
	global $player, $playersTable, $individualsTable;
	$numPlayers = 0;
	if($teamID == 0) {
		return;
	}
	
	$playersQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID ORDER BY player_id ASC") or die('ERROR getting team players '.mysql_error());
	while($playerArray = mysql_fetch_array($playersQuery)) {
		$player[$numPlayers] = new Player();
		$player[$numPlayers]->playerID = $playerArray['player_id'];
		$player[$numPlayers]->playerName = $playerArray['player_firstname'].' '.$playerArray['player_lastname'];
		$player[$numPlayers]->playerGender = $playerArray['player_sex'];
		$player[$numPlayers]->playerSkill = $playerArray['player_skill'];
		$player[$numPlayers]->playerNote = $playerArray['player_note'];
		$player[$numPlayers]->playerIsCaptain = $playerArray['player_is_captain'];
		$player[$numPlayers]->playerIsIndividual = $playerArray['player_is_individual'];
		$numPlayers++;
	}
	
	return $numPlayers;
}

function getAgentsData($leagueID) {
	global $agent, $individualsTable, $playersTable;
	$agentsNum = 0;
	
	if($leagueID == 0) {
		return;
	}
	
	$agentsQuery = mysql_query("SELECT * FROM $individualsTable INNER JOIN $playersTable ON $playersTable.player_id = $individualsTable.individual_player_id
		WHERE individual_preferred_league_id = $leagueID AND player_team_id = 0 AND individual_finalized = 1 ORDER BY individual_small_group_id ASC, player_id ASC")
		or die('ERROR getting free agents '.mysql_error());
	while($agentArray = mysql_fetch_array($agentsQuery)) {
		$agent[$agentsNum] = new Player();
		$agent[$agentsNum]->playerID = $agentArray['player_id'];
		$agent[$agentsNum]->playerName = $agentArray['player_firstname'].' '.$agentArray['player_lastname'];
		$agent[$agentsNum]->playerGender = $agentArray['player_sex'];
		$agent[$agentsNum]->playerGroupID = $agentArray['individual_small_group_id'];
		$agent[$agentsNum]->playerNote = $agentArray['player_note'];
		$agentsNum++;
	}
	return $agentsNum;
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
}

function getTeamDD($leagueID, $teamID) {
	global $teamsTable;
	$teamsDropDown = '';
	
	if($leagueID != 0) {
		//teams in dropdown
		$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league");
		while($team = mysql_fetch_array($teamsQuery)) {
			if($team['team_id'] == $teamID){
				$teamsDropDown.= "<option selected value=$team[team_id]>$team[team_name]</option>";
			} else {
				$teamsDropDown.= "<option value=$team[team_id]>$team[team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}?>