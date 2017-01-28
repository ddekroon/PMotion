<?php
//This program checks to see if the session that's viewing a password protected page has logged in, if not it sends them to the login screen

global $control;

$userName = $_SESSION['username'];

$admin_usernames = array('dm06tw', 'zachwilks', 'davekelly', 'ddekroon', 'userAdmin', 'aeckensw');

if($control == 0 && !isset($userName)){
	session_unset (); //so destroy whatever session there was and bring them to login page
	session_destroy ();
	header('Location: http://data.perpetualmotion.org/Login/');
	
} else if($control == 1 && !in_array($userName, $admin_usernames)) {
			
	session_unset(); //so destroy whatever session there was and bring them to login page
	session_destroy();
	header ('Location: http://data.perpetualmotion.org/Login/index.php?siteDirect=control');
}?>
