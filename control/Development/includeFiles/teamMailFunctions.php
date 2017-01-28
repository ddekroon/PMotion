<?php
//Function to create the body of the email for both captain and player

//How it works:
//- htmlentities function ensures that variables are displayed properly (without slashes) in an email format
//- uses for loops to do the same for players
//- $body is the variable for the body of the message
//- continue concatenating it with html code to create the email
//- return ($body) to the email function so that it can be emailed

function getAboutUsString($aboutUsMethod) {
	$aboutUsMethodString[1] = 'Google/Internet Search';
	$aboutUsMethodString[2] = 'Facebook Page';
	$aboutUsMethodString[3] = 'Kijiji Ad';
	$aboutUsMethodString[4] = 'Returning Player';
	$aboutUsMethodString[5] = 'From A Friend';
	$aboutUsMethodString[6] = 'Restaurant Advertisement';
	$aboutUsMethodString[7] = 'The Guelph Community Guide';
	$aboutUsMethodString[8] = 'Other';
	return $aboutUsMethodString[$aboutUsMethod];
}

function body($userID){
	global $teamObj, $player, $people, $topNotice, $playerHear;

	$capFirst=stripslashes($player[0]->playerFirstName);
	$capLast=stripslashes($player[0]->playerLastName);
	$phoneNumber=formatPhoneNumber($player[0]->playerPhone);
	$capEmail=stripslashes($player[0]->playerEmail);
	$capSex=stripslashes($player[0]->playerGender);
	$comments=stripslashes($teamObj->teamComments);
	$tmNm=stripslashes($teamObj->teamName);
	$date=date('r');
	$topNotice=stripslashes($topNotice);
	$lastFirstName = '';
	$lastLastName = '';
	$lastEmail = '';
	$curPlayerNum = 1;
	$aboutUsMethod = getAboutUsString($teamObj->aboutUsMethod);
	$aboutUsText = $teamObj->aboutUsText;

	for($b=0; $b<=$people; $b++){
		$playerFirst[$b]=stripslashes($player[$b]->playerFirstName);
		$playerLast[$b]=stripslashes($player[$b]->playerLastName);
		$playerEmail[$b]=stripslashes($player[$b]->playerEmail);
		$playerSex[$b]=stripslashes($player[$b]->playerGender);
	}
	$body='';

	$body.='<tr><td colspan=4 align=center>-----------------Captain\'s Information-------------------</td></tr>';
	$body.='<tr><th>First Name</th><th>Last Name</th><th></th><th>Gender</th></tr>';
	$body.='<tr><td>'.$capFirst.'</td><td>'.$capLast.'</td><td></td><td>'.$capSex.'</td></tr>';
	$body.='<tr><th colspan=2>Email</th><th colspan=2>Phone Number</th></tr>';
	$body.='<tr><td colspan=2>'.$capEmail.'</td><td colspan=2>'.$phoneNumber.'<br /></td></tr>';
	$body.='<tr><td colspan=4 align=center>-------------------Player Information---------------------</td></tr>';
	$body.='<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th></tr>';

	for($c=0; $c<=$people; $c++){
		if(!($playerFirst[$c] == $lastFirstName && $playerLast[$c] == $lastLastName && $playerEmail[$c] == $lastEmail) && !($playerFirst[$c] == '' && $playerLast[$c] == '' && $playerEmail[$c] == '')){
			$body.='<tr><td>'.$curPlayerNum.'. '.$playerFirst[$c].'</td><td>'.$playerLast[$c].'</td>';
			$body.='<td>'.$playerEmail[$c].'</td><td>'.$playerSex[$c].'</td></tr>';
			$lastFirstName = $playerFirst[$c];
			$lastLastName = $playerLast[$c];
			$lastEmail = $playerEmail[$c];
			$curPlayerNum++;
		}
	}
	$body.='<tr><td colspan=4 align=center>---------------------------------------------------------------</td></tr>';
	$body.='<tr><th colspan=4>How the heard about us: '.$aboutUsMethod.' - '.$aboutUsText.'</th></tr>';
	$body.='<tr><td colspan=4 align=center>---------------------------------------------------------------</td></tr>';
	$body.='<tr><th>Comments:</th></tr>';
	$body.='<tr><td colspan=4>'.$comments.'<br /></td></tr>';
	$body.='<tr><td>Received:</td><td>'.$date.'</td></tr>';

	return $body;
}//end function

//Function to actually send the email confirming registration

//How it works:
//- Creates a separate message header and title for the email sent to the captain and the email sent to the convenor
//- The body of the email is the same
//- The captain and convenor recieve a notification message if a team is not registered (saved) but they add a comment

