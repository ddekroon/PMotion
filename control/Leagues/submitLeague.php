<?php /*****************************************
File: submitLeague.php
Creator: Derek Dekroon
Created: June 14/2012
Creates a new league for the sport and season specified.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
$javaScript = "<script type='text/javascript' src='includeFiles/sbmtLeagJSFunctions.js'/></script>";
$container = new Container('Submit a League', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/sbmtLeagFormFunctions.php');
require_once('includeFiles/sbmtLeagVariableDeclarations.php');
require_once('includeFiles/sbmtLeagSQLFunctions.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($update = $_GET['update']) == '') {
	$update = 0;
}
if(isset($_POST['deleteLeague'])) {
	deleteLeague($leagueID);	
}

if(!isset($_POST['SubmitLeague'])) {
	if ($update == 0) {
		loadDefaults($sportID, $seasonID);
	} else {
		loadDatabaseValues($leagueID);
	}
} else {
	getSubmittedData();
	if ($update == 0 && ($leagueID = checkForUpdate($sportID, $seasonID)) == 0) {
		$leagueID = createLeague($sportID, $seasonID);
		$container->printSuccess('League '.$leagueName.' created');
	} else {
		updateLeague($sportID, $seasonID, $leagueID);
		$container->printSuccess('League '.$leagueName.' updated');
	}
}
$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID); ?>
        
<form id='results' method='POST' action='<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&seasonID='.$seasonID.'&leagueID='.$leagueID.'&update='.$update ?>'>
	<h1><?php print $update == 0?'Create League':'Update League';?></h1>
	<div class='getIDs'>
		<?php printIDs($sportsDropDown, $seasonsDropDown) ?>
	</div>
	<div class='tableData'>
		<table>
			<?php printForm(); ?>
		</table>
	</div>           
</form>

<?php $container->printFooter() ?>