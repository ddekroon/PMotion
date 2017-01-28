<?php
function updateScheduleData($leagueID) {
	global $scheduledMatchesTable;
	mysql_query("DELETE FROM $scheduledMatchesTable WHERE scheduled_match_league_id = $leagueID") 
		or die('ERROR deleting old values - '.mysql_error());
	printf("Records deleted: %d\n", mysql_affected_rows());
	
	$teamToBeStored = array();
	$venueID = 0;
	$dateID = 0;
	$teamIDArray = getTeamIDArray($leagueID);
	$numColumns = $_POST['numColumns']; //week 1 field 0, all columns will be the same
	$playoffWeek = $_POST['playoffStartWeek'];
	for($weekNum = 1; $weekNum <= $_POST['numWeeks']; $weekNum++) {
		
		if(($timeRows = $_POST['timeRows'][$weekNum - 1]) == '') {
			$timeRows = 1;
		}
		$dateID = $_POST['dateID'][$weekNum];
		
		for($j = 0; $j < $timeRows; $j++) {
			for($field = 0; $field < count($_POST["fieldID"][$weekNum][$j]); $field++) {
				
				$venueID = $_POST["fieldID"][$weekNum][$j][$field];
				
				for($games = $j * $numColumns; $games < ($j+1) *$numColumns; $games++) {
					
					$matchTime = $_POST['time'][$weekNum][$games];
					
					if($weekNum < $playoffWeek) {
						if(($teamToBeStored[0] = $teamIDArray[$_POST["teamOne"][$weekNum][$field][$games]]) == '') {
							$teamToBeStored[0] = 1;
						}
						if(($teamToBeStored[1] = $teamIDArray[$_POST["teamTwo"][$weekNum][$field][$games]]) == '') {
							$teamToBeStored[1] = 1;
						}
						
						if($teamToBeStored[0] != 1 || $teamToBeStored[1] != 1) {
							createMatch($leagueID, $teamToBeStored, $venueID, $matchTime, $dateID, $field + 1);
						}
					} else { //playoff match
						if(($teamToBeStored[0] = $_POST["teamOne"][$weekNum][$field][$games]) == '') {
							$teamToBeStored[0] = '';
						}
						if(($teamToBeStored[1] = $_POST["teamTwo"][$weekNum][$field][$games]) == '') {
							$teamToBeStored[1] = '';
						}
						if($teamToBeStored[0] != '' || $teamToBeStored[1] != '') {
							createPlayoffMatch($leagueID, $teamToBeStored, $venueID, $matchTime, $dateID, $field + 1);
						}
					}
				}
			}
		}
	}
	
	$noteTop = str_replace("\r\n", '%', $_POST['topNote']);
	$noteBottom = str_replace("\r\n", '%', $_POST['bottomNote']);
	
	setScheduleVariables($leagueID, $numColumns, $noteTop, $noteBottom, $_POST['numStars'][$playoffWeek]);
}

function createMatch($leagueID, $teamToBeStored, $curVenueID, $matchTime, $curDateID, $rowNum) {
	global $scheduledMatchesTable;
	
	$matchString = "INSERT INTO $scheduledMatchesTable (scheduled_match_league_id, scheduled_match_team_id_1,
		scheduled_match_team_id_2, scheduled_match_field_id, scheduled_match_time, scheduled_match_date_id, 
		scheduled_match_venue_num_in_week) VALUES ($leagueID, $teamToBeStored[0], $teamToBeStored[1], $curVenueID, $matchTime,
		$curDateID, $rowNum)";
	//print $matchString.'<br />';
	mysql_query($matchString);
}


function createPlayoffMatch($leagueID, $teamToBeStored, $curVenueID, $matchTime, $curDateID, $rowNum) {
	global $scheduledMatchesTable;
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
	//print $matchString.'<br />';
	mysql_query($matchString);
}

