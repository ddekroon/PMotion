<?php
date_default_timezone_set('America/New_York');

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

function invalidEmail($address)
{
  // check an email address is possibly valid
  if (preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9.-]+$/', $address))
    return false;
  else
    return true;
}

function formatPhoneNumber($strPhone) {
	$strPhone = preg_replace("[^0-9]",'', $strPhone);
	if (strlen($strPhone) != 10) {
    	return $strPhone;
	}
    $strArea = substr($strPhone, 0, 3);
    $strPrefix = substr($strPhone, 3, 3);
    $strNumber = substr($strPhone, 6, 4);

    $strPhone = "(".$strArea.") ".$strPrefix."-".$strNumber;

    return ($strPhone);
}


//checks if the username contains anything other than letters, numbers, underscores, dashes, or periods
function valid_name_pass($string){
	if(preg_match('/[^a-zA-Z0-9_.-]/', $string)) {
		return false;
	} else{
		return true;
	}
}//end valid_name_pass()

function checkInput() {
	global $error, $firstName, $lastName, $email, $phoneNumber, $gender, $username, $password, $password2, $userTable;
	
	//This makes sure they did not leave any fields blank
	if (!$firstName | !$lastName | !$email | !$phoneNumber | !$gender | !$username | !$password | !$password2) {
		$error.='<BR>You did not complete all of the required fields.';
	}
	
	// checks if the username is in use
	if (!get_magic_quotes_gpc()) {
		$username = addslashes($username);
	}
	$check = mysql_query("SELECT * FROM $userTable WHERE user_username = '$username'") or die(mysql_error());
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) {
		$error.='<BR>The username "'.$username.'" is already in use.';
	}

	//checks that the user name is between 6 and 16 characters
	$userLength=strlen($username);
	if(($userLength<=5)||($userLength>=17)){
		$error.="<BR>Username must be between 6 and 16 characters.";
	}

	//checks that the password is between 6 and 16 characters
	$passLength=strlen($password);
	if(($passLength<=5)||($passLength>=17)){
		$error.="<BR>Password must be between 6 and 16 characters.";
	}

	// checks if the email is in use
	if (!get_magic_quotes_gpc()) {
		$email = addslashes($email);
	}
	$echeck = mysql_query("SELECT * FROM $userTable WHERE user_email = '$email'") or die(mysql_error());
	$echeck2 = mysql_num_rows($echeck);

	//if the email exists it gives an error
	if ($echeck2 != 0) {
		$error.='<BR>There is already an account registered with the email '.$email.'';
	}

	//check if the email is invalid and give an error
	if (invalidEmail($email)){
		$error.='<BR>The email address "'.$email.'" is not valid (ex. "something@something.com").';
	}

	if (!valid_name_pass($username)){
		$error.='<BR>The username entered contained invalid characters. No spaces, apostrophes, or quotes please.';
	}

	if (!valid_name_pass($password)){
		$error.='<BR>The password entered contained invalid characters. No spaces, apostrophes, or quotes please.';
	}
	
	//check if the phone number is proper (10 digits, formatting doesn't matter), throw error if not
	if(!preg_match('^(\D*)?(\d{3})(\D*)?(\d{3})(\D*)?(\d{4})$^', $phoneNumber)){
		$error.='<BR>You entered an invalid phone number (Please provide 10 digits in the format (xxx) xxx-xxxx or similar)';
	}

	// this makes sure both passwords entered match
	if ($password != $password2) {
		$error.='<BR>Your passwords did not match.';
	}

	//If there are errors, exit the program and show the messages
	if((strlen($error)>2)){
   		$error="<B>The following errors were found in your submission. Please correct and resubmit.</B>$error</BR></BR>";
   	}
}

function registerUser() {
	global $error, $firstName, $lastName, $email, $phoneNumber, $gender, $username, $password, $password2, $userTable, $userHistoryTable;

	$emailBody="<TABLE ALIGN=CENTER><TR><TD COLSPAN=2 ALIGN=CENTER><B>THANK YOU FOR REGISTERING!</B></TD></TR>
	<TR><TD COLSPAN=2 ALIGN=CENTER>This email will serve as a password & username reminder.</TD><TR>
	<TR><TD><BR>Username: </td><td><BR>$username</TD></TR>
	<TR><TD>Password: </td><td>$password</TD></TR>
	<TR><TD COLSPAN=2 ALIGN=CENTER width=500><BR>Please keep this information in a safe place. Should you
	forget your username or password, both are changeable at any time from our 
	<a href='www.perpetualmotion.org/'>main website</a>.
	<TR><TD colspan=2><BR><BR>Thanks, enjoy the season!<BR>
	<B>The Perpetual Motion Team</B><BR>
	<a href='mailto: info@perpetualmotion.org'>info@perpetualmotion.org</a></TD></TR></TABLE>";
		
	$emailSubject= "New User Registered - ".$username;
	$from_head  = 'MIME-Version: 1.0' . "\r\n";
	$from_head .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$from_head .= 'From: info@perpetualmotion.org';
	mail($email, $emailSubject, $emailBody, $from_head);


	// here we encrypt the password and add slashes to fields if needed
	$password = md5($password);
	if (!get_magic_quotes_gpc()) {
		$password = addslashes($password);
		$username = addslashes($username);
		$firstName = addslashes($firstName);
		$lastName = addslashes($lastName);
		$email = addslashes($email);
		$phoneNumber = preg_replace("/\D/",'',$phoneNumber); //remove non-digits (brackets, dashes, dots, etc)
	}
	
	//NEW QUERY STARTS HERE
	if ($gender == 'Male') $gender = 'M';
	elseif ($gender == 'Female') $gender = 'F';
	else $gender = 'U';
	
	$phoneNumber = preg_replace("/\D/",'',$phoneNumber); //remove non-digits (brackets, dashes, dots, etc)
	
	$maxNum = mysql_query("SELECT MAX(user_id) AS maxNum FROM $userTable");
	
	$maxNumArray = mysql_fetch_array($maxNum);
	$newID = $maxNumArray['maxNum'] +1;

	$newInsert = "INSERT INTO $userTable (user_id, user_username, user_firstname, user_lastname, user_password, user_email, user_phone, user_sex, 
		user_all_access, user_created) VALUES ($newID, '$username', '$firstName', '$lastName', '$password', '$email', $phoneNumber, '$gender', 0, 
		CURDATE())";

	$add_new = mysql_query($newInsert) or die(mysql_error());
	
	$insertHistory = "INSERT INTO $userHistoryTable (user_history_user_id, user_history_username, user_history_type, user_history_timestamp)
		VALUES ($newID, '$username', 'Registered successfully as a new user.', NOW())";
		
	$add_history = mysql_query($insertHistory) or die(mysql_error());
	
}

