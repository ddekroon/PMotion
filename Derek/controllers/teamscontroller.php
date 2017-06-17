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
}
