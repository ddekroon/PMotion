<?php /*****************************************
File: addSpirit.php
Creator: Derek Dekroon
Created: August 7/2012
Program used to create hidden spirit scores for teams. This way, a user can modify a spirit to their liking without 
screwing up the score submission system. Necessary because spirit scores get calculated every time they're shown, they're
not just a number spit out from the database that can be modified.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/addSptJavaFunctions.js'></script>";
$container = new Container('Add a Spirit', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/addSptFunctions.php');
require_once('includeFiles/class_spirit.php');

if(isset($_POST['addSpiritScore'])) {
	addSpirit($teamID, $leagueID, $sportID);
}
if(isset($_POST['deleteSpiritScores'])) {
	deleteOldSpirits();
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, 0, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 

$numSpirits = getOldSpirits(); ?>

<form id='oldSpiritForm' action='<?php print "addSpirit.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID" ?>' method='post'
	onSubmit="return checkSure()">
	
	<h1>Add Spirit Scores</h1>
	<div class='getIDs'>
		<?php printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown); ?>
	</div>
	<?php if($teamID > 0) { ?>
		<div class='tableData'>
			<table>
				<?php printAddSpiritForm();  ?>
			</table>
		</div>
	<?php }?>
	<div class='tableData'>
		<table>
		<?php
			printOldSpiritsHeader();
			for($i=0;$i<$numSpirits;$i++) {
				printOldSpiritNode($spiritObj[$i], $i);
			}
			printOldSpiritsButtons($numSpirits); ?>
		</table>
	</div>
</form>
		
<?php $container->printFooter(); ?>