<?php

class Models_EmailAddress extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $firstName;
    protected $lastName;
	protected $email;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::addressesTable . " WHERE id = $id";

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
		if(isset($data['id'])) {
            $this->id = $data['id'];
        }
		
        $this->firstName = $data['FirstName'];
        $this->lastName = $data['LastName'];
		$this->email = $data['EmailAddress'];
	}

    function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
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

	function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	function setEmail($email) {
		$this->email = $email;
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
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::addressesTable . " "
					. "(
						FirstName, LastName, EmailAddress
					) "
					. "VALUES "
					. "(?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getFirstName(),
					$this->getLastName(),
					$this->getEmail()
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::addressesTable . " SET "
					. "
						FirstName = ?, 
						LastName = ?, 
						EmailAddress = ?
					WHERE id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getFirstName(),
					$this->getLastName(),
					$this->getEmail(),
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
