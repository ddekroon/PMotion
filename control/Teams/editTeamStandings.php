<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* July 24, 2012
* editTeamStandings.php
*
* NOT USED ANYMORE. Used to be this could be used to update standings in the teams database. Sometimes the standings would
* get messed up from what was reporter. Now this functionality is replaced by the team pages in the control panel.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/edtStndJavaFunctions.js'></script>";
$container = new Container('Edit Standings', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/edtStndFormFunctions.php');
require_once('includeFiles/edtStndSQLFunctions.php');
require_once('includeFiles/edtStndVariableDeclarations.php');
require_once('includeFiles/teamClass.php');

if(isset($_POST['submitTeams'])) {
	updateTeams();
}
$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeagueDD($leagueID, $sportID);
$numTeams = getTeamsData($leagueID); ?>

<form id='teamForm' METHOD='POST' action='<?php print $_SERVER['PHP_SELF']."?sportID=$sportID&leagueID=$leagueID" ?>'>
<table class="master">
	<tr>
		<td>
			<table class="titleBox">
				<?php printTopInfo($sportsDropDown, $leaguesDropDown); ?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table class="teamInfo"> 
				<?php printStandingsHeader();
				for($i=0;$i<$numTeams;$i++) {
					printTeamNode($team[$i], $i);
				} ?>
			   <input type='hidden' name='numTeams' value=<?php print $numTeams?> />
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table class="bottomButton">
				<?php if($leagueID != 0) {
					printButtons();
					}?>
			</table>
		</td>
	</tr>
</table>
</form>

<?php $container->printFooter(); ?>