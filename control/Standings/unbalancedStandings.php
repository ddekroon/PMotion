<?php /*****************************************
File: unbalancedStandings.php
Creator: Derek Dekroon
Created: June 13/2012
Program that tells the user of any potential issues with the score submissions/standings.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
			function checkAll() {
				var field = document.getElementsByName('remove[]');
				for (i = 0; i < field.length; i++)
					field[i].checked = true;
				return false;
			}
			function uncheckAll() {
				var field = document.getElementsByName('remove[]');
				for (i = 0; i < field.length; i++)
					field[i].checked = false;
				return false;
			}
			
			function checkYesNo() {
				return confirm('Are you sure you want to remove these submissions?');
			}
		</script>";
$container = new Container('Unbalanced Standings', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/unbalancedVariableDeclarations.php');
require_once('includeFiles/unbalancedFormFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/submissionClass.php');
require_once('includeFiles/matchClass.php');
/*line 36 gets rid of the errors being displayed on the page*/
ini_set( 'display_errors', 'off' );
if(isset($_POST['removeSubmissions'])) {
	updateDatabase();
}

$teamCount = getTeamsDatabaseInfo();
//$teams = getScheduledMatchesInfo($team);
$numSubmissions = getResultsDatabaseInfo($numTeams); ?>

<form name='badspirit' method='POST' action='teamStandingsViewer.php' onSubmit="return checkYesNo()">
<h1>Unbalanced Standings</h1>
<div class="tableData">
	<table>
		<tr>
			<th colspan=10>
				Uneven Wins/Losses/Ties
			</th>
		</tr>
		<?php $counter = 0;
		printTeamsHeader();
		foreach($team as $teamNode) {
			$oppSubmittedGames = $teamNode->teamOppWins + $teamNode->teamOppLosses + $teamNode->teamOppTies;
			$submittedGames = $teamNode->teamWins+$teamNode->teamLosses+$teamNode->teamTies;
			$standingsTotal = $teamNode->teamStndWins + $teamNode->teamStndLosses + $teamNode->teamStndTies;
			if($submittedGames == $oppSubmittedGames && ($teamNode->teamOppWins != $teamNode->teamWins 
				|| $teamNode->teamOppLosses != $teamNode->teamLosses || $teamNode->teamOppTies != 
				$teamNode->teamTies)) { 
				printTeamNode($teamNode);
				$counter++;
			}
		} 
		if($counter == 0) { ?>
			<tr>
				<td colspan=10>
					No teams with uneven standings
				</td>
			</tr>
		<?php } ?>
	</table>
</div><div class='tableData'>
	<table>
		<tr>
			<th colspan=10>
				Incorrect Standings
			</th>
		</tr>
		<?php $counter = 0;
		printStandingsHeader();
		foreach($team as $teamNode) {
			$submittedGames = $teamNode->teamWins + $teamNode->teamLosses + $teamNode->teamTies;
			$standingsTotal = $teamNode->teamStndWins + $teamNode->teamStndLosses + $teamNode->teamStndTies;
			if($submittedGames != $standingsTotal) { 
				printStandingsNode($teamNode);
				$counter++;
			}
		} 
		if($counter == 0) { ?>
			<tr>
				<td colspan=10>
					No teams with incorrect standings
				</td>
			</tr>
		<?php } ?>
	</table>
</div><div class='tableData'>
	<table>
		<tr>
			<th colspan=10>
				Incorrect Number of Score Submissions
			</th>
		</tr>
		<?php $counter = 0;
		printSubmissionsHeader();
		foreach($team as $teamNode) {
			$numSubmissions = $teamNode->leagueNumGames * $teamNode->leagueNumMatches * 
				$teamNode->leagueWeekInScoreReporter;
			if($teamNode->teamSubmissions > $numSubmissions || ($teamNode->teamSubmissions < $numSubmissions &&
				$teamNode->teamWeekInScoreReporter == $teamNode->leagueWeekInScoreReporter)) { 
				printSubmissionNode($teamNode, $numSubmissions);
				$counter++;
			}
		} 
		if($counter == 0) { ?>
			<tr>
				<td colspan=10>
					No teams with incorrect number of score submissions
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
</form>

<?php $container->printFooter(); ?>