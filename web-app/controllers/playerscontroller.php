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
	
	function updatePlayerFromRequest($player, $request) {
		$allPostVars = $request->getParsedBody();
		$player->setFirstName(array_key_exists('firstName', $allPostVars) ? $allPostVars['firstName'] : '');
		$player->setLastName(array_key_exists('lastName', $allPostVars) ? $allPostVars['lastName'] : '');
		$player->setEmail(array_key_exists('email', $allPostVars) ? $allPostVars['email'] : '');
		$player->setPhoneNumber(array_key_exists('phoneNumber', $allPostVars) ? $allPostVars['phoneNumber'] : '');
		$player->setGender(array_key_exists('gender', $allPostVars) ? $allPostVars['gender'] : '');
		$player->setNote(array_key_exists('note', $allPostVars) ? $allPostVars['note'] : '');
		$player->setIsCaptain(array_key_exists('isCaptain', $allPostVars));
		$player->saveOrUpdate();
	}
}
