<?php /******************************
*cancelLeagueNightSource.php
*Derek Dekroon
*Sometime in Summer 2012
*
*This program moves all cancelled leagues week in score reporter/standings ahead one and automatically makes
*score submissions for all the teams in the leagues that just say the games were cancelled.
*****************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<SCRIPT LANGUAGE='JavaScript'>
	function checkSure() {
		return confirm('Are you sure you want to cancel these leagues for the week?');
	}
</script>";
$container = new Container('Close League Weeks', 'includeFiles/standingsStyle.css', $javaScript);

function cancelLeagues() {
	global $leaguesTable, $teamsTable, $scheduledMatchesTable, $datesTable, $scoreSubmissionsTable, $container;
	$numLeaguesAffected = 0;
	for($i=0; $i < count($_POST['league']); $i++) {
		$leagueID = $_POST['league'][$i];
		$weekToClose = $_POST['weekToClose'][$leagueID];
		
		$numLeaguesAffected++;
		mysql_query("UPDATE $leaguesTable SET league_week_in_score_reporter = $weekToClose, league_week_in_standings = 
			$weekToClose WHERE league_id = $leagueID") or die('ERROR updating leagues '.mysql_error());
		mysql_query("UPDATE $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
			SET team_most_recent_week_submitted = league_week_in_score_reporter WHERE team_league_id = $leagueID") 
			or die('ERROR updating teams - '.mysql_error());
			
		$teamsQuery = mysql_query("SELECT team_id, team_name, league_week_in_score_reporter, league_num_of_games_per_match FROM $teamsTable 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
			WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out = 0") 
			or die('ERROR getting teams - '.mysql_error());
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			$teamID = $teamArray['team_id'];
			$teamName = $teamArray['team_name'];
			$leagueWeek = $teamArray['league_week_in_score_reporter'];
			$leagueNumGames = $teamArray['league_num_of_games_per_match'];
			$matchQuery = mysql_query("SELECT scheduled_match_team_id_1, scheduled_match_team_id_2, date_id FROM $scheduledMatchesTable 
				INNER JOIN $datesTable ON $datesTable.date_id = $scheduledMatchesTable.scheduled_match_date_id
				WHERE scheduled_match_league_id = $leagueID AND date_week_number = $leagueWeek 
				AND (scheduled_match_team_id_1 = $teamID OR scheduled_match_team_id_2 = $teamID)")
				or die('ERROR getting matches - '.mysql_error());
			$numRows = 0;
			while($matchArray = mysql_fetch_array($matchQuery)) {
				if($matchArray['scheduled_match_team_id_1'] == $teamID) {
					$oppTeamID = $matchArray['scheduled_match_team_id_2'];
				} else {
					$oppTeamID = $matchArray['scheduled_match_team_id_1'];
				}
				$dateID = $matchArray['date_id'];
				if($numRows == 0) {
					$numRows++;
					$scoreQuery = mysql_query("SELECT score_submission_result FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamID 
						AND score_submission_date_id = $dateID") 
						or die($container->printError('ERROR deleting score submissions - '.mysql_error()));
					$numScores = 0;
					while($scoreArray = mysql_fetch_array($scoreQuery)) {
						$result = $scoreArray['score_submission_result'];
						if($result == 1) {
							$standingsFilter = "team_wins = team_wins - 1 ";
						} else if($result == 2) {
							$standingsFilter = "team_losses = team_losses - 1 ";
						} else if($result == 3) {
							$standingsFilter = "team_ties = team_ties - 1 ";
						} else {
							$standingsFilter = '';
							continue;
						}
						$numScores++;
						mysql_query("UPDATE $teamsTable SET $standingsFilter WHERE team_id = $teamID") 
							or die($container->printError('ERROR updating team standings - '.mysql_error()));
					}
					mysql_query("DELETE FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamID AND 
						score_submission_date_id = $dateID") or die('ERROR deleting score submissions - '.mysql_error());
					$container->printSuccess('Deleted '.mysql_affected_rows().' submissions for team '.$teamID.'. '.$numScores.' scores negated');
				}
				for($i=0;$i<$leagueNumGames; $i++) {
					mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_team_id, score_submission_opp_team_id, 
						score_submission_date_id, score_submission_submitter_name, score_submission_result, 
						score_submission_ignored, score_submission_datestamp) VALUES ($teamID, $oppTeamID, $dateID, 
						'ADMIN-CANCEL', 4, 0, NOW())") or die('ERROR putting in score submission - '.mysql_error());
				}
			}
		}
	}
	return 'Update complete, '.$numLeaguesAffected.' league(s) effected';
	
}

function getWeeksDD($dayNumber, $sportID, $curWeek) {
	global $datesTable, $seasonsTable;
	$datesDropDown = '';
	
	$datesQuery=mysql_query("SELECT date_week_number, date_description FROM $datesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id 
		WHERE season_available_score_reporter = 1 AND date_day_number = $dayNumber 
		AND date_sport_id = $sportID ORDER BY date_week_number ASC") or die('ERROR getting weeks '.mysql_error());
	while($date = mysql_fetch_array($datesQuery)) {
		if($date['date_week_number']==$curWeek + 1){
			$datesDropDown.="<option selected value= $date[date_week_number]>$date[date_description]</option>";
		}else{
			$datesDropDown.="<option value= $date[date_week_number]>$date[date_description]</option>";
		}
	}
	return $datesDropDown;
}

if (isset($_POST['Submit'])) {
	$updateStatus = cancelLeagues();
}

$sportsQuery = mysql_query("SELECT sport_id, sport_name FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id") 
	or die('ERROR getting sports '.mysql_error());
$sportNum = 0;
while($sport = mysql_fetch_array($sportsQuery)) {
	$sportID[$sportNum] = $sport['sport_id'];
	$sportName[$sportNum] = $sport['sport_name'];
	$sportNum++;
}

for($i=0;$i<$sportNum;$i++) {
	$leaguesQuery = mysql_query("SELECT league_id, league_name, league_day_number, league_week_in_score_reporter, league_day_number FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $leaguesTable.league_season_id = $seasonsTable.season_id
		WHERE season_available_score_reporter = 1 AND league_sport_id = $sportID[$i] ORDER By league_day_number ASC") 
		or die('ERROR getting leagues '.mysql_error());
	$leagueNum[$i]=0;
	while($league = mysql_fetch_array($leaguesQuery)) {	
		$leagueID[$i][$leagueNum[$i]] = $league['league_id'];
		$leagueName[$i][$leagueNum[$i]] = $league['league_name'];
		$leagueDay[$i][$leagueNum[$i]] = $league['league_day_number'];
		$curWeek = $league['league_week_in_score_reporter'];
		$leagueCurWeek[$i][$leagueNum[$i]] = $curWeek;
		$leagueWeeksDD[$i][$leagueNum[$i]] = getWeeksDD($league['league_day_number'], $sportID[$i], $curWeek);
		$leagueNum[$i]++;	
	}
} ?>

<h1>Cancel a League Night</h1>

<?php if (strlen($updateStatus) > 2) {
	$container->printSuccess($updateStatus);
} else { 
	$container->printWarning('This program will delete any score submissions on a night specified to cancel and replace them with score submissions saying cancelled. It will also negate any wins/losses/ties a team was given with their score submissions.');
} ?>
<form id="cancelForm" action='cancelLeagueNight.php' method="post" onsubmit="return checkSure()">
	<?php for($i=0;$i<$sportNum;$i++) { ?>
		<div class='tableData'>
			<table>
				<tr>
					<th colspan=4>
						<?php print $sportName[$i]; ?>
					</th>
				</tr>
				<?php for($j=0;$j<$leagueNum[$i];$j++) { 
					if($j ==0) {?>
						<tr>
							<td style="width:20px;">
								Chk
							</td><td style="width:300px;">
								League Name
							</td><td>
								Current Week In Score Reporter
							</td><td>
								Date to Cancel
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td style="width:20px;">
							<input style='vertical-align:central' type='checkbox' name='league[]' 
								value=<?php print $leagueID[$i][$j] ?>></input>
						</td><td style="text-align:left;">
							<?php print $leagueName[$i][$j].' - '.dayString($leagueDay[$i][$j]); ?>
						</td><td>
							<?php print $leagueCurWeek[$i][$j]; ?>
						</td><td>
							<select id="userInput" name="weekToClose[<?php print $leagueID[$i][$j]?>]">
								<?php print $leagueWeeksDD[$i][$j]; ?>
							</select>
						</td>
					</tr>
				<?php } ?>
			</table>
	   </div>
	<?php } ?>
	<div class='tableData'>
		<input type='submit' name="Submit" value='Cancel Leagues' />
	</div>
</form>