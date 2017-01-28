<?php

///This function controls what happens when the register button is pushed on the initial signup page. Note that the register button is different
//than the confirm button on the secondary page where they confirm that they know how much it'll cost etc.
//How it works:
//- Runs an error check on what's entered when the user hits register
//- If there are errors, it reloads the form highlighting the errors in red
//- Keeps doing this until the error check passes, at which point it shows the confirmation screen
function register(){
	global $error, $footer;
	$errorCheck=errorCheck();

	if($errorCheck!="") { 
		$error=$errorCheck;
		$footer=showFooter(0); //reload page, show errors
	}else{
		$error="";
		$footer=showFooter(1); //show confirmation screen
	}
}//end function

//This function controls what happens when the save button is pushed on any of the registration pages.
//How it works:
//- Runs an error check on what's entered when the user hits save
//- If there are errors, it reloads the form highlighting the errors in red
//- Keeps doing this until the error check passes, at which point it saves the data in the database and thanks them
//- If the user entered a comment before they saved (ie still looking for players), an email is sent to the admin with the comment
function save(){
	global $error, $footer, $comments;
	$errorCheck="";
	$errorCheck=errorCheck();
	if($errorCheck!=""){
		$error=$errorCheck;
		$footer=showFooter(0); //Reload page, show errors
	}else{
		$error="";
		insertInfo(0);
		if($comments!=""){
			mailForm($regId, 1); //Send email if there were comments added before save
		}
		header("Location: thankyousave.htm"); //Show "Thank you for saving" page
	}
}//end function

//This function controls what happens when the confirm button is pushed on the confirmation page.
//How it works:
//- The confirm button is only available on the confirmation page (funny how that works, huh?)
//- Runs an error check on what's selected when the user hits confirm
//- Program knows that being on this page means there were no errors in the actual team submission
//- Still runs an error check on the confirmation page making sure the user selected one of the options
//- If there are errors, it reloads the form highlighting the errors in red
//- Keeps doing this until the error check passes, at which point it saves the data in the database as registered and thanks them
//- Admin and user/captain get an email confirming their registration
function confirm(){
        global $secondaryError, $footer, $comments, $userID;
        $areComments = $comments != '' ? 1 : 0;
        $errorCheck=errorCheck();
        if($errorCheck!=""){
			$secondaryError=$errorCheck;
			$footer=showFooter(1); //Reload confirmation page, show errors
        }else{
			$error="";
			insertInfo(1);
			prepConfirm($userID);   //Save payment data in the database (will pay in person, etc)
			mailForm($userID, $areComments);   //Send confirmation emails to the convenor and the user who registered
			mailWaivers();
			header("Location: thankyoureg.htm");  //show the thank you for registering page
        }
}// end function

function updateTeam(){
	global $secondaryError, $footer, $isRegistered, $isRegisteredDB, $comments;

	$areComments = $comments != '' ? 1 : 0;
	print "areComments = $areComments";
	$errorCheck=errorCheck();
	if($errorCheck!=''){
		$secondaryError=$errorCheck;
		$footer=showFooter(1); //Reload confirmation page, show errors
	}else{
		$regSwitch = checkRegChange($isRegistered, $isRegisteredDB);
		updateInfo($regSwitch);
		if($regSwitch == 1) { //0=no change, 1=now registered, 2 = now not registered
			mailForm($userID, $areComments);
		} else if($regSwitch == 2) {
			mailTeamUnregistered($areComments);
		}
		header("Location: thankYouUpdate.htm");  //show the thank you for registering page
	}
}// end function

//Function to make sure that any variable passed to it is not null or empty

function allProperEntered($data){
	if(strlen($data)>0){
		return true;
	}
	return false;
}//end function

//Function performs simple error checking by determining whether mandatory fields are entered

//How it works:
//- If mandatory field has a value of "" after register or confirm is pressed, obviously nothing was entered
//- Writes a descriptive error for each field that is not entered

function errorCheck(){

	global $capFirst, $capLast, $capEmail, $capPhone, $capSex, $teamName, $leagueID, $update, $payMethod, $update;
	$err=""; //Set err = nothing
	if(!isset($_POST['confirm'])){
		if($capFirst=='') 	$err.="Please enter the captain's first name.<BR>";        //if no captain first name
		if($capLast=='')  	$err.="Please enter the captain's last name.<BR>";         //if no captain last name
		if($capEmail=='') 	$err.="Please enter the captain's email address.<BR>";     //if no captain email
		if($capPhone=='') 	$err.="Please enter the captain's phone number.<BR>";      //if no captain phone
		if($capSex=='')   	$err.="Please select the captain's sex.<BR>";            //if no sex selected
		if($leagueID== 0) {
			$err.="Please select a league/division.<BR>";              //if no league selected
		} else {
			if (checkLeaguesFull($leagueID) ==2) {
				$err.="LEAGUE FULL<BR />";		//If the league is full
			}
			if (checkTeamExists($leagueID, $teamName) == true && $update == 0) {
				$err.='Team name already exists in that league<BR />';
			}
		}
		if($teamName=='') 	$err.="Please enter a teamname.<BR>";                    //if no team name is entered
		if ($update == 1) {
			if($payMethod==0)	  	$err.="Please select your method of payment.<BR>";           //if no payment method is selected
		}
	} else {
		if($payMethod==0)	  	$err.="Please select your method of payment.<BR>";           //if no payment method is selected
	}
	return $err;
}//end function

