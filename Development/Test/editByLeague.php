<?php /*****************************************
File: editByLeague.php
Creator: Derek Dekroon
Created: August 5/2012
Allows a user to edit the teams/players registered for a tournament
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/edtLeagueJavaFunctions.js'></script>";
$container = new Container('Tourney Control Panel', '', $javaScript);

require_once('includeFiles/edtLeagueVariableDeclarations.php');
require_once('includeFiles/edtLeagueFormFunctions.php');
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

<form id="teamForm" action='editByLeague.php?tournamentID=<?php print $tourneyID?>' method="post">
	<h1>Tournament Registration Control Panel</h1>
	<div class='getIDs'>
		<?php printTopInfo($tourneysDropDown, $leaguesDropDown); ?>
	</div>
	<?php if($tourneyObj->tourneyIsTeams == 1) {
		 $tourneyObj->tourneyIsLeagues == 1 ? $numLeagues = $tourneyObj->tourneyNumLeagues: $numLeagues = 1;
		for($j=0;$j < $numLeagues; $j++) { ?>
			<div class='tableData'>
				<table>	
					<?php $tourneyObj->tourneyIsLeagues == 1?printLeagueHeader($tourneyObj->tourneyLeagueNames[$j]):printLeagueHeader($tourneyObj->tourneyName);
					printTeamsHeader($tourneyObj);
					for($i=0;$i < $tourneyObj->tourneyNumTeams[$j]; $i++) {
						if($tourneyTeam[$j][$i+1]->teamIsWaiting == 0) {
							printTeamNode($i+1, $tourneyTeam[$j][$i+1], $tourneyObj);
						}
					}
					printTeamsEmail($tourneyTeam[$j], $tourneyObj);
					printTeamsHoldingTank($tourneyObj, $tourneyTeam[$j]); ?>
				</table>
			</div>
		<?php } ?>
		<div class='tableData'>
			<?php printTeamsFooter($tourneyID); ?>
		</div>
	<?php } else if ($tourneyObj->tourneyIsCards == 1) {	?>
		<?php $tourneyObj->tourneyIsLeagues == 1 ? $numLeagues = $tourneyObj->tourneyNumLeagues: $numLeagues = 1;
		for($j=0;$j < $numLeagues; $j++) { ?>
			<div class='tableData'>
			<?php $tourneyObj->tourneyIsLeagues == 1?print '<h5>'.$tourneyObj->tourneyLeagueNames[$j].'</h5>':''; ?>
				<?php for($k=0;$k<2;$k++) { ?>
					<table>
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
		<div class='tableData'>
			<?php printCardsFooter($tourneyPlayer, $tourneyID, $tourneyObj, $leagueID); ?>
		</div>
	<?php } else if($tourneyObj->tourneyIsPlayers == 1) { ?>
		<div class='tableData'>
			<table>
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
				printPlayersHoldingTank($tourneyPlayer[$j]);
				printPlayersFooter($tourneyID); ?>
			</table>
		</div>
	<?php } ?>
		
</form>

<?php $container->printFooter(); ?>