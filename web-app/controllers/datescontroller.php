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
	}

?>