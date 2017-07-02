<?php

class Models_Venue extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $name;
	protected $shortShowName;
	protected $shortMatchName;
	protected $sportId;
	protected $address;
	protected $link;
    		
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::venuesTable . " WHERE venue_id = $id";

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
        if(isset($data['venue_id'])) {
            $this->id = $data['venue_id'];
        }
		
		$this->name = $data['venue_name'];
		$this->shortShowName = $data['venue_short_show_name'];
		$this->shortMatchName = $data['venue_short_match_name'];
		$this->sportId = $data['venue_sport_id'];
		$this->address = $data['venue_address'];
		$this->link = strtotime($data['venue_link']);
	}
	
	function getId() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function getShortShowName() {
		return $this->shortShowName;
	}

	function getShortMatchName() {
		return $this->shortMatchName;
	}

	function getSportId() {
		return $this->sportId;
	}

	function getAddress() {
		return $this->address;
	}

	function getLink() {
		return $this->link;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setShortShowName($shortShowName) {
		$this->shortShowName = $shortShowName;
	}

	function setShortMatchName($shortMatchName) {
		$this->shortMatchName = $shortMatchName;
	}

	function setSportId($sportId) {
		$this->sportId = $sportId;
	}

	function setAddress($address) {
		$this->address = $address;
	}

	function setLink($link) {
		$this->link = $link;
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
