<?php 
ob_start();
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once('includeFiles/tourneyVariableDeclarations.php');
require_once('includeFiles/tourneyMailFunctions.php');
require_once('includeFiles/tourneySqlFunctions.php');
require_once('includeFiles/tourneyFormFunctions.php');
require_once('includeFiles/tourneyErrorFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');
require_once('includeFiles/tournamentClass.php');

$tourneyObj = declareTournamentVariables($tourneyID);
$teamObj = new Team();
if(($teamObj->teamLeagueID = $_GET['leagueID']) == '') {
	$teamObj->teamLeagueID = 10000;
}

//If a button was pressed, populate the variables based on what was in the form.                                                
if (isset($_POST['register'])) {	
	$teamObj = getSubmittedValues();
	if($tourneyObj->tourneyIsTeams == 1 && checkTeamErrors($tourneyObj, $teamObj, $player) == 1) {
		$teamID = registerTeam($teamObj, $player, $tourneyObj, $extraPlayersObj);
		mailRegTeam($teamID, $teamObj, $player, $tourneyObj, $extraPlayersObj);
		header('Location: thankYouTournament.php?tourneyID='.$tourneyID);
	}
	if(($tourneyObj->tourneyIsCards == 1 && checkCardErrors($tourneyObj, $teamObj, $player) == 1)
		|| ($tourneyObj->tourneyIsPlayers == 1 && checkPlayerErrors($tourneyObj, $teamObj, $player) == 1)) {
		$playerID = registerPlayer($teamObj, $player, $tourneyObj, $extraPlayersObj);
		mailRegPlayer($playerID, $teamObj, $player, $tourneyObj, $extraPlayersObj);
		$leagueID = $teamObj->teamLeagueID;
		header('Location: thankYouTournament.php?tourneyID='.$tourneyID.'&leagueID='.$leagueID);
	}
	if(isset($_POST['checkBox'])) {
		$addEmail=$playersArray[$i]->playerEmail;
		$addQuery = "INSERT INTO $addressesTable (EmailAddress)  VALUES ('$addEmail');";
		mysql_query($addQuery);
	}
}

$divisionDropDown = getDivisionDD($tourneyObj, $teamObj);
$paymentDropDown = getPayInfoDD($teamObj->teamPayMethod);
$aboutUsDropDown = getAboutUsDD($teamObj->aboutUsMethod); 
if($tourneyObj->tourneyIsCards == 1) {
	if($teamObj->teamLeagueID == 10000) {
		$numBlackCards = max($tourneyObj->tourneyNumBlackCards);
		$numRedCards = max($tourneyObj->tourneyNumRedCards);
	} else {
		$numBlackCards = $tourneyObj->tourneyNumBlackCards[$teamObj->teamLeagueID];
		$numRedCards = $tourneyObj->tourneyNumRedCards[$teamObj->teamLeagueID];
	}
	$cardsDropDown = getPlayerCardDD(100, $numBlackCards, $tourneyObj, $teamObj->teamLeagueID);
	$cardsDropDown .= getPlayerCardDD(300, $numRedCards, $tourneyObj, $teamObj->teamLeagueID);
} ?>

<html>
	<head>
    	<title>Register - <?php print $tourneyObj->tourneyName ?></title>
        <link rel="stylesheet" type="text/css" href="includeFiles/design.css"/>
        <script type="text/javascript" src="includeFiles/tourneyJavaFunctions.js"></script>
    </head>
    <body>
        <form id='tourneyReg' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']?>?tournamentID=<?php print $tourneyID?>>
        	<?php printHiddenValues($tourneyObj); ?>
        	<table class='master' align=center>
            	<?php printFormHeader($tourneyObj);
				printDivisionDD($divisionDropDown, $tourneyObj);
				if($tourneyObj->tourneyIsTeams == 1) {
					printTeamForm($teamObj);
				}
				printCaptainForm($player, $tourneyObj->tourneyIsCards, $cardsDropDown);
				if($tourneyObj->tourneyIsExtraField == 1) {
					printExtraField($tourneyObj);
				}
				if($tourneyObj->tourneyRegIsPlayers == 1) {
					printPlayerFields($tourneyObj->tourneyRegNumPlayers, $extraPlayersObj);
				}
				printFormCommentsAndButtons($teamObj, $player, $paymentDropDown, $tourneyObj, $aboutUsDropDown); ?>
            </table>
        </form>
    </body>
</html>
<?php ob_flush();?>
