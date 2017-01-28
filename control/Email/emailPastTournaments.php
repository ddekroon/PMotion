<?php /*****************************************
File: emailPastTournaments.php
Creator: Derek Dekroon
Created: June 2/2013
Program to email past tournament captains. Not sure if it will get all players for a team or just the captain.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/emailJSFunctions.js'></script>";
$container = new Container('Email Past Tournaments', 'includeFiles/emailStyle.css', $javaScript);

require_once('includeFiles/emailPastTournamentFunctions.php');
require_once('includeFiles/emailSQLFunctions.php');
require_once('includeFiles/playerClass.php');

if(($tourneyID = $_GET['tourneyID']) == '') {
	$tourneyID = 0;
}
if(($year = $_GET['year']) == '') {
	$year = 0;
}
if(($orderBy = $_GET['orderBy']) == '') {
	$orderBy = '';
}
if(($direction = $_GET['direction']) == '') {
	$direction = 'ASC';
}

if (isset($_POST['delPlayers'])) {
	$array = ($_POST['deleteBox']);
	foreach($array as $index) {
		mysql_query("DELETE FROM $tournamentPlayersTable WHERE tournament_player_id = $index;")or die("Error: ".mysql_error());
	}
	$container->printSuccess("Players Successfully Deleted");
}

$tourneysDD = getTourneysDD($tourneyID);
$yearsDD = getYearsDD($tourneyID, $year);
if($tourneyID != 0 || $year != 0) {
	$numPlayers = getPastTournamentData($tourneyID, $year, $orderBy, $direction); 
}
?>

<form target="_blank" name='email' method='POST' action='confirmSend.php?emailTarget=5'>
	<?php printTeamHeader($tourneysDD, $yearsDD); ?>
	<div class='tableData'>
		<?php
		/* variable to keep track of number of emails printed*/
		$emailCount = "1";
        for($i=0;$i<$numPlayers;$i++) {
			/*prints 49 emails then prints || to show the division*/
			if($emailCount != "49"){
            	print ($playerArray[$i]->playerEmail . "\r\n");
				$emailCount++;
			}else{
				/* dividing every 49th email with '	|	|	'*/
				print ($playerArray[$i]->playerEmail);?> <br/><br/> <?php
				$emailCount = "1";
			}
        } ?>
        <br /><br />
		<table>
			<?php printPlayerHeader($tourneyID, $year, $direction);
			$player = 0;
			for($i=0;$i<$numPlayers;$i++) {
				printPlayerNode($playerArray[$i]);
			} ?>
			<?php printBottomButton($tourneyID); ?>
		</table>
	</div>


<?php $container->printFooter(); ?>