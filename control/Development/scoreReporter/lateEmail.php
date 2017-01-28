<?php

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');

print "Content-type: text/html\n\n"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">   
    <head>
        <title>Late Emails</title>
	</head>
    <body>

<?php $dayOfWeek = date('N');
$dayOfYear = date('z');

/*if($_GET['userStartName'] != 'davekelly' && $userStartName != 'davekelly') {
	sendEmails(array('alex@perpetualmotion.org'), 'scores@perpetualmotion.org', 'Late Emails', 
		'Late emails failed to send, username was incorrect');
	exit(0);
}*/

$teamsQuery = mysql_query("SELECT * FROM $teamsTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
	INNER JOIN $playersTable ON $playersTable.player_team_id = $teamsTable.team_id
	INNER JOIN $datesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number
	WHERE league_week_in_score_reporter = date_week_number AND league_sport_id = date_sport_id AND season_id = date_season_id 
	AND season_available_score_reporter = 1 AND ($dayOfWeek >= league_day_number + 1 OR (league_day_number = 7 AND ($dayOfWeek = 1 OR $dayOfWeek = 2)))	
	AND player_is_captain = 1 AND league_send_late_email = 1 AND team_num_in_league > 0 AND team_most_recent_week_submitted < 
	league_week_in_score_reporter AND $dayOfYear >= date_day_of_year_num AND team_dropped_out = 0") 
	or die('ERROR getting teams - '.mysql_error());

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
	$lateEmailAllowed = $teamArray['team_late_email_allowed'];
	$gameDateString = dayString($leagueDay).' '.$gameDate;
	
	if($dayOfWeek == ($leagueDay +1) % 7) {
		$emailFilter = 1;
	} else if ($dayOfWeek == ($leagueDay +2) % 7) {
		$emailFilter = 2;
	} else {
		$emailFilter = 0;
	}
	
	if($noPractice == 1){
		//if($lateEmailAllowed == 1) {   not working?! check
		print($lateEmailAllowed." ");
		//mailRemind($sportID, $leagueID, $teamID, $leagueName, $teamName, $emailFilter, $gameDate, $capFirst, $capLast, $capEmail);
		//}
	}

} ?>
	</body>
</html>

<?php function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
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
		
        sendEmailsNotSMTP(array($capEmail), 'scores@perpetualmotion.org', $subject, $capMessage);

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
			
            sendEmailsNotSMTP(array($altEmail), 'scores@perpetualmotion.org', $subject, $altMessage);
		}
	}
}//end function mailRemind ?>