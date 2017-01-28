<?php /*****************************************
File: updatePlayer.php
Creator: Derek Dekroon
Created: June 14/2012
Program used to update a player data in the database.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/updtJavaFunctions.js'></script>";
$container = new Container('Update a Player', 'includeFiles/playerStyle.css', $javaScript);

require_once('includeFiles/updtVariableDeclarations.php');
require_once('includeFiles/updtFormFunctions.php');
require_once('includeFiles/updtSQLFunctions.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['update'])) {
	getPostPlayerData();
	updatePlayer($playerID);
} else {
	getDBPlayerData($playerID);
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 
$newLeaguesDropDown = getLeaguesDD($sportID, $seasonID, $newLeagueID);
$newTeamsDropDown = getTeamDD($newLeagueID, $newTeamID); 
$playersDropDown = getPlayerDD($leagueID, $teamID, $playerID); ?>

<form id="teamForm" action='<?php print "updatePlayer.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID&playerID=$playerID"?>'
	method="post">
	<h1>Update A Player</h1>
	<div class='tableData'>
		<table>
			<?php printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown, $playersDropDown); ?>
		</table>
		<table>
			<?php printPlayerHeader($newLeaguesDropDown, $newTeamsDropDown);
			printPlayerForm();?>
			<?php printFooter(); ?>
		</table>
		
	</div>
</form>
		
<?php $container->printFooter(); ?>