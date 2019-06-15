<?php

class Controllers_PrizeWinnersController extends Controllers_Controller {
	
	function getAllPastWinnerUserIds() {

		$stmt = $this->db->query("SELECT prize_team_user_id FROM " . Includes_DBTableNames::prizesTable);
		
		$results = [];

		while($row = $stmt->fetch()) {
			$results[] = Models_PrizeWinner::withRow($this->db, $this->logger, $row);
		}

		return $results;
	}

	function markPastWinners($teams) {
		$pastWinners = getAllPastWinnerUserId();

		foreach($teams as $curTeam) {
			$curTeam->setIsPastPrizeWinner(in_array($curTeam->getManagedByUserId(), $pastWinners));
		}
	}

	function getPrizeWinners($timeFrame, $sports, $leagues) {
		
		if($timeFrame <= 0) {
			return [];
		}

		$sportsFilter = '';
		$leaguesFilter = '';

		if(isset($leagues) && sizeof($leagues) > 0) {
			$leagueIDs = [];
			foreach($leagues as $curLeague) {
				if($curLeague->getId() > 0) {
					$leagueIDs[] = $curLeague->getId();
				}
			}

			if(sizeof($leagueIDs) > 0) {
				$leaguesFilter .= ' AND league.league_id in (' . implode(',', $leagueIDs) . ')';
			}
		}

		if(isset($sports) && sizeof($sports) > 0) {
			$sportsIDs = [];
			foreach($sports as $curSport) {
				if($curSport->getId() > 0) {
					$sportsIDs[] = $curSport->getId();
				}
			}

			if(sizeof($sportsIDs) > 0) {
				$sportsFilter .= ' AND league.league_sport_id in (' . implode(',', $sportsIDs) . ')';
			}
		}
		
		$sql = "SELECT prizeWinner.* FROM " . Includes_DBTableNames::prizesTable . " as prizeWinner"
			. " INNER JOIN " . Includes_DBTableNames::teamsTable . " team ON prizeWinner.prize_team_id = team.team_id"	
			. " INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON team.team_league_id = league.league_id $leaguesFilter $sportsFilter"
			. " INNER JOIN " . Includes_DBTableNames::seasonsTable . " season ON season.season_id = league.league_season_id"
			. " WHERE season_available_score_reporter = 1 AND team_num_in_league > 0 AND prize_time_frame = " . $timeFrame
			. " ORDER BY league_sport_id ASC, league_day_number ASC, team_name ASC";

		
		$stmt = $this->db->query($sql);
	
		$results = [];

		while($row = $stmt->fetch()) {
			$results[] = Models_PrizeWinner::withRow($this->db, $this->logger, $row);
		}

		return $results;
	} 
	
	function markTeamsAsWinners($teams, $timeFrame) {

		if($timeFrame <= 0) {
			return 'No time frame selected';
		}

		if(sizeof($teams) <= 0) {
			return 'No teams selected to mark as winners.';
		}

		foreach($teams as $curTeam) {
			$newPrizeWinner = Models_PrizeWinner::withRow($this->db, $this->logger, []);

			$newPrizeWinner->setWinnerName($curTeam->getManager()->getFirstName() . ' ' . $curTeam->getManager()->getLastName());
			$newPrizeWinner->setWinnerEmail($curTeam->getManager()->getEmail());
			$newPrizeWinner->setDescription('');
			$newPrizeWinner->setIsShowName(true);
			$newPrizeWinner->setIsSent(false);
			$newPrizeWinner->setTeamId($curTeam->getId());
			$newPrizeWinner->setUserId($curTeam->getManagedByUserId());
			$newPrizeWinner->setTimeFrame($timeFrame);
			$newPrizeWinner->save();
		}

		return '';
	}
	
	function removeTeamsAsWinners($teamIDs) {
		if(sizeof($teamIDs) <= 0) {
			return 'No teams selected to remove as winners';
		}

		$sql = "DELETE FROM " . Includes_DBTableNames::prizesTable 
			. " WHERE prize_team_id IN (" . implode(',', $teamIDs) . ')';

		if ($this->db->query($sql) === FALSE) {
			$this->logger->debug("Error deleting prize winners: " . implode(":", $this->db->errorInfo()));
		}

		return '';
	}

	function updateTeamPrizeDescription($team, $prizeDescription) {
		$sql = "UPDATE " . Includes_DBTableNames::prizesTable 
			. " SET prize_description = " . $prizeDescription 
			. " WHERE prize_team_id = " . $team->getId();

		$this->db->query($sql);
	}
}
