<?
class Team
{
	public $teamID, $teamName, $teamWins, $teamLosses, $teamTies, $teamStndWins, $teamStndLosses, $teamStndTies;
	public $teamPoints, $teamWinPercent;
	public $teamSpiritAverage, $teamSpiritTotal, $teamSpiritNumbers, $teamPointsAvailable, $teamSeasonID;
	public $titleSize, $titleWidth, $teamSubmittedWeek, $teamLeagueName, $teamLeagueID, $teamSportID;
	public $teamOppWins, $teamOppLosses, $teamOppTies, $teamOppSubmissions, $teamNum, $teamOppTeam1, $teamOppTeam2, $teamPicName;
	public $teamWeekInScoreReporter, $teamUserID, $teamCapID, $teamCapName, $teamCapEmail, $teamPrizeDD, $teamPrizeString;
	public $teamNumDropDown, $teamSpiritDropDown, $teamFinalPosition, $teamFinalSpiritPosition, $teamOppTeamID1, $teamOppTeamID2;
	public $leagueNumSubmissions, $teamPractices, $teamCancels, $teamSubmissions, $leagueWeekInScoreReporter;
	public $oppTeamID, $gameResult, $scoreUs, $scoreThem;
	
  	public function __construct()
  	{
    	$this->teamID = 0;
		$this->teamNum = 0;
		$this->teamLeagueID = 0;
		$this->teamName = '';
		$this->teamLeagueName = '';
		$this->teamSportID = 0;
		$this->teamSeasonID = 0;
		$this->teamWins = 0;
		$this->teamLosses = 0;
		$this->teamTies = 0;
		$this->teamStndWins = 0;
		$this->teamStndLosses = 0;
		$this->teamStndTies = 0;
		$this->teamPractices = 0;
		$this->teamCancels = 0;
		$this->teamSubmissions = 0;
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
		$this->teamOppTeam1 = array();
		$this->teamOppTeam2 = array();
		$this->teamOppTeamID1 = 0;
		$this->teamOppTeamID2 = 0;
		$this->teamPicName = '';
		$this->teamWeekInScoreReporter = 0;
		$this->leagueWeekInScoreReporter = 0;
		$this->teamUserID = 0;
		$this->teamCapID = 0;
		$this->teamCapName = 0;
		$this->teamCapEmail = 0;
		$this->teamPrizeDD = '';
		$this->teamPrizeString = '';
		$this->teamNumDropDown = '';
		$this->teamSpiritDropDown = '';
		$this->teamFinalPosition = 0;
		$this->teamFinalSpiritPosition = 0;
		$this->leagueNumGames = 0;
		$this->leagueNumMatches = 0;
		$this->gameResult = array();
		$this->oppTeamID = array();
		$this->scoreUs = array();
		$this->scoreThem = array();
  	}
  
  	function getPoints() 
	{
		return (($this->teamWins*2) + $this->teamTies);	  
  	}
  
  	function addSpirit($newSpirit) 
	{
	  	return $this->teamSpiritTotal + $newSpirit;
  	}
  
  	function getSpiritAverage() 
	{
		if ($this->teamSpiritNumbers != 0) 
		{
			return round($this->teamSpiritTotal/$this->teamSpiritNumbers, 2);
		} 
		else 
		{
			return 0;
		}
  	}
  
	function getAvailablePoints() 
	{
		return ($this->teamWins + $this->teamTies + $this->teamLosses)*2;
  	}
  
	function getWinPercent() 
	{
		if ($this->teamPointsAvailable != 0) 
		{
			return round(($this->teamPoints / $this->teamPointsAvailable), 3);
		} 
		else 
		{
			return 0;
		}
	}
  
	function setTitleSize() 
	{
		if(strlen($this->teamName) >= 20) 
		{
			$this->titleSize = '9px'; 
			$this->titleWidth = "width='160'";
		} 
		else 
		{
			$this->titleSize = '12px'; 
			$this->titleWidth = '';
		}
	}
}
?>