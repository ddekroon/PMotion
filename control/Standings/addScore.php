<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* ? 2012
* addScore.php
*
* Adds a phantom score report for a team.No idea when I made it, some time in '12
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/addScrJavaFunctions.js'></script>";
$container = new Container('Add Phantom Score', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/addScrFunctions.php');
require_once('includeFiles/scoreClass.php');

if(isset($_POST['addScore'])) {
	addScore($teamID, $numGames);
}
if(isset($_POST['deleteScores'])) {
	updateDatabase();
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, -1, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 

$numScores = getDatabaseInfo(); ?>

<form id="teamForm" action='<?php print "addScore.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID" ?>' 
	method="post" onSubmit="return checkSure()">
<h1>Add Scores</h1>
<div class='getIDs'>
	<?php printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown); ?>
</div>
<div class='tableData'>
	<table>
		<?php if($teamID > 0) {
			printAddScoreForm($numGames, $weekNum, $seasonID, $leagueID); 
		}?>
	</table>
</div><div class='tableData'>
	<table>
	<?php
		printOldScoresHeader();
		for($i=0;$i<$numScores;$i++) {
			printOldScoreNode($scoreObj[$i], $i);
		}
		printOldScoresButtons($numScores); ?>
	</table>
</div>
</form>

<?php $container->printFooter(); ?>