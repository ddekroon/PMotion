<?php

class Models_Team extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $leagueId;
    protected $name;
    protected $numInLeague;
    protected $managedByUserId;
    protected $wins;
    protected $losses;
    protected $ties;
    protected $mostRecentWeekSubmitted;
    protected $dateCreated;
    protected $isFinalized;
    protected $isPaid;
    protected $isDeleted;
    protected $paymentMethod;
    protected $finalPosition;
    protected $finalSpiritPosition;
    protected $picName;
    protected $isConvenor;
    protected $isDroppedOut;
	protected $isLateEmailAllowed;
	
	private $league;
	private $manager;
	private $captain;
	private $players;
	private $registrationComment;
	private $spiritAverage;
	private $scheduledMatches;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_id = $id";

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
		if(isset($data['team_id'])) {
            $this->id = $data['team_id'];
        }
		
		$this->leagueId = $data['team_league_id'];
		$this->name = $data['team_name'];
		$this->numInLeague = $data['team_num_in_league'];
		$this->managedByUserId = $data['team_managed_by_user_id'];
		$this->wins = $data['team_wins'];
		$this->losses = $data['team_losses'];
		$this->ties = $data['team_ties'];
		$this->mostRecentWeekSubmitted = $data['team_most_recent_week_submitted'];
		$this->dateCreated = $data['team_created'];
		$this->isFinalized = $data['team_finalized'];
		$this->isPaid = $data['team_paid'];
		$this->isDeleted = $data['team_deleted'];
		$this->paymentMethod = $data['team_payment_method'];
		$this->finalPosition = $data['team_final_position'];
		$this->finalSpiritPosition = $data['team_final_spirit_position'];
		$this->picName = $data['team_pic_name'];
		$this->isConvenor = $data['team_is_convenor'];
		$this->isDroppedOut = $data['team_dropped_out'];
		$this->isLateEmailAllowed = $data['team_late_email_allowed'];
	}
	
	public function getShortName() {
		return substr($this->name, 0, 20);
	}
	
	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
	}
	
	function getLeague() {
		if($this->league == null && $this->db != null) {
			$this->league = Models_League::withID($this->db, $this->logger, $this->leagueId);
		}
		
		return $this->league;
	}

	function getManager() {
		if($this->manager == null && $this->db != null) {
			$this->manager = Models_User::withID($this->db, $this->logger, $this->getManagedByUserId());
		}
		
		return $this->manager;
	}
	
	function getCaptain() {
		if($this->captain == null && $this->db != null && $this->getId() != null) {
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_team_id = " . $this->getId() . " AND player_is_captain = 1";
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->captain = Models_Player::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->captain;
	}

	function getPlayers() {
		if($this->players == null && $this->db != null && $this->getId() != null) {
			$this->players = [];
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_team_id = " . $this->getId() . " AND player_is_captain = 0";
			$stmt = $this->db->query($sql);

			while(($row = $stmt->fetch()) != false) {
				$this->players[] = Models_Player::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->players;
	}
	
	function getRegistrationComment() {
		
		if($this->registrationComment == null && $this->db != null && $this->getId() != null) {
			$sql = "SELECT * FROM " . Includes_DBTableNames::registrationCommentsTable . " WHERE registration_comment_team_id = " . $this->getId();
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->registrationComment = Models_RegistrationComment::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->registrationComment;
	}
	
	function getSpiritAverage() {
		
		if($this->spiritAverage == null && $this->db != null && $this->getId() != null) {
			$sql = "SELECT SUM(spiritScores.spirit_score_edited_value) / COUNT(spiritScores.spirit_score_edited_value) as team_spirit_average "
					. "FROM " . Includes_DBTableNames::teamsTable . " AS team "
					. "INNER JOIN " . Includes_DBTableNames::leaguesTable . " league ON league.league_id = team.team_league_id "
					. "INNER JOIN " . Includes_DBTableNames::scoreSubmissionsTable . " scoreSubmissions ON "
						. "(scoreSubmissions.score_submission_opp_team_id = team.team_id AND (scoreSubmissions.score_submission_ignored = 0 OR scoreSubmissions.score_submission_is_phantom = 1)) "
					. "INNER JOIN " . Includes_DBTableNames::spiritScoresTable . " spiritScores ON ("
						. "spiritScores.spirit_score_score_submission_id = scoreSubmissions.score_submission_id "
						. "AND (spiritScores.spirit_score_edited_value > 3.5 OR spiritScores.spirit_score_dont_show = 1 OR spiritScores.spirit_score_is_admin_addition = 1) "
						. "AND spiritScores.spirit_score_edited_value > 0 AND spiritScores.spirit_score_ignored = 0"
					. ") "
					. "INNER JOIN " . Includes_DBTableNames::datesTable . " dates ON (dates.date_id = scoreSubmissions.score_submission_date_id AND dates.date_week_number < league.league_playoff_week) "
					. "WHERE team.team_id = " . $this->getId();
			
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->spiritAverage = $row['team_spirit_average'];
			}
		}
		
		return $this->spiritAverage;
	}
	
	function getPoints() {
		return ($this->getWins() * 2) + $this->getTies();	  
  	}
	
	function getWinPercent() {
		$pointsAvailable = ($this->getWins() + $this->getTies() + $this->getLosses()) * 2;
		if ($pointsAvailable != 0) {
			return round(($this->getPoints() / $pointsAvailable), 3);
		} else {
			return 0;
		}
	}
  
	function getTitleSize() {
		if(strlen($this->getTeamName()) >= 20) {
			return '9px';
		} else {
			return '12px'; 
		}
	}
	
	function getFormattedStandings() {
		
		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		
		if($this->getLeague()->getSport()->getId() != 2) {
			$standings = '(' . $this->getWins() . '-' . $this->getLosses() . '-' . $this->getTies() . ')';
		} else {
			$standings = '(' . $this->getWins() . '-' . $this->getLosses() . ')';
		}
		
		$standings .= $leaguesController->checkHideSpirit($this->getLeague()) == false ? ' (' . number_format($this->getSpiritAverage(), 2, '.', '') . ')' : '';
		
		return $standings;
	}
	
	public function getFinalPositionWithSuffix() {
		if (!in_array(($this->getFinalPosition() % 100), array(11,12,13))) {
			switch ($this->getFinalPosition() % 10) {
				case 1:
					return $this->getFinalPosition().'st';
				case 2:
					return $this->getFinalPosition().'nd';
				case 3:
					return $this->getFinalPosition().'rd';
			}
		}
		return $this->getFinalPosition().'th';
	}
	
	public function getFinalSpiritPositionWithSuffix() {
		if (!in_array(($this->getFinalSpiritPosition() % 100), array(11,12,13))) {
			switch ($this->getFinalSpiritPosition() % 10) {
				case 1:
					return $this->getFinalSpiritPosition().'st';
				case 2:
					return $this->getFinalSpiritPosition().'nd';
				case 3:
					return $this->getFinalSpiritPosition().'rd';
			}
		}
		return $this->getFinalSpiritPosition().'th';
	}
	
	public function getScheduledMatches() {
		
		if($this->scheduledMatches == null && $this->db != null && $this->getId() != null) {
			
			$this->scheduledMatches = [];
			
			$sql = "SELECT matches.* FROM " . Includes_DBTableNames::scheduledMatchesTable . " as matches "
					. "INNER JOIN " . Includes_DBTableNames::datesTable . " dates ON dates.date_id = matches.scheduled_match_date_id "
					. "WHERE (matches.scheduled_match_team_id_2 = " . $this->getId() . " OR matches.scheduled_match_team_id_1 = " . $this->getId() . ") "
					. "ORDER BY dates.date_week_number ASC, matches.scheduled_match_time ASC";

			$stmt = $this->db->query($sql);

			while(($row = $stmt->fetch()) != false) {
				$this->scheduledMatches[] = Models_ScheduledMatch::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->scheduledMatches;
	}
	
	function getIsPic() {
		$ds = DIRECTORY_SEPARATOR;
		
		if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']) . $ds . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.JPG')) {
			return true;
		} else if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']) . $ds . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.jpg')) {
			return true;
		}
		return false;
	}
	
	function getPic() {
		$ds = DIRECTORY_SEPARATOR;
		
		if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']) . $ds . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.JPG')) {
			return $ds . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.JPG';
		} else if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']) . $ds . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.jpg')) {
			return '/' . $this->getLeague()->getPicLink() . $ds . $this->getPicName() . '.jpg';
		} 
		
		return "";
	}
	
	function getId() {
		return $this->id;
	}

	function getLeagueId() {
		return $this->leagueId;
	}

	function getName() {
		return $this->name;
	}

	function getNumInLeague() {
		return $this->numInLeague;
	}

	function getManagedByUserId() {
		return $this->managedByUserId;
	}

	function getWins() {
		return $this->wins;
	}

	function getLosses() {
		return $this->losses;
	}

	function getTies() {
		return $this->ties;
	}

	function getMostRecentWeekSubmitted() {
		return $this->mostRecentWeekSubmitted;
	}

	function getDateCreated() {
		return $this->dateCreated;
	}

	function getIsFinalized() {
		return $this->isFinalized;
	}

	function getIsPaid() {
		return $this->isPaid;
	}

	function getIsDeleted() {
		return $this->isDeleted;
	}

	function getPaymentMethod() {
		return $this->paymentMethod;
	}

	function getFinalPosition() {
		return $this->finalPosition;
	}

	function getFinalSpiritPosition() {
		return $this->finalSpiritPosition;
	}

	function getPicName() {
		return $this->picName;
	}

	function getIsConvenor() {
		return $this->isConvenor;
	}

	function getIsDroppedOut() {
		return $this->isDroppedOut;
	}

	function getIsLateEmailAllowed() {
		return $this->isLateEmailAllowed;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setLeagueId($leagueId) {
		$this->leagueId = $leagueId;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setNumInLeague($numInLeague) {
		$this->numInLeague = $numInLeague;
	}

	function setManagedByUserId($managedByUserId) {
		$this->managedByUserId = $managedByUserId;
	}

	function setWins($wins) {
		$this->wins = $wins;
	}

	function setLosses($losses) {
		$this->losses = $losses;
	}

	function setTies($ties) {
		$this->ties = $ties;
	}

	function setMostRecentWeekSubmitted($mostRecentWeekSubmitted) {
		$this->mostRecentWeekSubmitted = $mostRecentWeekSubmitted;
	}

	function setDateCreated($dateCreated) {
		$this->dateCreated = $dateCreated;
	}

	function setIsFinalized($isFinalized) {
		$this->isFinalized = $isFinalized;
	}

	function setIsPaid($isPaid) {
		$this->isPaid = $isPaid;
	}

	function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
	}

	function setPaymentMethod($paymentMethod) {
		$this->paymentMethod = $paymentMethod;
	}

	function setFinalPosition($finalPosition) {
		$this->finalPosition = $finalPosition;
	}

	function setFinalSpiritPosition($finalSpiritPosition) {
		$this->finalSpiritPosition = $finalSpiritPosition;
	}

	function setPicName($picName) {
		$this->picName = $picName;
	}

	function setIsConvenor($isConvenor) {
		$this->isConvenor = $isConvenor;
	}

	function setIsDroppedOut($isDroppedOut) {
		$this->isDroppedOut = $isDroppedOut;
	}

	function setIsLateEmailAllowed($isLateEmailAllowed) {
		$this->isLateEmailAllowed = $isLateEmailAllowed;
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
