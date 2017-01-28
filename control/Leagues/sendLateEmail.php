<?php /*****************************************
File: sendLateEmail.php
Creator: Derek Dekroon
Created: July 8/2013
Manually sends the late score report emails when the automatic one doesn't work. Requires a user to click submit and okay
for... security? I guess
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).$calendarPage);
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
$javaScript = "<script type='text/javascript' src='$calendarJS'></script>";
$container = new Container('Send Late Emails', 'includeFiles/leagueStyle.css', $javaScript); ?>
<link href="<?php print $calendarCSS ?>" rel="stylesheet" type="text/css" />

<?php if(isset($_POST['sendEmails'])) { 
	$dateTokens = explode('-', $_POST['date']);
	$selectedDay = $dateTokens[2];
	$selectedMonth = $dateTokens[1];
	$selectedYear = $dateTokens[0];
	sendLateEmails($selectedDay, $selectedMonth, $selectedYear);
} 

$minYear = date('Y') - 3;
$maxYear = date('Y') + 3;

$myCalendar = new tc_calendar("date", true);
$myCalendar->setIcon("$calendarRoot/images/iconCalendar.gif");
if(isset($selectedDay)) {
	$myCalendar->setDate($selectedDay, $selectedMonth, $selectedYear);
} else {
	$myCalendar->setDate(date('d'), date('m'), date('Y'));
}
$myCalendar->setPath("$calendarRoot/");
$myCalendar->setYearInterval(1960, $maxYear);
$myCalendar->dateAllow($minYear.'-01-01', $maxYear.'-03-01');
$myCalendar->setOnChange("myChanged('test')");?>
	
<form action='<?php print $_SERVER['PHP_SELF'] ?>' method='post' id='Schedule'>
	<h1>Send Late Emails</h1>
	<div class='tableData'>
		<?php $myCalendar->writeScript(); ?>
		<input type='submit' name="sendEmails" value='Send Emails' onClick="return confirm('Are you sure you want to send late emails?')" />
	</div>
</form>

<?php $container->printFooter(); 

function sendLateEmails($selectedDay, $selectedMonth, $selectedYear) {
	global $leaguesTable, $seasonsTable, $playersTable, $teamsTable, $datesTable, $scheduledMatchesTable;
	$dayOfWeek = date('N', mktime(0, 0, 0, $selectedMonth, $selectedDay, $selectedYear));
	$dayOfYear = date('z', mktime(0, 0, 0, $selectedMonth, $selectedDay, $selectedYear));
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
		INNER JOIN $datesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number
		WHERE league_week_in_score_reporter = date_week_number AND league_sport_id = date_sport_id AND season_id = date_season_id 
		AND season_available_score_reporter = 1 AND ($dayOfWeek >= league_day_number + 1 OR (league_day_number = 7 AND ($dayOfWeek = 1 OR $dayOfWeek = 2)))	
		AND player_is_captain = 1 AND league_send_late_email = 1 AND team_num_in_league > 0 AND team_most_recent_week_submitted < 
		league_week_in_score_reporter AND $dayOfYear >= date_day_of_year_num") or die('ERROR getting teams - '.mysql_error());
	
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$dateID = $teamArray['date_id'];
		$teamID=$teamArray['team_id'];
		$noPractice = 0; //by default, assumes all teams are only practicing
		$matchesQuery = mysql_query("SELECT * FROM $scheduledMatchesTable WHERE (scheduled_match_team_id_1 = $teamID OR
			scheduled_match_team_id_2 = $teamID) AND scheduled_match_date_id = $dateID") 
			or die('error getting matches for team '.$teamID.' '.mysql_error());
		if(mysql_num_rows($matchesQuery) == 0) {
			$noPractice = 1;
		} else {
			while($matchArray = mysql_fetch_array($matchesQuery)) {
				//print $matchArray['scheduled_match_team_id_1'].' '.$matchArray['scheduled_match_team_id_2'].'<br />';
				if(($matchArray['scheduled_match_team_id_1'] != 1 && $matchArray['scheduled_match_team_id_2'] == $teamID) ||
					($matchArray['scheduled_match_team_id_1'] == $teamID && $matchArray['scheduled_match_team_id_2'] != 1)) {
					$noPractice = 1; //there is a match against another team, this team didn't practice this week
				}
			}
		}
		$sportID = $teamArray['sport_id'];
		$leagueID=$teamArray['league_id'];
		
		$leagueName=$teamArray['league_name'];
		$teamName=$teamArray['team_name'];
		$leagueDay = $teamArray['league_day_number'];
		$gameDate = $teamArray['date_description'];
		$capFirst = $teamArray['player_firstname'];
		$capLast = $teamArray['player_lastname'];
		$capEmail = $teamArray['player_email'];
		$gameDateString = dayString($leagueDay).' '.$gameDate;
		if($dayOfWeek == ($leagueDay +1) % 7) {
			$emailFilter = 1;
		} else if ($dayOfWeek == ($leagueDay +2) % 7) {
			$emailFilter = 2;
		} else {
			$emailFilter = 0;
		}
		if($noPractice == 1) {
			mailRemind($sportID, $leagueID, $teamID, $leagueName, $teamName, $emailFilter, $gameDate, $capFirst, $capLast, $capEmail);
		}
	
	}//end while showInStandings=1
}

function mailRemind($sportID, $leagueID, $teamID, $leagueName, $teamName, $emailFilter, $gameDayString, $capFirst, $capLast, $capEmail){
	global $playersTable;
	 
	if($emailFilter == 1) { //3 days late, send to first other player
    	$subject="Missing Score Submission - ".$teamName;

		### Message to Captain ###
		
		$capMessage=
		"Sent To: $capFirst $capLast [$capEmail]<BR><BR>
		
		Hey $capFirst ($teamName Team Captain),<BR><BR>
		We noticed that you haven't submitted your results from ".$gameDayString."'s game yet.
		Please submit online as soon as possible at <a href='http://www.perpetualmotion.org'>www.perpetualmotion.org</a>.
		<BR>
		Thanks in advance and good luck for the rest of the season!
		<BR>
		The Perpetual Motion Team<br>
		<a href='mailto:scores@perpetualmotion.org?subject=RE:".$subject."'>scores@perpetualmotion.org</a><BR>";
        sendEmails(array($capEmail,), 'scores@perpetualmotion.org', $subject, $capMessage);

	} else if ($emailFilter == 2) {
		$playersQuery = mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID AND player_is_captain = 0");
		$numPlayers = 0;
		$altCaptainFound = 0;
		while($playerArray = mysql_fetch_array($playersQuery)) {
			$playerFirstName = $playerArray['player_firstname'];
			$playerLastName = $playerArray['player_lastname'];
			$playerEmail = $playerArray['player_email'];
			if(strlen($playerFirstName) > 0 && strlen($playerLastName) > 0 && filter_var($playerEmail, FILTER_VALIDATE_EMAIL) && $altCaptainFound == 0) {
				$altFirstName = $playerFirstName;
				$altLastName = $playerLastName;
				$altEmail = $playerEmail;
				$altCaptainFound = 1;
			}
		}
		if($altCaptainFound == 1) {
			$subject="Missing Score Submission - ".$teamName;

		    ### Message to Assistant Captain ###

			$altMessage=
			"Sent To: $altFirstName $altLastName [$altEmail]<BR><BR>
			
			Hey $altFirstName ($teamName),<BR><BR>
			We noticed that $capFirst hasn't submitted results from ".$gameDayString."'s game yet. Please submit the teams' results online
			as soon as possible at <a href='http://www.perpetualmotion.org'>www.perpetualmotion.org</a>.
			<BR>
			Thanks in advance and good luck for the rest of the season!
			<BR>
			The Perpetual Motion Team<br>
			<a href='mailto:scores@perpetualmotion.org?subject=RE:".$subject."'>scores@perpetualmotion.org</a><BR>";
            sendEmails(array($altEmail,), 'scores@perpetualmotion.org', $subject, $altMessage);
		}
	}
}//end function mailRemind ?>