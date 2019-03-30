<?php

class Models_Team extends Models_Generic implements Models_Interface, JsonSerializable {
	
	protected $leagueId;
    protected $name;
    protected $numInLeague = 0;
    protected $managedByUserId;
    protected $wins = 0;
    protected $losses = 0;
    protected $ties = 0;
    protected $mostRecentWeekSubmitted = 0;
    protected $dateCreated;
    protected $isFinalized = false;
    protected $isPaid = false;
    protected $isDeleted = false;
    protected $paymentMethod = 0;
    protected $finalPosition = 0;
    protected $finalSpiritPosition = 0;
    protected $picName;
    protected $isConvenor = false;
    protected $isDroppedOut = false;
	protected $isLateEmailAllowed = true;
	
	protected $submittedWins;
	protected $submittedLosses;
	protected $submittedTies;
	protected $submittedPractices;
	protected $submittedCancels;
	
	protected $oppSubmittedWins;
	protected $oppSubmittedLosses;
	protected $oppSubmittedTies;
	
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
		$this->dateCreated = new DateTime($data['team_created']);
		$this->isFinalized = $data['team_finalized'] > 0;
		$this->isPaid = $data['team_paid'];
		$this->isDeleted = $data['team_deleted'];
		$this->paymentMethod = $data['team_payment_method'];
		$this->finalPosition = $data['team_final_position'];
		$this->finalSpiritPosition = $data['team_final_spirit_position'];
		$this->picName = $data['team_pic_name'];
		$this->isConvenor = $data['team_is_convenor'] > 0;
		$this->isDroppedOut = $data['team_dropped_out'] > 0;
		$this->isLateEmailAllowed = $data['team_late_email_allowed'];
		
		$this->submittedWins = array_key_exists('team_submitted_wins', $data) ? $data['team_submitted_wins'] : 0;
		$this->submittedTies = array_key_exists('team_submitted_ties', $data) ? $data['team_submitted_ties'] : 0;
		$this->submittedLosses = array_key_exists('team_submitted_losses', $data) ? $data['team_submitted_losses'] : 0;
		$this->submittedPractices = array_key_exists('team_submitted_practices', $data) ? $data['team_submitted_practices'] : 0;
		$this->submittedCancels = array_key_exists('team_submitted_cancels', $data) ? $data['team_submitted_cancels'] : 0;
		
