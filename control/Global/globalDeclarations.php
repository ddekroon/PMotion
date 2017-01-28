<?php 

function dayString($dayNum) {
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

function dayStringShort($dayNum) {
	if($dayNum ==1) {
		return 'Mon';
	} else if($dayNum ==2) {
		return 'Tues';
	} else if($dayNum ==3) {
		return 'Wed';
	} else if($dayNum ==4) {
		return 'Thurs';
	} else if($dayNum ==5) {
		return 'Fri';
	} else if($dayNum ==6) {
		return 'Sat';
	} else if($dayNum ==7) {
		return 'Sun';
	}
}

function getSportDD($sportID) {
	global $sportsTable, $dbConnection, $container;
	$sportsDropDown = '<option value=0>-- Sport --</option>';
	
	$sportsQuery = "SELECT * FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id"; 
	if(!($result = $dbConnection->query($sportsQuery)))
		$container->printError('ERROR getting sports drop down - '.$dbConnection->error);
	
	while($sport = $result->fetch_object()) {
		if($sport->sport_id == $sportID){
			$sportsDropDown.='<option selected value= '.$sport->sport_id.'>'.$sport->sport_name.'</option>';
		} else {
			$sportsDropDown.='<option value= '.$sport->sport_id.'>'.$sport->sport_name.'</option>';
		}
	}
	return $sportsDropDown;
}

function getSeasonDD($givenSeasonID) {
	global $seasonsTable, $seasonID, $dbConnection, $container;
	$curYear = date('Y');
	$seasonsDropDown = '<option value=0>-- Season --</option>';
		
	$seasonsQuery = "SELECT * FROM $seasonsTable WHERE (season_year = $curYear OR season_year = $curYear + 1 OR season_available_score_reporter = 1)
		AND season_id > 0 ORDER BY season_id ASC";
	if(!($result = $dbConnection->query($seasonsQuery)))
		$container->printError('ERROR getting seasons drop down - '.$dbConnection->error);
		
	while($season = $result->fetch_object()) {
		if($season->season_id == $givenSeasonID || ($givenSeasonID==0 && $season->season_available_score_reporter == 1)) {
			$seasonsDropDown.='<option selected value='.$season->season_id.'>'.$season->season_name.' - '.$season->season_year.'</option>';
		} else {
			$seasonsDropDown.='<option value='.$season->season_id.'>'.$season->season_name.' - '.$season->season_year.'</option>';
		}
	}
	return $seasonsDropDown;
}

function getLeaguesDD($sportID, $seasonID, $leagueID) {
	global $leaguesTable, $seasonsTable, $dbConnection, $container;
	
	$pastSeasonID = 0;
	$leaguesDropDown = '<option value=0>-- League Name --</option>';
	
	if($seasonID == -1) {
		$leaguesQuery = "SELECT * FROM $leaguesTable 
			INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
			WHERE ($seasonsTable.season_available_score_reporter = 1 OR $seasonsTable.season_available_registration = 1) 
			AND league_sport_id = $sportID ORDER BY season_id DESC, league_day_number ASC, league_name ASC";
	}else if($seasonID == 0) {
		$leaguesQuery = "SELECT * FROM $leaguesTable 
			INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
			WHERE $seasonsTable.season_available_score_reporter = 1 AND league_sport_id = $sportID 
			ORDER BY league_day_number ASC, league_name ASC";
	} else {
		$leaguesQuery = "SELECT * FROM $leaguesTable WHERE league_sport_id = $sportID AND league_season_id = $seasonID 
			AND league_sport_id > 0 AND league_season_id > 0 ORDER BY league_day_number ASC, league_name ASC";
	}
	
	if(!($result = $dbConnection->query($leaguesQuery))) $container->printError('ERROR getting leagues DD '.$dbConnection->error);
	while($league = $result->fetch_object()) {
		$leagueDay = dayString($league->league_day_number);
		if($league->season_id != $pastSeasonID) {
			$leaguesDropDown.='<option value=0>-- '.$league->season_name.' --</option>';
		}
		$pastSeasonID = $league->season_id;
		
		if($league->league_id == $leagueID) {
			$leaguesDropDown.='<option selected value='.$league->league_id.'>'.$league->league_name.' - '.$leagueDay.'</option>';
		} else {
			$leaguesDropDown.='<option value='.$league->league_id.'>'.$league->league_name.' - '.$leagueDay.'</option>';
		}
	}
	return $leaguesDropDown;
}

function getDayNumDD($dayNumber) {
	$dayDropDown = '<option value=0>-- Day Of Week --</option>';
	
	for($i=1;$i<8;$i++) {
		$leagueDay = dayString($i);
		if($i == $dayNumber){
			$dayDropDown.="<option selected value= $i>".dayString($i).'</option>';
		}else{
			$dayDropDown.="<option value= $i>".dayString($i).'</option>';
		}
	}
	return $dayDropDown;
}

function getTourneysDD($tourneyID) {
	global $tournamentsTable, $dbConnection;
	$tourneysDropDown = '';

	$tourneysQuery = "SELECT * FROM $tournamentsTable ORDER BY tournament_id ASC";
	if(!($result = $dbConnection->query($tourneysQuery)))
		$container->printError('ERROR getting tourneys drop down '.$dbConnection->error);

	while($tourney = $result->fetch_object()) {
		if($tourney->tournament_id == $tourneyID){
			$tourneysDropDown.='<option selected value='.$tourney->tournament_id.'>'.$tourney->tournament_name.'</option>';
		}else{
			$tourneysDropDown.='<option value='.$tourney->tournament_id.'>'.$tourney->tournament_name.'</option>';
		}
	}
	return $tourneysDropDown;
}?>