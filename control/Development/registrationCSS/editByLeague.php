<?php /*****************************************
File: editByLeague.php
Creator: Derek Dekroon
Created: June 10/2012
Leagues Control Panel, the programs Dave uses to manage any and all registration concerns.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/leagJavaFunctions.js'></script>";
$container = new Container('Registration Control Panel', 'includeFiles/registrationStyle.css', $javaScript);

require_once('includeFiles/leagVariableDeclarations.php');
require_once('includeFiles/leagFormFunctions.php');
require_once('includeFiles/leagSQLFunctions.php');
require_once('includeFiles/teamClass.php');

if(isset($_POST['deleteTeams'])) {
	if(($teamCount = $_POST['teamCount']) == '') {
		$teamCount = 0;
	}
	deleteTeams($teamCount);
}

if(isset($_POST['deleteAgents'])) {
	deleteAgents();
}

if(isset($_POST['updateTeamNums'])) {
	if(($teamCount = $_POST['teamCount']) == '') {
		$teamCount = 0;
	}
	updateTeamInfoDB($teamCount);
}

if(isset($_POST['addTeam'])) {
	if(($teamCount = $_POST['teamCount']) == '') {
		$teamCount = 0;
	}
	if(($teamName = $_POST['newTeamName']) != '') {
		addTeam($teamName, $leagueID, $sportID, $teamCount);
	}
	print $teamName;
}

if(isset($_POST['addFenceTeams'])) {
    addFenceTeams();
}

if(isset($_POST['delFenceTeams'])) {
    deleteFenceTeams();
}

if($leagueID != 0) {
	$leagueArray = mysql_fetch_array(mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID"));
	$leagueIsSplit = $leagueArray['league_is_split'];
}


$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getControlLeaguesDD($sportID, $leagueID);

$teamNum = getDatabaseTeams($leagueID);
if($leagueIsSplit == 0) {
	updateTeamPictureNames();
}

$agentsNum = getAgents($leagueID);?>

<form id="leagueForm" action='<?php print "editByLeague.php?sportID=$sportID&leagueID=$leagueID" ?>' method="post" 
	onSubmit="return checkSure()">
<h1>Registration Control Panel</h1>
<div class='getIDs'>
	<?php printLeagueTopInfo($sportsDropDown, $leaguesDropDown); ?>
</div>
<div class="tableData">
<table>
	<?php printTeamsHeader();
	for($i=0;$i<$teamNum;$i++) {
		printTeamNode($i);
	}
	printTeamsFooter($teamNum, $leagueID); ?>
	<input type="hidden" name='teamCount' value=<?php print $teamNum ?>>
</table><table>
	<?php 
	printAgentsHeader();
	for($i=0;$i<$agentsNum;$i++) {
		printAgentNode($i);
	} 
	printAgentsFooter($teamNum, $leagueID);?>
	<input type="hidden" name='agentCount' value=<?php print $agentsNum ?>>
</table>
</div><div class="tableData">
<table class='1col'>
	<tr>
		<th colspan=6>
			Teams on the Fence
		</th>
	</tr>
	<?php printUnregTeams($unregTeam);?>
</table>
</div>
</form>
<?php $container->printFooter(); ?>