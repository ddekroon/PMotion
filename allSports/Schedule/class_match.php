<?php class Match {
	public $teamID1, $teamID2, $teamOneNum, $teamTwoNum, $teamOneString, $teamTwoString;
		
	function __construct(){
		$this->teamID1 = 0;
		$this->teamID2 = 0;
		$this->teamOneNum = 0;
		$this->teamTwoNum = 0;
		$this->teamOneString = '';
		$this->teamTwoString = '';
	}
	
	function getTeamOneID() {
		return $this->teamID1;
	}
	
	function setTeamOneID($teamID) {
		$this->teamID1 = $teamID;
	}
	
	function getTeamTwoID() {
		return $this->teamID2;
	}
	
	function setTeamTwoID($teamID) {
		$this->teamID2 = $teamID;
	}
	
	function getTeamOneNum() {
		return $this->teamOneNum;
	}
	
	function setTeamOneNum($teamNum) {
		$this->teamOneNum = $teamNum;
	}
	
	function getTeamTwoNum() {
		return $this->teamTwoNum;
	}
	
	function setTeamTwoNum($teamNum) {
		$this->teamTwoNum = $teamNum;
	}
	
	function getTeamOneLink() {
		return '/allSports/teamPage.php?teamID='.$this->teamOneID;
	}
	
	function getTeamTwoLink() {
		return '/allSports/teamPage.php?teamID='.$this->teamTwoID;
	}
	
	function getTeamOneString() {
		return $this->teamOneString;
	}
	
	function getTeamTwoString() {
		return $this->teamTwoString;
	}
	
	function setTeamOneString($teamOne) {
		$this->teamOneString = $teamOne;
	}
	
	function setTeamTwoString($teamTwo) {
		$this->teamTwoString = $teamTwo;
	}
} ?>