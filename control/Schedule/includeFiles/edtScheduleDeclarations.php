<?php

function getLeagueWeekData($leagueID) { 
	global $scheduledMatchesTable, $datesTable, $seasonsTable, $leaguesTable;
	
	//This gets all dates for a league including playoffs
	$datesQuery = mysql_query("SELECT * FROM $datesTable INNER JOIN $leaguesTable ON $leaguesTable.league_day_number = $datesTable.date_day_number
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id WHERE league_sport_id = date_sport_id AND league_season_id = date_season_id 
		AND league_id = $leagueID ORDER BY season_year ASC, date_day_of_year_num ASC") or die('ERROR getting dates - '.mysql_error());
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
	global $venuesTable, $venueID, $venueName, $venueShortName;
	
	$venuesQuery = mysql_query("SELECT * FROM $venuesTable WHERE venue_sport_id = $sportID") 
		or die ('problem getting venues '.mysql_error());
	$numVenues = mysql_num_rows($venuesQuery);
	$i =0;
	while($venueArray = mysql_fetch_array($venuesQuery)) {
		$venueID[$i] = $venueArray['venue_id'];
		$venueName[$i] = addslashes(ereg_replace("[^A-Za-z0-9]", "", $venueArray['venue_name']));
		$venueShortName[$i] = addslashes(ereg_replace("[^A-Za-z0-9]", "", $venueArray['venue_short_match_name'])); //slashes are for regex
		$i++;
	}
	return $numVenues;
}

function formattedTime($time) {
	if(strlen($time == 3)){
		$hr= substr($time,0,1);
		$mn= substr($time,1);
	}else{
		$hr= substr($time,0,2);
		$mn= substr($time,2);
	}
	if ($hr>=13){
		$hr = $hr-12;
		$meridiem = "pm";
	} else {
		$meridiem = "am";
	}
	return "$hr:$mn $meridiem";
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

function getTimesDD($sportID) {
	$timesDropDown = '';
	$timesArray = array(1800, 1815, 1830, 1845, 1900, 1910, 1915, 1930, 1945, 1950, 2000, 2010, );
	for($i = 0; $i < count($timesArray); $i++) {
		$timesDropDown .= "<option value='$timesArray[$i]'>".formattedTime($timesArray[$i]).'</option>';
	}
	return $timesDropDown;
}

function getVenuesDD($sportID) {
	global $venuesTable;
	$venuesDropDown = '';
	$lastVenueName = '';
	
	$venuesQuery = mysql_query("SELECT * FROM $venuesTable WHERE venue_sport_id = $sportID ORDER BY venue_short_show_name")
		or die('ERROR getting venues DD - '.mysql_error());
	while($venueArray = mysql_fetch_array($venuesQuery)) {
		if($venueArray['venue_short_show_name'] != $lastVenueName) {
			$venuesDropDown.="<option value= $venueArray[venue_id]>$venueArray[venue_short_show_name]</option>";
			$lastVenueName = $venueArray['venue_short_show_name'];
		}
	}
	return $venuesDropDown;
}