<?php

class Models_Season extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $name;
	protected $year;
	protected $isAvailableRegistration;
	protected $isAvailableScoreReporter;
	protected $numWeeks;
	protected $registrationOpensDate;
	protected $confirmationDueBy;
	protected $registrationDueBy;
	protected $registrationOpenUntil;
	protected $registrationBySport;
    		
	public static function withID($db, $id) {
		$instance = new self();
        $instance->loadByID($db, $id);
        return $instance;
	}
	
	public function loadByID($db, $id) {
		$this->setDb($db);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::seasonsTable . " WHERE season_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, array $row) {
		$instance = new self();
		$instance->setDb($db);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
		// no id if we're creating
        if(isset($data['season_id'])) {
            $this->id = $data['season_id'];
        }
		
		$this->name = $data['season_name'];
		$this->year = $data['season_year'];
		$this->isAvailableRegistration = $data['season_available_registration'] > 0;
		$this->isAvailableScoreReporter = $data['season_available_score_reporter'] > 0;
		$this->numWeeks = $data['season_num_weeks'];
		$this->registrationOpensDate = $data['season_registration_opens_date'];
		$this->confirmationDueBy = $data['season_confirmation_due_by'];
		$this->registrationDueBy = $data['season_registration_due_by'];
		$this->registrationOpenUntil = $data['season_registration_up_until'];
		$this->registrationBySport = $data['season_registration_by_sport'];
	}
	
	function getId() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function getYear() {
		return $this->year;
	}

	function getIsAvailableRegistration() {
		return $this->isAvailableRegistration;
	}

	function getIsAvailableScoreReporter() {
		return $this->isAvailableScoreReporter;
	}

	function getNumWeeks() {
		return $this->numWeeks;
	}

	function getRegistrationOpensDate() {
		return $this->registrationOpensDate;
	}

	function getConfirmationDueBy() {
		return $this->confirmationDueBy;
	}

	function getRegistrationDueBy() {
		return $this->registrationDueBy;
	}

	function getRegistrationOpenUntil() {
		return $this->registrationOpenUntil;
	}

	function getRegistrationBySport() {
		return $this->registrationBySport;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setYear($year) {
		$this->year = $year;
	}

	function setIsAvailableRegistration($isAvailableRegistration) {
		$this->isAvailableRegistration = $isAvailableRegistration;
	}

	function setIsAvailableScoreReporter($isAvailableScoreReporter) {
		$this->isAvailableScoreReporter = $isAvailableScoreReporter;
	}

	function setNumWeeks($numWeeks) {
		$this->numWeeks = $numWeeks;
	}

	function setRegistrationOpensDate($registrationOpensDate) {
		$this->registrationOpensDate = $registrationOpensDate;
	}

	function setConfirmationDueBy($confirmationDueBy) {
		$this->confirmationDueBy = $confirmationDueBy;
	}

	function setRegistrationDueBy($registrationDueBy) {
		$this->registrationDueBy = $registrationDueBy;
	}

	function setRegistrationOpenUntil($registrationOpenUntil) {
		$this->registrationOpenUntil = $registrationOpenUntil;
	}

	function setRegistrationBySport($registrationBySport) {
		$this->registrationBySport = $registrationBySport;
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
