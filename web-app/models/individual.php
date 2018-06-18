<?php

// NOT COMPLETE

class Models_Individual extends Models_Generic implements Models_Interface, JsonSerializable {
	protected $playerID;
	protected $phoneNumber;
	protected $preferredLeagueID;
	protected $dateCreated;
	protected $isFinalized;
	protected $managedByID;
	protected $groupID;
	protected $paymentMethod;
	protected $howHeardMethod;
	protected $howHeardOtherText;

	private $registrationComment;

	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::individualsTable . " WHERE individual_id = $id";

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
		
		if(empty($data)) {
			return;
		}
		
		if(isset($data['individual_id'])) {
            $this->id = $data['individual_id'];
        }
		
		$this->playerID = $data['individual_player_id'];
		$this->phoneNumber = $data['individual_phone'];
		$this->preferredLeagueID = $data['individual_preferred_league_id'];
		$this->dateCreated = $data['individual_created'];
		$this->isFinalized = $data['individual_finalized'];
		$this->managedByID = $data['individual_managed_by_user_id'];
		$this->groupID = $data['individual_small_group_id'];
		$this->paymentMethod = $data['individual_payment_method'];
		$this->howHeardMethod = $data['individual_hear_method'];
		$this->howHeardOtherText = $data['individual_hear_other_text'];
	}

	function getPlayerID() {
		return $this->playerID;
	}

	function getPhoneNumber() {
		return $this->phoneNumber;
	}

	function getPreferredLeagueID() {
		return $this->preferredLeagueID;
	}

	function getDateCreated() {
		return $this->dateCreated;
	}

	function getIsFinalized() {
		return $this->isFinalized;
	}

	function getManagedByID() {
		return $this->managedByID;
	}

	function getGroupID() {
		return $this->groupID;
	}

	function getPaymentMethod() {
		return $this->paymentMethod;
	}

	function getHowHeardMethod() {
		return $this->howHeardMethod;
	}

	function getHowHeardOtherText() {
		return $this->howHeardOtherText;
	}

	function setPlayerID($playerID) {
		$this->playerID = $playerID;
	}

	function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	function setPreferredLeagueID($preferredLeagueID) {
		$this->preferredLeagueID = $preferredLeagueID;
	}

	function setDateCreated($dateCreated) {
		$this->dateCreated = $dateCreated;
	}

	function setIsFinalized($isFinalized) {
		$this->isFinalized = $isFinalized;
	}

	function setManagedByID($managedByID) {
		$this->managedByID = $managedByID;
	}

	function setGroupID($groupID) {
		$this->groupID = $groupID;
	}

	function setPaymentMethod($paymentMethod)) {
		$this->paymentMethod = $paymentMethod;
	}

	function setHowHeardMethod($howHeardMethod) {
		$this->howHeardMethod = $howHeardMethod;
	}

	function setHowHeardOtherText($howHeardOtherText) {
		$this->howHeardOtherText = $howHeardOtherText;
	}

}

?>