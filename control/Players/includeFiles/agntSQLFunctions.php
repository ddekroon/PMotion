<?php


function createAgent($teamID, $leagueID) {
	global $playersTable, $individualsTable, $container;
	
	$error = '';
	if (($agentFirstName = $_POST['firstName'][0]) == '') {
		$error.='No First Name Specified<br />';
	} else {
		$agentFirstName = mysql_escape_string(stripslashes($agentFirstName));
	}
	if (($agentLastName = $_POST['lastName'][0]) == '') {
		$error.='No Last Name Specified<br />';
	} else {
		$agentLastName = mysql_escape_string(stripslashes($agentLastName));
	}
	$agentGender = $_POST['gender'][0];
	if(($agentEmail = $_POST['email'][0]) != '') {
		if (filter_var( $agentEmail, FILTER_VALIDATE_EMAIL ) == true) {
			$agentEmail = mysql_escape_string(stripslashes($agentEmail));
		} else {
			$error.='Email not valid<br />';
		}
	}
	if(strlen($error) < 2) {
		$playerNumQuery = mysql_query("SELECT MAX(player_id) as maxNum FROM $playersTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$playerID = $playerArray['maxNum'] +1;
		
		mysql_query("INSERT INTO $playersTable (player_id, player_team_id, player_firstname, player_lastname, player_email, player_sex, player_is_individual)
			VALUES ($playerID, 0, '$agentFirstName', '$agentLastName', '$agentEmail', '$agentGender', 1)") or die('ERROR adding new player '.mysql_error());
		mysql_query("INSERT INTO $individualsTable (individual_player_id, individual_preferred_league_id, individual_created, individual_finalized, individual_small_group_id)
			VALUES ($playerID, $leagueID, NOW(), 1, 0)") or die('ERROR adding new individual '.mysql_error());
		
		### Clear Fields ###
		
		$_POST['firstName'][0] = '';
		$_POST['lastName'][0] = '';
		$_POST['email'][0] = '';
		$_POST['phoneNum'][0] = '';
		
		$container->printSuccess('Player Added Successfully');
	} else {
		print $error;
	}
}

function createAgentGroup($teamID, $leagueID) {
	global $playersTable, $individualsTable;
	$error = '';
	for($i=0, $j=1; $i< count($_POST['firstName']); $i++, $j++) {
		if (($agentFirstName[$i] = $_POST['firstName'][$i]) == '') {
			$error.='No First Name Specified for player '.$j.'<br />';
		} else {
			$agentFirstName[$i] = mysql_escape_string(stripslashes($agentFirstName[$i]));
		}
		if (($agentLastName[$i] = $_POST['lastName'][$i]) == '') {
			$error.='No Last Name Specified for player '.$j.'<br />';
		} else {
			$agentLastName[$i] = mysql_escape_string(stripslashes($agentLastName[$i]));
		}
		$agentGender[$i] = $_POST['gender'][$i];
		if(($agentEmail[$i] = $_POST['email'][$i]) != '') {
			if (filter_var( $agentEmail[$i], FILTER_VALIDATE_EMAIL ) == true) {
				$agentEmail[$i] = mysql_escape_string(stripslashes($agentEmail[$i]));
			} else {
				$error.='Email not valid for player '.$j.'<br />';
			}
		}
		$phoneNum[$i] = preg_replace('/[^0-9]/s', '',$_POST['phoneNum'][$i]);
	}
	if(strlen($error) < 2) {
		$playerNumQuery = mysql_query("SELECT MAX(individual_small_group_id) as maxNum FROM $individualsTable") or die('ERROR getting max player # '.mysql_error());
		$playerArray = mysql_fetch_array($playerNumQuery);
		$agentGroupID = $playerArray['maxNum'] +1;
		
		
		for($i=0;$i<count($agentFirstName); $i++) {
			$playerNumQuery = mysql_query("SELECT MAX(player_id) as maxNum FROM $playersTable") or die('ERROR getting max player # '.mysql_error());
			$playerArray = mysql_fetch_array($playerNumQuery);
			$playerID = $playerArray['maxNum'] +1;
			mysql_query("INSERT INTO $playersTable (player_id, player_team_id, player_firstname, player_lastname, player_email, player_sex, player_is_individual, player_phone)
				VALUES ($playerID, 0, '$agentFirstName[$i]', '$agentLastName[$i]', '$agentEmail[$i]', '$agentGender[$i]', 1, '$phoneNum[$i]')") 
				or die('ERROR adding new player '.mysql_error());
			mysql_query("INSERT INTO $individualsTable (individual_player_id, individual_preferred_league_id, individual_created, individual_finalized, individual_small_group_id)
				VALUES ($playerID, $leagueID, NOW(), 1, $agentGroupID)") or die('ERROR adding new individual '.mysql_error());
		}
		print 'Players added successfully<br />';
	} else {
		print $error;
	}	
}