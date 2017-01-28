<?php
function insertInfo($playerObj, $groupComments, $leagueID, $leagueNames, $payMethod, $aboutUsMethod, $aboutUsText) {
	global $playersTable, $individualsTable, $leaguesTable, $registrationCommentsTable, $firstPlayerComments;

	$firstPlayerComments = '';
	for($i=1, $j=2;$i<3;$i++, $j++) {
		if($leagueID[$i] != 0) {	
			$firstPlayerComments .= "PL$j-".$leagueNames[$i].' ';
		}
	}
	
	$firstPlayerComments .= addslashes(html_entity_decode($groupComments, ENT_QUOTES));
	$comments = addslashes(html_entity_decode($groupComments, ENT_QUOTES));
	$aboutUsText = addslashes(html_entity_decode($aboutUsText, ENT_QUOTES));
	$groupPayMethod = $payMethod;
	
	if(count($playerObj) > 1) {
		//gets small group id number
		$maxGroupIDArray = mysql_query("SELECT MAX(individual_small_group_id) as maxNum FROM $individualsTable");
		$maxNumArray = mysql_fetch_array($maxGroupIDArray);
		$groupID = $maxNumArray['maxNum'] +1;
	} else {
		$groupID = 0;
	}

	//The addslashes function helps the database handle bad characters like apostrophes, slashes, and quoation marks
	for($i=0;$i<count($playerObj); $i++) {
		$playerFirst = addslashes(html_entity_decode($playerObj[$i]->playerFirstName, ENT_QUOTES));
		$playerLast = addslashes(html_entity_decode($playerObj[$i]->playerLastName, ENT_QUOTES));
		$playerGender = $playerObj[$i]->playerGender;
		$playerEmail = addslashes(html_entity_decode($playerObj[$i]->playerEmail, ENT_QUOTES));
		$playerPhone = preg_replace("/\D/",'',$playerObj[$i]->playerPhone); //removes all non-digits (dashes, dots, brackets, etc) from the phone number
		$playerSkill = $playerObj[$i]->playerSkill;
		if($i==0) {
			$playerNote = addslashes(html_entity_decode($firstPlayerComments, ENT_QUOTES));
			$playerAboutUsMethod = $aboutUsMethod;
			$playerAboutUsText = addslashes(html_entity_decode($aboutUsText, ENT_QUOTES));
		} else {
			$playerNote = '';
			$playerAboutUsMethod = 0;
			$playerAboutUsText = '';
		}
	
		//gets new player id number
		$maxPlayerIDArray = mysql_query("SELECT MAX(player_id) as maxNum FROM $playersTable") or die('ERROR getting new player id - '.mysql_error());
		$maxNumArray = mysql_fetch_array($maxPlayerIDArray);
		$playerID = $maxNumArray['maxNum'] +1;
	
		$teamInsert="INSERT INTO $playersTable (player_id, player_firstname, player_lastname, player_email, player_sex, player_is_individual, player_phone, player_skill,
			player_note, player_hear_method, player_hear_other_text) VALUES ($playerID, '$playerFirst', '$playerLast', '$playerEmail', '$playerGender', 1, '$playerPhone',
			$playerSkill, '$playerNote', $playerAboutUsMethod, '$playerAboutUsText')";
		mysql_query($teamInsert)or die("The 'insert into $playersTable' query threw an error: ".mysql_error());
		$captainInsert="INSERT INTO $individualsTable (individual_player_id, individual_preferred_league_id, individual_created, individual_finalized, 
			individual_small_group_id, individual_payment_method) 
			VALUES ($playerID, $leagueID[0], NOW(), 1, $groupID, $payMethod)";
		mysql_query($captainInsert) or die("The 'insert into $individualsTable' query threw an error: ".mysql_error());
		insertPlayerAddressDB($playerFirst, $playerLast, $playerEmail);

		if ($comments != '' && $i == 0) {
			$insertComments = "INSERT INTO $registrationCommentsTable (registration_comment_user_id, registration_comment_is_team, 
				registration_comment_team_id, registration_comment_is_individual, registration_comment_individual_id, registration_comment_value) 
				VALUES (0, 0, 0, 1, $playerID, '$comments')";
			mysql_query($insertComments) or die(mysql_error());
		}
	}
}

function insertPlayerAddressDB($playerFirst, $playerLast, $playerEmail) {
	
	if(filter_var($playerEmail, FILTER_VALIDATE_EMAIL)) {
		$numPeople = mysql_num_rows(mysql_query("SELECT * FROM addressdatabase WHERE EmailAddress = '$playerEmail'"));
		if($numPeople == 0) {
			mysql_query("INSERT INTO addressdatabase (FirstName, LastName, EmailAddress) VALUES ('$playerFirst', '$playerLast', '$playerEmail')") 
				or die('ERROR putting email in - '.mysql_error());
		}
	}
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
function formatPhoneNumber($strPhone)
{
        $strPhone = preg_replace("[^0-9]",'', $strPhone); // Removes all non digits characters
		
		// When there is the long disitance number (+1)
		if(strlen($strPhone) == 11) 
		{
			$strPhone = substr($strPhone, 1, 10);
		}
		
		// When there are not enough numbers (10)
        if (strlen($strPhone)!= 10)
		{
        	return ($strPhone);
        }
		
        $strArea = substr($strPhone, 0, 3);
        $strPrefix = substr($strPhone, 3, 3);
        $strNumber = substr($strPhone, 6, 4); 
        $strPhone = '('.$strArea.')'.$strPrefix.'-'.$strNumber;
        
		return ($strPhone);
}