<?php /*************************
* Bradley Connolly (From Derek Dekroon's finalizeStandings.php)
* bradley@perpetualmotion
* June 25, 2015
* manuallyChangeStandings.php
*
* Change the standings of a leauge
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/changeStandingsJavaFunctions.js'></script>";
$container = new Container('Change Leauge Standings', 'includeFiles/standingsStyle.css', $javaScript);

/* INCLUDED FILES */

require_once('includeFiles/changeStandingsVariable.php');
require_once('includeFiles/changeStandingsFormFunction.php');
require_once('includeFiles/changeStandingsSQLFunction.php');
require_once('includeFiles/teamClass.php');

/* When the 'change standings' button is pressed, call 'updateFinalStandings' (edit) */

if(isset($_POST['saveStandings'])) 
{
	if($leagueID > 0)
	{ 
		updateFinalStandings($leagueID);
	} 
	else 
	{
		$container->printError('No league selected');
	}
} 


$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);

if($leagueID != 0) 
{
	$leaguesQuery = mysql_query("SELECT league_sort_by_win_pct, league_week_in_standings FROM $leaguesTable WHERE league_id = $leagueID") or die('ERROR getting league data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leaguesQuery);
	$leagueSortByPerc = $leagueArray['league_sort_by_win_pct'];
	$leagueWeek = $leagueArray['league_week_in_standings'];
	$teamNum = getTeamsData($leagueSortByPerc, $leagueID); 
} 


$teamsObjArray = loadPlayoffScoreSubmissions($sportID, $leagueID); ?>

<input type="hidden" id="tabIndex" value="<?php print $activeTabIndex ?>" />
<h1>Change Leauge Standings</h1>
<div class='getIDs'>
	<?php printIDs($sportsDropDown, $seasonsDropDown, $leaguesDropDown); ?>
</div>
		<?php if($leagueID != 0) 
		{ ?>
			<form id='leagueForm' action='<?php print $_SERVER['PHP_SELF']."?sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID&activeTabIndex=1"?>' method='post'>
				<div class='tableData'>
					<table>
						<?php printTeamsHeader();
						for($i=0;$i<$teamNum;$i++) 
						{
							printTeamNode($team[$i], $i);
						} ?>
						<input type="hidden" name='teamCount' value=<?php print $teamNum ?>>
						<?php printTeamsFooter($leagueWeek); ?>
					</table>
				</div>
			</form>
		<?php }
		 else 
		 {
			$container->printInfo('To change a leauges standings please enter a sport, season, and league using the dropdowns above');
		}?>
	</div>
</div>
		
<?php $container->printFooter(); ?>