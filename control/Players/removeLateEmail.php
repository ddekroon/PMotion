<?php  /*****************************************
File: removeLateEmail.php
Creator: Alex Eckensweiler
Created: July 1st/2014
Allows teams to opt out of receiving emails if they haven't submitted their scores on time
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/emailJSFunctions.js'></script>";
$container = new Container('Cancel Team Late Email', 'includeFiles/emailStyle.css', $javaScript);

require_once('includeFiles/latePlayerForm.php');
require_once('includeFiles/lateSQLFunctions.php');
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

$sportsDD = getSportDD($sportID);
$seasonsDD = getSeasonDD($seasonID);
$leaguesDD = getLeaguesDD($sportID, $seasonID, $leagueID);
$daysDD = getDayNumDD($dayNumber);

$numPlayers = getCaptainData($sportID, $leagueID, $dayNumber, $seasonID, $orderBy, $direction); 

if (isset($_POST['Submit'])) {
	if(($checkBoxes = $_POST['checkBox']) != '') {
		$numEmails = 0;
		foreach($_POST['checkBox'] as $checkBox) {
			$teamID[$numEmails] = $_POST['teamID'][$checkBox];
			$teamIDs = $teamID[$numEmails];
			// Sets column to 1 if 0, and 0 if 1
			mysql_query("UPDATE $teamsTable  
			SET team_late_email_allowed = IF (team_late_email_allowed = 0, 1, IF (team_late_email_allowed = 1, 0, team_late_email_allowed)) 
			WHERE team_id = $teamIDs") or die('ERROR getting schedule data '.mysql_error());
			$numEmails++;
		}
	}
}
?>

<form name='email' method='POST' action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&leagueID='.$leagueID.'&teamID='.$teamID ?>>
	<?php printTeamHeader($sportsDD, $leaguesDD, $daysDD, $seasonsDD, 1); ?>
	<div class='tableData'>
		<table>
			<?php printPlayerHeader($sportID, $leagueID, $dayNumber, $seasonID, $direction);
			global $playerArray;
			$player = 0;
			for($i=0;$i<$numPlayers;$i++) {
				printPlayerNode($i);
			} ?>
		</table>
    </div>
	
</form>
<?php $container->printFooter(); ?>