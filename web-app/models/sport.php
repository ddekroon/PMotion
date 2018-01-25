<?php

class Models_Sport extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $name;
	protected $baseUrl;
	protected $isDefaultAskForScores;
	protected $defaultNumOfMatches;
	protected $defaultNumGamesPerMatch;
	protected $defaultMaxPointsPerGame;
	protected $isDefaultHasTies;
	protected $isDefaultSortByPct;
	protected $defaultHideSpiritHour;
	protected $defaultShowSpiritHour;
	protected $defaultSpiritHiddenForDays;
	
    protected $registrationDueDate;
	protected $defaultPicLink;
	protected $logoLink;
	protected $numPlayerInputsForRegistration;
	
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::sportsTable . " WHERE sport_id = $id";

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
        if(isset($data['sport_id'])) {
            $this->id = $data['sport_id'];
        }
		
        $this->name = $data['sport_name'];
		$this->baseUrl = $data['sport_base_url'];
		
		$this->isDefaultAskForScores = $data['sport_default_ask_for_scores'];
		$this->defaultNumOfMatches = $data['sport_default_num_of_matches'];
		$this->defaultNumGamesPerMatch = $data['sport_default_num_games_per_match'];
		$this->defaultMaxPointsPerGame = $data['sport_default_max_points_per_game'];
		$this->isDefaultHasTies = $data['sport_default_has_ties'];
		$this->isDefaultSortByPct = $data['sport_default_sort_by_pct'];
		$this->defaultHideSpiritHour = $data['sport_default_hide_spirit_hour'];
		$this->defaultShowSpiritHour = $data['sport_default_show_spirit_hour'];
		$this->defaultSpiritHiddenForDays = $data['sport_default_spirit_hidden_for_days'];
		
        $this->registrationDueDate = strtotime($data['sport_registration_due_date']);
		$this->defaultPicLink = $data['sport_default_pic_link'];
                
		if($this->getId() == 1) {
			$this->logoLink = '/Logos/ultimate_0.png';
			$this->numPlayerInputsForRegistration = 15;
		} elseif($this->getId() == 2) {
			$this->logoLink = '/Logos/volleyball_0.png';
			$this->numPlayerInputsForRegistration = 14;
		} elseif($this->getId() == 3) {
			$this->logoLink = '/Logos/football_0.png';
			$this->numPlayerInputsForRegistration = 12;
		} elseif($this->getId() == 4) {
			$this->logoLink = '/Logos/soccer_0.png';
			$this->numPlayerInputsForRegistration = 15;
		} else  {
			$this->logoLink  = '/Logos/Perpetualmotionlogo.jpg';
			$this->numPlayerInputsForRegistration = 15;
		}
    }

    public function getDayNumber() {
        return $this->dayNumber;
    }
	
	function getName() {
		return $this->name;
	}

	function getRegistrationDueDate() {
		return $this->registrationDueDate;
	}

	function getDefaultPicLink() {
		return $this->defaultPicLink;
	}

	function getLogoLink() {
		return $this->logoLink;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setRegistrationDueDate($registrationDueDate) {
		$this->registrationDueDate = $registrationDueDate;
	}

	function setDefaultPicLink($defaultPicLink) {
		$this->defaultPicLink = $defaultPicLink;
	}

	function setLogoLink($logoLink) {
		$this->logoLink = $logoLink;
	}
	
	function jsonSerialize() {
		return "{"
				. "id:" . $this->getId() . ","
				. "registrationDueDate:" . $this->getRegistrationDueDate() . ","
				. "defaultPicLink:" . $this->getDefaultPicLink() . ","
				. "logoLink:" . $this->getLogoLink()
			. "}";
	}
	
	function getIsDefaultAskForScores() {
		return $this->isDefaultAskForScores;
	}

	function getDefaultNumOfMatches() {
		return $this->defaultNumOfMatches;
	}

	function getDefaultNumGamesPerMatch() {
		return $this->defaultNumGamesPerMatch;
	}

	function getDefaultMaxPointsPerGame() {
		return $this->defaultMaxPointsPerGame;
	}

	function getIsDefaultHasTies() {
		return $this->isDefaultHasTies;
	}

	function getIsDefaultSortByPct() {
		return $this->isDefaultSortByPct;
	}

	function getDefaultHideSpiritHour() {
		return $this->defaultHideSpiritHour;
	}

	function getDefaultShowSpiritHour() {
		return $this->defaultShowSpiritHour;
	}

	function getDefaultSpiritHiddenForDays() {
		return $this->defaultSpiritHiddenForDays;
	}

	function setIsDefaultAskForScores($isDefaultAskForScores) {
		$this->isDefaultAskForScores = $isDefaultAskForScores;
	}

	function setDefaultNumOfMatches($defaultNumOfMatches) {
		$this->defaultNumOfMatches = $defaultNumOfMatches;
	}

	function setDefaultNumGamesPerMatch($defaultNumGamesPerMatch) {
		$this->defaultNumGamesPerMatch = $defaultNumGamesPerMatch;
	}

	function setDefaultMaxPointsPerGame($defaultMaxPointsPerGame) {
		$this->defaultMaxPointsPerGame = $defaultMaxPointsPerGame;
	}

	function setIsDefaultHasTies($isDefaultHasTies) {
		$this->isDefaultHasTies = $isDefaultHasTies;
	}

	function setIsDefaultSortByPct($isDefaultSortByPct) {
		$this->isDefaultSortByPct = $isDefaultSortByPct;
	}

	function setDefaultHideSpiritHour($defaultHideSpiritHour) {
		$this->defaultHideSpiritHour = $defaultHideSpiritHour;
	}

	function setDefaultShowSpiritHour($defaultShowSpiritHour) {
		$this->defaultShowSpiritHour = $defaultShowSpiritHour;
	}

	function setDefaultSpiritHiddenForDays($defaultSpiritHiddenForDays) {
		$this->defaultSpiritHiddenForDays = $defaultSpiritHiddenForDays;
	}
	
	function getNumPlayerInputsForRegistration() {
		return $this->numPlayerInputsForRegistration;
	}

	function setNumPlayerInputsForRegistration($numPlayerInputsForRegistration) {
		$this->numPlayerInputsForRegistration = $numPlayerInputsForRegistration;
	}

	function getBaseUrl() {
		return $this->baseUrl;
	}

	function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
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
