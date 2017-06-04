<?php

class Models_Generic implements JsonSerializable {
    protected $id;
	protected $db;
	
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
	
	function getDb() {
		return $this->db;
	}

	function setDb($db) {
		$this->db = $db;
	}
	
	public function toString() {
		return "Generic Model [$this->id]";
	}
	
	public function jsonSerialize() {
		return "{ id: $this->id }";
	}
	
	function saveOrUpdate() {
		if($this->getId() == null) {
			save();
		} else {
			update();
		}
	}
	
	function save() {
		
	}
	
	function update() {
		
	}

}
