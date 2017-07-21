<?php

class Controllers_TeamsController extends Controllers_Controller {
		
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
	
	public function getTeamsForUser($user, $season, $dayNum) {
		
		if(!isset($user) || $user == null) {
			return [];
		}
		
		$sql = "SELECT team.* FROM " . Includes_DBTableNames::teamsTable . " as team "
				. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON league.league_id = team.team_league_id "
				. "WHERE team.team_managed_by_user_id = " . $user->getId();
		
		if(isset($season)) {
			$sql .= " AND league.league_season_id = " . $season->getId();
		}
				
		if(isset($dayNum)) {
			$sql .= " AND league.league_day_number = $dayNum";
		}
								
		$stmt = $this->db->query($sql);
		$results = [];
		
        while(($row = $stmt->fetch()) != false) {		
            $results[] = Models_Team::withRow($this->db, $this->logger, $row);
        }
		
		return $results;
	}
	
	public function saveTeam($team, $request) {
		throw new Exception('TODO: Saving team.');
	}
}
