<?php /*****************************************
File: editTeam.php
Creator: Derek Dekroon
Created: August 7/2012
Allows a user to edit a team that registered for a tournament
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/edtTeamJavaFunctions.js'></script>";
$container = new Container('Edit Tourney Team', 'includeFiles/tournamentStyle.css', $javaScript);

require_once('includeFiles/edtTeamVariableDeclarations.php');
require_once('includeFiles/edtTeamFormFunctions.php');
require_once('includeFiles/edtTeamSQLFunctions.php');
require_once('includeFiles/tournamentClass.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['changeTeamName'])) {
	if(($newTeamName = $_POST['newTeamName']) == '') {
		print 'No new team name specified';
	} else {
		changeDBTeamName($teamID, $newTeamName);
	}
}
if(isset($_POST['changeLeague'])) {
	if(($newLeagueID = $_POST['newLeagueID']) == '') {
		$newLeagueID = 0;
		print 'League not specified';
	} else {
		if($newLeagueID != $leagueID && $newLeagueID != 10000) {
			changeDBLeague($teamID, $newLeagueID, $leagueID);
			$leagueID = $newLeagueID;
		} else {
			print 'League not changed';
		}
	}
}

$tourneyObj = getDefaultInfo($tourneyID);

if(isset($_POST['changeWaiting'])) {
	changeDBTeamWaiting($teamID, $_POST['isWaiting'], $tourneyObj);
}

if(isset($_POST['playerAdd'])) {
	createPlayer($tourneyID, $leagueID, $teamID);
}
if(isset($_POST['playerDelete'])) {
	deletePlayers();
}

$tourneysDropDown = getTournamentDD($tourneyID);

$leaguesDropDown = getLeagueDD($tourneyObj->tourneyNumLeagues, $tourneyObj->tourneyLeagueNames, $leagueID);
$teamsDropDown = getTeamDD($tourneyID, $tourneyObj->tourneyIsLeagues, $leagueID, $teamID); 

$playersObj = getTeamPlayerData($teamID); ?>

<form id="teamForm" action='<?php print "editTeam.php?tournamentID=$tourneyID&leagueID=$leagueID&teamID=$teamID" ?>' method='post'>
<h1>Teams Editor</h1>
<!-- This is the code for the "Select team drop down menu-->
	<?php printTopInfo($tourneyID, $tourneyObj->tourneyIsLeagues,$leagueID,$tourneysDropDown, $leaguesDropDown, $teamsDropDown);?> 
    
		<div class='tableData'>

		<?php if($teamID > 0) {
			printEditTeamForm($leaguesDropDown, getTeamWaiting($teamID)); 
		}?>
		<table>
		<?php
			printTeamTopInfo();
			printPlayersHeader();
			for($i=0; $i<count($playersObj); $i++) {
				printPlayerNode($i, $playersObj[$i]);
			}
			printPlayersFooter(); ?> 
			<input type="hidden" name='playerCount' value=<?php print $numPlayers ?>>
		</table>
	</div>
</form>

<?php $container->printFooter(); ?>