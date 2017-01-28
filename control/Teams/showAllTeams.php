<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'class_container.php');
require_once('includeFiles/shwAllFunctions.php');
require_once('includeFiles/playerClass.php');

if(($seasonID = $_GET['seasonID']) == '') {
	$seasonQuery = mysql_query("SELECT season_id FROM $seasonsTable WHERE season_available_score_reporter = 1") or die('ERROR - '.mysql_error());
	$seasonArray = mysql_fetch_array($seasonQuery);
	$seasonID = $seasonArray['season_id'];
}
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($divisionID = $_GET['divisionID']) == '') {
	$divisionID = 0;
}
$lastSportID = 0;
$lastLeagueID = 0;
$rowNum = 0;

if($seasonID != 0 && $sportID != 0) {
	$teamsObj = getTeamData($seasonID, $sportID, $divisionID);
}
$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
if($sportID == 2) {
	$divisionsDropDown = getDivisionDD($divisionID);
}?>

<html>
	<head>
    	<title>Show all Teams</title>
    	<script type="text/javascript">
			function reloadSeason(seasonDD) {
				var seasonID = seasonDD.value;
				self.location = 'showAllTeams.php?seasonID='+ seasonID;
			}
			function reloadSport(sportDD) {
				var seasonID = document.getElementById('seasonID').value;
				var sportID = sportDD.value;
				self.location = 'showAllTeams.php?seasonID='+ seasonID + '&sportID=' + sportID;
			}
			function reloadDivision(divisionDD) {
				var seasonID = document.getElementById('seasonID').value;
				var sportID = document.getElementById('sportID').value;
				var divisionID = divisionDD.value;
				self.location = 'showAllTeams.php?seasonID='+ seasonID + '&sportID=' + sportID+ '&divisionID=' + divisionID;
			}
		</script>
        <link type="text/css" rel="stylesheet" href="includeFiles/showAllTeamsStyle.css">
        </head>
    <body>
        <table class="teamTable">
        	<tr>
            	<td colspan=3>
                	Season <select id="seasonID" onChange="reloadSeason(this)">
                    	<?php print $seasonsDropDown ?>
                    </select>
				</td><td colspan=2>
                	Sport <select id="sportID" onChange="reloadSport(this)">
                    	<?php print $sportsDropDown ?>
                    </select>
                    <?php if($sportID == 2) { ?>
						Division: <select id="divisionID" onChange="reloadDivision(this)">
							<?php print $divisionsDropDown ?>
                        </select>
                    <?php } ?>
                </td>
            </tr>
            <?php if($seasonID != 0 && $sportID != 0) {
				foreach($teamsObj as $teamNode) {
					if($teamNode->playerSportID != $lastSportID) {
						printSportHeader($teamNode->playerSportName);
						$lastSportID = $teamNode->playerSportID;
					} 
					if($teamNode->playerLeagueID != $lastLeagueID) {
						printLeagueHeader($teamNode->playerLeagueName);
						$lastLeagueID = $teamNode->playerLeagueID;
					}
					$rowNum++;
					$rowNum = $rowNum % 2;
					printTeamNode($teamNode, $rowNum); 
				}
            } ?>
        </table>
	</body>
</html>