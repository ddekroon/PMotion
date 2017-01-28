<?php

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once('includeFiles/trackVariableDeclarations.php');
require_once('includeFiles/trackFormFunctions.php');
require_once('includeFiles/trackErrorFunctions.php');
require_once('includeFiles/trackSqlFunctions.php');
require_once('includeFiles/trackMailFunctions.php');

$sportID = 1;

session_start();
	$_SESSION['thankYou']=1;
	
loadDefaults($sportID);

if($leagueID != 0) 
{
	$weekNum = setLeagueWeek($leagueID);
}
if(!isset($_POST['Submit'])) 
{
	//loadVariables($sportID, $leagueID, $teamID);
} 
else 
{
	//getSubmittedData();
}

getLeagueDD($leagueID, $sportID);
getTeamDD($leagueID, $teamID, $oppTeamID);

if (isset($_POST['subUs']))
{
    ?>
	<script type="text/javascript">
		subOneUs(); 
    </script> <?php
}

if (isset($_POST['addUs']))
{
	?>
	<script type="text/javascript">
		addOneUs(); 
    </script> <?php
}

if (isset($_POST['Submit'])) 
{
	if (($error = checkForErrors()) == '') //no errors, submit data 
	{    		
		sendScoreEmails($leagueID, $teamID); //sends score reporter emails to admins, leave this before submitting scores 
 		$ignored = submitScores();
		
		if ($ignored == 0 && $isPlayoffs == 0) 
		{
			updateStandings($teamID, $matches, $games, $gameResults); //wins, lossees, ties
		} 
 
		header("Location: thankYouReport.php?sportID=".urlencode($sportID).'&leagueID='.urlencode($leagueID).'&teamID='.urlencode($teamID));
		
		exit();
	}
} 
?>

<!doctype html public '-//w3c//dtd html 3.2//en'>
<html>        
    <head>
       <!-- Title for the browser --> 
        <title>Score Tracker</title>
       <!-- This is the javascript file that makes all of the dropdowns and reloads work properly -->
        <script type='text/javascript' src='includeFiles/trackJavaFunctions.js'/></script>
        <!-- This is the css stylesheet that makes the form appear like it should -->
        <link rel='stylesheet' type='text/css' href='includeFiles/trackStyles<?php print $sportID ?>.css'/>
	</head>
    <body>
        <FORM id='trackReporterID' name="trackReporter" method='POST' action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&leagueID='.$leagueID.'&teamID='.$teamID.'&usScore='.$usScore.'&themScore='.$themScore?> onSubmit="return checkFields(<?php print $matches.','.$games?>)">
        
        <INPUT TYPE='hidden' NAME='sportID' VALUE=<?php print $sportID?>>
        <INPUT TYPE='hidden' NAME='matches' VALUE=<?php print $matches?>>
        <INPUT TYPE='hidden' NAME='games' VALUE=<?php print $games?>>
        <INPUT TYPE='hidden' NAME='teamName' VALUE='<?php print $teamName?>'>
        <INPUT TYPE='hidden' NAME='leagueName' VALUE="<?php print $leagueName?>">
        <INPUT TYPE='hidden' NAME='actualWeekDate' VALUE='<?php print $actualWeekDate?>'>
        <INPUT TYPE='hidden' NAME='dayOfYear' VALUE=<?php print $dayOfYear?>>
        <INPUT TYPE='hidden' NAME='dateID' VALUE=<?php print $dateID?>>
        <INPUT TYPE='hidden' NAME='dayNumber' VALUE=<?php print $dayNumber?>>
        <INPUT TYPE='hidden' NAME='usScore' VALUE=<?php print $usScore?>>
        <INPUT TYPE='hidden' NAME='themScore' VALUE=<?php print $themScore?>>

		<?php
		printJavaScript();?>            
		<TABLE class='master' align=center>
        	<?php printTopInfo($sportID);
			
			if(strlen($error) > 2) 
			{
				printError($error);
			}
			
            printMatches();
			
			contactInfo(); ?>
        </TABLE>      
        </FORM>
   </BODY>
</HTML>