<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2013
* spiritControlPanel.php
*
* Program that concatenates all spirit functionality into one program. Users can clear bad spirits, view teams with bad spirit, 
* and create phantom spirit scores.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/sptJSFunctions.js'></script>";
$container = new Container('Spirit Control Panel', 'includeFiles/teamStyle.css', $javaScript); 

require_once('includeFiles/sptFunctions.php'); //badSpirit.php
require_once('includeFiles/teamBadSptFunctions.php'); //teamsBadSpirit
require_once('includeFiles/teamClass.php'); //teamsBadSpirit
require_once('includeFiles/addSptFunctions.php'); //addSpirit.php
require_once('includeFiles/class_spirit.php'); //all three

if(($activeTabIndex = $_GET['activeTabIndex']) == '') {
	$activeTabIndex = 0;
}

//badSpirit
if(isset($_POST['ApproveBadSpiritButton'])) {
	if (($spiritCount = $_POST['spiritCount']) == '') {
		$spiritCount = 0;
	}
	updateDatabase($spiritCount);
} else if(isset($_POST['DeleteBadSpiritButton'])) {
	if (($spiritCount = $_POST['spiritCount']) == '') {
		$spiritCount = 0;
	}
	deleteSpirits($spiritCount);
}
$badSpiritSubmissions = getBadSpiritSubmissions();

//teamsBadSpirit
if(($spiritValue = $_GET['spiritValue']) == '') {
	$spiritValue = 4;
}
if($sportID != 0) {
	$teamsObj = getTeamsBadSpiritInfo($sportID);
}


//addSpirit
if(isset($_POST['addSpiritScore'])) {
	addSpirit($teamID, $leagueID, $sportID);
}
if(isset($_POST['deleteSpiritScores'])) {
	deleteOldSpirits();
}




$seasonsDropDown = getSeasonDD($seasonID);
$sportsDropDown = getSportDD($sportID);
$leaguesDropDown = getLeaguesDD($sportID, 0, $leagueID);
$teamsDropDown = getTeamDD($leagueID, $teamID); 

$numOldSpirits = getOldSpirits();

$leagues = getLeagueSpirits($seasonID); ?>



<input type="hidden" id="tabIndex" value="<?php print $activeTabIndex ?>" />
<h1>Spirit Score Control Panel</h1>
<div id="tabContainer" class='tabs box'>
	<ul class='tabrow'>
		<li class="selected"><a href="#tabs-1">Bad Spirit Scores</a></li>
		<li><a href="#tabs-2">Teams with low Spirit</a></li>
		<li><a href="#tabs-3">Create Phantom Spirit</a></li>
		<li><a href="#tabs-4">Spirits By League</a></li>
	</ul>

	<div id="tabs-1">
		<form name='badSpiritSubmissions' method='post' action='spiritControlPanel.php?activeTabIndex=0'>
		<?php storeHiddenVariables($dbCount); ?>
		
		<div class='tableData'>
			<table>
				<?php printBadSpiritHeader();
				for($i=0;$i<count($badSpiritSubmissions);$i++) {
					printBadSpiritNode($i, $badSpiritSubmissions[$i]);
				}
				printBadSpiritFooter(count($badSpiritSubmissions)); ?>
			</table>
		</div>
		</form>
	</div>
	<div id="tabs-2">
		<form id='teamsBadSpiritForm' method='POST' action='spiritControlPanel.php?activeTabIndex=1'>
				<?php printTeamsHeader($sportsDropDown, $sportID, $spiritValue, $teamDroppedOut);
				$teamNum = 1;
				for($i=0;$i<count($teamsObj);$i++) 
				{
					if($teamsObj[$i]->getSpiritAverage() <= $spiritValue) 
					{
						printTeamNode($teamNum++, $teamsObj[$i], $teamDroppedOut);
					}
				} 
				if(count($teamsObj) == 0) { ?>
					<tr>
						<td colspan=4>
							No Bad Spirits to Show!
						</td>
					</tr>
				<?php } ?>
				</table>
			</div>
		</form>
	</div>
	<div id="tabs-3">
		<form id='oldSpiritForm' action='<?php print "spiritControlPanel.php?sportID=$sportID&leagueID=$leagueID&teamID=$teamID&activeTabIndex=2"?>' 
			method='post' onSubmit="return checkSure()">
		
		<div class='getIDs'>
			<?php printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown); ?>
		</div>
		<?php if($teamID > 0) { ?>
			<div class='tableData'>
				<table>
					<?php printAddSpiritForm();  ?>
				</table>
			</div>
		<?php }?>
		<div class='tableData'>
			<table>
			<?php
				printOldSpiritsHeader();
				for($i=0;$i<$numOldSpirits;$i++) {
					printOldSpiritNode($oldSpiritObj[$i], $i);
				}
				printOldSpiritsButtons($numOldSpirits); ?>
			</table>
		</div>
	</form>
	</div><div id="tabs-4">
		<form id='spiritsByLeague' method='post' action='spiritControlPanel.php?activeTabIndex=3'>
			<div class='getIDs'>
				Season <select name="seasonID" id="userInput" onChange="reloadPageSeason()">
					<?php print $seasonsDropDown ?>
				</select>
			</div><div class='tableData'>
				<table>
					<tr>
						<th colspan=3>
							Spirits By League
						</th>
					</tr><tr>
						<td>
							League Name
						</td><td>
							Spirit Given
						</td><td>
							Edited Spirit Given
						</td>
					</tr>
					<?php for($i=0;$i<count($leagues);$i++) {
						print '<tr><td><a target="_blank" href="standingsPage.php?leagueID='.$leagues[$i]['leagueID'].'">'.
							$leagues[$i]['leagueName'].'</a></td><td>'.
							number_format($leagues[$i]['spiritGiven'], 2, '.', '').'</td><td>'.
							number_format($leagues[$i]['editedSpiritGiven'], 2, '.', '').'</td></tr>';
					} ?>
				</table>
			</div>
		</form>
	</div>
</div>	  
	  
<?php $container->printFooter(); ?>
