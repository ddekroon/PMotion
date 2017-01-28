<?php /********************
* Derek Dekroon
* ddekroon@uoguelph.ca
* July 4, 2012
* This program shows up when a user clicks either 'forgot name' or 'forgot password' on the login page
* A textbox shows up for the user to enter their email, and based on which link was clicked (input from $_GET), an email is sent out with the info required to change their information.
***************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');

function gen($start, $end){	
	srand ((double) microtime( )*1000000);
	$random_number = rand($start,$end);
}//end function gen

function mailBody($user, $type, $emailAddress, $userID){
	global $userTable;
	$updateCode = chr(rand(65,90)); //Capital letter
	$updateCode .= chr(rand(65,90)); //Capital letter
	$updateCode .= chr(rand(97,122)); //Small letter	
	$updateCode .= chr(rand(48,57));  //number
	$updateCode .= chr(rand(48,57));  //number
	$updateCode .= chr(rand(97,122)); //small letter
	$userQuery=mysql_query("UPDATE $userTable SET user_verify_code = '$updateCode' where user_id = $userID") or die('ERROR updating verify code - '.mysql_error());

	if($type == "Password"){
		$ttl="PASSWORD CHANGE / RESET INSTRUCTIONS";
	} elseif ($type == "Username") {
		$ttl="USER NAME CHANGE / RESET INSTRUCTIONS";
	}
	
	$message="** - $ttl - **
	<BR><BR>To reset your user information, please click the link below and fill in the required information.
	<BR><BR>User Name: <B>$user</B>
	<BR>Validation Code: <B>$updateCode</B>
	<BR><br>
	<a href=http://data.perpetualmotion.org/Login/resetAccountInfo.php?userID=$userID&type=$type&userEmail=$emailAddress>
	http://data.perpetualmotion.org/Login/resetAccountInfo.php?userID=$userID&type=$type&userEmail=$emailAddress</a>
	<BR><BR>
	Thanks,
	<BR>The Perpetual Motion Team.";
	
	return $message;
}//end function body 

function printForm($title, $reasonActual, $type) { ?>
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
     		<FORM NAME='resetEmail' METHOD='POST' action='<?php print $_SERVER['PHP_SELF'].'?type='.$type ?>'>
            	<font size=3 face='arial'>
                <div class="Table">
                	<div class="Row">
                    	
     						<?php print $reasonActual.' - Perpetual Motion\'s Online Registration System'; ?>
                        
                    </div>     
     				<div class="Row">
                        	<img src='/Logos/Perpetualmotionlogo2.jpg' width='300' height='130'>
                    </div>
     				<div class="Row">
                    	
                       		<font size=4><B><?php print $title ?></B></font>
                        
                    </div>
     				<div class="Row">
                    	
                        	Please enter the email address you used when registering to receive further instructions
                        
                    </div>
     				<div class="Row" align="center">
                    	<div class="noBorderCell">
                        	<div class="Row" align="center">
                        		<div class="Column" align="left">
                                	<b>Reason:</b>
                           		</div>
                            	<div class="Column" align="left">
                                   	<input type='text' name='reasonActual' maxlength='50' value='<?php print $reasonActual?>' disabled>
                            	</div>
                        	</div>
                        	<div class="Row" align="center">
                            	<div class="Column" align="left">
                            		<B>Email Address:</b>
                            	</div>
                            <div class="Column" align="left">
                            	<input type='text' name='email' maxlength='50'>
                            </div>
                        </div>
                    </div>   
     				<div class="Row" align="center">
                        	<input type="submit" name="submit" value="Submit">
                    </div>
     			<input type='hidden' name='type' maxlength='50' value='<?php print $type?>'>
            	</font>
            </FORM>
        </body>
     </html>
<?php }

if(($type = $_GET['type']) == '') {
	$type = 'unknown';
}

if($type == 'Password'){
     $title = 'Password Change / Update Function';
     $subject = 'Password Change Instructions';
     $reasonActual = 'Reset Password';
}elseif($type == 'Username'){
     $title = 'User Name Change / Update Function';
     $subject = 'User Name Change Instructions';
     $reasonActual = 'Reset User Name';
} 

if(!isset($_POST['submit'])){ 
	printForm($title, $reasonActual, $type); ?>
<?php } else if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$email = $_POST['email'];
	$userQuery = mysql_query("SELECT * FROM $userTable WHERE user_email='$email'") or die('ERROR gettin user - '.mysql_error());;
	
	if(mysql_num_rows($userQuery) > 0){
		$userArray = mysql_fetch_array($userQuery);
		$userID = $userArray['user_id'];
        $dbUsername = $userArray['user_username'];
        $subject = $subject.' - '.$dbUsername;
        $body = mailBody($dbUsername, $type, $email, $userID);
		
		sendEmailsBcc(array($email), 'info@perpetualmotion.org', $subject, $body);
        header("Location: resetInfoSent.htm");
     } else {
		print 'Error: Email not registered in our database';
		printForm($title, $reasonActual, $type);
     }
}//end if isset

?>
