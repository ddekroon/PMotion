<?php  /********************
* Derek Dekroon
* ddekroon@uoguelph.ca
* July 4, 2012
* This program will reset a users user name or password based on what the inputs from $_GET are.
* This program comes after resetAccount, which is poorly named. Just know this one actually changed stuff in the database.
***************************/

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');

function getError($type, $dbVerifyCode) {
	global $userTable;
	$error = '';
	if($type == 'Password'){
		 //checks that the password is between 6 and 16 characters
		 if((strlen($_POST['pass']) <= 5)||(strlen($_POST['pass']) >= 17)){
			$error.="<BR>Password must be between 6 and 16 characters.";
		 }
	
		 // this makes sure both passwords entered match
		 if ($_POST['pass'] != $_POST['pass2']) {
			$error.='<BR>Your passwords did not match.';
		 }
	} elseif ($type == 'Username'){
		 // checks if the username is in use
		 
		 if (!get_magic_quotes_gpc()) {
			$userCheck = mysql_escape_string($_POST['newUser']);
		 } else {
			$userCheck = $_POST['newUser'];
		 }
		 $checkQuery = mysql_query("SELECT * FROM $userTable WHERE user_username = '$userCheck'") or die(mysql_error());
	
		 //if the name exists it gives an error
		 if (mysql_num_rows($checkQuery) != 0) {
			$error.='<BR>The username "'.$_POST['newUser'].'" is already in use.';
		 }
		 //checks that the user name is between 6 and 16 characters
		 if((strlen($_POST['newUser']) <= 5)||(strlen($_POST['newUser']) >= 17)){
			$error.="<BR>Username must be between 6 and 16 characters.";
		 }
	}
	// this makes sure validation key entered matches the database
	if ($_POST['validation'] != $dbVerifyCode) {
		$error.='<BR>Your validation key did not match the assigned validation key.';
	}
	return $error;
}

function mailBody($userName, $type, $password){
	if($type == "Password"){
		$message="** Password Reset **
		<BR><BR>Below is your new login information:
		<BR><BR>Username: <B>$userName</B>
		<BR>Password: <B>$password</B>
		<BR><br>
		Thanks,
		<BR>The Perpetual Motion Team.";
	} elseif ($type == "Username"){
		$message="** User Name Reset **
		<BR><BR>Below is your new user name information:
		<BR><BR>Username: <B>$userName</B>
		<BR><br>
		Thanks,
		<BR>The Perpetual Motion Team.";
	}
	return $message;
}//end function body

