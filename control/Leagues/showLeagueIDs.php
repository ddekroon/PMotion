<?php /*****************************************
File: showLeagueIDs.php
Creator: Derek Dekroon
Created: July 27/2012
NOT USED ANYMORE, used to show all league emails but now the functionality is taken by leagues control panel
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript' />
			function reloadPageSeason() {
				var form = document.getElementById('results');
				var seasonID=form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
				self.location='showLeagueIDs.php?seasonID=' + seasonID;
			}
		</script>";
$container = new Container('Show League IDs', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/sbmtLeagVariableDeclarations.php');

if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}
$seasonsDropDown = getSeasonDD($seasonID);

if($seasonID != 0){ 
	$sportsQuery = mysql_query("SELECT * FROM $sportsTable WHERE sport_id > 0") or die('ERROR getting sports '.mysql_error());
	$sportNum = 0;
	while($sport = mysql_fetch_array($sportsQuery)) {
		$sportID[$sportNum] = $sport['sport_id'];
		$sportName[$sportNum] = $sport['sport_name'];
		$sportNum++;
	}
	
	for($i=0;$i<$sportNum;$i++) {
		$leaguesQuery = mysql_query("SELECT * FROM $leaguesTable INNER JOIN $seasonsTable ON $leaguesTable.league_season_id = $seasonsTable.season_id
			WHERE season_id = $seasonID AND league_sport_id = $sportID[$i] ORDER By league_day_number ASC") or die('ERROR getting leagues '.mysql_error());
		$leagueNum[$i]=0;
		while($league = mysql_fetch_array($leaguesQuery)) {	
			$leagueID[$i][$leagueNum[$i]] = $league['league_id'];
			$leagueName[$i][$leagueNum[$i]] = $league['league_name'];
			$leagueDay[$i][$leagueNum[$i]] = $league['league_day_number'];
			$leagueNum[$i]++;	
		}
	} 
}?>

<form id='results' method='POST' action='<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&seasonID='.$seasonID.'&leagueID='.$leagueID.'&update=1' ?>'>
	<table class='master' align="center">
		<tr>
			<td colspan=4>
				<table class='titleBox'>
					<tr>
						<th>
							Choose a Season
						</th>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan=4>
				<table class='getIDs'>
					<tr>
						<td>
							Season:
							<SELECT NAME='seasonID' onchange='reloadPageSeason()'>
								<OPTION VALUE=0>Choose a Season</OPTION>
								<?php print $seasonsDropDown ?>
							</SELECT>
						</td>
					</tr>
				</table>
			</td>
		</tr><tr>
		<?php for($i=0;$i<$sportNum;$i++) { 
			if($leagueNum[$i] > 0) { ?>
			<td>
				<table class="titleBox">
					<tr>
						<th>
							<?php print $sportName[$i]; ?>
						</th>
					</tr>
				</table>
				<table class="leagueInfo">
					<?php for($j=0;$j<$leagueNum[$i];$j++) { ?>
						<tr>
							<td align=left>
								<?php print '   '.$leagueName[$i][$j].' - '.dayString($leagueDay[$i][$j]); ?>
							</td>
							<td align=right>
								<?php print $leagueID[$i][$j]; ?>
							</td>
						</tr>
					<?php } ?>
				</table>
			</td>
			<?php }
		} ?>
		</tr>
	</table>
</form>

<?php $container->printFooter(); ?>