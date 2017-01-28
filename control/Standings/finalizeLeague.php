<?php /*****************************************
File: finalizeLeague.php
Creator: Derek Dekroon
Created: June 18/2012
Closes out a league for good.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/finalizeJavaFunctions.js'></script>";
$container = new Container('Finalize Leagues', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/finalizeVariableDeclarations.php');
require_once('includeFiles/finalizeFormFunctions.php');
require_once('includeFiles/finalizeSQLFunctions.php');
require_once('includeFiles/teamClass.php');

if(isset($_POST['closeStandings'])) {
	if($leagueID > 0) {
	updateFinalStandings($leagueID);
	mysql_query("UPDATE $leaguesTable SET league_week_in_standings = 99 WHERE league_id = $leagueID") 
		or die('ERROR setting league week to 99 - '.mysql_error());
	} else {
		$container->printError('No league selected');
	}
} else if(isset($_POST['saveStandings'])) {
	if($leagueID > 0) {
		updateFinalStandings($leagueID);
	} else {
		$container->printError('No league selected');
	}
} else if(isset($_POST['undoStandings'])) {
	if($leagueID > 0) {
		$leagueArray = mysql_fetch_array(mysql_query("SELECT league_week_in_score_reporter FROM $leaguesTable WHERE league_id = $leagueID") 
			or die('ERROR getting playoff week - '.mysql_error()));
		$leagueWeek = $leagueArray['league_week_in_score_reporter'];
		mysql_query("UPDATE $leaguesTable SET league_week_in_standings = $leagueWeek WHERE league_id = $leagueID") 
			or die('ERROR setting week to playoff week - '.mysql_error());
	} else {
		$container->printError('No league selected');
	}	
}

$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);

if($leagueID != 0) {
	$leaguesQuery = mysql_query("SELECT league_sort_by_win_pct, league_week_in_standings FROM $leaguesTable WHERE league_id = $leagueID") or die('ERROR getting league data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leaguesQuery);
	$leagueSortByPerc = $leagueArray['league_sort_by_win_pct'];
	$leagueWeek = $leagueArray['league_week_in_standings'];
	$teamNum = getTeamsData($leagueSortByPerc, $leagueID); 
} 


$teamsObjArray = loadPlayoffScoreSubmissions($sportID, $leagueID); ?>

<input type="hidden" id="tabIndex" value="<?php print $activeTabIndex ?>" />
<h1>Close League Standings</h1>
<div class='getIDs'>
	<?php printIDs($sportsDropDown, $seasonsDropDown, $leaguesDropDown); ?>
</div>
<div id="tabContainer" class='tabs box'>
	<ul class='tabrow'>
		<li><a href="#tabs-1">Helper</a></li>
		<li><a href="#tabs-2">Form</a></li>
	</ul>
	<div id="tabs-1">
		<?php if($leagueID != 0) { ?>
			<div class='tableData'>
				<table>
					<?php printHelperHeader($matches, $games); ?>
					<?php for($i=0;$i<count($teamsObjArray);$i++) { ?>
					<tr>
						<?php printHelperTeam($teamsObjArray[$i], $i); ?>
					</tr>
					<?php } ?>
				</table>
			</div>
			<div class='tableData'>
			<?php $type='playoff';
			require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Schedule'.DIRECTORY_SEPARATOR.'schedule.php'); ?>
			</div>
		<?php } else {
			$container->printInfo('Please enter Sport, Season, and League using the dropdowns above');
		}?>
	</div><div id="tabs-2">
		<?php if($leagueID != 0) { ?>
			<form id='leagueForm' action='<?php print $_SERVER['PHP_SELF']."?sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID&activeTabIndex=1"?>' method='post'>
				<div class='tableData'>
					<table>
						<?php printTeamsHeader();
						for($i=0;$i<$teamNum;$i++) {
							printTeamNode($team[$i], $i);
						} ?>
						<input type="hidden" name='teamCount' value=<?php print $teamNum ?>>
						<?php printTeamsFooter($leagueWeek); ?>
					</table>
				</div>
			</form>
		<?php } else {
			$container->printInfo('Please enter Sport, Season, and League using the dropdowns above');
		}?>
	</div>
</div>
		
<?php $container->printFooter(); ?>