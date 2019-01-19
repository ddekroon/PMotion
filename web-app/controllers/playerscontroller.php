<?php

class Controllers_PlayersController extends Controllers_Controller {

	function deletePlayer($player) {
		$sql = "DELETE FROM " . Includes_DBTableNames::playersTable . " WHERE player_id = " + $player->getId();

		if ($this->db->query($sql) === TRUE) {
			return true;
		} else {
			$this->logger->debug("Error deleting player " . $player . " : " . implode(":", $this->db->errorInfo()));
			return false;
		}
	}

	function getPlayersInGroup($groupId) {

		if($groupId <= 0) {
			return [];
		}

		$sql = "SELECT players.* FROM " . Includes_DBTableNames::individualsTable . " as individuals"
				. " INNER JOIN " . Includes_DBTableNames::playersTable . " as players ON players.player_id = individuals.individual_player_id "
				. " WHERE individual_small_group_id = " . $groupId;

		$players = [];

		$stmt = $this->db->query($sql);

		while(($row = $stmt->fetch()) != false) {
			$players[] = Models_Player::withRow($this->db, $this->logger, $row);
		}

		return $players;
	}
	
}
