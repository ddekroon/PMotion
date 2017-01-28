<?php

function declareLeagueVariables($leagueID) {
	global $leaguesTable, $teamsTable, $seasonsTable, $schedVars;
	
    $leagueQuery = mysql_query("SELECT * FROM $teamsTable 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE league_id = $leagueID AND team_num_in_league > 0") or die('ERROR getting league data - '.mysql_error());
	$schedVars->numTeamsDB = mysql_num_rows($leagueQuery);
    $leagueArray = mysql_fetch_array($leagueQuery);
    $schedVars->leagueScheduleLink = $leagueArray['league_schedule_link'];
	$schedVars->isPractise = $leagueArray['league_has_practice_games'];
	$schedVars->leagueDayOfWeek = $leagueArray['league_day_number'];
	$schedVars->leagueIsSplit = $leagueArray['league_is_split'];
	if(($schedVars->leaguePlayoffWeek = $_POST['playoffWeek']) == 0) {
		$schedVars->leaguePlayoffWeek = $leagueArray['league_playoff_week'];
	}
	$schedVars->leagueNumWeeks = $leagueArray['season_num_weeks'];
	if($schedVars->leagueIsSplit == 1) {
		if(($schedVars->weekNum = $leagueArray['league_split_week']) < 0) {
			$schedVars->weekNum = 0;
		}
	} else {
		$schedVars->weekNum = 0;
	}
}

