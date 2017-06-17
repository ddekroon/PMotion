<?php

class Models_Player extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $teamId;
	protected $isCaptain;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $gender;
    protected $phoneNumber;
    protected $skillLevel;
    protected $isIndividual;
    protected $note;
    protected $howHeardMethod;
    protected $howHeardOtherText;
	
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
	
	function saveOrUpdate() {
		if($this->getId() == null) {
			save();
		} else {
			update();
		}
	}
	
	function save() {
		
	}
	
	function update() {
		
	}
}
