<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
*  July 2, 2012
* submitTeam.php
*
* Create a team on the control panel, DOESN'T GET USED
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript' src='includeFiles/sbmtJavaFunctions.js'></script>";
$container = new Container('Prizes', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/sbmtSQLFunctions.php');
require_once('includeFiles/sbmtVariableDeclarations.php');
require_once('includeFiles/sbmtFormFunctions.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['submit'])) {
	getPostData();
	registerTeam($playerObj);
	print 'Team - '.$teamName.' added';
}

$seasonsDD = getSeasonDD($seasonID);
$sportsDD = getSportDD($sportID);
$leaguesDD = getLeaguesDD( $sportID, $seasonID, $leagueID); ?>

<form id='teamForm' METHOD='POST' action=<?php print $_SERVER['PHP_SELF'].'?seasonID='.$seasonID.'&sportID='.$sportID.'&leagueID='.$leagueID ?>>
<table class="master">
	<tr>
		<td>
			<table class="titleBox">
				<tr>
					<th align="center">
						<?php print 'Add a Team'; ?>
					</th>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table class="getIDs">
				<?php printInfoDDs($seasonsDD, $sportsDD, $leaguesDD); ?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table class="teamInfo">   
				<?php printTeamInfo($teamName); ?>
			</table>
			<table class="teamInfo"> 
				<?php printPlayerHeader();
				for($i=0;$i<15;$i++) {
					printPlayerNode($playerObj[$i], $i);
				} ?>
			</table>
		</td>
	</tr>
	<?php if($leagueID != 0) { ?>
	<tr>
		<td align="center">
			<table class="bottomButton">
				<?php printButtons() ?>
			</table>
		</td>
	</tr>
	<?php } ?>
</table>
</form>
		
<?php $container->printFooter(); ?>
