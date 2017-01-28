<?php
session_start();
/*
Variables and Declarations for connecting to the database server
*/
$dbservertype='mysql';
$servername='mysql.giantgoat.dreamhosters.com';
$dbusername='pmotiondata';
$dbpassword='rememberHeavysalmon4';
$dbname='data_perpetualmotion';
connecttodb($servername,$dbname,$dbusername,$dbpassword);
function connecttodb($servername,$dbname,$dbuser,$dbpassword){
global $link;
$link=mysql_connect ("$servername","$dbuser","$dbpassword");
if(!$link){die("Could not connect to MySQL");}
mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
}
//////// End of connecting to database ////////
/*
This code is for security / login purposes and will be on every page that's password protected
*/
global $user;
if(isset($_SESSION["username"])){$user = $_SESSION["username"];}else{$user = 'unknown';}
if (!isset ($_SESSION["username"])) //if your variable isn't there, then the session must not be
{
session_unset (); //so destroy whatever session there was and bring them to login page
session_destroy ();
$url = "Location: login.php";
header ( $url );
}
else //otherwise, they can see the page
{
/*
End of security / login code
*/

//By declaring these variables as global, they're important and known throughout the entire program
//Reserved variable names!!
global $sport, $footer, $cat_id, $league, $error, $regID;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $state, $semiIdentifier;
//NOTE: $user is also a global variable, but is declared above

//Gets whatever argument you pass into the program for sport ("?sport=beach" etc)
if(isset($_GET['sport'])){$sport = $_GET['sport'];}else{$sport = 'unknown';}

######### START OF 'BACKEND CODE' FUNCTIONS FOR EACH BUTTON #########

//The backend code for when the register button is pressed (different then confirm!!)
function register(){
global $error;
$errorCheck=errorCheck();
$numberError="";

if(($errorCheck!="")or($numberError!="")){//There was an error somewhere, print all errors and show the basic footer
$error=$errorCheck."<BR>".$numberError; //Set error equal to the error descriptions returned by the errorchecking functions
global $footer;
$footer=showFooter(0);
}else{$error=""; //No Errors, show the confirmation/fee footer box
global $footer;
$footer=showFooter(1);}
}//end function

//The backend code for when the save button is pressed
function save(){
global $error;
$errorCheck="";
$errorCheck=errorCheck();
if($errorCheck!=""){$error=$errorCheck; //There was an error somewhere, set error so it'll print the error, show basic footer
global $footer;
$footer=showFooter(0);
}else{ $error="";
$regID=prepare(0); //There were no errors, execute the prepare(0) function which inserts form data into the database, 0 means saved
if($comments!=""){mailForm($regId, 1);} //Mail if there were comments added on the save
header("Location: thankyousave.htm"); }   //Show the "Thank you for saving" page
}//end function

//The backend code for when the confirm button is pressed (different then register!!)
function confirm(){
global $secondaryError; //used if there's an error on the confirm page
$errorCheck="";
$errorCheck=errorCheck();
if(($errorCheck!="")){ //There was an error somewhere, set error so it'll print the error, show basic footer
$secondaryError=$errorCheck;
global $footer;
$footer=showFooter(1);
}
else{
$error="";
$regID=prepare(1);     //Execute the prepare function to insert data into the database, 1 means registered
prepConfirm($regID); //Execute the prepConfirm function which inserts the "payment" data into the db
mailForm($regID, 0);//Execute the mailConvenor function which emails the convenor a confirmation email
header("Location: thankyoureg.htm");  //show the thank you for registering page
}// end else
}// end function

############ END OF 'BACKEND CODE' FOR BUTTONS FUNCTIONS ##################

####################### PREPARE FUNCTIONS ########################

//Inserts data into the db
function prepare($registeredBool){
global $sport, $footer, $cat_id, $league, $error;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee, $user;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semiIdentifier, $semi;

//ADDS SLASHES BEFORE SAVING TO DATABASE
$leagueNm=addSlashes($league);
$teamNm=addSlashes($teamName);
$capFirst=addSlashes($cFirst);
$capLast=addSlashes($cLast);
$capSex=addSlashes($cSex);
$capEmail=addSlashes($cEmail);
$capPhone=preg_replace("/\D/",'',$cPhone); //remove all non-digits (dashes, dots, brackets, etc)
$semiID=$semiIdentifier;

$selectAll=mysql_query("SELECT MAX(registrationID) as maxnum FROM registration");
$regArr=mysql_fetch_array($selectAll);
$maximum=$regArr['maxnum'];

global $regID;
$regID=$maximum+1;
print"<INPUT TYPE='hidden' NAME='reg' VALUE='".$regID."'>";
//REGISTRATIONID NUM -- A Primary Key (Each number only used once, is 1 higher than the highest regID in the table...)
if(strlen($leagueNm)<=5){
header("Location: RegistrationError.htm");
}
$insert="INSERT INTO registration (registrationID, semi_identifier, teamName, sport, league, userName, cFirst, cLast, method, registered) VALUES
(".$regID.", '".$semiID."', '".$teamNm."', '".$sport."', '".$leagueNm."', '".$user."', '".$capFirst."
', '".$capLast."', '', ".$registeredBool.")";
$addRegistration=mysql_query($insert);
$insert="INSERT INTO captains (registrationID, teamName, cFirst, cLast, cEmail, cPhone, cSex) VALUES
(".$regID.", '".$teamNm."', '".$capFirst."', '".$capLast."'
, '".$capEmail."', '".$capPhone."', '".$capSex."')";
$addCaptain=mysql_query($insert);

for ($e=1; $e<=$people; $e++){
$fName[$e]=htmlEntities($playerFName[$e]);
$lName[$e]=htmlEntities($playerLName[$e]);
$email[$e]=htmlEntities($playerEmail[$e]);
$sex[$e]=addSlashes($playerSex[$e]);
if((strlen($fName[$e])>=1)){
$insert="INSERT INTO players (registrationID, teamName, firstName, lastName, playerEmail, playerSex) VALUES
(".$regID.", '".$teamNm."', '".$fName[$e]."', '".$lName[$e]."',
'".$email[$e]."', '".$sex[$e]."')";
$addPlayer=mysql_query($insert);
}
}//end for
return $regID;
}//end function
?><FORM NAME='update' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']?>?sport=<?php print $sport?>>
<INPUT TYPE='hidden' NAME='sport' VALUE=$sport>
<INPUT TYPE='hidden' NAME='user' VALUE=$user>
<?php
//VARIABLE DECLARATIONS BASED ON SPORT
if($sport=='ultimate'){ //If sport is ultimate, sets common name (type), logo,
//welcome note, database filter, and number of people
$type="Ultimate";
$logo="/logos/GuelphUltimate.jpg";
$sportHeader="<br>Register as a team for Guelph Ultimate";
$titleHeader="Register - Guelph Ultimate";
$people=15;
$filter = "where cat_id > 100 and cat_id < 200";
}elseif($sport=='beach'){//If sport is beach, sets common name (type), logo,
//welcome note, database filter, and number of people
$type="Beach Volleyball";
$logo="/logos/WheresTheBeach.jpg";
$sportHeader="<br>Register as a team for Where's The Beach Volleyball";
$titleHeader="Register - Where's The Beach Volleyball";
$people=12;
$filter = "where cat_id >0 and cat_id < 100";
}elseif($sport=='football'){//If sport is football, sets common name (type), logo, welcome note, database filter, and number of people
$type="Flag Football";
$logo="/logos/GuelphFlagFootball.jpg";
$sportHeader="<br>Register as a team for Guelph Flag Football";
$titleHeader="Register - Guelph Flag Football";
$people=12;
$filter = "where cat_id > 200 and cat_id < 300";
}elseif($sport=='soccer'){ //If sport is soccer, sets common name (type), logo,
//welcome note, database filter, and number of people
$type="6 vs 6 Soccer";
$logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
$sportHeader="<br>Register as a team for Guelph 6 vs 6 Soccer";
$titleHeader="Register - Guelph 6 vs 6 Soccer";
$people=12;
$filter = "where cat_id > 300";
}
//end declarations
?>

<INPUT TYPE='hidden' NAME='type' VALUE="$type">
<INPUT TYPE='hidden' NAME='people' VALUE="$people">

<?php
///////// Getting the data from Mysql table for the league dropdown, uses $fiter defined above//////////
$quer2=mysql_query("SELECT DISTINCT category, cat_id, semi_identifier FROM regCategory ".$filter." and available =1 order by semi_identifier");
///////////// End of query for first list box////////////

$secondaryError=''; 
function formatPhoneNumber($strPhone)
        {
                $strPhone = ereg_replace("[^0-9]",'', $strPhone);
                if (strlen($strPhone) != 10)
                {
                        return $strPhone;
                }

                $strArea = substr($strPhone, 0, 3);
                $strPrefix = substr($strPhone, 3, 3);
                $strNumber = substr($strPhone, 6, 4);

                $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;

                return ($strPhone);
        }



########## START OF ERROR CHECKING FUNCTIONS ##########

//Function to make sure that any variable passed to it is not null or empty
function allProperEntered($data){
if(strlen($data)>0){return true;}
return false;
}//end function

//Main error checking function, checks that minimum requirements are met
function errorCheck(){

global $sport, $footer, $cat_id, $league, $error, $regID;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semiIdentifier, $semi;

$err=""; //Set err = nothing
if(!isset($_POST['confirm'])){
if($cFirst==""){  $err.="Please enter the captain's first name.<BR>";} //if no captain first name
if($cLast==""){   $err.="Please enter the captain's last name.<BR>";} //if no captain last name
if($cEmail==""){  $err.="Please enter the captain's email address.<BR>";} //if no captain email
if($cPhone==""){  $err.="Please enter the captain's phone number.<BR>";} //if no captain phone
if($cSex==""){      $err.="Please select the captain's sex.<BR>";} //if no sex selected
if($semi==""){    $err.="Please select a league/division.<BR>";} //if no league selected
if($teamName==""){  $err.="Please enter a teamname.<BR>";} //if no team name is entered
}
else{ //if confirm registration was selected, check method of payment was selected
if($method==""){$err.="Please select your method of payment.<BR>";}
}//end if 'confirm'

return $err; //Return err, it'll be nothing if there were none!
}//end function

########## END OF ERROR CHECKING FUNCTIONS ############



############ START OF MAIL FUNCTIONS ############

//Function to create the body of the email for both captain and player
function body($regID){
global $sport, $footer, $cat_id, $league, $error;
global $people, $teamName, $cFirst, $cLast, $cSex, $regID;
global $cEmail, $cPhone, $comments, $method, $fee;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semi;

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
$body.="
<TR><TD colspan=4 align=center>-----------------Captain's Information-------------------</TD></TR>
<TR><TD><B>First Name:</B></td><td><b>Last Name:</b></td><td> </td><td><b>Sex:</b></td></tr>
<tr><TD>".$capFirst."</td><td>".$capLast."</td><td> </td><td>".$capSex."</td></tr>
<tr><TD colspan=2><B>Email:</B></td><td colspan=2><b>Phone Number:</b></td></tr>
<tr><TD colspan=2>".$capEmail."</td><td colspan=2>".$phoneNumber."</td></tr>
<tr><td><BR></td></tr>
<tr><td colspan=4 align=center>-----------------Player Information-------------------<td></tr>
<tr><td><b>First Name:</b></td><td><b>Last Name:</b></td><td><b>Email:</b></td><td><b>Sex:</b></td></tr>";

for($c=1; $c<=$people; $c++){
if(strLen($playerFName[$c])>=1){
$body.="<tr><td>".$c.". ".$playerFName[$c]."</td><td>".$playerLName[$c]."</td><td>".$playerEmail[$c]."</td><td>".$playerSex[$c]."</td></tr>";
}//end if
}//end for

$body.="<tr><td colspan=4 align=center>------------------------------------------------<td></tr>
<tr><td><B>Comments:</B></td></tr>
<tr><td colspan=4>".$comments."</td></tr>
<tr><td><BR></td></tr>
<tr><td>Received:</td><td>".$date."</td></tr></TABLE>";
return $body;
}//end function

//Function to email both dave (convenor) and the captain
function mailForm($regID, $comSub){
global $sport, $footer, $cat_id, $league, $error, $regID;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semi;

$title="<font size=3><TABLE align=center cellspacing=2 cellpadding=2><tr><td colspan=3 align=center><B>Perpetual Motion's Online
Registration System<BR>-- Registration Confirmation --</B>";
$secondary="<BR>New Team Registered for <br><B>".$type." - ".$league.".</B> </td></tr>";
$body=body($regID);
$teamLine="<TR><td align=center colspan=4><b>Team Name:</b> ".$teamName."</td></tr>";
$regIDLine="<TR><td align=center colspan=4><b>Team Name:</b> ".$teamName."</td></tr>
<tr><td colspan=4 align=center>Team Number: ".$regID."</td></tr>";
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
################### END OF MAIL FUNCTIONS ###################

################### START OF FOOTER FUNCTION ################

//Function to create the footer
function showFooter($state){
global $sport, $footer, $cat_id, $league, $error, $regID;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semi;

if($state==1){       //this is the "confirm details" footer
global $state;
$teamNm=htmlentities($teamName,ENT_QUOTES);
$capFirst=htmlentities($cFirst,ENT_QUOTES);
$capLast=htmlentities($cLast,ENT_QUOTES);
$foot= "<BR><TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>Confirm Fees</B><BR>
<FONT SIZE=1>
<FONT COLOR='red'>**The registration process is not finalized until fees have been paid**</font></TD></TR>
<tr><td><Font size=2>Team Name: <B>".stripslashes($teamNm)."</B></td><td align='right'><Font size=2>
Captain: <B>".stripslashes($capFirst)." ".stripslashes($capLast)."</B></td></tr>
<tr><td><FONT FACE='VERDANA' SIZE='2'>Preferred league: <B>".stripslashes($league)."</B></TD>
<TD align='right'><FONT FACE='VERDANA' SIZE='2'>Registration Fee: <B>$".$fee."</B></td>
<tr><TD><FONT FACE='VERDANA' SIZE='2'>Payment Method: </td><td colspan=2><select name='method'>
<option value=''>Choose Payment Method</option>
<option value='Team will mail fees to the office'>I will mail cheque to Perpetual Motion's home office</option>
<option value='Team will bring fees to registration night'>I will bring cash/cheque to registration night</option>
<option value='Team will bring fees To the office'>I will bring cash/cheque to Perpetual Motion's home office</option>
</select>
<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='confirm' Value='Confirm Registration Submission'>
<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'></td></tr>
<tr><td><br></td></tr>
<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b></td></tr>
<tr><td colspan=7 align=center><BR>
<B>Make Cheques Payable to Perpetual Motion<BR><BR>
Send This Confirmation Form & Fees to:</b>
<br>Perpetual Motion
<br>223 Waterloo Ave.
<br>Guelph, Ontario
<br>N1H 3J4
<br>(519) 222-0095";

}else{
$foot="<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>5. Register Your Team or Save Details</B><BR>
<FONT SIZE=1>Submit your team registration to the convenor or save details to your profile for another time.</TD></TR>
<tr><td colspan=4 align=center><INPUT TYPE='Submit' NAME='register' Value='Register'>
<INPUT TYPE='Submit' NAME='save' Value='Save Details'>
<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'></td></tr>
<tr><td> <BR></td></tr>
<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b></td></tr>";
//Sets the standard final options table, this is changed if register is clicked
}
return $foot;
}//end function


function prepConfirm($regID){
global $sport, $footer, $cat_id, $league, $error;
global $people, $teamName, $cFirst, $cLast, $cSex;
global $cEmail, $cPhone, $comments, $method, $fee, $user;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semi;

$insert="UPDATE registration SET method='".$method."' WHERE registrationID=".$regID;
$addRegistration=mysql_query($insert);
}//end prepConfirm

################# END CONFIRM FUNCTIONS ############################

################################ ALL FORM VARIABLES ARE DEFINED HERE!! ####################################
###########################################################################################################
###########################################################################################################
if ((isset($_POST['register']))or(isset($_POST['confirm']))or(isset($_POST['save']))or(isset($_POST['cSave']))or((strlen($secondaryError))>1)){
//If a button was pressed, populate the variables based on what was in the form.

//---League Information (cat_id, league name, and price of the league)---\\

if (isset($_POST['league'])){$semi=$_POST['league'];
$leagueQuery=mysql_query("SELECT * FROM regCategory WHERE semi_identifier='".$semi."'");
$leagueArray=mysql_fetch_array($leagueQuery);
$league=$leagueArray['category'];
$semiIdentifier=$semi;
$fee=$leagueArray['fee'];
}else{$semi="";}

//------------------------------------------------------------------------\\

//---Captain's Information (First/Last Name, Email, Phone Number, Sex)---\\
$cFirst=$_POST['cFirst'];
$cLast=$_POST['cLast'];
$cEmail=$_POST['cEmail'];
$cPhone=$_POST['cPhone'];
$cSex=$_POST['cSex'];
//-----------------------------------------------------------------------\\

//---Player's Information (First/Last Name, Email, Sex)---\\
$playerFName=array();
$playerLName=array();
$playerEmail=array();
$playerSex=array();

//For loop to store the data in an array
for($a=1; $a<=$people; $a++){
$playerFName[$a]=$_POST['firstName'.$a];
$playerLName[$a]=$_POST['lastName'.$a];
$playerEmail[$a]=$_POST['email'.$a];
$playerSex[$a]=$_POST['sex'.$a];
}
//--------------------------------------------------------\\

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





###############################THIS IS THE BULK OF THE FORM####################################
#################################################################################################

global $sport, $footer, $cat_id, $league, $error;
global $people, $teamName, $cFirst, $cLast, $cSex, $user;
global $cEmail, $cPhone, $comments, $method, $fee, $state;
global $playerFName, $playerLName, $playerEmail, $playerSex, $type, $semi, $regID;

$title=$titleHeader;
//Show the user header in the top left corner
?>
<head><title><?php print $title?></title></head>
<font face='arial' size=2>User: <B><?php print $user?></B>
<font size=1>(<a href=logout.php style='text-decoration: none;'>Logout</a>)</font>

<TABLE ALIGN="CENTER" cellpadding=2>
<tr><td colspan=4 align=center>
<TABLE align="center">
<tr align="center"><td align="center"><img src=<?php print $logo?>></td></tr></TABLE>
</td></tr>
<tr><td colspan=4 align="center"><font face="verdana" size=4><B><?php print $sportHeader?></B></font></td></tr>
<tr><td colspan=5 align=center><font color=red size=2><b>Registration due date: Friday, April 25th, 2008</b></td></tr>
<?php if ($error!=''){?>
<tr><td colspan=4 align="center"><font face="verdana" size=2 color="red"><?php print $error?></font></td></tr>
<?php }
if ($secondaryError!=''){?>
<tr><td colspan=4 align="center"><font face="verdana" size=2 color="red"><?php print $secondaryError?></font></td></tr>
<?php }


if ((isset($_POST['register']) or isset($_POST['confirm']) or isset ($_POST['save']) or (strlen($secondaryError)>1)) and $error==""){?>
<tr><td><?php print $footer?></td></tr>
<tr><td><BR></td><tr>
<?php }

if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){?>
<tr BGCOLOR="#CCCCCC"><td colspan=4 align="center"><font face="verdana" size=2><b>1. Select Your Division and Choose Team Name</b><br>
<font size=1>Please choose a division.</font>
<?php }


if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){?>
<tr><td colspan=4>
<TABLE align=center><tr BGCOLOR="white"><td align="center" colspan=2><font face="verdana" size=2><B>Preferred League:</B></td>
<td align="left" colspan=2>

<select name='league'><option value=''>League</option>
<?php $leagueDD="";
while($noticia2 = mysql_fetch_array($quer2)) {
     if($noticia2['semi_identifier']==@$semi){
          $leagueDD.= "<option value selected='$noticia2[semi_identifier]'>$noticia2[category]</option>"."<BR>";}//end if
     else{$leagueDD.= "<option value='$noticia2[semi_identifier]'>$noticia2[category]</option>";}//end else
}//end while
$leagueDD.= "</select>";
print $leagueDD."<BR>";?>
</td>
<?php }else{ //something was selected, recall what was selected in the dropdown
?>
<INPUT TYPE="hidden" NAME="league" VALUE=<?php '$semi'?>>
<?php }


if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm'])){
$teamNm=htmlentities($teamName,ENT_QUOTES);
}else{
$teamNm="";
}

if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){
?>
<tr><td align='center' colspan=2><font face='verdana' size=2><b>Team Name: </b></td>
<td colspan=2>
<input type='text' name='teamName' VALUE="<?php print stripslashes($teamNm);?>" size=30></td></tr>
<tr></table>
</td></tr>
<?php }else{
$tmNm=$teamNm;
?><INPUT TYPE='hidden' NAME='teamName' VALUE="<?php print $tmNm?>">
<?php }//end else





