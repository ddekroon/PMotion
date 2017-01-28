<?
class Tournament
{
	public $tourneyID, $tourneyName, $tourneyIsCards, $tourneyShow, $tourneyIsLeagues, $tourneyNumLeagues, $tourneyLeagueNames;
	public $tourneyIsTeams, $tourneyNumTeams, $tourneyIsPlayers, $tourneyNumPlayers, $tourneyIsExtraField, $tourneyExtraFieldName;
	public $tourneyNumRedCards, $tourneyNumBlackCards, $tourneyDateOpen, $tourneyDateClosed, $tourneyDatePlayed, $tourneyNumDays;
	public $tourneyNumRunning, $tourneyIsFull, $tourneyIsFullMale, $tourneyIsFullFemale;

  	public function __construct()
  	{
		$this->ID = 0;
    	$this->tourneyID = 0;
		$this->tourneyName = 0;
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
		$this->tourneyNumDays = 0;
		$this->tourneyNumRunning = 0;
		$this->tourneyIsFull = array();
		$this->tourneyIsFullMale = array();
		$this->tourneyIsFullFemale = array();
  	}
} ?>