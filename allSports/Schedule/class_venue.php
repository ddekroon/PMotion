<?php require_once('class_match.php');

class Venue {
	public $venueName, $venueID, $matchesArray, $numMatches, $venueLink, $timeSlots, $numTimeSlots;
		
	function __construct(){
		$this->venueName = '';
		$this->venueID = 0;
		$this->numMatches = 0;
		$this->matchesArray = array();
		$this->numTimeSlots = 0;
		$this->timeSlots = array();
	}
	
	function setMatchTeams($teamID1, $teamID2, $teamOneNum, $teamTwoNum) {
		$this->matchesArray[$this->numMatches] = new Match();
		$this->matchesArray[$this->numMatches]->setTeamOneID($teamID1);
		$this->matchesArray[$this->numMatches]->setTeamTwoID($teamID2);
		$this->matchesArray[$this->numMatches]->setTeamOneNum($teamOneNum);
		$this->matchesArray[$this->numMatches]->setTeamTwoNum($teamTwoNum);
		$this->numMatches++;
	}
	
	function setMatchTeamsPlayoff($teamOneString, $teamTwoString) {
		$this->matchesArray[$this->numMatches] = new Match();
		$this->matchesArray[$this->numMatches]->setTeamOneString($teamOneString);
		$this->matchesArray[$this->numMatches]->setTeamTwoString($teamTwoString);
		$this->numMatches++;
	}
	
	function getMatchTeams($matchNum) {
		if(isset($this->matchesArray[$matchNum])) {
			return $this->matchesArray[$matchNum];
		} else {
			return new Match();
		}
	}
	
	function getVenueName() {
		return $this->venueName;
	}
	
	function setVenueName($venueName) {
		$this->venueName = $venueName;
	}
	
	function getVenueTimeSlot($slotNum) {
		return $this->timeSlots[$slotNum];
	}
	
	function getVenueTimeSlots() {
		return $this->timeSlots;
	}
	
	function setVenueTimeSlot($time) {
		if(!in_array($time, $this->timeSlots)) {
			$this->timeSlots[$this->numTimeSlots++] = $time;
		}
	}
	
	function getVenueID() {
		return $this->venueID;
	}
	
	function setVenueID($venueID) {
		$this->venueID = $venueID;
	}
	
		function setVenueLink($link) {
		$this->venueLink = $link;
	}
	
	function getVenueLink() {
		return 'http://perpetualmotion.org'.$this->venueLink;
	}
} ?>