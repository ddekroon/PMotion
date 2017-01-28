<?php /*****************************************
File: editByLeague.php
Creator: Derek Dekroon
Created: August 5/2012
Allows a user to edit the teams/players registered for a tournament
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/edtLeagueJavaFunctions.js'></script>";

require_once('includeFiles/edtLeagueVariableDeclarations.php');
require_once('includeFiles/regPrintPgFormFunctions.php');
require_once('includeFiles/edtLeagueSQLFunctions.php');
require_once('includeFiles/tournamentClass.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');

$tourneyObj = getDefaultInfo($tourneyID);

if(isset($_POST['submitTeams'])) {
	changeTeamInfo($tourneyObj, $tourneyID);
}
if(isset($_POST['deleteTeams'])) {
	deleteTeams();
}

if(isset($_POST['submitPlayerInfo'])) {
	changeCardPlayerInfo();
}
if(isset($_POST['deleteCheckedPlayers'])) {
	deletePlayers();
}
if(isset($_POST['createCardPlayer'])) {
	$playerObj = getNewPlayerInfo();
	createNewPlayer($playerObj, $tourneyID, $tourneyObj);	
}

$tourneysDropDown = getTournamentDD($tourneyID);
$leaguesDropDown = getTourneyLeaguesDD($tourneyObj);

if(!($tourneyObj->tourneyIsLeagues == 1 && $leagueID == 10000)) {
	$leagueID == ''?$leagueID = 0:$leagueID = $leagueID;
	if($tourneyObj->tourneyIsTeams == 1) {
		$tourneyTeam = getTeamsInfo($tourneyID, $tourneyObj->tourneyIsLeagues, $tourneyObj->tourneyNumLeagues, $tourneyObj->tourneyNumTeams);
	} else if ($tourneyObj->tourneyIsCards == 1) {
		$tourneyPlayer = getPlayerCardsInfo($tourneyID, $tourneyObj->tourneyIsLeagues, $tourneyObj->tourneyNumLeagues, $tourneyObj->tourneyNumBlackCards, $tourneyObj->tourneyNumRedCards);
	} else if ($tourneyObj->tourneyIsPlayers == 1) {
		$tourneyPlayer = getPlayersInfo($tourneyID, $tourneyObj->tourneyIsLeagues, $tourneyObj->tourneyNumLeagues, $tourneyObj->tourneyNumPlayers);
	}
}?>
<html>
	<head>
		<title>
			Tournament Registration Print Page
		</title>
	</head><body>
	<?php if($tourneyObj->tourneyIsTeams == 1) {
		 $tourneyObj->tourneyIsLeagues == 1 ? $numLeagues = $tourneyObj->tourneyNumLeagues: $numLeagues = 1;
		for($j=0;$j < $numLeagues; $j++) { ?>
			<div class='tableData' style="margin-top:20px;">
				<table class='nostyle inlineTable' style="display:inline-table;">	
					<?php $tourneyObj->tourneyIsLeagues == 1?printLeagueHeader($tourneyObj->tourneyLeagueNames[$j]):printLeagueHeader($tourneyObj->tourneyName);
					printTeamsHeader($tourneyObj);
					for($i=0;$i < $tourneyObj->tourneyNumTeams[$j]; $i++) {
						if($tourneyTeam[$j][$i+1]->teamIsWaiting == 0) {
							printTeamNode($i+1, $tourneyTeam[$j][$i+1], $tourneyObj);
						}
					}
					printTeamsHoldingTank($tourneyObj, $tourneyTeam[$j]); ?>
				</table>
			</div>
		<?php } ?>
	<?php } else if ($tourneyObj->tourneyIsCards == 1) {	?>
		<?php $tourneyObj->tourneyIsLeagues == 1 ? $numLeagues = $tourneyObj->tourneyNumLeagues: $numLeagues = 1;
		for($j=0;$j < $numLeagues; $j++) { ?>
			<div class='tableData' style="page-break-after:always;">
			<?php $tourneyObj->tourneyIsLeagues == 1?print '<h2>'.$tourneyObj->tourneyLeagueNames[$j].'</h2>':''; ?>
				<?php for($k=0;$k<2;$k++) { ?>
					<table class='nostyle' style="display:inline-table;">
						<tr>
							<th colspan=6>
								<?php print $k == 0? 'Black':'Red'; ?>
							</th>
						</tr>
						<?php printCardsHeader();
						$curNum = 1;
							foreach($tourneyPlayer[$j][$k] as $curPlayer) {
								if($curPlayer->playerIsWaiting == 0) {
									printCardNode($curPlayer, $curNum++);
								}
							}
							printCardsHoldingTank($tourneyPlayer[$j][$k], $tourneyObj);?>
					</table>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else if($tourneyObj->tourneyIsPlayers == 1) { ?>
		<div class='tableData' style="page-break-after:always;">
			<table class='nostyle' style="display:inline-table;">
				<?php $tourneyObj->tourneyIsLeagues == 1 ? $numLeagues = $tourneyObj->tourneyNumLeagues: $numLeagues = 1;
				for($j=0;$j < $numLeagues; $j++) {
					$tourneyObj->tourneyIsLeagues == 1?printLeagueHeader($tourneyObj->tourneyLeagueNames[$j]):'';
					printPlayersHeader();
					for($i=0;$i<$tourneyObj->tourneyNumPlayers[$leagueID];$i++) {
						if($tourneyPlayer[$j][$i+1]->playerIsWaiting == 0) {
							printPlayerNode($i+1, $tourneyPlayer[$j][$i+1], $tourneyObj->isLeagues);
						}
					}
				}
				printPlayersHoldingTank($tourneyPlayer[$j]); ?>
			</table>
		</div>
	<?php } ?>
		
	</body>
</html>