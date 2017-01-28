<?
class Spirit
{
	public $spiritID, $spiritTeamID, $spiritTeamName, $spiritLeagueID, $spiritLeagueName, $spiritValue, $spiritSubmitterName,
		$spiritSubmitterEmail, $oppTeamID, $gameDate, $gameWeek, $reportDate, $scoreSubmissionID, $oppTeamName, $comment,
		$spiritDropDown;

  	public function __construct()
  	{
    	$this->spiritID = 0;
		$this->spiritTeamID = 0;
		$this->spiritTeamName = '';
		$this->spiritLeagueID = 0;
		$this->spiritLeagueName = '';
		$this->spiritValue = 0;
		$this->spiritSubmitterName = '';
		$this->spiritSubmitterEmail = '';
		$this->oppTeamID = 0;
		$this->gameDate = '';
		$this->gameWeek = 0;
		$this->reportDate = '';
		$this->scoreSubmissionID = 0;
		$this->oppTeamName = '';
		$this->comment = '';
		$this->spiritDropDown = '';
  	}
} ?>