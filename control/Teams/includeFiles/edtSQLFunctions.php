<?php

function changeDBTeamData($teamID, $newTeamName, $leagueID, $newLeagueID, $teamWeek, $teamDropped) {
	global $teamsTable, $container;
	$newTeamName = mysql_escape_string($newTeamName);
	if($leagueID != $newLeagueID) {
		$maxQuery = mysql_query("SELECT MAX(team_num_in_league) AS maxNum FROM $teamsTable WHERE 
			team_league_id = $newLeagueID") or die('ERROR getting new team num in league '.mysql_error());
		$maxArray = mysql_fetch_array($maxQuery);
		$newNum = $maxArray['maxNum'] + 1;
		$numInLeagueFilter = ", team_num_in_league = $newNum";
	} else {
		$numInLeagueFilter = '';
	}
	
	$updateString = "UPDATE $teamsTable SET team_name = '$newTeamName', team_league_id = $newLeagueID,
		team_most_recent_week_submitted = $teamWeek, team_dropped_out = $teamDropped $numInLeagueFilter
		WHERE team_id = $teamID";
	//print $updateString.'<br />';
	
	mysql_query($updateString) or die('ERROR changing name '.mysql_error());
	$container->printSuccess('Team data updated successfully');
}

function fixTeamNumbers($leagueID, $teamID) {
	global $teamsTable, $container;
	$teamNum = 0;
	$deleteNum = 0;
	$teamsQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league")
		or die('ERROR getting teams to change #s'.mysql_error());
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$teamIDArray[$teamNum] = $teamArray['team_id'];
		$teamNumInLeague[$teamNum] = $teamArray['team_num_in_league'];
		if($teamIDArray[$teamNum] == $teamID) {
			$deleteNum = $teamNumInLeague[$teamNum];
		}
		$teamNum++;
	}
	
	if($deleteNum != 0) {
		for($i=0; $i< $teamNum; $i++) {
			if($teamNumInLeague[$i]> $deleteNum) {
				mysql_query("UPDATE $teamsTable SET team_num_in_league = team_num_in_league-1 WHERE team_id = $teamIDArray[$i]") 
					or die($container->printError('ERROR updating team numbers '.mysql_error()));
			}
		}
	} else {
		$container->printError('Error changing team nums in league, current team to deleteID not in teams database');
	}
}

