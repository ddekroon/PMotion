<?
class Team
{
	public $teamID, $teamName, $teamWins, $teamLosses, $teamTies, $teamPoints, $teamWinPercent;
	public $teamSpiritAverage, $teamSpiritTotal, $teamSpiritNumbers, $teamPointsAvailable, $teamFinalPosition, $teamFinalSpiritPosition;
	public $titleSize, $titleWidth, $teamSubmittedWeek, $teamNumDropDown, $teamSpiritDropDown, $teamCaptainFirstName, $teamCaptainLastName;
	public $teamCaptainEmail, $teamHasIndividuals, $teamNumInLeague, $teamIsConvenor, $teamPaid, $teamOppSubmission, $teamDroppedOut;

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
		$this->teamNumDropDown = '';
		$this->teamSpiritDropDown = '';
		$this->teamFinalPosition = 0;
		$this->teamFinalSpiritPosition = 0;
		$this->teamCaptainFirstName = '';
		$this->teamCaptainLastName = '';
		$this->teamCaptainEmail = '';
		$this->teamHasIndividuals = 0;
		$this->teamNumInLeague = 0;
		$this->teamIsConvenor = 0;
		$this->teamPaid = 0;
		$this->teamOppSubmission = array();
		$this->teamDroppedOut = 0;
  	}
  
  	function getPoints() {
		return (($this->teamWins*2) + $this->teamTies);	  
  	}
  
  	function addSpirit($newSpirit) {
	  	return $this->teamSpiritTotal + $newSpirit;
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