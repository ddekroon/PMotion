<?
class Team
{
	public $teamID, $teamName, $teamWins, $teamLosses, $teamTies, $teamPoints, $teamWinPercent;
	public $teamSpiritAverage, $teamSpiritTotal, $teamSpiritGivenTotal, $teamSpiritNumbers, $teamSpiritGivenNumbers, $teamPointsAvailable;
	public $titleSize, $titleWidth, $teamSubmittedWeek, $teamFinalPosition, $teamFinalSpirit, $teamCaptainName, $teamCaptainEmail;
	public $teamRating, $teamPaid, $teamLeagueID, $teamNumInLeague, $teamNumInTournament, $teamTournamentID, $teamNote, $teamNumsDropDown, $teamRatingDropDown;

  	public function __construct()
  	{
    	$this->teamID = 0;
		$this->teamName = '';
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
		$this->teamRating = 0;
		$this->teamPaid = 0;
		$this->teamLeagueID = 0;
		$this->teamNumInLeague = 0;
		$this->teamNumInTournament = 0;
		$this->teamTournamentID = 0;
		$this->teamNote = '';
		$this->teamNumsDropDown = '';
		$this->teamRatingDropDown = '';
		$this->teamCaptainName = '';
		$this->teamCaptainEmail = '';
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
			$this->titleSize = '9px'; 
			$this->titleWidth = "width='160'";
		} else {
			$this->titleSize = '12px'; 
			$this->titleWidth = '';
		}
	}
}
?>