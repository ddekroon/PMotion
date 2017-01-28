<?php
class Player
{
	public $playerID, $playerTeamID, $playerTeamName, $playerFirstName, $playerLastName, $playerEmail, $playerPhone;
	public $playerGender;
	public $playerGroupID, $playerGameDay, $playerLeagueName, $playerSportID, $playerLeagueID, $playerIsCaptain, $playerHearText;
	public $playerIsIndividual, $playerIndividualGroup, $playerUserID, $playerUserEmail;	

  	public function __construct()
  	{
		$this->playerID = 0;
    	$this->playerTeamID = 0;
		$this->playerTeamName = '';
		$this->playerFirstName = '';
		$this->playerLastName = '';
		$this->playerEmail = '';
		$this->playerPhone = 0;
		$this->playerGender = '';
		$this->playerGroupID = 0;
		$this->playerGameDay = 0;
		$this->playerLeagueID = 0;
		$this->playerLeagueName = '';
		$this->playerSportID = 0;
		$this->playerIsCaptain = 0;
		$this->playerHearText = '';
		$this->playerIsIndividual = 0;
		$this->playerIndividualGroup = 0;
		$this->playerUserID = 0;
		$this->playerUserEmail = '';
  	}
	
	function getDayString() {
		if($this->playerGameDay ==1) {
			return 'Monday';
		} else if($this->playerGameDay ==2) {
			return 'Tuesday';
		} else if($this->playerGameDay ==3) {
			return 'Wednesday';
		} else if($this->playerGameDay ==4) {
			return 'Thursday';
		} else if($this->playerGameDay ==5) {
			return 'Friday';
		} else if($this->playerGameDay ==6) {
			return 'Saturday';
		} else if($this->playerGameDay ==7) {
			return 'Sunday';
		}
	}
	
	function getSportString() {
		if($this->playerSportID ==1) {
			return 'Ultimate';
		} else if($this->playerSportID ==2) {
			return 'Beach Volleyball';
		} else if($this->playerSportID ==3) {
			return 'Football';
		} else if($this->playerSportID ==4) {
			return 'Soccer';
		} else {
			return 'Unknown';
		}
	}
	
}
?>