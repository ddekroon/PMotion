<?php /*****************************************
File: emailCaptains.php
Creator: Derek Dekroon
Created: July 4/2012
With this program you can select captains to email
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/emailJSFunctions.js'></script>";
$container = new Container('Email Captains', 'includeFiles/emailStyle.css', $javaScript);

require_once('includeFiles/emailPlayerForm.php');
require_once('includeFiles/emailSQLFunctions.php');
require_once('includeFiles/playerClass.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($dayNumber = $_GET['dayNumber']) == '') {
	$dayNumber = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($orderBy = $_GET['orderBy']) == '') {
	$orderBy = '';
}
if(($direction = $_GET['direction']) == '') {
	$direction = 'ASC';
}
if(($emailTarget = $_GET['emailTarget']) == '') {
	$emailTarget = 0;
}

$sportsDD = getSportDD($sportID);
$seasonsDD = getSeasonDD($seasonID);
$leaguesDD = getLeaguesDD($sportID, $seasonID, $leagueID);
$daysDD = getDayNumDD($dayNumber);

$numPlayers = getCaptainData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction); 
$emailTarget = 1; ?>

<form target="_blank" name='email' method='POST' action='confirmSend.php?emailTarget=1'>
	<?php printTeamHeader($sportsDD, $leaguesDD, $daysDD, $seasonsDD, 1); ?>
	<div class='tableData'>
		<table>
			<?php printPlayerHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction);
			$player = 0;
			for($i=0;$i<$numPlayers;$i++) {
				printPlayerNode($i);
			} ?>
		</table>
	</div>
</form>
<?php $container->printFooter(); ?>