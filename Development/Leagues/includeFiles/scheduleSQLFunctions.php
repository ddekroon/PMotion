<?php
function setScheduleLink($leagueID, $URL) {
	global $leaguesTable;
	$storeLink = mysql_escape_string($URL);
	mysql_query("UPDATE $leaguesTable SET league_schedule_link = '$storeLink' WHERE league_id = $leagueID") 
		or die('ERROR storing schedule link - '.mysql_error());
}


function fixWeeks() {
	global $datesTable, $toPrint;
	$numAffected = 0;
	for($i=0;$i<count($_POST['dateID']); $i++) {
		$dateID = $_POST['dateID'][$i];
		$newWeek = $_POST['dateWeek'][$i];
		mysql_query("UPDATE $datesTable SET date_week_number = $newWeek WHERE date_id = $dateID") or die('ERROR setting new week #s - '.mysql_error());
		$numAffected++;
	}
	if($numAffected > 0) {
		print 'Week numbers fixed, '.$numAffected.' dates affected<br />';
	}
	$numAffected = 0;
	
	foreach($_POST['delDate'] as $delDate) {
		//print $delDate;
		mysql_query("DELETE FROM $datesTable WHERE date_id = $delDate") or die('ERROR deleting dates - '.mysql_error());
		$numAffected++;
	}
	if($numAffected > 0) {
		print 'Weeks deleted, '.$numAffected.' dates affected<br />';
	}
}

function setTeamNums($leagueID, $teamNamesArray) {
	global $teamsTable, $toPrint;
	$teamValuesArray = array();
	$teamNum = 1;
	
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 
		ORDER BY team_num_in_league ASC") or die('ERROR getting teams in league - '.mysql_error());
	while($teamsArray = mysql_fetch_array($teamsQuery)) {
		$teamValuesArray[$teamNum++] = array(preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($teamsArray['team_name'], ENT_QUOTES)),
			$teamsArray['team_id']);
	}
	
	for($i=1;$i <= count($teamNamesArray); $i++) {
		for($j = 1; $j <= count($teamValuesArray); $j++) {
			if(strtolower($teamNamesArray[$i]) == strtolower($teamValuesArray[$j][0])) {
				$teamName = mysql_escape_string($teamNamesArray[$i]);
				$teamID = $teamValuesArray[$j][1];
				mysql_query("UPDATE $teamsTable SET team_num_in_league = $i WHERE team_id = $teamID
					AND team_league_id = $leagueID AND team_num_in_league > 0") or die('ERROR setting team nums - '.mysql_error());
				$toPrint .= '<br />'.$i.' '.$teamName; 
				continue;
			}
		}
		if($j == count($teamNamesArray[$i])) {
			$toPrint .= '<br />Error, team # '.$j.' on the schedule doesn\'t match a team name registered in the league';
		}
	}
	$toPrint .= '<br />Team Nums updated<br />';
}

function getTeamIDArray($leagueID) {
	global $teamsTable, $teamNums, $isPractise, $schedVars;
	$schedVars->teamIDArray = array();
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID 
		AND team_num_in_league > 0") or die('error getting data for teams '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$numInLeague = $teamArray['team_num_in_league'];
		$schedVars->teamIDArray[$numInLeague] = $teamArray['team_id'];
		$schedVars->teamNums[$teamArray['team_id']] = $teamArray['team_num_in_league'];
	}
	$schedVars->teamIDArray[98] = 1; //team 1 in the database is practise!
	if($schedVars->isPractise == 1) {
		$schedVars->teamNums[1] = count($schedVars->teamNums) + 1;
	}
}

