<?php
function insertInfo($teamObj, $playerObj, $userID, $isRegistered) {
	global $teamsTable, $captainsTable, $playersTable, $registrationCommentsTable, $userHistoryTable, $teamID;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$teamName=addslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	$capFirst=addslashes(html_entity_decode($playerObj[0]->playerFirstName, ENT_QUOTES));
	$capLast=addslashes(html_entity_decode($playerObj[0]->playerLastName, ENT_QUOTES));
	$capSex = $playerObj[0]->playerGender;
	$capEmail=addslashes(html_entity_decode($playerObj[0]->playerEmail, ENT_QUOTES));
	$capPhone=preg_replace("/\D/",'',$playerObj[0]->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	$comments=addslashes(html_entity_decode($teamObj->teamComments, ENT_QUOTES));
	$teamPayMethod = $teamObj->teamPayMethod;
	$leagueID = $teamObj->teamLeagueID;

	deleteTeam($teamID); //if you're using an old team for the new one this will make it so the old one doesn't show up on the members page

	//gets new team id number
	$maxTeamIDArray = mysql_query("SELECT MAX(team_id) as maxNum FROM $teamsTable");
	$maxNumArray = mysql_fetch_array($maxTeamIDArray);
	$teamID = $maxNumArray['maxNum'] +1;
	//gets new team number in league
	$maxTeamNumArray = mysql_query("SELECT MAX(team_num_in_league) as highestTeamNum FROM $teamsTable WHERE team_league_id = $leagueID");
	$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
	$teamNum = $maxTeamNum['highestTeamNum'] +1;
	//gets new captain id
	$maxCaptainNumArray = mysql_query("SELECT MAX(captain_id) as  maxCaptain FROM $captainsTable");
	$maxCaptainNum = mysql_fetch_array($maxCaptainNumArray);
	$captainID = $maxCaptainNum['maxCaptain'] +1;
	

	$teamInsert="INSERT INTO $teamsTable (team_id, team_league_id, team_name, team_captain_id, team_num_in_league, team_managed_by_user_id, 
		team_created, team_finalized, team_payment_method) VALUES ($teamID, $leagueID, '$teamName', $captainID, $teamNum, $userID, NOW(), $isRegistered, $teamPayMethod)";
	mysql_query($teamInsert)or die("The 'insert into $teamsTable' query threw an error: ".mysql_error());
	$captainInsert="INSERT INTO $captainsTable (captain_id, captain_firstname, captain_lastname, captain_email, captain_phone, captain_sex) 
		VALUES ($captainID, '$capFirst', '$capLast', '$capEmail', $capPhone, '$capSex')";
	mysql_query($captainInsert) or die("The 'insert into $captainsTable' query threw an error: ".mysql_error());

	for($e=0; $e< count($playerObj); $e++){
		$playerFirst = addslashes(html_entity_decode($playerObj[$e]->playerFirstName, ENT_QUOTES));
		$playerLast = addslashes(html_entity_decode($playerObj[$e]->playerLastName, ENT_QUOTES));
		$playerEmail = addslashes(html_entity_decode($playerObj[$e]->playerEmail, ENT_QUOTES));
		$playerSex = addslashes(html_entity_decode($playerObj[$e]->playerGender, ENT_QUOTES));
		if($e==0) {
			$playerAboutUsMethod = $teamObj->aboutUsMethod;
			$playerAboutUsText = $teamObj->aboutUsText;
		} else {
			$playerNote = '';
			$playerAboutUsMethod = 0;
			$playerAboutUsText = '';
		}
		
		if((strlen($playerFirst)>=1)){
			$playerInsert="INSERT INTO $playersTable (player_team_id, player_firstname, player_lastname, player_email, player_sex,
				player_is_individual, player_hear_method, player_hear_other_text) VALUES ($teamID, '$playerFirst', '$playerLast', '$playerEmail', '$playerSex',0,
				$playerAboutUsMethod, '$playerAboutUsText')";
			$addPlayer=mysql_query($playerInsert) or die("The 'insert into players' query threw an error: ".mysql_error());
		}
	}//end inserting players
	
	if($isRegistered ==0) { //save
		$historyType = "Saved team";
	} else {
		$historyType = "Registered team";
	}
	$username = $_SESSION['username'];
	$insertHistory = "INSERT INTO $userHistoryTable (user_history_user_id, user_history_username, user_history_type, user_history_description,
		user_history_timestamp) VALUES ($userID, '$username', '$historyType', '$teamID', NOW())";
	mysql_query($insertHistory) or die(mysql_error());
	
	if ($comments != '') {
		$insertComments = "INSERT INTO $registrationCommentsTable (registration_comment_user_id, registration_comment_is_team, 
			registration_comment_team_id, registration_comment_is_individual, registration_comment_individual_id, registration_comment_value) 
			VALUES ($userID, 1, $teamID, 0, 0, '$comments')";
		mysql_query($insertComments) or die(mysql_error());
	}
}

