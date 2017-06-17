<?php
//Function to create the body of the email for both captain and player

//How it works:
//- htmlentities function ensures that variables are displayed properly (without slashes) in an email format
//- uses for loops to do the same for players
//- $body is the variable for the body of the message
//- continue concatenating it with html code to create the email
//- return ($body) to the email function so that it can be emailed

function body($regID){
	global $capFirst, $capLast, $capPhone, $capSex, $capEmail, $comments, $teamName;
	global $playerFirst, $playerLast, $playerEmail, $playerSex, $people, $topNotice;

	$capFirst=stripslashes($capFirst);
	$capLast=stripslashes($capLast);
	$capPhone=stripslashes($capPhone);
	$phoneNumber=formatPhoneNumber($capPhone);
	$capEmail=stripslashes($capEmail);
	$capSex=stripslashes($capSex);
	$comments=stripslashes($comments);
	$tmNm=stripslashes($teamName);
	$date=date('r');
	$topNotice=stripslashes($topNotice);

	for($b=1; $b<=$people; $b++){
		$playerFirst[$b]=stripslashes($playerFirst[$b]);
		$playerLast[$b]=stripslashes($playerLast[$b]);
		$playerEmail[$b]=stripslashes($playerEmail[$b]);
		$playerSex[$b]=stripslashes($playerSex[$b]);
	}
	$body="";

	$body.="<TR><TD colspan=4 align=center><font face='verdana' color=red>".$topNotice."</td></tr>";
	$body.="<TR><TD colspan=4 align=center>-----------------Captain's Information-------------------";
	$body.="<TR><TD><B>First Name:</B><td><b>Last Name:</b><td> <td><b>Sex:</b>";
	$body.="<tr><TD>".$capFirst."<td>".$capLast."<td> <td>".$capSex;
	$body.="<tr><TD colspan=2><B>Email:</B><td colspan=2><b>Phone Number:</b>";
	$body.="<tr><TD colspan=2>".$capEmail."<td colspan=2>".$phoneNumber;
	$body.="<tr><td><BR>";
	$body.="<tr><td colspan=4 align=center>-----------------Player Information-------------------<td>";
	$body.="<tr><td><b>First Name:</b><td><b>Last Name:</b><td><b>Email:</b><td><b>Sex:</b>";

	for($c=1; $c<=$people; $c++){
		if(strlen($playerFirst[$c])>=1){
			$body.="<tr><td>".$c.". ".$playerFirst[$c]."<td>".$playerLast[$c]."<td>".$playerEmail[$c]."<td>".$playerSex[$c];
		}
	}

	$body.="<tr><td colspan=4 align=center>------------------------------------------------<td>";
	$body.="<tr><td><B>Comments:</B>";
	$body.="<tr><td colspan=4>".$comments;
	$body.="<tr><td><BR>";
	$body.="<tr><td>Received:<td>".$date."</TABLE>";

	return $body;
}//end function

//Function to actually send the email confirming registration

//How it works:
//- Creates a separate message header and title for the email sent to the captain and the email sent to the convenor
//- The body of the email is the same
//- The captain and convenor recieve a notification message if a team is not registered (saved) but they add a comment

function mailForm($regID, $comSub){
	global $type, $league, $teamName, $regID, $capEmail;

	$title="<font face='verdana' size=3><TABLE align=center cellspacing=2 cellpadding=2>";
	$title.="<tr><td colspan=3 align=center><B>Perpetual Motion's Online Registration System<BR>-- Registration Confirmation --</B>";

	$secondary="<BR>New Team Registered for <br><B>".stripslashes($type)." - ".stripslashes($league).".</B> ";
	$body=body($regID);
	$teamLine="<TR><td align=center colspan=3><b>Team Name:</b> ".stripslashes($teamName);
	$regIDLine="<TR><td align=center colspan=3><b>Team Name:</b> ".stripslashes($teamName);
	$regIDLine.="<tr><td colspan=3 align=center>Team Number: ".$regID;

	$message=$title.$secondary.$teamLine.$body;
	$altMessage=$title.$secondary.$regIDLine.$body;
	$to=$capEmail;
	$subject="Registration Confirmation - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES));
	$altSubject="Reg - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES))." - ".stripslashes(html_entity_decode($league, ENT_QUOTES));

	if($comSub==1){
		$subject="Comment Added - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES));
		$altSubject="Com - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES))." - ".stripslashes(html_entity_decode($league, ENT_QUOTES));

	}

	$from_head  = 'MIME-Version: 1.0' . "\r\n";
	$from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$from_head .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$from_head .= 'From: info@perpetualmotion.org';

	mail($to, $subject,  rtrim(chunk_split(base64_encode($message))), $from_head);
	mail('dave@perpetualmotion.org',  $altSubject, rtrim(chunk_split(base64_encode($altMessage))), $from_head);
	mail('derek@perpetualmotion.org', $altSubject, rtrim(chunk_split(base64_encode($altMessage))), $from_head);

}//end function

function mailWaivers() {
	global $type, $league, $teamName, $playerEmail, $people, $sport;
	if ($sport == 'ultimate') {
		$sportID = 1;
	} else if ($sport == 'beach') {
		$sportID = 2;
	} else if ($sport == 'football') {
		$sportID = 3;
	} else if ($sport == 'soccer') {
		$sportID = 4;
	}
	
	$title='<font face=verdana size=3><TABLE align=center cellspacing=2 cellpadding=2>';
	$title.='<tr><td align=center><B>Perpetual Motion\'s Online Waivers<BR></B></td></tr>';
	$body='<tr><td><BR>You have been registered for <B>'.stripslashes($type).' - '.stripslashes($league).'.</B></td></tr>';
   	$body.='<tr><td>Please go to our <a href=http://www.perpetualmotion.org/waiver.php?sportID='.$sportID.'>Online Waiver</a> page to sign your waiver.';
    $body.='</td></tr>';
	$body.='<tr><td>Please note you only need to do this once per season</td></tr>';
	$body.='<tr><td>Thank you for signing your waiver online, have a great season!</td></tr></TABLE>';

	$message=$title.$body;

	$subject='Online Waiver - '.stripslashes(html_entity_decode($teamName, ENT_QUOTES));
	
	$from_head  = 'MIME-Version: 1.0' . "\r\n";
	$from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$from_head .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$from_head .= 'From: info@perpetualmotion.org';
	
	for($i=1;$i<$people;$i++) {
		if (filter_var($playerEmail[$i], FILTER_VALIDATE_EMAIL)) {
			$to = $playerEmail[$i];
			mail($to, $subject,  rtrim(chunk_split(base64_encode($message))), $from_head);
		}
	}
}

?>