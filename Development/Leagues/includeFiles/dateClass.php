<?
class Date
{
	public $dateID, $dateWeek, $dateActualDate, $dateYear, $dateNumLeagueMatches;

  	public function __construct()
  	{
    	$this->dateID = 0;
		$this->dateWeek = 0;
		$this->dateActualWeek = '';
		$this->dateYear = 0;
		$this->dateNumLeagueMatches = 0;
  	}
	
	public function getWeekDropDown() {
		$dateDropDown = '';
		
		for($i=1;$i<=20;$i++) {
			if($this->dateWeek == $i) {
				$dateDropDown.="<option selected value= $i>$i</option><BR>";
			} else {
				$dateDropDown.="<option value= $i>$i</option><BR>";
			}
		}
		return $dateDropDown;
	}
} ?>