function mailForm($userID, $comSub, $saveFilter = 0){
	global $teamObj, $player, $teamID, $leaguesTable, $seasonsTable, $teamsTable;
	
	$leagueID = $teamObj->teamLeagueID;
	$leagueArray = query("SELECT * FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id WHERE league_id = $leagueID");
	$teamSeasonName = $leagueArray['season_name'];
	$teamSeasonID = $leagueArray['season_id'];
	$teamQuery = mysql_query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = 
		$teamsTable.team_league_id WHERE league_season_id = $teamSeasonID - 1 AND team_managed_by_user_id = $userID AND 
		team_finalized = 1") or die('ERROR getting userID - '.mysql_error());
	if(mysql_num_rows($teamQuery) > 0 && $userID != 0) {
		$returningString = 'Returning ';
	} else {
		$returningString = 'New ';
	}
	$title='<table cellspacing=2 cellpadding=2 style="font:10px">';
	$title.='<tr><th colspan=3 align=center>Perpetual Motion\'s Online Registration System</th></tr>';
	$title.='<tr><th colspan=3 align=center>-- Registration Confirmation --</th></tr>';

	  $secondary='<tr><td colspan=3 align=center>'.$returningString.'Team Registered for <B>'.stripslashes($teamObj->teamSportName).' - '.stripslashes($teamObj->teamLeagueName).'</B></td></tr>';
	$body=body($userID);
	$teamLine='<tr><td align=center colspan=3><b>Team Name:</b> '.stripslashes($teamObj->teamName).'</td></tr>';
	$regIDLine='<tr><td align=center colspan=3><b>Team Name:</b> '.stripslashes($teamObj->teamName).'</td></tr>';
	$regIDLine.='<tr><td colspan=3 align=center>Team ID Number: '.$teamID.'</td></tr>';

	$message=$title.$secondary.$teamLine.$body;
	$altMessage=$title.$secondary.$regIDLine.$body;
	$subject='Registration Confirmation - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($teamSeasonName, ENT_QUOTES));
	$altSubject='Reg - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($teamObj->teamLeagueName, ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($teamSeasonName, ENT_QUOTES));

	if($comSub==1){
		$altSubject='Reg - Com - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES)).' - '.stripslashes(html_entity_decode($teamObj->teamLeagueName, ENT_QUOTES)).
			' - '.dayString($teamObj->teamDayNum).' - '.
		stripslashes(html_entity_decode($teamSeasonName, ENT_QUOTES));
	}
	$message.='</table>';
	$altMessage.='</table>';
	if($saveFilter == 0) {
		sendEmails(array($player[0]->playerEmail,),'info@perpetualmotion.org', $subject,  $message);
	}
	sendAdminEmails(teamReg(), 'info@perpetualmotion.org', $altSubject, $altMessage);	

}//end function

function mailTeamUnregistered($comSub){
	global $teamObj;
	$comment='';

	$title='<table align=center cellspacing=2 cellpadding=2 style="font:10px">';
	$title.='<tr><th colspan=3 align=center>Perpetual Motion\'s Online Registration System<BR>-- Team Unregistered Themselves --</th></tr>';

	$secondary='<tr><td>Team Unregistered for <br><B>'.stripslashes($teamObj->teamSportName).' - '.stripslashes($teamObj->teamLeagueName).'.</B></td></tr>';	
	$regIDLine='<TR><td align=center colspan=3><b>Team Name:</b> '.stripslashes($teamObj->teamName).'</td></tr>';
	$regIDLine.='<tr><td colspan=3 align=center>Team Number: '.$teamObj->teamID.'</td></tr>';	
	$subject='Unreg - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	
	if($comSub==1){
		$subject='Unreg - Com - Team  - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
		$comment='<tr><td colspan=3 align=center>Comment: '.$teamObj->teamComments.'</td></tr>';
	}
	$message=$title.$secondary.$regIDLine.$comment.'</table>';
	
	sendAdminEmails(teamUnreg(), 'info@perpetualmotion.org', $subject, $message);	
}


function mailWaivers() {
	global $teamObj, $player, $people, $sportID;
    $toSend[] = array();
	
	$body='<table align=center cellspacing=2 cellpadding=2 style="font:10px">';
	$body.='<tr><td align=center><B>Perpetual Motion\'s Online Waivers<BR></B></td></tr>';
	$body.='<tr><td><BR>You have been registered for <B>'.stripslashes($teamObj->teamSportName).' - '.stripslashes($teamObj->teamLeagueName).'.</B></td></tr>';
   	$body.='<tr><td>Please go to our <a href=http://data.perpetualmotion.org/waiver.php?sportID='.$sportID.'>Online Waiver</a> page to sign your waiver.';
    $body.='</td></tr>';
	$body.='<tr><td>Please note you only need to do this once per year</td></tr>';
	$body.='<tr><td>Thank you for signing your waiver online, have a great season!</td></tr></table>';


	$subject='Online Waiver - '.stripslashes(html_entity_decode($teamObj->teamName, ENT_QUOTES));
	
	for($i=0;$i<$people;$i++) {
		if (filter_var($player[$i]->playerEmail, FILTER_VALIDATE_EMAIL)) {
			if(!in_array($player[$i]->playerEmail, $toSend)) {
				array_push($toSend, $player[$i]->playerEmail);
			}
		}
	}
	sendEmailsBcc($toSend,'info@perpetualmotion.org', $subject, $body);
}

?>