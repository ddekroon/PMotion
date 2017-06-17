<?
class Team
{
	public $teamID, $teamName, $teamWins, $teamLosses, $teamTies, $teamPoints, $teamWinPercent;
	public $teamSpiritAverage, $teamSpiritTotal, $teamSpiritNumbers, $teamPointsAvailable, $aboutUsMethod, $aboutUsText;
	public $titleSize, $titleWidth, $teamSubmittedWeek, $teamLeagueName, $teamLeagueID, $teamSportID, $teamSportName, $teamSeasonName;
	public $teamOppWins, $teamOppLosses, $teamOppTies, $teamOppSubmissions, $teamComments, $payMethod, $isRegistered, $teamLeagueFee, $teamDayNum;
	public $teamRating, $teamPaid, $teamNumInLeague, $teamNumInTournament, $teamTournamentID, $teamNote;

  	public function __construct()
  	{
    	$this->teamID = 0;
		$this->teamLeagueID = 0;
		$this->teamName = '';
		$this->teamLeagueName = '';
		$this->teamSportID = 0;
		$this->teamSportName = '';
		$this->teamSeasonName = '';
		$this->teamWins = 0;
		$this->teamLosses = 0;
		$this->teamTies = 0;
		$this->teamPoints = 0;
		$this->teamWinPercent = 0;
		$this->teamSpiritAverage = 0;
		$this->teamSpiritTotal = 0;
		$this->teamSpiritNumbers = 0;
		$this->teamPointsAvailable = 0;
		$this->teamOppWins = 0;
		$this->teamOppLosses = 0;
		$this->teamOppTies = 0;
		$this->teamOppSubmissions = 0;
		$this->teamComments = '';
		$this->teamPayMethod = 0;
		$this->teamIsRegistered = 0;
		$this->teamLeagueFee = 0;
		$this->aboutUsMethod = 0;
		$this->aboutUsText = '';
		$this->teamDayNum = 0;
		$this->teamRating = 0;
		$this->teamPaid = 0;
		$this->teamNumInLeague = 0;
		$this->teamNumInTournament = 0;
		$this->teamTournamentID = 0;
		$this->teamNote = '';
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