<?php require_once('class_match.php');

class Venue {
	public $venueName, $venueID, $matchesArray, $numMatches, $venueLink, $timeSlots, $numTimeSlots, $venuesDropDown;
		
	function __construct(){
		$this->venueName = '';
		$this->venueID = 0;
		$this->numMatches = 0;
		$this->matchesArray = array();
		$this->numTimeSlots = 0;
		$this->timeSlots = array();
		$this->venuesDropDown = '';
	}
	
	function setMatchTeams($teamID1, $teamID2, $teamOneNum, $teamTwoNum, $time) {
		for($i = 0; $i < $this->numTimeSlots; $i++) {
			if($time == $this->timeSlots[$i] || ($this->timeSlots[$i] == 0 && $time > $this->timeSlots[$i - 1] 
				&& $i == $this->numTimeSlots - 1)) {
					
				$this->matchesArray[$i] = new Match();
				$this->matchesArray[$i]->setTeamOneID($teamID1);
				$this->matchesArray[$i]->setTeamTwoID($teamID2);
				$this->matchesArray[$i]->setTeamOneNum($teamOneNum);
				$this->matchesArray[$i]->setTeamTwoNum($teamTwoNum);
				$this->matchesArray[$i]->setTimeValue($timesArray[$i]);
				$this->timeSlots[$i] = $time;
				$this->numMatches++;
				break;
			}
		}
	}
	
	function setMatchTeamsPlayoff($teamOneString, $teamTwoString, $time) {
		for($i = 0; $i < $this->numTimeSlots; $i++) {
			if($time == $this->timeSlots[$i] || ($this->timeSlots[$i] == 0 && $this->timeSlots[$i - 1] < $time 
				&& $i == $this->numTimeSlots - 1)) {
				
				$this->matchesArray[$i] = new Match();
				$this->matchesArray[$i]->setTeamOneString($teamOneString);
				$this->matchesArray[$i]->setTeamTwoString($teamTwoString);
				$this->matchesArray[$i]->setTimeValue($time);
				$this->timeSlots[$i] = $time;
				$this->numMatches++;
			}
		}
	}
	
	function getMatchTeams($matchNum) {
		if(isset($this->matchesArray[$matchNum])) {
			return $this->matchesArray[$matchNum];
		} else {
			return new Match();
		}
	}
	
	function setVenueDD($venueID, $venueNames) {
		$venueIDs = array_keys($venueNames);
		$counter = 0;	
		foreach($venueNames as $venue) {
			$this->venuesDropDown .= '<option ';
			$this->venuesDropDown .= $venueID == $venueIDs[$counter] ? 'selected':'';
			$this->venuesDropDown .= ' value='.$venueIDs[$counter++].">$venue</option>";
		}
	}
	
	function getVenueDD() {
		return $this->venuesDropDown;
	}
	
	function getVenueName() {
		return $this->venueName;
	}
	
	function setVenueName($venueName) {
		$this->venueName = $venueName;
	}
	
	function getVenueTimeSlot($slotNum) {
		if($this->timeSlots[$slotNum] != NULL) {
			return $this->timeSlots[$slotNum];
		} else {
			return 0;
		}
	}
	
	function getVenueTimeSlots() {
		return $this->timeSlots;
	}
	
	function setVenueTimeSlot($time) {
		if(!in_array($time, $this->timeSlots) || $time == 0) {
			
			for($i = 0; $i < $this->numTimeSlots; $i++) {
				if($this->timeSlots[$i] == 0 && $time > $this->timeSlots[$i - 1] && $i == $this->numTimeSlots - 1) {
					//print 'VenueID:'.$this->venueID.' time:'.$i.' - '.$time.'<br />';
					$this->timeSlots[$i] = $time;
					break;
				}
			}
			if($i == $this->numTimeSlots) {
				//print 'VenueID:'.$this->venueID.' time:'.$this->numTimeSlots.' - '.$time.'<br />';
				$this->timeSlots[$this->numTimeSlots++] = $time;
			}
		}
	}
	
	function getNumTimeSlots() {
		return $this->numTimeSlots;
	}
	
	function getFormattedTime($timeNum){
		if(strlen($this->timeSlots[$timeNum] == 3)){
			$hr= substr($this->timeSlots[$timeNum],0,1);
			$mn= substr($this->timeSlots[$timeNum],1);
		}elseif($this->timeSlots[$timeNum] != '') {
			$hr= substr($this->timeSlots[$timeNum],0,2);
			$mn= substr($this->timeSlots[$timeNum],2);
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