<?php

session_start ();
include ('connect.PHP');    //Connection code for the mysql database
include ('security.PHP');   //Security code to ensure only logged in users can access this page


//Gets whatever argument you pass into the program for sport ("?sport=beach" etc)
    if(isset($_GET['sport'])){
        $sport = $_GET['sport'];
    }else{
        $sport = 'unknown';
    }

//Saves the variable as a hidden variable for use later on if necessary (ie if page needs to reload b/c of error)
?><INPUT TYPE='hidden' NAME='sport' VALUE=$sport><?php

//Gets whatever argument you pass into the form for registration id (will be automatically set in the members page)
    if(isset($_GET['registrationID'])){
        $regID = $_GET['registrationID'];
    }else{
        $regID = 'unknown';
    }

?><INPUT TYPE='hidden' NAME='registrationID' VALUE='".$regID."'><?


//###################################################################################################################################################
//                                             ADDED SECURITY FOR URL HACKERS
//###################################################################################################################################################

//These lines of code will stop people from changing the variables passed in the URL
//and essentially "hacking" the database to see other teams' information
//If the sport in the URL doesn't match the sport assigned to the regID or if
//the regID doesn't match the user (ie they didn't create it) kick them out!
//Example: if the url is www.perpetualmotion.org/Registration/update.PHP?sport=beach&regID=100 and they try
//to change the regID to 200 (ie to see another teams' info) it will block them!

$databaseArray=query("SELECT sport, userName FROM registration WHERE registrationID=$regID");
$databaseSportName=$databaseArray['sport']; 
$databaseUserName=$databaseArray['userName']; 

if(($user!=$databaseUserName) OR ($sport!=$databaseSportName)){
     header ("Location: securityWarning.htm");
     //Log them out, give the warning page (why it happened)
}

//###################################################################################################################################################
//                                         END OF ADDED SECURITY FOR URL HACKERS
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
    global $regID, $error, $footer, $teamNm, $tmNm;
    print "Old Name: ".$teamNm;
    print "<BR>NEW NAME: ".$tmNm; 
    $errorCheck=errorCheck();
    $array=query("SELECT * FROM registration WHERE registrationID=$regID");
    $confirmed=$array['registered'];
    if($errorCheck!=""){//There was an error somewhere, print all errors and show the basic footer
        $footer=showFooter(0);
    }else{
        $error=""; //No Errors, check if the league is the same, if so save details, if not show the confirmation/fee footer box
        if((leagueSame()) and $confirmed==1){
            prepareSave(1);
            if($comments!=""){
                mailForm($regID, 1);
            }
            header("Location: thankYouUpdate.htm");  //Show the "Thank you for updating" page
        }else{
            $footer=showFooter(1); //Show the confirmation page, the league isn't the same so it'll be treated as a new registration
        }
    }
}//end function


//The backend code for when the save button is pressed
function save(){
        global $error, $comments, $regID;
        $queryConfirmed=mysql_query("SELECT * FROM registration WHERE registrationID=$regID");
        $arrayConfirmed=mysql_fetch_array($queryConfirmed);
        $confirmed=$arrayConfirmed['registered'];
        $errorCheck="";
        $errorCheck=errorCheck();
        if($errorCheck!=""){$error=$errorCheck; 
                global $footer;
                $footer=showFooter(0);
        }else{ $error="";

                if(leagueSame()){
                        if($confirmed==0){
                                prepareSave(0); //There were no errors, execute the prepareSave() function which replaces database data with new data from the form
                        }else{
                                prepareSave(1);
                        }
                        if($comments!=""){   //Email the user and convenor with an email indicating that a comment was added
                            mailForm($regID, 1);
                        }  
                        header("Location: thankYouUpdate.htm");  //Show the "Thank you for updating" page
                }else{
                    $footer=showFooter(1); //Show the confirmation page, they chose a different league so it's treated as a new registration
                }
        }
}//end function

