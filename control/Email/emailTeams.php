<?php /*****************************************
File: emailTeams.php
Creator: Derek Dekroon
Created: July 3/2012
With this program you can select players to email team by team
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/emailJSFunctions.js'></script>";
$container = new Container('Email Teams', 'includeFiles/emailStyle.css', $javaScript);


require_once('includeFiles/emailTeamForm.php');
require_once('includeFiles/emailSQLFunctions.php');
require_once('includeFiles/playerClass.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($teamName = $_GET['teamName']) == '') {
	$teamName = '';
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
$leaguesDD = getLeaguesDD($sportID, $seasonID, $leagueID);
$daysDD = getDayNumDD($dayNumber);
$seasonsDD = getSeasonDD($seasonID);
if($sportID != 0 || $leagueID != 0 || $dayNumber != 0) {
	$numPlayers = getPlayerData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction); 
}

?>

<form target="_blank" name='email' method='post' action='confirmSend.php?emailTarget=2'>
	<?php printTeamHeader($sportsDD, $leaguesDD, $daysDD, $seasonsDD); 
    if($teamName!="") {
	printTeamEmails($teamName, $numPlayers);
}?>
	<div class='tableData'>
		<table>
			<?php printPlayerHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction);
			$teamNum = 0;
			$player = 0;
			for($i=0;$i<$numPlayers;$i++) {
				printPlayerNode($i);
			} ?>
		</table>
	</div>
</form>
<?php $container->printFooter(); ?>