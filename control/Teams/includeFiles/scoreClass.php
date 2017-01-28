<?php
class Score
{
	public $scoreID, $scoreTeamID, $scoreOppTeamID, $scoreTeamName, $scoreOppTeamName, $scoreResult, $scoreScoreUs, $scoreScoreThem, $scoreNote;
	public $scoreSpirit, $scoreSubmitterName, $scoreSubmitterEmail, $scoreSubmitterNote, $scoreLeagueID, $spiritLeagueName, $scoreDateID, $scoreGameDay;

  	public function __construct()
  	{
    	$this->scoreID = 0;
		$this->scoreTeamID = 0;
		$this->scoreOppTeamID = 0;
		$this->scoreTeamName = '';
		$this->scoreOppTeamName = '';
		$this->scoreResult = 0;
		$this->scoreScoreUs = 0;
		$this->scoreScoreThem = 0;
		$this->scoreSpirit = 0;
		$this->scoreSubmitterName = '';
		$this->scoreSubmitterEmail = '';
		$this->scoreSubmitterNote = '';
		$this->spiritLeagueID = 0;
		$this->spiritLeagueName = '';
		$this->scoreDateID = 0;
		$this->scoreGameDay = '';
		$this->scoreSubmittedDate = '';
		$this->scoreNote = '';
  	}
	
	public function getResultString() {
		if($this->scoreResult == 1) {
			return 'Won: '.$this->scoreScoreUs.' - '.$this->scoreScoreThem;
		} elseif($this->scoreResult == 2) {
			return 'Lost: '.$this->scoreScoreUs.' - '.$this->scoreScoreThem;
		} elseif($this->scoreResult == 3) {
			return 'Tied: '.$this->scoreScoreUs.' - '.$this->scoreScoreThem;
		} elseif($this->scoreResult == 4) {
			return 'Cancelled: '.$this->scoreScoreUs.' - '.$this->scoreScoreThem;
		} elseif($this->scoreResult == 5) {
			return 'Practiced: '.$this->scoreScoreUs.' - '.$this->scoreScoreThem;
		} else {
			return 'ERROR game result > 5';
		}
	}
} ?>