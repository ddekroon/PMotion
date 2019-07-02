<?php

	class Controllers_ScheduledMatchesController extends Controllers_Controller {

		function getLeagueScheduledMatchesForCurrentWeek($league) {

			if(!isset($league) || $league->getId() <= 0) {
				return [];
			}

			$sql = "SELECT " . Includes_DBTableNames::scheduledMatchesTable . ".* FROM " . Includes_DBTableNames::scheduledMatchesTable . " "
					. "INNER JOIN " . Includes_DBTableNames::datesTable . " ON " . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_date_id = " . Includes_DBTableNames::datesTable . ".date_id "
					. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " ON " . Includes_DBTableNames::scheduledMatchesTable . ".scheduled_match_league_id = " . Includes_DBTableNames::leaguesTable . ".league_id "
					. "WHERE " . Includes_DBTableNames::datesTable . ".date_week_number = " . Includes_DBTableNames::leaguesTable . ".league_week_in_score_reporter "
					. "AND " . Includes_DBTableNames::datesTable . ".date_season_id = " . Includes_DBTableNames::leaguesTable . ".league_season_id AND " . Includes_DBTableNames::leaguesTable . ".league_id = " . $league->getId() . " "
					. "ORDER BY scheduled_match_time";

			$stmt = $this->db->query($sql);
			
			$matches = [];

			//goes through the results and figures out which opponents team teamID had
			while($matchNode = $stmt->fetch()) {
				$matches[] = Models_ScheduledMatch::withRow($this->db, $this->logger, $matchNode);
			}

			return $matches;
		}
	}

?>