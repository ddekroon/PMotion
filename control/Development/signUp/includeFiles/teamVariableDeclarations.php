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
if (($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}

//Variable declarations based on what sport is being registered for:

//$type= Common name (proper capitalization) of the sport "../register.PHP?sport='soccer'" .... really it's "6 vs 6 Soccer" etc
//$logo= Image path (and sometimes resizing information) for the logos
//$sportHeader= Title seen on the form
//$titleHeader= Title seen in the browser window
//$people= Number of player spots allotted on the registration form
//$filter= For the purpose of the query, you should never have to touch this
function declareSportVariables() {
	global $sportID, $logo, $sportHeader, $titleHeader, $people, $update;
	
	if($sportID==1){
			$logo="/Logos/GuelphUltimate.jpg";
			if($update == 0) {
				$titleHeader="Register - Guelph Ultimate";
				$sportHeader="<br>Register as a team for Guelph Ultimate";
			} else {
				$titleHeader="Update - Guelph Ultimate";
				$sportHeader="<br>Update a current team for Guelph Ultimate";
			}
			$people=15;
	}elseif($sportID==2){
		$type="Beach Volleyball";
		$logo="/Logos/WheresTheBeach.jpg";
		if($update == 0) {
			$titleHeader="Register - Where's The Beach Volleyball";
			$sportHeader="<br>Register as a team for Where's The Beach Volleyball";
		} else {
			$titleHeader="Update - Where's The Beach Volleyball";
			$sportHeader="<br>Update a current team for Where's The Beach Volleyball";
		}
		$people=14;
	}elseif($sportID==3){
		$type="Flag Football";
		$logo="/Logos/GuelphFlagFootball.jpg";
		if($update == 0) {
			$titleHeader="Register - Guelph Flag Football";
			$sportHeader="<br>Register as a team for Guelph Flag Football";
		} else {
			$titleHeader="Update - Guelph Flag Football";
			$sportHeader="<br>Update a current team for Guelph Flag Football";
		}
		$people=12;
	}elseif($sportID==4){
		$type="Soccer";
		$logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
		if($update == 0) {
			$titleHeader="Register - Guelph Soccer";
			$sportHeader="<br>Register as a team for Guelph Soccer";
		} else {
			$titleHeader="Update - Guelph Soccer";
			$sportHeader="<br>Update a current team for Guelph Soccer";
		}
		$people=15;
	}
}

function formatTitle($oldTitle) {
	$newTitle = '';
	$dontCapitalizeArray = array('of', 'a', 'an', 'the', 'but', 'or', 'not', 'yet', 'at', 'on', 'in', 'over', 'above', 'under', 'below', 'behind', 'next', 'to', 'beside', 'by', 'among',
								 'between', 'by', 'till', 'since', 'during', 'for', 'throughout', 'to', 'and',);
	
	$teamNameArray = explode(' ', $oldTitle);
	foreach($teamNameArray as $teamName) {
		if(strtolower($teamName) == 'fc') {
			$newTitle .= 'FC ';
		} else if(!in_array(strtolower($teamName), $dontCapitalizeArray)) {
			$newTitle .= ucfirst(strtolower($teamName)).' ';
		} else {
			$newTitle.= strtolower($teamName).' ';
		}
	}
	
	// Capitalize First Letter
	
	$newTitle{0} = strtoupper($newTitle{0});
	
	return substr($newTitle, 0, -1);
}

$nameCheck;

function checkTeamName($teamName, $leagueID, $userID) { // Check to see if team name already taken 
	
	global $teamsTable;
	$check = 0;
	
	$teamName=addslashes(html_entity_decode($teamName, ENT_QUOTES));
	$nameMatch=mysql_query("SELECT team_managed_by_user_id, COUNT(*) as total 
							FROM $teamsTable 
							WHERE (team_name = '$teamName' AND team_league_id = $leagueID AND team_finalized = 1);");
	$matchCheck = mysql_fetch_assoc($nameMatch);

	if ($matchCheck['total']>=1) {
		if ($matchCheck['team_managed_by_user_id']==$userID) {
			echo("<script> alert('You Already Have A Team Registered With This Name!') </script>");
			$check = 1;
	
		}
		else {
			echo("<script> alert('Team Name Taken!') </script>");
			$check = 1;
		}
	}
	return $check;
}

function getSubmittedValues($teamID, $userID) {
	global $player, $leaguesTable, $sportsTable, $teamsTable;
	$team = new Team();
	$team->teamLeagueID = $_POST['leagueID'];
	$team->teamID = $teamID;

	if ($team->teamLeagueID != 0){
		$leagueArray=query("SELECT * FROM $leaguesTable INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
			WHERE league_id = ".$team->teamLeagueID);
		$team->leagueFee = $leagueArray['league_registration_fee'];
		$team->teamLeagueName = getLeagueName($leagueArray['league_name'], $leagueArray['league_full_teams'], 
			dayString($leagueArray['league_day_number']), $leagueArray['league_registration_fee']);
		$team->teamSportName = $leagueArray['sport_name'];
		$team->teamDayNum = $leagueArray['league_day_number'];
	}

	$team->teamComments = $_POST['teamComments'];
	$team->teamName = formatTitle($_POST['teamName']);
	$team->teamIsRegistered = $_POST['isRegistered'];
	$team->teamPayMethod = $_POST['payMethod'];
	$team->aboutUsMethod = $_POST['aboutUsMethod'];
	$team->aboutUsText = $_POST['aboutUsTextBox'];
	
	$check = checkTeamName($team->teamName, $team->teamLeagueID, $userID);
	
	//---Captain's Information (First/Last Name, Email, Phone Number, Sex)---\\
	$player[0] = new Player();
	$player[0]->playerFirstName = $_POST['capFirst'];
	$player[0]->playerLastName = $_POST['capLast'];
	$player[0]->playerEmail = $_POST['capEmail'];
	$player[0]->playerPhone = $_POST['capPhone'];
	$player[0]->playerGender = $_POST['capSex'];

	//For loop to store player data in an array of player objects
	for($a=0, $b=1; $a<=count($_POST['playerFirst']); $a++, $b++){
		$player[$b] = new Player();
		$player[$b]->playerFirstName = $_POST['playerFirst'][$a];
		$player[$b]->playerLastName = $_POST['playerLast'][$a];
		$player[$b]->playerEmail = $_POST['playerEmail'][$a];
		$player[$b]->playerGender = $_POST['playerSex'][$a];
	}
	
	return array('team' => $team, 'check' => $check);
}

function loadDefaultValues($teamLeagueID, $teamID, $teamName, $teamIsRegisteredDB, $teamPayMethod) {
	global $player, $leaguesTable, $playersTable, $userID, $userTable, $sportsTable;
	$team = new Team();
	
	$team->teamLeagueID = $teamLeagueID;
	$team->teamID = $teamID;
	$team->teamComments = '';
	$team->teamIsRegistered = $teamIsRegisteredDB;
	$team->teamName = $teamName;
	$team->teamPayMethod = $teamPayMethod;

	if ($team->teamLeagueID != 0){
		$leagueArray = query("SELECT * FROM $leaguesTable INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
			WHERE league_id = $teamLeagueID");
		$team->LeagueFee = $leagueArray['league_registration_fee'];
		$team->teamLeagueName = $leagueArray['league_name'];
		$team->teamSportName = $leagueArray['sport_name'];
	}

	//Captain variables
	$capArray=query("SELECT * FROM $userTable WHERE user_id = $userID");
	for($i=0;$i<2;$i++) {
		$player[$i] = new Player();
		$player[$i]->playerFirstName = $capArray['user_firstname'];
		$player[$i]->playerLastName = $capArray['user_lastname'];
		$player[$i]->playerEmail = $capArray['user_email'];
		$player[$i]->playerPhone = formatPhoneNumber($capArray['user_phone']);
		$player[$i]->playerGender = $capArray['user_sex'];
	}
	
	if($teamID != 0) {
		//teammates information
		$playerQuery=mysql_query("SELECT * FROM $playersTable WHERE player_team_id = $teamID ORDER BY player_is_captain DESC, player_id ASC");
		$s=1;
		while ($playerArray = mysql_fetch_array($playerQuery)) {
			if($s > 1) { //first 2 players to show up on the form should be the user... why not right? Only the captain gets skipped.
				$player[$s] = new Player();
				$player[$s]->playerFirstName = $playerArray['player_firstname'];
				$player[$s]->playerLastName = $playerArray['player_lastname'];
				$player[$s]->playerEmail = $playerArray['player_email'];
				$player[$s]->playerGender = $playerArray['player_sex'];
			}
			$s++;
		}
	}
	return $team;
}

function getOldTeams($userID, $sportID) {
	global $userID, $teamsTable, $leaguesTable, $seasonsTable, $sportsTable;
	$teamsByUser=mysql_query("SELECT * FROM $teamsTable 
		Inner Join $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id
		Inner Join $sportsTable ON $leaguesTable.league_sport_id = $sportsTable.sport_id
		Inner Join $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		WHERE sport_id = $sportID AND team_managed_by_user_id = $userID AND team_deleted != 1") or die('ERROR gettings old teams - '.mysql_error());
	$i=0;
	while($userTeamArray = mysql_fetch_array($teamsByUser)) {
		$userTeam[$i] = new Team();
		$userTeam[$i]->teamID = $userTeamArray['team_id'];
		$userTeam[$i]->teamName = $userTeamArray['team_name'];
		$userTeam[$i]->teamLeagueName = preg_replace("/\*(.*)\*/",'',$userTeamArray['league_name']).' - '.dayString($userTeamArray['league_day_number']);
		$userTeam[$i]->teamSeasonName = $userTeamArray['season_name'].' '.$userTeamArray['season_year'];
		$userTeam[$i]->teamSportID = $sportID;
		$i++;
	}
	return $userTeam;
}

function getLeagueDD($leagueID, $sportID) {
	global $leaguesTable, $seasonsTable;
	$leagueDropDown = '<option value=0>-- League Name --</option>';
	//leagues in dropdown
	$leaguesQuery=mysql_query("SELECT * FROM $leaguesTable 
			INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
			WHERE $seasonsTable.season_available_registration = 1
			AND league_sport_id = $sportID ORDER BY season_id DESC, league_day_number ASC, league_name ASC") or die("TEST ".mysql_error());
	$pastSeasonID = 0;
	while($league = mysql_fetch_array($leaguesQuery)) {
		if($league['season_id'] != $pastSeasonID) {
			$leagueDropDown.='<option value=0>-- '.$league['season_name'].' --</option>';
		}
		$pastSeasonID = $league['season_id'];
		
		$league['league_id']==$leagueID ? $selectedFilter = 'selected' : $selectedFilter = '';
		$leagueName = getLeagueName($league['league_name'], $league['league_full_teams'], dayString($league['league_day_number']),
			$league['league_registration_fee']);
			
		$leagueDropDown.="<option $selectedFilter value=$league[league_id] fee=$league[league_registration_fee]>$league[season_name] -- $leagueName</option>";
	}
	
	return $leagueDropDown;	
}

function getLeagueName($tempLeagueName, $teamsFull, $leagueDay, $regFee) {
	
	global $isRegisteredDB;
	
	$leagueName = $tempLeagueName;

	if ($teamsFull > 0 && $isRegisteredDB == 0) {
		$leagueName .= '- Full - Waiting List ';
	}
	else {
		$leagueName .= '';
	}
	$leagueName .= "- $leagueDay ($".number_format($regFee, 2).')';
	
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