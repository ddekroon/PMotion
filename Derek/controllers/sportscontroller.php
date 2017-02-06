<?php

class Controllers_SportsController extends Controllers_Controller {
	
	public function getSportById($leagueID) {
		$sql = "SELECT * FROM $this->sportsTable as sport "
				. "WHERE sport.sport_id = $leagueID ";
						
		$stmt = $this->db->query($sql);
		
        if(($row = $stmt->fetch()) != false) {		
            return new Models_Sport($row);
        }
	}
	
    public function getSports() {

        $sql = "SELECT * FROM $this->sportsTable as sport "
				. "WHERE sport.sport_id > 0 "
				. "ORDER BY sport.sport_id ASC";
		
        $stmt = $this->db->query($sql);

        $results = [];
		
        while($row = $stmt->fetch()) {
            $results[] = new Models_Sport($row);
        }
		
        return $results;
    }
}
