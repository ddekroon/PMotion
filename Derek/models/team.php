<?php

class Models_Team extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $leagueId;
    protected $name;
	protected $captainId;
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
	private $captain;
	private $manager;

	public static function withID($db, $id) {
		$instance = new self();
        $instance->loadByID($db, $id);
        return $instance;
	}
	
	public function loadByID($db, $id) {
		$this->setDb($db);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_id = $id";

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
		if(isset($data['team_id'])) {
            $this->id = $data['team_id'];
        }
		
		$this->leagueId = $data['team_league_id'];
		$this->name = $data['team_name'];
		$this->captainId = $data['team_captain_id'];
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
			$this->league = Models_League::withID($this->db, $this->leagueId);
		}
		
		return $this->league;
	}

	function getCaptain() {
		return $this->captain;
	}

	function getManager() {
		return $this->manager;
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

	function getCaptainId() {
		return $this->captainId;
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

	function setCaptainId($captainId) {
		$this->captainId = $captainId;
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
