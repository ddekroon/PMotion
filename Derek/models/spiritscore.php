<?php

class Models_SpiritScore extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $scoreSubmissionId;
	protected $value;
	protected $isIgnored;
	protected $isDontShow;
	protected $editedValue;
	protected $isAdminAddition;
	
	private $scoreSubmission;

    public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::spiritScoresTable . " WHERE spirit_score_id = $id";

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
        if(isset($data['spirit_score_id'])) {
            $this->id = $data['spirit_score_id'];
        }
		
        $this->scoreSubmissionId = $data['spirit_score_score_submission_id'];
		$this->value = $data['spirit_score_value'];
		$this->isIgnored = $data['spirit_score_ignored'];
		$this->isDontShow = $data['spirit_score_dont_show'];
		$this->editedValue = $data['spirit_score_edited_value'];
		$this->isAdminAddition = $data['spirit_score_is_admin_addition'];
    }
	
	function getScoreSubmission() {
		if($this->scoreSubmission == null && $this->db != null) {
			$this->scoreSubmission = Models_ScoreSubmission::withID($this->db, $this->logger, $this->getScoreSubmissionId());
		}
		
		return $this->scoreSubmission;
	}
	
	function setScoreSubmission($scoreSubmission) {
		$this->scoreSubmission = $scoreSubmission;
	}
	
	function getScoreSubmissionId() {
		return $this->scoreSubmissionId;
	}

	function getValue() {
		return $this->value;
	}

	function getIsIgnored() {
		return $this->isIgnored;
	}

	function getIsDontShow() {
		return $this->isDontShow;
	}

	function getEditedValue() {
		return $this->editedValue;
	}

	function getIsAdminAddition() {
		return $this->isAdminAddition;
	}

	function setScoreSubmissionId($scoreSubmissionId) {
		$this->scoreSubmissionId = $scoreSubmissionId;
	}

	function setValue($value) {
		$this->value = $value;
	}

	function setIsIgnored($isIgnored) {
		$this->isIgnored = $isIgnored;
	}

	function setIsDontShow($isDontShow) {
		$this->isDontShow = $isDontShow;
	}

	function setEditedValue($editedValue) {
		$this->editedValue = $editedValue;
	}

	function setIsAdminAddition($isAdminAddition) {
		$this->isAdminAddition = $isAdminAddition;
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
