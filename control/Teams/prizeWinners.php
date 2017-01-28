<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* May 7, 2013
* prizeWinners.php
*
* This program is used to show all available prize winners.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function reloadSeason(seasonDD) {
				var seasonID = seasonDD.value;
				
			}
			function reloadPageSport() {
				var form = document.getElementById('prizeWinners');
				var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				var prizeTime=form.elements['prizeTime'].options[form.elements['prizeTime'].options.selectedIndex].value
				self.location='prizeWinners.php?sportID=' + sportID+ '&prizeTime=' + prizeTime;
			}
			function reloadPageLeague() {
				var form = document.getElementById('prizeWinners');
				var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
				var prizeTime=form.elements['prizeTime'].options[form.elements['prizeTime'].options.selectedIndex].value
				self.location='prizeWinners.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&prizeTime=' + prizeTime;
			}
			function reloadPagePrize() {
				var form = document.getElementById('prizeWinners');
				var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
				var prizeTime=form.elements['prizeTime'].options[form.elements['prizeTime'].options.selectedIndex].value;
				
				self.location='prizeWinners.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&prizeTime=' + prizeTime;
			}
			
			function checkAll(toCheck) {
				var teams = document.getElementsByName(toCheck);
				for (i = 0; i < teams.length; i++)
					teams[i].checked = true ;
			}
			
			function checkYesNo() {
				return confirm('Are you sure you want to perform this action?');
			}
			function openManagePrizes() {
				open('managePrizes.php', '_blank');
				return false;
			}
			function manageTimes() {
				open('managePrizeTimes.php', '_blank');
				return false;
			}
			function printWinners(timeFrame) {
				open('printPrizeWinners.php?prizeTime=' + timeFrame, '_blank');
				return false;
			}
		</script>";
$container = new Container('Prizes', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/przFormFunctions.php');
require_once('includeFiles/przSQLFunctions.php');
require_once('includeFiles/przVariableDeclarations.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');


if(isset($_POST['moveTeams'])) {
	moveTeams($prizeTime);
} else if (isset($_POST['moveWinners'])) {
	moveWinners();
} else if (isset($_POST['updatePrizes'])) {
	updatePrizes();
}

$sportsDD = getSportDD($sportID);
$leaguesDD = getLeaguesDD($sportID, -1, $leagueID);
$prizeTimesDD = getPrizeTimesDD($prizeTime);
$teamsArray = getTeamsInfo($sportID, $leagueID);
/*Variable with the past winners ID*/ 
$pastWinners = getPastWinnerIDs();
removePastWinners($pastWinners, $teamsArray);
$curWinners = getCurrentWinners($sortBy, $prizeTime);?>
       
<form id='prizeWinners' METHOD='POST' action='<?php print "prizeWinners.php?sportID=$sportID&leagueID=$leagueID&prizeTime=$prizeTime"?>'>
<h1>Manage Prize Winners</h1>

<div class='tableData'>
	<table style="font:90%">
		<?php printTeamsHeader($sportsDD, $leaguesDD); ?>
		<?php printTeamsTop();
		$count = 1;
		foreach($teamsArray as $team) {
			if($team->teamID != 0) {
				printTeamNode($count, $team);
				$count++;
			}
		} ?>
		<?php printButtons(1); ?>
	</table><table style="font:90%">
		<?php printWinnersHeader($prizeTimesDD, $prizeTime); ?>
		<?php printWinnersTop($sportID, $leagueID);
		$count = 1;
		if(count($curWinners) > 0) {
			foreach($curWinners as $team) {
				if($team->teamID != 0) {
					printWinnerNode($count, $team);
					$count++;
				}
			} 
		}?>
		<?php printButtons(2); ?>
	</table>
    <br>
</div>
</form>
		
<?php	$container->printFooter(); ?>