function createDate($sportID, $leagueDayOfWeek, $nodeString, $weekNum, $dayOfYear, $seasonID) {
	global $datesTable, $toPrint;
	$dateQuery = mysql_query("SELECT * FROM $datesTable WHERE date_sport_id = $sportID AND date_day_number = $leagueDayOfWeek 
		AND date_description = '$nodeString' AND date_week_number = $weekNum AND date_day_of_year_num = $dayOfYear AND 
		date_season_id = $seasonID") or die('error checking dates '.mysql_error());
	if (mysql_num_rows($dateQuery) > 0) {
		$toPrint .= 'DATE '.$nodeString.' already in database <br />';
		$dateArray = mysql_fetch_array($dateQuery);
		$curDateID = $dateArray['date_id'];	
	} else {
		$dateQuery = mysql_query("SELECT MAX(date_id) as maxDate FROM $datesTable") 
			or die ('error getting max date '.mysql_error());
		$dateArray = mysql_fetch_array($dateQuery);
		$curDateID = $dateArray['maxDate'] + 1;
		$dateInsertString = "INSERT INTO $datesTable (date_id, date_sport_id, date_day_number, date_description,
		date_week_number, date_day_of_year_num, date_season_id) VALUES ($curDateID, $sportID, $leagueDayOfWeek,
		'$nodeString', $weekNum, $dayOfYear, $seasonID)";
		print $dateInsertString.'<br />';
		$dateInsert = mysql_query($dateInsertString) or die('error inserting new date '.mysql_error());
	}
	return $curDateID;
}

function createMatch($leagueID, $teamToBeStored, $curVenueID, $matchTime, $curDateID, $rowNum) {
	global $scheduledMatchesTable, $toPrint;
	$matchString = "INSERT INTO $scheduledMatchesTable (scheduled_match_league_id, scheduled_match_team_id_1,
		scheduled_match_team_id_2, scheduled_match_field_id, scheduled_match_time, scheduled_match_date_id, 
		scheduled_match_venue_num_in_week) VALUES ($leagueID, $teamToBeStored[0], $teamToBeStored[1], $curVenueID, $matchTime,
		$curDateID, $rowNum)";
	$toPrint .= $matchString.'<br />';
	mysql_query($matchString);
}


function createPlayoffMatch($leagueID, $teamToBeStored, $curVenueID, $matchTime, $curDateID, $rowNum) {
	global $scheduledMatchesTable, $toPrint;
	$teamOne = mysql_escape_string($teamToBeStored[0]);
	$teamTwo = mysql_escape_string($teamToBeStored[1]);
	if($teamOne == '') {
		$teamOne = 1;
	} else if($teamTwo == '') {
		$teamTwo = 1;
	}
	
	
	$matchString = "INSERT INTO $scheduledMatchesTable (scheduled_match_league_id, scheduled_match_team_id_1,
		scheduled_match_team_id_2, scheduled_match_field_id, scheduled_match_time, scheduled_match_date_id,
		scheduled_match_playoff_team_1, scheduled_match_playoff_team_2, scheduled_match_venue_num_in_week) 
		VALUES ($leagueID, 1, 1, $curVenueID, $matchTime, $curDateID, '$teamOne', '$teamTwo', $rowNum)";
	$toPrint .= $matchString.'<br />';
	mysql_query($matchString);
}

function setScheduleVariables($leagueID, $numColumns, $inlineNotes, $noteTop, $noteBottom, $venueStars) {
	global $scheduleVariablesTable;
	
	$noteTop = mysql_escape_string($noteTop);
	$noteBottom = mysql_escape_string($noteBottom);
	
	$starsString = '';
	if(count($venueStars) > 0) {
		foreach($venueStars as $weekStars) {
			if(strlen($weekStars) > 0) {
				$starsString .= $weekStars.'%';
			}
		}
	}
	$inlineNoteString = '';
	if(count($inlineNotes) > 0) {
		foreach($inlineNotes as $weekNote) {
			$inlineNoteString .= $weekNote.'%';
		}
	}
	
	mysql_query("DELETE FROM $scheduleVariablesTable WHERE schedule_variables_league_id = $leagueID") 
		or die('ERROR deleting old records - '.mysql_error());
	mysql_query("INSERT INTO $scheduleVariablesTable (schedule_variables_league_id, schedule_variables_num_columns, 
		schedule_variables_inline_notes, schedule_variables_top_note, schedule_variables_bottom_note, 
		schedule_variables_playoff_venue_stars) VALUES ($leagueID, $numColumns, '$inlineNoteString', '$noteTop', '$noteBottom', 
		'$starsString')") or die('ERROR adding variables - '.mysql_error());
}