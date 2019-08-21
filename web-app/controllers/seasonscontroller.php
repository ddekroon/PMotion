<?php

class Controllers_SeasonsController extends Controllers_Controller {
	
    public function getSeasonsAvailableForRegistration() {

        $sql = "SELECT * FROM " . Includes_DBTableNames::seasonsTable . " WHERE season_available_registration = 1 "; 
		
        $stmt = $this->db->query($sql);
        $results = [];
		
        while($row = $stmt->fetch()) {
            $results[] = Models_Season::withRow($this->db, $this->logger, $row);
        }
		
        return $results;
    }
	
	public function getSeasonsAvailableForScoreReporter() {

        $sql = "SELECT * FROM " . Includes_DBTableNames::seasonsTable . " WHERE season_available_score_reporter = 1 "; 
		
        $stmt = $this->db->query($sql);
        $results = [];
		
        while($row = $stmt->fetch()) {
            $results[] = Models_Season::withRow($this->db, $this->logger, $row);
        }
		
        return $results;
    }
}
