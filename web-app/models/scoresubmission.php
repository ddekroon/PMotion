<?php

class Models_ScoreSubmission extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $teamId;
	protected $oppTeamId;
	protected $dateId;
	protected $submitterName;
	protected $submitterEmail;
	protected $result;
	protected $scoreUs;
	protected $scoreThem;
	protected $ignored;
	protected $dateStamp;
	protected $dontShow;
	protected $isPhantom;
	
	private $spiritScore;
	private $scoreSubmissionComment;
	private $team;
	private $oppTeam;
	private $date;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::scoreSubmissionsTable . " WHERE score_submission_id = $id";

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
        if(isset($data['score_submission_id'])) {
            $this->id = $data['score_submission_id'];
        }
		
        $this->teamId = $data['score_submission_team_id'];
        $this->oppTeamId = $data['score_submission_opp_team_id'];
        $this->dateId = $data['score_submission_date_id'];
        $this->submitterName = $data['score_submission_submitter_name'];
        $this->submitterEmail = $data['score_submission_submitter_email'];
        $this->result = $data['score_submission_result'];
        $this->scoreUs = $data['score_submission_score_us'];
        $this->scoreThem = $data['score_submission_score_them'];
        $this->ignored = $data['score_submission_ignored'] == 1;
        $this->dateStamp = $data['score_submission_datestamp'];
        $this->dontShow = $data['score_submission_dont_show'] == 1;
        $this->isPhantom = $data['score_submission_is_phantom'] == 1;
    }
	
	public function getSpiritScore() {
		if($this->spiritScore == null && $this->db != null && $this->getId() > 0) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::spiritScoresTable . " WHERE spirit_score_score_submission_id = " . $this->getId() . " LIMIT 1";

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$spiritScore = new Models_SpiritScore();
				$spiritScore->fill($row);
				$spiritScore->setScoreSubmission($this);
				$this->spiritScore = $spiritScore;
			}
		}
		
		return $this->spiritScore;
	}
	
	public function setSpiritScore($spiritScore) {
		$this->spiritScore = $spiritScore;
	}

	public function getScoreSubmissionComment() {
		
		if($this->scoreSubmissionComment == null && $this->db != null && $this->getId() > 0) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::scoreCommentsTable . " WHERE comment_score_submission_id = " . $this->getId() . " LIMIT 1";

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$comment = new Models_ScoreSubmissionComment();
				$comment->fill($row);
				$comment->setScoreSubmission($this);
				$this->scoreSubmissionComment = $comment;
			}
		}
		
		return $this->scoreSubmissionComment;
	}

	public function setScoreSubmissionComment($scoreSubmissionComment) {
		$this->scoreSubmissionComment = $scoreSubmissionComment;
	}

	public function getTeam() {
		if($this->team == null && $this->db != null && $this->getTeamId() != null) {
			$this->team = Models_Team::withID($this->db, $this->logger, $this->getTeamId());
		}
		
		return $this->team;
	}
	
	public function getOppTeam() {
		if($this->oppTeam == null && $this->db != null && $this->getOppTeamId() != null) {
			$this->oppTeam = Models_Team::withID($this->db, $this->logger, $this->getOppTeamId());
		}
		
		return $this->oppTeam;
	}
	
	public function getDate() {
		if($this->date == null && $this->db != null && $this->getDateId() != null) {
			$this->date = Models_Date::withID($this->db, $this->logger, $this->getDateId());
		}
		
		return $this->date;
	}
	
	public function setDate(Models_Date $date) {
		$this->date = $date;
		
		if($date != null) {
			$this->setDateId($date->getId());
		}
	}
	
	public function getResultsString() {
		switch($this->getResult()) {
			case Includes_GameResults::WIN:
				return "Won";
			case Includes_GameResults::LOSS:
				return "Lost";
			case Includes_GameResults::TIE:
				return "Tied";
			case Includes_GameResults::CANCELLED:
				return "Cancelled";
			case Includes_GameResults::PRACTICE:
				return "Practiced";
			case Includes_GameResults::ERROR:
				return "*";
			default:
				return "Error";
		}
	}
	
    public function getId() {
        return $this->id;
    }
	
	public function getDateId() {
		return $this->dateId;
	}

	public function getOppTeamId() {
		return $this->oppTeamId;
	}
	
	public function setOppTeamId($oppTeamId) {
		$this->oppTeamId = $oppTeamId;
	}
	
	function getTeamId() {
		return $this->teamId;
	}

	function getSubmitterName() {
		return $this->submitterName;
	}

	function getSubmitterEmail() {
		return $this->submitterEmail;
	}

	function getResult() {
		return $this->result;
	}

	function getScoreUs() {
		return $this->scoreUs;
	}

	function getScoreThem() {
		return $this->scoreThem;
	}

	function getIsIgnored() {
		return $this->ignored;
	}

	function getDateStamp() {
		return $this->dateStamp;
	}

	function getIsDontShow() {
		return $this->dontShow;
	}

	function getIsPhantom() {
		return $this->isPhantom;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setSubmitterName($submitterName) {
		$this->submitterName = $submitterName;
	}

	function setSubmitterEmail($submitterEmail) {
		$this->submitterEmail = $submitterEmail;
	}

	function setResult($result) {
		$this->result = $result;
	}

	function setScoreUs($scoreUs) {
		$this->scoreUs = $scoreUs;
	}

	function setScoreThem($scoreThem) {
		$this->scoreThem = $scoreThem;
	}

	function setIsIgnored($ignored) {
		$this->ignored = $ignored;
	}

	function setDateStamp($dateStamp) {
		$this->dateStamp = $dateStamp;
	}

	function setIsDontShow($dontShow) {
		$this->dontShow = $dontShow;
	}

	function setIsPhantom($isPhantom) {
		$this->isPhantom = $isPhantom;
	}
	
	function setId($id) {
		$this->id = $id;
	}

	function setDateId($dateId) {
		$this->dateId = $dateId;
	}

		
	function validate(Models_League $league, $matchNum) {
		
		$error = '';
		
		if ($league == null) {
			throw new Exception('No League Given');
		}
		
		if ($this->getTeamId() == null || $this->getTeamId() <= 1) { //Team 1 is practice. Can't submit a score for them.
			$error .= 'Invalid Team Given for match ' .$matchNum;
		}

		if(!$league->getIsInPlayoffs()) {
			
			$spirit = $this->getSpiritScore() != null ? $this->getSpiritScore()->getValue() : -1;
			$comment = $this->getScoreSubmissionComment() != null ? $this->getScoreSubmissionComment()->getComment() : "";
			
			if ($this->getOppTeamId() == null || $this->getOppTeamId() <= 0) {
				$error .= '* Please enter a team for match ' . $matchNum . "\n";
			}

			if ($this->getSpiritScore() != null && $spirit < 3.5 && ($this->getScoreSubmissionComment() == null || strlen($comment) < 2) && $this->getOppTeamId() != 1) {
				$error .= '* Please enter the reason for spirit being so low in match ' . $matchNum . "\n";
			}
			
			if ($this->getOppTeamId() == $this->getTeamId()) {
				$error .= '* Please select a different opposing team for game ' . $matchNum . "\n";
			}
			
			if ($this->getOppTeamId() != 1 && $this->getResult() == null) { //Not practice
				$error .= '* Please enter a result for game ' . $matchNum . "\n";
			}
		}

		if (strlen($this->getSubmitterName()) < 2 || preg_match("/[^A-Za-z0-9\'\- @\.]/", $this->getSubmitterName())) { //Too short or invalid characters
			$error .= '* Please enter your name'. "\n";
		}
		
		if(strlen($error) > 0) {
			throw new Exception($error);
		}
	}
	
	public function saveOrUpdate() {
		if($this->getId() == null) {
			$this->save();
		} else {
			$this->update();
		}
	}
	
	public function save() {
		try {
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::scoreSubmissionsTable . " "
					. "(
						score_submission_team_id,
						score_submission_opp_team_id, 
						score_submission_date_id, 
						score_submission_submitter_name, 
						score_submission_submitter_email, 
						score_submission_result, 
						score_submission_score_us, 
						score_submission_score_them, 
						score_submission_ignored, 
						score_submission_datestamp, 
						score_submission_dont_show, 
						score_submission_is_phantom
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)"
			);
						
			$this->db->beginTransaction(); 
						
			$stmt->execute(
				array(
					$this->getTeamId(), 
					$this->getOppTeamId(), 
					$this->getDateId(), 
					$this->getSubmitterName(), 
					$this->getSubmitterEmail(), 
					$this->getResult(), 
					$this->getScoreUs(), 
					$this->getScoreThem(), 
					$this->getIsIgnored() ? 1 : 0, 
					$this->getIsDontShow() ? 1 : 0, 
					$this->getIsPhantom() ? 1 : 0
				)
			);
			
			$this->setId($this->db->lastInsertId());
			$this->db->commit();
			
			if($this->scoreSubmissionComment != null) {
				$this->scoreSubmissionComment->setScoreSubmissionId($this->getId());
				$this->scoreSubmissionComment->saveOrUpdate();
			}
			
			if($this->spiritScore != null) {
				$this->spiritScore->setScoreSubmissionId($this->getId());
				$this->spiritScore->saveOrUpdate();
			}
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->debug($ex->getMessage()); 
		}
	}
	
	public function update() {
		try {
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::scoreSubmissionsTable . " SET "
					. "
						score_submission_team_id = ?,
						score_submission_opp_team_id = ?, 
						score_submission_date_id = ?,
						score_submission_submitter_name = ?,
						score_submission_submitter_email = ?,
						score_submission_result = ?,
						score_submission_score_us = ?,
						score_submission_score_them = ?,
						score_submission_ignored = ?,
						score_submission_datestamp = NOW(),
						score_submission_dont_show = ?,
						score_submission_is_phantom = ?
					WHERE score_submission_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getTeamId(), 
					$this->getOppTeamId(), 
					$this->getDateId(), 
					$this->getSubmitterName(), 
					$this->getSubmitterEmail(), 
					$this->getResult(), 
					$this->getScoreUs(), 
					$this->getScoreThem(), 
					$this->getIsIgnored() ? 1 : 0, 
					$this->getIsDontShow() ? 1 : 0, 
					$this->getIsPhantom() ? 1 : 0,
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
