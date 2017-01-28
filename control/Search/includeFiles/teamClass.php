<?
class Team
{
	public $teamID, $teamName, $teamWins, $teamLosses, $teamTies, $teamPoints, $teamWinPercent;
	public $teamSpiritAverage, $teamSpiritTotal, $teamSpiritGivenTotal, $teamSpiritNumbers, $teamSpiritGivenNumbers, $teamPointsAvailable;
	public $teamTitleSize, $teamTitleWidth, $teamSubmittedWeek, $teamFinalPosition, $teamFinalSpirit, $teamLeagueID, $teamSportID;

  	public function __construct()
  	{
    	$this->teamID = 0;
		$this->teamName = 0;
		$this->teamWins = 0;
		$this->teamLosses = 0;
		$this->teamTies = 0;
		$this->teamPoints = 0;
		$this->teamWinPercent = 0;
		$this->teamSpiritAverage = 0;
		$this->teamSpiritTotal = 0;
		$this->teamSpiritNumbers = 0;
		$this->teamPointsAvailable = 0;
		$this->teamSpiritTotalGiven = 0;
		$this->teamFinalPosition = 0;
		$this->teamFinalSpirit = 0;
		$this->teamSpiritGivenNumbers = 0;
		$this->teamLeagueID = 0;
		$this->teamSportID = 0;
		$this->teamTitleSize = 0;
		$this->teamTitleWidth = 0;
  	}
  
  	function getPoints() {
		return (($this->teamWins*2) + $this->teamTies);	  
  	}
  
  	function addSpirit($newSpirit) {
	  	return $this->teamSpiritTotal + $newSpirit;
  	}
	
	function addGivenSpirit($newSpirit) {
	  	return $this->teamSpiritGivenTotal + $newSpirit;
  	}
  
  	function getSpiritAverage() {
		if ($this->teamSpiritNumbers != 0) {
			return round($this->teamSpiritTotal/$this->teamSpiritNumbers, 2);
		} else {
			return 0;
		}
  	}
  
	function getAvailablePoints() {
		return ($this->teamWins + $this->teamTies + $this->teamLosses)*2;
  	}
  
	function getWinPercent() {
		if ($this->teamPointsAvailable != 0) {
			return round(($this->teamPoints / $this->teamPointsAvailable), 3);
		} else {
			return 0;
		}
	}
	
	function teamSpiritAvgGiven() {
		if ($this->teamSpiritGivenNumbers != 0) {
			return round($this->teamSpiritGivenTotal/$this->teamSpiritGivenNumbers, 2);
		} else {
			return 0;
		}
	}
  
	function setTitleSize() {
		if(strlen($this->teamName) >= 20) {
			$this->teamTitleSize = '9px'; 
			$this->teamTitleWidth = 'width:160px';
		} else {
			$this->teamTitleSize = '12px'; 
			$this->teamTitleWidth = '';
		}
	}
	
	function getFormattedStandings($leagueID) {
		if($this->teamSportID != 2) {
			$standings = '('.$this->teamWins .'-'.$this->teamLosses.'-'.$this->teamTies.')';
			$standings.= hideSpirit($leagueID) == false?' ('.$this->teamSpiritAverage.')':'';
		} else {
			$standings = '('.$this->teamWins .'-'.$this->teamLosses.')';
		$standings.= hideSpirit($leagueID) == false?' ('.$this->teamSpiritAverage.')':'';
		}
		return $standings;
	}
}
?>