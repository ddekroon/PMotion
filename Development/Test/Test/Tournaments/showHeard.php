<?php /*****************************************
File: showHeard.php
Creator: Derek Dekroon
Created: August 29/2012
Shows a graph for how tournament players found out about the tournaments
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
        	function loadValues(self) {
				var imgBox = document.getElementById('imageBox');
				var peopleBox = document.getElementById('totalPeopleBox');
				var peopleInLeaguesBox = document.getElementById('totalPeopleInLeaguesBox');
				var peopleValues = document.getElementsByName('totalPeople[]');
				var peopleInLeaguesValues = document.getElementsByName('totalPeopleInLeagues[]');
				
				imgBox.src = 'includeFiles/heardImg.php?tournamentID=' + self.value;
				peopleBox.innerHTML = peopleValues[self.value].value;
				peopleInLeaguesBox.innerHTML = peopleInLeaguesValues[self.value].value;		
			}
        </script>";
$container = new Container('Team Stats', 'includeFiles/tournamentStyle.css', $javaScript);

require_once('includeFiles/showHeardFunctions.php'); 
require_once('includeFiles/playerClass.php');

function getTournamentDD($tourneyID) {
	global $tournamentsTable;
	$tournamentsDropDown = '<option value=0>All Tournaments</option>';
	
	$tournamentsQuery=mysql_query("SELECT * FROM $tournamentsTable ORDER BY tournament_id") or die("ERROR getting tournaments drop down ".mysql_error());
	while($tournament = mysql_fetch_array($tournamentsQuery)) {
		if($tournament['tournament_id']==$tourneyID){
			$tournamentsDropDown.="<option selected value= $tournament[tournament_id]>$tournament[tournament_name]</option><BR>";
		}else{
			$tournamentsDropDown.="<option value= $tournament[tournament_id]>$tournament[tournament_name]</option>";
		}
	}
	return $tournamentsDropDown;
}

function printHiddenVariables() {
	global $totalPeople, $numPeopleInLeagues, $tournamentPlayersTable, $tournamentsTable;
	
	$totalPeople = 0;
	$numPeopleInLeagues = 0;
	
	$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable");
	$numTourneys = mysql_num_rows($tourneyQuery);
	
	for($j = 1;$j <= $numTourneys;$j++) {
		$personQuery = mysql_query("SELECT * FROM $tournamentPlayersTable WHERE tournament_player_hear_method > 0 AND tournament_player_tournament_id = $j") 
			or die('ERROR getting hear methods - '.mysql_error());
		$totalTournamentPeople[$j] = mysql_num_rows($personQuery);
		$totalPeople += $totalTournamentPeople[$j];
		
		$personQuery = mysql_query("SELECT * FROM $tournamentPlayersTable WHERE tournament_player_hear_method = 5 AND tournament_player_tournament_id = $j") 
			or die('ERROR getting hear methods - '.mysql_error());
		$totalTournamentPeopleInLeagues[$j] = mysql_num_rows($personQuery);
		$numPeopleInLeagues += $totalTournamentPeopleInLeagues[$j];
	}
	$totalTournamentPeople[0] = $totalPeople;
	$totalTournamentPeopleInLeagues[0] = $numPeopleInLeagues;
		
	for($i=0;$i<=$numTourneys;$i++) { ?>
    	<input type="hidden" name="totalPeople[]" value="<?php print $totalTournamentPeople[$i] ?>" >
		<input type="hidden" name="totalPeopleInLeagues[]" value="<?php print $totalTournamentPeopleInLeagues[$i] ?>" >
	<?php }
}

$method[1] = 'Internet';
$method[2] = 'Facebook';
$method[3] = 'Kijiji';
$method[4] = 'Returning';
$method[5] = 'Leaguer';
$method[6] = 'Friend';
$method[7] = 'Restaurant';
$method[8] = 'Community Guide';
$method[9] = 'Other';
$playerObj = array();

if(($tourneyID = $_GET['tournamentID']) == '') {
	$tourneyID = 0;
}
$tourneyDropDown = getTournamentDD($tourneyID);
printHiddenVariables(); 

$numPlayers = 0;
$playerQuery = mysql_query("SELECT * FROM $tournamentPlayersTable 
	INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id
	WHERE tournament_player_hear_method = 9 ORDER BY tournament_player_id DESC") 
	or die('ERROR getting hear methods - '.mysql_error());
while($personArray = mysql_fetch_array($playerQuery)) {
	$playerObj[$numPlayers] = new Player();
	$playerObj[$numPlayers]->playerFirstName = $personArray['tournament_player_firstname'];
	$playerObj[$numPlayers]->playerLastName = $personArray['tournament_player_lastname'];
	$playerObj[$numPlayers]->playerEmail = $personArray['tournament_player_email'];
	if($personArray['tournament_is_leagues'] == 1) {
		$leagueNum = $personArray['tournament_player_league_id'];
		$leagueNames = explode('%', $personArray['tournament_league_names']);
		$playerObj[$numPlayers]->playerLeagueName = $leagueNames[$leagueNum].' - '.$personArray['tournament_name'];
	} else {
		$playerObj[$numPlayers]->playerLeagueName = $personArray['tournament_name'];
	}
	$playerObj[$numPlayers]->playerHearText = $personArray['tournament_player_other_text'];
	$numPlayers++;
} ?>

<h1>How Tournament Players Heard about us</h1>
<div class='getIDs'>
	<select name="tourneyID" onChange="loadValues(this)">
		<?php print $tourneyDropDown ?>
	</select>
</div>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=2>
				Tournament Data
			</th>
		</tr><tr>
			<td colspan=2>
				<img id="imageBox" src="includeFiles/heardImg.php">
			</td>
		</tr><tr>
			<td>
				Total people entered:
			</td><td>
				<label id="totalPeopleBox"><?php print $totalPeople ?></label>
			</td>
		</tr><tr>
			<td>
				People From Leagues:
			</td><td>
				<label id="totalPeopleInLeaguesBox"><?php print $numPeopleInLeagues ?></label>
			</td>
		</tr>
	</table>
</div><div class='tableData'>
	<table>
		<tr>
			<th colspan=4>
				Player Testamonies
			</th>
		</tr>
		<?php printPlayerHeader();
		foreach($playerObj as $player) {
			printPlayerNode($player);
		} ?>
	</table>
</div>
<?php $container->printFooter(); ?>