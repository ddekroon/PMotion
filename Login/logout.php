<?php
//let's completely teminate the session and bring them to login page
session_start(); //yes, you still have to start the session
session_unset();
session_destroy();
if($_GET['siteDirect'] != '') {
	header('Location: /Login/index.php?siteDirect=control');
} else {
	header('Location: /Login/');
}
?>
