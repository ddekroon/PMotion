<?php
function registerTeam($playerObj) {
	global $seasonID, $sportID, $leagueID, $teamName, $numPeople, $teamComments;
	global $teamsTable, $captainsTable, $playersTable, $registrationCommentsTable, $userHistoryTable;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$teamName=addslashes(html_entity_decode($teamName, ENT_QUOTES));
	$capFirst = addslashes(html_entity_decode($playerObj[0]->playerFirstName, ENT_QUOTES));
	$capLast = addslashes(html_entity_decode($playerObj[0]->playerLastName, ENT_QUOTES));
	$capEmail = addslashes(html_entity_decode($playerObj[0]->playerEmail, ENT_QUOTES));
	$capSex = addslashes(html_entity_decode($playerObj[0]->playerGender, ENT_QUOTES));
	
	$teamComments=addslashes(html_entity_decode($teamComments, ENT_QUOTES));

	//gets new team id number
	$maxTeamIDArray = mysql_query("SELECT MAX(team_id) as maxNum FROM $teamsTable");
	$maxNumArray = mysql_fetch_array($maxTeamIDArray);
	$teamID = $maxNumArray['maxNum'] +1;
	//gets new team number in league
	$maxTeamNumArray = mysql_query("SELECT MAX(team_num_in_league) as highestTeamNum FROM $teamsTable WHERE team_league_id = $leagueID");
	$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
	$teamNum = $maxTeamNum['highestTeamNum'] +1;
	
	if($teamNum < 10) {
		$picName = $leagueID.'-0'.$teamNum;
	} else {
		$picName = $leagueID.'-'.$teamNum;
	}
	
	mysql_query("INSERT INTO $teamsTable (team_id, team_league_id, team_name, team_num_in_league, team_managed_by_user_id, 
		team_created, team_finalized, team_payment_method, team_pic_name) VALUES ($teamID, $leagueID, '$teamName', $teamNum, 0, NOW(), 1, 5, '$picName')")
		or die("The 'insert into $teamsTable' query threw an error: ".mysql_error());

	for ($i=0; $i<$numPeople; $i++){
		$playerFirst = addslashes(html_entity_decode($playerObj[$i]->playerFirstName, ENT_QUOTES));
		$playerLast = addslashes(html_entity_decode($playerObj[$i]->playerLastName, ENT_QUOTES));
		$playerEmail = addslashes(html_entity_decode($playerObj[$i]->playerEmail, ENT_QUOTES));
		$playerSex = addslashes(html_entity_decode($playerObj[$i]->playerGender, ENT_QUOTES));
		if(($playerPhone = preg_replace("/\D/",'',$playerObj[$i]->playerPhone)) == '') { //removes all non-digits (dashes, dots, brackets, etc) from the phone number
			$playerPhone = 0;
		}
		if($i == 0) {
			$playerIsCaptain = 1;
		} else {
			$playerIsCaptain = 0;
		}
		$addPlayer=mysql_query("INSERT INTO $playersTable (player_team_id, player_firstname, player_lastname, player_email, player_sex, player_phone, 
			player_is_individual, player_is_captain) VALUES ($teamID, '$playerFirst', '$playerLast', '$playerEmail', '$playerSex', '$playerPhone', 0, $playerIsCaptain)") 
			or die("The 'insert into players' query threw an error: ".mysql_error());
	}//end inserting players
	
	$historyType = 'Registered team in control panel';
	if(($username = $_SESSION['username']) == '') {
		$username = 'unknown';
	}
	if(($userID = $_SESSION['userID']) == '') {
		$userID = 0;
	}
	$insertHistory = "INSERT INTO $userHistoryTable (user_history_user_id, user_history_username, user_history_type, user_history_description,
		user_history_timestamp) VALUES ($userID, '$username', '$historyType', '$teamID', NOW())";
	mysql_query($insertHistory) or die(mysql_error());
	
	if ($teamComments != '') {
		mysql_query("INSERT INTO $registrationCommentsTable (registration_comment_user_id, registration_comment_is_team, 
			registration_comment_team_id, registration_comment_is_individual, registration_comment_individual_id, registration_comment_value) 
			VALUES ($userID, 1, $teamID, 0, 0, '$comments')") or die('ERROR inserting comments - '.mysql_error());
	}
}

//This function expedites the process of querying a database
function query($query_string){
        $quer_line=mysql_query($query_string);
        $array_line=mysql_fetch_array($quer_line);
        return ($array_line);
}
?>