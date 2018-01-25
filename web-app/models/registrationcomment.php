<?php

class Models_RegistrationComment extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $userId;
	protected $isTeamComment;
	protected $teamId;
	protected $isIndividualComment;
	protected $playerId;
	protected $comment;
	
	private $team;
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
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::registrationCommentsTable . " WHERE registration_comment_id = $id";

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
        if(isset($data['registration_comment_id'])) {
            $this->id = $data['registration_comment_id'];
        }
		
        $this->userId = $data['registration_comment_user_id'];
		$this->isTeamComment = $data['registration_comment_is_team'];
		$this->teamId = $data['registration_comment_team_id'];
		$this->isIndividualComment = $data['registration_comment_is_individual'];
		$this->playerId = $data['registration_comment_individual_id'];
		$this->comment = $data['registration_comment_value'];
    }
	
	function getTeam() {
		if($this->team == null && $this->db != null) {
			$this->team = Models_Team::withID($this->db, $this->logger, $this->getTeamId());
		}
		
		return $this->team;
	}

	function getPlayer() {
		if($this->player == null && $this->db != null) {
			if($this->getIsTeamComment()) {
				$this->player = Models_Player::withID($this->db, $this->logger, $this->getUserId());
			} else {
				$this->player = Models_Player::withID($this->db, $this->logger, $this->getPlayerId());
			}
		}
		
		return $this->player;
	}
	
	function getUserId() {
		return $this->userId;
	}

	function getIsTeamComment() {
		return $this->isTeamComment;
	}

	function getTeamId() {
		return $this->teamId;
	}

	function getIsIndividualComment() {
		return $this->isIndividualComment;
	}

	function getPlayerId() {
		return $this->playerId;
	}

	function getComment() {
		return $this->comment;
	}

	function setUserId($userId) {
		$this->userId = $userId;
	}

	function setIsTeamComment($isTeamComment) {
		$this->isTeamComment = $isTeamComment;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setIsIndividualComment($isIndividualComment) {
		$this->isIndividualComment = $isIndividualComment;
	}

	function setPlayerId($playerId) {
		$this->playerId = $playerId;
	}

	function setComment($comment) {
		$this->comment = $comment;
	}

	function setTeam($team) {
		$this->team = $team;
	}

	function setPlayer($player) {
		$this->player = $player;
	}

		
	function saveOrUpdate() {
		if($this->getId() == null) {
			$this->save();
		} else {
			$this->update();
		}
	}
	
	function save() {
		if ($this->getComment() !== null && !empty($this->getComment())) {
			try {
				$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::registrationCommentsTable . " "
						. "(
							registration_comment_user_id, registration_comment_is_team, registration_comment_team_id, 
							registration_comment_is_individual, registration_comment_individual_id, registration_comment_value
						) "
						. "VALUES "
						. "(?, ?, ?, ?, ?, ?)"
				);

				$this->db->beginTransaction(); 
				$stmt->execute(
					array(
						$this->getUserId(), 
						$this->getIsTeamComment() ? 1 : 0,
						$this->getTeamId(), 
						$this->getIsIndividualComment() ? 1 : 0, 
						$this->getPlayerId(), 
						$this->getComment()
					)
				); 
				$this->setId($this->db->lastInsertId());
				$this->db->commit(); 

			} catch (Exception $ex) {
				$this->db->rollback();
				$this->logger->log($ex->getMessage()); 
			}
		}
	}
	
	function update() {
		try {
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::registrationCommentsTable . " SET "
					. "
						registration_comment_user_id = ?, 
						registration_comment_is_team = ?, 
						registration_comment_team_id = ?, 
						registration_comment_is_individual = ?, 
						registration_comment_individual_id = ?, 
						registration_comment_value = ?
					WHERE registration_comment_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getUserId(), 
					$this->getIsTeamComment() ? 1 : 0,
					$this->getTeamId(), 
					$this->getIsIndividualComment() ? 1 : 0, 
					$this->getPlayerId(), 
					$this->getComment(),
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
