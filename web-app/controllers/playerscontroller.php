<?php

class Controllers_PlayersController extends Controllers_Controller {

	function deletePlayer($player) {
		try {
			$sql = "DELETE FROM " . Includes_DBTableNames::playersTable . " WHERE player_id = " . $player->getId();

			$this->db->exec($sql);

			return true;

		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
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
