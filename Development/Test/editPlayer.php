<?php /*****************************************
File: editByLeague.php
Creator: Derek Dekroon
Created: August 7/2012
Allows a user to edit a player registered for a tournament
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/edtPlyrJavaFunctions.js'></script>";
$container = new Container('Unbalanced Standings', '', $javaScript);

require_once('includeFiles/edtPlyrVariableDeclarations.php');
require_once('includeFiles/edtPlyrFormFunctions.php');
require_once('includeFiles/edtPlyrSQLFunctions.php');
require_once('includeFiles/tournamentClass.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['update'])) {
	$playerObj = getPostPlayerData();
	updatePlayer($playerID, $playerObj);
} else {
	$playerObj = getDBPlayerData($playerID);
}
$tourneyObj = getDefaultInfo($tourneyID);
$tourneysDropDown = getTournamentDD($tourneyID);
$leaguesDropDown = getLeagueDD($tourneyObj->tourneyNumLeagues, $tourneyObj->tourneyLeagueNames, $leagueID);
$teamsDropDown = getTeamDD($tourneyID, $tourneyObj->tourneyIsLeagues, $leagueID, $teamID); 
$playersDropDown = getPlayerDD($tourneyID, $tourneyObj->tourneyIsLeagues, $leagueID, $tourneyObj->tourneyIsTeams, $teamID, $tourneyObj->tourneyIsCards, $playerID); ?>

<form id="teamForm" action='<?php print "editPlayer.php?tournamentID=$tourneyID&leagueID=$leagueID&teamID=$teamID
	&playerID= $playerID" ?>' method="post">
	<h1>Edit A Player</h1>
	<div class='tableData'>
		<?php printTopInfo($tourneysDropDown, $tourneyObj->tourneyIsLeagues, $leaguesDropDown, $tourneyObj->tourneyIsTeams, $teamsDropDown, $playersDropDown); ?>
	</div><div class='tableData'>
		<table>
			<?php printPlayerHeader($tourneyObj->tourneyIsLeagues, $leaguesDropDown, $tourneyObj->tourneyIsTeams, $teamsDropDown);
			printPlayerForm($playerObj); ?>
			<tr>
				<td colspan=2>
					<input type="submit" name="update" value='Update Player' />
				</td>
			</tr>
		</table>
	</div>
</form>

<?php $container->printFooter(); ?>