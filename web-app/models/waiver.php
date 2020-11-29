<?php

class Models_Waiver extends Models_Generic implements Models_Interface, JsonSerializable {
	
    protected $name;
    protected $email;
    protected $guardName = '';
    protected $guardEmail = '';
	protected $date;
	protected $sportId;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
				
		$sql = "SELECT * FROM " . Includes_DBTableNames::waiversTable . " WHERE waiver_id = $id";

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
		if(isset($data['waiver_id'])) {
            $this->id = $data['waiver_id'];
        }
		
		$this->name = $data['waiver_name'];
		$this->email = $data['waiver_email'];
		$this->guardName = $data['waiver_guard_name'];
		$this->guardEmail = $data['waiver_guard_email'];
		$this->date = new DateTime($data['waiver_date']);
		$this->sportId = $data['waiver_sport_id'];
	}
	
	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
	}
	
	function getId() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function getEmail() {
		return $this->email;
	}

	function getGuardName() {
		return $this->guardName;
	}

	function getGuardEmail() {
		return $this->guardEmail;
	}

	function getDate() {
		return $this->date;
	}

	function getSportId() {
		return $this->sportId;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setEmail($email) {
		$this->email = $email;
	}

	function setGuardName($guardName) {
		$this->guardName = $guardName;
	}

	function setGuardEmail($guardEmail) {
		$this->guardEmail = $guardEmail;
	}

	function setDate($date) {
		$this->date = $date;
	}

	function setSportId($sportId) {
		$this->sportId = $sportId;
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
			$this->setDate(new DateTime());

			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::waiversTable . " "
			. "(
				waiver_name, waiver_email, waiver_guard_name, waiver_guard_email, waiver_date, waiver_sport_id
			) "
			. "VALUES "
			. "(?, ?, ?, ?, ?, ?)");
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getName(), 
					$this->getEmail(), 
					$this->getGuardName(), 
					$this->getGuardEmail(), 
					$this->getDate() != null ? $this->getDate()->format('Y-m-d H:i:s') : null, 
					$this->getSportId()
				)
			); 
			
			$this->setId($this->db->lastInsertId());
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage());
			throw $ex;
		}
	}
	
	public function update() {
		try {			
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::waiversTable . " SET "
					. "
						waiver_name = ?, 
						waiver_email = ?, 
						waiver_guard_name = ?, 
						waiver_guard_email = ?, 
						waiver_date = ?, 
						waiver_sport_id = ?
					WHERE waiver_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getName(), 
					$this->getEmail(), 
					$this->getGuardName(), 
					$this->getGuardEmail(), 
					$this->getDate() != null ? $this->getDate()->format('Y-m-d H:i:s') : null, 
					$this->getSportId(), 
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
