<?php

class Models_ScoreSubmissionComment extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $scoreSubmissionId;
	protected $comment;
	
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
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::scoreCommentsTable . " WHERE comment_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
		$instance->setLogger($logger);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
        // no id if we're creating
        if(isset($data['comment_id'])) {
            $this->id = $data['comment_id'];
        }
		
        $this->scoreSubmissionId = $data['comment_score_submission_id'];
		$this->comment = $data['comment_value'];
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

	function getComment() {
		return $this->comment;
	}

	function setComment($value) {
		$this->comment = $value;
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
