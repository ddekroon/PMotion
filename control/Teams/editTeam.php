<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* July 24, 2012
* editTeam.php
*
* Can only be accessed by links from registration control panel. Page that allows a user to edit team parameters, 
* change what players are on the team and add/remove players to/from free agency.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/edtJavaFunctions.js'></script>";
$container = new Container('Edit Team', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/edtVariableDeclarations.php');
require_once('includeFiles/edtFormFunctions.php');
require_once('includeFiles/edtSQLFunctions.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST["updateTeamData"])) {
	if(($newTeamName = $_POST['newTeamName']) == '') {
		print 'No new team name specified';
	} else if(($newLeagueID = $_POST['newLeagueID']) == 0) {
		$newLeagueID = 0;
		print 'League not specified';
	} else {
		changeDBTeamData($teamID, $newTeamName, $leagueID, $newLeagueID, $_POST['newTeamWeek'], $_POST['teamDropped']);
		$teamWeek = $_POST['newTeamWeek'];
		$teamName = htmlentities($newTeamName, ENT_QUOTES);
	}
	$leagueID = $newLeagueID;
}

if(isset($_POST['agentAdd'])) {
	createAgent($teamID, $leagueID);
}
if(isset($_POST['playerAdd'])) {
	createPlayer($teamID, $leagueID);
}

if(isset($_POST['addAgents'])) {
	if(($numAgents = $_POST['agentCount']) == '') {
		$numAgents = 0;
	}
	addAgents($numAgents, $teamID);
}
if(isset($_POST['removePlayers'])) {
	if(($numPlayers = $_POST['playerCount']) == '') {
		$numPlayers = 0;
	}
	removePlayers($numPlayers, $teamID);
}

if(isset($_POST['deleteAgents'])) {
	if(($numAgents = $_POST['agentCount']) == '') {
		$numAgents = 0;
	}
	deleteAgents($numAgents, $teamID);
}
if(isset($_POST['deletePlayers'])) {
	if(($numPlayers = $_POST['playerCount']) == '') {
		$numPlayers = 0;
	}
	deletePlayers($numPlayers, $teamID);
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, -1, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 


$numPlayers = getTeamPlayerData($teamID);
$numAgents = getAgentsData($leagueID);?>


<form id="teamForm" action='<?php print "editTeam.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID" ?>' method="post" 
	onSubmit="return checkSure()">
	
	<h1>Team Editor</h1>
	<?php printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown); 
	if($teamID > 0) {
		printEditTeamForm($leaguesDropDown, $teamWeek, $teamPicLink, $teamDroppedOut, $teamName); 
	}?>
	<div class='tableData'>
			<table>
				<?php printTeamTopInfo();
				printPlayersHeader(0); ?>
				<?php for($i=0;$i<$numPlayers;$i++) {
					printPlayerNode($i, $player, $playerGroupID, 0);
				} ?>
				<?php printPlayersFooter($numPlayers, 0); ?>
				<input type="hidden" name='playerCount' value=<?php print $numPlayers ?>>
			</table><table>
				<?php printAgentsTopInfo();
				printPlayersHeader(1); ?>
				<?php for($i=0;$i<$numAgents;$i++) {
					printPlayerNode($i, $agent, $agentGroupID, 1);
				} ?>
				<?php printPlayersFooter($numAgents, 1); ?>
				<tr>
				<input type="hidden" name='agentCount' value=<?php print $numAgents ?>>
			</table>
	</div><div class='tableData'>
		<table>
			<?php printAddPlayers(0); ?>
		</table><table>
			<?php printAddPlayers(1); ?>
		</table>
	</div>
</form>
		
<?php $container->printFooter(); ?>