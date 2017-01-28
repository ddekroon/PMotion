<?
require_once('includeFiles/seasonClass.php');
class League
{
	public $leagueID, $leagueName, $leagueSeasonID, $leagueSportID, $leagueDayNumber, $leagueRegistrationFee, $leagueAskForScores;
	public $leagueNumOfMatches, $leagueNumOfGamesPerMatch, $leagueHasTies, $leagueHasPracticeGames, $leagueMaxPointsPerGame; 
	public $leagueShowCancelDefaultOption, $leagueSendLateEmail, $leagueHideSpiritHour, $leagueShowSpiritHour;
	public $leagueNumDaysSpiritHidden, $leagueWeekInScoreReporter, $leagueWeekInStandings, $leagueSortByWinPct, $leagueShowSpirit;
	public $leagueAllowsIndividuals, $leagueAvailableForRegistration, $leagueNumTeamsBeforeWaiting, $leagueMaximumTeams;
	public $leaguePlayoffWeek, $leagueIndividualRegistrationFee, $leaguePicLink, $leagueScheduleLink, $seasonObj;

  	public function _Construct()
  	{
		$this->leagueID = 0;
		$this->leagueName = '';
		$this->leagueSeasonID = 0;
		$this->leagueSportID = 0;
		$this->leagueDayNumber = 0;
		$this->leagueRegistrationFee = 0;
		$this->leagueAskForScores = 0;
		$this->leagueNumOfMatches = 0;
		$this->leagueNumOfGamesPerMatch = 0;
		$this->leagueHasTies = 0;
		$this->leagueHasPracticeGames = 0;
		$this->leagueMaxPointsPerGame = 0;
		$this->leagueShowCancelDefaultOption = 0;
		$this->leagueSendLateEmail = 0;
		$this->leagueHideSpiritHour = 0;
		$this->leagueShowSpiritHour = 0;
		$this->leagueNumDaysSpiritHidden = 0;
		$this->leagueWeekInScoreReporter = 0;
		$this->leagueWeekInStandings = 0;
		$this->leagueSortByWinPct = 0;
		$this->leagueShowSpirit = 0;
		$this->leagueAllowsIndividuals = 0;
		$this->leagueAvailableForRegistration = 0;
		$this->leagueNumTeamsBeforeWaiting = 0;
		$this->leagueMaximumTeams = 0;
		$this->leaguePlayoffWeek = 0;
		$this->leagueIndividualRegistrationFee = 0;
		$this->leaguePicLink = '';
		$this->leagueScheduleLink = '';
		$this->seasonObj = new Season();
  	}
}
?>