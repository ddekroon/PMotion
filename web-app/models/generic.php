<?php

class Models_Generic implements JsonSerializable {
    protected $id;
	protected $db;
	protected $logger;
	
	/**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct() {
		
	}

    public function getId() {
        return $this->id;
    }
	
	public function setId($id) {
        return $this->id = $id;
    }
	
	function getDb() {
		return $this->db;
	}

	function setDb($db) {
		$this->db = $db;
	}
	
	function getLogger() {
		return $this->logger;
	}

	function setLogger($logger) {
		$this->logger = $logger;
	}
	
	public function __toString() {
		return get_class($this) . " [ " . $this->getId() . " ] ";
	}
	
	public function varDump() {
		var_dump(get_object_vars($this));
	}

	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
	
	function saveOrUpdate() {
		if($this->getId() == null) {
			$this->save();
		} else {
			$this->update();
		}
	}
	
	function save() {
		
	}
	
	function update() {
		
	}
}
