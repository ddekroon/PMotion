<?php

//To get the data, it first gets all the IDs of the teams with captains in the current season, and season in registration. Then it gets all the teams and any that
//haven't already been figured to have a captain get added to a list for later display.
function getTeamsData() {
	global $teamsTable, $leaguesTable, $seasonsTable, $team, $playersTable;
	$teamsWithCaptains = array();
	
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
	 	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE (season_available_score_reporter = 1 OR season_available_registration = 1) AND player_is_captain = 1 ORDER BY team_id ASC") 
		or die('ERROR getting teams with captains - '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		array_push($teamsWithCaptains, $teamArray['team_id']);
	}
	
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE (season_available_score_reporter = 1 OR season_available_registration = 1) AND team_num_in_league > 0 ORDER BY league_day_number ASC, league_sport_id ASC, league_name ASC, team_id ASC") 
		or die('ERROR getting teams - '.mysql_error());
	$numTeams = 0;
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		if(!in_array($teamArray['team_id'], $teamsWithCaptains)) {
			$team[$numTeams] = new Team();
			$team[$numTeams]->teamID = $teamArray['team_id'];
			$team[$numTeams]->teamName = $teamArray['team_name'];
			$team[$numTeams]->teamLeagueName = $teamArray['league_name'].' - '.dayString($teamArray['league_day_number']);
			$team[$numTeams]->teamLeagueID = $teamArray['team_league_id'];
			$team[$numTeams]->teamSportID = $teamArray['league_sport_id'];
			$numTeams++;
		}
	}
	return $numTeams;
}

function getPostData() {
	global $playerObj;
	
	$numTeams = $_POST['numTeams'];
  	for($i=0;$i<$numTeams;$i++) {
		$playerObj[$i] = new Player();
		$playerObj[$i]->playerTeamID = $_POST['teamID'][$i];
		$playerObj[$i]->playerFirstName = $_POST['capFirst'][$i];
		$playerObj[$i]->playerLastName = $_POST['capLast'][$i];
		$playerObj[$i]->playerEmail = $_POST['capEmail'][$i];
		$playerObj[$i]->playerGender = $_POST['capGender'][$i];
		if(($playerObj[$i]->playerPhone = $_POST['capPhone'][$i]) == '') {
			$playerObj[$i]->playerPhone = 0;
		}
	}
	return $numTeams;
}

function updateTeams($playerObj, $numTeams) {
	global $playersTable, $teamsTable, $captainsTable;
	
	$numUpdatedTeams = 0;
	for($i=0;$i<$numTeams;$i++) {
		if($playerObj[$i]->playerFirstName != '' && $playerObj[$i]->playerLastName != '' && filter_var($playerObj[$i]->playerEmail, FILTER_VALIDATE_EMAIL)) {
			$maxCaptainNumArray = mysql_query("SELECT MAX(player_id) as  maxPlayer FROM $playersTable");
			$maxCaptainNum = mysql_fetch_array($maxCaptainNumArray);
			$playerID = $maxCaptainNum['maxPlayer'] +1;
			$teamID = $playerObj[$i]->playerTeamID;
			$capFirst = mysql_escape_string($playerObj[$i]->playerFirstName);
			$capLast = mysql_escape_string($playerObj[$i]->playerLastName);
			$capEmail = mysql_escape_string($playerObj[$i]->playerEmail);
			$capGender = mysql_escape_string($playerObj[$i]->playerGender);
			if(($capPhone = preg_replace("/[^0-9]/", "", $playerObj[$i]->playerPhone)) == '' ) {
				$capPhone = 0;
			}
			
			mysql_query("INSERT INTO $playersTable (player_id, player_team_id, player_firstname, player_lastname, player_email, player_sex, player_phone, 
				player_is_individual, player_is_captain) VALUES ($playerID, $teamID, '$capFirst', '$capLast', '$capEmail', '$capGender', '$capPhone', 0, 1)") 
				or die('ERROR inserting player - '.mysql_error());
			$numUpdatedTeams++;
		}
	}	
	print 'Captains Added, '.$numUpdatedTeams.' team(s) affected';
}

function printCaptainHeader() { ?>
	<tr>
		<th colspan=7>
			Teams
		</th>
	</tr><tr>
    	<td style="width:20px;">
        	#
        </td><td>
        	Team Name
        </td><td>
        	League Name
        </td><td>
        	Captain Name
        </td><td>
        	Captain Email
        </td><td>
        	Captain Gender
        </td><td>
        	Captain Phone
        </td>
    </tr>
<?php }

function printCaptainNode($curTeam, $iteratorNum, $numEditedTeams, $playerObj) { ?>
	<?php $teamFound = 0; ?>
    <tr>
    	<td style="width:20px;">
        	<?php print $iteratorNum+1 ?>
        </td><td>
			<?php print '<a href="../Teams/editTeam.php?sportID='.$curTeam->teamSportID.'&leagueID='.$curTeam->teamLeagueID.'&teamID='.$curTeam->teamID.'">'.$curTeam->teamName.'</a>'; ?>
            <input type='hidden' name='teamID[]' value='<?php print $curTeam->teamID ?>' />
        </td><td>
            <?php print $curTeam->teamLeagueName ?>
        </td>
        <?php for($i=0;$i<$numEditedTeams; $i++) {
			if($curTeam->teamID == $playerObj[$i]->playerTeamID) {
            	$teamFound = 1;
				$teamNum = $i;
			}
		}
		if($teamFound == 1) { ?>
            <td>
                <input type="text" name="capFirst[]" style="width:60px" value="<?php print $playerObj[$teamNum]->playerFirstName ?>" >
                <input type="text" name="capLast[]" style="width:80px" value="<?php print $playerObj[$teamNum]->playerLastName ?>">
            </td><td>
                <input type="text" name="capEmail[]" style="width:200px" value="<?php print $playerObj[$teamNum]->playerEmail ?>">
            </td><td>
                <select name="capGender[]">
                    <option <?php $playerObj[$teamNum]->playerGender == 'M' ? 'selected':'' ?> value="M">Male</option>
                    <option <?php $playerObj[$teamNum]->playerGender == 'F' ? 'selected':'' ?> value="F">Female</option>
                    <option <?php $playerObj[$teamNum]->playerGender == 'O' ? 'selected':'' ?> value='O'>Other</option>
                </select>
            </td><td>
                <input type="text" name="capPhone[]" style="width:100px" value='<?php print $playerObj[$teamNum]->playerPhone ?>'>
            </td>
        <?php } else { ?>
            <td>
                <input type="text" name="capFirst[]" style="width:60px">
                <input type="text" name="capLast[]" style="width:80px">
            </td><td>
                <input type="text" name="capEmail[]" style="width:200px">
            </td><td>
                <select name="capGender[]">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                    <option value='O'>Other</option>
                </select>
            </td><td>
                <input type="text" name="capPhone[]" style="width:100px">
            </td>
        <?php } ?>
    </tr>
<?php }

function printButtons() {?>
	<tr>
    	<td colspan=7>
        	<button name="submitCaptains">Submit Captains</button>
        </td>
	</tr>
<?php }