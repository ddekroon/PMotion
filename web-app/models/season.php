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
    		
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::seasonsTable . " WHERE season_id = $id";

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
		// no id if we're creating
        if(isset($data['season_id'])) {
            $this->id = $data['season_id'];
        }
		
		$this->name = $data['season_name'];
		$this->year = $data['season_year'];
		$this->isAvailableRegistration = $data['season_available_registration'] > 0;
		$this->isAvailableScoreReporter = $data['season_available_score_reporter'] > 0;
		$this->numWeeks = $data['season_num_weeks'];
		$this->registrationOpensDate = strtotime($data['season_registration_opens_date']);
		$this->confirmationDueBy = strtotime($data['season_confirmation_due_by']);
		$this->registrationDueBy = strtotime($data['season_registration_due_by']);
		$this->registrationOpenUntil = strtotime($data['season_registration_up_until']);
		$this->registrationBySport = $data['season_registration_by_sport'] > 0;
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

	function getIsRegistrationBySport() {
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
