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

function getControlLeaguesDD($sportID, $leagueID) {
	global $leaguesTable, $seasonsTable;
	$leaguesDropDown = '<option value=0>-- League Name --</option>';
	$leaguesQuery=mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE ($seasonsTable.season_available_score_reporter = 1 OR $seasonsTable.season_available_registration = 1) 
		AND league_sport_id = $sportID ORDER BY season_id DESC, league_day_number ASC, league_name ASC") 
		or die("ERROR getting leagues DD ".mysql_error());
		
	while($league = mysql_fetch_array($leaguesQuery)) {
		$leagueDay = dayString($league['league_day_number']);
		if($league['season_id'] != $pastLeagueID) {
			$leaguesDropDown.="<option value=0>-- $league[season_name] --</option>";
		}
		$pastLeagueID = $league['season_id'];
		$league['league_id']==$leagueID?$selected = 1:$selected = 0;
		$leaguesDropDown.= getOption($league['league_id'], $league['league_name'], $league['league_full_teams'], 
			$league['league_full_individual_males'], $league['league_full_individual_females'], $leagueDay, $selected);
	}
	return $leaguesDropDown;
}

function getOption($leagueID, $leagueName, $fullTeams, $fullMales, $fullFemales, $leagueDay, $selected) {
	$leaguesDropDown = '';
	if($fullMales > 0 || $fullFemales > 0 || $fullTeams > 0) {
		$fullFilter = '- Full (';
	} else {
		$fullFilter = '';
	}
	$fullTeams > 0?$fullFilter.= 'T':'';
	$fullMales > 0?$fullFilter.= 'M':'';
	$fullFemales > 0?$fullFilter.='F':'';
	$fullMales > 0 || $fullFemales > 0 || $fullTeams > 0?$fullFilter .=')':$fullFilter.='';
	$selected == 1?$selectedFilter = 'selected':$selectedFilter = '';
	
	$leaguesDropDown.="<option $selectedFilter value= $leagueID>$leagueName $fullFilter - $leagueDay</option>";
	
	return $leaguesDropDown;
}

function getDatabaseTeams($leagueID) {
	global $teamsTable, $team, $playersTable, $unregTeam, $leaguesTable;
	
	if($leagueID > 0) {
		//Gets registered teams
		$teamsQuery = mysql_query("SELECT * FROM $teamsTable 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
			WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC") 
			or die('ERROR getting teams '.mysql_error());
		$teamNum = 0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$leagueIsSplit = $teamArray['league_is_split'];
			$team[$teamNum] = new Team();	
			$team[$teamNum]->teamName = $teamArray['team_name'];
			$team[$teamNum]->teamID = $teamArray['team_id'];
			if($leagueIsSplit == 0) {
				$team[$teamNum]->teamNumInLeague = $teamNum + 1;
				if($teamNum + 1 != $teamArray['team_num_in_league']) {
					mysql_query("UPDATE $teamsTable SET team_num_in_league = $teamNum + 1 WHERE 
						team_id = ".$team[$teamNum]->teamID) or die('ERROR updating team nums - '.mysql_error());
				}
			} else {
				$team[$teamNum]->teamNumInLeague = $teamArray['team_num_in_league'];
			}
			$team[$teamNum]->teamIsConvenor = $teamArray['team_is_convenor'];
			$team[$teamNum]->teamPaid = $teamArray['team_paid'];
			$team[$teamNum]->teamLeagueID = $teamArray['team_league_id'];
			$playerQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = ".$team[$teamNum]->teamID.
				' ORDER BY player_is_individual DESC LIMIT 1') or die ('ERROR getting player data - '.mysql_error());
			if(mysql_num_rows($playerQuery) == 0) { //team is empty, individual team
				$team[$teamNum]->teamHasIndividuals = 1;
			} else {
				$playerArray = mysql_fetch_array($playerQuery);
				if ($playerArray['player_is_individual'] == 1) {
					$team[$teamNum]->teamHasIndividuals = 1;
				}
			}
			$team[$teamNum]->teamDroppedOut = $teamArray['team_dropped_out'];
			$teamNum++;
			
		}
		for($i=0;$i<$teamNum;$i++) {
			$team[$i]->teamNumDropDown = getTeamNumDD($team[$i]->teamNumInLeague, $teamNum);
		}
		
		
		//Gets teams on the fence
		$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_finalized = 0 
			AND team_num_in_league = 0 ORDER BY team_id ASC") or die('ERROR getting teams '.mysql_error());
		$unregTeamNum = 0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$unregTeam[$unregTeamNum] = new Team();
			$unregTeam[$unregTeamNum]->teamID = $teamArray['team_id'];
			$unregTeam[$unregTeamNum]->teamName = $teamArray['team_name'];
			$captainQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id =".$unregTeam[$unregTeamNum]->teamID." AND player_is_captain = 1")
				or die('ERROR getting captain for team - '.$unregTeam[$unregTeamNum]->teamName);
			$captainArray = mysql_fetch_array($captainQuery);
			$unregTeam[$unregTeamNum]->teamCaptainFirstName = $captainArray['player_firstname'];
			$unregTeam[$unregTeamNum]->teamCaptainLastName = $captainArray['player_lastname'];
			$unregTeam[$unregTeamNum]->teamCaptainEmail = $captainArray['player_email'];
			$unregTeamNum++;
		}

	}
	return $teamNum;
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


function getAgents($leagueID) {
	global $playerName, $playerGender, $playerEmail, $individualsTable, $playersTable, $playerGroupID, $playerNote, $playerID;
	$agentsNum = 0;
	
	$agentsQuery = mysql_query("SELECT * FROM $individualsTable INNER JOIN $playersTable ON $playersTable.player_id = $individualsTable.individual_player_id
		WHERE individual_preferred_league_id = $leagueID AND player_team_id is NULL AND individual_finalized = 1 ORDER BY individual_small_group_id ASC, player_id ASC") 
		or die('ERROR getting free agents '.mysql_error());
	while($agentArray = mysql_fetch_array($agentsQuery)) {
		$playerID[$agentsNum] = $agentArray['player_id'];
		$playerName[$agentsNum] = $agentArray['player_firstname'].' '.$agentArray['player_lastname'];
		$playerGender[$agentsNum] = $agentArray['player_sex'];
		$playerEmail[$agentsNum] = $agentArray['player_email'];
		$playerNote[$agentsNum] = $agentArray['player_note'];
		$playerGroupID[$agentsNum] = $agentArray['individual_small_group_id'];
		$agentsNum++;
	}
	return $agentsNum;
}