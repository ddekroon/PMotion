<?php /*****************************************
File: closeLeagueWeek.php
Creator: Derek Dekroon
Created: June 5/2013
File to close a league week with one button. Compares all the scheduled matches and score submissions to figure out
what the probable outcomes of any unsubmitted matches would be. User needs only click submit and the values are 
automatically added to the database.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/clsWeekJS.js'></script>";
$container = new Container('Close League Weeks', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/clsWeekVariableDeclarations.php');
require_once('includeFiles/clsWeekFormFunctions.php');
require_once('includeFiles/clsWeekSQLFunctions.php');
require_once('includeFiles/teamClass.php');
if(isset($_POST['closeWeek'])) {
	getPostData($leagueID);
	updateWeek($leagueID, $dateID);
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, 0, $leagueID);
$datesDropDown = getWeekDD($dateID, $leagueID);

global $teamIDs;

if($leagueID != 0) {
	$teamNames = getGlobalVariables($leagueID);
	getWeekData($leagueID, $dateID); 
	if(count($team) > 0) {
		$numRows = getScheduledMatchesInfo($leagueID, $dateID, $team, $teamIDs);
		getOppSubmissions($leagueID, $dateID, $team, $teamIDs,1);
	}
} ?>

<form id='leagueForm' action=<?php print "closeLeagueWeek.php?sportID=$sportID&leagueID=$leagueID&dateID=$dateID" ?> method='post'>
	<h1>Close League Standings</h1>
	<?php printLeagueTopInfo($sportsDropDown, $leaguesDropDown, $datesDropDown); ?>
	<div class='tableData'>
	<table>
		<?php printTeamsHeader();
		if($leagueID != 0 && count($team) && $numRows > 0) {
			$i = 0;
			$dropDownIndex = 0;
			foreach($team as $curTeam) {
				if(isset($curTeam->teamOppSubmission[$curTeam->teamOppTeamID1])) {
					printTeamNode($curTeam, $i++);
				}
			}
		}
		printTeamsFooter($leagueWeek);  ?>
	</table>
	</div>
</form>

<?php $container->printFooter(); ?>
