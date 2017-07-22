<?php

class Models_UserHistory extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $userId;
	protected $username;
	protected $type;
    protected $description;
	protected $time;
	
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
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::userHistoryTable . " WHERE user_history_id = $id";

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
        if(isset($data['user_history_id'])) {
            $this->id = $data['user_history_id'];
        }
		
        $this->userId = $data['user_history_user_id'];
		$this->username = $data['user_history_username'];
		$this->type = $data['user_history_type'];
		$this->description = $data['user_history_description'];
		$this->time = $data['user_history_timestamp'];
	}
	
	public function getUser() {
		if($this->user == null && $this->db != null) {
			$this->user = Models_User::withID($this->db, $this->logger, $this->getUserId());
		}
		
		return $this->season;
	}
	
	function getUserId() {
		return $this->userId;
	}

	function getUsername() {
		return $this->username;
	}

	function getType() {
		return $this->type;
	}

	function getDescription() {
		return $this->description;
	}

	function getTime() {
		return $this->time;
	}

	function setUserId($userId) {
		$this->userId = $userId;
	}

	function setUsername($username) {
		$this->username = $username;
	}

	function setType($type) {
		$this->type = $type;
	}

	function setDescription($description) {
		$this->description = $description;
	}

	function setTime($time) {
		$this->time = $time;
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
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::userHistoryTable . " "
					. "(
						user_history_user_id, user_history_username, user_history_type, user_history_description
					) "
					. "VALUES "
					. "(?, ?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getUserId(), 
					$this->getUsername(), 
					$this->getType(), 
					$this->getDescription()
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::userHistoryTable . " SET "
					. "
						user_history_user_id = ?, 
						user_history_username = ?,
						user_history_type = ?,
						user_history_description = ?
					WHERE user_history_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getUserId(), 
					$this->getUsername(), 
					$this->getType(), 
					$this->getDescription(),
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
