<?
class Season
{
	public $seasonID, $seasonName, $seasonYear, $seasonAvailableRegistration, $seasonAvailableScoreReporter;
	public $seasonRegistrationOpensDate, $seasonConfirmationDueBy, $seasonRegistrationDueBy, $seasonRegistrationUpUntil;
	public $seasonNumWeeks, $regBySport;

  	public function __construct()
  	{
    	$this->seasonID = 0;
		$this->seasonName = '';
		$this->seasonYear = 0;
		$this->seasonNumWeeks = 0;
		$this->seasonAvailableRegistration = 0;
		$this->seasonAvailableScoreReporter = 0;
		$this->seasonRegistrationOpensDate = '';
		$this->seasonConfirmationDueBy = '';
		$this->seasonRegistrationDueBy = '';
		$this->seasonRegistrationUpUntil = '';
		$this->regBySport = 0;
  	}
}
?>