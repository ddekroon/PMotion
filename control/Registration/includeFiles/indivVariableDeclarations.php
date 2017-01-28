<?php 
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
/*line 20 gets rid of the errors being displayed on the page*/
ini_set( 'display_errors', 'off' );

function declareSportVariables() {
	global $sportID, $logo, $sportHeader, $titleHeader, $people, $update;
	
	if($sportID==1){
			$logo="/Logos/GuelphUltimate.jpg";
			if($update == 0) {
				$titleHeader="Register - Guelph Ultimate";
				$sportHeader="<br>Register as a team for Guelph Ultimate";
			} else {
				$titleHeader="Update - Guelph Ultimate";
				$sportHeader="<br>Update a current team for Guelph Ultimate";
			}
			$people=15;
	}elseif($sportID==2){
		$type="Beach Volleyball";
		$logo="/Logos/WheresTheBeach.jpg";
		if($update == 0) {
			$titleHeader="Register - Where's The Beach Volleyball";
			$sportHeader="<br>Register as a team for Where's The Beach Volleyball";
		} else {
			$titleHeader="Update - Where's The Beach Volleyball";
			$sportHeader="<br>Update a current team for Where's The Beach Volleyball";
		}
		$people=14;
	}elseif($sportID==3){
		$type="Flag Football";
		$logo="/Logos/GuelphFlagFootball.jpg";
		if($update == 0) {
			$titleHeader="Register - Guelph Flag Football";
			$sportHeader="<br>Register as a team for Guelph Flag Football";
		} else {
			$titleHeader="Update - Guelph Flag Football";
			$sportHeader="<br>Update a current team for Guelph Flag Football";
		}
		$people=12;
	}elseif($sportID==4){
		$type="Soccer";
		$logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
		if($update == 0) {
			$titleHeader="Register - Guelph Soccer";
			$sportHeader="<br>Register as a team for Guelph Soccer";
		} else {
			$titleHeader="Update - Guelph Soccer";
			$sportHeader="<br>Update a current team for Guelph Soccer";
		}
		$people=15;
	}
} 

function getDatabaseTeams($leagueID) {
	global $players, $team, $teamIDArray, $teamsTable, $playersTable;
	$teamIDArray = array();
	if($leagueID > 0) {
		//Gets registered teams
		$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND team_finalized = 1 ORDER BY team_num_in_league ASC") 
			or die('ERROR getting teams '.mysql_error());
		$teamNum = 0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$teamID = $teamArray['team_id'];
			$playerQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID") 
				or die ('ERROR getting player data - '.mysql_error());
			if(mysql_num_rows($playerQuery) == 0) { //team is empty, individual team
				array_push($teamIDArray, $teamID);
			} else {
				while($playerArray = mysql_fetch_array($playerQuery)) {
					if($playerArray['player_is_individual'] == 1) {
						array_push($teamIDArray, $teamID);
						break;
					}
				}
			}
		}
		foreach($teamIDArray as $teamID) {
			$teamQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_id = $teamID") 
				or die('ERROR getting teams - '.mysql_error());
			while($teamArray = mysql_fetch_array($teamQuery)) {
				$team[$teamNum] = new Team();	
				$team[$teamNum]->teamName = $teamArray['team_name'];
				$team[$teamNum]->teamID = $teamArray['team_id'];
				$playerQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID")
					or die('ERROR getting players - '.mysql_error());
					$playerNum = 0;
				while($playerArray = mysql_fetch_array($playerQuery)) {
					$players[$teamNum][$playerNum]->playerName = $playerArray['player_firstname'].' '.$playerArray['player_lastname'];
					$playerNum++;
				}
				$teamNum++;
			}
		}
	}
	return $teamNum;
}?>