<?
class Submission
{
	public $submissionID, $oppTeamID, $submitterName, $submitterEmail, $result, $scoreThem, $scoreUs, $submittedDate, $gameDate;
	public $isComment, $commentValue, $isSpirit, $spiritValue, $weekNum, $dateID;

  	public function __construct()
  	{
		$this->submissionID = 0;
    	$this->oppTeamID = 0;
		$this->submitterName = '';
		$this->submitterEmail = '';
		$this->result = 0;
		$this->scoreThem = 0;
		$this->scoreUs = 0;
		$this->gameDate = 0;
		$this->submittedDate = 0;
		$this->isComment = 0;
		$this->commentValue = '';
		$this->isSpirit = 0;
		$this->spiritValue = 0;
		$this->weekNum = 0;
		$this->dateID = 0;
  	}
	
	function getResultString() {
		if($this->result == 1) {
			return 'Won';
		} else if($this->result == 2) {
			return 'Lost';
		} else if($this->result == 3) {
			return 'Tied';
		} else if($this->result == 4) {
			return 'Cancelled';
		} else if($this->result == 5) {
			return 'Practiced';
		} else if($this->result == 0) {
			return 'Alert Brad';
		}else {
			return 'Alert Derek';
		}
	}
	
	function getScoreString() {
		return ' '.$this->scoreUs.'-'.$this->scoreThem;
	}
	
}
?>