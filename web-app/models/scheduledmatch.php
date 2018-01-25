<?php

class Models_ScheduledMatch extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $id;
	protected $leagueId;
    protected $teamOneId;
	protected $teamTwoId;
	protected $fieldId;
	protected $matchTime;
	protected $dateId;
	protected $playoffTeamOneString;
	protected $playoffTeamTwoString;
	protected $venueNumInWeek;
	
	private $league;
	private $teamOne;
	private $teamTwo;
	private $venue;
	private $date;
	private $scoreSubmissions;
	private $curTeam;
	private $oppTeam;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::scheduledMatchesTable . " WHERE scheduled_match_id = $id";

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
        if(isset($data['scheduled_match_id'])) {
            $this->id = $data['scheduled_match_id'];
        }
		
        $this->leagueId = $data['scheduled_match_league_id'];
		$this->teamOneId = $data['scheduled_match_team_id_1'];
		$this->teamTwoId = $data['scheduled_match_team_id_2'];
		$this->fieldId = $data['scheduled_match_field_id'];
		$this->matchTime = $data['scheduled_match_time'];
		$this->dateId = $data['scheduled_match_date_id'];
		$this->playoffTeamOneString = $data['scheduled_match_playoff_team_1'];
		$this->playoffTeamTwoString = $data['scheduled_match_playoff_team_2'];
		$this->venueNumInWeek = $data['scheduled_match_venue_num_in_week'];
    }

	function getLeague() {
		if($this->league == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::leaguesTable . " WHERE league_id = " . $this->getLeagueId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->league = Models_League::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->league;
	}
	
	function getTeamOne() {
		if($this->teamOne == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_id = " . $this->getTeamOneId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->teamOne = Models_League::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->teamOne;
	}
	
	function getTeamTwo() {
		if($this->teamTwo == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_id = " . $this->getTeamTwoId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->teamTwo = Models_League::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->teamTwo;
	}
	
	public function getOppTeam(Models_Team $team) {
		if(($this->oppTeam == null || $this->curTeam == null || $this->curTeam->getId() != $team->getId()) && $this->db != null) {
			if($this->getTeamOneId() == $team->getId()) {
				$this->oppTeam = Models_Team::withID($this->db, $this->logger, $this->getTeamTwoId());
			} else if($this->getTeamTwoId() == $team->getId()) {
				$this->oppTeam = Models_Team::withID($this->db, $this->logger, $this->getTeamOneId());
			}
			$this->curTeam = $team;
		}
		
		return $this->oppTeam;
	}
	
	public function getOppTeamId(Models_Team $team) {
		$oppTeam = $this->getOppTeam($team);
		
		if($oppTeam != null) {
			return $oppTeam->getId();
		}
		
		return -1;
	}
	
	function getVenue() {
		if($this->venue == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::venuesTable . " WHERE venue_id = " . $this->getFieldId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->venue = Models_Venue::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->venue;
	}
	
	public function getDate() {
		if($this->date == null && $this->db != null) {
		
			$sql = "SELECT * FROM " . Includes_DBTableNames::datesTable . " WHERE date_id = " . $this->getDateId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->date = Models_Date::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->date;
	}
	
	public function getScoreSubmissions($team) {
		
		echo '-' . $team->getId() . '-' . $this->getTeamOneId(). '-' . $this->getTeamTwoId() . '-';
		
		
		if($this->scoreSubmissions == null && $this->db != null && ($this->getTeamOneId() == $team->getId() || $this->getTeamTwoId() == $team->getId()) ) {
			$this->scoreSubmissions = [];
		
			$sql = "SELECT scoreSubmissions.* FROM " . Includes_DBTableNames::scoreSubmissionsTable . " scoreSubmissions "
					. "INNER JOIN " . Includes_DBTableNames::datesTable . " dates "
						. "ON scoreSubmissions.score_submission_date_id = dates.date_id AND dates.date_id = " . $this->getDateId() . " "
					. "WHERE scoreSubmissions.score_submission_team_id = " . $team->getId() . " AND scoreSubmissions.score_submission_ignored = 0 "
					. "ORDER BY dates.date_week_number ASC, scoreSubmissions.score_submission_is_phantom ASC, scoreSubmissions.score_submission_id ASC";
			
			echo $sql;
			
			$stmt = $this->db->query($sql);

			while(($row = $stmt->fetch()) != false) {
				$this->scoreSubmissions[] = Models_ScoreSubmission::withRow($this->db, $this->logger, $row);
			}
		}
		
		print_r($this->scoreSubmissions);
		
		return $this->scoreSubmissions;
	}
	
	public function getFormattedDatePlayed() {
		if($this->getDate() != null || $this->getDate()->getDescription() != '') {
			return date('D, M d', strtotime($this->getDate()->getDescription()));
		} else {
			return 'Date Error';
		}
	}
	
	public function getFormattedGameTime() {
		return date('g:i A', strtotime($this->getMatchTime()));
	}
	
    public function getId() {
        return $this->id;
    }
	
	function getLeagueId() {
		return $this->leagueId;
	}

	function getTeamOneId() {
		return $this->teamOneId;
	}

	function getTeamTwoId() {
		return $this->teamTwoId;
	}

	function getFieldId() {
		return $this->fieldId;
	}

	function getMatchTime() {
		return $this->matchTime;
	}
	
	public function getMatchTimeFormatted() {
		if(strlen($this->getMatchTime() == 3)){
			$hr = substr($this->getMatchTime(), 0, 1);
			$mn = substr($this->getMatchTime(), 1);
		} elseif($this->getMatchTime() != '') {
			$hr= substr($this->getMatchTime(), 0, 2);
			$mn= substr($this->getMatchTime(), 2);
		} else {
			return '';
		}
		
		if ($hr >= 13){
			$hr = $hr - 12;
			$meridian = "pm";
		} else {
			$meridian = "am";
		}
		
		return "$hr:$mn $meridian";
	}

	function getDateId() {
		return $this->dateId;
	}

	function getPlayoffTeamOneString() {
		return $this->playoffTeamOneString;
	}

	function getPlayoffTeamTwoString() {
		return $this->playoffTeamTwoString;
	}

	function getVenueNumInWeek() {
		return $this->venueNumInWeek;
	}

	function setLeagueId($leagueId) {
		$this->leagueId = $leagueId;
	}

	function setTeamOneId($teamOneId) {
		$this->teamOneId = $teamOneId;
	}

	function setTeamTwoId($teamTwoId) {
		$this->teamTwoId = $teamTwoId;
	}

	function setFieldId($fieldId) {
		$this->fieldId = $fieldId;
	}

	function setMatchTime($matchTime) {
		$this->matchTime = $matchTime;
	}

	function setDateId($dateId) {
		$this->dateId = $dateId;
	}

	function setPlayoffTeamOneString($playoffTeamOneString) {
		$this->playoffTeamOneString = $playoffTeamOneString;
	}

	function setPlayoffTeamTwoString($playoffTeamTwoString) {
		$this->playoffTeamTwoString = $playoffTeamTwoString;
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
