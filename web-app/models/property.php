<?php

class Models_Property extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
	
	protected $key = null;
    protected $value = null;
	
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::propertiesTable . " WHERE id = $id";

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
		if(empty($data)) {
			return;
		}
		
		if(isset($data['id'])) {
            $this->id = $data['id'];
        }
		
		$this->key = $data['key'];
		$this->value = $data['value'];
	}

    function getId() {
		return $this->id;
	}

	function getKey() {
		return $this->key;
	}

	function getValue() {
		return $this->value;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setKey($key) {
		$this->key = $key;
	}

	function setValue($value) {
		$this->value = $value;
	}

	public function save() {
		
		if($this->getKey() == null || empty($this->getKey()) 
				|| $this->getValue() == null || empty($this->getValue()) 
		) {
			$this->logger->log("Trying to save property with content missing.\n"
					. "Key: " . $this->getKey() . "\n"
					. "Value: " . $this->getValue() . "\n"); 
			return;
		}
		
		try {
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::propertiesTable . " "
					. "(
						key, value
					) "
					. "VALUES "
					. "(?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getKey(), 
					$this->getValue()
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::propertiesTable . " SET "
					. "
						value= ?
					WHERE key = ?"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getValue(),
					$this->getKey()
				)
			); 
			$this->db->commit(); 
			
		} catch (Exception $ex) {
			$this->db->rollback();
			$this->logger->log($ex->getMessage()); 
		}
	}
}
