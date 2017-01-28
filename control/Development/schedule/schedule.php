<?php 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('Schedule'.DIRECTORY_SEPARATOR.'class_lib.php');
/*if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
} else {*/
$leagueID = 1260;
	$leagueArray = mysql_fetch_array(mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID"));
	$seasonID = $leagueArray['league_season_id'];
	$sportID = $leagueArray['league_sport_id'];
	$staticScheduleLink = $leagueArray['league_schedule_link'];
	$leagueShowStatic = $leagueArray['league_show_static_schedule'];
//}

if($leagueID != 0 && $leagueShowStatic == 1) {
	printStaticSchedule($staticScheduleLink);
} else if($leagueID != 0 && $leagueShowStatic == 0) {
	$leagueQuery = mysql_query("SELECT * FROM $scheduledMatchesTable WHERE scheduled_match_league_id = $leagueID") 
		or die('ERROR getting schedule data '.mysql_error());
	$numGames = mysql_num_rows($leagueQuery);
	
	if($numGames == 0) {
		printStaticSchedule($staticScheduleLink);
		exit(0);
	} else {
		if($sportID == 1) {
			$sportName = 'GuelphUltimate';
		} else if($sportID == 2) {
			$sportName = 'BeachVolleyball';
		} else if($sportID == 3) {
			$sportName = 'FlagFootball';
		} else if($sportID == 4) {
			$sportName = 'Soccer';
		}
		
		$scheduleLink = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$sportName.DIRECTORY_SEPARATOR.
				'Schedules'.DIRECTORY_SEPARATOR.'schedule-'.$leagueID.'.htm';
		
		if(file_exists($scheduleLink)) {
			$scheduleLink = "/home/pmotion/data.perpetualmotion.org/Soccer/Schedules/schedule-1260 - Copy.htm";
			require_once($scheduleLink);
		} else {
			$league_schedule = new Schedule($leagueID, 'show'); 
		}
	}
} else {
	print 'No league selected';
}

function printStaticSchedule($schedLink) {
	if($schedLink != '' && file_exists(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$schedLink)) { 
		require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$schedLink);
	} else {
		print 'League Schedule Not Uploaded';
	}
}?>