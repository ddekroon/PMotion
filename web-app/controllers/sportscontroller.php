<?php

class Controllers_SportsController extends Controllers_Controller {
	
    public function getSports() {

        $sql = "SELECT * FROM " . Includes_DBTableNames::sportsTable . " WHERE sport_id > 0 ORDER BY sport_id ASC";
		
        $stmt = $this->db->query($sql);
        $results = [];
		
        while($row = $stmt->fetch()) {
            $results[] = Models_Sport::withRow($this->db, $this->logger, $row);
        }
		
        return $results;
    }
}
