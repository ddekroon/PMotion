<?php /*****************************************************************
* Bradley Connolly
* bradley@perpetualmotion.org
* June 25, 2015
* tieBreak.php
*
* Makes it easy to change standings of players who are tied  in points and spirit
************************************************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 

$javaScript = "<script type='text/javascript' src='includeFiles/tieBreakJavaFunctions.js'></script>";	
$container = new Container('Tie Break', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/tieBreakVariable.php');
require_once('includeFiles/tieBreakFormFunction.php');
require_once('includeFiles/tieBreakSQLFunction.php');
require_once('includeFiles/teamClass.php');

/* When the 'change standings' button is pressed, call 'updateSpiritForTie($teamID1, $teamID2);'*/

if(isset($_POST['saveStandings'])) 
{
	if(($team1ID > 0) && ($team2ID > 0) || ($team1ID == $team2ID))
	{
		if($leagueID > 0)
		{ 
			updateSpiritForTie($team1ID, $team2ID, $leagueID);
		} 
		else 
		{
			$container->printError('No league selected');
		}
	}
	else
	{
		$container->printError('Error selecting teams');
	}
} 

// Decloration of Drop Down Menus used
$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);
$teamsDropDown1 = getTeamDD($leagueID, $team1ID);
$teamsDropDown2 = getTeamDD($leagueID, $team2ID);

if($leagueID != 0) 
{
	$leaguesQuery = mysql_query("SELECT league_sort_by_win_pct, league_week_in_standings FROM $leaguesTable WHERE league_id = $leagueID")
		or die('ERROR getting league data - '.mysql_error());
		
	$leagueArray = mysql_fetch_array($leaguesQuery);
	$leagueWeek = $leagueArray['league_week_in_standings']; 
} ?>

<form id="teamForm" action='<?php print 'tieBreak.php?sportID='.$sportID.'&seasonID='.$seasonID.'&leagueID='.$leagueID.'&team1ID='.$team1ID.'&team2ID='.$team2ID?>' method="post" onSubmit="return checkSure()">

<? // Scrpit ?>
<h1>Tie Breaker</h1>
<script type='text/javascript' src='includeFiles/tieBreakJavaFunctions.js'/></script>

	<div class='getIDs'>
		<?php printIDs($sportsDropDown, $seasonsDropDown, $leaguesDropDown, $teamsDropDown1, $teamsDropDown2); ?>
        
	</div>
    
	<div class='tableData'>
		<?php printTeamsFooter($leagueWeek); ?>
	</div>
    
</form>

<?php $container->printInfo('This will allow the user to manipulate the standings so teams who are tied in points and spirit are able to be swapped'); 

$container->printFooter(); ?>