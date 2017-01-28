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
        
if(($tourneyID = $_GET['tournamentID']) == '') {
	$tourneyID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 10000;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}

function getDefaultInfo($tourneyID) {
	global $tournamentsTable;
	
	if($tourneyID == 0) {
		return;
	}
	$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable WHERE tournament_id = $tourneyID") or die('ERROR getting tourney info - '.mysql_error());
	$tourneyArray = mysql_fetch_array($tourneyQuery);
	$tourneyObj = new Tournament();
	$tourneyObj->tourneyID = $tourneyArray['tournament_id'];
	$tourneyObj->tourneyName = $tourneyArray['tournament_name'];
	$tourneyObj->tourneyIsCards = $tourneyArray['tournament_is_cards'];
	$tourneyObj->tourneyIsLeagues = $tourneyArray['tournament_is_leagues'];
	$tourneyObj->tourneyNumLeagues = $tourneyArray['tournament_num_leagues'];
	$tourneyObj->tourneyLeagueNames = explode('%', $tourneyArray['tournament_league_names']);
	$tourneyObj->tourneyIsTeams = $tourneyArray['tournament_is_teams'];
	$tourneyObj->tourneyNumTeams = explode('%', $tourneyArray['tournament_num_teams']);
	$tourneyObj->tourneyIsPlayers = $tourneyArray['tournament_is_players'];
	$tourneyObj->tourneyNumPlayers = explode('%', $tourneyArray['tournament_num_players']);
	$tourneyObj->tourneyIsExtraField = $tourneyArray['tournament_is_extra_field'];
	$tourneyObj->tourneyExtraFieldName = $tourneyArray['tournament_extra_field_name'];
	$tourneyObj->tourneyNumRedCards = explode('%', $tourneyArray['tournament_num_red_cards']);
	$tourneyObj->tourneyNumBlackCards = explode('%', $tourneyArray['tournament_num_black_cards']);
	$tourneyObj->tourneyDateOpen = $tourneyArray['tournament_registration_open'];
	$tourneyObj->tourneyDateClosed = $tourneyArray['tournament_registration_closed'];
	$tourneyObj->tourneyDatePlayed = $tourneyArray['tournament_game_day'];
	$tourneyObj->tourneyNumDays = $tourneyArray['tournament_num_days'];
	$tourneyObj->tourneyNumRunning = $tourneyArray['tournament_num_running'];
	return $tourneyObj;
	
}

function getTeamPlayerData($teamID) {
	global $tournamentPlayersTable;
	$playerObj = array();
	$numPlayers = 0;
	if($teamID == 0) {
		return;
	}
	
	$playersQuery = mysql_query("SELECT * FROM $tournamentPlayersTable WHERE tournament_player_team_id = $teamID ORDER BY tournament_player_id ASC") 
		or die('ERROR getting team players '.mysql_error());
	while($playerArray = mysql_fetch_array($playersQuery)) {
		$playerObj[$numPlayers] = new Player();
		$playerObj[$numPlayers]->playerID = $playerArray['tournament_player_id'];
		$playerObj[$numPlayers]->playerFirstName = $playerArray['tournament_player_firstname'];
		$playerObj[$numPlayers]->playerLastName = $playerArray['tournament_player_lastname'];
		$playerObj[$numPlayers]->playerGender = $playerArray['tournament_player_gender'];
		$playerObj[$numPlayers]->playerNote = $playerArray['tournament_player_note'];
		$playerObj[$numPlayers]->playerEmail = $playerArray['tournament_player_email'];
		$playerObj[$numPlayers]->playerTourneyID = $playerArray['tournament_player_tournament_id'];
		$playerObj[$numPlayers]->playerLeagueID = $playerArray['tournament_player_league_id'];
		$playerObj[$numPlayers]->playerTeamID = $playerArray['tournament_player_team_id'];
		$numPlayers++;
	}
	
	return $playerObj;
}

function getTeamWaiting($teamID) {
	global $tournamentTeamsTable;
	$teamQuery = mysql_query("SELECT tournament_team_is_waiting as isWaiting FROM $tournamentTeamsTable 
		WHERE tournament_team_id = $teamID LIMIT 1") or die('ERROR getting team '.mysql_error());
	$teamArray = mysql_fetch_array($teamQuery);
	return $teamArray['isWaiting'];
}


function getTournamentDD($tourneyID) {
	global $tournamentsTable;
	$tournamentDropDown ='';
	//leagues in dropdown
	$tournamentsQuery=mysql_query("SELECT * FROM $tournamentsTable WHERE tournament_is_teams = 1 ORDER BY tournament_id ASC") or die("ERROR getting tourneys DD ".mysql_error());
	while($tournament = mysql_fetch_array($tournamentsQuery)) {
		if($tournament['tournament_id'] == $tourneyID){
			$tournamentDropDown.="<option selected value= $tournament[tournament_id]>$tournament[tournament_name]</option><BR>";
		}else{
			$tournamentDropDown.="<option value= $tournament[tournament_id]>$tournament[tournament_name]</option>";
		}
	}
	return $tournamentDropDown;
}

function getLeagueDD($numLeagues, $leagueNames, $leagueID) {
	$leagueDropDown = '';

	for($i=0;$i<$numLeagues;$i++) {
		if($i == $leagueID) {
			$leagueDropDown.="<option selected value= $i>$leagueNames[$i]</option>";
		}else{
			$leagueDropDown.="<option value= $i>$leagueNames[$i]</option>";
		}
	}
	return $leagueDropDown;
}

function getTeamDD($tourneyID, $isLeagues, $leagueID, $teamID) {
	global $tournamentTeamsTable;
	$teamsDropDown = '';
	if($isLeagues == 1) {
		$leagueFilter = "AND tournament_team_league_id = $leagueID";
		$teamNumFilter = 'AND tournament_team_num_in_league > 0';
		$orderFilter = 'tournament_team_num_in_league ASC';
	} else {
		$leagueFilter = '';
		$teamNumFilter = 'AND tournament_team_num_in_tournament > 0';
		$orderFilter = 'tournament_team_num_in_tournament ASC';
	}
	
	if($tourneyID != 0) {
		//teams in dropdown
		$teamsQuery = mysql_query("SELECT * FROM $tournamentTeamsTable WHERE tournament_team_tournament_id = $tourneyID $leagueFilter $teamNumFilter ORDER BY $orderFilter")
			or die('ERROR gettings teams - '.mysql_error());
		while($team = mysql_fetch_array($teamsQuery)) {
			if($team['tournament_team_id'] == $teamID){
				$teamsDropDown.= "<option selected value=$team[tournament_team_id]>$team[tournament_team_name]</option>";
			} else {
				$teamsDropDown.= "<option value=$team[tournament_team_id]>$team[tournament_team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}?>