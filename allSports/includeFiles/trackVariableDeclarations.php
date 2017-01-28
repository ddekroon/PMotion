<?php
function stripslashes_deep($value)
{
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
}

$_POST = stripslashes_deep($_POST);
	    
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}
	
$formTitle = '';
$games=0;
$showCancelDefaultOption=0;
$matches=0;
$oppTeamID = array();
$actualWeekDate='';
$dayOfYear = 0;
$dateID = 0;
$dayNumber = 0;
$scoresAvailable=1; //why not?
$teamDropDown = ''; //variable holding the dropdown data for the submitting team
$oppDropDown = array(); //variable holding the dropdown data for the opponent dropdown(s)
$leagueDropdown='';
$maxPoints = 10;
$hasTies = 1; //the only league without is volleyball... for now
$logo = '';
$scoreUs = array();
$scoreThem = array();
$leagueName = '';
$teamName = '';
$usScore = 0;
$themScore = 0;
		
function loadDefaults($sportID) {
	global $formTitle, $matches, $games, $maxPoints, $sportsTable, $scoresAvailable, $hasTies, $logo;
	
	if($sportID == 1) 
	{
		$formTitle='';
		$logo='/Logos/ultimate_0.png';
	} 
	elseif($sportID == 2) 
	{
		$formTitle='';
		$logo='/Logos/volleyball_0.png';
	} 
	elseif($sportID == 3) 
	{
		$formTitle='';
		$logo='/Logos/football_0.png';
	} 
	elseif($sportID == 4) 
	{
		$formTitle='';
		$logo= '/Logos/soccer_0.png';
	} 
	else  
	{
		$formTitle='';
		$logo = '/Logos/Perpetualmotionlogo.jpg';
	}
	
	$sportArray = query("SELECT * FROM $sportsTable WHERE sport_id = $sportID");
	
	$matches = $sportArray['sport_default_num_of_matches'];
	$games = $sportArray['sport_default_num_games_per_match'];
	$maxPoints = $sportArray['sport_default_max_points_per_game'];
	$scoresAvailable = $sportArray['sport_default_ask_for_scores'];
	$hasTies = $sportArray['sport_default_has_ties'];
}

