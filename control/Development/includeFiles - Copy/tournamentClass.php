<?
class Tournament
{
	public $tourneyID, $tourneyName, $tourneyIsCards, $tourneyShow, $tourneyIsLeagues, $tourneyNumLeagues, $tourneyLeagueNames;
	public $tourneyIsTeams, $tourneyNumTeams, $tourneyIsPlayers, $tourneyNumPlayers, $tourneyIsExtraField, $tourneyExtraFieldName;
	public $tourneyNumRedCards, $tourneyNumBlackCards, $tourneyDateOpen, $tourneyDateClosed, $tourneyDatePlayed, $tourneyNumDays, $tourneyNumRunning, $tourneyLogoLink;
	public $logoLink, $registrationFee, $tourneyRegIsPlayers, $tourneyRegNumPlayers;

  	public function __construct()
  	{
		$this->ID = 0;
    	$this->tourneyID = 0;
		$this->tourneyName = 0;
		$this->tourneyRegistrationFee = '';
		$this->tourneyIsCards = 0;
		$this->tourneyShow = 0;
		$this->tourneyIsLeagues = 0;
		$this->tourneyNumLeagues = 0;
		$this->tourneyLeagueNames = array();
		$this->tourneyIsTeams = 0;
		$this->tourneyNumTeams = array();
		$this->tourneyIsPlayers = 0;
		$this->tourneyNumPlayers = array();
		$this->tourneyIsExtraField = 0;
		$this->tourneyExtraFieldName = 0;
		$this->tourneyNumRedCards = array();
		$this->tourneyNumBlackCards = array();
		$this->tourneyDateOpen = '';
		$this->tourneyDateClosed = '';
		$this->tourneyDatePlayed = '';
		$this->tourneyNumDays = 0;
		$this->tourneyNumRunning = 0;
		$this->tourneyLogoLink = '';
		$this->logoLink = '';
		$this->tourneyRegIsPlayers = 0;
		$this->tourneyRegNumPlayers = 0;
  	}
	
	public function getFormattedDatePlayed() {
		if($this->tourneyNumDays == 1) {
			return date('l, F jS, Y', strtotime($this->tourneyDatePlayed));
		} elseif($this->tourneyNumDays > 1) {
			$month = date('F', strtotime($this->tourneyDatePlayed));
			$dayFirst = date('j', strtotime($this->tourneyDatePlayed));
			$dayLast = date('j', mktime(0, 0, 0, intval($month), intval($dayFirst + $this->tourneyNumDays - 1), intval($year)));
			$year = date('Y', strtotime($this->tourneyDatePlayed));
					
			$dateString = $month;
			$dateString.= ' '.date('jS', mktime(0, 0, 0, intval($month), intval($dayFirst), intval($year)));
			//this if statement should set formatted date to 'september 30th - october 1st, 20XX'... but it doesn't work, it'll just go to 31, 32, 33 etc.	
			if(intval($dayFirst) > intval($dayLast)) {
				$dateString.=' - '.date('F jS', mktime(0, 0, 0, intval($month), intval($dayLast), intval($year)));
			} else {
				$dateString.= ' - '.date('jS', mktime(0, 0, 0, intval($month), intval($dayLast), intval($year)));
			}
			$dateString.=', '.$year;
			return $dateString;
		} else {
			return 'ERROR';
		}
	}
	
	public function getFormattedDateClosed() {
		return date('l, F jS, Y', strtotime($this->tourneyDateClosed));
	}
} ?>