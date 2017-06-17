<?php 
session_start();
ob_start();
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'security.php');

date_default_timezone_set('America/New_York');

//Gets whatever argument you pass into the program for sport ("?sport=beach" etc)
if(isset($_GET['sport'])){
    $sport = $_GET['sport'];
} else {
    $sport ='unknown';
}

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'connect.php');
require_once('includeFunctions/mailFunctions.php');
require_once('includeFunctions/sqlFunctions.php');
require_once('includeFunctions/declarations.php');
require_once('includeFunctions/formFunction.php');

//Magic quotes by default is on, this function takes out all backslashes in the superglobal variables
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
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

//##############################################################################################################################################
//                                         START OF 'BACKEND CODE' FUNCTIONS FOR EACH BUTTON
//##############################################################################################################################################

///This function controls what happens when the register button is pushed on the initial signup page. Note that the register button is different
//than the confirm button on the secondary page where they confirm that they know how much it'll cost etc.
//How it works:
//- Runs an error check on what's entered when the user hits register
//- If there are errors, it reloads the form highlighting the errors in red
//- Keeps doing this until the error check passes, at which point it shows the confirmation screen
function register(){
	global $error, $footer;
	$errorCheck=errorCheck();

	if($errorCheck!=""){
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
		$regID=prepare(0); //Save the data
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
        global $secondaryError, $footer;
        $errorCheck=errorCheck();
        if($errorCheck!=""){
                $secondaryError=$errorCheck;
                $footer=showFooter(1); //Reload confirmation page, show errors
        }else{
                $error='';
                $regID=prepare(1);     //Save the data in the database as registered
                prepConfirm($regID);   //Save payment data in the database (will pay in person, etc)
                mailForm($regID, 0);   //Send confirmation emails to the convenor and the user who registered
				mailWaivers();
                header("Location: thankyoureg.htm");  //show the thank you for registering page
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

        global $capFirst, $capLast, $capEmail, $capPhone, $capSex, $semi, $teamName, $method;

        $err=""; //Set err = nothing
        if(!isset($_POST['confirm'])){
                if($capFirst=="") $err.="Please enter the captain's first name.<BR>";        //if no captain first name
                if($capLast=="")  $err.="Please enter the captain's last name.<BR>";         //if no captain last name
                if($capEmail=="") $err.="Please enter the captain's email address.<BR>";     //if no captain email
                if($capPhone=="") $err.="Please enter the captain's phone number.<BR>";      //if no captain phone
                if($capSex=="")   $err.="Please select the captain's sex.<BR>";            //if no sex selected
                if($semi=="")     $err.="Please select a league/division.<BR>";              //if no league selected
                if($teamName=="") $err.="Please enter a teamname.<BR>";                    //if no team name is entered
        } else {
                if($method=="")	  $err.="Please select your method of payment.<BR>";           //if no payment method is selected
        }
        return $err;
}//end function

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

		$foot= "<BR><TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><font face='verdana' SIZE=2><B>Confirm Fees</B><BR>";
		$foot.= "<font face='verdana' SIZE=1>";
		$foot.= "<font face='verdana' COLOR='red'>**The registration process is not finalized until fees have been paid**</font>";
		$foot.= "<tr><td><font face='verdana' size=2>Team Name: <B>".$teamName."</B><td align='right'><font face='verdana' size=2>";
		$foot.= "Captain: <B>".$capFirst." ".$capLast."</B>";
		$foot.= "<tr><td><font face='verdana' SIZE='2'>Preferred league: <B>".$leagueName."</B>";
		$foot.= "<TD align='right'><font face='verdana' SIZE='2'>Registration Fee: <B>$".$fee."</B>";
		$foot.= "<tr><TD><font face='verdana' SIZE='2'>Payment Method: <td colspan=2><select name='method'>";
		$foot.= "<option value=''>Choose Payment Method</option>";
		$foot.= "<option value='Team will mail fees to the office'>I will mail cheque to Perpetual Motion's home office</option>";
		$foot.= "<option value='Team will bring fees to registration night'>I will bring cash/cheque to registration night</option>";
		$foot.= "<option value='Team will bring fees To the office'>I will bring cash/cheque to Perpetual Motion's home office</option>";
		$foot.= "<option value='Team will send an email money transfer'>I will send an email money transfer to dave@perpetualmotion.org</option>";
		$foot.= "</select>";
		$foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='confirm' Value='Confirm Registration Submission'>";
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
		$foot.= "<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><font face='verdana' SIZE=2><B>5. Register Your Team or Save Details</B><BR>";
		$foot.= "<font face='verdana' SIZE=1>Submit your team registration to the convenor or save details to your profile for another time.";
		$foot.= "<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='register' Value='Register'>";
		$foot.= "<INPUT TYPE='Submit' NAME='save' Value='Save Details'>";
		$foot.= "<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>";
		$foot.= "<tr><td> <BR>";
		$foot.= "<tr><td colspan=5 align=center><font face='verdana' color=red size=2><b>Registration due date: ".$registration_due."</b>";
	}
	
	return $foot;
}//end function

