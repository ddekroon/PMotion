<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2013
* teamsBadSpirit.php
*
* This program is used to show, and update all spirit scores of 3.5 or lower.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function reloadPage() {
				var sportID = document.getElementsByName('sportID')[0].value;
				var spiritValue = document.getElementsByName('spiritValue')[0].value;
				self.location='teamsBadSpirit.php?sportID=' + sportID + '&spiritValue=' + spiritValue;
			}
		</script>";
$container = new Container('Prizes', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/teamBadSptFunctions.php');
require_once('includeFiles/teamClass.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($spiritValue = $_GET['spiritValue']) == '') {
	$spiritValue = 4;
}
$sportsDD = getSportDD($sportID);

if($sportID != 0) {
	$teamsObj = getTeamsBadSpiritInfo($sportID);
}?>

<form id='teamsBadSpiritForm' METHOD='POST' action='badSpirit.php'>
<h1>Teams With Bad Spirit</h1>
		<?php printTeamsHeader($sportsDD, $sportID, $spiritValue);
		$teamNum = 1;
		for($i=0;$i<count($teamsObj);$i++) {
			if($teamsObj[$i]->getSpiritAverage() <= $spiritValue) {
				printTeamNode($teamNum++, $teamsObj[$i]);
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
		
<?php $container->printFooter(); ?>