//Captain table
if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm'])){
$capFirst=htmlentities($cFirst,ENT_QUOTES);
$capLast=htmlentities($cLast,ENT_QUOTES);
$capEmail=htmlentities($cEmail,ENT_QUOTES);
$capPhone=htmlentities($cPhone,ENT_QUOTES);
$capSex=htmlentities($cSex,ENT_QUOTES);
$capMaleSelected='';
$capFemaleSelected='';
if($capSex=='Male'){
$capMaleSelected="selected";
}elseif($capSex=='Female'){
$capFemaleSelected="selected";
}else{$capNoneSelected="selected";}
}else{ //else set captain info from the database
$capQuery=mysql_query("SELECT * from users where username='$user'");
$capArray=mysql_fetch_array($capQuery);
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
}//end else

$capBlurb="The captain is the first person we'll contact with team inquiries,
and is responsible for submitting scores.";
global $state;
if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){?>
<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>2. Captain Information</B><BR>
<FONT SIZE=1><?php print $capBlurb?></font></td></tr>

<tr><td colspan=4><TABLE align=center cellspacing=10 cellpadding=1>
<tr BGCOLOR='white'><td align='center'><FONT FACE='verdana' SIZE='2'><B>First Name:</td>
<td align='center'><FONT FACE='verdana' SIZE='2'><B>Last Name:</td>
<td align='center'><FONT FACE='verdana' SIZE='2'><B>Sex:</td></tr>

<tr BGCOLOR="white"><td><INPUT TYPE="text" NAME="cFirst" VALUE="<?php print stripslashes($capFirst);?>" SIZE=40></td>
<td><INPUT TYPE="text" NAME="cLast" VALUE="<?php print stripslashes($capLast);?>" SIZE=40></td>
<td align=center><SELECT NAME="cSex">
<FONT FACE="verdana" SIZE=2><OPTION VALUE="">Select One</OPTION></Font>
<FONT FACE="verdana" SIZE=2><OPTION VALUE="Male" <?php print $capMaleSelected?>>Male</OPTION></Font>
<FONT FACE="verdana" SIZE=2><OPTION VALUE="Female" <?php print $capFemaleSelected?>>Female</OPTION></Font>
</td></tr>
<tr BGCOLOR="white"><td align="center"><FONT FACE="verdana" SIZE="2"><B>Email:</td>
<td align="center"><FONT FACE="verdana" SIZE="2"><B>Phone Number:</td></tr>
<tr BGCOLOR="white"><td><INPUT TYPE="text" NAME="cEmail" VALUE="<?php print stripslashes($capEmail);?>" SIZE=40>
</td><td align="center"><INPUT TYPE="text" NAME="cPhone" VALUE="<?php print stripslashes($capPhone);?>" SIZE=15>
</td></tr></table>
</td></tr>

<?php }else{?>
<INPUT TYPE='hidden' NAME='cFirst' VALUE="<?php print $capFirst?>">
<INPUT TYPE='hidden' NAME='cLast' VALUE="<?php print $capLast?>">
<INPUT TYPE='hidden' NAME='cSex' VALUE="<?php print $capSex?>">
<INPUT TYPE='hidden' NAME='cEmail' VALUE="<?php print $capEmail?>">
<INPUT TYPE='hidden' NAME='cPhone' VALUE="<?php print $capPhone?>">
<?php }//end else





