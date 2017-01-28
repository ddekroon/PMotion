<?php /***************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2012
* scrFunctions.php
*
* This file holds all the functions (except javascript) for addScoreSource.php
**********************************/

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}

if($teamID > 0) {
	$leagueQuery = mysql_query("SELECT league_num_of_games_per_match, league_week_in_score_reporter, league_season_id, league_day_number FROM $leaguesTable WHERE league_id = $leagueID") or die('ERROR getting league data - '.mysql_error());
	$leagueArray = mysql_fetch_array($leagueQuery);
	$numGames = $leagueArray['league_num_of_games_per_match'];
	$weekNum = $leagueArray['league_week_in_score_reporter'];
	$seasonID = $leagueArray['league_season_id'];
	$dayNumber = $leagueArray['league_day_number']; //used for getWeeksDD, need day number so the right dates are obtained for the league
}

function addScore($teamID, $numGames) {
	global $spiritScoresTable, $scoreSubmissionsTable, $scoreCommentsTable, $teamsTable;
	$dateID = $_POST['dateID'];
	$oppTeamID = $_POST['oppTeamID'];
	$gameResult = $_POST['gameResult'];
	$scoreUs = $_POST['scoreUs'];
	$scoreThem = $_POST['scoreThem'];
	$spiritValue = $_POST['spiritValue'];
	if(strlen($_POST['submitterName']) > 0) {
		$submitterName = mysql_escape_string(stripslashes($_POST['submitterName']));
	} else {
		$submitterName = 'Phantom Score';
	}
	$submitterEmail = mysql_escape_string(stripslashes($_POST['submitterEmail']));
	$gameNote = mysql_escape_string(stripslashes($_POST['gameNote']));
	
	//print $dateID.' - '.$teamID.' - '.$oppTeamID.' - '.$gameResult[0].' - '.$scoreUs[0].' - '.$scoreThem[0].' - '.$spiritValue.'<br />';
	//print $submitterName.' - '.$submitterEmail.' - '.$gameNote.'<br />';
	
	if($oppTeamID != 0 && $gameResult != 0) {
		$submissionQuery = mysql_query("SELECT MAX(score_submission_id) as maxnum FROM $scoreSubmissionsTable") or die('ERROR getting new score submission number - '.mysql_error());
		$submissionArray = mysql_fetch_array($submissionQuery);
		$newScoreSubmissionNum = $submissionArray['maxnum']+1;
		
		for($i = 0; $i< $numGames; $i++) {
			mysql_query("INSERT INTO $scoreSubmissionsTable (score_submission_id, score_submission_team_id, score_submission_opp_team_id, 
				score_submission_date_id, score_submission_submitter_name, score_submission_submitter_email, score_submission_result, score_submission_score_us, 
				score_submission_score_them, score_submission_ignored, score_submission_datestamp, score_submission_is_phantom) 
				VALUES ($newScoreSubmissionNum, $teamID, $oppTeamID, $dateID, '$submitterName', '$submitterEmail', $gameResult[$i], $scoreUs[$i], $scoreThem[$i], 0, NOW(), 1)") 
				or die ('Error with inserting into score submission db - '.mysql_error());
			if ($gameResult[$i] == 1) { //When a score gets deleted, this part sets each teams standings who got deleted back one
				mysql_query("UPDATE $teamsTable SET team_wins = team_wins+1 WHERE team_id = $teamID");
			} else if ($gameResult[$i] == 2) {
				mysql_query("UPDATE $teamsTable SET team_losses = team_losses+1 WHERE team_id = $teamID");
			} else if ($gameResult[$i] == 3) {
				mysql_query("UPDATE $teamsTable SET team_ties = team_ties+1 WHERE team_id = $teamID");
			}
		}
		mysql_query("INSERT INTO $spiritScoresTable (spirit_score_score_submission_id, spirit_score_value, spirit_score_ignored, 
			spirit_score_dont_show, spirit_score_edited_value, spirit_score_is_admin_addition) VALUES ($newScoreSubmissionNum, $spiritValue, 
			0, 1, $spiritValue, 1)") or die('spirit score insert - '.mysql_error());	
			
		if (strlen($gameNote) > 2) {
			mysql_query("INSERT INTO $scoreCommentsTable (comment_score_submission_id, comment_value) VALUES
				($newScoreSubmissionNum, '$gameNote')") or die('comments insert - '.mysql_error());
		}	
		
	} else {
		print 'Cannot create score submission - ';
		print $oppTeamID == 0? 'No opposing team selected':'';
		print $gameResult == 0? 'Game result = 0':'';
		print '<br />';
	}
}

function updateDatabase() {
	global $spiritScoresTable, $scoreSubmissionsTable, $teamsTable;
	if(isset($_POST['deleteScore'])) {
		$teamCount = 0;
		foreach($_POST['deleteScore'] as $deleteNum) {
			$scoreID = $_POST['oldScoreID'][$deleteNum];
			$gameResult = $_POST['oldGameResult'][$deleteNum];
			$teamID = $_POST['oldTeamID'][$deleteNum];
			
			mysql_query("UPDATE $scoreSubmissionsTable SET score_submission_ignored = 1 WHERE score_submission_id = $scoreID") 
				or die('ERROR updating spirit '.mysql_error());
			mysql_query("UPDATE $spiritScoresTable SET spirit_score_ignored = 1 WHERE spirit_score_score_submission_id = $scoreID") 
				or die('ERROR updating spirit '.mysql_error());
				
			if ($gameResult == 1) { //When a score gets deleted, this part sets each teams standings who got deleted back one
				mysql_query("UPDATE $teamsTable SET team_wins = team_wins-1 WHERE team_id = $teamID");
			} else if ($gameResult == 2) {
				mysql_query("UPDATE $teamsTable SET team_losses = team_losses-1 WHERE team_id = $teamID");
			} else if ($gameResult == 3) {
				mysql_query("UPDATE $teamsTable SET team_ties = team_ties-1 WHERE team_id = $teamID");
			}
				
			$teamCount++;
		}
		print 'Score\'s updated, '.$teamCount.' teams affected';
	} else {
		print 'No scores selected';
	}
}

/**************************************** THIS IS WHERE WE PUT THE BAR **********************************************/

function getSpiritdropDown() {
	$dropDown="<select name='spiritValue'>
            	<option value=0>N/A</option>";
	for($k = 5; $k >=1; $k-=.5) {
		$dropDown.="<option VALUE=$k>$k</option>";
	}
	$dropDown.='</select>';
	return $dropDown;
}

/**************************************** THIS IS WHERE WE PUT THE BAR **********************************************/

function getdatabaseInfo() {
	global $spiritScoresTable, $scoreSubmissionsTable, $scoreCommentsTable, $teamsTable, $leaguesTable, $seasonsTable, $datesTable, $scoreObj;
	
	$scoreQuery = mysql_query ("SELECT score_submission_id, team_id, score_submission_opp_team_id, team_name, score_submission_result, score_submission_score_us, score_submission_score_them, 
		league_id, league_name, league_day_number, score_submission_datestamp, date_id, date_description
		FROM $scoreSubmissionsTable
		INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $datesTable ON $datesTable.date_id = $scoreSubmissionsTable.score_submission_date_id
		INNER JOIN $seasonsTable ON $leaguesTable.league_season_id = $seasonsTable.season_id
		WHERE score_submission_ignored = 0 AND score_submission_is_phantom = 1 AND season_available_score_reporter = 1 ORDER BY date_day_of_year_num ")
		or die('ERROR getting old scores '.mysql_error());
	$scoreCount=0;
	while($scoreArray=mysql_fetch_array($scoreQuery)){
		$scoreObj[$scoreCount] = new Score();
		$scoreObj[$scoreCount]->scoreID = $scoreArray['score_submission_id'];
		$scoreObj[$scoreCount]->scoreTeamID = $scoreArray['team_id'];
		$scoreObj[$scoreCount]->scoreOppTeamID = $scoreArray['score_submission_opp_team_id'];
		$scoreObj[$scoreCount]->scoreTeamName = $scoreArray['team_name'];
		$scoreObj[$scoreCount]->scoreResult = $scoreArray['score_submission_result'];
		$scoreObj[$scoreCount]->scoreScoreUs = $scoreArray['score_submission_score_us'];
		$scoreObj[$scoreCount]->scoreScoreThem = $scoreArray['score_submission_score_them'];
		
		$teamQuery = mysql_query("SELECT team_name FROM $teamsTable WHERE team_id = ".$scoreArray['score_submission_opp_team_id']) 
			or die('ERROR getting opp team '.$scoreArray['score_submission_opp_team_id'].' - '.mysql_error());
		$teamArray = mysql_fetch_array($teamQuery);
		$scoreObj[$scoreCount]->scoreOppTeamName = $teamArray['team_name'];
		$scoreObj[$scoreCount]->scoreLeagueID = $scoreArray['league_id'];
		$scoreObj[$scoreCount]->scoreLeagueName = $scoreArray['league_name'].' - '.dayString($scoreArray['league_day_number']);
		
		$spiritQuery = mysql_query("SELECT spirit_score_edited_value FROM $spiritScoresTable WHERE spirit_score_score_submission_id = ".$scoreArray['score_submission_id']) 
			or die('ERROR getting spirit score for score submission '.$scoreArray['score_submission_id'].' - '.mysql_error());
		$spiritArray = mysql_fetch_array($spiritQuery);
		$scoreObj[$scoreCount]->scoreSpirit = $spiritArray['spirit_score_edited_value'];
		$scoreObj[$scoreCount]->scoreSubmittedDate = $scoreArray['score_submission_datestamp'];
		$scoreObj[$scoreCount]->scoreDateID = $scoreArray['date_id'];
		$scoreObj[$scoreCount]->scoreGameDay = $scoreArray['date_description'];
		
		$commentQuery = mysql_query("SELECT comment_value FROM $scoreCommentsTable WHERE comment_score_submission_id = ".$scoreArray['score_submission_id']) 
			or die('ERROR getting comment for score submission '.$scoreArray['score_submission_id'].' - '.mysql_error());
		$commentArray = mysql_fetch_array($commentQuery);
		$scoreObj[$scoreCount]->scoreNote = $commentArray['comment_value'];
		$scoreCount++;
	} 
	return $scoreCount;
}

function printAddScoreForm($numGames, $weekNum, $seasonID, $leagueID) { ?>
    <tr>
		<th colspan=3>
			Add Score Form
		</th>
	</tr><tr>
        <td>
            <select name='dateID'>
                <?php print getWeeksDD($seasonID, $weekNum) ?>
            </select>
        </td><td colspan=2>
            <select name='oppTeamID' id='userInput'>
                <option value=0>--Team--</option>
                <?php print getTeamDD($leagueID, 0) ?>
            </select>
        </td>
    </tr>
    <?php for($i=0;$i<$numGames;$i++) { ?>
    <tr>
        <td>
            <select name='gameResult[]'>
                <option value = 0>Result</option>
                <option value = 1>Won</option>
                <option value = 2>Lost</option>
                <option value = 3>Tied</option>
                <option value = 4>Practice</option>
                <option value = 5>Cancelled</option>
            </select>
        </td><td>
            <select name='scoreUs[]'>
                <?php for($i=0;$i<21;$i++) {
                    print "<option value = $i>$i</option>";
                } ?>
            </select>
        </td><td>
            <select name='scoreThem[]'>
                <?php for($i=0;$i<21;$i++) {
                    print "<option value = $i>$i</option>";
                } ?>
            </select>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td>
            Spirit Score:
            <?php print getSpiritdropDown(); ?>
        </td><td>
            Submitter Name:
            <input type="text" name='submitterName' />
        </td><td>
            Submitter Email:
            <input type="text" name='submitterEmail' />
        </td>
    </tr><tr>
        <td colspan=3>
            Game Note:<textarea rows="5" cols="50" name="gameNote"></textarea>
        </td>
    </tr><tr>
        <td colspan=3>
            <button name="addScore" value=1>Add Score</button>
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
		<option value=0>--Team Name--</option>
		<?php print $teamsDropDown; ?>
	</select>
<?php }

function printOldScoresHeader() { ?>
	<tr>
		<th colspan=9>
			Current Phantom Scores
		</th>
	</tr><tr>
    	<td>
        	#
        </td><td>
        	League
        </td><td>
        	Game Week
        </td><td>
        	Team
        </td><td>
        	OppTeam
        </td><td>
        	Result
        </td><td>
        	Spirit
        </td><td>
        	Submission Date
        </td><td>
        	Delete
        </td>
    </tr>
<?php }

function printOldScoreNode($curScore, $i) { ?>
	<tr>
    	<td>
            <?php print $i+1; ?>
            <input type='hidden' name='oldScoreID[]' value=<?php print $curScore->scoreID?> />
		</td><td>
			<?php print $curScore->scoreLeagueName ?>
		</td><td>
			<?php print $curScore->scoreGameDay ?>
		</td><td>
			<?php print $curScore->scoreTeamName ?>
			<input type='hidden' name='oldTeamID[]' value=<?php print $curScore->scoreTeamID?> />
		</td><td>
			<?php print $curScore->scoreOppTeamName ?>
		</td><td>
			<?php print $curScore->getresultString() ?>
			<input type='hidden' name='oldGameResult[]' value=<?php print $curScore->scoreResult?> />
		</td><td>
			<?php print $curScore->scoreSpirit ?>
		</td><td>
			<?php print $curScore->scoreSubmittedDate ?>
		</td>
	    <td style="vertical-align:middle">
	    <INPUT TYPE='checkbox' NAME='deleteScore[]' VALUE=<?php print $i?>>
        </td>
    </tr>
<?php }

function printOldScoresButtons($scoreCount) {
	if($scoreCount > 0){?> 
		<tr>
			<td colspan=6>
				<input type='submit' name='deleteScores' value='Delete'>
			</td>
			<td colspan=3 align="center">
				<input type='button' name='CheckAll' value='Check All' onClick="checkAllScores()">
                <input type='button' name='UncheckAll' value='Uncheck All' onClick="uncheckAllScores()">
			</td>
		</tr>
	<?php }else{ ?>
		<tr>
			<td colspan=9>
				<i>No scores in holding</i>
			</td>
		</tr>
	<?php }	
}

function getTeamDD($leagueID, $teamID) {
	global $teamsTable;
	$teamsDropDown = '';

	//teams in dropdown
	$teamsQuery=mysql_query("SELECT team_id, team_name FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 ORDER BY team_num_in_league ASC, team_id DESC");
	while($team = mysql_fetch_array($teamsQuery)) {
		if($team['team_id']==$teamID) {
			$teamsDropDown.=  "<option selected value=$team[team_id]>$team[team_name]</option>";
		} else {
			$teamsDropDown.=  "<option value=$team[team_id]>$team[team_name]</option>";
		}
	}
	return $teamsDropDown;
}

function getWeeksDD($seasonID, $weekNum) {
	global $datesTable, $seasonsTable, $dayNumber, $sportID;
	$datesDropDown = '';
	
	$datesQuery=mysql_query("SELECT date_id, date_description FROM $datesTable INNER JOIN $seasonsTable ON $seasonsTable.season_id = $datesTable.date_season_id 
					   WHERE season_available_score_reporter = 1 AND date_season_id = $seasonID AND date_day_number = $dayNumber AND date_sport_id = $sportID
					   ORDER BY date_week_number ASC") or die('ERROR getting weeks '.mysql_error());
	while($date = mysql_fetch_array($datesQuery)) {
		if($date['date_week_number']==$weekNum){
			$datesDropDown.="<option selected value= $date[date_id]>$date[date_description]</option><BR>";
		}else{
			$datesDropDown.="<option value= $date[date_id]>$date[date_description]</option>";
		}
	}
	return $datesDropDown;
}