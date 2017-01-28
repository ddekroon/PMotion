<?php //Magic quotes by default is on, this function takes out all backslashes in the superglobal variables
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
			array_map('stripslashes_deep', $value) :
			stripslashes($value);
        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

if(($sportID = $_GET['sportID']) == ''){
    $sportID = 0;
}

$seasonArray = query("SELECT * FROM $seasonsTable WHERE season_available_registration = 1");
$registration_dueDB = $seasonArray['season_registration_due_by']; //sets what shows up on the registration form as the due date.
$seas_name=$seasonArray['season_name'];
$registration_due = date('F j, Y', strtotime($registration_dueDB));

$sportArray = query("SELECT * FROM $sportsTable WHERE sport_id = $sportID");
$sportName = $sportArray['sport_name']; //sets what shows up on the registration form as the due date.

//Variable declarations based on what sport is being registered for:

//$type= Common name (proper capitalization) of the sport "../register.PHP?sport='soccer'" .... really it's "6 vs 6 Soccer" etc
//$logo= Image path (and sometimes resizing information) for the logos
//$sportHeader= Title seen on the form
//$titleHeader= Title seen in the browser window
//$people= Number of player spots allotted on the registration form
//$filter= For the purpose of the query, you should never have to touch this
function declareSportVariables() {
	global $sportID, $logo, $sportHeader, $titleHeader, $people, $update, $leagueID;
	
	if($sportID==1){
		$logo="/Logos/GuelphUltimate.jpg";
		$titleHeader="Register - Guelph Ultimate";
		$sportHeader="<br>Register as a group/individual for Guelph Ultimate";
		$people=15;
	}elseif($sportID==2){
		$logo="/Logos/WheresTheBeach.jpg";
		$titleHeader="Register - Where's The Beach Volleyball";
		$sportHeader="<br>Register as a group/individual for Where's The Beach Volleyball";
		$people=14;
	}elseif($sportID==3){
		$logo="/Logos/GuelphFlagFootball.jpg";
		$titleHeader="Register - Guelph Flag Football";
		$sportHeader="<br>Register as a group/individual for Guelph Flag Football";
		$people=12;
	}elseif($sportID==4){
		$logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
		$titleHeader="Register - Guelph Soccer";
		$sportHeader="<br>Register as a group/individual for Guelph Soccer";
		$people=15;
	}
}

function getSubmittedValues() {
	global $groupComments, $leagueID, $groupComments, $payMethod, $people, $sportID, $aboutUsMethod, $aboutUsText;
	
	for($i=0;$i<3;$i++) {
		$leagueID[$i] = $_POST['leagueID'][$i];	
	}
	$numPeople = 0;
	for($a=0; $a < 50; $a++){
		if(strlen($_POST['playerFirst'][$a]) > 2) { 
			$player[$numPeople] = new Player();
			$player[$numPeople]->playerFirstName = $_POST['playerFirst'][$a];
			$player[$numPeople]->playerLastName = $_POST['playerLast'][$a];
			$player[$numPeople]->playerEmail = $_POST['playerEmail'][$a];
			$player[$numPeople]->playerSkill = $_POST['playerSkill'][$a];
			if(($player[$numPeople]->playerPhone = $_POST['playerPhone'][$a]) == '') {
				$player[$numPeople]->playerPhone = 0;
			}
			$player[$numPeople]->playerGender = $_POST['playerGender'][$a];
			$numPeople++;
		}
	}
	$groupComments = $_POST['groupComments'];
	$payMethod = $_POST['payMethod'];
	$aboutUsMethod = $_POST['aboutUsMethod'];
	$aboutUsText = $_POST['aboutUsTextBox'];
	
	return $player;
}

function getLeagueNames($leagueID) {
	global $leaguesTable;
	for($i=0;$i<3;$i++) {
		if($leagueID[$i] != 0) {
			$league = query("SELECT * FROM $leaguesTable WHERE league_id = $leagueID[$i]");
			$leagueDay = dayString($league['league_day_number']);
			$leagueNames[$i] = getLeagueName($league['league_name'], $league['league_full_individual_males'], 
				$league['league_full_individual_females'], $leagueDay, $league['league_individual_registration_fee']);
		} else {
			$leagueNames[$i] = 'N/A';
		}
	}
	return $leagueNames;
}

