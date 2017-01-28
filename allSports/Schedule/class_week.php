<?php 
require_once('class_venue.php');

class Week {
	public $venuesArray, $timesArray, $numTimes, $numVenues, $weekDateID, $weekDateDescription;
		
	function __construct(){
		$this->venuesArray = array();
		$this->timesArray = array();
		$this->numTimes = 0;
		$this->numVenues = 0;
	}
	
	function addVenue($venueID, $venueName, $venueLink){
		$this->venuesArray[$this->numVenues] = new Venue();
		$this->venuesArray[$this->numVenues]->setVenueID($venueID);
		$this->venuesArray[$this->numVenues]->setVenueName($venueName);
		$this->venuesArray[$this->numVenues]->setVenueLink($venueLink);
		$this->numVenues++;
	}
	
	function getVenue($venueNum) {
		return $this->venuesArray[$venueNum];
	}
	
	function getAllVenues() {
		return $this->venuesArray;
	}
	
	function getNumVenues() {
		return $this->numVenues;
	}
	
	function setVenueMatch($teamID1, $teamID2, $teamOneNum, $teamTwoNum) {
		$this->venuesArray[$this->numVenues - 1]->setMatchTeams($teamID1, $teamID2, $teamOneNum, $teamTwoNum);
	}
	
	function setVenueMatchPlayoff($teamOneString, $teamTwoString) {
		$this->venuesArray[$this->numVenues - 1]->setMatchTeamsPlayoff($teamOneString, $teamTwoString);
	}
	
	function getFieldNameValue($venueNum){
		return $this->venuesArray[$venueNum];
	}

	function getFormattedTime($timeNum){
			if(strlen($this->timesArray[$timeNum] == 3)){
				$hr= substr($this->timesArray[$timeNum],0,1);
				$mn= substr($this->timesArray[$timeNum],1);
			}elseif($this->timesArray[$timeNum] != '') {
				$hr= substr($this->timesArray[$timeNum],0,2);
				$mn= substr($this->timesArray[$timeNum],2);
			} else {
				return '';
			}
			if ($hr>=13){
				$hr = $hr-12;
				$meridiem = "pm";
			} else {
				$meridiem = "am";
			}
		return "$hr:$mn $meridiem";
	}
	
	function setTime($timeInt) {
		$this->timesArray[$this->numTimes++] = $timeInt;
	}
	
	function getTime($timeNum) {
		return $this->timesArray[$timeNum];
	}
	
	function getTimes() {
		return $this->timesArray;
	}
	
	function setNumTimes($numTimes) {
		$this->numTimes = $numTimes;
	}
	
	function getNumTimes() {
		return $this->numTimes;
	}
	
	function setWeekDescription($dateID, $dateDescription) {
		$this->weekDateID = $dateID;
		$this->weekDateDescription = $dateDescription;
	}
	
	function getWeekDateDescription() {
		return $this->weekDateDescription;
	}
	
	function getWeekDateID() {
		return $this->weekDateID;
	}
	
}