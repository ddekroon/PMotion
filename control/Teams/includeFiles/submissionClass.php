<?
class Submission
{
	public $submissionID, $submissionTeamID, $submissionTeamName, $submissionDBTeamID1, $submissionDBTeamName1, $submissionDBTeamID2, $submissionDBTeamName2;
	public $submissionResults, $submissionOppTeamID, $submissionOppTeamName, $submissionWeek, $submissionDate, $submissionLeagueID, $submissionLeagueName;
	public $submissionDBOppTeamID, $submissionDBOppTeamName, $submissionDBOppResults, $submissionGames, $submissionSportID;

  	public function __construct()
  	{
		$this->submissionID = 0;
    	$this->submissionTeamID = 0; 
		$this->submissionTeamName = '';
		$this->submissionDBTeamID1 = 0; 
		$this->submissionDBTeamName1 = ''; 
		$this->submissionDBTeamID2 = 0; 
		$this->submissionDBTeamName2 = '';
		$this->submissionOppTeamID = 0;
		$this->submissionOppTeamName = '';
		$this->submissionDBTeamOppTeamID = 0;
		$this->submissionDBTeamOppTeamName = '';
		$this->submissionGames = 0;
		$this->submissionWeek = 0;
		$this->submissionDate = '';
		$this->submissionLeagueID = 0;
		$this->submissionLeagueName = '';
		$this->submissionSportID = 0;
  	}
} ?>