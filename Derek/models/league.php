<?php

class Models_League {
    protected $id;
    protected $name;
    protected $dayNumber;
	protected $seasonId;
	protected $seasonName;
	protected $hasPracticeGames;
	protected $hasTies;
	protected $numMatches;
	protected $numGamesPerMatch;
	protected $showCancelOption;
	protected $askForScores;
	protected $maxPointsPerGame;

	/**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['league_id'])) {
            $this->id = $data['league_id'];
        }
		
        $this->name = $data['league_name'];
        $this->dayNumber = $data['league_day_number'];
		$this->seasonId = $data['season_id'];
		$this->seasonName = $data['season_name'];
		$this->hasPracticeGames = $data['league_has_practice_games'];
		$this->hasTies = $data['league_has_ties'];
		$this->numMatches = $data['league_num_of_matches'];
		$this->numGamesPerMatch = $data['league_num_of_games_per_match'];
		$this->showCancelOption = $data['league_show_cancel_default_option'];
		$this->askForScores = $data['league_ask_for_scores'];
		$this->maxPointsPerGame = $data['league_max_points_per_game'];
    }

    public function getId() {
        return $this->id;
    }

    public function getDayNumber() {
        return $this->dayNumber;
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
	
	function getName() {
		return $this->name;
	}
	
	function getShortName() {
		return substr($this->name, 0, 20);
	}

	function getSeasonId() {
		return $this->seasonId;
	}

	function getSeasonName() {
		return $this->seasonName;
	}
	
	function getHasPracticeGames() {
		return $this->hasPracticeGames;
	}
	
	function getHasTies() {
		return $this->hasTies;
	}
	
	public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
	
	function getNumMatches() {
		return $this->numMatches;
	}

	function getNumGamesPerMatch() {
		return $this->numGamesPerMatch;
	}
	
	function getShowCancelOption() {
		return $this->showCancelOption;
	}

	function getAskForScores() {
		return $this->askForScores;
	}
	
	function getMaxPointsPerGame() {
		return $this->maxPointsPerGame;
	}
}
