<?php
class Tournament
{
	public $tourneyID, $maxPerYear, $numTeams, $startYear, $tourneyName,
		$tourneyIsCards, $tourneyIsLeagues, $tourneyIsTeams, $tourneyIsPlayers;

  	public function __construct($tourneyID = 0, $maxPerYear = 1, $startYear = 2002) {
		$this->tourneyID = $tourneyID;
		$this->maxPerYear = $maxPerYear;
		$this->numTeams = array();
		$this->startYear = $startYear;
		$this->tourneyName = 'Tournament';
		$this->tourneyIsCards = 0;
		$this->tourneyIsLeagues = 0;
		$this->tourneyIsTeams = 0;
		$this->tourneyIsPlayers = 0;
  	}
}