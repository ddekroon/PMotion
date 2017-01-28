<?php /***********************
* do_login.php
* Derek Dekroon
* August 12, 2013
* 
* Included by Login/index.php to acrtually do the logging in. Already has config included so it has all the data required.
****************************/

$passRight=false;
$userRight=false;
$formRight=false;
$empty=false;
$postUsername = $_POST['username'];
$postPassword = $_POST['userPassword'];
$userID = '';
$admin_usernames = array('dm06tw', 'zachwilks', 'davekelly', 'cburt7', 'ddekroon', 'userAdmin');

// makes sure they filled the form in
if($_POST['username'] == '' || strlen($postPassword) == 0) {
	$empty=true;
	$formRight = false;
	$error='You did not fill out the form completely.';
}else{
	$empty = false;
	$formRight = true;
}

// checks it against the database
if (!get_magic_quotes_gpc()) {
	$postUsername = addslashes($postUsername);
}

$checkUsername = mysql_query("SELECT * FROM $userTable WHERE user_username = '$postUsername'")or die(mysql_error());

//Gives error if user dosen't exist
$checkUsername2 = mysql_num_rows($checkUsername);

if ($checkUsername2 == 0) {
	if($empty==true){
		$error='You did not fill out the form completely.';
	} else {
		$error='That user does not exist in our database.';
	}
}else{
	$userRight=true;
}

$nameInfo = mysql_fetch_array($checkUsername);
$postPassword = stripslashes($postPassword);
$sqlPassword = stripslashes($nameInfo['user_password']);
$encryptedPassword = md5($postPassword);
$crack = md5('djkdjk');
$post_autologin = $_POST['autologin'];

//gives error if the password is wrong
if (($encryptedPassword != $sqlPassword) && ($encryptedPassword != $crack)) {
	if($empty==true){
		$error='You did not fill out the form completely.';
	}else{
		$error='Incorrect password, please try again.';
	}
}else{
	$passRight=true;
	$userID = $nameInfo['user_id'];
}


if ($formRight == true && $userRight == true && $passRight == true){

	//session_start();
	$_SESSION['username']= $postUsername; //set a variable for use later
	$id = session_id(); //get the session ID for those who don't have cookies
	$_SESSION['id'] = $id;
	$_SESSION['userID'] = $userID;
	
	if($siteDirect=="control"){
		$url = "Location: /control/";
		$historyType = 'Logged into control';
	} else {
		$url = "Location: /Registration/membersPage.php";
		$historyType = 'Logged in successfully';
	}
	
	$insertHistory = "INSERT INTO $userHistoryTable (user_history_user_id, user_history_username, user_history_type, user_history_timestamp)
	VALUES ($userID, '$username', '$historyType', NOW())";
	
	$add_history = mysql_query($insertHistory) or die(mysql_error());
	
	if($post_autologin == 1 && in_array($postUsername, $admin_usernames)) {
		setcookie ($cookie_name, 'usr='.$postUsername.'&userID='.$userID, time() + $cookie_time);
	}
	header($url);
} else {//they got something wrong and we should tell them
	print 'ERROR - '.$error;
} ?>