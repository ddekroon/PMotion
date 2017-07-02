<?php

class Controllers_TeamsController extends Controllers_Controller {
	
	public function getTeamById($teamID) {
		$sql = "SELECT team.* FROM $this->teamsTable as team "
				. "WHERE team.team_id = $teamID";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_Team($row);
        }
	}
	
	function getTeams($leagueID) {
		$league = Models_League::withID($this->db, $this->logger, $leagueID);
		
		if(isset($league) && $league->getId() != null) {
			
			if($league->getIsPracticeGames()) { //league has practice, include the practice team
				$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
						. "WHERE ((team.team_league_id = $leagueID AND team.team_num_in_league > 0) OR team.team_id = 1) AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			} else {
				$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
						. "WHERE team.team_league_id = $leagueID AND team.team_num_in_league > 0 AND team.team_dropped_out = 0 "
						. "ORDER BY team.team_num_in_league";
			}
			
			$stmt = $this->db->query($sql);
			
			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_Team::withRow($this->db, $this->logger, $row);
			}

			return $results;
			
		}
		
		return [];
	}
	
	function compareHeadToHead($teamOne, $teamTwo) {
		$sql = "SELECT SUM(score_submission_score_us) - SUM(score_submission_score_them) as score_differential "
				. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
				. "WHERE score_submission_ignored = 0 AND score_submission_team_id = " . $teamOne->getId() . " AND score_submission_opp_team_id = " . $teamTwo->getId() . " "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		
		return $row["score_differential"];
	}

	function compareCommonPlusMinus($teamOne, $teamTwo) {
		
		$diffOne = 0;
		$diffTwo = 0;
		
		$sql = "SELECT score_submission_team_id, SUM(score_submission_score_us - score_submission_score_them) as common_score_differential "
				. "FROM ("
						. "SELECT score_submission_team_id, score_submission_score_us, score_submission_score_them "
						. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
						. "WHERE score_submission_ignored = 0 AND (score_submission_team_id = " . $teamOne->getId() . " OR score_submission_team_id = " . $teamTwo->getId() . ")"
				. ") as sub_table "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		
		while($row = $stmt->fetch()) {
			if($row['score_submission_team_id'] == $teamOne->getId()) {
				$diffOne = $submission['common_score_differential'];
			} else {
				$diffTwo = $submission['common_score_differential'];
			}
		}
		
		return $diffOne - $diffTwo;
	}

	function comparePoints($teamOne, $teamTwo) {
		
		if ($teamOne->getPoints() == $teamTwo->getPoints()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
				
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getPoints() > $teamTwo->getPoints()) ? -1 : 1;
	}

	function comparePercent($teamOne, $teamTwo) {
		
		if ($teamOne->getWinPercent() == $teamTwo->getWinPercent()) {
			
			if($teamOne->getSpiritAverage() == $teamTwo->getSpiritAverage()) {
				
				$headToHead = compareHeadToHead($teamOne, $teamTwo);
				
				if($headToHead == 0) {
					return compareCommonPlusMinus($teamOne, $teamTwo);
				} else {
					return $headToHead;
				}
			} else {
				return ($teamOne->getSpiritAverage() > $teamTwo->getSpiritAverage()) ? -1 : 1;
			}	
		}
		
		return ($teamOne->getWinPercent() > $teamTwo->getWinPercent()) ? -1 : 1;
	}

	function comparePosition($teamOne, $teamTwo) {
		
		if ($teamOne->getFinalPosition() == $teamTwo->getFinalPosition()) {
			return 0;
		}
		return ($teamOne->getFinalPosition() < $teamTwo->getFinalPosition()) ? -1 : 1;
	}

	function compareSpirit($teamOne, $teamTwo) {
		
		if ($teamOne->getFinalSpiritPosition() == $teamTwo->getFinalSpiritPosition()) {
			return 0;
		}
		return ($teamOne->getFinalSpiritPosition() < $teamTwo->getFinalSpiritPosition()) ? -1 : 1;
	}
	
	//Sorts whether the teams tied
	function checkTied($teams, $curNum, $numTeams, $index) {
		if($curNum == 0 && $curNum == $numTeams - 1) { //if there is only one team for some reason
			return false;
		} else if($curNum == 0 && $curNum != $numTeams - 1) { //first team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum]->getFinalPosition() == $teams[$curNum + 1]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum]->getFinalSpiritPosition() == $teams[$curNum + 1]->getFinalSpiritPosition();
			}
		} else if($curNum > 0 && $curNum != $numTeams - 1) { //some middle team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum - 1]->getFinalPosition() == $teams[$curNum]->getFinalPosition() || $teams[$curNum]->getFinalPosition() == $teams[$curNum + 1]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum - 1]->getFinalSpiritPosition() == $teams[$curNum]->getFinalSpiritPosition() || $teams[$curNum]->getFinalSpiritPosition() == $teams[$curNum + 1]->getFinalSpiritPosition();
			}
		} else { //by default last team in multi team league
			switch($index) {
				case 'finalPosition':
					return $teams[$curNum - 1]->getFinalPosition() == $teams[$curNum]->getFinalPosition();
				case 'finalSpiritPosition':
					return $teams[$curNum - 1]->getFinalSpiritPosition() == $teams[$curNum]->getFinalSpiritPosition();
			}
		}
	}
	
	/* public function getTeamMatchData($activeTeam, $allTeamsInLeague)  {

		// Variable Declortation and Init

		$lastOppTeam = 0;
		$matchNumDay = 0;
		$lastGameTime = 0;
		$lastDate = 0;
		$matchNum = 1;
		$matches = $activeTeam->getScheduledMatches();

		while($matchNode = $datesQuery->fetch_object()) 
		{	
			if($matchNode->scheduled_match_team_id_1 == $teamID) 
			{
				$curOppTeamID = $matchNode->scheduled_match_team_id_2;
				$shirtColour = 'Dark';
			} 
			else 
			{
				$curOppTeamID = $matchNode->scheduled_match_team_id_1;
				$shirtColour = 'Light';
			}

			if($curOppTeamID != $lastOppTeam || $matchNode->date_id != $lastDate || $matchNode->scheduled_match_time != $lastGameTime) 
			{
				if($lastDate != 0 && $lastDate == $matchNode->date_id) 
				{ //same date, next match
					$matchNumDay++;
				}
				else 
				{ //assuming different date
					$matchNumDay = 0;
				}

				$matchObj[$matchNode->date_id][$matchNumDay] = new Match();
				$matchObj[$matchNode->date_id][$matchNumDay]->matchNum = $matchNum++;
				$matchObj[$matchNode->date_id][$matchNumDay]->oppTeamID = $curOppTeamID;
				$matchObj[$matchNode->date_id][$matchNumDay]->dateID = $matchNode->date_id;
				$matchObj[$matchNode->date_id][$matchNumDay]->gameDate = $matchNode->date_description;
				$matchObj[$matchNode->date_id][$matchNumDay]->matchField = $matchNode->venue_short_show_name;
				$matchObj[$matchNode->date_id][$matchNumDay]->matchGameTime = $matchNode->scheduled_match_time;
				$matchObj[$matchNode->date_id][$matchNumDay]->matchFieldLink = $matchNode->venue_link;
				// **********************************
				// $matchObj[$matchNode->date_id][$matchNumDay]->teamDroppedOut = $teamObjs[$curOppTeamID]->team_dropped_out;;
				// **********************************
				$matchObj[$matchNode->date_id][$matchNumDay]->oppTeamName = $teamObjs[$curOppTeamID]->team_name;
				$matchObj[$matchNode->date_id][$matchNumDay]->matchShirtColour = $shirtColour;
				$lastOppTeam = $curOppTeamID;
				$lastGameTime = $matchNode->scheduled_match_time;
				$lastDate = $matchNode->date_id;
			}
		}
		$datesQuery->close();

		if(!($teamScoreSubmissionsQuery = $dbConnection->query("SELECT date_id, score_submission_opp_team_id,
			score_submission_is_phantom, score_submission_result, date_description, team_dropped_out
			FROM $scoreSubmissionsTable 
			INNER JOIN $datesTable ON $scoreSubmissionsTable.score_submission_date_id = $datesTable.date_id
			INNER JOIN $teamsTable ON $scoreSubmissionsTable.score_submission_team_id = $teamsTable.team_id 
			WHERE score_submission_team_id = $teamID AND score_submission_ignored = 0
			ORDER BY date_week_number ASC, score_submission_is_phantom ASC, score_submission_id ASC"))) 
		{
			print 'ERROR getting score submissions - '.$dbConnection->error;
			exit(0);
		}



		$submissionNum = 0;
		$gameNum = 0;
		$lastDateID = 0;
		$lastOppTeamID = 0;

		while($scoreSubmission = $teamScoreSubmissionsQuery->fetch_object()) 
		{
			$dateID = $scoreSubmission->date_id;
			$oppTeamID = $scoreSubmission->score_submission_opp_team_id;

			if(!($teamQuery = $dbConnection->query("SELECT team_dropped_out FROM $teamsTable WHERE team_id = $oppTeamID")))
			{
				print 'ERROR getting team information - '.$dbConnection->error;
				exit(0);
			}

			$teamInfo = $teamQuery->fetch_object();	
			$teamDropped = $teamInfo->team_dropped_out;

			if($dateID != $lastDateID) {
				$matchNumDay = 0;
				$gameNum = 0;
				$lastDateID = $dateID;
				$lastOppTeamID = $oppTeamID;
			} 
			else if($oppTeamID != $lastOppTeamID || $oppTeamID == 0) {
				$matchNumDay++;
				$lastOppTeamID = $oppTeamID;
				$gameNum = 0;
			} else {
				$gameNum++;
			}

			if(isset($matchObj[$dateID][$matchNumDay])) 
			{
				$matchObj[$dateID][$matchNumDay]->gameResults[$gameNum] = $scoreSubmission->score_submission_result;
				$matchObj[$dateID][$matchNumDay]->oppTeamID = $oppTeamID;		
				$matchObj[$dateID][$matchNumDay]->oppDropOut = $teamDropped;

				if($oppTeamID != 1 && $oppTeamID != 0) 
				{
					$matchObj[$dateID][$matchNumDay]->oppTeamName = $teamObjs[$oppTeamID]->team_name;

					if($teamDropped == 0)
					{
						$matchObj[$dateID][$matchNumDay]->standingsString = $teamObjs[$oppTeamID]->getFormattedStandings();
					}
					// When a team has dropped out of the league their name will have a star added to the front to indicate the FormFunctions (no link)
					else
					{
						$matchObj[$dateID][$matchNumDay]->standingsString = '(N/A)';
						$teamName = substr_replace($teamObjs[$oppTeamID]->team_name, '*',0,0);
						$matchObj[$dateID][$matchNumDay]->oppTeamName = $teamName;
					}

				}
				else if ($oppTeamID == 1) 
				{
					$matchObj[$dateID][$matchNumDay]->oppTeamName = 'Practice';
				}
				//  if no team has been selected as opponent (likely because ADMIN cancelled the games), then search schedule to find who they were supposed to play
				else if ($oppTeamID == 0) 
				{ 
					$matchObj[$dateID][$matchNumDay]->oppTeamName = 'CANCELLED';
					$matchesArray = mysql_query("SELECT scheduled_match_team_id_2,scheduled_match_team_id_1 FROM $scheduledMatchesTable 
							Inner Join $datesTable ON $scheduledMatchesTable.scheduled_match_date_id = $datesTable.date_id
							Inner Join $leaguesTable ON $scheduledMatchesTable.scheduled_match_league_id = $leaguesTable.league_id 
							WHERE ($scheduledMatchesTable.scheduled_match_team_id_2 = $teamID OR $scheduledMatchesTable.scheduled_match_team_id_1 = $teamID)
							AND $datesTable.date_week_number = 7
							AND $datesTable.date_season_id = $leaguesTable.league_season_id ORDER BY scheduled_match_time") 
							or die ("Error: ".mysql_error());


					//goes through the results and figures out which opponents team $teamID had
					for($i=0;$i<$matchNumDay+1;$i++) 
					{
						$matchNode=mysql_fetch_array($matchesArray);
				//This if statements checks if both scheduled matches are against the same team, this can happen if you double submit a leagues matches for a week.

						if($i != 0) 
						{
							if(($matchNode['scheduled_match_team_id_1'] == $oppTeamID[$i-1] || $matchNode['scheduled_match_team_id_2'] == $oppTeamID[$i-1]) 
								&& $matchNode['scheduled_match_team_id_1'] != '') 
							{
								$i--;
							}
						}
						if ($matchNode['scheduled_match_team_id_1'] == $teamID) {
							$matchObj[$dateID][$matchNumDay]->oppTeamID = $matchNode['scheduled_match_team_id_2'];
							$oppTeamID = $matchObj[$dateID][$matchNumDay]->oppTeamID;
							$matchObj[$dateID][$matchNumDay]->oppTeamName = $teamObjs[$oppTeamID]->team_name;
							$matchObj[$dateID][$matchNumDay]->standingsString = $teamObjs[$oppTeamID]->getFormattedStandings();
						} 
						else 
						{
							$matchObj[$dateID][$matchNumDay]->oppTeamID = $matchNode['scheduled_match_team_id_1'];
							$oppTeamID = $matchObj[$dateID][$matchNumDay]->oppTeamID;
							$matchObj[$dateID][$matchNumDay]->oppTeamName = $teamObjs[$oppTeamID]->team_name;
							$matchObj[$dateID][$matchNumDay]->standingsString = $teamObjs[$oppTeamID]->getFormattedStandings();
						}
					}
				}
			}
		}
		$teamScoreSubmissionsQuery->close();
		return $matchObj;
	} */
}
