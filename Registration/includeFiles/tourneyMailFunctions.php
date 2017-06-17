<?php
//Function to create the body of the email for both captain and player

//How it works:
//- htmlentities function ensures that variables are displayed properly (without slashes) in an email format
//- uses for loops to do the same for players
//- $body is the variable for the body of the message
//- continue concatenating it with html code to create the email
//- return ($body) to the email function so that it can be emailed

function getPlayersSection($teamObj, $player, $tourneyObj, $extraPlayersObj){

	$capFirst=stripslashes($player->playerFirstName);
	$capLast=stripslashes($player->playerLastName);
	$phoneNumber=formatPhoneNumber($player->playerPhone);
	$capEmail=stripslashes($player->playerEmail);
	$capSex=stripslashes($player->playerGender);
	$capAddress=stripslashes($player->playerAddress);
	$capCity=stripslashes($player->playerCity);
	$capProvince=stripslashes($player->playerProvince);
	$capPostalCode=stripslashes($player->playerPostalCode);
	$comments=stripslashes($teamObj->teamComments);
	$date=date('r');

	for($b=1, $a = 0; $b <= count($extraPlayersObj); $b++, $a++){
		$playerFirst[$b]=stripslashes($extraPlayersObj[$a]->playerFirstName);
		$playerLast[$b]=stripslashes($extraPlayersObj[$a]->playerLastName);
		$playerRating[$b]=stripslashes($extraPlayersObj[$a]->playerRating);
	}
	$body='';

	$body.='<TR><TD align=center><table class="master" cellpadding=2 cellspacing="2"><tr><td colspan=2>--------------------Captain\'s Information----------------------</td></tr>';
	$body.='<TR><TD align=left><B>Name:</B></td><TD align=left>'.$capFirst.' '.$capLast.'</td></tr>';
	$body.='<tr><td align=left><b>Gender:</b></td><td align=left>'.$capSex.'</td></tr>';
	$body.='<tr><TD align=left><B>Email:</B></td><TD align=left>'.$capEmail.'</td></tr>';
	$body.='<tr><td align=left><b>Phone Number:</b></td><td align=left>'.$phoneNumber.'</td></tr>';
	if($tourneyObj->tourneyIsCards == 1) {
		$body.='<tr><td>Card:</td><td>'.getCardString($player->playerCard).'</td></tr>';
	}
	$body.='<tr><td align=left style="vertical-align:top"><b>Mailing Address:</b></td>';
	
	//Checking the player added a mailing address
	if($capCity != NULL){
		$body.='<td align=left><br/>'.$capAddress.'<br/>'.$capCity.', '.$capProvince.'<br/>'.$capPostalCode.'</td></tr>';
		$body.='<tr><td><br/></td></tr>';
	}else if($capCity == NULL){
		$body.='<td align=left><br/>'."";	
	}
	
	if(count($extraPlayersObj) > 0) {
		$body.='<tr><td colspan=2>-----------Addititional Player(s) Information--------------</td></tr>';
		$body.='<tr><td><b>Name</b></td><td><b>Rating:</b></td>';
	
		for($i=1; $i <= count($extraPlayersObj); $i++){
			$body.='<tr><td>'.$i.'. '.$playerFirst[$i].' '.$playerLast[$i].'</td><td>'.$playerRating[$i].'</td></tr>';
		}
		$body.='<tr><td colspan=2 align=center>---------------------------------------------------------------------<td>';
	}
	$body.='<tr><td><B>Comments:</B></td></tr>';
	$body.='<tr><td colspan=2>'.$comments.'</td></tr>';
	$body.='<tr><td colspan=2><BR></td></tr>';
	$body.='<tr><td colspan=2>Received:'.$date.'</td></tr>';

	return $body;
}//end function

//Function to actually send the email confirming registration

//How it works:
//- Creates a separate message header and title for the email sent to the captain and the email sent to the convenor
//- The body of the email is the same
//- The captain and convenor recieve a notification message if a team is not registered (saved) but they add a comment

