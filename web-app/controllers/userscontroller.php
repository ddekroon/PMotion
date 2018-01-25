<?php

class Controllers_UsersController extends Controllers_Controller {
		
	function getActiveScheduledMatchesForUser($user) {
		
		if(!isset($user) || $user == null || $user->getId() == null) {
			return [];
		}
			
		$sql = "SELECT sm.* FROM " . Includes_DBTableNames::scheduledMatchesTable . " as sm "
				. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON sm.scheduled_match_league_id = league.league_id "
				. "INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON league.league_season_id = season.season_id "
						. "AND (season.season_available_registration = 1 OR season.season_available_score_reporter = 1) "
				. "INNER JOIN " . Includes_DBTableNames::teamsTable . " teamOne ON teamOne.team_id = sm.scheduled_match_team_id_1 "
				. "INNER JOIN " . Includes_DBTableNames::teamsTable . " teamTwo ON teamTwo.team_id = sm.scheduled_match_team_id_2 "
				. "WHERE (teamOne.team_managed_by_user_id = " . $user->getId() . " AND teamOne.team_finalized = 1 AND teamOne.team_dropped_out = 0) "
				. "OR (teamTwo.team_managed_by_user_id = " . $user->getId() . " AND teamTwo.team_finalized = 1 AND teamTwo.team_dropped_out = 0)";

		$stmt = $this->db->query($sql);

		$results = [];

		while($row = $stmt->fetch()) {
			$results[] = Models_Team::withRow($this->db, $this->logger, $row);
		}

		return $results;
	}
}
