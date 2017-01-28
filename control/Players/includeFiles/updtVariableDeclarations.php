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
} else {
	$newLeagueID = $leagueID;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
} else {
	$newTeamID = $teamID;
}
if(($playerID = $_GET['playerID']) == '') {
	$playerID = 0;
}
if($_GET['newLeagueID'] != '') {
	$newLeagueID =$_GET['newLeagueID'];
}
if($_GET['newTeamID'] != '') {
	$newTeamID = $_GET['newTeamID'];
}
if($leagueID > 0) {
	$seasonQuery = mysql_query("SELECT season_id FROM $leaguesTable INNER JOIN $seasonsTable ON 
		$seasonsTable.season_id = $leaguesTable.league_season_id WHERE league_id = $leagueID") or die('ERROR - '.mysql_error());
} else {
	$seasonQuery = mysql_query("SELECT season_id FROM $seasonsTable WHERE season_available_score_reporter = 1") 
		or die('ERROR - '.mysql_error());
}
$seasonArray = mysql_fetch_array($seasonQuery);
$seasonID = $seasonArray['season_id'];


function getDBPlayerData($playerID) {
	global $player, $playersTable, $individualsTable, $userTable, $teamsTable;

	if($playerID != 0) {
		$playerQuery = mysql_query("SELECT player_firstname, player_lastname, player_email, player_phone, player_skill, player_sex, player_note, player_is_captain, 
									player_is_individual
									FROM $playersTable WHERE player_id = $playerID") or die ('ERROR getting player DB data '.mysql_error());
		$playerArray = mysql_fetch_array($playerQuery);
		$player = new Player();
		$player->playerFirstName = $playerArray['player_firstname'];
		$player->playerLastName = $playerArray['player_lastname'];
		$player->playerEmail = $playerArray['player_email'];
		$player->playerPhone = $playerArray['player_phone'];
		$player->playerSkill = $playerArray['player_skill'];
		$player->playerGender = $playerArray['player_sex'];
		$player->playerNote = $playerArray['player_note']; 
		$player->playerIsCaptain = $playerArray['player_is_captain'];
		$player->playerIsIndividual = $playerArray['player_is_individual'];
		if($player->playerIsCaptain == 1) {
			$captainQuery = mysql_query("SELECT user_id, user_email FROM $userTable 
				INNER JOIN $teamsTable ON $teamsTable.team_managed_by_user_id = $userTable.user_id 
				INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id 
				WHERE player_id = $playerID") or die('ERROR getting user data - '.mysql_error());	
			$captainArray = mysql_fetch_array($captainQuery);
			$player->playerUserEmail = $captainArray['user_id'];
			$player->playerUserEmail = $captainArray['user_email'];
			
		}
		if($player->playerIsIndividual == 1) {
			$individualQuery = mysql_query("SELECT individual_small_group_id FROM $individualsTable WHERE individual_player_id = $playerID")
				or die('ERROR getting individual data - '.mysql_error());
			if(mysql_num_rows($individualQuery) > 0) {
				$individualArray = mysql_fetch_array($individualQuery);
				$player->playerIndividualGroup = $individualArray['individual_small_group_id'];
			} else {
				$player->playerIndividualGroup = 0;
			}
		}
	}
}

function getPostPlayerData() {
	global $player, $newTeamID, $newLeagueID;

	$player = new Player();
	$player->playerFirstName = $_POST['firstName'];
	$player->playerLastName = $_POST['lastName'];
	$player->playerEmail = $_POST['email'];
	$player->playerPhone = $_POST['phone'];
	$player->playerGender = $_POST['playerGender'];
	$player->playerNote = $_POST['note']; 
	$player->playerIsCaptain = $_POST['isCaptain'];
	if($player->playerIsCaptain == 1) {
		$player->playerUserID = $_POST['userID'];
		$player->playerUserEmail = $_POST['userEmail'];
	}
	if(($player->playerIsIndividual = $_POST['isIndividual']) == '') {
		$player->playerIsIndividual = 0;
	}
	if(($player->playerIndividualGroup = $_POST['moveGroup']) == '') {
		$player->playerIndividualGroup = 0;
	}
	$newLeagueID = $_POST['newLeagueID'];
	$newTeamID = $_POST['newTeamID'];
}

function getTeamDD($leagueID, $teamID) {
	global $teamsTable;
	$teamsDropDown = '';
	
	if($leagueID != 0) {
		//teams in dropdown
		$teamsQuery=mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league");
		while($team = mysql_fetch_array($teamsQuery)) {
			if ($team['team_id'] == $teamID) {
				$teamsDropDown.= "<option selected='selected' value=$team[team_id]>$team[team_name]</option>";
			} else {
				$teamsDropDown.= "<option value=$team[team_id]>$team[team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}

function getPlayerDD($leagueID, $teamID, $playerID) {
	global $playersTable, $individualsTable;
	$playersDropDown = '';
	
	if($teamID != 0) {
		$playersQuery=mysql_query("SELECT player_id, player_firstname, player_lastname FROM $playersTable WHERE player_team_id = $teamID ORDER BY player_id") or die('ERROR getting player data from team '.mysql_error());
		while($player = mysql_fetch_array($playersQuery)) {
			if ($player['player_id'] == $playerID) {
				$playersDropDown.= "<option selected='selected' value=$player[player_id]>$player[player_firstname] $player[player_lastname]</option>\n";
			} else {
				$playersDropDown.= "<option value=$player[player_id]>$player[player_firstname] $player[player_lastname]</option>\n";
			}
		}
	} else {
		$playersQuery=mysql_query("SELECT player_id, player_firstname, player_lastname FROM $playersTable INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id
			WHERE individual_preferred_league_id = $leagueID AND player_team_id = 0 ORDER BY player_id") 
			or die('ERROR getting player data from league '.mysql_error());
		while($player = mysql_fetch_array($playersQuery)) {
			if ($player['player_id'] == $playerID) {
				$playersDropDown.= "<option selected='selected' value=$player[player_id]>$player[player_firstname] $player[player_lastname]</option>\n";
			} else {
				$playersDropDown.= "<option value=$player[player_id]>$player[player_firstname] $player[player_lastname]</option>\n";
			}
		}
	}
	return $playersDropDown;
}?>