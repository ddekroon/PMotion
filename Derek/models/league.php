<?php

class Models_League extends Models_Generic implements Models_Interface, JsonSerializable {
    protected $name;
	protected $seasonId;
	protected $sportId;
    protected $dayNumber;
	protected $registrationFee;
	protected $isAskForScores;
	protected $numMatches;
	protected $numGamesPerMatch;
	protected $isTies;
	protected $isPracticeGames;
	protected $maxPointsPerGame;
	protected $isShowCancelOption;
	protected $isSendLateEmail;
	protected $hideSpiritHour;
	protected $showSpiritHour;
	protected $numDaysSpiritHidden;
	protected $weekInScoreReporter;
	protected $weekInStandings;
	protected $isSortByWinPct;
	protected $isShowSpirit;
	protected $isAllowIndividuals;
	protected $isAvailableForRegistration;
	protected $numTeamsBeforeWaiting;
	protected $maximumTeams;
	protected $playoffWeek;
	protected $individualRegistrationFee;
	protected $picLink;
	protected $scheduleLink;
	protected $isSplit;
	protected $splitWeek;
	protected $isFullIndividualMales;
	protected $isFullIndividualFemales;
	protected $isFullTeams;
	protected $isShowStaticSchedule;
	
	private $season;
	
	public static function withID($db, $id) {
		$instance = new self();
        $instance->loadByID($db, $id);
        return $instance;
	}
	
	public function loadByID($db, $id) {
		$this->setDb($db);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::leaguesTable . " WHERE league_id = $id";

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
        if(isset($data['league_id'])) {
            $this->id = $data['league_id'];
        }
		
        $this->name = $data['league_name'];
		$this->seasonId = $data['league_season_id'];
		$this->sportId = $data['league_sport_id'];
		$this->dayNumber = $data['league_day_number'];
		$this->registrationFee = $data['league_registration_fee'];
		$this->isAskForScores = $data['league_ask_for_scores'] > 0;
		$this->numMatches = $data['league_num_of_matches'];
		$this->numGamesPerMatch = $data['league_num_of_games_per_match'];
		$this->isTies = $data['league_has_ties'] > 0;
		$this->isPracticeGames = $data['league_has_practice_games'] > 0;
		$this->maxPointsPerGame = $data['league_max_points_per_game'];
		$this->isCancelOption = $data['league_show_cancel_default_option'] > 0;
		$this->isSendLateEmail = $data['league_send_late_email'] > 0;
		$this->hideSpiritHour = $data['league_hide_spirit_hour'];
		$this->showSpiritHour = $data['league_show_spirit_hour'];
		$this->numDaysSpiritHidden = $data['league_num_days_spirit_hidden'];
		$this->weekInScoreReporter = $data['league_week_in_score_reporter'];
		$this->weekInStandings = $data['league_week_in_standings'];
		$this->isSortByWinPct = $data['league_sort_by_win_pct'];
		$this->isShowSpirit = $data['league_show_spirit'] > 0;
		$this->isAllowIndividuals = $data['league_allows_individuals'] > 0;
		$this->isAvailableForRegistration = $data['league_available_for_registration'] > 0;
		$this->numTeamsBeforeWaiting = $data['league_num_teams_before_waiting'];
		$this->maximumTeams = $data['league_maximum_teams'];
		$this->playoffWeek = $data['league_playoff_week'];
		$this->individualRegistrationFee = $data['league_individual_registration_fee'];
		$this->picLink = $data['league_pic_link'];
		$this->scheduleLink = $data['league_schedule_link'];
		$this->isSplit = $data['league_is_split'] > 0;
		$this->splitWeek = $data['league_split_week'];
		$this->isFullIndividualMales = $data['league_full_individual_males'];
		$this->isFullIndividualFemales = $data['league_full_individual_females'];
		$this->isFullTeams = $data['league_full_teams'];
		$this->isShowStaticSchedule = $data['league_show_static_schedule'];
	}
	
	public function getSeason() {
		if($this->season == null && $this->db != null) {
			$this->season = Models_Season::withID($this->db, $this->getSeasonId());
		}
		
		return $this->season;
	}
	
	public function getDayString() {

		switch($this->getDayNumber()) {
			case 1:
				return 'Monday';
			case 2: 
				return 'Tuesday';
			case 3:
				return 'Wednesday';
			case 4:
				return 'Thursday';
			case 5:
				return 'Friday';
			case 6:
				return 'Saturday';
			default:
				return 'Sunday';
		}
	}
	
	function getShortName() {
		return substr($this->name, 0, 20);
	}
	
	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
	
	public function toString() {
		return "Modules_League[id=$this->id]";
	}
	
	function getId() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function getSeasonId() {
		return $this->seasonId;
	}

	function getSportId() {
		return $this->sportId;
	}

	function getDayNumber() {
		return $this->dayNumber;
	}

	function getRegistrationFee() {
		return $this->registrationFee;
	}

	function getIsAskForScores() {
		return $this->isAskForScores;
	}

	function getNumMatches() {
		return $this->numMatches;
	}

	function getNumGamesPerMatch() {
		return $this->numGamesPerMatch;
	}

	function getIsTies() {
		return $this->isTies;
	}

	function getIsPracticeGames() {
		return $this->isPracticeGames;
	}

	function getMaxPointsPerGame() {
		return $this->maxPointsPerGame;
	}

	function getIsShowCancelOption() {
		return $this->isShowCancelOption;
	}

	function getIsSendLateEmail() {
		return $this->isSendLateEmail;
	}

	function getHideSpiritHour() {
		return $this->hideSpiritHour;
	}

