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
if(($isIndividual = $_GET['isIndividual']) == '') {
	$isIndividual = 1;
}
function getPostPlayerData() {
	global $playerFirstName, $playerLastName, $playerEmail, $playerGender, $playerPhoneNum, $isFinalized, $numPlayers;
	
	for($i=0;$i<$numPlayers;$i++) {
		$playerFirstName[$i] = $_POST['firstName'][$i];
		$playerLastName[$i] = $_POST['lastName'][$i];
		$playerEmail[$i] = $_POST['email'][$i];
		$playerGender[$i] = $_POST['gender'][$i];
		$playerPhoneNum[$i] = $_POST['phoneNum'][$i];
		if(($isFinalized[$i] = $_POST['isFinalized'][$i]) == '') {
			$isFinalized[$i] = 1;
		}
	}
}

function getTeamDD($leagueID, $teamID) {
	global $teamsTable;
	$teamsDropDown = '';
	
	if($leagueID != 0) {
		//teams in dropdown
		$teamsQuery=mysql_query("SELECT team_name, team_id FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league");
		while($team = mysql_fetch_array($teamsQuery)) {
			if ($team['team_id'] == $teamID) {
				$teamsDropDown.= "<option selected='selected' value=$team[team_id]>$team[team_name]</option>";
			} else {
				$teamsDropDown.= "<option value=$team[team_id]>$team[team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}?>