//##############################################################################################################################################
//                                                  END OF FOOTER FUNCTION
//##############################################################################################################################################

///////// Getting the data from Mysql table for the league dropdown, uses $fiter defined above//////////	
$quer2=mysql_query("SELECT DISTINCT category, cat_id, semi_identifier FROM regcategory $filter AND available=1 ORDER BY semi_identifier")
	or die(mysql_error());
///////////// End of query for first list box////////////

//##############################################################################################################################################
//                                                  "FRONT-END" FORM VARIABLE DECLARATIONS
//##############################################################################################################################################

// These are the variables used when reshowing the form to the user
// They have to be different than the "Back-end" or "raw" data which is formatted differently to go into the database

// ** Example: **
// Real Team name: "Can't Score Won't Score" 
// FRONT END VARIABLE would hold: Can't Score Won't Score (no difference)
// Back End Variable would hold: Can/'t Score Won/'t Score (slashes)  

$quer2_array=query("SELECT DISTINCT registrationDue, semi_identifier FROM regcategory $filter AND available=1 ORDER BY semi_identifier");
$registration_due=$quer2_array['registrationDue']; //sets what shows up on the registration form as the due date. Set in regcategory database

$regCat_semi=$quer2_array['semi_identifier'];
$seas_array=array("","Spring","Summer","Fall", "Winter 1", "Winter 2"); //sets what each number means
$seas_number=substr($regCat_semi,4,1); //gets the season number (1-5) from the semi-identifier (the number after the first dash)
$seas_name=$seas_array[$seas_number]; //sets the name of the upcoming season for use in the "do you want to reregister line"

//##############################################################################################################################################
//                                               END OF "FRONT-END" FORM VARIABLE DECLARATIONS
//##############################################################################################################################################

//##############################################################################################################################################
//                                                  "BACK-END" FORM VARIABLE DECLARATIONS
//##############################################################################################################################################

// These are the variables used to hold data that will eventually get input into the database
// They have to be different than the "Front-end" or "real" data which is formatted to appear regularly when reshowing form info to the user

// ** Example: **
// Real Team name: "Can't Score Won't Score" 
// Front End Variable would hold: Can't Score Won't Score (no difference)
// BACK END VARIABLE would hold: Can/'t Score Won/'t Score (slashes)  
               
	$user = $_SESSION['username'];                             
