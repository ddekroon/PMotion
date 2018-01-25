<?php 
class Match {
	public $matchNum, $gameIsPlayed, $submissionID, $oppTeamID, $oppTeamName, $dateID, $gameDate, $matchResults;
	public $matchField, $matchGameTime, $matchShirtColour, $standingsString;

  	public function __construct()
  	{
		$this->matchNum = 0;
		$this->gameIsPlayed = 0;
		$this->submissionID = 0;
    	$this->oppTeamID = 0;
		$this->oppTeamName = '';
		$this->dateID = 0;
		$this->gameDate = '';
		$this->gameResults = array();
		$this->matchField = '';
		$this->matchFieldLink = '';
		$this->matchGameTime = '';
		$this->matchShirtColour = 'N/A';
		$this->standingsString = '';
  	}
	
	function getResultString() {
		$resultString = '';
		if(count($this->gameResults) > 0) {
			foreach($this->gameResults as $result) {
				if($result == 1) {
					$resultString .= 'Won, ';
				} else if($result == 2) {
					$resultString .= 'Lost, ';
				} else if($result == 3) {
					$resultString .= 'Tied, ';
				} else if($result == 4) {
					$resultString .= 'Cancelled, ';
				} else if($result == 5) {
					$resultString .= 'Practiced, ';
				} else if($result == 0) {
					$resultString .= '*';
					// The star means that a score was submitted and was changed back to no result (likely in the edit score submissions control
					// Go to the score_submisson_dbtable and remove the record where score_submission_team_id = the team AND score_submssion_result = 0
					// and then... NEVER DO THAT AGAIN (trust me) - Brad 
				} else {
					$resultString .= 'ERROR, ';
				}
			}
			return substr($resultString, 0, -2);
		} else {
			return '';
		}
	}
	
	public function getFormattedDatePlayed() 
	{
		if($this->gameDate != NULL || $this->gameDate != '')
		{
			return date('D, M d', strtotime($this->gameDate));
		}
		else
		{
			return 'Date Error';
		}
	}
	
	public function getFormattedGameTime() {
		return date('g:i A', strtotime($this->matchGameTime));
	}
	
} ?>