function loadVariables($sportID, $leagueID, $teamID) {

	global $dayNumber, $showCancelOption, $scoresAvailable, $matches, $games, $maxPoints, $hasTies, $hasPractice;
	global $oppTeamID, $dayOfYear, $actualWeekDate, $dateID, $teamName, $leagueName, $dayNumber, $isPlayoffs, $usScore, $themScore;
	global $datesTable, $leaguesTable, $teamsTable, $seasonsTable, $sportsTable, $scheduledMatchesTable;
	global $usScore, $themScore;
	
	
	$usScore = 0;
	$themScore = 0;
	//League based variable
	if($leagueID != 0){
		$leagueArray = query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
	
		$leagueName = $leagueArray['league_name'];
		$dayNumber = $leagueArray['league_day_number'];
		$showCancelOption = $leagueArray['league_show_cancel_default_option'];
		$scoresAvailable = $leagueArray['league_ask_for_scores'];
		$matches = $leagueArray['league_num_of_matches'];
		$games = $leagueArray['league_num_of_games_per_match'];
		$maxPoints = $leagueArray['league_max_points_per_game'];
		$hasTies = $leagueArray['league_has_ties'];
		$hasPractice = $leagueArray['league_has_practice_games'];
		$playoffWeek = $leagueArray['league_playoff_week'];
		$leagueWeek = $leagueArray['league_week_in_score_reporter'];
		if($leagueWeek >= $playoffWeek) 
		{
			$isPlayoffs = 1;
		} else {
			$isPlayoffs = 0;
		}
	}
	
	//which teams the current teamID played on week from the leagueTable
	if($teamID != 0){
		$teamArray = query("SELECT * FROM $teamsTable WHERE team_id = $teamID");
		$teamName = $teamArray['team_name'];
		
		$matchesArray = mysql_query("SELECT * FROM $scheduledMatchesTable 
						Inner Join $datesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id
						Inner Join $leaguesTable ON $scheduledMatchesTable.scheduled_match_league_id = $leaguesTable.league_id 
						WHERE ($scheduledMatchesTable.scheduled_match_team_id_2 = $teamID OR $scheduledMatchesTable.scheduled_match_team_id_1 = $teamID)
						AND $datesTable.date_week_number = $leaguesTable.league_week_in_score_reporter
						AND $datesTable.date_season_id = $leaguesTable.league_season_id AND $leaguesTable.league_id = $leagueID ORDER BY scheduled_match_time") 
						or die ("Error: ".mysql_error());
		//goes through the results and figures out which opponents team teamID had
		for($i=0;$i<$matches;$i++) {
			$matchNode=mysql_fetch_array($matchesArray);
			//This if statements checks if both scheduled matches are against the same team, this can happen if you double submit a leagues matches for a week.
			if($i != 0) {
				if(($matchNode['scheduled_match_team_id_1'] == $oppTeamID[$i-1] || $matchNode['scheduled_match_team_id_2'] == $oppTeamID[$i-1]) 
					&& $matchNode['scheduled_match_team_id_1'] != '') {
					$i--;
				}
			}
			if ($matchNode['scheduled_match_team_id_1'] == $teamID) {
				$oppTeamID[$i] = $matchNode['scheduled_match_team_id_2'];
			} else {
				$oppTeamID[$i] = $matchNode['scheduled_match_team_id_1'];
			}
		}
	}           
	
	//Which date the game was played on
	$curDateArray = query("SELECT * FROM $datesTable Inner Join $leaguesTable ON $datesTable.date_day_number = $leaguesTable.league_day_number
						WHERE $leaguesTable.league_id = $leagueID AND $datesTable.date_week_number = $leaguesTable.league_week_in_score_reporter
						AND date_sport_id = $sportID AND date_season_id = league_season_id");
	if (($dayOfYear = $curDateArray['date_day_of_year_num']) == '') {
		$dayOfYear = 0;
	} else {
		$actualWeekDate = $curDateArray['date_description'];
		$dateID = $curDateArray['date_id'];
	}
	
	
	
}

function getSubmittedData() {
	global $oppTeamID, $scoreUs, $scoreThem, $gameResults, $spiritScores, $matchComments, $submitName, $submitEmail, $matches, $games;
	global $dayOfYear, $actualWeekDate, $dateID, $teamName, $leagueName, $dayNumber, $hasPractice, $hasTies, $isPlayoffs;
	global $usScore, $themScore;
	
	$gameNum = 0;
	
	$matches = $_POST['matches'];
	$games = $_POST['games'];
	$dayOfYear = $_POST['dayOfYear'];
	$actualWeekDate = $_POST['actualWeekDate'];
	$dateID = $_POST['dateID'];
	$teamName = $_POST['teamName'];
	$leagueName = $_POST['leagueName'];
	$dayNumber = $_POST['dayNumber'];
	$hasTies = $_POST['hasTies'];
	$hasPractice = $_POST['hasPractice'];
	$isPlayoffs = $_POST['isPlayoffs'];
	$usScore = $_POST['usScore'];
	$themScore = $_POST['themScore'];
	
	for ($i=0;$i<$matches;$i++) {
		$oppTeamID[$i] = $_POST['oppID'][$i];
		$matchComments[$i] = $_POST['matchComments'][$i];
		if(($spiritScores[$i] = $_POST['spiritScore'][$i]) == '') {
			$spiritScores[$i] = 0;
		}
		for ($j=0;$j<$games;$j++) {
			if(($scoreUs[$gameNum] = $_POST['scoreus'][$gameNum]) == '') {
				$scoreUs[$gameNum] = 0;
			}
			if(($scoreThem[$gameNum] = $_POST['scorethem'][$gameNum]) == '') {
				$scoreThem[$gameNum] = 0;
			}
			if(($gameResults[$gameNum] = $_POST['results'][$gameNum]) == '') {
				$gameResults[$gameNum] = 0;
			}
			$gameNum++;
		}
	}
	$submitName = $_POST['submitterName'];
	$submitEmail = $_POST['submitemail'];
}

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

function getLeagueDD($leagueID, $sportID) 
{
	global $leagueDropDown, $leaguesTable, $seasonsTable;
	
	$lastSeasonID = 0;
	//leagues in dropdown
	$leaguesQuery=mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE season_available_score_reporter = 1 AND league_sport_id = $sportID ORDER BY league_season_id ASC, league_day_number ASC, league_name ASC")
			or die("TEST ".mysql_error());
			
	while($league = mysql_fetch_array($leaguesQuery)) 
	{
		$leagueDay = dayString($league['league_day_number']);
		if($league['season_id'] != $lastSeasonID && $lastSeasonID != 0) 
		{
			$leagueDropDown.='<option value=0>--------'.$league['season_name'].'------</option>';
		}
		if($league['league_id']==$leagueID){
			$leagueDropDown.="<option selected value= $league[league_id]>$league[league_name] - $leagueDay</option><BR>";
		}
		else
		{
			$leagueDropDown.="<option value= $league[league_id]>$league[league_name] - $leagueDay</option>";
		}
		$lastSeasonID = $league['season_id'];
	}
}

function getTeamDD($leagueID, $teamID, $oppTeamID) 
{
	global $oppDropDown, $matches, $teamsTable, $teamDropDown, $teamName, $hasPractice;
	//print $hasPractice;
	if($leagueID > 0) 
	{
		if($hasPractice == 1) 
		{ //league has practice, include the practice team
			$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE ((team_league_id = $leagueID 
				AND team_num_in_league > 0) OR team_id = 1) AND team_dropped_out = 0 ORDER BY team_num_in_league")
				or die('ERROR getting teams for dropdown - '.mysql_error());
		} 
		else 
		{
			$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID 
				AND team_num_in_league > 0 AND team_dropped_out = 0 ORDER BY team_num_in_league")
				or die('ERROR getting teams for dropdown - '.mysql_error());
		}
		while($team = mysql_fetch_array($teamsQuery)) 
		{
			if ($team['team_id'] != 1) 
			{
				if($team['team_id'] == $teamID)
				{
					$teamDropDown.= "<option selected value=$team[team_id]>$team[team_num_in_league] - $team[team_name]</option>";
					$teamName = $team['team_name'];
				} 
				else 
				{
					$teamDropDown.= "<option value=$team[team_id]>$team[team_num_in_league] - $team[team_name]</option>";
				}
			}
			for($i=0;$i<$matches;$i++) 
			{
				if($team['team_id']==$oppTeamID[$i]) 
				{
					$oppDropDown[$i].=  "<option selected value=$team[team_id]>$team[team_num_in_league] - $team[team_name]</option>";
				} 
				else 
				{
					$oppDropDown[$i].=  "<option value=$team[team_id]>$team[team_num_in_league] - $team[team_name]</option>";
				}
			}
		}
	}
}
?>
