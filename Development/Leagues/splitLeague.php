<?php /*****************************************
File: splitLeague.php
Creator: Derek Dekroon
Created: July 4/2012
Splits two leagues into two new ones. The new leagues are created at the same time the teams are put into the two new 
leagues. Team picture IDs do NOT change during this process.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/splitJavaFunctions.js'/></script>";
$container = new Container('Split a League', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/splitFormFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/splitVariableDeclarations.php');
require_once('includeFiles/splitSQLFunctions.php');

if(!isset($_POST['setup'])) {
	$numTeams = getTeamsData($leagueID);
} else {
	$numTeams = getPostData();
} ?>

<?php if(!isset($_POST['setup'])){ ?>
	<form NAME='splitLeague' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']."?sportID=$sportID&leagueID=$leagueID"?>>
		<input type='hidden' name='numTeams' value=<?php print $numTeams ?> />
		<h1>Split a League</h1>
		<div class='tableData'>
				<?php printTopInfo($sportsDD, $leaguesDD, $leagueNameOne, $leagueNameTwo); ?>
		</div><div class='tableData'>
			<?php if($leagueID != 0) { ?>
				<table>
					<?php printTeamsHeader();
					for($i=0;$i<$numTeams;$i++) {
						printTeamNode($i, $numTeams, $team[$i]);
					} ?>
					<?php printBottomButton() ?>
				</table>
			<?php } ?>
		</div>
	</form>
<?php } else {
	
	$leagueIDOne = createNewLeague($leagueID, $leagueNameOne);
	$leagueIDTwo = createNewLeague($leagueID, $leagueNameTwo);
	print '<u>Teams changed:</u><br /><br />';
	
	for($i=0; $i< $numTeams; $i++){
		$teamNewLeague[$i] == 1 ? $teamLeagueID = $leagueIDOne : $teamLeagueID = $leagueIDTwo;
		mysql_query("UPDATE $teamsTable SET team_league_id = $teamLeagueID WHERE team_id = $teamID[$i]") 
			or die('ERROR changing leagues - '.mysql_error());
		$teamNewLeague[$i] == 1 ? $curLeagueName = $leagueNameOne : $curLeagueName = $leagueNameTwo;
		print "$teamName[$i] - $curLeagueName<br />";
	} 
	setLeagueValues($leagueID, $leagueIDOne, $leagueIDTwo);
	$container->printSuccess('Leagues split successfully');
}

$container->printFooter() ?>