<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* July 24, 2012
* delTeamInSeason.php
*
* Team Dropped out program. Takes a team out of standings/team pictures
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function checkYesNo() {
				return confirm('Are you sure you want to remove this team from standings/score reporter?');
			}
		
			function reloadPageSport() {
				var form = document.getElementById('teamForm');
				var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				self.location='delTeamInSeason.php?sportID=' + sportID;
			}
			
			function reloadPageLeague() {
				var form = document.getElementById('teamForm');
				var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
				var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
				self.location='delTeamInSeason.php?sportID=' + sportID + '&leagueID=' + leagueID;
			}
		</script>";
$container = new Container('Team Dropped Out', 'includeFiles/teamStyle.css', $javaScript);
require_once('includeFiles/teamClass.php');

function getTeamData($leagueID) {
	global $teamsTable;
	$teamsArray = array();
	
	if($leagueID != 0) {
		//teams in dropdown
		$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID 
			AND team_num_in_league > 0 ORDER BY team_num_in_league");
			$teamCounter = 0;
		while($team = mysql_fetch_array($teamsQuery)) {
			$teamArray[$teamCounter] = new Team();
			$teamArray[$teamCounter]->teamID = $team['team_id'];
			$teamArray[$teamCounter]->teamName = $team['team_name'];
			$teamArray[$teamCounter++]->teamDropped = $team['team_dropped_out'];
		}
	}
	return $teamArray;
}

if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}


if(isset($_POST['removeTeamButton'])) {
	if(isset($_POST['teamDropped'])) {
		foreach($_POST['teamDropped'] as $teamID) {
			$updateString = "UPDATE $teamsTable SET team_dropped_out = 1 WHERE team_id = $teamID";
			//print $updateString.'<br />';
			mysql_query($updateString) or die('ERROR changing team name - '.mysql_error());
		}
		print 'Teams Updated<br />';
	} else {
		print 'No teams selected<br />';
	}
}

$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, 0, $leagueID);
$teamsArray = getTeamData($leagueID); ?>

<form id="teamForm" action='<?php print "delTeamInSeason.php?sportID=$sportID&leagueID=$leagueID" ?>' method="post">
<h1>Edit Teams That Dropped Out</h1>
<div class='getIDs'>
	Sport
	<select id='userInput' name='sportID' onchange='reloadPageSport()'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadPageLeague()'>
		<?php print $leaguesDropDown; ?>
	</select>
</div>
	<?php if($leagueID > 0) { ?>
		<div class='tableData'>
			<table>
				<tr>
					<th colspan=3>
						League Teams
					</th>
				</tr><tr>
					<td>
						team ID
					</td><td>
						Team Name
					</td><td>
						Dropped
					</td>
				</tr>
				<?php foreach($teamsArray as $teamNode) { ?>
				<tr>
					<td>
						<?php print $teamNode->teamID ?>
					</td>
					<td>
						<?php print $teamNode->teamName ?>
					</td><td>
						<input type='checkbox' name="teamDropped[]" <?php print $teamNode->teamDropped == 1?'checked':'' ?> value="<?php print $teamNode->teamID ?>" />
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan=3>
						<input type="submit" name="removeTeamButton" onClick="return checkYesNo()" value="Remove Team" />
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>

</form>

<?php $container->printFooter(); ?>