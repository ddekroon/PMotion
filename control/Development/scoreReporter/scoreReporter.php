<?php

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once('includeFiles/scrVariableDeclarations.php');
require_once('includeFiles/scrFormFunctions.php');
require_once('includeFiles/scrErrorFunctions.php');
require_once('includeFiles/scrSqlFunctions.php');
require_once('includeFiles/scrMailFunctions.php');
$sportID=3;
loadDefaults($sportID);
if($leagueID != 0) {
	$weekNum = setLeagueWeek($leagueID);
}
if(!isset($_POST['Submit'])) {
//all in declarations.php
	loadVariables($sportID, $leagueID, $teamID);
} else {
	getSubmittedData();
}
getLeagueDD($leagueID, $sportID);
getTeamDD($leagueID, $teamID, $oppTeamID);
if (isset($_POST['Submit'])) {
	if (($error = checkForErrors()) == '') {    //no errors, submit data 
		/*sendScoreEmails($leagueID, $teamID); //sends score reporter emails to admins, leave this before submitting scores 
 		$ignored = submitScores();
		if ($ignored == 0 && $isPlayoffs == 0) {
			updateStandings($teamID, $matches, $games, $gameResults); //wins, lossees, ties
		}
		//header("Location: thankYouReport.php?sportID=".urlencode($sportID).'&leagueID='.urlencode($leagueID).'&teamID='.urlencode($teamID));
		sendEmails(array("aeckensw@uoguelph.ca"), "alex@perpetualmotion.org", "Test","Did It Get Through?");*/
	}
} ?>

<!doctype html public '-//w3c//dtd html 3.2//en'>
<html>        
    <head>
       <!-- Title for the browser --> 
        <title>Submit Results for Perpetual Motion</title>
       <!-- This is the javascript file that makes all of the dropdowns and reloads work properly -->
        <script type='text/javascript' src='includeFiles/scrJavaFunctions.js'/></script>
        <!-- This is the css stylesheet that makes the form appear like it should -->
        <link rel='stylesheet' type='text/css' href='includeFiles/scrStyles<?php print $sportID ?>.css'/>
	</head>
    <body>
        <FORM id='scoreReporterID' name="scoreReporter" method='POST' action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&leagueID='.$leagueID.'&teamID='.$teamID ?> 
        	onSubmit="return checkFields(<?php print $matches.','.$games?>)">
        <INPUT TYPE='hidden' NAME='sportID' VALUE=<?php print $sportID?>>
        <INPUT TYPE='hidden' NAME='matches' VALUE=<?php print $matches?>>
        <INPUT TYPE='hidden' NAME='games' VALUE=<?php print $games?>>
        <INPUT TYPE='hidden' NAME='teamName' VALUE='<?php print $teamName?>'>
        <INPUT TYPE='hidden' NAME='leagueName' VALUE="<?php print $leagueName?>">
        <INPUT TYPE='hidden' NAME='actualWeekDate' VALUE='<?php print $actualWeekDate?>'>
        <INPUT TYPE='hidden' NAME='dayOfYear' VALUE=<?php print $dayOfYear?>>
        <INPUT TYPE='hidden' NAME='dateID' VALUE=<?php print $dateID?>>
        <INPUT TYPE='hidden' NAME='dayNumber' VALUE=<?php print $dayNumber?>>
        <INPUT TYPE='hidden' NAME='isPlayoffs' VALUE=<?php print $isPlayoffs?>>
        <INPUT TYPE='hidden' NAME='hasPractice' VALUE=<?php print $hasPractice?>>
        <INPUT TYPE='hidden' NAME='hasTies' VALUE=<?php print $hasTies?>>
        
		<?php
		printJavaScript();?>            
		<TABLE class='master' align=center>
        	<?php printTopInfo();
			if(strlen($error) > 2) {
				printError($error);
			}
            printMatches();  
            contactInfo(); ?>
        </TABLE>      
        </FORM>
   </BODY>
</HTML>