function setScheduleVariables($leagueID, $numColumns, $noteTop, $noteBottom, $venueStars) {
	global $scheduleVariablesTable;
	
	$noteTop = mysql_escape_string($noteTop);
	$noteBottom = mysql_escape_string($noteBottom);
	
	$starsString = '';
	foreach($venueStars as $weekStars) {
		if(strlen($weekStars) > 0) {
			$starsString .= $weekStars.'-';
		}
	}
	$starsString .= '%';
	
	mysql_query("DELETE FROM $scheduleVariablesTable WHERE schedule_variables_league_id = $leagueID") 
		or die('ERROR deleting old records - '.mysql_error());
	$insertString = "INSERT INTO $scheduleVariablesTable (schedule_variables_league_id, schedule_variables_num_columns, 
		schedule_variables_top_note, schedule_variables_bottom_note, schedule_variables_playoff_venue_stars) VALUES 
		($leagueID, $numColumns, '$noteTop', '$noteBottom', '$starsString')";
	//print $insertString.'<br />';
	mysql_query($insertString) or die('ERROR adding variables - '.mysql_error());
}



function setScheduleLink($leagueID) {
	global $leaguesTable, $seasonsTable;
	$leagueArray = mysql_fetch_array(mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $leaguesTable.league_season_id = $seasonsTable.season_id WHERE league_id = $leagueID"));
	if($leagueArray['league_sport_id'] == 1) {
		$scheduleLink = 'GuelphUltimate/Schedules/';
		$sportFilter = 'ultimate';
	} else if($leagueArray['league_sport_id'] == 2) {
		$scheduleLink = 'BeachVolleyball/Schedules/';
		$sportFilter = 'volleyball';
	} else if($leagueArray['league_sport_id'] == 3) {
		$scheduleLink = 'FlagFootball/Schedules/';
		$sportFilter = 'football';
	} else if($leagueArray['league_sport_id'] == 4) {
		$scheduleLink = 'Soccer/Schedules/';
		$sportFilter = 'soccer';
	}
	$dayFilter = strtolower(dayString($leagueArray['league_day_number']));
	$leagueFilter = strtolower(str_replace("'", '', "$leagueArray[league_name]"));
	$leagueFilter = str_replace(" ", "-", $leagueFilter);
	$leagueFilter = str_replace("%/%", '-', $leagueFilter);
	$seasonName = strtolower($leagueArray['season_name']);
	$seasonYear = $leagueArray['season_year'];
	$leagueScheduleLink = $scheduleLink.'schedule-'.$dayFilter.'-'.$leagueFilter.'-'.$seasonName.'-'.$seasonYear.'.htm';
	
	$storeLink = mysql_escape_string($leagueScheduleLink);
	mysql_query("UPDATE $leaguesTable SET league_schedule_link = '$storeLink' WHERE league_id = $leagueID") 
		or die('ERROR storing schedule link - '.mysql_error());
	return $leagueScheduleLink;
}

function fixWeekNumbers() {
	global $datesTable;
	$numAffected = 0;
	for($i=0;$i<count($_POST['dateID']); $i++) {
		$dateID = $_POST['dateID'][$i];
		$newWeek = $_POST['dateWeek'][$i];
		mysql_query("UPDATE $datesTable SET date_week_number = $newWeek WHERE date_id = $dateID") or die('ERROR setting new week #s - '.mysql_error());
		$numAffected++;
	}
	print 'Numbers fixed, '.$numAffected.' dates affected';
}

function setTeamNums($leagueID, $teamNamesArray) {
	global $teamsTable;
	
	for($i=1;$i <= count($teamNamesArray); $i++) {
		$teamName = mysql_escape_string($teamNamesArray[$i]);
		mysql_query("UPDATE $teamsTable SET team_num_in_league = $i WHERE STRCMP(team_name, '$teamName') = 0 
			AND team_league_id = $leagueID AND team_num_in_league > 0") or die('ERROR setting team nums - '.mysql_error());
			print '<br />'.$i.' '.$teamName;
	}
	print 'Team Nums updated<br />';
}

function getTeamIDArray($leagueID) {
	global $teamsTable;
	$teamIDArray = array();
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID 
		AND team_num_in_league > 0") or die('error getting data for teams '.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$numInLeague = $teamArray['team_num_in_league'];
		$teamIDArray[$numInLeague] = $teamArray['team_id'];
	}
	$teamIDArray[98] = 1; //team 1 in the database is practise!
	return $teamIDArray;
}