function getSportDD($sportID) {
	global $sportsTable;
	$sportDropDown = '';
	//leagues in dropdown
	$sportsQuery=mysql_query("SELECT * FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id") or die("ERROR gettings sports drop down - ".mysql_error());
	while($sport = mysql_fetch_array($sportsQuery)) {
		if($sport['sport_id']==$sportID){
			$sportDropDown.="<option selected value= $sport[sport_id]>$sport[sport_name]</option>";
		}else{
			$sportDropDown.="<option value= $sport[sport_id]>$sport[sport_name]</option>";
		}
	}
	return $sportDropDown;
}

function getLeagueDD($leagueID, $sportID) {
	global $leaguesTable, $seasonsTable;
	$leagueDropDown = '';
	//leagues in dropdown
	$leaguesQuery=mysql_query("SELECT * FROM $leaguesTable 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE season_available_registration = 1 AND league_sport_id = $sportID AND league_allows_individuals = 1 
		ORDER BY league_day_number ASC, league_name ASC") or die("TEST ".mysql_error());
	while($league = mysql_fetch_array($leaguesQuery)) {
		
		$leagueDay = dayString($league['league_day_number']);
		$league['league_id'] == $leagueID ? $selectedFilter = 'selected':$selectedFilter = '';
		$leagueName = getLeagueName($league['league_name'], $league['league_full_individual_males'], 
			$league['league_full_individual_females'], $leagueDay, $league['league_individual_registration_fee']);
			
		$leagueDropDown.="<option $selectedFilter value= $league[league_id]>$league[season_name] -- $leagueName</option> ";
	}
	return $leagueDropDown;
}

function getLeagueName($tempLeagueName, $malesFull, $femalesFull, $leagueDay, $regFee) {
	$leagueName = $tempLeagueName;
	
	if($malesFull > 0 && $femalesFull > 0) {
		$leagueName .= '- Full ';
	} else {
		$malesFull > 0 ? $leagueName .= '- Females Needed ' : $leagueName .= '';
		$femalesFull > 0 ? $leagueName .= '- Males Needed ' : $leagueName .= '';
	}
	$leagueName .= "- $leagueDay ($".number_format($regFee, 2)." pp)";
	
	return $leagueName;
}

function getPayInfoDD($payMethod, $seas_name) {
	$i=4;
	$payMethodString[1] = 'I will send an email money transfer to dave@perpetualmotion.org';
	$payMethodString[2] = 'I will mail cheque to Perpetual Motion\'s home office';
	$payMethodString[3] = 'I will bring cash/cheque to Perpetual Motion\'s home office';
	if ($seas_name == "Summer" || $seas_name == "Spring") {
		$payMethodString[4] = 'I will bring cash/cheque to registration night';
		$i=$i+1;
	}
	$payInfoDropDown = '';
	
	$payInfoDropDown.= "<option value=0>Choose Payment Method</option>";
		for($x=1; $x<$i;$x++) {
			if ($x == $payMethod) {
				$payInfoDropDown.= "<option selected value=$x>$payMethodString[$x]</option>";
			} else {
				$payInfoDropDown.= "<option value=$x>$payMethodString[$x]</option>";
			}
		}
	return $payInfoDropDown;
}

function getAboutUsDD($aboutUsMethod) {
	$aboutUsMethodString[1] = 'Google/Internet Search';
	$aboutUsMethodString[2] = 'Facebook Page';
	$aboutUsMethodString[3] = 'Kijiji Ad';
	$aboutUsMethodString[4] = 'Returning Player';
	$aboutUsMethodString[5] = 'From A Friend';
	$aboutUsMethodString[6] = 'Restaurant Advertisement';
	$aboutUsMethodString[7] = 'The Guelph Community Guide';
	$aboutUsMethodString[8] = 'Other';
	$aboutUsDropDown = '';
	
	$aboutUsDropDown.= "<option value=0>Choose Method</option>";
		for($x=1; $x<9;$x++) {
			if ($x == $aboutUsMethod) {
				$aboutUsDropDown.= "<option selected value=$x>$aboutUsMethodString[$x]</option>";
			} else {
				$aboutUsDropDown.= "<option value=$x>$aboutUsMethodString[$x]</option>";
			}
		}
	return $aboutUsDropDown;
}