//If a button was pressed, populate the variables based on what was in the form.                                                
if (isset($_POST['register']) or isset($_POST['confirm']) or isset($_POST['save']) or isset($_POST['cSave'])) {	
	
	//---League Information (cat_id, league name, and price of the league)---\\
	if (isset($_POST['semi'])){
		$semi=$_POST['semi'];
		$holderVariable=$_POST['holderVariable'];
		
		if ($semi=="" or (strlen($semi))>=12) {
			$semi=$holderVariable;
		} elseif($semi=="nullVal") {
			$semi="";
		}
		
		$leagueArray=query("SELECT * FROM regcategory WHERE semi_identifier='$semi'");
		$league=$leagueArray['category'];
		$semiIdentifier=$semi;
		$fee=$leagueArray['fee'];
		$topNotice=$leagueArray['topNotice'];
	} else {
		$semi="";
	}
	
	//---Captain's Information (First/Last Name, Email, Phone Number, Sex)---\\
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
	}

	//-----------Player's Information (First/Last Name, Email, Sex)----------\\
	$playerFirst=array();
	$playerLast=array();
	$playerEmail=array();
	$playerSex=array();

	//For loop to store the data in an array
	for($a=1; $a<=$people; $a++){
		$playerFirst[$a]=$_POST['playerFirst'.$a];
		$playerLast[$a]=$_POST['playerLast'.$a];
		$playerEmail[$a]=$_POST['playerEmail'.$a];
		$playerSex[$a]=$_POST['playerSex'.$a];
		$playerMaleSelected[$a]='';
		$playerFemaleSelected[$a]='';
		if($playerSex[$a]=='Male'){
			$playerMaleSelected[$a]="selected";
		}elseif($playerSex[$a]=='Female'){
			$playerFemaleSelected[$a]="selected";
		}
	}

	//--- Various Variables (comments, teamName) ---\\
	$comments=$_POST['comments'];
	$teamName=$_POST['teamName'];

	//--- Mesthod Variable (The only that isn't checked EVERY time) ---\\
	if(isset($_POST['confirm']) or isset($_POST['cSave'])){
		$method=$_POST['method'];
	}

}else{
	//No buttons were pressed, therefore this is the initial (empty) version of the form
	//Populate captain info from the user database
	//It is assumed that the user registering the team will be the captain

	$teamName="";

	//Captain variables
	$capArray=query("SELECT * from users where username='$user'");
	$capFirst=$capArray['first'];
	$capLast=$capArray['last'];
	$capEmail=$capArray['email'];
	$capPhone=formatPhoneNumber($capArray['phone']);
	$capSex=$capArray['sex'];
	$capMaleSelected='';
	$capFemaleSelected='';
	if($capSex=='Male'){
		$capMaleSelected="selected";
	}elseif($capSex=='Female'){
		$capFemaleSelected="selected";
	}

	//Player Variables
	//By default, the first spot in the player roster will be filled with the same info as the captain (pulled from user database)
	$playerFirst[1]=$capFirst;
	$playerLast[1]=$capLast;
	$playerEmail[1]=$capEmail;
	$playerPhone[1]=$capPhone;
	$playerSex[1]=$capSex;
	$playerMaleSelected[1]='';
	$playerFemaleSelected[1]='';
	$playerNoneSelected[1]='';
	
	if($playerSex[1]=='Male') {
		$playerMaleSelected[1]='selected';
	} elseif($playerSex[1]=='Female') {
		$playerFemaleSelected[1]='selected';
	} else {
		$playerNoneSelected[1]='selected';
	}

	//The rest of the player spots are empty initially
	for($s=2; $s<=$people; $s++){
			$playerFirst[$s]='';
			$playerLast[$s]='';
			$playerEmail[$s]='';
			$playerSex[$s]='';
			$playerMaleSelected[$s]='';
			$playerFemaleSelected[$s]='';
			$playerNoneSelected[$s]='';
	}// for (s)

			$comments="";

}//end setting variables

//--- CALLS THE FUNCTIONS BASED ON WHAT BUTTON WAS CLICKED ---\\

if (isset($_POST['register'])) register();  //if register button is pressed, execute register function
elseif(isset($_POST['save'])) save();   	//if save button is pressed, execute save function
elseif(isset($_POST['confirm'])) confirm();	//if confirm button is pressed, execute confirm function
elseif(isset($_POST['cSave'])) cSave();    	//if cSave button (confirming option to save) is pressed...
else $footer=showFooter(0);                	//if no button was pressed, show the original form with bottom footer

$title=$titleHeader; //Used to show the title in the explorer
$teamNameFromDBA=query("SELECT * FROM registration WHERE userName='".$user."' AND sport='".$sport."'");
$teamNameFromDB=$teamNameFromDBA['teamName'];

printForm(); ?>

</html>

<?php ob_flush();?>