function updateInfo($teamObj, $playerObj, $userID, $regSwitch) {
	global $teamsTable, $captainsTable, $playersTable, $registrationCommentsTable, $userHistoryTable;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$teamID = $teamObj->teamID;
	$teamName=addslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	$capFirst=addslashes(html_entity_decode($playerObj[0]->playerFirstName, ENT_QUOTES));
	$capLast=addslashes(html_entity_decode($playerObj[0]->playerLastName, ENT_QUOTES));
	$capSex = $playerObj[0]->playerGender;
	$capEmail=addslashes(html_entity_decode($playerObj[0]->playerEmail, ENT_QUOTES));
	$capPhone=preg_replace("/\D/",'',$playerObj[0]->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	$comments=addslashes(html_entity_decode($teamObj->teamComments, ENT_QUOTES));
	$teamPayMethod = $teamObj->teamPayMethod;
	$leagueID = $teamObj->teamLeagueID;
	$isRegistered = $teamObj->teamIsRegistered;
	
	if ($regSwitch == 1) {
		//gets new team number in league
		$maxTeamNumArray = mysql_query("SELECT MAX(team_num_in_league) as highestTeamNum FROM $teamsTable WHERE team_league_id = $leagueID");
		$maxTeamNum = mysql_fetch_array($maxTeamNumArray);
		$teamNumInLeague = $maxTeamNum['highestTeamNum'] +1;
	} else if ($regSwitch == 2) {
		$teamNumInLeague = 0;
	} else {
		$teamNumArray = mysql_query("SELECT * FROM $teamsTable WHERE team_id = $teamID");
		$teamNum = mysql_fetch_array($teamNumArray);
		$teamNumInLeague = $teamNum['team_num_in_league'];
	}
	
	$teamUpdate="UPDATE $teamsTable SET team_league_id = $leagueID, team_name = '$teamName', team_finalized = $isRegistered, team_payment_method = $teamPayMethod,
		team_num_in_league = $teamNumInLeague WHERE team_id = $teamID";
	mysql_query($teamUpdate) or die("The 'update $teamsTable' query threw an error: ".mysql_error());
	$captainUpdate="UPDATE $captainsTable INNER JOIN $teamsTable ON $teamsTable.team_captain_id = $captainsTable.captain_id SET captain_firstname = '$capFirst', 
		captain_lastname = '$capLast', captain_email = '$capEmail', captain_phone = $capPhone, captain_sex = '$capSex' WHERE $teamsTable.team_id = $teamID"; 
	mysql_query($captainUpdate) or die("The 'update $captainsTable' query threw an error: ".mysql_error());

	mysql_query("DELETE FROM $playersTable WHERE player_team_id = $teamID");
	for ($e=0; $e< count($playerObj); $e++){
		$playerFirst = addslashes(html_entity_decode($playerObj[$e]->playerFirstName, ENT_QUOTES));
		$playerLast = addslashes(html_entity_decode($playerObj[$e]->playerLastName, ENT_QUOTES));
		$playerEmail = addslashes(html_entity_decode($playerObj[$e]->playerEmail, ENT_QUOTES));
		$playerSex = addslashes(html_entity_decode($playerObj[$e]->playerGender, ENT_QUOTES));
		
		if((strlen($playerFirst)>=1)){
			$playerInsert="INSERT INTO $playersTable (player_team_id, player_firstname, player_lastname, player_email, player_sex,
				player_is_individual) VALUES ($teamID, '$playerFirst', '$playerLast', '$playerEmail', '$playerSex',0)";
			$addPlayer=mysql_query($playerInsert) or die("The 'insert into players' query threw an error: ".mysql_error());
		}
	}//end inserting players
	
	$historyType = 'Updated team';
	$username = $_SESSION['username'];
	$insertHistory = "INSERT INTO $userHistoryTable (user_history_user_id, user_history_username, user_history_type, user_history_description,
		user_history_timestamp) VALUES ($userID, '$username', '$historyType', '$teamID', NOW())";
	mysql_query($insertHistory) or die(mysql_error());
	
	if ($comments != '') {
		$insertComments = "INSERT INTO $registrationCommentsTable (registration_comment_user_id, registration_comment_is_team, 
			registration_comment_team_id, registration_comment_is_individual, registration_comment_individual_id, registration_comment_value) 
			VALUES ($userID, 1, $teamID, 0, 0, '$comments')";
		mysql_query($insertComments) or die(mysql_error());
	}
}

//returns 2 if the league is full, 1 if the user will be put on waiting, 0 if they're fine to register
function checkLeaguesFull($leagueID) {
	global $leaguesTable, $teamsTable;
	
	//gets new team number in league
	$leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID");
	$leagueArray = mysql_fetch_array($leagueQuery);
	$numUntilWaiting = $leagueArray['league_num_teams_before_waiting'];
	$leagueFull = $leagueArray['league_maximum_teams'];

	//gets new team number in league
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_finalized = 1");
	$numTeamsRegistered = mysql_num_rows($teamsQuery);

	if ($numTeamsRegistered >= $leagueFull && $leagueFull != 0) {
		return 2;
	} else if ($numTeamsRegistered >= $numUntilWaiting && $leagueFull != 0) {
		return 1;
	} else {
		return 0;
	}
}

