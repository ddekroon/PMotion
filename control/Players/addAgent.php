<?php /*****************************************
File: addAgent.php
Creator: Derek Dekroon
Created: July 20/2012
Adds a free agent to the league specified. Don't think this has ever been used but it's here
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/agntJavaFunctions.js'></script>";
$container = new Container('Add a Free Agent', 'includeFiles/playerStyle.css', $javaScript);
require_once('includeFiles/agntVariableDeclarations.php');
require_once('includeFiles/agntFormFunctions.php');
require_once('includeFiles/agntSQLFunctions.php');

if(($numPlayers = $_GET['numPlayers']) == '') {
	$numPlayers = 1;
}

if(isset($_POST['addPlayer'])) {
	createAgent($teamID, $leagueID);
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, -1, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 

getPostPlayerData();?>

<form id="teamForm" action='<?php print 'addAgent.php?sportID='.$sportID.'&leagueID='.$leagueID.'&teamID='.$teamID.
	'&numPlayers='.$numPlayers ?>' method="post" onSubmit="return checkSure()">
	<h1>Add a Player</h1>
	<div class="getIDs">
		<?php printAgentTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown, 0); ?>
	</div>
	<div class='tableData'>
		<table>
			<?php printAgentForm($numPlayers, 0); ?>
			<?php printAgentFooter($numPlayers); ?>
		</table>
	</div>
	<input type="hidden" name='playerCount' value=<?php print $numPlayers ?>>
</form>
<?php $container->printFooter(); ?>