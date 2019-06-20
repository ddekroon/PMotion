<?php

class Models_PrizeTimeframe extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $id;
    protected $description;
	protected $isVisible;
    		
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::prizesTimeframesTable . " WHERE id = $id";

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
        if(isset($data['id'])) {
            $this->id = $data['id'];
		}

		$this->$description = $data['description'];
		$this->$isVisible = $data['visible'] > 0;
	}
	
	function setDescription($description) {
		$this->description = $description;
	}

	function setIsVisible($isVisible) {
		$this->isVisible = $isVisible;
	}

	function getDescription() {
		return $this->description;
	}

	function getIsVisible() {
		return $this->isVisible;
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
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::prizesTimeframesTable . " "
					. "(
						`description`, `visible`
					) "
					. "VALUES "
					. "(?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getDescription(),
					$this->getIsVisible() ? 1 : 0
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::prizesTimeframesTable . " SET "
					. "
						description = ?, 
						visible = ?
					WHERE id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getDescription(), 
					$this->getIsVisible() ? 1 : 0,
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
