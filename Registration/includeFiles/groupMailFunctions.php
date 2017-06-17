<?php
//Function to create the body of the email for both captain and player

//How it works:
//- htmlentities function ensures that variables are displayed properly (without slashes) in an email format
//- uses for loops to do the same for players
//- $body is the variable for the body of the message
//- continue concatenating it with html code to create the email
//- return ($body) to the email function so that it can be emailed

function body($playerArray, $groupComments){
	global $firstPlayerComments; //This is from the insertInfo function, it formats the preferred leagues so it can be displayed
	$groupComments=stripslashes($firstPlayerComments);
	$date=date('r');

	for($b=0; $b<=count($playerArray); $b++){
		$playerFirst[$b]=stripslashes($playerArray[$b]->playerFirstName);
		$playerLast[$b]=stripslashes($playerArray[$b]->playerLastName);
		$playerEmail[$b]=stripslashes($playerArray[$b]->playerEmail);
		$playerGender[$b]=stripslashes($playerArray[$b]->playerGender);
		$playerPhone[$b] = formatPhoneNumber(preg_replace("/\D/",'',$playerArray[$b]->playerPhone));
		$playerArray[$b]->playerSkill == 0?$playerSkill[$b] = 'N/A':$playerSkill[$b]= $playerArray[$b]->playerSkill;
	}
	$body='';

	$body.='<TR><TD align=center>-------------------Players Information---------------------</td></tr>';
	$body.='<TR><TD align=center><table align=center width="100%" cellpadding=2><tr><td>#</td><th>First</th><th>Last</th><th>G</th>';
	$body.='<th>Email</th><th>Phone</th><th>Skill</th></tr>';

	for($c=0, $d=1; $c<count($playerArray); $c++, $d++){
		if(strlen($playerFirst[$c]) > 2){
			if(strlen($playerPhone[$c]) < 4) {
				$playerPhoneNum = '';
			} else {
				$playerPhoneNum = $playerPhone[$c];
			}
			$body.='<tr><td>'.$d.'</td><td>'.$playerFirst[$c].'</td><td>'.$playerLast[$c].'</td><td>'.$playerGender[$c].'</td>';
			$body.='<td>'.$playerEmail[$c].'</td><td>'.$playerPhoneNum.'</td><td>'.$playerSkill[$c].'</td></tr>';
		}
	}
	
	$body.='</table></td></tr>';
	
	$body.='<tr><td align="center">-----------------------------------------</td>';
	$body.='<tr><th>Comments:</th></tr>';
	$body.='<tr><td>'.$groupComments.'</td></tr>';
	$body.='<tr><td><BR>Received: '.$date.'</TD></TR></table>';

	return $body;
}//end function

//How it works:
//- Creates a separate message header and title for the email sent to the captain and the email sent to the convenor
//- The body of the email is the same
//- The captain and convenor recieve a notification message if a team is not registered (saved) but they add a comment
function mailForm($playerArray, $groupComments, $sportName, $leagueID, $leagueNames, $payMethod){
	
	global $seasonsTable;
	
	$areComments = $groupComments != '' ? 1 : 0;
	$payMethodString[1] = 'Send an email money transfer to dave@perpetualmotion.org';
	$payMethodString[2] = 'Mail cheque to Perpetual Motion\'s home office';
	$payMethodString[3] = 'Bring cash/cheque to registration night';
	$payMethodString[4] = 'Bring cash/cheque to Perpetual Motion\'s home office';

	$seasonArray = query("SELECT * FROM $seasonsTable WHERE season_available_registration = 1");
	$seas_name=$seasonArray['season_name'];

	$title = '<font face=\'verdana\' size=3><TABLE align=center cellspacing=2 cellpadding=2 style="width:500px;">';
	$title .= '<tr><td align=center><B>Perpetual Motion\'s Online Registration System</b></td></tr><tr><td align=center>-- Registration Confirmation --<br /><br /></td></tr>';

	$secondary = '<tr><td align=center>New Player(s) Registered for <B>'.$sportName.' - '.stripslashes($leagueNames[0]).' - '.stripslashes($seas_name).'.</B></td></tr>';
	$secondary.= '<tr><td align=center><b>Payment Method:</b> '.$payMethodString[$payMethod].'<br></td></tr>';
	$body = body($playerArray, $groupComments);

	$message = $title.$secondary.$body;
	$altMessage = $title.$secondary.$body;
	if(count($playerArray) == 1) {
		$subject = 'Registration Confirmation - Individual'.' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		$altSubject = 'Reg - Individual - '.stripslashes(html_entity_decode($leagueNames[0], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		if($areComments == 1){
			$altSubject = 'Reg - Com - Individual - '.stripslashes(html_entity_decode($leagueNames[0], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		}
	} else {
		$subject = 'Registration Confirmation - Small Group'.' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		$altSubject = 'Reg - Small Group - '.stripslashes(html_entity_decode($leagueNames[0], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		if($areComments == 1){
			$altSubject = 'Reg - Com - Small Group - '.stripslashes(html_entity_decode($leagueNames[0], ENT_QUOTES)).' - '.
		stripslashes(html_entity_decode($seas_name, ENT_QUOTES));
		}
	}

	sendEmailsBcc(array($playerArray[0]->playerEmail,),'info@perpetualmotion.org', $subject,  $message);
	sendAdminEmails(groupReg(), 'info@perpetualmotion.org', $altSubject, $altMessage);	

}//end function

function mailWaivers($playerArray, $sportName, $leagueNames, $sportID) {
	$toSend = array();
	$title='<font face=verdana size=3><TABLE align=center cellspacing=2 cellpadding=2>';
	$title.='<tr><th align=center>Perpetual Motion\'s Online Waivers<BR></th></tr>';
	$body='<tr><td><BR>You have been registered for <B>'.stripslashes($sportName).' - '.stripslashes($leagueNames[0]).'.</B></td></tr>';
   	$body.='<tr><td>Please go to our <a href=http://data.perpetualmotion.org/waiver.php?sportID='.$sportID.'>Online Waiver</a> page to sign your waiver.';
    $body.='</td></tr>';
	$body.='<tr><td>Please note you only need to do this once per year</td></tr>';
	$body.='<tr><td>Thank you for signing your waiver online, have a great season!</td></tr></TABLE>';

	$message=$title.$body;

	$subject='Online Waiver - Free Agency';
	
	for($i=0;$i<$people;$i++) {
		if (filter_var($playerArray[$i]->playerEmail, FILTER_VALIDATE_EMAIL)) {
			if(!in_array($playerArray[$i]->playerEmail, $toSend)) {
				array_push($toSend, $playerArray[$i]->playerEmail);
			}
		}
	}
	sendEmailsBcc($toSend,'info@perpetualmotion.org', $subject, $body);
}?>