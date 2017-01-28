<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 24, 2012
* editSubmissions.php
*
* Edit score submissions filtered by sport, league, and week.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
$javaScript = "<script type='text/javascript' src='includeFiles/submissionJavaFunctions.js'/></script>";
$container = new Container('Score Submissions', 'includeFiles/standingsStyle.css', $javaScript);

require_once('includeFiles/submissionVariableDeclarations.php');
require_once('includeFiles/submissionFormFunctions.php');
require_once('includeFiles/submissionErrorFunctions.php');
require_once('includeFiles/submissionSqlFunctions.php');

$teamCount = loadDBVariables($sportID, $leagueID, $teamID, $dateID);

if(!isset($_POST['Submit'])) {
	setVariables();
} else {
	getSubmittedData($leagueID);
	$editedTeams = checkChanges();
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, 0, $leagueID);
if ($seasonID != 0) {
	$weeksDropDown = getWeeksDD($seasonID, $dateID);
} else {
	$weeksDropDown = '';
}

//mysql_query("ALTER TABLE $scoreSubmissionsTable ADD notes VARCHAR(250);") or die('spirit score insert - '.mysql_error());

//if(strlen(strstr($date,"Playoffs"))>0) {
//	echo "<meta http-equiv=\"refresh\" content=\"0;URL=mailformPO.PHP?sport=$sport&cat=$cat\">";
//}

if (isset($_POST['Submit'])) {   //no errors, submit data  
	updateScores($editedTeams, $teamCount);
	updateStandings($editedTeams, $teamCount); //wins, lossees, ties
}?>

<FORM id='results' method='POST' action=<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&leagueID='.$leagueID.'&dateID='.$dateID ?>>
<input type='hidden' name='matches' value=<?php print $matches?>>
<input type='hidden' name='games' value=<?php print $games?>>
<input type='hidden' name='dayNumber' value=<?php print $dayNumber?>>
<input type='hidden' name='teamCount' value=<?php print $teamCount?>>
<input type='hidden' name='seasonID' value=<?php print $seasonID?>>

<?php for($k=0;$k<$teamCount;$k++) {
	print "<input type='hidden' name='teamName[$k]' value=\"".$teamName[$k]."\">";
	for ($i=0;$i<$matches*$games;$i++) {
		$teamScoreSubmissionIDString = $teamScoreSubmissionID[$k][$i];
		print "<input type='hidden' name='teamScoreSubmissionID[$k][$i]' value=$teamScoreSubmissionIDString>";
	}
}

printJavaScript();?>            
<h1>Edit/Create Score Submissions</h1>
<div class='getIDs'>
	<?php printTopInfo($sportsDropDown, $leaguesDropDown, $weeksDropDown); ?>
</div>
<div class='tableData'>
	<?php if($leagueID != 0 && $dateID != 0) { ?>
		<table>
			<tr>
				<?php printHeaderInfo($matches, $games); ?>
			</tr>
			<?php for($i=0;$i<$teamCount;$i++) { ?>
			<tr>
				<?php printTeamInfo($i); ?>
			</tr>
			<?php } ?>
			<tr>
				<td colspan=9>
					<input type='Submit' value='Change Scores' name='Submit'>
				</td>
			</tr>
		</table>
	<?php } ?>
</div>   
</form>
		
<?php $container->printFooter(); ?>