function checkRegChange($isRegistered, $isRegisteredDB) {
	if ($isRegistered == $isRegisteredDB) { //no change
		return 0;
	} else if ($isRegistered > $isRegisteredDB) { //werent registered, are now
		return 1;
	} else { //were registered, aren't now
		return 2;
	}
}

//This function creates the footer (the bottom section on the registration page, and the actual content of the confirm page)
//How it works:
//- Functions that create the form call showFooter(0) or showFooter(1)
//- showFooter(0) creates the bottom half of the registration page
//- showFooter(1) creates the content of the confirm page (complete with "where to mail cheques to" instructions
//- $foot is the only important variable, it is basically just html code that determines how the footers look
//- Specifically for the confirm page, info on what league was selected and how much it costs is dropped in
//- The user then has to pick what option they want to use to pay

function showFooter($state){

	global $teamName, $capFirst, $capLast, $league, $fee, $registration_due, $update, $isRegistered, $payMethod;
	
	$payMethodString[1] = 'I will send an email money transfer to dave@perpetualmotion.org';
	$payMethodString[2] = 'I will mail cheque to Perpetual Motion\'s home office';
	$payMethodString[3] = 'I will bring cash/cheque to registration night';
	$payMethodString[4] = 'I will bring cash/cheque to Perpetual Motion\'s home office';
	
	if($state==1 || $update == 1){
		$teamName=htmlentities($teamName, ENT_QUOTES);
		$capFirst=htmlentities($capFirst, ENT_QUOTES);
		$capLast=htmlentities($capLast, ENT_QUOTES);
		$leagueName=htmlentities($league, ENT_QUOTES);

		$foot= "<BR><TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><font face='verdana' SIZE=2><B>Confirm Fees</B><BR>";
		$foot.= "<font face='verdana' SIZE=1>";
		$foot.= "<font face='verdana' COLOR='red'>**The registration process is not finalized until fees have been paid**</font>";
		$foot.= "<tr><td><font face='verdana' size=2>Team Name: <B>".$teamName."</B><td align='right'><font face='verdana' size=2>";
		$foot.= "Captain: <B>".$capFirst." ".$capLast."</B>";
		$foot.= "<tr><td><font face='verdana' SIZE='2'>Preferred league: <B>".$leagueName."</B>";
		$foot.= "<TD align='right'><font face='verdana' SIZE='2'>Registration Fee: <B>$".$fee."</B>";
		if ($update == 1) {
			$foot.="<tr><td>Would you like this team to be registered for the current season?";
			$foot.="<td><SELECT NAME='isRegistered'>";
			for ($x = 1; $x>=0;$x--) { 
				$yesNo = $x > 0 ? 'Yes' : 'No';
				if ($x == $isRegistered) {
					$foot.="<OPTION selected Value=$x>$yesNo</OPTION>";	
				} else {
					$foot.="<OPTION Value=$x>$yesNo</OPTION>";	
				}
			}
			$foot.="</SELECT>";
		}
		$foot.= "<tr><TD><font face='verdana' SIZE='2'>Payment Method: <td colspan=2><select name='payMethod'>";
		$foot.= "<option value=0>Choose Payment Method</option>";
		for($x=1; $x<5;$x++) {
			if ($x == $payMethod) {
				$foot.= "<option selected value=$x>$payMethodString[$x]</option>";
			} else {
				$foot.= "<option value=$x>$payMethodString[$x]</option>";
			}
		}
		$foot.= "</select>";
		$foot.= "<tr><td colspan=4 align=center>";
		if ($update == 1) {
			$foot.="<INPUT TYPE='Submit' NAME='update' Value='Update Team'>";
		} else {
			$foot.="<INPUT TYPE='Submit' NAME='confirm' Value='Confirm Registration Submission'>";
		}
		$foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
		$foot.= "<tr><td><br>";
		$foot.= "<tr><td colspan=5 align=center><font face='verdana' color=red size=2><b>Registration due date: $registration_due</b>";
		$foot.= "<tr><td colspan=7 align=center><BR>";
		$foot.= "<B>Make Cheques Payable to Perpetual Motion<BR><BR>";
		$foot.= "Send This Confirmation Form & Fees to:</b>";
		$foot.= "<br>Perpetual Motion";
		$foot.= "<br>223 Waterloo Ave.";
		$foot.= "<br>Guelph, Ontario";
		$foot.= "<br>N1H 3J4";
		$foot.= "<br>(519) 222-0095";
	}else{
		$foot= "<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><font face='verdana' SIZE=2><B>5. Register Your Team or Save Details</B><BR>";
		$foot.= "<font face='verdana' SIZE=1>Submit your team registration to the convenor or save details to your profile for another time.";
		$foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='register' Value='Register'>";
		$foot.= "<INPUT TYPE='Submit' NAME='save' Value='Save Details'>";
		$foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
		$foot.= "<tr><td> <BR>";
		$foot.= "<tr><td colspan=5 align=center><font face='verdana' color=red size=2><b>Registration due date: ".$registration_due."</b>";
	}
	
	return $foot;
}//end footer
?>