function mailRegTeam($teamID, $teamObj, $player, $tourneyObj, $extraPlayersObj){

	$title='<font face=\'verdana\' size=3><TABLE class="master" cellspacing=2 cellpadding=2 style="width:"500px">';
	$title.='<tr><th align=center>Perpetual Motion\'s Online Registration System<BR>-- Registration Confirmation --</th></tr>';
	if($tourneyObj->tourneyIsLeagues == 1) {
		$leagueName = ' - '.stripslashes($tourneyObj->tourneyLeagueNames[$teamObj->teamLeagueID])
			.' ($'.$tourneyObj->tourneyRegistrationFee[$teamObj->teamLeagueID].')';
	} else {
		$leagueName = ' - ($'.$tourneyObj->tourneyRegistrationFee[$teamObj->teamLeagueID].')';
	}

	$secondary='<tr><th>New Team Registered for <br>'.stripslashes($tourneyObj->tourneyName).$leagueName.'</th></tr>';
	$body=getPlayersSection($teamObj, $player, $tourneyObj, $extraPlayersObj);
	$teamLine='<TR><td align=center><b>Team Name:</b> '.stripslashes($teamObj->teamName).'</td></tr>';
	$regIDLine='<TR><td align=center><b>Team Name:</b> '.stripslashes($teamObj->teamName).'</td></tr>';
	$regIDLine.='<TR><td align=center><b>Rating:</b> '.$teamObj->teamRating.'</td></tr>';
	$regIDLine.='<tr><td align=center>Tournament team ID Number: '.$teamID.'</td></tr>';

	$message=$title.$secondary.$teamLine.$body;
	$altMessage=$title.$secondary.$regIDLine.$body;
	$subject='Registration Confirmation - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.stripslashes($tourneyObj->tourneyName).' Tournament ($'.$tourneyObj->tourneyRegistrationFee[$teamObj->teamLeagueID].')';
	$altSubject='TournamentReg - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.stripslashes($tourneyObj->tourneyName).' Tournament ($'.$tourneyObj->tourneyRegistrationFee[$teamObj->teamLeagueID].')';
	
	if(strlen($teamObj->teamComments) > 2){
		$altSubject='TournamentReg - Com - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.stripslashes($tourneyObj->tourneyName).' Tournament';
	}
	$message.='</TABLE>';
	$altMessage.='</TABLE>';

	sendEmailsBcc(array($player->playerEmail,),'info@perpetualmotion.org', $subject,  $message);
	sendAdminEmails(tourneyReg(), 'info@perpetualmotion.org', $altSubject, $altMessage);	

}//end function

function mailRegPlayer($playerID, $teamObj, $player, $tourneyObj, $extraPlayersObj){

	$title='<font face=\'verdana\' size=3><TABLE class="master" cellspacing=2 cellpadding=2 style="width:"500px">';
	$title.='<tr><td align=center><B>Perpetual Motion\'s Online Registration System<BR>-- Registration Confirmation --</B>';
	if($tourneyObj->tourneyIsLeagues == 1) {
		$leagueName = ' - '.stripslashes($tourneyObj->tourneyLeagueNames[$teamObj->teamLeagueID]);
	} else {
		$leagueName = '';
	}

	$secondary='<BR>New Player Registered for <br><B>'.stripslashes($tourneyObj->tourneyName).$leagueName.'</B></td></tr>';
	$body=getPlayersSection($teamObj, $player, $tourneyObj, $extraPlayersObj);
	$regIDLine.='<tr><td align=center>Tournament player ID Number: '.$playerID.'</td></tr>';

	$message=$title.$secondary.$body;
	$altMessage=$title.$secondary.$regIDLine.$body;
	$subject='Registration Confirmation - '.stripslashes(html_entity_decode($tourneyObj->tourneyLeagueNames[$teamObj->teamLeagueID], ENT_QUOTES));
	$altSubject='TournamentReg - '.stripslashes(html_entity_decode($tourneyObj->tourneyLeagueNames[$teamObj->teamLeagueID], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($player->playerFirstName, ENT_QUOTES)).' '.stripslashes(html_entity_decode($player->playerLastName, ENT_QUOTES));

	if($comSub==1){
		$altSubject='TournamentReg - Com - '.stripslashes(html_entity_decode($teamObj->teamLeagueName[$teamObj->teamLeagueID], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($player->playerFirstName, ENT_QUOTES)).' '.stripslashes(html_entity_decode($player->playerLastName, ENT_QUOTES));
	}
	$message.='</TABLE>';
	$altMessage.='</TABLE>';

	sendEmails(array($player->playerEmail,),'info@perpetualmotion.org', $subject,  $message);
	sendAdminEmails(tourneyReg(), 'info@perpetualmotion.org', $altSubject, $altMessage);	

}//end function


function mailWaivers() {
	global $teamObj, $player, $people, $sportID;
	$toSend = array();
	
	$title='<font face=verdana size=3><TABLE align=center cellspacing=2 cellpadding=2>';
	$title.='<tr><td align=center><B>Perpetual Motion\'s Online Waivers<BR></B></td></tr>';
	$body='<tr><td><BR>You have been registered for <B>'.stripslashes($teamObj->teamSportName).' - '.stripslashes($teamObj->teamLeagueName).'.</B></td></tr>';
   	$body.='<tr><td>Please go to our <a href=http://data.perpetualmotion.org/waiver.php?sportID='.$sportID.'>Online Waiver</a> page to sign your waiver.';
    $body.='</td></tr>';
	$body.='<tr><td>Please note you only need to do this once per year</td></tr>';
	$body.='<tr><td>Thank you for signing your waiver online, have a great season!</td></tr></TABLE>';

	$message=$title.$body;

	$subject='Online Waiver - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	
	for($i=0;$i<$people;$i++) {
		if (filter_var($player[$i]->playerEmail, FILTER_VALIDATE_EMAIL)) {
			if(!in_array($player[$i]->playerEmail, $toSend)) {
				array_push($toSend, $player[$i]->playerEmail);
			}
		}
	}
	sendEmailsBcc($toSend,'info@perpetualmotion.org', $subject, $body);
}?>