<?php

	class Controllers_DatesController extends Controllers_Controller {

		public function getNextLeagueDate($league) {

			$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable . " "
					. "WHERE date_week_number > " . $league->getWeekInScoreReporter() 
					. " AND date_sport_id = " . $league->getSportId() . " AND date_season_id = " . $league->getSeasonId() 
					. " AND date_day_number = " . $league->getDayNumber() . " ORDER BY date_week_number ASC";
			
			$stmt = $this->db->query($sql);

			$dateArray = $stmt->fetch();
			
			if($dateArray) {
				return Models_date::withRow($this->db, $this->logger, $dateArray);
			} else {
				return null;
			}
		}

		public function getFilteredDates($sportId, $seasonId, $dayNumber) {

			if($sportId == null || $seasonId == null || $dayNumber == null) {
				return [];
			}

			$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable
					. " WHERE date_sport_id = " . $sportId . " AND date_season_id = " . $seasonId 
					. " AND date_day_number = " . $dayNumber
					. " ORDER BY date_week_number ASC";

			$stmt = $this->db->query($sql);

			$results = [];

			while($row = $stmt->fetch()) {
				$results[] = Models_Date::withRow($this->db, $this->logger, $row);
			}

			return $results;
		}
	}

?>