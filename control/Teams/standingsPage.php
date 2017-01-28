<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* August 20, 2013
* standingsPage.php
*
* Will be linked from spirit control panel. Shows the league standings page for a league selected.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$container = new Container('League Standings'); 

if(($leagueID = $_GET['leagueID']) == '') {
	$container->printError('No league specified');
	exit(0);
}

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Search'.
	DIRECTORY_SEPARATOR.'standings.php');