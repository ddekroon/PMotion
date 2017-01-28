<?php 
/*****************************************
File: createScheduleSource.php
Creator: Derek Dekroon
Created: May 30/2013
If this ever works I will be incredibly happy. Program that dynamically makes schedules, should be fun to try! :)

******************************************/

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'globalDeclarations.php');
require_once('includeFiles/edtScheduleDeclarations.php');
require_once('includeFiles/edtScheduleSQLFunctions.php');
//require_once('includeFiles/edtScheduleFileFunctions.php');
require_once('includeFiles/edtScheduleFormFunctions.php');
require_once('includeFiles/dateClass.php');
require_once('class_lib.php');
require_once('class_week.php');
require_once('includeFiles/class_schedule_variables.php'); 

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);
$weeksDropDown = getWeeksDD($leagueID);

if(isset($_POST['submitSchedule'])) {
	if ($leagueID == 0) {
		print 'ERROR - league = 0';
		exit(0);
	} else {
		updateScheduleData($leagueID);
	}
}

if($leagueID != 0) {
    $leagueQuery = mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $teamsTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE league_id = $leagueID") or die('ERROR getting league data - '.mysql_error());
	$numTeams = mysql_num_rows($leagueQuery);
    $leagueArray = mysql_fetch_array($leagueQuery);
    $leagueScheduleLink = $leagueArray['league_schedule_link'];
	$isPractise = $leagueArray['league_has_practice_games'];
	$leagueDayOfWeek = $leagueArray['league_day_number'];
	$leagueIsSplit = $leagueArray['league_is_split'];
	$numWeeks = $leagueArray['season_num_weeks'];
} else {
    $leagueScheduleLink = '';
}

/*if($leagueID != 0) {
	$venueDD = getVenuesDD($sportID);
	$timesDropDown = getTimesDD($sportID);
	for($i = 0; $i < $maxWeeks; $i++) {
		for($j = 0; $j < $maxVenues; $j++) {
			$fieldVenuesDD[$i][$j] = $venueDD;
		}
		for($j = 0; $j < $maxTimes; $j++) {
			$timesDD[$i][$j] = $timesDropDown;
		}
	}
} */?>

<form action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&seasonID='.$seasonID.'&leagueID='.$leagueID ?> method="post" id='Schedule'>
    <input type="hidden" id="maxWeeks" value="<?php print $maxWeeks ?>" />
    <input type="hidden" id="maxVenues" value="<?php print $maxVenues ?>" />
    <input type="hidden" id="maxTimes" value="<?php print $maxTimes ?>" />
	<input type="hidden" name='numWeeks' value="<?php print $numWeeks ?>" />
    <table class='master'>
        <TR><td><table class='titleBox'><tr><th>
			Edit a Schedule
		</th></tr></table></td></TR>
		<?php printGetIDs($seasonID, $sportID, $leagueID, $weeksDropDown); ?>
		<tr>
			<td>
				<?php if ($leagueID != 0) { 
					$league_schedule = new Schedule($leagueID, 'edit');
				} ?>
			</td>
		</tr>
		<?php printBottomButton(); ?>
    </table>
</form>