//Player table
if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm'])){
for($r=1; $r<=$people; $r++){
$playerFName[$r]=htmlentities($playerFName[$r],ENT_QUOTES);
$playerLName[$r]=htmlentities($playerLName[$r],ENT_QUOTES);
$playerEmail[$r]=htmlentities($playerEmail[$r],ENT_QUOTES);
$playerSex[$r]=htmlentities($playerSex[$r],ENT_QUOTES);
$playerMaleSelected[$r]='';
$playerFemaleSelected[$r]='';
$playerNoneSelected[$r]='';
if($playerSex[$r]=='Male'){$playerMaleSelected[$r]="selected";}
elseif($playerSex[$r]=='Female'){$playerFemaleSelected[$r]="selected";}
else{$playerNoneSelected[$r]="selected";}
}//end for (r)
}//end if isset
else{ //else Player info is nothing

$playerFName[1]=$capFirst;
$playerLName[1]=$capLast;
$playerEmail[1]=$capEmail;
$playerPhone[1]=$capPhone;
$playerSex[1]=$capSex;
$playerMaleSelected[1]="";
$playerFemaleSelected[1]="";
$playerNoneSelected[1]="";
if($playerSex[1]=='Male'){$playerMaleSelected[1]="selected";}
elseif($playerSex[1]=='Female'){$playerFemaleSelected[1]="selected";}
else{$playerNoneSelected[1]="selected";}

for($s=2; $s<=$people; $s++){
$playerFName[$s]="";
$playerLName[$s]="";
$playerEmail[$s]="";
$playerSex[$s]="";
$playerMaleSelected[$s]="";
$playerFemaleSelected[$s]="";
$playerNoneSelected[$s]="";
}// for (s)
}//end else

