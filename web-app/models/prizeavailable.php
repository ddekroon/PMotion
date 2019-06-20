<?php

class Models_PrizeAvailable extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $id;
    protected $name;
	protected $price;
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
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::prizesAvailableTable . " WHERE id = $id";

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

		$this->$name = $data['name'];
		$this->$price = $data['price'];
		$this->$isVisible = $data['visible'] > 0;
	}
	
	function setName($name) {
		$this->name = $name;
	}

	function setPrice($price) {
		$this->price = $price;
	}

	function setIsVisible($isVisible) {
		$this->isVisible = $isVisible;
	}

	function getName() {
		return $this->name;
	}

	function getPrice() {
		return $this->price;
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
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::prizesAvailableTable . " "
					. "(
						`name`, `price`, `visible`
					) "
					. "VALUES "
					. "(?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getName(), 
					$this->getPrice(), 
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::prizesAvailableTable . " SET "
					. "
						name = ?, 
						price = ?, 
						visible = ?
					WHERE id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getName(), 
					$this->getPrice(), 
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
