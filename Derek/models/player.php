<?php

class Models_Player extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $teamId;
	protected $isCaptain = false;
    protected $firstName = '';
    protected $lastName = '';
    protected $email = '';
    protected $gender = '';
    protected $phoneNumber = '';
    protected $skillLevel = 0;
    protected $isIndividual = false;
    protected $note = '';
    protected $howHeardMethod = 0;
    protected $howHeardOtherText = '';
	
	private $team;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_id = $id";

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
		if(isset($data['player_id'])) {
            $this->id = $data['player_id'];
        }
		
		$this->teamId = $data['player_team_id'];
		$this->isCaptain = $data['player_is_captain'];
		$this->firstName = $data['player_firstname'];
		$this->lastName = $data['player_lastname'];
		$this->email = $data['player_email'];
		$this->gender = $data['player_sex'];
		$this->phoneNumber = $data['player_phone'];
		$this->skillLevel = $data['player_skill'];
		$this->isIndividual = $data['player_is_individual'];
		$this->note = $data['player_note'];
		$this->howHeardMethod = $data['player_hear_method'];
		$this->howHeardOtherText = $data['player_hear_other_text'];
	}
	
	function getTeam() {
		if($this->team == null && $this->db != null) {
			$this->team = Models_League::withID($this->db, $this->logger, $this->teamId);
		}
		
		return $this->team;
	}
	
	function setTeam($team) {
		$this->team = $team;
	}

	function getTeamId() {
		return $this->teamId;
	}

	function getIsCaptain() {
		return $this->isCaptain;
	}

	function getFirstName() {
		return $this->firstName;
	}

	function getLastName() {
		return $this->lastName;
	}

	function getEmail() {
		return $this->email;
	}

	function getGender() {
		return $this->gender;
	}

	function getPhoneNumber() {
		return $this->phoneNumber;
	}

	function getSkillLevel() {
		return $this->skillLevel;
	}

	function getIsIndividual() {
		return $this->isIndividual;
	}

	function getNote() {
		return $this->note;
	}

	function getHowHeardMethod() {
		return $this->howHeardMethod;
	}

	function getHowHeardOtherText() {
		return $this->howHeardOtherText;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setIsCaptain($isCaptain) {
		$this->isCaptain = $isCaptain;
	}

	function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	function setEmail($email) {
		$this->email = $email;
	}

	function setGender($gender) {
		$this->gender = $gender;
	}

	function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	function setSkillLevel($skillLevel) {
		$this->skillLevel = $skillLevel;
	}

	function setIsIndividual($isIndividual) {
		$this->isIndividual = $isIndividual;
	}

	function setNote($note) {
		$this->note = $note;
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
		
		if(empty($this->getFirstName()) && empty($this->getLastName()) && empty($this->getEmail())) {
			return;
		}
		
		try {
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::playersTable . " "
					. "(
						player_team_id, player_is_captain, player_firstname, player_lastname, player_email, player_sex, player_phone, player_skill,
						player_is_individual, player_note, player_hear_method, player_hear_other_text
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getTeamId(), 
					$this->getIsCaptain() ? 1 : 0, 
					$this->getFirstName(), 
					$this->getLastName(), 
					$this->getEmail(), 
					$this->getGender(), 
					$this->getPhoneNumber(), 
					$this->getSkillLevel(), 
					$this->getIsIndividual() ? 1 : 0, 
					$this->getNote(), 
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::playersTable . " SET "
					. "
						player_team_id = ?, 
						player_is_captain = ?, 
						player_firstname = ?,
						player_lastname = ?,
						player_email = ?,
						player_sex = ?,
						player_phone = ?,
						player_skill = ?,
						player_is_individual = ?, 
						player_note = ?,
						player_hear_method = ?,
						player_hear_other_text = ?
					WHERE player_id = ?"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getTeamId(), 
					$this->getIsCaptain() ? 1 : 0, 
					$this->getFirstName(), 
					$this->getLastName(), 
					$this->getEmail(), 
					$this->getGender(), 
					$this->getPhoneNumber(), 
					$this->getSkillLevel(), 
					$this->getIsIndividual() ? 1 : 0, 
					$this->getNote(), 
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
