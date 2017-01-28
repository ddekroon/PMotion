<?php /***************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2012
* sptFunctions.php
*
* This file holds all the functions for badSpiritSource.php.
**********************************/ 

function getTeamsBadSpiritInfo($sportID) 
{
	global $spiritScoresTable, $scoreSubmissionsTable, $teamsTable, $datesTable, $seasonsTable, $leaguesTable;
	
	if($sportID == 0) {
		print 'Invalid Sport<br />';
		return NULL;
	}
	
	if($sportID != 99) {
		$sportQuery = " AND league_sport_id = $sportID ";
	} else {
		$sportQuery = '';
	}
	
	$spiritQuery = mysql_query ("SELECT * FROM $spiritScoresTable 
		INNER JOIN $scoreSubmissionsTable ON score_submission_id = spirit_score_score_submission_id
		INNER JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
		INNER JOIN $seasonsTable ON $datesTable.date_season_id = $seasonsTable.season_id
		INNER JOIN $teamsTable ON $teamsTable.team_id = $scoreSubmissionsTable.score_submission_opp_team_id 
		INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id
		WHERE season_available_score_reporter = 1 AND spirit_score_ignored = 0 AND spirit_score_edited_value > 0 $sportQuery
		ORDER BY league_sport_id ASC, league_day_number ASC, team_id ASC") 
		or die('ERROR getting bad spirit data '.mysql_error());
		
	$teamNum = -1;
	$lastTeamID = 0;
	
	while($spiritArray=mysql_fetch_array($spiritQuery))
	{
		if($spiritArray['team_id'] != $lastTeamID) 
		{	
			$lastTeamID = $spiritArray['team_id'];
			$teamsObj[++$teamNum] = new Team();
			$teamsObj[$teamNum]->teamName = $spiritArray['team_name'];
			$teamsObj[$teamNum]->teamID = $spiritArray['team_id'];
			$teamsObj[$teamNum]->teamLeagueID = $spiritArray['team_league_id'];
			$teamsObj[$teamNum]->teamLeagueName = $spiritArray['league_name'].' - '.dayString($spiritArray['league_day_number']);
			$teamsObj[$teamNum]->teamSportID = $spiritArray['league_sport_id'];
			$teamsObj[$teamNum]->teamSeasonID = $spiritArray['league_season_id'];
			
			$teamID = $spiritArray['team_id'];
			
			$droppedQuery = mysql_query("SELECT team_dropped_out FROM $teamsTable WHERE team_id = $teamID") 
				or die('ERROR getting team dropped out info '.mysql_error());
			
			$droppedArray = mysql_fetch_array($droppedQuery);
		
			$teamsObj[$teamNum]->teamDroppedOut = $droppedArray['team_dropped_out'];
		}
		$teamsObj[$teamNum]->teamSpiritTotal += $spiritArray['spirit_score_edited_value'];
		$teamsObj[$teamNum]->teamSpiritNumbers++;
	} 
	usort($teamsObj, 'compareSpirit');
	return $teamsObj;
}

function compareSpirit($a, $b) {
    if ($a->getSpiritAverage() == $b->getSpiritAverage()) {
        return 0;
    }
    return ($a->getSpiritAverage() < $b->getSpiritAverage()) ? -1 : 1;
}

function printTeamsHeader($sportsDD, $sportID, $spiritValue, $teamDroppedOut) { ?>
	<div class='getIDs'>
		Sport <select name="sportID" id="userInput" onChange="reloadPage()">
			<?php print $sportsDD ?>
			<option <?php print $sportID == 99?'selected':'' ?> value='99'>All Sports</option>
		</select><br /><br />
		Spirit
		<select name="spiritValue" id="userInput" onChange="reloadPage()">
			<?php for($i = 5; $i >= 0; $i-= 0.5) { 
				 print '<option ';
				 print $spiritValue == $i?'selected':''; 
				 print " value=$i>$i</option>";
			}?>
		</select><br /><br />


	</div><div class='tableData'>
		<table>
			<tr>
				<th colspan=4>
					Teams List
				</th>
			</tr><tr>
				<td style="width:20px;">
					#
				</td><td>
					Team Name
				</td><td>
					League Name
				</td><td>
					Spirit Average
				</td>
			</tr>
            
	<? return $showDropOut; ?>
<?php }

function printTeamNode($teamCount, $teamNode, $showDropped) 
{	
	if($showDropped == 1)
	{
		if($teamNode->teamDroppedOut != 1)
		{?>
		<tr>
    		<td style="width:20px;">
           	 	<?php print $teamCount; ?>
			</td><td>
           	 	<?php print "<a target='_blank' href='/control/Search/teamPage.php?teamID=".$teamNode->teamID."'>".$teamNode->teamName.'</a>'; ?>
       		</td><td>
            	<?php print $teamNode->teamLeagueName; ?>
       		 </td><td>
            	<?php print $teamNode->getSpiritAverage(); ?>
       		 </td>
    	</tr>
	<?php }
		else
		{?>
		<tr>
    		<td style="width:20px;">
           	 		<?php print $teamCount; ?>
				</td><td>
       				<?php $teamName = substr_replace($teamNode->teamName, 'Team Dropped - ',0,0);
					print $teamName; ?>
       			</td><td>
            		<?php print $teamNode->teamLeagueName; ?>
       	 		</td><td>
            		<?php print $teamNode->getSpiritAverage(); ?>
        		</td>
    		</tr>
	<?php }
	}
	
	else
	{
		if($teamNode->teamDroppedOut != 1)
		{?>
		<tr>
    		<td style="width:20px;">
            	<?php print $teamCount; ?>
			</td><td>
            	<?php print "<a target='_blank' href='/control/Search/teamPage.php?teamID=".$teamNode->teamID."'>".$teamNode->teamName.'</a>'; ?>
       		</td><td>
           		<?php print $teamNode->teamLeagueName; ?>
        	</td><td>
            	<?php print $teamNode->getSpiritAverage(); ?>
        	</td>
    	</tr>
	<?php }
	}
}

