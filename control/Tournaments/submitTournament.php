<?php /*****************************************
File: submitTournament.php
Creator: Derek Dekroon
Created: August 2/2012
Allows a user to edit tournament parameters
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/sbmtTrnyJSFunctions.js'/></script>";
$container = new Container('Edit Tourney Parameters', 'includeFiles/tournamentStyle.css', $javaScript);

require_once('includeFiles/sbmtTrnyFormFunctions.php');
require_once('includeFiles/sbmtTrnyVariableDeclarations.php');
require_once('includeFiles/sbmtTrnySQLFunctions.php');
require_once('includeFiles/tournamentClass.php');

if(($tourneyID = $_GET['tourneyID']) == '') {
	$tourneyID = 0;
}

if(!isset($_POST['SubmitTourney'])) {
	$tourneyObj = loadDatabaseValues($tourneyID);
} else {
	$tourneyObj = getSubmittedData($tourneyID);
	updateLeague($tourneyObj);
	$container->printSuccess('Tournament Updated');	
}
$tourneysDropDown = getTournamentDD($tourneyID); ?>

<form id='results' method='POST' action='<?php print $_SERVER['PHP_SELF'].'?tourneyID='.$tourneyID ?>'>
	<h1>
		<?php if ($tourneyAvailableID == 0) { ?>
			Create Tournament
		<?php } else { ?>
			Update Tournament
		<?php } ?>
	</h1>
	<div class='getIDs'>
			<?php printIDs($tourneysDropDown) ?>
		</div>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=10>
					Tournament Numbers
				</th>
			</tr>
			<?php printTeamsAndLeagues($tourneyObj); ?>
		</table>
	</div><div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					Tournament Parameters
				</th>
			</tr>
			<?php printForm($tourneyObj); ?>
		</table>
	</div><div class='tableData'>
				<?php printFooter(); ?>
	</div>          
</form>

<?php $container->printFooter(); ?>