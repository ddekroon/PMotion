<?php /***************
* Derek Dekroon
* July 16/2012
* This program holds the functions for what happens when the buttons are pressed
*********************/

//Saves the data in the db, if there was a comment then send it to davey crockett
function save($teamObj, $playerObj, $userID){
	$areComments = $teamObj->teamComments != '' ? 1 : 0;
	insertInfo($teamObj, $playerObj, $userID, 0);
	if(strlen($teamObj->teamComments > 2)) {
		mailForm($userID, $areComments); //Send email if there were comments added before save
	}
	header("Location: thankyousave.htm"); //Show "Thank you for saving" page
}//end function

//If the user hits register the second time and gets by error checking, put info into the db, mail reg form to everyone, mail waivers to players
function register($teamObj, $playerObj, $userID){
	$areComments = $teamObj->teamComments != '' ? 1 : 0;
	if($teamObj->teamPayMethod != 0) {
		insertInfo($teamObj, $playerObj, $userID, 1);
		mailForm($userID, $areComments);   //Send confirmation emails to the convenor and the user who registered
		mailWaivers();
		header("Location: thankyoureg.htm");  //show the thank you for registering page
	} else {
		print 'ERROR - please choose payment information (javascript must be enabled)';
	}
}// end function

//update teams data... pretty straight forward
function updateTeam($teamObj, $playerObj, $userID){
	global $isRegisteredDB;

	$areComments = $teamObj->teamComments != '' ? 1 : 0;
	$regSwitch = checkRegChange($teamObj->teamIsRegistered, $isRegisteredDB);
	updateInfo($teamObj, $playerObj, $userID, $regSwitch);
	if($regSwitch == 0) { //0=no change, 1=now registered, 2 = now not registered
		mailForm($userID, $areComments, 1); //no change so set saved filter = 1, that way the team captain won't get a new email.
	} else if($regSwitch == 1) { //0=no change, 1=now registered, 2 = now not registered
		mailForm($userID, $areComments);
	} else if($regSwitch == 2) {
		mailTeamUnregistered($areComments);
	}
	header("Location: thankYouUpdate.htm");  //show the thank you for registering page
}// end function

//only for the update page, checks to see if the unregistered or registered emails should be sent to davey crokket
function checkRegChange($isRegistered, $isRegisteredDB) {
	if ($isRegistered == $isRegisteredDB) { //no change
		return 0;
	} else if ($isRegistered > $isRegisteredDB) { //werent registered, are now
		return 1;
	} else { //were registered, aren't now
		return 2;
	}
}