function createAgent($teamID, $leagueID) {
	global $playersTable, $individualsTable;
	$error = '';
	$agentFirstName = mysql_escape_string($_POST['agentFirstName']);
	$agentLastName = mysql_escape_string($_POST['agentLastName']);
	$agentGender = $_POST['agentGender'];
	$agentEmail = mysql_escape_string($_POST['agentEmail']);
	/*if (($agentFirstName = $_POST['agentFirstName']) == '') {
		$error.='No First Name Specified<br />';
	} else {
		$agentFirstName = mysql_escape_string($agentFirstName);
	}
	if (($agentLastName = $_POST['agentLastName']) == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$agentLastName = mysql_escape_string($agentLastName);
	}
	$agentGender = $_POST['agentGender'];
	if(($agentEmail = $_POST['agentEmail']) != '') {
		if (filter_var( $agentEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$agentEmail = mysql_escape_string($agentEmail);
		} else {
			$error.='Email not valid<br />';
		}
	}*/
	if(($agentGroupID = $_POST['groupNumber']) == '') {
		$agentGroupID = 0;
	}
	$agentNote = mysql_escape_string($_POST['agentNote']);
	
	$playerNumQuery = mysql_query("SELECT MAX(player_id) as maxNum FROM $playersTable") or die('ERROR getting max player # '.mysql_error());
	$playerArray = mysql_fetch_array($playerNumQuery);
	$playerID = $playerArray['maxNum'] +1;
	
	mysql_query("INSERT INTO $playersTable (player_id, player_team_id, player_firstname, player_lastname, player_email, player_sex, player_is_individual, player_note, player_is_captain)
		VALUES ($playerID, NULL, '$agentFirstName', '$agentLastName', '$agentEmail', '$agentGender', 1, '$agentNote', 0)") or die('ERROR adding new player '.mysql_error());
	mysql_query("INSERT INTO $individualsTable (individual_player_id, individual_preferred_league_id, individual_created, individual_finalized, individual_small_group_id)
		VALUES ($playerID, $leagueID, NOW(), 1, $agentGroupID)") or die('ERROR adding new individual '.mysql_error());
	insertPlayerAddressDB($agentFirstName, $agentLastName, $agentEmail);
}
function insertPlayerAddressDB($playerFirst, $playerLast, $playerEmail) {
	global $container;
	if(filter_var($playerEmail, FILTER_VALIDATE_EMAIL)) {
		$numPeople = mysql_num_rows(mysql_query("SELECT * FROM addressdatabase WHERE EmailAddress = '$playerEmail'"));
		if($numPeople == 0) {
			mysql_query("INSERT INTO addressdatabase (FirstName, LastName, EmailAddress) VALUES ('$playerFirst', '$playerLast', '$playerEmail')") 
				or die($container->printError('ERROR putting email in - '.mysql_error()));
		}
	}
}

function createPlayer($teamID, $leagueID) {
	global $playersTable, $individualsTable, $container;
	$error = '';
	if (($playerFirstName = $_POST['playerFirstName']) == '') {
		$error.='No First Name Specified<br />';
	} else {
		$playerFirstName = mysql_escape_string($playerFirstName);
	}
	if (($playerLastName = $_POST['playerLastName']) == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$playerLastName = mysql_escape_string($playerLastName);
	}
	$playerGender = $_POST['playerGender'];
	if(($playerEmail = $_POST['playerEmail']) != '') {
		if (filter_var( $playerEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$playerEmail = mysql_escape_string($playerEmail);	
		} else {
			$error.='Email not valid<br />';
		}
	}
	$playerNote = mysql_escape_string($_POST['playerNote']);

	if(strlen($error) < 2) {
		$playerNumQuery = mysql_query("SELECT MAX(player_id) as maxNum FROM $playersTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$playerID = $playerArray['maxNum'] +1;
		
		mysql_query("INSERT INTO $playersTable (player_id, player_team_id, player_firstname, player_lastname, player_email, player_sex, player_is_individual, 
			player_note, player_is_captain) VALUES ($playerID, $teamID, '$playerFirstName', '$playerLastName', '$playerEmail', '$playerGender', 0, 
			'$playerNote', 0)") or die('ERROR adding new player '.mysql_error());
			mysql_query("INSERT INTO $individualsTable (individual_player_id, individual_preferred_league_id, individual_created, individual_finalized, individual_small_group_id)
		VALUES ($playerID, $leagueID, NOW(), 1, 0)") or die('ERROR adding new individual '.mysql_error());
		insertPlayerAddressDB($playerFirstName, $playerLastName, $playerEmail);
	} else {
		$container->printError($error);
	}
		
}

function addAgents($numAgents, $teamID) {
	global $playersTable, $container;
	
	if($teamID == 0) {
		print 'ERROR - no team specified';
		return;
	}
	if (isset($_POST['agent'])) {
		$effectedCount = 0;
		foreach($_POST['agent'] as $agent) {
			if (($playerID = $_POST['agentID'][$agent]) != '') {
				mysql_query("UPDATE $playersTable SET player_team_id = $teamID WHERE player_id = $playerID") 
					or die('ERROR adding player '.mysql_error());
				$effectedCount++;
			}
		}
		$container->printSuccess('Additions complete, '.$effectedCount.' players effected.');
	} else {
		$container->printError('No players selected to add');
	}	
}

function removePlayers($numPlayers, $teamID) {
	global $playersTable, $container;
	
	if($teamID == 0) {
		print 'ERROR - no team specified';
		return;
	}
	if (isset($_POST['player'])) {
		$effectedCount = 0;
		foreach($_POST['player'] as $player) {
			if(($playerID = $_POST['playerID'][$player]) != '') {
				mysql_query("UPDATE $playersTable SET player_team_id = NULL WHERE player_id = $playerID") 
					or die('ERROR adding player '.mysql_error());
				$effectedCount++;
			}
		}
		$container->printSuccess('Removes complete, '.$effectedCount.' players effected.');
	} else {
		$container->printError('No players selected to add');
	}	
	
}

function deleteAgents($numAgents, $teamID) {
	global $playersTable, $individualsTable, $container;
	
	if (isset($_POST['agent'])) {
		$effectedCount = 0;
		foreach($_POST['agent'] as $agent) {
			if (($playerID = $_POST['agentID'][$agent]) != '') {
				mysql_query("DELETE FROM $playersTable WHERE player_id = $playerID") or die('ERROR deleting agents '.mysql_error());
				mysql_query("DELETE FROM $individualsTable WHERE individual_player_id = $playerID") or die('ERROR deleting agents '.mysql_error());
				$effectedCount++;
			}
		}
		$container->printSuccess('Deletions complete, '.$effectedCount.' players effected.');
	} else {
		$container->printError('No players selected to delete');
	}
}

function deletePlayers($numPlayers, $teamID) {
	global $playersTable, $individualsTable, $container;
	
	if($teamID == 0) {
		$container->printError('Error, no team specified');
		return;
	}
	
	if (isset($_POST['player'])) {
		$effectedCount = 0;
		foreach($_POST['player'] as $player) {
			if(($playerID = $_POST['playerID'][$player]) != '') {
				mysql_query("DELETE FROM $playersTable WHERE player_id = $playerID") or die('ERROR deleting players '.mysql_error());
				mysql_query("DELETE FROM $individualsTable WHERE individual_player_id = $playerID") or die('ERROR deleting agents '.mysql_error());
				$effectedCount++;
			}
		}
		$container->printSuccess('Deletions complete, '.$effectedCount.' players effected.');
	} else {
		$container->printError('No players selected to delete');
	}
	
} ?>