	function getShowSpiritHour() {
		return $this->showSpiritHour;
	}

	function getNumDaysSpiritHidden() {
		return $this->numDaysSpiritHidden;
	}

	function getWeekInScoreReporter() {
		return $this->weekInScoreReporter;
	}

	function getWeekInStandings() {
		return $this->weekInStandings;
	}

	function getIsSortByWinPct() {
		return $this->isSortByWinPct;
	}

	function getIsShowSpirit() {
		return $this->isShowSpirit;
	}

	function getIsAllowIndividuals() {
		return $this->isAllowIndividuals;
	}

	function getIsAvailableForRegistration() {
		return $this->isAvailableForRegistration;
	}

	function getNumTeamsBeforeWaiting() {
		return $this->numTeamsBeforeWaiting;
	}

	function getMaximumTeams() {
		return $this->maximumTeams;
	}

	function getPlayoffWeek() {
		return $this->playoffWeek;
	}

	function getIndividualRegistrationFee() {
		return $this->individualRegistrationFee;
	}

	function getPicLink() {
		return $this->picLink;
	}

	function getScheduleLink() {
		return $this->scheduleLink;
	}

	function getIsSplit() {
		return $this->isSplit;
	}

	function getSplitWeek() {
		return $this->splitWeek;
	}

	function getIsFullIndividualMales() {
		return $this->isFullIndividualMales;
	}

	function getIsFullIndividualFemales() {
		return $this->isFullIndividualFemales;
	}

	function getIsFullTeams() {
		return $this->isFullTeams;
	}

	function getIsShowStaticSchedule() {
		return $this->isShowStaticSchedule;
	}

	function setId($id) {
		$this->id = $id;
	}

	function setName($name) {
		$this->name = $name;
	}

	function setSeasonId($seasonId) {
		$this->seasonId = $seasonId;
	}

	function setSportId($sportId) {
		$this->sportId = $sportId;
	}

	function setDayNumber($dayNumber) {
		$this->dayNumber = $dayNumber;
	}

	function setRegistrationFee($registrationFee) {
		$this->registrationFee = $registrationFee;
	}

	function setIsAskForScores($isAskForScores) {
		$this->isAskForScores = $isAskForScores;
	}

	function setNumMatches($numMatches) {
		$this->numMatches = $numMatches;
	}

	function setNumGamesPerMatch($numGamesPerMatch) {
		$this->numGamesPerMatch = $numGamesPerMatch;
	}

	function setIsTies($isTies) {
		$this->isTies = $isTies;
	}

	function setIsPracticeGames($isPracticeGames) {
		$this->isPracticeGames = $isPracticeGames;
	}

	function setMaxPointsPerGame($maxPointsPerGame) {
		$this->maxPointsPerGame = $maxPointsPerGame;
	}

	function setIsShowCancelOption($isShowCancelOption) {
		$this->isShowCancelOption = $isShowCancelOption;
	}

	function setIsSendLateEmail($isSendLateEmail) {
		$this->isSendLateEmail = $isSendLateEmail;
	}

	function setHideSpiritHour($hideSpiritHour) {
		$this->hideSpiritHour = $hideSpiritHour;
	}

	function setShowSpiritHour($showSpiritHour) {
		$this->showSpiritHour = $showSpiritHour;
	}

	function setNumDaysSpiritHidden($numDaysSpiritHidden) {
		$this->numDaysSpiritHidden = $numDaysSpiritHidden;
	}

	function setWeekInScoreReporter($weekInScoreReporter) {
		$this->weekInScoreReporter = $weekInScoreReporter;
	}

	function setWeekInStandings($weekInStandings) {
		$this->weekInStandings = $weekInStandings;
	}

	function setIsSortByWinPct($isSortByWinPct) {
		$this->isSortByWinPct = $isSortByWinPct;
	}

	function setIsShowSpirit($isShowSpirit) {
		$this->isShowSpirit = $isShowSpirit;
	}

	function setIsAllowIndividuals($isAllowIndividuals) {
		$this->isAllowIndividuals = $isAllowIndividuals;
	}

	function setIsAvailableForRegistration($isAvailableForRegistration) {
		$this->isAvailableForRegistration = $isAvailableForRegistration;
	}

	function setNumTeamsBeforeWaiting($numTeamsBeforeWaiting) {
		$this->numTeamsBeforeWaiting = $numTeamsBeforeWaiting;
	}

	function setMaximumTeams($maximumTeams) {
		$this->maximumTeams = $maximumTeams;
	}

	function setPlayoffWeek($playoffWeek) {
		$this->playoffWeek = $playoffWeek;
	}

	function setIndividualRegistrationFee($individualRegistrationFee) {
		$this->individualRegistrationFee = $individualRegistrationFee;
	}

	function setPicLink($picLink) {
		$this->picLink = $picLink;
	}

	function setScheduleLink($scheduleLink) {
		$this->scheduleLink = $scheduleLink;
	}

	function setIsSplit($isSplit) {
		$this->isSplit = $isSplit;
	}

	function setSplitWeek($splitWeek) {
		$this->splitWeek = $splitWeek;
	}

	function setIsFullIndividualMales($isFullIndividualMales) {
		$this->isFullIndividualMales = $isFullIndividualMales;
	}

	function setIsFullIndividualFemales($isFullIndividualFemales) {
		$this->isFullIndividualFemales = $isFullIndividualFemales;
	}

	function setIsFullTeams($isFullTeams) {
		$this->isFullTeams = $isFullTeams;
	}

	function setIsShowStaticSchedule($isShowStaticSchedule) {
		$this->isShowStaticSchedule = $isShowStaticSchedule;
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