function getWeeksDD($leagueID) {
	global $datesTable, $seasonsTable, $leaguesTable;
	$datesDropDown = '<option value=0>-- Weeks --</option>';
	
	$datesQuery=mysql_query("SELECT * FROM $datesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = $seasonsTable.season_id
		WHERE league_id = $leagueID AND date_day_number = league_day_number AND date_sport_id = league_sport_id
		ORDER BY date_week_number ASC") or die('ERROR getting weeks '.mysql_error());
	while($date = mysql_fetch_array($datesQuery)) {
		$date['date_week_number'] == $date['league_playoff_week'] ? $selectedFilter = 'selected' : $selectedFilter = '';
		$datesDropDown.="<option $selectedFilter value=$date[date_week_number]>$date[date_description]</option>";
	}
	return $datesDropDown;
}

function getLeagueWeekData($leagueID) { 
	global $scheduledMatchesTable, $datesTable, $seasonsTable, $leaguesTable;
	
	//This gets all dates for a league including playoffs
	$datesQuery = mysql_query("SELECT * FROM $datesTable INNER JOIN $leaguesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id WHERE league_sport_id = date_sport_id AND league_season_id = date_season_id 
		AND league_id = $leagueID ORDER BY date_week_number ASC") or die('ERROR getting dates - '.mysql_error());
	$numDates = 0;
	$oldDateID = 0;
	while($dateArray = mysql_fetch_array($datesQuery)) {
		if(($dateID = $dateArray['date_id']) != $oldDateID) {
			$date[$numDates] = new Date();
			$date[$numDates]->dateID = $dateID;
			$date[$numDates]->dateWeek = $dateArray['date_week_number'];
			$date[$numDates]->dateActualWeek = $dateArray['date_description'];
			$date[$numDates]->dateYear = $dateArray['season_year'];
			$dayNumMatches = mysql_num_rows(mysql_query("SELECT * FROM $scheduledMatchesTable WHERE scheduled_match_league_id = $leagueID AND scheduled_match_date_id = $dateID"));
			$date[$numDates]->dateNumLeagueMatches = $dayNumMatches;
			$numDates++;
			$oldDateID = $dateID;
		}
	}
	return $date;
}

function getVenues($sportID) {
	global $venuesTable, $schedVars;
	
	$venuesQuery = mysql_query("SELECT * FROM $venuesTable WHERE venue_sport_id = $sportID") 
		or die ('problem getting venues '.mysql_error());
	$schedVars->numVenues = mysql_num_rows($venuesQuery);
	$i =0;
	while($venueArray = mysql_fetch_array($venuesQuery)) {
		$schedVars->venueID[$i] = $venueArray['venue_id'];
		$schedVars->venueName[$i] = preg_replace("/[^A-Za-z0-9]/", "", $venueArray['venue_name']);
		$schedVars->venueShortName[$i] = preg_replace("/[^A-Za-z0-9]/", "", $venueArray['venue_short_match_name']); //slashes are for regex
		$i++;
	}
}

//Figures out the current venue... this is very useless right now but fuck it I'm already in on Darryls idea's so far. 
//EDIT haha me funny, now gets used for team page
//so its pretty important, maybe Darryl isn't a nut case
//... 
function getCurVenueID($nodeString) {
	global $schedVars, $leaguesTable;
	$venueShortName = $schedVars->venueShortName;
	$venueName = $schedVars->venueName;
	
	$schedVars->venueSet = 0; //always gets set to 0, if it's 0 then the venue id will get set on short names which are more likely to find a match
	$schedVars->curVenue = preg_replace("/[^A-Za-z0-9]/", "", $schedVars->nodeString);
	for ($i = 0; $i < $schedVars->numVenues; $i++) {
		if (preg_match("/$venueShortName[$i]/i", $schedVars->curVenue) > 0) {
			if ($schedVars->venueSet == 0) { //defaults the venue to the first one it finds, after this tries to find the specific one, makes it more foolproof
				$schedVars->curVenueID = $schedVars->venueID[$i];
				$schedVars->venueSet = 1;
				$schedVars->venueTeamNumber = 0;
				$schedVars->newGameTime = 0;
				$schedVars->numVenuesByDate++;
				$schedVars->timeSlotNum = 0;
				if(preg_match("/Practi[s|c]e/i", $schedVars->curVenue) > 0) {
					$schedVars->practiseRow = 1;
					if($schedVars->isPrctice == 0) {
						$schedVars->isPractice = 1;
						mysql_query("UPDATE $leaguesTable SET league_has_practice_games = 1 WHERE league_id = 
							".$schedVars->leagueID) or die('ERROR updating is practice - '.mysql_error());
					}
				} else {
					$schedVars->practiseRow = 0;
				}
			}
			if (preg_match("/$venueName[$i]/i", $schedVars->curVenue) > 0 || $schedVars->practiseRow == 1 || ($i == $schedVars->numVenues - 1 && $schedVars->venueSet == 1)) {
				if(preg_match("/$venueName[$i]/i", $schedVars->curVenue) > 0) {
					$schedVars->curVenueID = $schedVars->venueID[$i];
				}
				if(!in_array($schedVars->curVenueID, $schedVars->usedVenueIDs)) {
					$schedVars->venueRow = $schedVars->maxVenueRow + 1;
					$schedVars->usedVenueIDs[$schedVars->venueRow] = $schedVars->curVenueID;
					if($schedVars->venueRow > $schedVars->maxVenueRow) {
						$schedVars->maxVenueRow = $schedVars->venueRow;
					}
				} else {
					for($i= 1; $i <= count($schedVars->usedVenueIDs); $i++) {
						if($schedVars->usedVenueIDs[$i] == $schedVars->curVenueID) {
							$schedVars->venueRow = $i;
							break;
						}
					}
				}
				return 1;
			}
		}
	}
	return 0;
}

//Checks for a plain number in the 'td', if so this is a team and is gonna go into scheduled matches table.
//getGames == 1 means a date was found therefore you won't get <td>'s with just the team # in them, you can imagine how that would muck stuff up.

//Returning 1 means create a match, 2 means a new team was found, and 0 means continue
function checkTeamNum($nodeString, $practiseRow, $getGames, $teamIDArray) {
	global $schedVars;
	$schedVars->teamNumInLeague = preg_replace("[^A-Za-z0-9]", "", $schedVars->nodeString);
	$schedVars->teamNumInLeague = preg_replace('/Team/', '', $schedVars->teamNumInLeague);
	if (is_numeric($schedVars->teamNumInLeague) && $schedVars->getGames == 1) {
		$schedVars->venueTeamNumber++;
		if($schedVars->practiseRow == 1) { //whatever team the program found is practising this week
			$schedVars->teamToBeStored[0] = 1;
			$schedVars->teamInMatchNum = 1;
		}
		$schedVars->teamInMatchNum = $schedVars->teamInMatchNum > 0 ? 0 : 1;
		if ($schedVars->teamInMatchNum == 0) {
			$schedVars->teamToBeStored[1] = $schedVars->teamIDArray[$schedVars->teamNumInLeague];
			return 1;
		} else {
			$schedVars->teamToBeStored[0] = $schedVars->teamIDArray[$schedVars->teamNumInLeague];
			return 0;
		}
	} else if (is_numeric($schedVars->teamNumInLeague) && $schedVars->getGames == 0) { //The next <td> will be a team
		return 2;
	}
	return 0;
}