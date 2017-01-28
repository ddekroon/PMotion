<?php 

function updateDatabase($spiritCount) {
	global $spiritScoresTable, $container;
	
	if(isset($_POST['approveBadSpirit'])) {
		$teamCount = 0;
		foreach($_POST['approveBadSpirit'] as $approved) {
			$spiritID = $_POST['spiritID'][$approved];
			$spiritValue = $_POST['spiritValue'][$approved];
			if ($spiritValue > 0) {
				mysql_query("UPDATE $spiritScoresTable SET spirit_score_edited_value = $spiritValue, spirit_score_dont_show = 1 
					WHERE spirit_score_id = $spiritID") or die('ERROR updating spirit '.mysql_error());
			} else {
				mysql_query("UPDATE $spiritScoresTable SET spirit_score_ignored = 1, spirit_score_dont_show = 1 
					WHERE spirit_score_id = $spiritID") or die('ERROR updating spirit '.mysql_error());
			}
			$teamCount++;
		}
		$container->printSuccess('Spirits updated, '.$teamCount.' scores updated');
	} else {
		$container->printError('No spirits selected to approve');
	}
}

function deleteSpirits($spiritCount) {
	global $spiritScoresTable, $container;
	if(isset($_POST['deleteBadSpirit'])) {
		$teamCount = 0;
		foreach($_POST['deleteBadSpirit'] as $delete) {
			$spiritID = $_POST['spiritID'][$delete];
                        mysql_query("DELETE FROM $spiritScoresTable WHERE spirit_score_id = $spiritID") 
                                or die('ERROR deleting spirit '.mysql_error());
			$teamCount++;
		}
		$container->printSuccess('Spirits updated, '.$teamCount.' scores deleted');
	} else {
		$container->printError('No spirits selected for deletion');
	}
}

function getSpiritDropDown($spiritScore) {
	$dropDown="<select name='spiritValue[]'>
            	<option value=0>N/A</option>";
	for($k = 5; $k >=1; $k-=.5) {
		if ($k == $spiritScore) {
			$dropDown.="<option selected VALUE=$k>$k</option>";
		} else {
			$dropDown.="<option VALUE=$k>$k</option>";
		}
	}
	$dropDown.='</select>';
	return $dropDown;
}

function getBadSpiritSubmissions() {
	global $spiritScoresTable, $scoreSubmissionsTable, $teamsTable, $scoreCommentsTable, $datesTable, $seasonsTable, $leaguesTable;
	
	$spiritQuery = mysql_query ("SELECT * FROM $spiritScoresTable 
		INNER JOIN $scoreSubmissionsTable ON score_submission_id = spirit_score_score_submission_id
		INNER JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
		INNER JOIN $seasonsTable ON $datesTable.date_season_id = $seasonsTable.season_id 
		INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		WHERE season_available_score_reporter = 1 AND spirit_score_ignored = 0 AND spirit_score_value <= 3.5 
		AND spirit_score_dont_show != 1") or die('ERROR getting bad spirit data '.mysql_error());
		$spiritNum = 0;
		
	while($spiritArray=mysql_fetch_array($spiritQuery)){
		$spirits[$spiritNum] = new Spirit();
		$spirits[$spiritNum]->spiritID = $spiritArray['spirit_score_id'];
		$spirits[$spiritNum]->spiritTeamID = $spiritArray['score_submission_team_id'];
		$spirits[$spiritNum]->spiritTeamName = $spiritArray['team_name'];
		$spirits[$spiritNum]->spiritLeagueID = $spiritArray['league_id'];
		$spirits[$spiritNum]->spiritLeagueName = $spiritArray['league_name'];
		$spirits[$spiritNum]->spiritValue = $spiritArray['spirit_score_edited_value'];
		$spirits[$spiritNum]->spiritSubmitterName = $spiritArray['score_submission_submitter_name'];
		$spirits[$spiritNum]->spiritSubmitterEmail = $spiritArray['score_submission_submitter_email'];
		$spirits[$spiritNum]->oppTeamID = $spiritArray['score_submission_opp_team_id'];
		$spirits[$spiritNum]->gameDate = $spiritArray['date_description'];
		$spirits[$spiritNum]->gameWeek = $spiritArray['date_week_number'];
		$spirits[$spiritNum]->reportDate = $spiritArray['score_submission_datestamp'];
		$scoreSubmissionID = $spiritArray['score_submission_id'];
		
		$oppTeamID = $spiritArray['score_submission_opp_team_id'];
		
		$oppTeamQuery = mysql_query("SELECT * FROM $teamsTable WHERE team_id = $oppTeamID") 
			or die('ERROR getting opposite team data '.mysql_error());
		$oppTeamArray = mysql_fetch_array($oppTeamQuery);
		
		$spirits[$spiritNum]->oppTeamName = $oppTeamArray['team_name'];
		
		$commentQuery = mysql_query("SELECT * FROM $scoreCommentsTable WHERE comment_score_submission_id = $scoreSubmissionID") 
			or die('ERROR getting opposite team data '.mysql_error());
		$commentArray = mysql_fetch_array($commentQuery);
		
		$spirits[$spiritNum]->comment = $commentArray['comment_value'];
		
		$spirits[$spiritNum]->spiritDropDown = getSpiritDropDown($spirits[$spiritNum]->spiritValue);
		$spiritNum++;
	} 
	return $spirits;
}

function printBadSpiritHeader() { ?>
	<tr>
		<th colspan=9>
			Spirit Scores
		</th>
	</tr><tr>
		<td style="width:20px;">
			Del
		</td><td style="width:20px;">
        	#
        </td><td>
        	Date Played
        </td><td>
        	Submitting Team
        </td><td>
        	Opponent
        </td><td>
        	Spirit Score
        </td><td>
        	Submitted By
        </td><td>
        	Submission Date
        </td><td>
        	Submit
        </td>
    </tr>
<?php }

function printBadSpiritNode($spiritNum, $spiritNode) { ?>

	<tr>
        <td rowspan=2 style="vertical-align:middle; width:20px;">
        	<input type='checkbox' name='deleteBadSpirit[]' VALUE=<?php print $spiritNum?>>
        </td>
    	<td style="width:20px;">
            <?php print $spiritNum+1;
			print "<input type='hidden' name='spiritID[]' value='".$spiritNode->spiritID."'>"; ?>
		</td><td>
            <?php print 'Week '.$spiritNode->gameWeek.' - '.$spiritNode->gameDate; ?>
		</td><td>
            <?php print $spiritNode->spiritTeamName ?>
        </td><td>
            <?php print $spiritNode->oppTeamName ?>
        </td><td>
            <?php print $spiritNode->spiritDropDown; ?>
        </td><td>
            <?php print $spiritNode->spiritSubmitterName.':'.$spiritNode->spiritSubmitterEmail; ?>
        </td><td>
            <?php print $spiritNode->confirmationDate = date('F j, Y', strtotime($spiritNode->reportDate)); ?>
        </td><td rowspan=2 style="vertical-align:middle">
        	<input type='checkbox' name='approveBadSpirit[]' value=<?php print $spiritNum?>>
        </td>
    </tr><tr>
    	<td colspan=7>
            	<?php if(strlen($spiritNode->comment) > 2) { 
					print $spiritNode->comment;
				} else {
					print 'No comment';
				} ?>
				<br /><br />
        </td>
    </tr>
<?php }

function printBadSpiritFooter($spiritCount) {
	if($spiritCount > 0){?> 
		<tr>
			<td align="center">
				<input type='submit' name='DeleteBadSpiritButton' value='Delete' onclick="return checkYesNoDeleteSpirit()">
			</td><td colspan=7>
				<input type='submit' name='ApproveBadSpiritButton' value='Approve' onclick="return checkYesNoApproveSpirit()">
			</td><td align="center">
				<input type='button' name='CheckAll' value='Check All' onClick="checkAll()">
			</td>
		</tr>
	<?php }else{ ?>
		<tr>
			<td colspan=9>
				<i>No spirit scores in holding</i>
			</td>
		</tr>
	<?php }	
}

//These variables are needed to change the data after submit has been pressed.
function storeHiddenVariables($submissions) {
	global $spiritID;
	print "<input type='hidden' name='spiritCount' value=".count($submissions).'>';
	for ($i=0;$i<count($submissions);$i++) {
		print "<input type='hidden' name='spiritID[$i]' value='".$submissions[$i]->spiritValue."'>";
	}
}

function getLeagueSpirits($seasonID) {
	global $spiritScoresTable, $scoreSubmissionsTable, $teamsTable, $leaguesTable, $sportsTable, $seasonsTable;
	
	$leagues = array();
	
	$spiritQuery = mysql_query("SELECT * FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable 
		ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
		INNER JOIN $teamsTable ON $teamsTable.team_id = score_submission_team_id
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE spirit_score_ignored = 0 AND spirit_score_edited_value > 0 AND season_id = $seasonID
		ORDER BY sport_id ASC, league_id ASC") 
		or die ('ERROR getting spirits '.mysql_error());
	$numLeagues = -1;
	$lastLeagueID = 0;
	$numSubmissions = 0;
	while($spiritArray = mysql_fetch_array($spiritQuery)) {
		if($spiritArray['league_id'] != $lastLeagueID || $lastLeagueID == 0) {
			if($numSubmissions != 0) {
				$leagues[$numLeagues]['spiritGiven'] = $spiritGiven / $numSubmissions;
				$leagues[$numLeagues]['editedSpiritGiven'] = $editedSpiritGiven / $numSubmissions;
				$numSubmissions = 0;
			}
			$spiritGiven = 0;
			$editedSpiritGiven = 0;
			$numLeagues++;
			$lastLeagueID = $spiritArray['league_id'];
			$leagues[$numLeagues]['leagueID'] = $spiritArray['league_id'];
			$leagues[$numLeagues]['leagueName'] = $spiritArray['league_name'].' '.dayString($spiritArray['league_day_number']);
		}
		$spiritGiven += $spiritArray['spirit_score_value'];
		$editedSpiritGiven += $spiritArray['spirit_score_edited_value'];
		$numSubmissions++;
	}
	if($numSubmissions != 0) {
		$leagues[$numLeagues]['spiritGiven'] = $spiritGiven / $numSubmissions;
		$leagues[$numLeagues]['editedSpiritGiven'] = $editedSpiritGiven / $numSubmissions;
	}
	return $leagues;
}