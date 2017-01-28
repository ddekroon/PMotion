<?php /***************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2012
* sptFunctions.php
*
* This file holds all the functions (except javascript) for addSpiritSource.php.
**********************************/

if(($seasonID = $_GET['seasonID']) == '') {
	$seasonArray = mysql_fetch_array(mysql_query("SELECT * FROM $seasonsTable WHERE season_available_score_reporter = 1"));
	$seasonID = $seasonArray['season_id'];
}
if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}

function addSpirit($teamID, $leagueID, $sportID) {
	global $spiritScoresTable, $scoreSubmissionsTable;
	$spiritValue = $_POST['spiritValue'];
	if(!is_numeric($spiritValue)) {
		print 'Value not numeric<br />';
		return 0;
	}
		
	
	if($teamID != 0 && $spiritValue != 0) {
	$submissionQuery = mysql_query("SELECT MAX(score_submission_id) as maxnum FROM $scoreSubmissionsTable") or die('ERROR getting new score submission number - '.mysql_error());
	$submissionArray = mysql_fetch_array($submissionQuery);
	$newScoreSubmissionNum = $submissionArray['maxnum']+1;
	
	mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, score_submission_opp_team_id, 
		score_submission_date_id, score_submission_submitter_name, score_submission_result, score_submission_ignored,  score_submission_datestamp,
		score_submission_is_phantom) 
		VALUES ($newScoreSubmissionNum, 0, $teamID, 0, 'ADMIN-Spirit', 0, 1, NOW(), 1)") 
		or die ('Error with inserting into score submission db - '.mysql_error());
						
	mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, spirit_score_ignored, 
		spirit_score_dont_show, spirit_score_edited_value, spirit_score_is_admin_addition) VALUES ($newScoreSubmissionNum, $spiritValue, 
		0, 1, $spiritValue, 1)") or die('spirit score insert - '.mysql_error());	
	} else {
		print 'Cannot create spirit score - ';
		print $teamID == 0? 'No team selected':'';
		print $spiritValue == 0? 'Spirit Value = 0':'';
		print '<br />';
	}
}

function deleteOldSpirits() {
	global $spiritScoresTable, $container;
	if(isset($_POST['deleteOldSpirit'])) {
		$teamCount = 0;
		foreach($_POST['deleteOldSpirit'] as $spiritID) {
			mysql_query("UPDATE $spiritScoresTable SET spirit_score_ignored = 1 WHERE spirit_score_id = $spiritID") 
				or die('ERROR updating spirit '.mysql_error());
			$teamCount++;
		}
		$container->printSuccess('Spirit\'s updated, '.$teamCount.' teams affected');
	} else {
		$container->printError('No spirits selected');
	}
}

function getOldSpirits() {
	global $spiritScoresTable, $scoreSubmissionsTable, $teamsTable, $leaguesTable, $oldSpiritObj;
	
	$spiritQuery = mysql_query ("SELECT * FROM $spiritScoresTable 
		INNER JOIN $scoreSubmissionsTable ON $scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id 
		INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_opp_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		WHERE spirit_score_ignored = 0 AND spirit_score_is_admin_addition = 1") or die('ERROR getting bad spirit data '.mysql_error());
	$spiritCount=0;
	while($spiritArray=mysql_fetch_array($spiritQuery)){
		$oldSpiritObj[$spiritCount] = new Spirit();
		$oldSpiritObj[$spiritCount]->spiritID = $spiritArray['spirit_score_id'];
		$oldSpiritObj[$spiritCount]->spiritTeamID = $spiritArray['team_id'];
		$oldSpiritObj[$spiritCount]->spiritTeamName = $spiritArray['team_name'];
		$oldSpiritObj[$spiritCount]->spiritLeagueID = $spiritArray['league_id'];
		$oldSpiritObj[$spiritCount]->spiritLeagueName = $spiritArray['league_name'].' - '.dayString($spiritArray['league_day_number']);
		$oldSpiritObj[$spiritCount]->spiritValue = $spiritArray['spirit_score_edited_value'];
		$oldSpiritObj[$spiritCount]->spiritSubmittedDate = $spiritArray['score_submission_datestamp'];
		$spiritCount++;
	} 
	return $spiritCount;
}

function printAddSpiritForm() { ?>
	<tr>
    	<td>
        	<input type='text' name='spiritValue' value=0 />
            <button name="addSpiritScore" value=1>Add Spirit Score</button>
        </td>
    </tr>
<?php }

function printTopInfo($sportsDropDown, $leaguesDropDown, $teamsDropDown) { ?>
	Sport
	<select id='userInput' name='sportID' onchange='reloadPageSport()'>
		<?php print $sportsDropDown; ?>
	</select><br /><br />
	League
	<select id='userInput' name='leagueID' onchange='reloadPageLeague()'>
		<?php print $leaguesDropDown; ?>
	</select><br /><br />
	Team
	<select id='userInput' name='teamID' onchange='reloadPageTeam()'>
		<option value=0>-- Team Name --</option>
		<?php print $teamsDropDown; ?>
	</select>
<?php }

function printOldSpiritsHeader() { ?>
	<tr>
		<th colspan=6>
			Current Spirits
		</th>
	</tr><tr>
    	<td>
        	#
        </td><td>
        	Team
        </td><td>
        	League
        </td><td>
        	Spirit Score
        </td><td>
        	Submission Date
        </td><td>
        	Delete
        </td>
    </tr>
<?php }

function printOldSpiritNode($curSpirit, $i) { ?>
	<tr>
    	<td>
            <?php print $i+1; ?>
		</td><td>
        	<?php print $curSpirit->spiritTeamName ?>
		</td><td>
        	<?php print $curSpirit->spiritLeagueName ?>
		</td><td>
        	<?php print $curSpirit->spiritValue ?>
        </td><td>
        	<?php print $curSpirit->spiritSubmittedDate ?>
        </td><td style="vertical-align:middle">
        	<input type='checkbox' name='deleteOldSpirit[]' VALUE=<?php print $curSpirit->spiritID ?>>
        </td>
    </tr>
<?php }

function printOldSpiritsButtons($spiritCount) {
	if($spiritCount > 0){?> 
		<tr>
			<td colspan=5>
				<input type='submit' name='deleteSpiritScores' value='Delete'>
			</td><td>
				<input type='button' name='CheckAll' value='Check All' onClick="checkAllSpirits()">
                <input type='button' name='UncheckAll' value='Uncheck All' onClick="uncheckAllSpirits()">
			</td>
		</tr>
	<?php }else{ ?>
		<tr>
			<td colspan=6>
				<i>No spirit scores in holding</i>
			</td>
		</tr>
	<?php }	
}

function getTeamDD($leagueID, $teamID) {
	global $teamsTable;
	$teamsDropDown = '';
	if($leagueID > 0) {
		//teams in dropdown
		$teamsQuery=mysql_query("SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 
			ORDER BY team_num_in_league ASC, team_id DESC");
		while($team = mysql_fetch_array($teamsQuery)) {
			if($team['team_id']==$teamID) {
				$teamsDropDown.=  "<option selected value=$team[team_id]>$team[team_name]</option>";
			} else {
				$teamsDropDown.=  "<option value=$team[team_id]>$team[team_name]</option>";
			}
		}
	}
	return $teamsDropDown;
}