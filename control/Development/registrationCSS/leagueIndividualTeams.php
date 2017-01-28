<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');
require_once('includeFiles/indivFormFunctions.php');
require_once('includeFiles/indivVariableDeclarations.php');

if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

$leagueQuery = mysql_query("SELECT league_name, league_day_number, sport_name, league_sport_id FROM $leaguesTable INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
    WHERE league_id = $leagueID") or die('ERROR - '.mysql_error());
$leagueArray = mysql_fetch_array($leagueQuery);
$leagueName = $leagueArray['league_name'].' '.dayString($leagueArray['league_day_number']);
$sportName = $leagueArray['sport_name'];
$sportID = $leagueArray['league_sport_id'];

declareSportVariables();

$teamNum = getDatabaseTeams($leagueID); //the first number is sportID, no clue why it's needed but its useless so i just send through 2
?>

<html>
	<head>
    	<title><?php print $titleHeader?></title>
        <link rel="stylesheet" type="text/css" href="includeFiles/indivStyle.css"/>
    </head>
    <body>
    	<?php for($i = 0; $i< $teamNum; $i++) { ?>
        	<div class="print">
                <table style="width:100%;">
                    <?php printFormHeader($logo, $sportHeader);
                    printLeagueAndTeam($i, $leagueName);
                    printPlayerForm($players, $people, $i); ?>
                </table>
                Please feel free to add your email and phone number so you can be in contact with each other.
            </div>
            
        <?php } ?>
    </body>
</html>
