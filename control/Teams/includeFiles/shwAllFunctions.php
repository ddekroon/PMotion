<?php 

function getTeamData($seasonID, $sportID, $divisionID) {
	global $leaguesTable, $teamsTable, $playersTable, $sportsTable;
	$teamCounter = 1;
	$lastLeague = 0;

	if($divisionID == 0) {
		$divisionFilter = '';
	} else if($divisionID == 1) { //comp, inter, open
		$divisionFilter = "AND (league_name LIKE '%2%' OR league_name LIKE '%4%' )";
	} else { //recreational
		$divisionFilter = "AND league_name LIKE '%6%'";
	}
	
	$teamsQuery = mysql_query("SELECT team_name, team_id, team_league_id, league_day_number, league_id, league_name, sport_id, sport_name
							   FROM $leaguesTable INNER JOIN $teamsTable ON $teamsTable.team_league_id = $leaguesTable.league_id INNER JOIN $sportsTable ON 			
							   $sportsTable.sport_id = $leaguesTable.league_sport_id WHERE league_season_id = $seasonID AND team_num_in_league > 0 AND sport_id = $sportID 	
							   $divisionFilter ORDER By league_sport_id ASC, league_day_number ASC, league_name ASC, team_name ASC") 
		or die('ERROR getting leagues '.mysql_error());

	while($team = mysql_fetch_array($teamsQuery)) {
		if(strlen($team['team_name']) > 2) {
			$teamID = $team['team_id'];
			$captainQuery = mysql_query("SELECT player_firstname, player_lastname, player_email, player_phone, player_is_individual FROM $playersTable WHERE player_team_id = $teamID ORDER BY player_is_captain DESC, player_is_individual DESC") or die('ERROR getting captain - '.mysql_error());
			$captainArray = mysql_fetch_array($captainQuery);
			if($team['team_league_id'] != $lastLeague) {
				$lastLeague = $team['team_league_id'];
				$teamCounter = 1;
			}
			$player[$teamID] = new Player();
			$player[$teamID]->playerTeamID = $team['team_id'];
			$player[$teamID]->playerTeamName = $team['team_name'];
			$player[$teamID]->playerTeamNum = $teamCounter;
			$player[$teamID]->playerLeagueID = $team['league_id'];
			$player[$teamID]->playerLeagueName = $team['league_name'].' - '.dayString($team['league_day_number']);
			$player[$teamID]->playerFirstName = $captainArray['player_firstname'];
			$player[$teamID]->playerLastName = $captainArray['player_lastname'];
			$player[$teamID]->playerEmail = $captainArray['player_email'];
			$player[$teamID]->playerPhone = $captainArray['player_phone'];
			$player[$teamID]->playerSportID = $team['sport_id'];
			$player[$teamID]->playerSportName = $team['sport_name'];
			$captainArray = mysql_fetch_array($captainQuery); //the second player tells if a team has individuals
			$player[$teamID]->playerIsIndividual = $captainArray['player_is_individual'];
			$teamCounter++;
			$lastLeague = $team['team_league_id'];
		}
	}
	return $player;
}

function printSportHeader($sportName) { ?>
	<tr style="background-color:#333333; color:#FFFFFF">
    	<td colspan="5" align="center" style="font-size:24px">
        	<?php print $sportName ?>
        </td>
    </tr>	
<?php }

function printLeagueHeader($leagueName) { ?>
	<tr style="background-color:#999; color:#000">
    	<td colspan="5" align="center" style="font-size:18px">
        	<?php print $leagueName ?>
        </td>
    </tr>	
<?php }

function printTeamNode($teamNode, $rowNum) { ?>
	<tr <?php print $rowNum == 1? 'style="background-color:#EEE;height:15px;"':'style="height:15px;"' ?>>
    	<td>
        	<?php print $teamNode->playerTeamNum ?>
        </td><td style="text-align:left;">
        	<?php print $teamNode->playerIsIndividual == 1?'<b>':'';
			print $teamNode->playerTeamName;
			print $teamNode->playerIsIndividual == 1?'</b>':''; ?>
        </td><td>
        	<?php print $teamNode->playerFirstName.' '.$teamNode->playerLastName ?>
        </td><td>
        	<?php print formatPhoneNumber($teamNode->playerPhone) ?>
        </td><td>
        	<?php print $teamNode->playerEmail ?>
        </td>
	</tr>
<?php }

function getDivisionDD($divisionID) {
	$divisionsDropDown = '<option value=0>All</option>';
	$divisionsDropDown .= '<option ';
	$divisionsDropDown .= $divisionID==1?'selected ':'';
	$divisionsDropDown .= 'value=1>2s and 4s</option>';
	$divisionsDropDown .= '<option ';
	$divisionsDropDown .= $divisionID==2?'selected ':'';
	$divisionsDropDown .= 'value=2>6s</option>';
	return $divisionsDropDown;
}

function formatPhoneNumber($strPhone){
        $strPhone = preg_replace("[^0-9]",'', $strPhone);
        if (strlen($strPhone)!= 10){
                return $strPhone;
        }
        $strArea = substr($strPhone, 0, 3);
        $strPrefix = substr($strPhone, 3, 3);
        $strNumber = substr($strPhone, 6, 4);
        $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;
        return ($strPhone);
}