<?php /*****************************************
File: updateLeague.php
Creator: Derek Dekroon
Created: June 16/2012
NOT USED ANYMORE. File that allowed a user to choose a league for editing. Functionality taken by leagues control panel
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' src='includeFiles/sbmtLeagJSFunctions.js'/></script>";
$container = new Container('Update a League', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/sbmtLeagVariableDeclarations.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}

$sportsDropDown = getSportdD($sportID);
$seasonsDropDown = getSeasonDD($seasonID);
$leaguesDropDown = getLeaguesDD($sportID, $seasonID, $leagueID); ?>

<form id='results' method='POST' action='<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&seasonID='.$seasonID.'&leagueID='.$leagueID.'&update=1' ?>'>
	<table class='master' align="center">
		<tr>
			<td>
				<table class='titleBox'>
					<tr>
						<th>
							Choose a League
						</th>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table class='getIDs'>
					<tr>
						<td>
							Sport
						</td><td>
							<select id='userInput' name='sportID' onchange='reloadUpdatePage()'>
								<?php print $sportsDropDown ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Season
						</td><td>
							<SELECT id='userInput' NAME='seasonID' onchange='reloadUpdatePage()'>
								<?php print $seasonsDropDown ?>
							</SELECT>
						</td>
					</tr>
					<tr>
						<td>
							League
						</td><td>
							<SELECT id='userInput' NAME='leagueID' onchange='reloadUpdatePage()'>
								<?php print $leaguesDropDown ?>
							</SELECT>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>

<?php $container->printFooter() ?>