global $state;
if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){
?>
<tr><td colspan=4><TABLE><TR BGCOLOR='#CCCCCC'><TD COLSPAN=5 align='center'><FONT FACE='verdana' SIZE=2><B>3. Player Information</B><BR>
<FONT SIZE=1><FONT COLOR='red'>*</FONT> The first player listed should be the captain, the second will be an alternate contact if
the captain is unavailable.</font></td></tr>


<tr BGCOLOR='white'><td></td><td align='center'><FONT FACE='verdana' SIZE='2'>
<B>First Name:</td><td align='center'><FONT FACE='verdana' SIZE='2'><B>Last Name:</td>
<td align='center'><FONT FACE='verdana' SIZE='2'><B>Email:</td>
<td align='center'><FONT FACE='verdana' SIZE='2'><B>Sex:</td></tr>
<?php for($v=1; $v<=$people; $v++){?>
<tr BGCOLOR="white"><td>
<?php if($v<=2){
print $v.'.'?>
<FONT COLOR="red">*</FONT></td>
<?php }//adds the asterisk

else{
print $v.'.'?></td><?php }?>
<td><INPUT TYPE="text" NAME="firstName<?php print $v?>" VALUE="<?php print stripslashes($playerFName[$v]);?>" SIZE=27></td>
<td><INPUT TYPE="text" NAME="lastName<?php print $v?>" VALUE="<?php print stripslashes($playerLName[$v]);?>" SIZE=30></td>
<td><INPUT TYPE="text" NAME="email<?php print $v?>" VALUE="<?php print stripslashes($playerEmail[$v]);?>" SIZE=30></td>
<td align=center>
<SELECT NAME="sex<?php print $v?>"><FONT FACE = "verdana" SIZE=2>
<OPTION VALUE="" <?php print $playerNoneSelected[$v]?>>Select One</OPTION></Font>
<FONT FACE="verdana" SIZE=2><OPTION VALUE="Male" <?php print $playerMaleSelected[$v]?>>Male</OPTION></Font>
<FONT FACE="verdana" SIZE=2><OPTION VALUE="Female" <?php print $playerFemaleSelected[$v]?>>Female</OPTION></Font>
</td></tr>
<?php }//END FOR (v)
?>
</TABLE></td></tr>

<?php }else{
for($u=1; $u<=$people; $u++){ ?>
<INPUT TYPE='hidden' NAME="firstName<php print $u?>" VALUE="<?php print $playerFName[$u]?>">
<INPUT TYPE='hidden' NAME="lastName<?php print $u?>" VALUE="<?php print $playerLName[$u]?>">
<INPUT TYPE='hidden' NAME="sex<?php print $u?>" VALUE="<?php print $playerSex[$u]?>">
<INPUT TYPE='hidden' NAME="email<?php print $u?>" VALUE="<?php print $playerEmail[$u]?>">
<?php }//end for (u)
}//end else


if (isset($_POST['register'])or isset($_POST['save']) or isset($_POST['confirm'])){
$comment=htmlentities($comments, ENT_QUOTES);
}else{
$comment='';
}
global $state;
if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){?>
<TR BGCOLOR='#CCCCCC'><TD COLSPAN=4 align='center'><FONT FACE='verdana' SIZE=2><B>4. Comments</B><BR>
<FONT SIZE=1>Comments, notes, player needs, etc.</font></td></tr>
<tr><TD colspan=4 align='center'><TEXTAREA NAME='comments' COLS= 80 ROWS=6><?php print stripslashes($comment);?></TEXTAREA></TD></TR>
<?php }else{?>
<INPUT TYPE='hidden' NAME='comments' VALUE=<?php print $comment?>>
<?php }

//Final Options Table
global $state;
if(($error!="")||(!isset($_POST['register']) and !isset($_POST['save']) and !isset($_POST['confirm']))){
print $footer;
}?>
</table>
</form>
<?php print"</html>";

}//end they can see the page
?>
