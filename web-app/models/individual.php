<?php

/* Created by Kyle Conrad - Summer 2018 */

class Models_Individual extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $playerID;
	protected $phoneNumber = '';
	protected $preferredLeagueID;
	protected $dateCreated;
	protected $isFinalized = false;
	protected $managedByID = null;
	protected $groupID = 0;
	protected $paymentMethod = 0;
	protected $howHeardMethod = 0;
	protected $howHeardOtherText = '';

	private $player;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::individualsTable . " WHERE individual_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
        $instance->fill( $row );
        return $instance;
	}

	public function fill(array $data) {
		
		if(empty($data)) {
			return;
		}
		
		if(isset($data['individual_id'])) {
            $this->id = $data['individual_id'];
        }
		
		$this->playerID = $data['individual_player_id'];
		$this->phoneNumber = $data['individual_phone'];
		$this->preferredLeagueID = $data['individual_preferred_league_id'];
		$this->dateCreated = new DateTime($data['individual_created']);
		$this->isFinalized = $data['individual_finalized'] > 0;
		$this->managedByID = $data['individual_managed_by_user_id'];
		$this->groupID = $data['individual_small_group_id'];
		$this->paymentMethod = $data['individual_payment_method'];
		$this->howHeardMethod = $data['individual_hear_method'];
		$this->howHeardOtherText = $data['individual_hear_other_text'];
	}

	public function getPlayer() {
		if($this->player == null && $this->getPlayerID() != null && $this->db != null) {
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_id = " . $this->getPlayerID();
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->player = Models_Player::withRow($this->db, $this->logger, $row);
			}
		}
		
		if($this->player == null) {
			$this->player = Models_Player::withID($this->db, $this->logger, -1);
		}
		
		return $this->player;
	}

	public function setPlayer($player) {
		$this->player = $player;
	}

	function getPlayerID() {
		return $this->playerID;
	}

	function getPhoneNumber() {
		return $this->phoneNumber;
	}

	function getPreferredLeagueID() {
		return $this->preferredLeagueID;
	}

	function getDateCreated() {
		return $this->dateCreated;
	}

	function getIsFinalized() {
		return $this->isFinalized;
	}

	function getManagedByID() {
		return $this->managedByID;
	}

	function getGroupID() {
		return $this->groupID;
	}

	function getPaymentMethod() {
		return $this->paymentMethod;
	}

	function getHowHeardMethod() {
		return $this->howHeardMethod;
	}

	function getHowHeardOtherText() {
		return $this->howHeardOtherText;
	}

	function setPlayerID($playerID) {
		$this->playerID = $playerID;
	}

	function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	function setPreferredLeagueID($preferredLeagueID) {
		$this->preferredLeagueID = $preferredLeagueID;
	}

	function setDateCreated($dateCreated) {
		$this->dateCreated = $dateCreated;
	}

	function setIsFinalized($isFinalized) {
		$this->isFinalized = $isFinalized;
	}

	function setManagedByID($managedByID) {
		$this->managedByID = $managedByID;
	}

	function setGroupID($groupID) {
		$this->groupID = $groupID;
	}

	function setPaymentMethod($paymentMethod) {
		$this->paymentMethod = $paymentMethod;
	}

	function setHowHeardMethod($howHeardMethod) {
		$this->howHeardMethod = $howHeardMethod;
	}

	function setHowHeardOtherText($howHeardOtherText) {
		$this->howHeardOtherText = $howHeardOtherText;
	}

	public function saveOrUpdate() {
		if($this->getId() == null) {
			$this->save();
		} else {
			$this->update();
		}
	}

	public function save() {
		
		 if(empty($this->getPlayerID())) {
			return;
		} 
		
		try {
			$this->setDateCreated(new DateTime());

			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::individualsTable . " "
					. "(
						individual_player_id, individual_phone, individual_preferred_league_id, individual_created, individual_finalized, individual_managed_by_user_id, individual_small_group_id, individual_payment_method, individual_hear_method, individual_hear_other_text
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getPlayerID(), 
					$this->getPhoneNumber(), 
					$this->getPreferredLeagueID(), 
					$this->getDateCreated() != null ? $this->getDateCreated()->format('Y-m-d H:i:s') : null, 
					$this->getIsFinalized(), 
					$this->getManagedByID(),
					$this->getGroupID(), 
					$this->getPaymentMethod(), 
					$this->getHowHeardMethod(),
					$this->getHowHeardOtherText()
				)
			); 
			$this->setId($this->db->lastInsertId());
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}

	public function update() {
		try {			
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::individualsTable . " SET "
				. "
					individual_player_id = ?, 
					individual_phone = ?, 
					individual_preferred_league_id = ?, 
					individual_created = ?, 
					individual_finalized = ?, 
					individual_managed_by_user_id = ?, 
					individual_small_group_id = ?, 
					individual_payment_method = ?, 
					individual_hear_method = ?, 
					individual_hear_other_text = ?
				WHERE individual_id = ?
				"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getPlayerID(), 
					$this->getPhoneNumber(), 
					$this->getPreferredLeagueID(), 
					$this->getDateCreated() != null ? $this->getDateCreated()->format('Y-m-d H:i:s') : null, 
					$this->getIsFinalized() ? 1 : 0, 
					$this->getManagedByID(),
					$this->getGroupID(), 
					$this->getPaymentMethod(), 
					$this->getHowHeardMethod(),
					$this->getHowHeardOtherText(),
					$this->getId()
				)
			); 
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}

}

?>