function printForm() {
	global $error, $firstName, $lastName, $email, $phoneNumber, $gender, $username, $password, $password2;
	
	//RESHOW THE TABLE DUE TO ERRORS
	if($gender=='Male')$maleSelected='selected';
	elseif($gender=='Female')$femaleSelected='selected';?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <font face='Arial'><table align='center'><tr><td align=center><font size='4'><B>Create an acount for Online Registration</B><BR><BR>
    <font size=2 color=red><?php print $error?></td></tr></table></font>
    <font face='Arial'><font size='2'><DIV align='center'><I><B>Personal Information<B></I></DIV>
    <table border="0" align='center'>
    <tr><td width=150>*First Name: </td><td>
    <input type="text" name="first" maxlength="20" value="<?print htmlentities($firstName, ENT_QUOTES);?>"></td></tr>
    <tr><td width=150>
    *Last Name: </td><td>
    <input type="text" name="last" maxlength="30" value="<?print htmlentities($lastName, ENT_QUOTES);?>"></td></tr>
    <tr><td width=150>*Email: </td><td>
    <input type="email" name="email" maxlength="50" value="<?print htmlentities($email, ENT_QUOTES);?>"></td></tr>
    <tr><td width=150>*Phone Number:<br><font size=2 color=red>eg. xxxxxxxxxx</td><td>
    <input type="number" name="phone" maxlength="15" align='left' value="<?print formatPhoneNumber($phoneNumber)?>"></td></tr>
    <tr><td width=150>*Sex:</td>
    <td align='center'><SELECT NAME='sex'>;
    <FONT FACE='verdana' SIZE=2><OPTION VALUE=''>Select One</OPTION></Font>
    <FONT FACE='verdana' SIZE=2><OPTION VALUE='Male' <?php print $maleSelected?>>Male</OPTION></Font>
    <FONT FACE='verdana' SIZE=2><OPTION VALUE='Female' <?php print $femaleSelected?>>Female</OPTION></Font></td></tr>
    </TABLE>
    <BR><BR>
    <font face='Arial'><font size='2'><DIV align='center'><I><B>Login Information</B></I></DIV>
    <TABLE align='center'><tr><td colspan=2 width=300 align=center><font size=2>
    Username and password must be between 6 and 16 characters. Please do not use spaces, quotes, or apostrophes.</td></tr>
    <tr><td colspan=2><br></td></tr>
    <tr><td width=150>*Username:</td><td>
    <input type="text" name="username" maxlength="16" value="<?print htmlentities($username, ENT_QUOTES);?>">
    </td></tr>
    <tr><td width=150>*Password:</td><td>
    <input type="password" name="pass" maxlength="16">
    </td></tr>
    <tr><td width=150>*Confirm Password:</td><td>
    <input type="password" name="pass2" maxlength="16">
    </td></tr>
    <tr><td colspan=2 align=center><font size=2>* - indicates required field</td></tr>
    <tr><th colspan=2><input type="submit" name="submit" value="Register"></th></tr> </table>
<?php }

$error='';
$maleSelected='';
$femaleSelected='';
$firstName = $_POST['first'];
$lastName = $_POST['last'];
$email = $_POST['email'];
$phoneNumber = $_POST['phone'];
$gender = $_POST['sex'];
$username = $_POST['username'];
$password = $_POST['pass'];
$password2 = $_POST['pass2'];
 
//This code runs if the form has been submitted
if (isset($_POST['submit'])) {
	checkInput(); //checks all the input for validity, if it's fine then $error will still be blank
	if((strlen($error)<=2)){
		registerUser(); //input was good, register the user
		header("Location: RegisteredNote.htm"); //If the user entered all fine input, thank them and dont reprint the form
	}
}
printForm(); //won't get here if good input is given and submit was pressed

