<?php
class Season
{
	public $seasonID, $seasonName, $seasonYear, $numTeamsSport, $numTeamsLeague;

  	public function __construct($seasonID = 0, $seasonName = '', $seasonYear = 0) {
		$this->seasonID = $seasonID;
		$this->seasonName= $seasonName;
		$this->seasonYear = $seasonYear;
		$this->numTeamsSport = array();
		$this->numTeamsLeague = array();
  	}
}