function printForm($userID, $type, $userEmail, $title, $userName) { ?>
    <html>
    	<head>
        	<title>
            	<?php print $title ?>
			</title>
        </head>
        
        <style>
		.Table
		{
			display: table;
			width:600px;
			text-align:center;
			margin:0px auto;
		}
		.Heading
		{
			display: table-row;
			font-weight: bold;
			text-align: center;
			width:900px;
			margin:0px auto;
		}
		.Row
		{
			display: table-row;
			width:500px;
			margin:0px auto;
			padding:5px;
		}
		.colourRow
		{
			display: table-row;
			width:500px;
			height:15px;
			margin:0px auto;
			padding:5px;
			background:#CCCCCC;
		}
		.Column
		{
			width:auto;
			display: table-cell;
			margin:0px auto;
			padding:5px;
	
		}
		.Cell
		{
			width:500px;
			height:25px;
			display: table-cell;
			text-align:center;
			border: solid;
			border-width: medium;
			margin:0px auto;
		}
		.noBorderCell
		{
			width:500px;
			display: table-cell;
			margin:0px auto;
		}
	</style>
        
        <body>
        	<FORM NAME='reset' action='<?php print $_SERVER['PHP_SELF'].'?userID='.$userID.'&type='.$type.'&userEmail='.$userEmail ?>' method='post'>
        	<font size=3 face='arial'>
        	<div class="Table" align="center">
            	<div class="Row">
					<?php print "Confirm $reasonActual - Perpetual Motion's Online Registration System"; ?>
                </div>
				<div class="Row">
                    <img src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'>
                </div>
                <div class="Row">
                    <font size=4><B><?php print $title ?></B></font>
                </div>
                <div class="Row">
                    <?php print "Set New $type" ?>
                </div>
                <div class="Row" align="center">
                    <div class="noBorderCell">
                        <div class="Row">   	
                        <?php if ($type == 'Username') { ?>
							<div class="Column" align="left">
                                <B>Current Username:</b>
                            </div>
                            <div class="Column">
                                <input type='text' name='user' maxlength='16' value='<?php print $userName ?>' disabled>
                            </div>
                        </div>
                        <div class="Row">
                            <div class="Column" align="left">
                                <B>New User Name:</B>
                            </div>
                       		<div class="Column">
                            	<input type='text' name='newUser' maxlength='16'>
                        	</div>
                        </div>
						<?php } else { ?>
							<div class="Column" align="left">
                                <B>Username:</b>
                            </div>
                            <div class="Column" align="left">
                                <?php print $userName ?>
                            </div>
     					</div>
                        <div class="Row">
                            <div class="Column" align="left">
                            	<B>New Password:</B>
                            </div>
                            <div class="Column">
                                <input type='password' name='pass' maxlength='16'>
                            </div>
                        </div>
     					<div class="Row">
                            <div class="Column" align="left">
                            	<B>Confirm Password:</B>
                            </div>
                            <div class="Column">
                                <input type='password' name='pass2' maxlength='16'>
                            </div>
                        </div>
						<?php }?>
						<div class="Row">
                           	<div class="Column" align="left">
                               	<B>Validation Key:</b><br />
                                <font size=1>Refer to email, case sensitive</font>
                            </div>
							<div class="Column">
                               	<input type='text' name='validation' maxlength='6'>
                            </div>
                        </div>
                    </div>
				</div>               
				<div class="Row">
                	<input type='submit' name='submit' value='Submit'>
                </div>
            </div>
                   
        </font>
        </FORM>
        </body>
	</html>
<?php }

if(($userID = $_GET['userID']) == '') { 
	$userID = 0;
}
if(($type = $_GET['type']) == '') {
	$type = 'unknown';
}
if(($userEmail = $_GET['userEmail']) == ''){
	$userEmail = 'unknown';
}

$userQuery = mysql_query("SELECT * FROM $userTable WHERE user_id = $userID") or die('ERROR getting user information - '.mysql_error());
$userArray = mysql_fetch_array($userQuery);
$dbVerifyCode = $userArray['user_verify_code'];
$userName = $userArray['user_username'];
    
if($type == 'Password'){
     $title="Password Change / Update Function";
     $subject="Password Changed";
     $reasonActual="Reset Password";
     $item="Password";
} elseif($type == 'Username'){
     $title="User Name Change / Update Function<BR>
     <font size=2>Note: Please use a memorable user name, the login is case sensitive.<font>";
     $subject="User Name Changed";
     $reasonActual="Reset User Name";
     $item="User Name";
}

if(isset($_POST['submit'])){
	$valSubmitted = $_POST['validation'];
	$error = getError($type, $dbVerifyCode);
	
	if(strlen($error) < 3 && $type=='Password'){
		
		 $passwordEmail = $_POST['pass'];
		 print($passwordEmail);
		 $password = md5($_POST['pass']);
		 print($password);
		 $update=mysql_query("UPDATE $userTable SET user_password='$password' WHERE user_id = $userID");
		 $subject = $subject." - ".$userName;
		 $body = mailBody($userName, $type, $passwordEmail);
		 
		 sendEmails(array($userEmail,), 'info@perpetualmotion.org', $subject, $body);
		 header("Location: passwordUpdated.htm");
	}//end if
	elseif(strlen($error) < 3 && $type == 'Username'){
		
		 $newUserName = $_POST['newUser'];
		 
		 $update=mysql_query("UPDATE $userTable SET user_username = '$newUserName' WHERE user_id = $userID");
		 $subject = $subject." - ".$newUserName;
		 $body = mailBody($newUserName, $type, $passwordEmail);
		 
		 sendEmails(array($userEmail,), 'info@perpetualmotion.org', $subject, $body);
		 header("Location: userNameUpdated.htm");
	} else {
		print $error;
		printForm($userID, $type, $userEmail, $title, $userName);
	}//end else
} else{ //if nothing was selected 
	printForm($userID, $type, $userEmail, $title, $userName);
}