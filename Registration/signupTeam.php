<?php 
session_start();
ob_start();
require(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once('includeFiles/teamVariableDeclarations.php');
require_once('includeFiles/teamMailFunctions.php');
require_once('includeFiles/teamSqlFunctions.php');
require_once('includeFiles/teamFormFunctions.php');
require_once('includeFiles/teamButtons.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');

if(($userID = $_SESSION['userID']) == '') {
	$userID = 0;
}
$user = $_SESSION['username'];

$seasonQuery = mysql_query("SELECT * FROM $seasonsTable WHERE season_available_registration = 1 ORDER BY season_id ASC");
$numSeasons = 0;
while($seasonArray = mysql_fetch_array($seasonQuery)) {
	if($seasonArray['season_registration_by_sport'] == 1) {
		$sportsQuery = mysql_query("SELECT * FROM $sportsTable WHERE sport_id = $sportID") 
			or die('ERROR getting sport data - '.mysql_error());
		$sportArray = mysql_fetch_array($sportsQuery);
		$seasonData[$numSeasons]['regDue'] = date('F j, Y', strtotime($sportArray['sport_registration_due_date']));
	} else {
		$seasonData[$numSeasons]['regDue'] = date('F j, Y', strtotime($seasonArray['season_registration_due_by']));
	}
	$seasonData[$numSeasons++]['name'] = $seasonArray['season_name'];
	$seas_name = $seasonArray['season_name'];
	$curSeasonID = $seasonArray['season_id'];
}

$teamArray = query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
	WHERE team_id = $teamID");
$teamLeagueID = $teamArray['team_league_id'];
$teamName = $teamArray['team_name'];
$teamSeasonID = $teamArray['season_id'];
$teamUserID = $teamArray['team_managed_by_user_id'];
$isRegisteredDB = $teamArray['team_finalized'];
$payMethod = $teamArray['team_payment_method'];
$teamNumInLeague = $teamArray['team_num_in_league'];
//$aboutUsMethod = $teamArray['player_hear_method'];

if ($teamUserID != $userID && $teamUserID != '') {
	header('Location: securityWarning.htm');
}
if ($curSeasonID == $teamSeasonID && $curSeasonID != '') {
	$update = 1;
}
else {
	$update = 0;
}
declareSportVariables();

//If a button was pressed, populate the variables based on what was in the form.                                                
if (isset($_POST['register']) or isset($_POST['save']) or isset($_POST['update'])) {	
	$teamObj = getSubmittedValues($teamID);
}else{
	$teamObj = loadDefaultValues($teamLeagueID, $teamID, $teamName, $isRegisteredDB, $payMethod);
}
$oldTeams = getOldTeams($userID, $sportID);
$leaguesDropDown = getLeagueDD($teamObj->teamLeagueID, $sportID);
$payInfoDropDown = getPayInfoDD($teamObj->teamPayMethod,$seas_name);
$aboutUsDropDown = getAboutUsDD($aboutUsMethod);


//--- CALLS THE FUNCTIONS BASED ON WHAT BUTTON WAS CLICKED ---\\

if (isset($_POST['register'])) register($teamObj, $player, $userID);  //if register button is pressed, execute register function
elseif(isset($_POST['save'])) save($teamObj, $player, $userID);   	//if save button is pressed, execute save function
elseif(isset($_POST['update'])) updateTeam($teamObj, $player, $userID);    	//update das team ?>

<html>
	<head>
    	<title><?php print $titleHeader?></title>
        <link rel="stylesheet" type="text/css" href="includeFiles/design.css"/>
        <script type="text/javascript" src="includeFiles/teamJavaFunctions.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript">
			$(document).ready(function() {
				$('#changeLeague').on('change', function() {
					console.log($(this).find(':selected').attr('fee'));
					$('#regFeeLabel').html($(this).find(':selected').attr('fee'));
				});
			});
		</script>
    </head>
    <body>
        <br /><font face='arial' size=2>User: <B><?php print $user;?></B></font>
        <font face='verdana' size=1>(<a href=/Login/logout.php style='text-decoration: none;'>Logout</a>)</font>
        <form id='update' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']?>?sportID=<?php print $sportID?>&teamID=<?php print $teamID?> onSubmit="setBlanks()" />
        	<table class='master' align=center>
            	<?php printJavaScript();
				printFormHeader($logo, $sportHeader);
				printOldTeamList($oldTeams, $seas_name);
				printLeagueAndTeam($teamObj, $leaguesDropDown);
				printCaptainForm($player);
				printPlayerForm($player, $people);
				printFormCommentsAndButtons($teamObj, $player, $update, $payInfoDropDown, $aboutUsDropDown); ?>
            </table>
        </form>
    </body>
</html>
<?php ob_flush();?>