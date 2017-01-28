<?php
  
//Magic quotes by default is on, this function takes out all backslashes in the superglobal variables
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
if(($dateID = $_GET['dateID']) == '') {
	$dateID = 0;
}
	
$games=0;
$matches=0;
$dayNumber = 0;

function loadDBVariables($sportID, $leagueID, $teamID, $dateID) {

	global $dayNumber, $matches, $games, $teamName,  $dayNumber, $seasonID, $oppTeamsDropDown, $dbTeamNote;
	global $teamScoreSubmissionID, $dbTeamOppTeamID, $dbTeamGameResult, $dbTeamSpiritSubmission, $teamID, $dbTeamSpiritID;
	global $datesTable, $leaguesTable, $teamsTable, $seasonsTable, $sportsTable, $scoreSubmissionsTable, $spiritScoresTable;
	
	//League based variable
	if($leagueID != 0){
		$leagueArray = query("SELECT league_day_number,league_num_of_matches, league_num_of_games_per_match, league_season_id FROM $leaguesTable WHERE league_id = $leagueID");
		$dayNumber = $leagueArray['league_day_number'];
		$matches = $leagueArray['league_num_of_matches'];
		$games = $leagueArray['league_num_of_games_per_match'];
		$seasonID = $leagueArray['league_season_id'];
	}
	
	if ($leagueID != 0 && $dateID != 0) {
		$teamsQuery = mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC")
		or die('ERROR getting teams '.mysql_error());
		$teamCounter=0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$teamID[$teamCounter] = $teamArray['team_id'];
			$teamName[$teamCounter] = $teamArray['team_name'];
			$teamIDNumber = $teamID[$teamCounter]; //queries give troubles when comparing to array elements
			$matchesArray = mysql_query("SELECT score_submission_result, score_submission_id, score_submission_opp_team_id, notes FROM $scoreSubmissionsTable 
				WHERE score_submission_team_id = $teamIDNumber AND score_submission_date_id = $dateID AND score_submission_ignored = 0 ")
				or die ("Error: ".mysql_error());
			$spiritQuery = mysql_query("SELECT spirit_score_edited_value, spirit_score_id FROM $spiritScoresTable 
				INNER JOIN $scoreSubmissionsTable ON $spiritScoresTable.spirit_score_score_submission_id = $scoreSubmissionsTable.score_submission_id
				WHERE score_submission_team_id = $teamIDNumber AND score_submission_date_id = $dateID") or die('ERROR getting spirit scores '.mysql_error());
			
			$gameNum = 0;
			for($i=0;$i<$matches;$i++) {
				for($j=0;$j<$games;$j++) {
					if($scoreNode = mysql_fetch_array($matchesArray)) {
						$dbTeamGameResult[$teamCounter][$gameNum] = $scoreNode['score_submission_result'];
						$teamScoreSubmissionID[$teamCounter][$gameNum] = $scoreNode['score_submission_id'];
						$dbTeamOppTeamID[$teamCounter][$i] = $scoreNode['score_submission_opp_team_id'];
						$dbTeamNote[$teamCounter] = $scoreNode['notes'];
					} else {
						$teamScoreSubmissionID[$teamCounter][$gameNum] = 0;
						$dbTeamOppTeamID[$teamCounter][$i] = 0;
						$dbTeamGameResult[$teamCounter][$gameNum] = 0;
					}
					if($j == 0) {
						$spiritNode = mysql_fetch_array($spiritQuery);
						//$spiritID = $spiritNode['spirit_score_score_submission_id'];
						//$realSpirit = mysql_query("SELECT spirit_score_edited_value FROM $spiritScoresTable WHERE spirit_score_score_submission_id = '$spiritID'") or die('ERROR getting spirit scores '.mysql_error());
						//$realSpirit = mysql_fetch_array($realSpirit);
						if (($dbTeamSpiritSubmission[$teamCounter][$i] = $spiritNode['spirit_score_edited_value']) == '') {
							$dbTeamSpiritSubmission[$teamCounter][$i] = 0;
						}
						if (($dbTeamSpiritID[$teamCounter][$i] = $spiritNode['spirit_score_id']) == '') {
							$dbTeamSpiritID[$teamCounter][$i] = 0;
						}
						/*if ($realSpirit['spirit_score_value'] != $dbTeamSpiritSubmission[$teamCounter][$i]) {
							print($realSpirit['spirit_score_edited_value'] . " ");
						}*/
					}
					$gameNum++;
				}
			}
			$oppTeamsDropDown[$teamCounter] = getOppTeamDD($leagueID, $matches, $dbTeamOppTeamID[$teamCounter]);
			$teamCounter++;
		}
		return $teamCounter;
	} else {
		return 0;
	}
}

function getSubmittedData($leagueID) {
	global $dayNumber, $matches, $games, $teamName,  $dayNumber, $seasonID, $oppTeamsDropDown, $teamNote;
	global $teamScoreSubmissionID, $teamOppTeamID, $teamGameResult, $teamSpiritSubmission;
	global $datesTable, $leaguesTable, $teamsTable, $seasonsTable, $sportsTable, $scoreSubmissionsTable, $spiritScoresTable;
	
	$matches = $_POST['matches'];
	$games = $_POST['games'];
	$dayNumber = $_POST['dayNumber'];
	$teamCount = $_POST['teamCount'];
	$seasonID = $_POST['seasonID'];
	
	for($k=0;$k<$teamCount;$k++) {
		$gameNum = 0;
		$teamName[$k]=$_POST['teamName'][$k];
		for ($i=0;$i<$matches;$i++) {
			$teamSpiritSubmission[$k][$i] = $_POST['spirit'][$k][$i];
			$teamOppTeamID[$k][$i] = $_POST['oppID'][$k][$i];
			for ($j=0;$j<$games;$j++) {
				$teamScoreSubmissionID[$k][$gameNum] = $_POST['teamScoreSubmissionID'][$k][$gameNum];
				$teamGameResult[$k][$gameNum] = $_POST['gameResult'][$k][$gameNum];
				$gameNum++;
			}
		}
		$oppTeamsDropDown[$k] = getOppTeamDD($leagueID, $matches, $teamOppTeamID[$k]);
		$teamNote[$k] = $_POST['note'][$k];
	}
}

function setVariables() {
	global $teamCount, $matches, $games;
	global $teamOppTeamID, $teamGameResult, $teamSpiritSubmission;
	global $dbTeamOppTeamID, $dbTeamGameResult, $dbTeamSpiritSubmission;
	
	for($k=0;$k<$teamCount;$k++) {
		$gameNum = 0;
		for ($i=0;$i<$matches;$i++) {
			$teamOppTeamID[$k][$i] = $dbTeamOppTeamID[$k][$i];
			$teamSpiritSubmission[$k][$i] = $dbTeamSpiritSubmission[$k][$i];
			for ($j=0;$j<$games;$j++) {
				$teamGameResult[$k][$gameNum] = $dbTeamGameResult[$k][$gameNum];
				$gameNum++;
			}
		}
	}
}
//Checks to see which teams data has been changed, returns a 2d array, 1st dimension being the team, 2nd being the game num
//KEY: 1 means opponent or spirit were changed
//	   2 the game result was changed
//	   3 the team hadn't submitted yet, changes from 0's.
function checkChanges() {
	global $teamCount, $matches, $games;
	global $teamOppTeamID, $teamGameResult, $teamSpiritSubmission, $teamNote, $dbTeamNote;
	global $dbTeamOppTeamID, $dbTeamGameResult, $dbTeamSpiritSubmission, $teamScoreSubmissionID;
	$editedArray = array();
	
	for($k=0;$k<$teamCount;$k++) {
		$gameNum = 0;
		for ($i=0;$i<$matches;$i++) {
			for ($j=0;$j<$games;$j++) {
				if ($teamSpiritSubmission[$k][$i] != $dbTeamSpiritSubmission[$k][$i]) {
					$editedArray[$k][$gameNum] = 1;
				}
				if ($teamOppTeamID[$k][$i] != $dbTeamOppTeamID[$k][$i]) {
					$editedArray[$k][$gameNum] = 1;
				}
				if($teamGameResult[$k][$gameNum] != $dbTeamGameResult[$k][$gameNum]) {
					$editedArray[$k][$gameNum] = 2;
				}
				if($teamScoreSubmissionID[$k][$gameNum] == 0 && ($teamGameResult[$k][$gameNum] != 0 || $teamOppTeamID[$k][$i] != 0 || $teamSpiritSubmission[$k][$i] != 0)) {
					$editedArray[$k][$gameNum] = 3;
				}

				if ($teamNote[$k] != $dbTeamNote[$k]) {
					$editedArray[$k][$gameNum] = 1;
				}
				$gameNum++;
			}
		}
	}
	
	return $editedArray;
}

function getOppTeamDD($leagueID, $matches, $oppTeamID) {
	global $teamsTable;
	$teamsDropDown = '';

	//teams in dropdown
	$teamsQuery=mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE (team_league_id = $leagueID AND team_num_in_league > 0) OR team_id = 1 ORDER BY team_num_in_league ASC, team_id DESC");
	while($team = mysql_fetch_array($teamsQuery)) {
		for($i=0;$i<$matches;$i++) {
			if($team['team_id']==$oppTeamID[$i]) {
				$teamsDropDown[$i].=  "<option selected value=$team[team_id]>$team[team_name]</option>";
			} else {
				$teamsDropDown[$i].=  "<option value=$team[team_id]>$team[team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}

function getWeeksDD($seasonID, $dateID) {
	global $datesTable, $seasonsTable, $dayNumber, $sportID;
	$datesDropDown = '';
	
	$datesQuery=mysql_query("SELECT date_id, date_description FROM $datesTable INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id 
					   WHERE season_available_score_reporter = 1 AND date_season_id = $seasonID AND date_day_number = $dayNumber AND date_sport_id = $sportID
					   ORDER BY date_week_number ASC") or die('ERROR getting weeks '.mysql_error());
	while($date = mysql_fetch_array($datesQuery)) {
		if($date['date_id']==$dateID){
			$datesDropDown.="<option selected value= $date[date_id]>$date[date_description]</option><BR>";
		}else{
			$datesDropDown.="<option value= $date[date_id]>$date[date_description]</option>";
		}
	}
	return $datesDropDown;
}
?>