//The backend code for when the confirm button is pressed (different then register!!)
function confirm(){
global $secondaryError; // Used if there's an error on the 'confirm' page
$errorCheck="";
$errorCheck=errorCheck();
if(($errorCheck!="")){ //There was an error somewhere, set error so it'll print the error, show basic footer
$secondaryError=$errorCheck;
global $footer;
$footer=showFooter(1);
}
else{
$error="";
$tester=prepareRegister();     //Execute the prepare function to insert data into the database, creates a new team record for the new league
prepConfirm(); //Execute the prepConfirm function which inserts the "payment" data into the db
mailForm($regID, 0);//Execute the mailConvenor function which emails the convenor a confirmation email
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

        global $cFirst, $cLast, $cEmail, $cPhone, $cSex, $semi, $teamName, $method;

        $err=""; //Set err = nothing
        if(!isset($_POST['confirm'])){
                if($cFirst==""){  $err.="Please enter the captain's first name.<BR>";}        //if no captain first name
                if($cLast==""){   $err.="Please enter the captain's last name.<BR>";}         //if no captain last name
                if($cEmail==""){  $err.="Please enter the captain's email address.<BR>";}     //if no captain email
                if($cPhone==""){  $err.="Please enter the captain's phone number.<BR>";}      //if no captain phone
                if($cSex==""){      $err.="Please select the captain's sex.<BR>";}            //if no sex selected
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




?><FORM NAME='update' METHOD='POST' action='<?php print $_SERVER['PHP_SELF']?>?sport=<?php print $sport?>&&registrationID=<?php print $regID?>'><?php

                                      
//VARIABLE DECLARATIONS (BASED ON WHAT SPORT WAS CHOSEN) OCCUR IN THIS FILE
//NOTE: This is where the logo is defined, as well
include 'declarations.PHP';


///////// Getting the data from Mysql table for the league dropdown, uses $fiter defined above//////////
$quer2=mysql_query("SELECT DISTINCT category, cat_id, semi_identifier FROM regcategory ".$filter." and available =1 order by cat_id");






                                                                                    

//###################################################################################################################################################
//                                                 START OF EMAIL FUNCTIONS
//###################################################################################################################################################

//Function to create the body of the email for both captain and player
function body($regID){
    global $cFirst, $cLast, $cSex, $cEmail, $cPhone, $comments, $teamName, $people;
    global $playerFName, $playerLName, $playerEmail, $playerSex;

    $capFirst=htmlentities($cFirst, ENT_QUOTES);
    $capLast=htmlentities($cLast, ENT_QUOTES);
    $capPhone=htmlentities($cPhone, ENT_QUOTES);
    $phoneNumber=formatPhoneNumber($capPhone);
    $capEmail=htmlentities($cEmail, ENT_QUOTES);
    $capSex=htmlentities($cSex, ENT_QUOTES);
    $comment=htmlentities($comments, ENT_QUOTES);
    $tmNm=htmlentities($teamName, ENT_QUOTES);
    $date=date('r');
    for($b=1; $b<=$people; $b++){
        $first[$b]=htmlentities($playerFName[$b], ENT_QUOTES);
        $last[$b]=htmlentities($playerLName[$b], ENT_QUOTES);
        $email[$b]=htmlentities($playerEmail[$b], ENT_QUOTES);
        $sex[$b]=htmlentities($playerSex[$b], ENT_QUOTES);
    }
    $body="";
    $body.="<TR><TD colspan=4 align=center>-----------------Captain's Information-------------------";
    $body.="<TR><TD><B>First Name:</B><td><b>Last Name:</b><td><td><b>Sex:</b>";
    $body.="<tr><TD>".$capFirst."<td>".$capLast."<td><td>".$capSex;
    $body.="<tr><TD colspan=2><B>Email:</B><td colspan=2><b>Phone Number:</b>";
    $body.="<tr><TD colspan=2>".$capEmail."<td colspan=2>".$phoneNumber;
    $body.="<tr><td><BR>";
    $body.="<tr><td colspan=4 align=center>-----------------Player Information-------------------";
    $body.="<tr><td><b>First Name:</b><td><b>Last Name:</b><td><b>Email:</b><td><b>Sex:</b>";

    for($c=1; $c<=$people; $c++){
        if(strLen($playerFName[$c])>=1){
            $body.="<tr><td>".$c.". ".$playerFName[$c]."<td>".$playerLName[$c]."<td>".$playerEmail[$c]."<td>".$playerSex[$c];
        }//end if
    }//end for

    $body.="<tr><td colspan=4 align=center>------------------------------------------------<td>";
    $body.="<tr><td><B>Comments:</B>";
    $body.="<tr><td colspan=4>".$comments;
    $body.="<tr><td><BR>";
    $body.="<tr><td>Received:<td>".$date."</TABLE>";
    return $body;
}//end function

//Function to email both dave (convenor) and the captain
function mailForm($regID, $comSub){
    global $league, $type, $title, $secondary, $teamLine, $body, $cEmail, $league;
    
    $title="<font size=3><TABLE align=center cellspacing=2 cellpadding=2>";
    $title.="<tr><td colspan=3 align=center><B>Perpetual Motion's Online Registration System";
    $title.="<BR>-- Registration Confirmation --</B>";
    $secondary="<BR>Existing Team Updated and Registered for <br><B>".$type." - ".$league.".</B>";
    
    $body=body($regID);
    
    $teamLine="<TR><td align=center colspan=4><b>Team Name:</b> ".$teamName;
    $regIDLine="<TR><td align=center colspan=4><b>Team Name:</b> ".$teamName;
    $regIDLine.="<tr><td colspan=4 align=center>Team Number: ".$regID;
    
    $message=$title.$secondary.$teamLine.$body;
    $altMessage=$title.$secondary.$regIDLine.$body;
    
    $to=$cEmail;
    
    $subject="Registration Confirmation - ".$teamName;
    $altSubject="Reg - ".$teamName." - ".$league;
    
    if($comSub==1){
        $subject="Comment Added - ".$teamName;
        $altSubject="Com - ".$teamName." - ".$league;
    }
    
    $from_head  = 'MIME-Version: 1.0' . "\r\n";
    $from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $from_head .= 'From: info@perpetualmotion.org';
    
    mail($to, $subject, $message, $from_head);
    mail('dave@perpetualmotion.org', $altSubject, $altMessage, $from_head);
    mail('darryl.moyers@hotmail.com', $altSubject, $altMessage, $from_head);

}//end function

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

        global $teamName, $cFirst, $cLast, $league, $fee;

        if($state==1){

                $teamNm=htmlentities($teamName, ENT_QUOTES);
                $capFirst=htmlentities($cFirst, ENT_QUOTES);
                $capLast=htmlentities($cLast, ENT_QUOTES);
                $leagueName=htmlentities($league, ENT_QUOTES);

                $foot= "<BR><TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>Confirm Fees</B><BR>";
                $foot.= "<FONT SIZE=1>";
                $foot.= "<FONT COLOR='red'>**The registration process is not finalized until fees have been paid**</font>";
                $foot.= "<tr><td><Font size=2>Team Name: <B>".$teamNm."</B><td align='right'><Font size=2>";
                $foot.= "Captain: <B>".$capFirst." ".$capLast."</B>";
                $foot.= "<tr><td><FONT FACE='VERDANA' SIZE='2'>Preferred league: <B>".$leagueName."</B>";
                $foot.= "<TD align='right'><FONT FACE='VERDANA' SIZE='2'>Registration Fee: <B>$".$fee."</B>";
                $foot.= "<tr><TD><FONT FACE='VERDANA' SIZE='2'>Payment Method: <td colspan=2><select name='method'>";
                $foot.= "<option value=''>Choose Payment Method</option>";
                $foot.= "<option value='Team will mail fees to the office'>I will mail cheque to Perpetual Motion's home office</option>";
                $foot.= "<option value='Team will bring fees to registration night'>I will bring cash/cheque to registration night</option>";
                $foot.= "<option value='Team will bring fees To the office'>I will bring cash/cheque to Perpetual Motion's home office</option>";
                $foot.= "</select>";
                $foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='confirm' Value='Confirm Registration Submission'>";
                $foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
                $foot.= "<tr><td><br>";
                $foot.= "<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b>";
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
                $foot.= "<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b>";
        }
        return $foot;
}//end function

//###################################################################################################################################################
//                                                  END OF FOOTER FUNCTION
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
    global  $regID, $league, $teamName, $semi, $cFirst, $cLast, $cSex, $cEmail, $cPhone, $method;
    global $playerFName, $playerLName, $playerSex, $playerEmail;

    $methodA=query("SELECT method FROM registration WHERE registrationID=".$regID);
    $method=$methodA['method']; 

    //By deleting previous records, it's a cheaters way of updating the fields
    //Insted of using update, I just delete what was there and copy over it with all of the newly obtained data
    $remove="DELETE FROM players WHERE registrationID='".$regID."'";
    $deletePlayer=mysql_query($remove);

    $remove="DELETE FROM captains WHERE registrationID='".$regID."'";
    $deleteCaptain=mysql_query($remove);

    $remove="DELETE FROM registration WHERE registrationID='".$regID."'";
    $deleteReg=mysql_query($remove);


    //ADDS SLASHES BEFORE SAVING TO DATABASE
    $leagueNm=addSlashes($league);
    $semiID=$semi;
    $teamNm=addSlashes($teamName);

    $capFirst=addSlashes($cFirst);
    $capLast=addSlashes($cLast);
    $capSex=addSlashes($cSex);

    $capEmail=addSlashes($cEmail);
    $capPhone=preg_replace("/\D/",'',$cPhone); //removes any non-digits (brackets, dashes, dots, etc)


    //REGISTRATIONID NUM -- A Primary Key (Each number only used once, is 1 higher than the highest regID in the table...)

    if(strlen($leagueNm)<=5){
         header("Location: RegistrationError.htm");
    }

    $insert="INSERT INTO registration (registrationID, teamName, sport, league, semi_identifier, userName, cFirst, cLast, method, registered) VALUES
    (".$regID.", '".$teamNm."', '".$sport."', '".$leagueNm."', '".$semiID."', '".$user."', '".$capFirst."
    ', '".$capLast."', '".$method."', $boolRegistered)";
    $addRegistration=mysql_query($insert) or die("The insert into registration query threw an error ".mysql_error());


    $insert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES
    (".$regID.", '".$teamName."', '".$capFirst."', '".$capLast."'
    , '".$capEmail."', '".$capPhone."', '".$capSex."')";
    $addCaptain=mysql_query($insert) or die("The insert into captains query threw an error ".mysql_error());


    for ($e=1; $e<=$people; $e++){
        $fName[$e]=addSlashes($playerFName[$e]);
        $lName[$e]=addSlashes($playerLName[$e]);
        $email[$e]=addSlashes($playerEmail[$e]);
        $sex[$e]=addSlashes($playerSex[$e]);
        if((strlen($fName[$e])>=1)){
            $insert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES
            (".$regID.", '".$teamNm."', '".$fName[$e]."', '".$lName[$e]."',
            '".$email[$e]."', '".$sex[$e]."')";
            $addPlayer=mysql_query($insert) or die("The insert into players query threw an error ".mysql_error());
        }
    }//end for

}//end function

function prepareRegister(){     // Function that adds a new team record if the team registers for a new league
    global $regID, $cFirst, $cLast, $cEmail, $cSex, $cPhone, $league, $semi;
    global $playerEmail, $playerFName, $playerLName, $playerSex;

    //ADDS SLASHES BEFORE SAVING TO DATABASE
    $leagueNm=addSlashes($league);
    $teamNm=addSlashes($teamName);
    $capFirst=addSlashes($cFirst);
    $capLast=addSlashes($cLast);
    $capSex=addSlashes($cSex);
    $capEmail=addSlashes($cEmail);
    $capPhone=preg_replace("/\D/",'',$cPhone); //remove all non-digits (dashes, dots, brackets, etc)
    $semiID=$semi;


    $selectAll=mysql_query("SELECT MAX(registrationID) as maxnum FROM captains");
    $regArr=mysql_fetch_array($selectAll);
    $maximum=$regArr['maxnum'];


    global $regID;
    $regID=$maximum+1;
    ?><INPUT TYPE='hidden' NAME='reg' VALUE=$regID><?php



    if(strlen($leagueNm)<=5){
         header("Location: RegistrationError.htm");
    }

    //REGISTRATIONID NUM -- A Primary Key (Each number only used once, is 1 higher than the highest regID in the table...)
    $insert="INSERT INTO registration (registrationID, teamName, sport, league, semi_identifier, userName, cFirst, cLast, method, registered) VALUES
    (".$regID.", '".$teamNm."', '".$sport."', '".$leagueNm."', '".$semiID."', '".$user."', '".$capFirst."
    ', '".$capLast."', '', 1)";
    $addRegistration=mysql_query($insert) or die("The insert into registration query threw an error ".mysql_error());



    $insert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES
    (".$regID.", '".$teamName."', '".$capFirst."', '".$capLast."'
    , '".$capEmail."', '".$capPhone."', '".$capSex."')";
    $addCaptain=mysql_query($insert) or die("The insert into captains query threw an error ".mysql_error());



    for ($e=1; $e<=$people; $e++){
        $fName[$e]=addSlashes($playerFName[$e]);
        $lName[$e]=addSlashes($playerLName[$e]);
        $email[$e]=addSlashes($playerEmail[$e]);
        $sex[$e]=addSlashes($playerSex[$e]);
        if((strlen($fName[$e])>=1)){
            $insert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES
            (".$regID.", '".$teamNm."', '".$fName[$e]."', '".$lName[$e]."',
            '".$email[$e]."', '".$sex[$e]."')";
            $addPlayer=mysql_query($insert) or die("The insert into players query threw an error ".mysql_error());
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
//                                                  "FRONT-END" FORM VARIABLE DECLARATIONS
//###################################################################################################################################################

// These are the variables used when reshowing the form to the user
// They have to be different than the "Back-end" or "raw" data which is formatted differently to go into the database

// ** Example: **
// Real Team name: "Can't Score Won't Score" 
// FRONT END VARIABLE would hold: Can't Score Won't Score (no difference)
// Back End Variable would hold: Can/'t Score Won/'t Score (slashes)  

if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm'])){
    //If one of the buttons is pressed, the form is refilled using data from the form
    //htmlentities is just a standardized function to make sure it appears as it should (without slashes etc)
                
                $teamNm=htmlentities($teamName,ENT_QUOTES);
                
                //Captain variables
                $capFirst=htmlentities($cFirst, ENT_QUOTES);
                $capLast=htmlentities($cLast, ENT_QUOTES);
                $capEmail=htmlentities($cEmail, ENT_QUOTES);
                $capPhone=htmlentities($cPhone, ENT_QUOTES);
                $capSex=htmlentities($cSex, ENT_QUOTES);
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
                        $playerFName[$r]=htmlentities($playerFName[$r], ENT_QUOTES);
                        $playerLName[$r]=htmlentities($playerLName[$r], ENT_QUOTES);
                        $playerEmail[$r]=htmlentities($playerEmail[$r], ENT_QUOTES);
                        $playerSex[$r]=htmlentities($playerSex[$r], ENT_QUOTES);
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
                $comment=htmlentities($comments, ENT_QUOTES);       
        }else{ 
            //No buttons were pressed, therefore this is the initial (old data) version of the form
            //Populate captain info from the captain database
             
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
                
                $teamNm=$capArray['teamName'];
                
                
                //Player Variables
                $query=mysql_query("SELECT * FROM players WHERE registrationID=".$regID);
                $playCount=1;
                while($array=mysql_fetch_array($query)){
                    $playerFName[$playCount]=$array['firstName'];
                    $playerLName[$playCount]=$array['lastName'];
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
                        $playerFName[$s]='';
                        $playerLName[$s]='';
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
                                                

//###################################################################################################################################################
//                                                  "BACK-END" FORM VARIABLE DECLARATIONS
//###################################################################################################################################################

// These are the variables used to hold data that will eventually get input into the database
// They have to be different than the "Front-end" or "real" data which is formatted to appear regularly when reshowing form info to the user

// ** Example: **
// Real Team name: "Can't Score Won't Score" 
// Front End Variable would hold: Can't Score Won't Score (no difference)
// BACK END VARIABLE would hold: Can/'t Score Won/'t Score (slashes)  
                                                
                                                
if ((isset($_POST['register']))or(isset($_POST['confirm']))or(isset($_POST['save']))or(isset($_POST['cSave']))or((strlen($secondaryError))>1)){
        //If a button was pressed, populate the variables based on what was in the form.


        //---League Information (cat_id, league name, and price of the league)---\\
        if (isset($_POST['league'])){
                $semi=$_POST['league'];
                $leagueArray=query("SELECT * FROM regcategory WHERE semi_identifier='".$semi."'");
                $league=$leagueArray['category'];
                $semiIdentifier=$semi;
                $fee=$leagueArray['fee'];
        }else{
                $semiArray=query("SELECT * FROM registration WHERE registrationID=".$regID);
                $semi=$semiArray['semi_identifier'];
                $league=$semiArray['league'];
                $semiIdentifier=$semi;
        }
        //------------------------------------------------------------------------\\


        //---Captain's Information (First/Last Name, Email, Phone Number, Sex)---\\
                $cFirst=$_POST['cFirst'];
                $cLast=$_POST['cLast'];
                $cEmail=$_POST['cEmail'];
                $cPhone=$_POST['cPhone'];
                $cSex=$_POST['cSex'];
        //-----------------------------------------------------------------------\\

        //-----------Player's Information (First/Last Name, Email, Sex)----------\\
                
        //For loop to store the data in an array
        for($a=1; $a<=$people; $a++){
                $playerFName[$a]=$_POST['firstName'.$a];
                $playerLName[$a]=$_POST['lastName'.$a];
                $playerEmail[$a]=$_POST['email'.$a];
                $playerSex[$a]=$_POST['sex'.$a];
        }
        //-----------------------------------------------------------------------\\

        //--- Various Variables (comments, teamName) ---\\
                $comments=$_POST['comments'];
                $teamName=$_POST['teamName'];
        //-----------------------------------------------\\

        //--- Method Variable (The only that isn't checked EVERY time) ---\\
        if(isset($_POST['confirm']) or isset($_POST['cSave'])){
                $method=$_POST['method'];
        }
        //-----------------------------------------------------------------\\
}//end setting variables


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
//                                                   THE FORM - FUNCTIONS
//###################################################################################################################################################

    //Function to populate the previous teams list
    //How it works:
    //Queries database
    //Loops through query pulling out teams registered to the user
    
    function createPreviousTeamsList(){
        global $user, $sport;
            
        $query=mysql_fetch_array("SELECT * FROM registration WHERE userName='".$user."' AND sport='".$sport."'");
        $list="";
            
        while ($array=mysql_fetch_array($query)){
            $teamNameFromDB=stripslashes($array['teamName']);
            $leagueFromDB=stripslashes($array['league']);
            $regIDFromDB=$array['registrationID'];
            $sportFromDB=$array['sport'];
                
            $list.="
                    <tr>
                        <td>
                            <a href='update.PHP?sport=".$sportFromDB."&&registrationID=".$regIDFromDB."'>".$teamNameFromDB."</a>
                        <td>
                            <font size=2>".$leagueFromDB;
        }
        return $list;
    }// End function
    
    
    //Function to populate the dropdown list for league choices
    //How it works:
    //Queries database
    //Loops through query pulling out possible leagues based on the chosen sport
    
    function populateDropDown(){
        $leagueDD="";
        while($noticia2 = mysql_fetch_array($quer2)) {
            if($noticia2['semi_identifier']==@$semi){
                $leagueDD.= "<option value selected='$noticia2[semi_identifier]'>$noticia2[category]</option>"."<BR>";}//end if
            else{
                $leagueDD.= "<option value='$noticia2[semi_identifier]'>$noticia2[category]</option>";
            }//end else
        }//end while
        $leagueDD.= "</select>";
        return $leagueDD;
    }

//###################################################################################################################################################
//                                                   END OF FORM FUNCTIONS
//###################################################################################################################################################






//###################################################################################################################################################
//                                          THE FORM - TITLE, TEAM, AND HEADER SECTION
//###################################################################################################################################################

        
        
 
        $title=$titleHeader; //Used to show the title in the explorer
        $teamNameFromDB=query("SELECT * FROM registration WHERE userName='".$user."' AND sport='".$sport."'")['teamName'];
                
        ?><FORM NAME='update' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']?>?sport=<?php print $sport?>>
        <INPUT TYPE='hidden' NAME='sport' VALUE='$sport'>
        <INPUT TYPE='hidden' NAME='user' VALUE='$user'><?php

        
        //User name (logout) header and title for the browser
        ?><head><title><?php print $title?></title> 
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
                <td colspan=4 align="center">
                    <B><?php print $sportHeader?></B>
                
            
            
            
            <?php
//###################################################################################################################################################
//                                          END OF TITLE, TEAM, AND HEADER SECTION
//###################################################################################################################################################

            
            
//###################################################################################################################################################
//                                                 PREVIOUS TEAMS LIST
//###################################################################################################################################################

         
         // IF THERE'S ALREADY A TEAM REGISTERED BY THE USER IN THE DATABASE, OFFER THE OPTION TO UPDATE THAT REGISTRATION INSTEAD
        if(strlen($teamNameFromDB)>=1 and (!isset($_POST['register']))){ 
        ?>
            <tr>
                <td colspan=3 align=center>
                    <TABLE class="previousTeams" align=center>
                        <tr>
                            <td colspan=5 align=center>
                                <BR>
                                <font color=red><B><font size=3>Do you want to register one of these previous teams for <? print $seas_name?>?
                                <hr>                                
                        <tr>                            
                            <td align=center>
                                <b>Team Name</b>
                            <td align=center>
                                <b>League</b>
                        
                        <?php  //Calls function to populate the previous teams list, see above
                        createPreviousTeamsList(); 
                        ?>
                
                        <tr>
                            <td>
                                <BR>
                   </table>
                
        <?php 
        }// end if strlen          
//###################################################################################################################################################
//                                                     END OF PREVIOUS TEAMS LIST
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
                                    <select name='league'><option value=''>League</option>
                                    <?php 
                                    $leagueDD=populateDropDown();
                                    print $leagueDD."<BR>";
                                    ?>
                            <tr>
                                <td align='center' colspan=2>
                                    <b>Team Name: </b>
                                <td colspan=2>
                                    <input type='text' name='teamName' VALUE="<?php print $teamNm;?>" size=30>
                        </TABLE>
                        
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
                                    <INPUT TYPE="text" NAME="cFirst" VALUE="<?php print $capFirst;?>" SIZE=40>
                                <td>
                                    <INPUT TYPE="text" NAME="cLast" VALUE="<?php print $capLast;?>" SIZE=40>
                                <td align=center>
                                    <SELECT NAME="cSex">
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
                                    <INPUT TYPE="text" NAME="cEmail" VALUE="<?php print $capEmail;?>" SIZE=40>
                                <td align="center">
                                    <INPUT TYPE="text" NAME="cPhone" VALUE="<?php print $capPhone;?>" SIZE=15>
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
                                        <INPUT TYPE="text" NAME="firstName<?php print $v?>" VALUE="<?php print $playerFName[$v];?>" SIZE=27>
                                    <td>
                                        <INPUT TYPE="text" NAME="lastName<?php print $v?>" VALUE="<?php print $playerLName[$v];?>" SIZE=30>
                                    <td>
                                        <INPUT TYPE="text" NAME="email<?php print $v?>" VALUE="<?php print $playerEmail[$v];?>" SIZE=30>
                                    <td align=center>
                                        <SELECT NAME="sex<?php print $v?>"><FONT FACE = "verdana" SIZE=2>
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
                        <FONT SIZE=1>Comments, notes, player needs, etc.</font>
                <TR>
                    <TD colspan=4 align='center'>
                        <TEXTAREA NAME='comments' COLS= 80 ROWS=6>
                            <?php print $comment;?>
                        </TEXTAREA>
                
                
            <?php 
            }else{
                //A BUTTON WAS PRESSED AND THERE WERE NO ERRORS, STORE ALL FORM INFORMATION IN HIDDEN VARIABLES
                ?>
                <INPUT TYPE="hidden" NAME="league" VALUE='<?php print $semi?>'>
                <INPUT TYPE='hidden' NAME='teamName' VALUE='<?php print $teamNm?>'>
                <INPUT TYPE='hidden' NAME='cFirst' VALUE='<?php print $capFirst?>'>
                <INPUT TYPE='hidden' NAME='cLast' VALUE='<?php print $capLast?>'>
                <INPUT TYPE='hidden' NAME='cSex' VALUE='<?php print $capSex?>'>
                <INPUT TYPE='hidden' NAME='cEmail' VALUE='<?php print $capEmail?>'>
                <INPUT TYPE='hidden' NAME='cPhone' VALUE='<?php print $capPhone?>'>
                
                <?php 
                for($u=1; $u<=$people; $u++){ 
                ?>
                    <INPUT TYPE='hidden' NAME="firstName<?php print $u?>" VALUE='<?php print $playerFName[$u]?>'>
                    <INPUT TYPE='hidden' NAME="lastName<?php print $u?>" VALUE='<?php print $playerLName[$u]?>'>
                    <INPUT TYPE='hidden' NAME="sex<?php print $u?>" VALUE='<?php print $playerSex[$u]?>'>
                    <INPUT TYPE='hidden' NAME="email<?php print $u?>" VALUE='<?php print $playerEmail[$u]?>'>
                <?php 
                }
                ?>
                <INPUT TYPE='hidden' NAME='comments' VALUE='<?php print $comment?>'>
                <?php
            }
            
        if(($error!="")||(!isset($_POST['register']) and !isset($_POST['save']) and !isset($_POST['confirm']))){
                print $footer;
        }?>
        </table>
    </form>
</html>