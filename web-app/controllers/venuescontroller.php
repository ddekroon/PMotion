<?php

class Controllers_VenuesController extends Controllers_Controller {
	public function getAllVenuesAsHashmap() {
		$sql = "SELECT * FROM " . Includes_DBTableNames::venuesTable
				. " ORDER BY venue_sport_id ASC";

		$stmt = $this->db->query($sql);

		$results = [];

		while($row = $stmt->fetch()) {
			$results[$row['venue_id']] = Models_Venue::withRow($this->db, $this->logger, $row);
		}

		return $results;
	}
}
