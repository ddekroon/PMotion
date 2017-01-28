<?php

class ScheduleVariables {
	
	public $leagueID, $scheduleTitle, $numWeeks, $playoffStartWeek, $sportID, $seasonID;
	public $venuesArray, $timesArray, $numVenues, $numTimes, $sameVenues, $sameTimes;
	public $startDay, $startMonth, $startYear;

	function __construct(){
		$this->leagueID = 0;
		$this->scheduleTitle = 0;
		$this->numWeeks = 0;
		$this->playoffStartWeek = 0;
		$this->sportID = 0;
		$this->seasonID = 0;
		$this->venuesArray = array();
		$this->timesArray = array();
		$this->numVenues = 0;
		$this->numTimes = 0;
		$this->sameVenues = 0;
		$this->sameTimes = 0;
		$this->startDay = 0;
		$this->startMonth = 0;
		$this->startYear = 0;
	}
}