		$this->oppSubmittedWins = array_key_exists('team_opp_submitted_wins', $data) ? $data['team_opp_submitted_wins'] : 0;
		$this->oppSubmittedTies = array_key_exists('team_opp_submitted_ties', $data) ? $data['team_opp_submitted_ties'] : 0;
		$this->oppSubmittedLosses = array_key_exists('team_opp_submitted_losses', $data) ? $data['team_opp_submitted_losses'] : 0;
	}
	
	function getShortName() {
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
		
		if(($this->captain == null || $this->captain->getId() == null) && $this->db != null && $this->getId() != null) {
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_team_id = " . $this->getId() . " AND player_is_captain = 1";
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->captain = Models_Player::withRow($this->db, $this->logger, $row);
			}
		}
		
		if($this->captain == null) {
			$this->captain = Models_Player::withID($this->db, $this->logger, -1);
			$this->captain->setIsCaptain(true);
		}
		
		return $this->captain;
	}

	function getCaptainContactInfo() {
		$captain = $this->getCaptain();

		if($captain->getId() > 0) {
			return $captain->getFirstName() . ' ' . $captain->getLastName()
			. ' (<a href="mailto:' . $captain->getEmail() . '">' . $captain->getEmail() . '</a>)';
		}
		
		return '';
	}

	function getPlayers() {
		
		if(($this->players == null || empty($this->players)) && $this->db != null && $this->getId() != null) {
			$this->players = [];
			
			$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_team_id = " . $this->getId() . " AND player_is_captain = 0";
			$stmt = $this->db->query($sql);

			while(($row = $stmt->fetch()) != false) {
				$this->players[] = Models_Player::withRow($this->db, $this->logger, $row);
			}
		}
		
		if($this->players == null) {
			$this->players = [];
		}
		
		return $this->players;
	}

	function getAllPlayers() {
		
		$players = [];
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::playersTable . " WHERE player_team_id = " . $this->getId() . " ORDER BY player_id ASC";
		$stmt = $this->db->query($sql);

		while(($row = $stmt->fetch()) != false) {
			$players[] = Models_Player::withRow($this->db, $this->logger, $row);
		}
		
		return $players;
	}

	function getIndividuals() {
		
		$players = [];
		
		$sql = "SELECT players.* FROM " . Includes_DBTableNames::playersTable . " players"
			. " INNER JOIN " . Includes_DBTableNames::individualsTable . " ind ON ind.individual_player_id = players.player_id"
			. " WHERE player_team_id = " . $this->getId() . " ORDER BY ind.individual_small_group_id DESC, players.player_id ASC";
		$stmt = $this->db->query($sql);

		while(($row = $stmt->fetch()) != false) {
			$players[] = Models_Player::withRow($this->db, $this->logger, $row);
		}
		
		return $players;
	}
	
	function setPlayers(array $players) {
		$this->players = $players;
	}
	
	function getPlayerByID($playerID) {
		
		foreach($this->getPlayers() as $curPlayer) {
			if($curPlayer->getId() == $playerID) {
				return $curPlayer;
			}
		}
		
		return null;
	}
	
	function getTeamHasIndividuals() {
		return sizeof($this->getIndividuals()) > 0;
	}

	function getRegistrationComment() {
		
		if(($this->registrationComment == null || $this->registrationComment->getId() == null) && $this->db != null && $this->getId() != null) {
			$sql = "SELECT * FROM " . Includes_DBTableNames::registrationCommentsTable . " WHERE registration_comment_team_id = " . $this->getId();
			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->registrationComment = Models_RegistrationComment::withRow($this->db, $this->logger, $row);
			}
		}
		
		if($this->registrationComment == null) {
			$this->registrationComment = Models_RegistrationComment::withID($this->db, $this->logger, -1);
			$this->registrationComment->setIsIndividualComment(false);
			$this->registrationComment->setIsTeamComment(true);
			$this->registrationComment->setTeamId($this->getId());
			$this->registrationComment->setTeam($this);
			$this->registrationComment->setUserId($this->getManagedByUserId());
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
	
	function getHeadToHeadDifferential($teamTwo) {
		$sql = "SELECT SUM(score_submission_score_us) - SUM(score_submission_score_them) as score_differential "
				. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
				. "WHERE score_submission_ignored = 0 AND score_submission_team_id = " . $this->getId() . " AND score_submission_opp_team_id = " . $teamTwo->getId() . " "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		
		return $row["score_differential"];
	}
	
	function getCommonPlusMinusDifferential($teamTwo) {
		$diffOne = 0;
		$diffTwo = 0;
		
		$sql = "SELECT score_submission_team_id, SUM(score_submission_score_us - score_submission_score_them) as common_score_differential "
				. "FROM ("
						. "SELECT score_submission_team_id, score_submission_score_us, score_submission_score_them "
						. "FROM " . Includes_DBTableNames::scoreSubmissionsTable . " "
						. "WHERE score_submission_ignored = 0 AND (score_submission_team_id = " . $this->getId() . " OR score_submission_team_id = " . $teamTwo->getId() . ")"
				. ") as sub_table "
				. "GROUP BY score_submission_team_id";
		
		$stmt = $this->db->query($sql);
		
		while($row = $stmt->fetch()) {
			if($row['score_submission_team_id'] == $this->getId()) {
				$diffOne = $submission['common_score_differential'];
			} else {
				$diffTwo = $submission['common_score_differential'];
			}
		}
		
		return $diffOne - $diffTwo;
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
	function getSubmittedWins() {
		return $this->submittedWins;
	}

	function getSubmittedLosses() {
		return $this->submittedLosses;
	}

	function getSubmittedTies() {
		return $this->submittedTies;
	}
	
	function getSubmittedPractices() {
		return $this->submittedPractices;
	}
	
	function getSubmittedCancels() {
		return $this->submittedCancels;
	}

	function setSubmittedWins($submittedWins) {
		$this->submittedWins = $submittedWins;
	}

	function setSubmittedLosses($submittedLosses) {
		$this->submittedLosses = $submittedLosses;
	}

	function setSubmittedTies($submittedTies) {
		$this->submittedTies = $submittedTies;
	}
	
	function setSubmittedPractices($submittedPractices) {
		$this->submittedPractices = $submittedPractices;
	}
	
	function setSubmittedCancels($submittedCancels) {
		$this->submittedCancels = $submittedCancels;
	}

	function getOppSubmittedWins() {
		return $this->oppSubmittedWins;
	}

	function getOppSubmittedLosses() {
		return $this->oppSubmittedLosses;
	}

	function getOppSubmittedTies() {
		return $this->oppSubmittedTies;
	}

	function setOppSubmittedWins($oppSubmittedWins) {
		$this->oppSubmittedWins = $oppSubmittedWins;
	}

	function setOppSubmittedLosses($oppSubmittedLosses) {
		$this->oppSubmittedLosses = $oppSubmittedLosses;
	}

	function setOppSubmittedTies($oppSubmittedTies) {
		$this->oppSubmittedTies = $oppSubmittedTies;
	}
	
	public function saveOrUpdate() {
		if($this->getId() == null) {
			$this->save();
		} else {
			$this->update();
		}
	}
	
	public function save() {
		try {
			
			$this->setDateCreated(new DateTime());
			
			$stmt = $this->db->prepare("INSERT INTO " . Includes_DBTableNames::teamsTable . " "
					. "(
						team_league_id, team_name, team_num_in_league, team_managed_by_user_id, team_wins, team_losses, team_ties, 
						team_most_recent_week_submitted, team_created, team_finalized, team_paid, team_deleted, team_payment_method, 
						team_final_position, team_final_spirit_position, team_pic_name, 
						team_is_convenor, team_dropped_out, team_late_email_allowed
					) "
					. "VALUES "
					. "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getLeagueId(), 
					$this->getName(), 
					$this->getNumInLeague(), 
					$this->getManagedByUserId(), 
					$this->getWins(), 
					$this->getLosses(), 
					$this->getTies(), 
					$this->getMostRecentWeekSubmitted(),
					$this->getDateCreated() != null ? $this->getDateCreated()->format('Y-m-d H:i:s') : null,
					$this->getIsFinalized() ? 1 : 0, 
					$this->getIsPaid() ? 1 : 0, 
					$this->getIsDeleted() ? 1 : 0, 
					$this->getPaymentMethod(), 
					$this->getFinalPosition(), 
					$this->getFinalSpiritPosition(), 
					$this->getPicName(), 
					$this->getIsConvenor() ? 1 : 0, 
					$this->getIsDroppedOut() ? 1 : 0, 
					$this->getIsLateEmailAllowed() ? 1 : 0
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
			$stmt = $this->db->prepare("UPDATE " . Includes_DBTableNames::teamsTable . " SET "
					. "
						team_league_id = ?, 
						team_name = ?, 
						team_num_in_league = ?, 
						team_managed_by_user_id = ?, 
						team_wins = ?, 
						team_losses = ?, 
						team_ties = ?, 
						team_most_recent_week_submitted = ?, 
						team_finalized = ?, 
						team_paid = ?, 
						team_deleted = ?, 
						team_payment_method = ?, 
						team_final_position = ?, 
						team_final_spirit_position = ?, 
						team_pic_name = ?, 
						team_is_convenor = ?, 
						team_dropped_out = ?, 
						team_late_email_allowed = ?
					WHERE team_id = ?
					"
			);
			
			$this->db->beginTransaction(); 
			$stmt->execute(
				array(
					$this->getLeagueId(), 
					$this->getName(), 
					$this->getNumInLeague(), 
					$this->getManagedByUserId(), 
					$this->getWins(), 
					$this->getLosses(), 
					$this->getTies(), 
					$this->getMostRecentWeekSubmitted(), 
					$this->getIsFinalized() ? 1 : 0, 
					$this->getIsPaid() ? 1 : 0, 
					$this->getIsDeleted() ? 1 : 0, 
					$this->getPaymentMethod(), 
					$this->getFinalPosition(), 
					$this->getFinalSpiritPosition(), 
					$this->getPicName(), 
					$this->getIsConvenor() ? 1 : 0, 
					$this->getIsDroppedOut() ? 1 : 0, 
					$this->getIsLateEmailAllowed() ? 1 : 0,
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
