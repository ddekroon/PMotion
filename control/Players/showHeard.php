<?php /*****************************************
File: showHeard.php
Creator: Derek Dekroon
Created: August 3/2012
Shows a graph of how players found us. Info is collected when teams/players register for leagues.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
        	function reloadValues(self) {
            	var picBox = document.getElementById('picBox');
				var totalPeopleBox = document.getElementById('totalPeople');
				var totalPeopleReturningBox = document.getElementById('totalPeopleReturning');
				var yearNum = document.getElementById('yearNum').value;
				if(self.value == 0) {
					picBox.src = 'includeFiles/heardImgAll.php?yearNum='+yearNum;
					totalPeopleBox.innerHTML = document.getElementById('total').value;
					totalPeopleReturningBox.innerHTML = document.getElementById('totalReturning').value;
				}	else if(self.value == 1) {
					picBox.src = 'includeFiles/heardImgTeams.php?yearNum='+yearNum;
					totalPeopleBox.innerHTML = document.getElementById('totalTeam').value;
					totalPeopleReturningBox.innerHTML = document.getElementById('totalReturningTeam').value;
				} else if(self.value == 2) {
					picBox.src = 'includeFiles/heardImgIndividuals.php?yearNum='+yearNum;
					totalPeopleBox.innerHTML = document.getElementById('totalIndividual').value;
					totalPeopleReturningBox.innerHTML = document.getElementById('totalReturningIndividual').value;
				}
            }
			function reloadPage(yearNum) {
				self.location = ('showHeard.php?yearNum=' + yearNum.value);					
			}
        </script>";
$container = new Container('How Players Found Us', 'includeFiles/playerStyle.css', $javaScript);
require_once('includeFiles/playerClass.php');
require_once('includeFiles/showHeardFunctions.php');



if(($yearNum = $_GET['yearNum']) == '') {
	$seasonQuery = mysql_query("SELECT season_year FROM $seasonsTable WHERE season_available_score_reporter = 1") or die('ERROR - '.mysql_error());
	$seasonArray = mysql_fetch_array($seasonQuery);
	$yearNum = $seasonArray['season_year'];
}

$method[1] = 'Internet Search';
$method[2] = 'Facebook';
$method[3] = 'Kijiji';
$method[4] = 'Returning Player';                       
$method[5] = 'From a Friend';
$method[6] = 'Restraunt Ad';
$method[7] = 'Comunity Guide';
$method[8] = 'Other';
$playerObj = array();
$yearDropDown = getYearDD($yearNum);

$numPlayers = 0;
$playerQuery = mysql_query("SELECT player_firstname, player_lastname, player_id, player_email, player_hear_other_text, player_is_individual FROM $playersTable WHERE player_hear_method = 8  AND (player_is_captain = 1 OR player_is_individual = 1) ORDER BY player_id DESC") 
	or die('ERROR getting hear methods - '.mysql_error());
while($personArray = mysql_fetch_array($playerQuery)) {
	$playerObj[$numPlayers] = new Player();
	$playerObj[$numPlayers]->playerFirstName = $personArray['player_firstname'];
	$playerObj[$numPlayers]->playerLastName = $personArray['player_lastname'];
	$playerObj[$numPlayers]->playerEmail = $personArray['player_email'];
	$playerObj[$numPlayers]->playerIsIndividual = $personArray['player_is_individual'];
	if($personArray['player_is_individual'] == 1) {
		$individualArray = mysql_fetch_array(mysql_query("SELECT league_name, league_day_number FROM $individualsTable 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $individualsTable.individual_preferred_league_id WHERE individual_player_id = ".$personArray['player_id']));
		$playerObj[$numPlayers]->playerLeagueName = $individualArray['league_name'].' - '.dayString($individualArray['league_day_number']);
	} else {
		$teamPlayerArray = mysql_fetch_array(mysql_query("SELECT league_name, league_day_number FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id WHERE player_id = ".$personArray['player_id']));
		$playerObj[$numPlayers]->playerLeagueName = $teamPlayerArray['league_name'].' - '.dayString($teamPlayerArray['league_day_number']);
	}
	$playerObj[$numPlayers]->playerHearText = $personArray['player_hear_other_text'];
	$numPlayers++;
}

setHiddenValues($yearNum); ?>
<h1>How Players Heard About Us</h1>
<div class='tableData'>
			<table>
				<tr>
					<th colspan=2>
						Options/Graph
					</th>
				</tr><tr>
					<td colspan=2>
						<select name="yearNum" onChange="reloadPage(this)">
							<?php print $yearDropDown ?>
						</select>
						<select name="picType" onChange="reloadValues(this)">
							<option value=0>All</option>
							<option value=1>Teams</option>
							<option value=2>Individuals</option>
						</select>
					</td>
				</tr><tr>
					<td colspan=2 style="padding:0px 0px 0px 0px;">
						<img id="picBox" src="includeFiles/heardImgAll.php?yearNum=<?php print $yearNum?>">
					</td>
				</tr>
				<tr>
					<td>
						Total people entered:
					</td><td>
						<label id="totalPeople"><?php print $totalPeople ?></label>
					</td>
				</tr><tr>
					<td>
						Total returning players:
					</td><td>	
						<label id="totalPeopleReturning"><?php print $totalPeopleReturning ?></label>
					</td>
				</tr>
			</table>
	</div><div class='tableData'>
			<table>
				<tr>
					<th colspan=4>
						Player Testemonies
					</th>
				</tr>
				<?php printPlayerHeader();
				foreach($playerObj as $player) {
					printPlayerNode($player);
				} ?>
			</table>
	</div>
<?php $container->printFooter(); ?>
