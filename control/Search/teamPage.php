<?php /*****************************************
File: submitTournament.php
Creator: Derek Dekroon
Created: July 5/2012
Allows a user to edit tournament parameters
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
require_once('includeFiles/submissionClass.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');
require_once('includeFiles/tmpgVariableDeclarations.php');
require_once('includeFiles/tmpgFormFunctions.php');

$javaScript = "<script language='text/javascript'>	
	function checkYesNo() {
		return confirm('Are you sure you want to delete these score submissions?');
	}
</script>";

if(($teamID = $_GET['teamID']) == '') {
	$leagueID = 0;
	$teamID = 0;
	exit('No Team ID');
} else {
	$leagueQuery = mysql_query("SELECT team_league_id, team_name FROM $teamsTable WHERE team_id = $teamID") 
		or die('ERROR getting team data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	$leagueID = $leagueArray['team_league_id'];
	$teamName = $leagueArray['team_name'];
}


$container = new Container($teamName.' Team Page', 'includeFiles/searchStyle.css', $javaScript);

if(isset($_POST['deleteScores'])) {
    deleteScoreSubmissions($teamID, $leagueID);
}

$numTeams = getTeamsData($teamID);
$submissionNum = getSubmissionData($teamID);
$oppSubmissionNum = getOppSubmissionData($teamID);

$teamQuery = mysql_query("SELECT league_num_of_games_per_match, league_sport_id, league_id, team_num_in_league, league_pic_link, team_pic_name FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
	WHERE team_id = $teamID") or die('ERROR getting team picture - '.mysql_error());
	
$teamArray = mysql_fetch_array($teamQuery);
$games = $teamArray['league_num_of_games_per_match'];
$sportID = $teamArray['league_sport_id'];
$leagueID = $teamArray['league_id'];
$teamNum = $teamArray['team_num_in_league'];
$leaguePicLink = $teamArray['league_pic_link'];
$teamPicName = $teamArray['team_pic_name'];

$picExists = 0;
$filePath = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$leaguePicLink.DIRECTORY_SEPARATOR.$teamPicName;
if(file_exists($filePath.'.JPG') || file_exists($filePath.'.jpg')) {
	$picLink = '/allSports/TeamPictures/archivePicturePage.php?teamID='.$teamID; 
	$picExists = 1;
} ?>
<form id="teamForm" method="post" action=<?php print $_SERVER['PHP_SELF']."?teamID=$teamID"?>>
	<?php printPageHeader($teamObjs, $teamID); ?>
	<div class='tableData'>
		<table>
			<?php printSubmissionsHeader();
			for($i=0;$i<$submissionNum;$i+=$games) {
				printSubmissionNode($submission, $i, $games, $teamObjs, $numTeams, $sportID);
			} ?>
			<tr>
				<td>
					<input type='submit' name="deleteScores" value="Delete" onclick="return checkYesNo()" />
				</td><td colspan="6">
					<br />
				</td>
			</tr>
		</table>
    </div>
	<div class='tableData'>
		<table>
			<?php printOppSubmissionsHeader();
			for($i=0;$i<$oppSubmissionNum;$i+=$games) {
				printOppSubmissionNode($oppSubmission, $i, $games, $teamObjs, $numTeams, $sportID);
			} ?>
        </table>
    </div>
    
    <?php $numPlayers = getPlayerData($sportID, $leagueID, $teamID, $seasonID); ?>
    
	<div class='tableData'>
        <table align="center">
            <?php printPlayerHeader($teamObjs);
            $player = 0;
            for($i=0;$i<$numPlayers;$i++) {
                printPlayerNode($i);
            } ?>
        </table>
    </div>
    
    <?php require_once('standings.php'); ?>
    
</form>

<?php $container->printFooter(); ?>