<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* July 24, 2012
* showTeamIDs.php
*
* Shows team ID's and picture ID's for any given sport/league. Also gives the user the chance to update pic ID's
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function reloadPage() {
				var form = document.getElementById('teamForm');
				var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;	
				var seasonID=form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
				self.location='showTeamIDs.php?seasonID=' + seasonID + '&sportID=' + sportID;
			}
			
			function reloadPageLeague() {
				var form = document.getElementById('teamForm');
				var seasonID=form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
				var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
				self.location='showTeamIDs.php?seasonID=' + seasonID + '&sportID=' + sportID + '&leagueID=' + leagueID;
			}
		</script>";
$container = new Container('Team IDs', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/shwIDsFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['submitPics'])) {
	updatePics();
}

if($seasonID == 0) {
	$seasonQuery = mysql_query("SELECT season_id FROM $seasonsTable WHERE season_available_score_reporter = 1") 
		or die('ERROR getting default season - '.mysql_error());
	$seasonArray = mysql_fetch_array($seasonQuery);
	$seasonID = $seasonArray['season_id'];
}

$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID);

$teamObjs = getTeamsData($sportID, $seasonID, $leagueID);
if($sportID != 0 && $seasonID != 0 && !isset($_POST['submitPics'])) {
	updateTeamNums($teamObjs);
}?>

<form id='teamForm' method='post' action=<?php print $_SERVER['PHP_SELF']."?sportID=$sportID&seasonID=$seasonID&leagueID=$leagueID" ?>>
<h1>Team Picture ID's</h1>
<div class='getIDs'>
	<?php printInfoDDs($seasonsDropDown, $sportsDropDown, $leaguesDropDown) ?>
</div>
	<?php if($sportID > 0) { 
	$prevTeamLeagueID = 0;
	for($i=0;$i<count($teamObjs);$i++) {
		if($i % 2 == 0) {?>
			<div class='tableData'>
		<?php } ?>
		<table style="font-size:90%;"> 
			<tr>
				<th colspan=4>
					<?php print $teamObjs[$i][0]->teamLeagueName ?>
				</th>
			</tr>
			<?php printLeagueHeader();
			for($j=0;$j<count($teamObjs[$i]);$j++) {
				printTeamNode($teamObjs[$i][$j], $j);
			} ?>
		</table>
		<?php if($i % 2 == 1 && $i != count($teamObjs) - 1) { ?>
			</div>
		<?php }
	}?>
	<div class='tableData'>
		<input type="submit" name="submitPics" value="Update Pictures" />
	</div>
	<?php } ?>
</div>
</form>
		
<?php $container->printFooter(); ?>