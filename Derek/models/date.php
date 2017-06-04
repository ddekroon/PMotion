<?php

class Models_Date extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
    protected $sportId;
	protected $dayNumber;
	protected $description;
	protected $weekNumber;
	protected $dayOfYearNumber;
	protected $seasonId;
	
	private $sport;
	private $season;
    		
	public static function withID($db, $id) {
		$instance = new self();
        $instance->loadByID($db, $id);
        return $instance;
	}
	
	public function loadByID($db, $id) {
		$this->setDb($db);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable . " WHERE date_id = $id";

		$stmt = $db->query($sql);

		if(($row = $stmt->fetch()) != false) {
			$this->fill($row);
		}
	}

	public static function withRow($db, array $row) {
		$instance = new self();
		$instance->setDb($db);
        $instance->fill( $row );
        return $instance;
	}
	
	public function fill(array $data) {
		// no id if we're creating
        if(isset($data['date_id'])) {
            $this->id = $data['date_id'];
        }
		
		$this->sportId = $data['date_sport_id'];
		$this->dayNumber = $data['date_day_number'];
		$this->description = $data['date_description'];
		$this->weekNumber = $data['date_week_number'];
		$this->dayOfYearNumber = $data['date_day_of_year_num'];
		$this->seasonId = $data['date_season_id'];
	}
	
	function getSeasonId() {
		if($this->season == null && $this->db != null) {
			$this->season = Models_Season::withID($this->db, $this->getSeasonId());
		}
		
		return $this->seasonId;
	}

	function getSport() {
		if($this->sport == null && $this->db != null) {
			$this->sport = Models_Sport::withID($this->db, $this->getSportId());
		}
		
		return $this->sport;
	}
	
	function setSport($sport) {
		$this->sport = $sport;
	}

	function setSeason($season) {
		$this->season = $season;
	}
	
	function getId() {
		return $this->id;
	}

	function getSportId() {
		return $this->sportId;
	}

	function getDayNumber() {
		return $this->dayNumber;
	}

	function getDescription() {
		return $this->description;
	}

	function getWeekNumber() {
		return $this->weekNumber;
	}

	function getDayOfYearNumber() {
		return $this->dayOfYearNumber;
	}

	function getSeason() {
		return $this->season;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setSportId($sportId) {
		$this->sportId = $sportId;
	}

	function setDayNumber($dayNumber) {
		$this->dayNumber = $dayNumber;
	}

	function setDescription($description) {
		$this->description = $description;
	}

	function setWeekNumber($weekNumber) {
		$this->weekNumber = $weekNumber;
	}

	function setDayOfYearNumber($dayOfYearNumber) {
		$this->dayOfYearNumber = $dayOfYearNumber;
	}

	function setSeasonId($seasonId) {
		$this->seasonId = $seasonId;
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