//Checks if a team exists in a certain league with a certain teamname
function checkTeamExists($leagueID, $teamName) {
	global $teamsTable;
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_name = '$teamName'");
	$numTeams = mysql_num_rows($teamsQuery);
	if ($numTeams > 0) 
		return true;
	else
		return false;	
}

//Checks if a team with 
function deleteTeam($teamID) {
	global $teamsTable, $seasonsTable, $leaguesTable;
	mysql_query("UPDATE $teamsTable 
		Inner Join $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		Inner Join $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		SET team_deleted = 1 WHERE team_id = $teamID AND (season_year != YEAR(NOW()) OR team_finalized = 0)") 
		or die ('error updating old team '.mysql_error());
}

//This function expedites the process of querying a database
function query($query_string){
        $quer_line=mysql_query($query_string) or die('ERROR - '.mysql_error());
        $array_line=mysql_fetch_array($quer_line);
        return ($array_line);
}

//This function formats a phone number into the proper display. Makes "5198234502" or anything similar appear as "(519)-823-4502"
//How it works:
//- Chops phone number input into 3 sections: area code, prefix (first 3 digits), and number (last 4 digits)
function formatPhoneNumber($strPhone){
        $strPhone = ereg_replace("[^0-9]",'', $strPhone);
        if (strlen($strPhone)!= 10){
                return $strPhone;
        }
        $strArea = substr($strPhone, 0, 3);
        $strPrefix = substr($strPhone, 3, 3);
        $strNumber = substr($strPhone, 6, 4);
        $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;
        return ($strPhone);
}

/* THE FOLLOWING IS LEGACY DATA, USED IN THE OLD SYSTEM< NOT ANYMORE */

//This function updates the registration database with "payment method" for a team after they confirm the registration
function prepConfirm($regID){
	global $method, $regID;

	$insert="UPDATE registration SET method='".$method."' WHERE registrationID=".$regID;
	$addRegistration=mysql_query($insert);
}

function prepare($registeredBool){
	global $league, $teamName, $capFirst, $capLast, $capSex, $capEmail, $capPhone, $semiIdentifier, $regID, $sport, $user;
	global $playerFirst, $playerLast, $playerEmail, $playerSex, $people, $comments;
	global $captainsTable, $teamsTable, $playersTable, $leaguesTable, $seasonsTable;

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	$league=addslashes(html_entity_decode($league, ENT_QUOTES));
	$teamName=addslashes(html_entity_decode($teamName, ENT_QUOTES));
	$capFirst=addslashes(html_entity_decode($capFirst, ENT_QUOTES));
	$capLast=addslashes(html_entity_decode($capLast, ENT_QUOTES));
	$capSex=addslashes(html_entity_decode($capSex, ENT_QUOTES));
	$capEmail=addslashes(html_entity_decode($capEmail, ENT_QUOTES));
	$comments=addslashes(html_entity_decode($comments, ENT_QUOTES));
	$capPhone=preg_replace("/\D/",'',$capPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
	$semiID=$semiIdentifier;

	$array = mysql_query("SELECT MAX(registrationID) as maxNum FROM registration");
	
	$maxNumArray = mysql_fetch_array($array);
	$regID = $maxNumArray['maxNum'] +1;

	print "<INPUT TYPE='hidden' NAME='reg' VALUE=$regID>"; //sets regID in a hidden variable

	$regInsert="INSERT INTO registration (registrationID, semi_identifier, teamName, sport, league, userName, cFirst, cLast, comments, 
		method, registered) VALUES ($regID, '$semiID', '$teamName', '$sport', '$league', '$user', '$capFirst', '$capLast', 
		'$comments', '' ,$registeredBool)";
	$addRegistration=mysql_query($regInsert)or die("The 'insert into registration' query threw an error: ".mysql_error());

	$captainInsert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES ($regID, '$teamName', 
		'$capFirst', '$capLast', '$capEmail', $capPhone, '$capSex')";

	$addCaptain=mysql_query($captainInsert) or die("The 'insert into captains' query threw an error: ".mysql_error());

	for ($e=1; $e<=$people; $e++){
		$playerFirst[$e]=addslashes(html_entity_decode($playerFirst[$e], ENT_QUOTES));
		$playerLast[$e]=addslashes(html_entity_decode($playerLast[$e], ENT_QUOTES));
		$playerEmail[$e]=addslashes(html_entity_decode($playerEmail[$e], ENT_QUOTES));
		$playerSex[$e]=addslashes(html_entity_decode($playerSex[$e], ENT_QUOTES));
		if((strlen($playerFirst[$e])>=1)){
			$playerInsert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES 
				($regID, '$teamName', '$playerFirst[$e]', '$playerLast[$e]', '$playerEmail[$e]', '$playerSex[$e]')";
			$addPlayer=mysql_query($playerInsert) or die("The 'insert into players' query threw an error: ".mysql_error());
			$addEmail=mysql_query("INSERT IGNORE INTO addressDatabase (FirstName, LastName, EmailAddress) VALUES 
				('$playerFirst[$e]', '$playerLast[$e]', '$playerEmail[$e]')");
		}
	}//end inserting players
	return $regID;
}