<?php

class Models_Match extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
	protected $leagueId;
	protected $teamId;
	protected $oppTeamId;
	protected $fieldId;
	protected $time;
    protected $dateId;
	protected $playoffTeam;
	protected $playoffOppTeam;
	protected $venueNumInWeek;
	
	private $league;
	private $team;
	private $oppTeam;
	private $field;
	private $date;
	

    public static function withID($db, $id) {
		$instance = new self();
        $instance->loadByID($db, $id);
        return $instance;
	}
	
	public function loadByID($db, $id) {
		$this->setDb($db);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::scheduledMatchesTable . " WHERE scheduled_match_id = $id";

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
		if(isset($data['scheduled_match_id'])) {
            $this->id = $data['scheduled_match_id'];
        }
		
		$this->leagueId = $data['scheduled_match_league_id'];
		$this->teamId = $data['scheduled_match_team_id_1'];
		$this->oppTeamId = $data['scheduled_match_team_id_2'];
		$this->fieldId = $data['scheduled_match_field_id'];
		$this->time = $data['scheduled_match_time'];
		$this->dateId = $data['scheduled_match_date_id'];
		$this->playoffTeam = $data['scheduled_match_playoff_team_1'];
		$this->playoffOppTeam = $data['scheduled_match_playoff_team_2'];
		$this->venueNumInWeek = $data['scheduled_match_venue_num_in_week'];
	}
	
	function getLeague() {
		if($this->league == null && $this->db != null) {
			$this->league = Models_League::withID($db, $this->getLeagueId());
		}
		
		return $this->league;
	}

	function getTeam() {
		if($this->team == null && $this->db != null) {
			$this->team = Models_League::withID($db, $this->getTeamId());
		}
		
		return $this->team;
	}

	function getOppTeam() {
		if($this->oppTeam == null && $this->db != null) {
			$this->oppTeam = Models_Team::withID($db, $this->getOppTeamId());
		}
		
		return $this->oppTeam;
	}

	function getField() {
		return $this->field;
	}

	function getDate() {
		return $this->date;
	}
	

    function getId() {
		return $this->id;
	}

	function getLeagueId() {
		return $this->leagueId;
	}

	function getTeamId() {
		return $this->teamId;
	}

	function getOppTeamId() {
		return $this->oppTeamId;
	}

	function getFieldId() {
		return $this->fieldId;
	}

	function getTime() {
		return $this->time;
	}

	function getDateId() {
		return $this->dateId;
	}

	function getPlayoffTeam() {
		return $this->playoffTeam;
	}

	function getPlayoffOppTeam() {
		return $this->playoffOppTeam;
	}

	function getVenueNumInWeek() {
		return $this->venueNumInWeek;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setLeagueId($leagueId) {
		$this->leagueId = $leagueId;
	}

	function setTeamId($teamId) {
		$this->teamId = $teamId;
	}

	function setOppTeamId($oppTeamId) {
		$this->oppTeamId = $oppTeamId;
	}

	function setFieldId($fieldId) {
		$this->fieldId = $fieldId;
	}

	function setTime($time) {
		$this->time = $time;
	}

	function setDateId($dateId) {
		$this->dateId = $dateId;
	}

	function setPlayoffTeam($playoffTeam) {
		$this->playoffTeam = $playoffTeam;
	}

	function setPlayoffOppTeam($playoffOppTeam) {
		$this->playoffOppTeam = $playoffOppTeam;
	}

	function setVenueNumInWeek($venueNumInWeek) {
		$this->venueNumInWeek = $venueNumInWeek;
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
