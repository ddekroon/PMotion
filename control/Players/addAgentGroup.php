<?php /*****************************************
File: addAgentGroup.php
Creator: Derek Dekroon
Created: July 21/2012
Program to manually add a group of players, I don't think this program has ever been used because even if Dave got an 
email for registration he'd use the regular registration program or add through the registration control panel.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/agntJavaFunctions.js'></script>";
$container = new Container('Add Agent Group', 'includeFiles/playerStyle.css', $javaScript);
require_once('includeFiles/agntVariableDeclarations.php');
require_once('includeFiles/agntFormFunctions.php');
require_once('includeFiles/agntSQLFunctions.php');

if(($numPlayers = $_GET['numPlayers']) == '') {
	$numPlayers = 3;
}

if(isset($_POST['addPlayer'])) {
	createAgentGroup($teamID, $leagueID);
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, -1, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID);

getPostPlayerData();
$playerNum = 0; ?>

<form id="teamForm" action='<?php print 'addAgentGroup.php?sportID='.$sportID.'&leagueID='.$leagueID.'&teamID='.$teamID.
	'&numPlayers='.$numPlayers ?>' method="post" onSubmit="return checkSure()">
	<h1>Add Players</h1>
	<div class="getIDs">
		<?php printAgentTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown, 1); ?>
	</div>
	<?php for($i = 0;$i < ceil($numPlayers/3);$i++) { ?>
		<div class='tableData'>
			<?php $numPlayers - (3*$i) > 3?$numRows = 3:$numRows = $numPlayers - ($i*3);
				for($j=0;$j< $numRows;$j++) { ?>
					<table> 
						<?php printAgentForm($numPlayers, $playerNum); ?>
					</table>
				<?php $playerNum++;
			} ?>
		</div>
	<?php } ?>
	<div class='tableData'>
		<?php printAgentFooter($numPlayers); ?>
	</div>
	<input type='hidden' name='isIndividual' value=1 />
</form>
<?php $container->printFooter(); ?>