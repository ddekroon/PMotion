<?php
class Team {
	public $team_title_size;
	
  	public function __construct($teamObj) {
		if($teamObj != '') {
    		foreach($teamObj as $key => $value) {
				$this->$key = $value;
			}
		}
  	}
  
  	function getPoints() {
		return ($this->team_wins*2) + $this->team_ties;	  
  	}
	
	function getWinPercent() {
		$pointsAvailable = ($this->team_wins + $this->team_ties + $this->team_losses)*2;
		if ($pointsAvailable != 0) {
			return round(($this->team_points / $pointsAvailable), 3);
		} else {
			return 0;
		}
	}
  
	function getTitleSize() {
		if(strlen($this->team_name) >= 20) {
			return '9px';
		} else {
			return '12px'; 
		}
	}
	
	function getFormattedStandings() {
		if($this->league_sport_id != 2) {
			$standings = '('.$this->team_wins .'-'.$this->team_losses.'-'.$this->team_ties.')';
			$standings.= hideSpirit($this) == false?' ('.number_format($this->team_spirit_average, 2, '.', '').')':'';
		} else {
			$standings = '('.$this->team_wins .'-'.$this->team_losses.')';
			$standings.= hideSpirit($this) == false?' ('.number_format($this->team_spirit_average, 2, '.', '').')':'';
		}
		return $standings;
	}
}
?>