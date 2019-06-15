<?php

class Models_PrizeWinner extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $id;
    protected $winnerName;
	protected $winnerEmail;
	protected $description;
	protected $isShowName;
	protected $isSent;
	protected $teamId;
	protected $userId;
	protected $timeFrame;
	
	private $team;
	private $user;
    		
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::prizesTable . " WHERE prize_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, $logger, array $row) {
		$instance = new self();
		$instance->setDb($db);
		$instance->setLogger($logger);
        $instance->fill($row);
        return $instance;
	}
	
	public function fill(array $data) {
		// no id if we're creating
        if(isset($data['prize_id'])) {
            $this->id = $data['prize_id'];
		}

		$this->$winnerName = $data['prize_winner_name'];
		$this->$winnerEmail = $data['prize_winner_email'];
		$this->$description = $data['prize_description'];
		$this->$isShowName = $data['prize_show_name'] > 0;
		$this->$isSent = $data['prize_sent'] > 0;
		$this->$teamId = $data['prize_team_id'];
		$this->$userId = $data['prize_team_user_id'];
		$this->$timeFrame = $data['prize_time_frame'];
	}
	
	function getTeam() {
		if($this->team == null && $this->db != null) {
			$this->team = Models_Team::withID($this->db, $this->logger, $this->getTeamId());
		}
		
		return $this->team;
	}

	function getUser() {
		if($this->user == null && $this->db != null) {
			$this->user = Models_Usert::withID($this->db, $this->logger, $this->getUserId());
		}
		
		return $this->user;
	}
	
	function setTeam($team) {
		$this->team = $team;
	}

	function setUser($user) {
		$this->user = $user;
	}
	
	function getId() {
		return $this->id;
	}

	function getWinnerName() {
		return $this->winnerName;
	}

	function getWinnerEmail() {
		return $this->winnerEmail;
	}

	function getDescription() {
		return $this->description;
	}

	function getIsShowName() {
		return $this->isShowName;
	}

	function getIsSent() {
		return $this->isSent;
	}

	function getTeamId() {
		return $this->teamId;
	}

	function getUserId() {
		return $this->userId;
	}

	function getTimeFrame() {
		return $this->timeFrame;
	}

	function setWinnerName($winnerName) {
		$this->winnerName = $winnerName;
	}

	function setWinnerEmail($winnerEmail) {
		$this->winnerEmail = $winnerEmail;
	}

	function setDescription($description) {
		$this->description = $description;
	}

	function setIsShowName($isShowName) {
		$this->isShowName = $isShowName;
	}

	function setIsSent($isSent) {
		$this->isSent = $isSent;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setUserId($userId) {
		$this->userId = $userId;
	}

	function setTimeFrame($timeFrame) {
		$this->timeFrame = $timeFrame;
	}
	
	function saveOrUpdate() {
		if($this->getId() == null) {
			save();
		} else {
			update();
		}
	}
	
	public function save() {
		try {			
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::prizesTable . " "
					. "(
						prize_winner_name, prize_winner_email, prize_description, prize_show_name, 
						prize_sent, prize_team_id, prize_team_user_id, prize_time_frame
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getWinnerName(), 
					$this->getWinnerEmail(), 
					$this->getDescription(), 
					$this->getIsShowName() ? 1 : 0, 
					$this->getIsSent() ? 1 : 0, 
					$this->getTeamId(), 
					$this->getUserId(), 
					$this->getTimeFrame()
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::teamsTable . " SET "
					. "
						prize_winner_name = ?, 
						prize_winner_email = ?, 
						prize_description = ?, 
						prize_show_name = ?, 
						prize_sent = ?, 
						prize_team_id = ?, 
						prize_team_user_id = ?, 
						prize_time_frame = ?
					WHERE prize_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getWinnerName(), 
					$this->getWinnerEmail(), 
					$this->getDescription(), 
					$this->getIsShowName() ? 1 : 0, 
					$this->getIsSent() ? 1 : 0, 
					$this->getTeamId(), 
					$this->getUserId(), 
					$this->getTimeFrame(),
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
