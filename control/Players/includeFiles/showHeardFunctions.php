<?php function getYearDD($yearNum) {
	global $seasonsTable;
	$yearDropDown ='';
	$lastYear = 0;
	//leagues in dropdown
	$yearsQuery=mysql_query("SELECT season_year, season_available_score_reporter FROM $seasonsTable WHERE season_id != 0 ORDER BY season_year ASC, season_available_score_reporter DESC") 
		or die("ERROR getting seasons DD ".mysql_error());
	while($year = mysql_fetch_array($yearsQuery)) {
		if($year['season_year'] != $lastYear) {
			if($year['season_year'] == $yearNum || ($yearNum == 0 && $year['season_available_score_reporter'] == 1)){
				$yearDropDown.="<option selected value= $year[season_year]>$year[season_year]</option>";
			}else{
				$yearDropDown.="<option value= $year[season_year]>$year[season_year]</option>";
			}
			$lastYear = $year['season_year'];
		}
	}
	return $yearDropDown;
}

function setHiddenValues($yearNum) {
	global $totalPeople, $totalPeopleReturning, $playersTable, $teamsTable, $leaguesTable, $seasonsTable, $individualsTable;
	$personQuery = mysql_query("SELECT player_hear_method FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_hear_method > 0 AND (player_is_captain = 1 OR player_is_individual = 1) AND season_year = $yearNum")
		or die('ERROR getting hear methods - '.mysql_error());
	$totalTeamPeople = mysql_num_rows($personQuery);

	$personQuery = mysql_query("SELECT player_hear_method FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_hear_method = 4  AND (player_is_captain = 1 OR player_is_individual = 1) AND season_year = $yearNum") 
		or die('ERROR getting hear methods - '.mysql_error());
	$totalTeamPeopleReturning = mysql_num_rows($personQuery);
	
	$personQuery = mysql_query("SELECT player_hear_method FROM $playersTable INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $individualsTable.individual_preferred_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_hear_method > 0 AND (player_is_captain = 1 OR player_is_individual = 1) AND season_year = $yearNum")
		or die('ERROR getting hear methods - '.mysql_error());
	$totalIndividualPeople = mysql_num_rows($personQuery);

	$personQuery = mysql_query("SELECT player_hear_method FROM $playersTable INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $individualsTable.individual_preferred_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE player_hear_method = 4 AND (player_is_captain = 1 OR player_is_individual = 1) AND season_year = $yearNum") 
		or die('ERROR getting hear methods - '.mysql_error());
	$totalIndividualPeopleReturning = mysql_num_rows($personQuery);
		
	$totalPeople = $totalTeamPeople + $totalIndividualPeople;
	$totalPeopleReturning = $totalTeamPeopleReturning + $totalIndividualPeopleReturning;
	
	
	?>
    <input type="hidden" id="totalReturningTeam" value="<?php print $totalTeamPeopleReturning ?>">
    <input type="hidden" id="totalTeam" value="<?php print $totalTeamPeople ?>">
    <input type="hidden" id="totalReturningIndividual" value="<?php print $totalIndividualPeopleReturning ?>">
    <input type="hidden" id="totalIndividual" value="<?php print $totalIndividualPeople ?>">
    <input type="hidden" id="totalReturning" value="<?php print $totalPeopleReturning ?>">
    <input type="hidden" id="total" value="<?php print $totalPeople ?>">
    <input type="hidden" id="yearNum" value="<?php print $yearNum ?>">

<?php }

function printPlayerHeader() { ?>
	<tr>
    	<td>
        	Name
        </td><td>
        	Individual
        </td><td>
        	League
        </td><td>
        	Comment
        </td>
    </tr>
<?php }

function printPlayerNode($playerObj) { ?>
	<tr>
    	<td>
        	<?php print "<a target='_blank' href='mailto:$playerObj->playerEmail'>".$playerObj->playerFirstName.' '.$playerObj->playerLastName.'</a>'; ?>
        </td><td>
        	<?php print $playerObj->playerIsIndividual?'Yes':'No'; ?>
        </td><td>
        	<?php print $playerObj->playerLeagueName; ?>
        </td><td>
        	<?php print $playerObj->playerHearText; ?>
        </td>
    </tr>
<?php }