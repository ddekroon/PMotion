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
	private $sport;
	private $teams;
	private $fenceTeams;
	private $freeAgents;
	private $dateInStandings;
	private $dateInScoreReporter;
	
	public static function withID($db, $logger, $id) {
		$instance = new self();
        $instance->loadByID($db, $logger, $id);
        return $instance;
	}
	
	public function loadByID($db, $logger, $id) {
		$this->setDb($db);
		$this->setLogger($logger);
		
		if($id == null || $id < 0) return;
		
		$sql = "SELECT * FROM " . Includes_DBTableNames::leaguesTable . " WHERE league_id = $id";

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
		$this->isShowCancelOption = $data['league_show_cancel_default_option'] > 0;
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
		$this->isFullIndividualMales = $data['league_full_individual_males'] > 0;
		$this->isFullIndividualFemales = $data['league_full_individual_females'] > 0;
		$this->isFullTeams = $data['league_full_teams'] > 0;
		$this->isShowStaticSchedule = $data['league_show_static_schedule'] > 0;
	}
	
	public function getSeason() {
		if($this->season == null && $this->db != null) {
			$this->season = Models_Season::withID($this->db, $this->logger, $this->getSeasonId());
		}
		
		return $this->season;
	}
	
	public function getSport() {
		if($this->sport == null && $this->db != null) {
			$this->sport = Models_Sport::withID($this->db, $this->logger, $this->getSportId());
		}
		
		return $this->sport;
	}
	
	public function getTeams() {
		
		if($this->teams == null && $this->getId() != null && $this->db != null) {
			$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_league_id = " . $this->getId()
					. " AND team_finalized = 1 AND team_num_in_league > 0 AND team_dropped_out = 0"
					. " ORDER BY team_num_in_league ASC";

			$stmt = $this->db->query($sql);

			$this->teams = [];

			while(($row = $stmt->fetch()) != false) {
				$this->teams[] = Models_Team::withRow($this->db, $this->logger, $row);
			}
		}

		if($this->teams == null) {
			$this->teams = [];
		}
		
		return $this->teams;
	}

	public function getFenceTeams() {
		
		if($this->fenceTeams == null && $this->getId() != null && $this->db != null) {
			$sql = "SELECT * FROM " . Includes_DBTableNames::teamsTable . " WHERE team_league_id = " . $this->getId()
					. " AND team_finalized = 0 AND team_num_in_league = 0 AND team_deleted = 0";

			$stmt = $this->db->query($sql);

			$this->fenceTeams = [];

			while(($row = $stmt->fetch()) != false) {
				$this->fenceTeams[] = Models_Team::withRow($this->db, $this->logger, $row);
			}
		}

		if($this->fenceTeams == null) {
			$this->fenceTeams = [];
		}
		
		return $this->fenceTeams;
	}

	public function getFreeAgents() {
		
		if($this->freeAgents == null && $this->getId() != null && $this->db != null) {
			$sql = "SELECT * FROM " . Includes_DBTableNames::individualsTable . " as individuals"
					. " INNER JOIN " . Includes_DBTableNames::playersTable . " as players ON players.player_id = individuals.individual_player_id "
					. " WHERE individual_preferred_league_id = " . $this->getId() . " AND player_team_id is NULL "
					. " AND individual_finalized = 1 ORDER BY individual_small_group_id ASC, player_id ASC";

			$stmt = $this->db->query($sql);

			$this->freeAgents = [];

			while(($row = $stmt->fetch()) != false) {
				$this->freeAgents[] = Models_Individual::withRow($this->db, $this->logger, $row);
			}
		}

		if($this->freeAgents == null) {
			$this->freeAgents = [];
		}

		return $this->freeAgents;

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
	
	public function getShowSpiritDayString() {
		switch(($this->getDayNumber() + 2) % 7) {
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
	
	function checkHideSpirit() {
		
		if(!$this->getLeagueAvailableScoreReporter()) { //if not in score reporter show spirits, at that point who cares
			return false;
		}

		$dayOfWeekNum = date('N'); //Number representing day of week... Mon=1, Tues=2..Sun=7
		$timeOfDay = date('G');   //24 Hour representation of time: 0-23

		$dateHide = $this->getDayNumber();
		$dateShow = $dateHide + $this->getNumDaysSpiritHidden();
		
		if($dateShow > 7) {
			$dateShow = $dateShow % 7; //if games are sunday show date is gonna be greater than 7.
		}

		if($dayOfWeekNum == $dateHide) { //If it's the day of the game, hide spirit
			return $timeOfDay >= $this->getHideSpiritHour();
		}

		if(($dayOfWeekNum == $dateShow)){ //If it's 2 days after the game, show spirit
			return $timeOfDay < $this->getShowSpiritHour();
		}

		//If it's anytime inbetween, hide spirit
		//also included for sunday... if it's supposed to be hidden on sunday and it's monday, hide spirit
		return ($dayOfWeekNum > $dateHide && $dayOfWeekNum < $dateShow) || ($dayOfWeekNum == 1 && $dateHide == 7);
	}
	
	function checkUpdateWeekInStandings() {
		
		$maxWeekInStandings = 0;
		foreach($this->getTeams() as $team) {
			if($team->getMostRecentWeekSubmitted() > $maxWeekInStandings) $maxWeekInStandings = $team->getMostRecentWeekSubmitted();
		}
		
		if ($maxWeekInStandings > $this->getWeekInStandings()) {
			$this->setWeekInStandings($maxWeekInStandings);
			$this->saveOrUpdate();
		}
	}
	
	function getDateInStandings() {
		if($this->dateInStandings == null && $this->db != null) {
			$sql = "SELECT dates.* FROM " . Includes_DBTableNames::datesTable . " as dates "
					. "WHERE dates.date_day_number = " . $this->getDayNumber() . ' AND dates.date_week_number = ' . $this->getWeekInStandings() . ' '
					. 'AND dates.date_season_id = ' . $this->getSeasonId() . ' AND dates.date_sport_id = ' . $this->getSportId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->dateInStandings =  Models_Date::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->dateInStandings;
	}
	
	function getDateInScoreReporter() {
		if($this->dateInScoreReporter == null && $this->db != null) {
			$sql = "SELECT dates.* FROM " . Includes_DBTableNames::datesTable . " as dates "
					. "WHERE dates.date_day_number = " . $this->getDayNumber() . ' AND dates.date_week_number = ' . $this->getWeekInScoreReporter() . ' '
					. 'AND dates.date_season_id = ' . $this->getSeasonId() . ' AND dates.date_sport_id = ' . $this->getSportId();

			$stmt = $this->db->query($sql);

			if(($row = $stmt->fetch()) != false) {
				$this->dateInScoreReporter =  Models_Date::withRow($this->db, $this->logger, $row);
			}
		}
		
		return $this->dateInScoreReporter;
	}
	
	function getShortName() {
		return substr($this->name, 0, 20);
	}
	
	public function getIsInPlayoffs() {
		return $this->getWeekInScoreReporter() >= $this->getPlayoffWeek();
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
	
	function getFormattedName() {
		return $this->getSport()->getName() . ' - ' . $this->getName() . ' - ' . $this->getDayString();
	}
	
	function getRegistrationFormattedName() {
		$leagueName = $this->getName();

		$leagueName .= $this->getIsFullTeams() ? ' - ** Full - Waiting List **' : '';
		$leagueName .= " - " . $this->getDayString() . " (Team Fee: $" . number_format($this->getRegistrationFee(), 2) . ')';
		
		return $leagueName;
	}

	function getRegistrationFormattedNameGroup() {
		$leagueName = $this->getName();

		$isFullText = '';

		if (($this->getIsFullIndividualMales()) && (!($this->getIsFullIndividualFemales()))) {
			$isFullText .= ' - ** Females Needed **';
		}
		else if (($this->getIsFullIndividualFemales()) && (!($this->getIsFullIndividualMales()))) {
			$isFullText .= ' - ** Males Needed **';
		}
		else if ($this->getIsFullTeams() && $this->getIsFullIndividualMales() && $this->getIsFullIndividualFemales()) {
			$isFullText .= ' - ** Full - Waiting List **';
		}

		// $leagueName .= $this->getIsFullTeams() ? ' - ** Full - Waiting List **' : ''; // Moved to if statement above
		$leagueName .= $isFullText . " - " . $this->getDayString() . " (Player Fee: $" . number_format($this->getIndividualRegistrationFee(), 2) . ')';
		
		return $leagueName;
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
