<?php 
require_once('class_venue.php');

class Week {
	public $venuesArray, $timesArray, $numTimes, $numVenues, $weekDateID, $weekDateDescription, $numColumns;
		
	function __construct(){
		$this->venuesArray = array();
		$this->timesArray = array();
		$this->numTimes = 0;
		$this->numVenues = 0;
	}
	
	function addVenue($venueID, $venueName, $venueLink, $venueNames, $numColumns){
		$this->venuesArray[$this->numVenues] = new Venue();
		$this->venuesArray[$this->numVenues]->setVenueID($venueID);
		$this->venuesArray[$this->numVenues]->setVenueName($venueName);
		$this->venuesArray[$this->numVenues]->setVenueLink($venueLink);
		$this->venuesArray[$this->numVenues]->setVenueDD($venueID, $venueNames);
		$this->numColumns = $numColumns;
		
		if($this->numVenues != 0) { //first venue is done with, add the first venue times to the others
			for($i = 0; $i < $numColumns; $i++) {
				$this->venuesArray[$this->numVenues]->setVenueTimeSlot($this->venuesArray[0]->getVenueTimeSlot($i));
			}
		}
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
	
	function setVenueMatch($teamID1, $teamID2, $teamOneNum, $teamTwoNum, $time) {
		$this->venuesArray[$this->numVenues - 1]->setMatchTeams($teamID1, $teamID2, $teamOneNum, $teamTwoNum, $time);
	}
	
	function setVenueMatchPlayoff($teamOneString, $teamTwoString, $time) {
		$this->venuesArray[$this->numVenues - 1]->setMatchTeamsPlayoff($teamOneString, $teamTwoString, $time);
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
	
	function setTime($venueID, $timeInt) {
		if(!in_array($timeInt, $this->timesArray)) {
			if($timeInt < $this->timesArray[$this->numTimes - 1] + 20 && $this->numTimes % $this->numColumns != 0) {
				$timeRows = ceil($this->numTimes / $this->numColumns);
				for($i = $this->numTimes; $i < $this->numColumns * $timeRows; $i++) {
					$this->timesArray[$i] = 0;
					$this->numTimes++;
				}
			}
			$this->timesArray[$this->numTimes++] = $timeInt;
		}
		
		for($i = 0; $i < $this->numVenues; $i++) {
			if($this->venuesArray[$i]->venueID == $venueID || $this->venuesArray[0]->venueID == $venueID) {
				$this->venuesArray[$i]->setVenueTimeSlot($timeInt);
			}
		}
	}
	
	function getTime($timeNum) {
		if($timeNum == 0) {
			return $this->timesArray[$timeNum];
		} else if($this->timesArray[$timeNum] >= $this->timesArray[$timeNum - 1]) {
			return $this->timesArray[$timeNum];
		} else {
			return 0;
		}
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
	
	function setWeekDateID($dateID) {
		$this->weekDateID = $dateID;
	}
	
	function getWeekDateID() {
		return $this->weekDateID;
	}
	
}