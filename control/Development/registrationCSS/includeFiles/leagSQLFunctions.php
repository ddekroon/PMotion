<?php

function deleteTeams($teamCount) {
	global $teamsTable, $leagueID;
	
	if (isset($_POST['delete'])) {
		$effectedCount = 0;
		foreach($_POST['delete'] as $deleteNum) {
			$teamID = $_POST['teamID'][$deleteNum];
			mysql_query("UPDATE $teamsTable SET team_num_in_league = 0, team_finalized = 0 WHERE team_id = $teamID") 
				or die('ERROR updating teams '.mysql_error());
			fixTeamNumbers($leagueID, $deleteNum, $teamCount);
			$effectedCount++;
		}
		print 'Deletions complete, '.$effectedCount.' teams effected.';
	} else {
		print 'No teams selected to delete';
	}
}

function deleteAgents() {
	global $playersTable, $individualsTable;
	
	foreach($_POST['delAgent'] as $playerID) {
		mysql_query("DELETE FROM $playersTable WHERE player_id = $playerID") or die('ERROR deleting player - '.mysql_error());
		mysql_query("DELETE FROM $individualsTable WHERE individual_player_id = $playerID") 
			or die('ERROR deleting player - '.mysql_error());	
	}
	print 'Agents Deleted<br />';
}

function fixTeamNumbers($leagueID, $deleteNum, $teamCount) {
	global $teamsTable;
	
	for($i=$deleteNum+1; $i< $teamCount;$i++) {
		$teamID = $_POST['teamID'][$i];
		mysql_query("UPDATE $teamsTable SET team_num_in_league = team_num_in_league-1 WHERE team_id = $teamID") 
			or die('ERROR updating team numbers '.mysql_error());
	}
}

function updateTeamPictureNames() {
	global $teamsTable, $team;
	for($i = 0; $i < count($team); $i++) {
		$teamID = $team[$i]->teamID;
		if($team[$i]->teamNumInLeague < 10) {
			$teamPicString = $team[$i]->teamLeagueID.'-0'.$team[$i]->teamNumInLeague;
		} else {
			$teamPicString = $team[$i]->teamLeagueID.'-'.$team[$i]->teamNumInLeague;
		}
		mysql_query("UPDATE $teamsTable SET team_pic_name = '$teamPicString' WHERE team_id = $teamID")
			or die('ERROR updating pic name - '.mysql_error());
	}
}

//This function actually changes team #'s and their convenor status... too lazy to fix names
function updateTeamInfoDB($teamCount) {
	global $teamsTable;
	
	for($i=0;$i<$teamCount;$i++) {
		$teamNum = $_POST['teamNum'][$i];
		$teamID = $_POST['teamID'][$i];
		if($_POST['isConvenor'] != '') {
			if(in_array($teamID, $_POST['isConvenor'])) {
				$teamIsConvenor = 1;
			} else {
				$teamIsConvenor = 0;
			}
		} else {
			$teamIsConvenor = 0;
		}
		if($_POST['teamPaid'] != '') {
			if(in_array($teamID, $_POST['teamPaid'])) {
				$teamPaid = 1;
			} else {
				$teamPaid = 0;
			}
		} else {
			$teamPaid = 0;
		}
		mysql_query("UPDATE $teamsTable SET team_num_in_league = $teamNum, team_is_convenor = $teamIsConvenor, team_paid = $teamPaid WHERE team_id = $teamID") 
			or die('ERROR updating teams '.mysql_error());
	}
}

function addTeam($teamName, $leagueID, $sportID, $teamCount) {
	global $leaguesTable, $teamsTable;
	$teamNumInLeague = $teamCount+1;
	if($teamNumInLeague < 10) {
		$picName = $leagueID.'-0'.$teamNumInLeague;
	} else {
		$picName = $leagueID.'-'.$teamNumInLeague;
	}
	$teamName = mysql_real_escape_string($teamName);
	
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID") or die('ERROR getting league data '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	$seasonID = $leagueArray['league_season_id'];
	
	mysql_query("INSERT INTO $teamsTable (team_league_id, team_name, team_num_in_league, team_created, team_finalized, team_payment_method, team_pic_name)
		VALUES ($leagueID, '$teamName', $teamNumInLeague, NOW(), 1, 5, '$picName')") or die('ERROR adding team into db '.mysql_error());
}

function addFenceTeams() {
	global $teamsTable, $leagueID;
	
	if (isset($_POST['delFence'])) {
        $effectedCount = 0;
        foreach($_POST['delFence'] as $teamID) {
			$maxTeamNumArray = mysql_query("SELECT MAX(team_num_in_league) as highestTeamNum FROM $teamsTable 
				WHERE team_league_id = $leagueID");
			$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
			$teamNum = $maxTeamNum['highestTeamNum'] +1;
			
            mysql_query("UPDATE $teamsTable SET team_num_in_league = $teamNum, team_finalized = 1 WHERE team_id = $teamID") 
                or die('ERROR adding team '.$teamID.' '.mysql_error());
            //fixTeamNumbers($leagueID, $deleteNum, $teamCount);
            $effectedCount++;
        }
        print 'Additions complete, '.$effectedCount.' teams effected.';
    } else {
        print 'No teams selected to add';
    }
}

function deleteFenceTeams() {
    global $teamsTable, $playersTable, $leagueID;
	
    if (isset($_POST['delFence'])) {
        $effectedCount = 0;
        foreach($_POST['delFence'] as $teamID) {
            mysql_query("DELETE FROM $teamsTable WHERE team_id = $teamID") 
                or die('ERROR deleting team '.$teamID.' '.mysql_error());
			mysql_query("DELETE FROM $playersTable WHERE player_team_id = $teamID") 
                or die('ERROR deleting players '.$teamID.' '.mysql_error());
            //fixTeamNumbers($leagueID, $deleteNum, $teamCount);
            $effectedCount++;
        }
        print 'Deletions complete, '.$effectedCount.' teams effected.';
    } else {
        print 'No teams selected to delete';
    }
    
}

?>