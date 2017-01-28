<?php

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

//Gets whatever argument you pass into the program for sport ("?sportID=1" etc)
if(($seasonID = $_GET['seasonID']) == ''){
    $seasonID = 0;
}
if(($sportID = $_GET['sportID']) == ''){
    $sportID = 0;
}
if (($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

function getPostData() {
	global $teamName, $playerObj, $teamComments, $numPeople;

	$numPeople = 0;
	$teamName = $_POST['teamName'];

	while(strlen($_POST['playerFirst'][$numPeople]) > 0) {
		$playerObj[$numPeople] = new Player();
		$playerObj[$numPeople]->playerFirstName = $_POST['playerFirst'][$numPeople];
		$playerObj[$numPeople]->playerLastName = $_POST['playerLast'][$numPeople];
		$playerObj[$numPeople]->playerEmail = $_POST['playerEmail'][$numPeople];
		$playerObj[$numPeople]->playerGender = $_POST['playerSex'][$numPeople];
		$playerObj[$numPeople]->playerPhone = preg_replace("/\D/",'',$_POST['playerPhone'][$numPeople]);
		$numPeople++;
	}
	$teamComments=$_POST['comments'];
}
