<?php

//Gets whatever argument you pass into the program for sport ("?sportID=1" etc)
if(($seasonID = $_GET['seasonID']) == ''){
    $seasonID = 0;
}
if(($sportID = $_GET['sportID']) == ''){
    $sportID = 0;
}
if (($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

function updateTeamNums($teamObjs) {
	global $teamsTable, $container;
	$teamCount = 0;
	for($i = 0; $i < count($teamObjs); $i++) { //iterates through the leagues
		foreach($teamObjs[$i] as $team) {
			if($team->teamLeagueSplit != 1) {
				$teamID = $team->teamID;
				if($team->teamNumInLeague < 10) {
					$teamPicString = $team->teamLeagueID.'-0'.$team->teamNumInLeague;
				} else {
					$teamPicString = $team->teamLeagueID.'-'.$team->teamNumInLeague;
				}
				mysql_query("UPDATE $teamsTable SET team_pic_name = '$teamPicString' WHERE team_id = $teamID")
					or die('ERROR updating pic name - '.mysql_error());
				$team->teamPicName = $teamPicString;
				$teamCount++;
			}
		}
	}
	$container->printSuccess('Team picture ID\'s successfully updated, '.$teamCount.' teams effected');
}

function updatePics() {
	global $teamsTable, $container;
	
	for($i=0; $i < count($_POST['teamID']); $i++) {
		$teamID = $_POST['teamID'][$i];
		$picName = $_POST['picName'][$i];
		mysql_query("UPDATE $teamsTable SET team_pic_name = '$picName' WHERE team_id = $teamID") or die('ERROR updating pics - '.mysql_error());
	}
	$container->printSuccess("$i Picture names changed");
}

function getTeamsData($sportID, $seasonID, $leagueID) {
	global $teamsTable, $leaguesTable, $leagueIsSplit, $playersTable;
	
	if($seasonID != 0 && $sportID != 0) {
		if($leagueID != 0) {
			$leagueFilter = "AND team_league_id = $leagueID";
		} else {
			$leagueFilter = '';
		}
	
		$teamsQuery = mysql_query("SELECT league_is_split, league_id, team_name, team_num_in_league, team_id, league_name, league_day_number, team_pic_name FROM $teamsTable 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
			WHERE league_season_id = $seasonID AND league_sport_id = $sportID $leagueFilter AND team_num_in_league > 0 
			ORDER BY league_day_number ASC, league_name ASC, team_num_in_league ASC") 
			or die('ERROR getting teams - '.mysql_error());
		$numTeams = 0;
		$leagueNum = -1;
		$lastLeagueID = 0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$leagueIsSplit = $teamArray['league_is_split'];
			$leagueID = $teamArray['league_id'];
			if($leagueID != $lastLeagueID) {
				$lastLeagueID = $leagueID;
				$leagueNum++;
				$numTeams = 0;
			}
			$team[$leagueNum][$numTeams] = new Team();
			$team[$leagueNum][$numTeams]->teamID = $teamArray['team_id'];
			$team[$leagueNum][$numTeams]->teamName = $teamArray['team_name'];
			$team[$leagueNum][$numTeams]->teamLeagueID = $teamArray['league_id'];
			$team[$leagueNum][$numTeams]->teamNumInLeague = $teamArray['team_num_in_league'];
			$team[$leagueNum][$numTeams]->teamLeagueName = $teamArray['league_name'].' - '.dayString($teamArray['league_day_number']);
			$team[$leagueNum][$numTeams]->teamPicName = $teamArray['team_pic_name'];
			$cptnQuery = mysql_query("SELECT player_firstname, player_lastname FROM $playersTable WHERE player_team_id = ".$teamArray['team_id']." 
			AND player_is_captain = 1") or die('ERROR getting captain - '.mysql_error());
			$cptnArray = mysql_fetch_array($cptnQuery);
			$team[$leagueNum][$numTeams]->teamCaptainName = $cptnArray['player_firstname'].' '.$cptnArray['player_lastname'];
			$team[$leagueNum][$numTeams]->teamLeagueSplit = $teamArray['league_is_split'];
			$numTeams++;
		}
		return $team;
	} else {
		return 0;
	}
}

function printInfoDDs($seasonsDD, $sportsDD, $leaguesDD) { ?>
	Season
	<select id='userInput' name="seasonID" onchange="reloadPage()">
		<?php print $seasonsDD ?>
	</select><br /><br />
	Sport
	<select id='userInput' name="sportID" onchange="reloadPage()">
		<?php print $sportsDD ?>
	</select><br /><br />
	League
	<select id='userInput' name="leagueID" onchange="reloadPageLeague()">
		<?php print $leaguesDD ?>
	</select>
<?php }

function printLeagueHeader() { ?>
	<tr>
    	<td style="width:20px">
        	#
        </td><td>
        	Team Name
        </td><td>
			Cptn Name
		</td><td>
        	Team ID
        </td>
    </tr>
<?php }

function printTeamNode($curTeam, $iteratorNum) { ?>
    <tr>
    	<td style="width:20px">
        	<?php print $iteratorNum+1 ?>
        </td><td>
			<?php print $curTeam->teamName ?>
            <input type='hidden' name='teamID[]' value='<?php print $curTeam->teamID ?>' />
        </td><td>
            <?php print $curTeam->teamCaptainName ?>
        </td><td>
            <input type='text' name='picName[]' style="width:60px;" value="<?php print $curTeam->teamPicName ?>" />
        </td>
    </tr>
<?php }