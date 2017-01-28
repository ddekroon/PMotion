<?php /*****************************************
File: turnOnCancelOption.php
Creator: Derek Dekroon
Created: May 19/2012
Turn on cancel option in score reporter for a league week.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
	
	date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'sendEmail.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');

$javaScript = "<script language='JavaScript'>
function checkSure() {
	return confirm('Are you sure?');
}
</script>";
$container = new Container('Cancel Option', 'includeFiles/standingsStyle.css', $javaScript);

function getPostValues($numSports, $numLeagues) {
	$leaguesToSet = array();
	for ($i=0;$i<$numSports;$i++) {
		for($j=0;$j<$numLeagues[$i];$j++) {
			if(($leaguesToSet[$i][$j] = $_POST['league'][$i][$j]) == '') {
				$leaguesToSet[$i][$j] = 0;
			}
		}
	}
	return $leaguesToSet;
}

function query($query_string){
	$quer_line=mysql_query($query_string) or die("TEST ".mysql_error());
	$array_line=mysql_fetch_array($quer_line);
	return ($array_line);
}

function setCancel($numSports, $numLeagues, $leagueID, $leaguesToSet) {
	global $leaguesTable, $teamsTable, $datesTable, $scheduledMatchesTable, $seasonsTable;

	$numLeaguesAffected = 0;
	for ($i=0;$i<$numSports;$i++) {
		for($j=0;$j<$numLeagues[$i];$j++) {
			if($leaguesToSet[$i][$j] == 1) {
				$numLeaguesAffected++;
				$leagueIDString = $leagueID[$i][$j];

				$leagueArray = query("SELECT league_week_in_score_reporter, league_sport_id, league_season_id, league_day_number, league_hide_spirit_hour 
									  FROM $leaguesTable WHERE league_id = $leagueIDString");
				$leagueWeek = $leagueArray['league_week_in_score_reporter'];
				$leagueSport = $leagueArray['league_sport_id'];
				$leagueSeason = $leagueArray['league_season_id'];
				$leagueDay = $leagueArray['league_day_number'];
				$dateChangeTime = $leagueArray['league_hide_spirit_hour'];
			
				$dateQuery = mysql_query("SELECT date_day_of_year_num, date_week_number FROM $datesTable 
					INNER JOIN $scheduledMatchesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id 
					WHERE (date_week_number = $leagueWeek + 1 OR date_week_number = $leagueWeek + 2) AND date_sport_id = $leagueSport
					AND date_season_id = $leagueSeason AND date_day_number = $leagueDay ORDER BY date_day_of_year_num ASC")
					or die('ERROR getting dates '.mysql_error());
				if(mysql_num_rows($dateQuery) == 0) {
					$dateQuery = mysql_query("SELECT date_day_of_year_num, date_week_number FROM $datesTable WHERE date_week_number = $leagueWeek + 1 AND date_sport_id = 
						$leagueSport AND date_season_id = $leagueSeason AND date_day_number = $leagueDay") 
						or die('ERROR getting date 2 - '.mysql_error());
				}
				$dateArray = mysql_fetch_array($dateQuery);
				$dateDayOfYear = $dateArray['date_day_of_year_num'];
				$nextWeek = $dateArray['date_week_number'];
				$curDayOfYear = date('z');
				
				//if it is a correct time to switch the week in score reporter
				
				if ($dateDayOfYear != '' && $curDayOfYear >= $dateDayOfYear) { 
					mysql_query("UPDATE $leaguesTable SET league_week_in_score_reporter = $nextWeek WHERE league_id = $leagueIDString") 
						or die('ERROR setting new week '.mysql_error());
					$leagueWeek = $nextWeek;
				}
				
				mysql_query("UPDATE $leaguesTable SET league_show_cancel_default_option = 1 WHERE league_id = $leagueIDString") 
				or die('ERROR updating leagues '.mysql_error());
			}
		}
	}
	return 'Update complete, '.$numLeaguesAffected.' leagues affected<br /><br />';
}

function offCancel($numSports, $numLeagues, $leagueID, $leaguesToSet) {
	global $leaguesTable;
	$numLeaguesAffected = 0;
	for ($i=0;$i<$numSports;$i++) {
		for($j=0;$j<$numLeagues[$i];$j++) {
			if($leaguesToSet[$i][$j] == 1) {
				$numLeaguesAffected++;
				$leagueIDString = $leagueID[$i][$j];
				mysql_query("UPDATE $leaguesTable SET league_show_cancel_default_option = 0 WHERE league_id = $leagueIDString") 
				or die('ERROR updating leagues '.mysql_error());
			}
		}
	}
	return 'Update complete, '.$numLeaguesAffected.' leagues affected<br /><br />';
}

$sportsQuery = mysql_query("SELECT sport_id, sport_name FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id ASC") 
	or die('ERROR getting sports '.mysql_error());
$sportNum = 0;
while($sport = mysql_fetch_array($sportsQuery)) {
	$sportID[$sportNum] = $sport['sport_id'];
	$sportName[$sportNum] = $sport['sport_name'];
	$sportNum++;
}

for($i=0;$i<$sportNum;$i++) {
	$leaguesQuery = mysql_query("SELECT league_id, league_name, league_day_number, league_show_cancel_default_option
								 FROM $leaguesTable INNER JOIN $seasonsTable ON $leaguesTable.league_season_id = 
								 $seasonsTable.season_id WHERE season_available_score_reporter = 1 AND league_sport_id = $sportID[$i] ORDER BY 
								 league_day_number ASC") or die('ERROR getting leagues '.mysql_error());
	$leagueNum[$i]=0;
	while($league = mysql_fetch_array($leaguesQuery)) {	
		$leagueID[$i][$leagueNum[$i]] = $league['league_id'];
		$leagueName[$i][$leagueNum[$i]] = $league['league_name'];
		$leagueDay[$i][$leagueNum[$i]] = $league['league_day_number'];
		$leagueCancel[$i][$leagueNum[$i]] = $league['league_show_cancel_default_option'];
		$leagueNum[$i]++;	
	}
} 

if (isset($_POST['Submit'])) {
	$leaguesToSet = getPostValues($sportNum, $leagueNum);
	$updateStatus = setCancel($sportNum, $leagueNum, $leagueID, $leaguesToSet);
} 

if (isset($_POST['Off'])) {
	$leaguesToSet = getPostValues($sportNum, $leagueNum);
	$updateStatus = offCancel($sportNum, $leagueNum, $leagueID, $leaguesToSet);
}

?>

<form id="cancelForm" action='turnOnCancelOption.php' method="post" onsubmit="return checkSure()">
<h1>Turn on Cancel Option</h1>
<?php if (strlen($updateStatus) > 2) {
	$container->printSuccess($updateStatus);
} ?>
<div class='tableData'>
<?PHP for($i=0;$i<$sportNum;$i++) { ?>
	
		<table>
			<tr>
				<th colspan=3>
					<?php print $sportName[$i]; ?>
				</th>
			</tr><tr>
				<td>
					Chk
				</td><td>
					League Name
				</td><td>
                	On/Off
                </td>
			</tr>
			<?php for($j=0;$j<$leagueNum[$i];$j++) { ?>
				<tr>
					<td>
						<input type='checkbox' name='league[<?php print $i?>][<?php print $j?>]' value=1 />
					</td><td>
						<?php print '   '.$leagueName[$i][$j].' - '.dayString($leagueDay[$i][$j]); ?>
					</td><td>
                    	<?php print $leagueCancel[$i][$j] ?>
                    </td>
				</tr>
			<?php } ?>
		</table>
<?php } ?>
</div><div class='tableData'>
	<input type='submit' name="Submit" value='Turn On' />
    <input type='submit' name="Off" value='Turn Off' />
</div>
</form>

<?php $container->printFooter(); ?>