<?php
session_start();
ob_start();
	
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'connect.php');    //Connection code for the mysql database
require_once('security.PHP');   //Security code to ensure only logged in users can access this page
	
	if (get_magic_quotes_gpc()) {
	    function stripslashes_deep($value)
	    {
	        $value = is_array($value) ?
	                    array_map('stripslashes_deep', $value) :
	                    stripslashes($value);
	
	        return $value;
	    }
	
	    $_POST = array_map('stripslashes_deep', $_POST);
	    $_GET = array_map('stripslashes_deep', $_GET);
	    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}
	
	
	
	//Gets whatever argument you pass into the program for sport ("?sport=beach" etc)
	    if(isset($_GET['sport'])){
	        $sport = $_GET['sport'];
	    }else{
	        $sport = 'unknown';
	    }
	
	//Saves the variable as a hidden variable for use later on if necessary (ie if page needs to reload b/c of error)
	
	//Gets whatever argument you pass into the form for registration id (will be automatically set in the members page)
	    if(isset($_GET['registrationID'])){
	        $regID = $_GET['registrationID'];
	    }else{
	        $regID = 'unknown';
	    }
	
	//###################################################################################################################################################
	//                                             ADDED SECURITY FOR URL HACKERS
	//###################################################################################################################################################
	
	//These lines of code will stop people from changing the variables passed in the URL
	//and essentially "hacking" the database to see other teams' information
	//If the sport in the URL doesn't match the sport assigned to the regID or if
	//the regID doesn't match the user (ie they didn't create it) kick them out!
	//Example: if the url is www.perpetualmotion.org/Registration/update.PHP?sport=beach&regID=100 and they try
	//to change the regID to 200 (ie to see another teams' info) it will block them!
	
	
	$databaseArray=query("SELECT * FROM registration WHERE registrationID=$regID");
	$databaseSportName=$databaseArray['sport']; 
	$databaseUserName=$databaseArray['userName']; 
	
	$databaseUserName=strtolower($databaseUserName);
	$user2=strtolower($user);
	
	if(($user2!=$databaseUserName) OR ($sport!=$databaseSportName)){
	     header ("Location: securityWarning.htm");
	     //Log them out, give the warning page (why it happened)
	}
	
	
	//###################################################################################################################################################
	//                                         END OF ADDED SECURITY FOR URL HACKERS
	//###################################################################################################################################################
	
	//###################################################################################################################################################
	//                                  START OF FUNCTION TO PREPARE DATA FOR ENTRY INTO THE DATABASE
	//###################################################################################################################################################
	
	
	
	
	//This function makes sure that the data makes it into the database without causing any problems.
	//Databases are very picky and "breakable" so the smallest apostrophe ('), quotation mark ("), or misplaced bracket ({,[,() will throw it off
	
	//PrepareSave -- How it works:
	//- This function is called after all error checking is completed
	//- This function is called in one of two cases:
	//-   1) When the user hits save on the update page
	//-   2) When the user hits register on the update page but they're registering for the same league
	//- First it declares all variables as global. This is necessary to make sure we're manipulating the correct data
	//- It "addslashes" each of the variables in question to make sure they won't screw up the database
	//- It deletes all previous data registered for that team
	//- It runs the code to insert the data into the registration database and captain database
	//- The code needs to be executed in a loop for the player database
	//
	
	function prepareSave($boolRegistered){
	    global  $regID, $league, $teamName, $semi, $capFirst, $capLast, $capSex, $capEmail, $capPhone, $method;
	    global $playerFirst, $playerLast, $playerSex, $playerEmail, $user, $sport, $people, $comments;
	
	    $methodA=query("SELECT method FROM registration WHERE registrationID=".$regID) or die("Method A ".mysql_error());
	    $method=$methodA['method'];
	
	    //By deleting previous records, it's a cheaters way of updating the fields
	    //Insted of using update, I just delete what was there and copy over it with all of the newly obtained data
	    $remove="DELETE FROM players WHERE registrationID='".$regID."'";
	    $deletePlayer=mysql_query($remove) or die("Delete Player ".mysql_error());
	
	    $remove="DELETE FROM captains WHERE registrationID='".$regID."'";
	    $deleteCaptain=mysql_query($remove)or die("Delete Captain ".mysql_error());
	
	    $remove="DELETE FROM registration WHERE registrationID='".$regID."'";
	    $deleteReg=mysql_query($remove) or die("Delete Registration ".mysql_error());
	
	
	    //ADDS SLASHES BEFORE SAVING TO DATABASE
	    $league=addSlashes(html_entity_decode($league, ENT_QUOTES));
	    $semiID=$semi;
	    $teamName=addSlashes(html_entity_decode($teamName, ENT_QUOTES));
	    $comments=addSlashes(html_entity_decode($comments, ENT_QUOTES));
	
	
	    $capFirst=addSlashes(html_entity_decode($capFirst, ENT_QUOTES));
	    $capLast=addSlashes(html_entity_decode($capLast, ENT_QUOTES));
	    $capSex=addSlashes(html_entity_decode($capSex, ENT_QUOTES));
	
	    $capEmail=addSlashes(html_entity_decode($capEmail, ENT_QUOTES));
	    $capPhone=preg_replace("/\D/",'',$capPhone); //removes any non-digits (brackets, dashes, dots, etc)
	
	
	    //REGISTRATIONID NUM -- A Primary Key (Each number only used once, is 1 higher than the highest regID in the table...)
	
	    /*if(strlen($league)<=5){
	         header("Location: RegistrationError.htm");
	    }*/
	
	    $insert="INSERT INTO registration (registrationID, teamName, sport, league, semi_identifier, userName, cFirst, cLast, comments, method, registered) VALUES
	    (".$regID.", '".$teamName."', '".$sport."', '".$league."', '".$semiID."', '".$user."', '".$capFirst."
	    ', '".$capLast."', '".$comments."', '".$method."', $boolRegistered)";
	    $addRegistration=mysql_query($insert) or die("The insert into registration query threw an error ".mysql_error());
	
	
	    $insert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES
	    (".$regID.", '".$teamName."', '".$capFirst."', '".$capLast."'
	    , '".$capEmail."', '".$capPhone."', '".$capSex."')";
	    $addCaptain=mysql_query($insert) or die("The insert into captains query threw an error ".mysql_error());
	
	
	    for ($e=1; $e<=$people; $e++){
	        $playerFirst[$e]=addSlashes(html_entity_decode($playerFirst[$e], ENT_QUOTES));
	        $playerLast[$e]=addSlashes(html_entity_decode($playerLast[$e], ENT_QUOTES));
	        $playerEmail[$e]=addSlashes(html_entity_decode($playerEmail[$e], ENT_QUOTES));
	        $playerSex[$e]=addSlashes(html_entity_decode($playerSex[$e], ENT_QUOTES));
	        if((strlen($playerFirst[$e])>=1)){
	            $insert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES
	            (".$regID.", '".$teamName."', '".$playerFirst[$e]."', '".$playerLast[$e]."',
	            '".$playerEmail[$e]."', '".$playerSex[$e]."')";
	            $addPlayer=mysql_query($insert) or die("The insert into players query threw an error ".mysql_error());
	            $addEmail=mysql_query("INSERT IGNORE INTO addressDatabase (FirstName, LastName, EmailAddress) VALUES ('$fName[$e]', '$lName[$e]', '$email[$e]'");
	        }
	    }//end for
	
	}//end function
	
	function prepareRegister(){     // Function that adds a new team record if the team registers for a new league
	    global $regID, $capFirst, $capLast, $capEmail, $capSex, $capPhone, $league, $semi;
	    global $playerEmail, $playerFirst, $playerLast, $playerSex, $people, $teamName, $user, $sport, $comments;
	
	    //By deleting previous records, it's a cheaters way of updating the fields
	    //Insted of using update, I just delete what was there and copy over it with all of the newly obtained data
	/*
	    $remove="DELETE FROM players WHERE registrationID='".$regID."'";
	    $deletePlayer=mysql_query($remove) or die("Delete Player ".mysql_error());
	
	    $remove="DELETE FROM captains WHERE registrationID='".$regID."'";
	    $deleteCaptain=mysql_query($remove)or die("Delete Captain ".mysql_error());
	
	    $remove="DELETE FROM registration WHERE registrationID='".$regID."'";
	    $deleteReg=mysql_query($remove) or die("Delete Registration ".mysql_error());
	*/
		
		$updateShowstandingsQ=mysql_query("SELECT identifier FROM showstandings WHERE registrationID=".$regID);
		//print "SELECT identifier FROM showstandings WHERE registrationID=".$regID;
		$updateShowstandingsA=mysql_fetch_array($updateShowstandingsQ);
		$ident=$updateShowstandingsA['identifier'];
	
	
	    //ADDS SLASHES BEFORE SAVING TO DATABASE
	    $user=addSlashes(html_entity_decode($user, ENT_QUOTES));
	    $league=addSlashes(html_entity_decode($league, ENT_QUOTES));
	    $teamName=addSlashes(html_entity_decode($teamName, ENT_QUOTES));
	    $capFirst=addSlashes(html_entity_decode($capFirst, ENT_QUOTES));
	    $capLast=addSlashes(html_entity_decode($capLast, ENT_QUOTES));
	    $capSex=addSlashes(html_entity_decode($capSex, ENT_QUOTES));
	    $capEmail=addSlashes(html_entity_decode($capEmail, ENT_QUOTES));
	    $comments=addSlashes(html_entity_decode($comments, ENT_QUOTES));
	    $capPhone=preg_replace("/\D/",'',$capPhone); //remove all non-digits (dashes, dots, brackets, etc)
	    $semiID=$semi;
	
	
	    $selectAll=mysql_query("SELECT MAX(registrationID) as maxnum FROM captains");
	    $regArr=mysql_fetch_array($selectAll);
	    $maximum=$regArr['maxnum'];
	
	
	    global $regID;
	    $regID=$maximum+1;
	    ?><INPUT TYPE='hidden' NAME='reg' VALUE=$regID><?php
	
	
	
	    /*if(strlen($league)<=5){
	         header("Location: RegistrationError.htm");
	    }*/
	
	    //REGISTRATIONID NUM -- A Primary Key (Each number only used once, is 1 higher than the highest regID in the table...)
	    $insert="INSERT INTO registration (registrationID, teamName, sport, league, semi_identifier, userName, cFirst, cLast, comments, method, registered) VALUES
	    (".$regID.", '".$teamName."', '".$sport."', '".$league."', '".$semiID."', '".$user."', '".$capFirst."
	    ', '".$capLast."', '".$comments."', '', 1)";
	    $addRegistration=mysql_query($insert) or die("The insert into registration query threw an error ".mysql_error());
		
		$updateShowstandings=mysql_query("UPDATE showstandings SET registrationID=$regId WHERE identifier='".$ident."'");
		//print "UPDATE showstandings SET registrationID=$regId WHERE identifier='".$ident."'";
	
	
	
	    $insert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES
	    (".$regID.", '".$teamName."', '".$capFirst."', '".$capLast."'
	    , '".$capEmail."', '".$capPhone."', '".$capSex."')";
	    $addCaptain=mysql_query($insert) or die("The insert into captains query threw an error ".mysql_error());
	
	
	
	    for ($e=1; $e<=$people; $e++){
	        $playerFirst[$e]=addSlashes(html_entity_decode($playerFirst[$e], ENT_QUOTES));
	        $playerLast[$e]=addSlashes(html_entity_decode($playerLast[$e], ENT_QUOTES));
	        $playerEmail[$e]=addSlashes(html_entity_decode($playerEmail[$e], ENT_QUOTES));
	        $playerSex[$e]=addSlashes(html_entity_decode($playerSex[$e], ENT_QUOTES));
	        if((strlen($playerFirst[$e])>=1)){
	            $insert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES
	            (".$regID.", '".$teamName."', '".$playerFirst[$e]."', '".$playerLast[$e]."',
	            '".$playerEmail[$e]."', '".$playerSex[$e]."')";
	            $addPlayer=mysql_query($insert) or die("The insert into players query threw an error ".mysql_error());
	            $addEmail=mysql_query("INSERT IGNORE INTO addressDatabase (FirstName, LastName, EmailAddress) VALUES ('$fName[$e]', '$lName[$e]', '$email[$e]'");
	        }
	    }//end for
	
	}//end function
	
	function prepConfirm(){
	    global $method, $regID;
	
	    $insert="UPDATE registration SET method='".$method."' WHERE registrationID=".$regID;
	    $addRegistration=mysql_query($insert);
	    $insert="UPDATE registration SET registered= 1 WHERE registrationID=".$regID;
	    $addRegistration=mysql_query($insert);
	}//end prepConfirm
	
	//###################################################################################################################################################
	//                                  END OF FUNCTION TO PREPARE DATA FOR ENTRY INTO THE DATABASE
	//###################################################################################################################################################
	
	                                                                     
	//###################################################################################################################################################
	//                                         'BACKEND CODE' FUNCTIONS FOR EACH BUTTON
	//###################################################################################################################################################
	
	
	//This function controls what happens when the register button is pushed on the initial page. Note that the register button is different
	//than the confirm button on the secondary page where they confirm that they know how much it'll cost etc.
	
	//How it works:
	//- Runs an error check on what's entered when the user hits register
	//- If there are errors, it reloads the form highlighting the errors in red
	//- Keeps doing this until the error check passes, at which point it checks to see if the user kept the same league or not
	//- If the user kept the same league, it treats the data as an "update and save", even if they hit register
	//- If during this update and save, the user adds a comment or changes the team name, an email is sent to the convenor
	//- If the user changed leagues, it treats the data as a new registration and takes them through to the confirmation page
	
	
	function register(){
	    global $confirmed, $regID, $error, $footer, $teamName, $comments;
	    $errorCheck=errorCheck();
	    $array=query("SELECT * FROM registration WHERE registrationID=$regID");
	    $confirmed=$array['registered'];
	    if($errorCheck!=""){//There was an error somewhere, print all errors and show the basic footer
	        $error=$errorCheck;
	        $footer=showFooter(0);
	
	    }else{
	        $error=""; //No Errors, check if the league is the same, if so save details, if not show the confirmation/fee footer box
	        if((leagueSame()) and $confirmed==1){
	            if($array['teamName']==$teamName){
	                prepareSave(1);
	                if($comments!=""){
	                        mailForm($regID, 1);
	                }
	                header("Location: thankYouUpdate.htm");  //Show the "Thank you for updating" page
	            }else{
	                $error.="Please email <a href='mailto:info@perpetualmotion.org'>info@perpetualmotion.org</a> if you'd like to change your existing team's name<BR>";
	                $footer=showFooter(0);
	            }
	        }else{
	                $footer=showFooter(1); //Show the confirmation page, the league isn't the same so it'll be treated as a new registration
	        }
	    }
	} // END FUNCTION
	
	
	
	//The backend code for when the save button is pressed
	function save(){
	        global $footer, $error, $comments, $confirmed, $regID, $teamName;
	        $arrayConfirmed=query("SELECT * FROM registration WHERE registrationID=$regID");
	        $confirmed=$arrayConfirmed['registered'];
	        $errorCheck="";
	        $errorCheck=errorCheck();
	        $teamName=stripslashes($teamName);
	        if($errorCheck!=""){
	                $error=$errorCheck;
	                $footer=showFooter(0);
	        }else{
	                $error="";
	                if(leagueSame()){           //If the league is the same
	                    if($confirmed==1){      // and if the team has already confirmed
	                         if($arrayConfirmed['teamName']==$teamName){  //and if the team has the same name
	                                prepareSave(1);              //simply save what they entered
	                                if($comments!=""){          // if there was a comment added, send the email
	                                     mailForm($regID, 1);
	                                }
	                                header("Location: thankYouUpdate.htm");  //Show the "Thank you for updating" page
	
	                        }else{                             //If the team changed their name and hit save after they'd confirmed, show an error
	                                $error.="Please email <a href='mailto:info@perpetualmotion.org'>info@perpetualmotion.org</a> if you'd like to change your existing team's name<BR>";
	                                $footer=showFooter(0);
	                        }
	                    }else{
	                        if($comments!=""){          // if there was a comment added, send the email
	                                mailForm($regID, 1);
	                        }
	                        prepareSave(0);
	                        if($comments!=""){          // if there was a comment added, send the email
	                                     mailForm($regID, 1);
	                        }
	                        header("Location: thankYouUpdate.htm");  //Show the "Thank you for updating" page
	                    }
	                }else{
	                $footer=showFooter(1); //Show the confirmation page, the league isn't the same so it'll be treated as a new registration
	                }
	                
	        }
	}//end function
	
	
	
	//The backend code for when the confirm button is pressed (different then register!!)
	function confirm(){
	        global $secondaryError, $footer, $error, $regID; // Used if there's an error on the 'confirm' page
	        $errorCheck="";
	        $errorCheck=errorCheck();
	        if(($errorCheck!="")){ //There was an error somewhere, set error so it'll print the error, show basic footer
	                $secondaryError=$errorCheck;
	                $footer=showFooter(1);
	        }
	        else{
	                $error="";
	                prepareRegister();     //Execute the prepare function to insert data into the database, creates a new team record for the new league
	                prepConfirm(); //Execute the prepConfirm function which inserts the "payment" data into the db
	                mailForm($regID, 0);//Execute the mailConvenor function which emails the convenor a confirmation email
					mailWaivers();
	                header("Location: thankyoureg.htm");  //show the thank you for registering page
	        }// end else
	}// end function
	
	//###################################################################################################################################################
	//                                         END OF 'BACKEND CODE' FUNCTIONS FOR EACH BUTTON
	//###################################################################################################################################################
	
	
	
	//###################################################################################################################################################
	//                                         ERROR CHECKING AND GENERAL FUNCTIONS
	//###################################################################################################################################################
	
	//This function expedites the process of querying a database
	//How it works:
	//- Cuts necessary coding from 3 lines to 2 lines and eliminates repitition
	
	function query($query_string){
	        $quer_line=mysql_query($query_string);
	        $array_line=mysql_fetch_array($quer_line);
	        return ($array_line);
	}//end function
	
	//This function formats a phone number into the proper display. Makes "5198234502" or anything similar appear as "(519)-823-4502"
	//How it works:
	//- Chops phone number input into 3 sections: area code, prefix (first 3 digits), and number (last 4 digits)
	
	function formatPhoneNumber($strPhone){
	        $strPhone = ereg_replace("[^0-9]",'', $strPhone);
	        if (strlen($strPhone) != 10){
	                return $strPhone;
	        }
	
	        $strArea = substr($strPhone, 0, 3);
	        $strPrefix = substr($strPhone, 3, 3);
	        $strNumber = substr($strPhone, 6, 4);
	
	        $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;
	
	        return ($strPhone);
	}//end function
	
	//Function to make sure that any variable passed to it is not null or empty
	//How it works:
	//- returns true if the length of the string passed into it is greater than 0
	//- returns false otherwise
	//- pretty simple, huh?
	//
	
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
	
	        global $capFirst, $capLast, $capEmail, $capPhone, $capSex, $semi, $teamName, $method;
	
	        $err=""; //Set err = nothing
	        if(!isset($_POST['confirm'])){
	                if($capFirst==""){  $err.="Please enter the captain's first name.<BR>";}        //if no captain first name
	                if($capLast==""){   $err.="Please enter the captain's last name.<BR>";}         //if no captain last name
	                if($capEmail==""){  $err.="Please enter the captain's email address.<BR>";}     //if no captain email
	                if($capPhone==""){  $err.="Please enter the captain's phone number.<BR>";}      //if no captain phone
	                if($capSex==""){      $err.="Please select the captain's sex.<BR>";}            //if no sex selected
	                if($semi==""){    $err.="Please select a league/division.<BR>";}              //if no league selected
	                if($teamName==""){  $err.="Please enter a teamname.<BR>";}                    //if no team name is entered
	        }else{
	                if($method==""){$err.="Please select your method of payment.<BR>";}           //if no payment method is selected
	        }
	
	        return $err;
	}//end function
	
	
	//Function checks if a user is updating a team in the same league or registering a new team
	//How it works:
	//- When a user clicks on previous teams, it brings them to the update page with all of their info filled in
	//- If they add a team member, change their team's name or add a comment without changing the league, the program knows they've just updated
	//- If they change the league, it treats it as if they're registering a new team
	//- The program needs to know whether to treat it as an update or a register so that it can handle the data properly
	
	function leagueSame(){ 
	    global $semi, $regID;      
	    $array=query('SELECT semi_identifier FROM registration WHERE registrationID='.$regID);
	    $semiID_old=$array['semi_identifier'];
	    if($semiID_old==$semi){
	        return true;
	    }else{
	        return false;
	    }
	}//end leagueSame
	
	
	
	
	//###################################################################################################################################################
	//                                      END OF ERROR CHECKING AND GENERAL FUNCTIONS
	//###################################################################################################################################################
	
	                                      
	//VARIABLE DECLARATIONS (BASED ON WHAT SPORT WAS CHOSEN) OCCUR IN THIS FILE
	//NOTE: This is where the logo is defined, as well
	include 'declarations.PHP';
	
	
	 ///////// Getting the data from Mysql table for the league dropdown, uses $fiter defined above//////////
	$quer2=mysql_query("SELECT DISTINCT category, cat_id, semi_identifier, registrationDue FROM regcategory ".$filter." AND available=1 ORDER BY semi_identifier")or die("quer2 threw an error: ".mysql_error());
	///////////// End of query for first list box////////////
	
	
	
	
	
	
	                                                                                    
	
	//###################################################################################################################################################
	//                                                 START OF EMAIL FUNCTIONS
	//###################################################################################################################################################
	
	//Function to create the body of the email for both captain and player
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
	
	        $body.="<TR><TD colspan=4 align=center><font color=red>".$topNotice."</td></tr>";
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
	                        $body.="\r\n <tr><td>".$c.". ".$playerFirst[$c]."<td>".$playerLast[$c]."<td>".$playerEmail[$c]."<td>".$playerSex[$c];
	                }
	        }
	
	        $body.="<tr><td colspan=4 align=center>------------------------------------------------<td>";
	        $body.="<tr><td><B>Comments:</B>";
	        $body.="<tr><td colspan=4>".$comments;
	        $body.="<tr><td><BR>";
	        $body.="<tr><td>Received:<td>".$date."</TABLE>";
	
	        return $body;
	}//end function
	
	//Function to email both dave (convenor) and the captain
	function mailForm($regID, $comSub){
	    global $league, $type, $title, $secondary, $teamLine, $body, $capEmail, $league, $teamName;
	    
	    $title="<font size=3><TABLE align=center cellspacing=2 cellpadding=2>";
	    $title.="<tr><td colspan=3 align=center><B>Perpetual Motion's Online Registration System";
	    $title.="<BR>-- Registration Confirmation --</B>";
	    $secondary="<BR>Existing Team Updated and Registered for <br><B>".stripslashes($type)." - ".stripslashes($league).".</B>";
	    
	    $body= body($regID);
	    
	    $teamLine="<TR><td align=center colspan=3><b>Team Name:</b> ".stripslashes($teamName);
	    //print "1. TEST JUNE 10 - ".stripslashes($teamName);
		$regIDLine="<TR><td align=center colspan=3><b>Team Name:</b> ".stripslashes($teamName);
	    $regIDLine.="<tr><td colspan=3 align=center>Team Number: ".$regID;
	    
	    $message=$title.$secondary.$teamLine.$body;
	    $altMessage=$title.$secondary.$regIDLine.$body;
	    
	    $to=$capEmail;
	    
	    $subject="Registration Confirmation - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES));
	    $altSubject="Reg - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES))." - ".stripslashes(html_entity_decode($league, ENT_QUOTES));
	    //print "<BR> 2. TEST JUNE 10 - ".stripslashes($subject);
		//print "<BR> 3. TEST JUNE 10 - ".stripslashes($altSubject);
	    if($comSub==1){
	        $subject="Comment Added - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES));
	        $altSubject="Com - ".stripslashes(html_entity_decode($teamName, ENT_QUOTES))." - ".stripslashes(html_entity_decode($league, ENT_QUOTES));
			//print "<BR> 4. TEST JUNE 10 - ".stripslashes($subject);
			//print "<BR> 5. TEST JUNE 10 - ".stripslashes($altSubject);
	    }
	    
	    $from_head  = 'MIME-Version: 1.0' . "\r\n";
	    $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	    $from_head .= 'Content-Transfer-Encoding: base64' . "\r\n";
	    $from_head .= 'From: info@perpetualmotion.org';
	    
	    mail($to, $subject, rtrim(chunk_split(base64_encode($message))), $from_head);
	    //print "<BR> 6. TEST JUNE 10 - DID MAIL";
		
		mail('dave@perpetualmotion.org', $altSubject, rtrim(chunk_split(base64_encode($altMessage))), $from_head);
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
	
	//###################################################################################################################################################
	//                                      END OF EMAIL FUNCTIONS
	//###################################################################################################################################################
	
	
	//###################################################################################################################################################
	//                                                  START OF FOOTER FUNCTION
	//###################################################################################################################################################
	
	//This function creates the footer (the bottom section on the registration page, and the actual content of the confirm page)
	
	//How it works:
	//- Functions that create the form call showFooter(0) or showFooter(1)
	//- showFooter(0) creates the bottom half of the registration page
	//- showFooter(1) creates the content of the confirm page (complete with "where to mail cheques to" instructions
	//- $foot is the only important variable, it is basically just html code that determines how the footers look
	//- Specifically for the confirm page, info on what league was selected and how much it costs is dropped in
	//- The user then has to pick what option they want to use to pay
	
	function showFooter($state){
	
	        global $teamName, $capFirst, $capLast, $league, $fee, $registration_due;
	
	        if($state==1){
	
	                $teamName=htmlentities($teamName, ENT_QUOTES);
	                $capFirst=htmlentities($capFirst, ENT_QUOTES);
	                $capLast=htmlentities($capLast, ENT_QUOTES);
	                $leagueName=htmlentities($league, ENT_QUOTES);
	
	                $foot= "<BR><TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>Confirm Fees</B><BR>";
	                $foot.= "<FONT SIZE=1>";
	                $foot.= "<FONT COLOR='red'>**The registration process is not finalized until fees have been paid**</font>";
	                $foot.= "<tr><td><Font size=2>Team Name: <B>".$teamName."</B><td align='right'><Font size=2>";
	                $foot.= "Captain: <B>".$capFirst." ".$capLast."</B>";
	                $foot.= "<tr><td><FONT FACE='VERDANA' SIZE='2'>Preferred league: <B>".$leagueName."</B>";
	                $foot.= "<TD align='right'><FONT FACE='VERDANA' SIZE='2'>Registration Fee: <B>$".$fee."</B>";
	                $foot.= "<tr><TD><FONT FACE='VERDANA' SIZE='2'>Payment Method: <td colspan=2><select name='method'>";
	                $foot.= "<option value=''>Choose Payment Method</option>";
	                $foot.= "<option value='Team will mail fees to the office'>I will mail cheque to Perpetual Motion's home office</option>";
	                $foot.= "<option value='Team will bring fees to registration night'>I will bring cash/cheque to registration night</option>";
	                $foot.= "<option value='Team will bring fees To the office'>I will bring cash/cheque to Perpetual Motion's home office</option>";
					$foot.= "<option value='Team will send an email money transfer'>I will send an email money transfer to dave@perpetualmotion.org</option>";
	                $foot.= "</select>";
	                $foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='confirm' Value='Confirm Registration Submission'>";
	                $foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
	                $foot.= "<tr><td><br>";
	                //$foot.= "<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b>";
	                $foot.= "<tr><td colspan=7 align=center><BR>";
	                $foot.= "<B>Make Cheques Payable to Perpetual Motion<BR><BR>";
	                $foot.= "Send This Confirmation Form & Fees to:</b>";
	                $foot.= "<br>Perpetual Motion";
	                $foot.= "<br>223 Waterloo Ave.";
	                $foot.= "<br>Guelph, Ontario";
	                $foot.= "<br>N1H 3J4";
	                $foot.= "<br>(519) 222-0095";
	
	        }else{
	                $foot.= "<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>5. Register Your Team or Save Details</B><BR>";
	                $foot.= "<FONT SIZE=1>Submit your team registration to the convenor or save details to your profile for another time.";
	                $foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='register' Value='Register'>";
	                $foot.= "<INPUT TYPE='Submit' NAME='save' Value='Save Details'>";
	                $foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
	                $foot.= "<tr><td> <BR>";
	                $foot.= "<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: ".$registration_due."</b>";
	        }
	        return $foot;
	}//end function
	
	//###################################################################################################################################################
	//                                                  END OF FOOTER FUNCTION
	//###################################################################################################################################################
	
	
	
	
	
	
	
	//###################################################################################################################################################
	//                                                  "FRONT-END" FORM VARIABLE DECLARATIONS
	//###################################################################################################################################################
	
	// These are the variables used when reshowing the form to the user
	// They have to be different than the "Back-end" or "raw" data which is formatted differently to go into the database
	
	// ** Example: **
	// Real Team name: "Can't Score Won't Score" 
	// FRONT END VARIABLE would hold: Can't Score Won't Score (no difference)
	// Back End Variable would hold: Can/'t Score Won/'t Score (slashes)  
	
	$reg_due_array=query("SELECT DISTINCT category, cat_id, semi_identifier, registrationDue FROM regcategory ".$filter." AND available=1 ORDER BY semi_identifier");
	$registration_due=$reg_due_array['registrationDue'];
	
	
	if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm']) or (strlen($secondaryError>1)) or (strlen($error>1))){
	    //If one of the buttons is pressed, the form is refilled using data from the form
	    //htmlentities is just a standardized function to make sure it appears as it should (without slashes etc)
	                
	                //---League Information (cat_id, league name, and price of the league)---\\
	                if (isset($_POST['league'])){
	                        $semi=$_POST['league'];
	                        $holderVariable=$_POST['holderVariable'];
	                        if ($semi=="" or (strlen($semi))>=12){
	                                $semi=$holderVariable;
	                        }elseif($semi=="nullVal"){
	                                $semi="";
	                        }
	                        $leagueArray=query("SELECT * FROM regcategory WHERE semi_identifier='".$semi."'");
	                        $league=$leagueArray['category'];
	                        $semiIdentifier=$semi;
	                        $fee=$leagueArray['fee'];
							$topNotice=$leagueArray['topNotice'];
	                }else{
	                        $semi="";
	                }
	                //------------------------------------------------------------------------\\
	
	                $teamName=$_POST['teamName'];
	                //Captain variables
	                $capFirst=$_POST['capFirst'];
	                $capLast=$_POST['capLast'];
	                $capEmail=$_POST['capEmail'];
	                $capPhone=$_POST['capPhone'];
	                $capSex=$_POST['capSex'];
	                $capMaleSelected='';
	                $capFemaleSelected='';
	                if($capSex=='Male'){
	                        $capMaleSelected="selected";
	                }elseif($capSex=='Female'){
	                        $capFemaleSelected="selected";
	                }else{
	                        $capNoneSelected="selected";
	                }
	                
	                //People variables
	                //These are populated using a for loop
	                for($r=1; $r<=$people; $r++){
	                        $playerFirst[$r]=$_POST['playerFirst'.$r];
	                        $playerLast[$r]=$_POST['playerLast'.$r];
	                        $playerEmail[$r]=$_POST['playerEmail'.$r];
	                        $playerSex[$r]=$_POST['playerSex'.$r];
	                        $playerMaleSelected[$r]='';
	                        $playerFemaleSelected[$r]='';
	                        $playerNoneSelected[$r]='';
	                        if($playerSex[$r]=='Male'){
	                                $playerMaleSelected[$r]="selected";
	                        }elseif(
	                                $playerSex[$r]=='Female'){$playerFemaleSelected[$r]="selected";}
	                        else{
	                                $playerNoneSelected[$r]="selected";
	                        }
	                }//end for (r)
	                $comments=$_POST['comments'];
	                $method=$_POST['method'];
	        }else{ 
	            //No buttons were pressed, therefore this is the initial (old data) version of the form
	            //Populate captain info from the captain database
	
	                $registrationArray=query("SELECT * from registration where registrationID=".$regID);
	                $semi=$registrationArray['semi_identifier'];
	                $comments=$registrationArray['comments'];
	                
	                //Captain variables
	                $capArray=query("SELECT * from captains where registrationID=".$regID);
	                $capFirst=$capArray['cFirst'];
	                $capLast=$capArray['cLast'];
	                $capEmail=$capArray['cEmail'];
	                $capPhone=formatPhoneNumber($capArray['cPhone']);
	                $capSex=$capArray['cSex'];
	                $capMaleSelected='';
	                $capFemaleSelected='';
	                if($capSex=='Male'){
	                        $capMaleSelected="selected";
	                }elseif($capSex=='Female'){
	                        $capFemaleSelected="selected";
	                }
	
	                $teamName=$capArray['teamName'];
	
	                //Player Variables
	                $query=mysql_query("SELECT * FROM players WHERE registrationID=".$regID);
	                $playCount=1;
	                while($array=mysql_fetch_array($query)){
	                    $playerFirst[$playCount]=$array['firstName'];
	                    $playerLast[$playCount]=$array['lastName'];
	                    $playerEmail[$playCount]=$array['playerEmail'];
	                    $playerSex[$playCount]=$array['playerSex'];
	                    $playerMaleSelected[$playCount]='';
	                    $playerFemaleSelected[$playCount]='';
	                    $playerNoneSelected[$playCount]='';
	                    if($playerSex[$playCount]=='Male'){
	                        $playerMaleSelected[$playCount]='selected';
	                    }elseif($playerSex[$playCount]=='Female'){
	                        $playerFemaleSelected[$playCount]='selected';
	                    }else{
	                        $playerNoneSelected[$playCount]='selected';
	                    }
	                    $playCount+=1;
	                }
	
	                //The rest of the player spots are empty
	                for($s=$playCount; $s<=$people; $s++){
	                        $playerFirst[$s]='';
	                        $playerLast[$s]='';
	                        $playerEmail[$s]='';
	                        $playerSex[$s]='';
	                        $playerMaleSelected[$s]='';
	                        $playerFemaleSelected[$s]='';
	                        $playerNoneSelected[$s]='';
	                }// for (s)
	        }//end else
	
	//###################################################################################################################################################
	//                                               END OF "FRONT-END" FORM VARIABLE DECLARATIONS
	//###################################################################################################################################################
	
	        //--- CALLS THE FUNCTIONS BASED ON WHAT BUTTON WAS CLICKED ---\\
	
	        if (isset($_POST['register'])){register();} //if register button is pressed, execute register function
	        elseif(isset($_POST['save'])){save();}      //if save button is pressed, execute save function
	        elseif(isset($_POST['confirm'])){confirm();}//if confirm button is pressed, execute confirm function
	        elseif(isset($_POST['cSave'])){cSave();}    //if cSave button (confirming option to save) is pressed...
	        else{$footer=showFooter(0);}                //if no button was pressed, show the original form with bottom footer
	
	        //------------------------------------------------------------\\
	
	//###################################################################################################################################################
	//                                              END OF BACK-END FORM VARIABLE DECLARATIONS
	//###################################################################################################################################################
	
	
	
	
	//###################################################################################################################################################
	//                                          THE FORM - TITLE, TEAM, AND HEADER SECTION
	//###################################################################################################################################################
	
	        ?><FORM NAME='update' METHOD='POST' action='<?php print $_SERVER['PHP_SELF']?>?sport=<?php print $sport?>&&registrationID=<?php print $regID?>'>
				<INPUT TYPE='hidden' NAME='type' VALUE='<?php print $type ?>'>
				<INPUT TYPE='hidden' NAME='people' VALUE=<?php print $people ?>> 
	 			<INPUT TYPE='hidden' NAME='sport' VALUE='<?php print $sport ?>'>
	 			<INPUT TYPE='hidden' NAME='registrationID' VALUE='<?php print $regID?>'>      
	 
	        <?php $title=$titleHeader; //Used to show the title in the explorer
	        $teamNameFromDB=query("SELECT * FROM registration WHERE userName='".$user."' AND sport='".$sport."'");
	                
	        ?><FORM NAME='update' METHOD='POST' action= <?php print $_SERVER['PHP_SELF'] ?>?sport=<?php print $sport?>>
	        <INPUT TYPE='hidden' NAME='sport' VALUE='$sport'>
	        <INPUT TYPE='hidden' NAME='user' VALUE='$user'><?php
	
	        
	        //User name (logout) header and title for the browser
	        ?><head><title><?php print $title?></title>
	        <link rel="stylesheet" type="text/css" href="design.css"/>
	        </head>
	        <font face='arial' size=2>User: <B><?php print $user?></B>
	        <font size=1>(<a href=logout.PHP style='text-decoration: none;'>Logout</a>)</font>
	
	        
	        
	        
	        
	        
	        
	        
	        <TABLE class="master" ALIGN="CENTER">
	            <tr>
	                <td colspan=4 align=center>
	                    <TABLE class="logo" align="center">
	                        <tr align="center">
	                            <td align="center"><img src=<?php print $logo?>>
	                    </TABLE class="logo">
	            <tr>
	                <td colspan=4 align="center"><font size=4>
	                    <B><?php print $sportHeader?></B>
	                
	            
	            
	            
	            <?php
	//###################################################################################################################################################
	//                                          END OF TITLE, TEAM, AND HEADER SECTION
	//###################################################################################################################################################
	
	            
	            
	
	
	
	//###################################################################################################################################################
	//                                             THE FORM - CHOOSE LEAGUE AND TEAMNAME SECTION
	//###################################################################################################################################################
	
	        ?>
	        <tr>
	            <td colspan=5 align=center>
	                <font color=red><b>Registration due date: <?php print $registration_due; ?></b>
	            <?php
	            if ($error!=''){
	            ?>  
	                <tr>
	                    <td colspan=4 align="center">
	                        <font color="red"><?php print $error?></font>
	            <?php 
	            }
	            
	            if ($secondaryError!=''){?>
	                <tr>
	                    <td colspan=4 align="center">
	                        <font color="red"><?php print $secondaryError?></font>
	            <?php 
	            }
	
	            if ((isset($_POST['register']) or isset($_POST['confirm']) or isset ($_POST['save']) or (strlen($secondaryError)>1)) and $error==""){
	                //Print footer confirmation page if one of the buttons was pressed and there were no errors 
	                ?>
	                <tr>
	                    <td>
	                        <?php print $footer?>
	                <tr>
	                    <td>
	                        <BR>
	                        <?php 
	            }
	
	            
	            
	            
	            if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){
	                //If a button is not pressed or there is an error, show the form
	                
	                //###################################################################################################################################################
	                //                              THE FORM - LEAGUE/TEAM NAME SECTION
	                //###################################################################################################################################################
	                ?>
	                <tr BGCOLOR="#CCCCCC">
	
	                    <td colspan=4 align="center">
	                        <b>1. Select Your Division and Choose Team Name</b>
	                        <br>
	                        <font size=1>Please choose a division.</font>
	                <tr>
	                    <td colspan=4>
	                        
	                        
	                        <TABLE class="leagueTeamName" align=center>
	                            <tr BGCOLOR="white">
	                                <td align="center" colspan=2>
	                                    <B>Preferred League:</B>
	                                <td align="left" colspan=2>
	                                    <select name='league'><option value='nullVal'>League</option>
	                                    <?php $leagueDD="";
	                                    while($noticia2 = mysql_fetch_array($quer2)) {
	                                        if($noticia2['semi_identifier']==@$semi){
	                                                $leagueDD.= "<option value selected='$noticia2[semi_identifier]'>$noticia2[category]</option>"."<BR>";}//end if
	                                        else{
	                                                $leagueDD.= "<option value='$noticia2[semi_identifier]'>$noticia2[category]</option>";}//end else
	                                    }//end while
	                                    $leagueDD.= "</select>";
	                                    print $leagueDD."<BR>";?>
	                            <tr>
	                                <td align='center' colspan=2>
	                                    <b>Team Name: </b>
	                                <td colspan=2>
	                                    <input type='text' name='teamName' VALUE="<?php print htmlentities($teamName, ENT_QUOTES);?>" size=30>
	                        </TABLE>
	                        <INPUT TYPE="hidden" NAME="holderVariable" VALUE='<?php print htmlentities($semi, ENT_QUOTES);?>'>
	                        
	                <?php
	                //###################################################################################################################################################
	                //                              THE FORM - CAPTAIN INFORMATION SECTION
	                //###################################################################################################################################################
	                ?>
	                        
	                <TR BGCOLOR='#CCCCCC'>
	                    <TD COLSPAN=4 align='center'>
	                        <B>2. Captain Information</B>
	                        <BR>
	                        <FONT SIZE=1>
	                        The captain is the first person we'll contact with team inquiries and is responsible for submitting scores.
	                <tr>
	                    <td colspan=4>
	                        <TABLE class="playerTable" align=center cellspacing=10 cellpadding=1>
	                            <tr BGCOLOR='white'>
	                                <td align='center'>
	                                    <B>First Name:
	                                <td align='center'>
	                                    <B>Last Name:
	                                <td align='center'>
	                                    <B>Sex:
	                            <tr BGCOLOR="white">
	                                <td>
	                                    <INPUT TYPE="text" NAME="capFirst" VALUE="<?php print htmlentities($capFirst, ENT_QUOTES);?>" SIZE=40>
	                                <td>
	                                    <INPUT TYPE="text" NAME="capLast" VALUE="<?php print htmlentities($capLast, ENT_QUOTES);?>" SIZE=40>
	                                <td align=center>
	                                    <SELECT NAME="capSex">
	                                    <OPTION VALUE="">Select One</OPTION></Font>
	                                    <OPTION VALUE="Male" <?php print $capMaleSelected;?>>Male</OPTION></Font>
	                                    <OPTION VALUE="Female" <?php print $capFemaleSelected;?>>Female</OPTION></Font>
	                            <tr BGCOLOR="white">
	                                <td align="center">
	                                    <B>Email:
	                                    <td align="center">
	                                    <B>Phone Number:
	                            <tr BGCOLOR="white">
	                                <td>
	                                    <INPUT TYPE="text" NAME="capEmail" VALUE="<?php print htmlentities($capEmail, ENT_QUOTES);?>" SIZE=40>
	                                <td align="center">
	                                    <INPUT TYPE="text" NAME="capPhone" VALUE="<?php print htmlentities($capPhone, ENT_QUOTES);?>" SIZE=15>
	                        </table>
	
	                <?php
	                //###################################################################################################################################################
	                //                              THE FORM - PLAYER INFORMATION SECTION
	                //###################################################################################################################################################
	                ?>
	                        
	                <tr>
	                    <td colspan=4>
	                        <TABLE>
	                            <TR BGCOLOR='#CCCCCC'>
	                                <TD COLSPAN=5 align='center'>
	                                    <FONT FACE='verdana' SIZE=2><B>3. Player Information</B>
	                                    <BR>
	                                    <FONT SIZE=1><FONT COLOR='red'>*</FONT>
	                                    The first player listed should be the captain, the second will be an alternate contact if the captain is unavailable.</font>
	
	                            <tr BGCOLOR='white'>
	                                <TD>
	                                <td align='center'>
	                                    <FONT FACE='verdana' SIZE='2'><B>First Name:
	                                <td align='center'>
	                                    <FONT FACE='verdana' SIZE='2'><B>Last Name:
	                                <td align='center'>
	                                    <FONT FACE='verdana' SIZE='2'><B>Email:
	                                <td align='center'>
	                                    <FONT FACE='verdana' SIZE='2'><B>Sex:
	                
	                            <?php 
	                            for($v=1; $v<=$people; $v++){?>
	                                <tr BGCOLOR="white">
	                                    <td>
	                                        <?php 
	                                        if($v<=2){
	                                            print $v.'.'?>
	                                            <FONT COLOR="red">*</FONT>
	                                        <?php 
	                                        }//adds the asterisk
	                                        else{
	                                            print $v.'.';
	                                        }?>
	                                    <td>
	                                        <INPUT TYPE="text" NAME="playerFirst<?php print $v?>" VALUE="<?php print htmlentities($playerFirst[$v], ENT_QUOTES);?>" SIZE=27>
	                                    <td>
	                                        <INPUT TYPE="text" NAME="playerLast<?php print $v?>" VALUE="<?php print htmlentities($playerLast[$v], ENT_QUOTES);?>" SIZE=30>
	                                    <td>
	                                        <INPUT TYPE="text" NAME="playerEmail<?php print $v?>" VALUE="<?php print htmlentities($playerEmail[$v], ENT_QUOTES);?>" SIZE=30>
	                                    <td align=center>
	                                        <SELECT NAME="playerSex<?php print $v?>"><FONT FACE = "verdana" SIZE=2>
	                                        <OPTION VALUE="" <?php print $playerNoneSelected[$v]?>>Select One</OPTION></Font>
	                                        <FONT FACE="verdana" SIZE=2><OPTION VALUE="Male" <?php print $playerMaleSelected[$v]?>>Male</OPTION></Font>
	                                        <FONT FACE="verdana" SIZE=2><OPTION VALUE="Female" <?php print $playerFemaleSelected[$v]?>>Female</OPTION></Font>
	                            <?php 
	                            }//END FOR
	                            ?>
	                        </TABLE>
	                    
	                    <?php
	                //###################################################################################################################################################
	                //                                                       THE FORM - COMMENT BOX AND ACTION BUTTONS
	                //###################################################################################################################################################
	                ?>
	                <TR BGCOLOR='#CCCCCC'>
	                    <TD COLSPAN=4 align='center'>
	                        <B>4. Comments</B><BR>
	                        <FONT SIZE=1>Comments, notes, player needs, etc. (Limit 1000 characters)</font>
	                <TR>
	                    <TD colspan=4 align='center'>
	                        <?php print "<TEXTAREA NAME='comments' COLS= 80 ROWS=6>".$comments."</TEXTAREA>";
	            }else{
	                //A BUTTON WAS PRESSED AND THERE WERE NO ERRORS, STORE ALL FORM INFORMATION IN HIDDEN VARIABLES
	                ?>
	                <INPUT TYPE="hidden" NAME="league" VALUE='<?php print htmlentities($semi, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='teamName' VALUE='<?php print htmlentities($teamName, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='capFirst' VALUE='<?php print htmlentities($capFirst, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='capLast' VALUE='<?php print htmlentities($capLast, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='capSex' VALUE='<?php print htmlentities($capSex, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='capEmail' VALUE='<?php print htmlentities($capEmail, ENT_QUOTES);?>'>
	                <INPUT TYPE='hidden' NAME='capPhone' VALUE='<?php print htmlentities($capPhone, ENT_QUOTES);?>'>
	                
	                <?php 
	                for($u=1; $u<=$people; $u++){ 
	                ?>
	                    <INPUT TYPE='hidden' NAME="playerFirst<?php print $u?>" VALUE='<?php print htmlentities($playerFirst[$u], ENT_QUOTES);?>'>
	                    <INPUT TYPE='hidden' NAME="playerLast<?php print $u?>" VALUE='<?php print htmlentities($playerLast[$u], ENT_QUOTES);?>'>
	                    <INPUT TYPE='hidden' NAME="playerSex<?php print $u?>" VALUE='<?php print htmlentities($playerSex[$u], ENT_QUOTES);?>'>
	                    <INPUT TYPE='hidden' NAME="playerEmail<?php print $u?>" VALUE='<?php print htmlentities($playerEmail[$u], ENT_QUOTES);?>'>
	                <?php 
	                }
	                ?>
	                <INPUT TYPE='hidden' NAME='comments' VALUE='<?php print htmlentities($comments, ENT_QUOTES);?>'>
	                <?php
	            }
	            
	        if(($error!="")||(!isset($_POST['register']) and !isset($_POST['save']) and !isset($_POST['confirm']))){
	                print $footer;
	        }?>
	        </table>
	    </form>
	</html>
	
<?php ob_flush();?>
