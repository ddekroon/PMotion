<?php 
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

function loadDatabaseValues($tourneyID) {
	global $tournamentsTable;
	
	if ($tourneyID != 0 ) {
		$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable WHERE tournament_id = $tourneyID") or die('ERROR getting tourney data - '.mysql_error());
		$tourneyArray = mysql_fetch_array($tourneyQuery);
		$tourneyObj = new Tournament();
		$tourneyObj->ID = 0;
		$tourneyObj->tourneyID = $tourneyArray['tournament_id'];
		$tourneyObj->tourneyName = $tourneyArray['tournament_name'];
		$tourneyObj->tourneyLeaguePrices = explode('%', $tourneyArray['tournament_registration_fee']);
		$tourneyObj->tourneyIsCards = $tourneyArray['tournament_is_cards'];
		$tourneyObj->tourneyShow = 1;
		$tourneyObj->tourneyIsLeagues = $tourneyArray['tournament_is_leagues'];
		if($tourneyObj->tourneyIsLeagues == 1) {
			$tourneyObj->tourneyNumLeagues = $tourneyArray['tournament_num_leagues'];
		} else {
			$tourneyObj->tourneyNumLeagues = 1;
		}
		$tourneyObj->tourneyLeagueNames = explode('%', $tourneyArray['tournament_league_names']);
		$tourneyObj->tourneyIsTeams = $tourneyArray['tournament_is_teams'];
		for($i=0;$i<8;$i++) {
			$tourneyObj->tourneyNumTeams[$i] = $tourneyArray['tournament_num_teams'];
		}
		$tourneyObj->tourneyIsPlayers = $tourneyArray['tournament_is_players'];
		for($i=0;$i<8;$i++) {
			$tourneyObj->tourneyNumPlayers[$i] = $tourneyArray['tournament_num_players'];
		}
		$tourneyObj->tourneyIsExtraField = $tourneyArray['tournament_is_extra_field'];
		$tourneyObj->tourneyExtraFieldName = $tourneyArray['tournament_extra_field_name'];
		$tourneyObj->tourneyNumRedCards = explode('%', $tourneyArray['tournament_num_red_cards']);
		$tourneyObj->tourneyNumBlackCards = explode('%', $tourneyArray['tournament_num_black_cards']);
		$tourneyObj->tourneyDateClosed = $tourneyArray['tournament_registration_closed'];
		$tourneyObj->tourneyDatePlayed = $tourneyArray['tournament_game_day'];
		$tourneyObj->tourneyNumDays = $tourneyArray['tournament_num_days'];
		$tourneyObj->tourneyNumRunning = $tourneyArray['tournament_num_running'];
		if($tourneyObj->tourneyIsPlayers == 1|| $tourneyObj->tourneyIsCards == 1) {
			$tourneyObj->tourneyIsFull = explode('%', $tourneyArray['tournament_is_full']);
			$i = 0;
			foreach($tourneyObj->tourneyIsFull as $genderIsFull) {
				$genderTokens = explode('-', $genderIsFull);
				$tourneyObj->tourneyIsFullMale[$i] = $genderTokens[0];
				$tourneyObj->tourneyIsFullFemale[$i++] = $genderTokens[1];
			}
		} else {
			$tourneyObj->tourneyIsFull = explode('%', $tourneyArray['tournament_is_full']);
		}
	}
	return $tourneyObj;
}

function getSubmittedData($tourneyAvailableID) {
	$tourneyObj = new Tournament();
	$tourneyObj->ID = $tourneyAvailableID;
	$tourneyObj->tourneyID = $_POST['tourneyID'];
	$tourneyObj->tourneyName = $_POST['tourneyName'];
	$tourneyObj->tourneyIsCards = $_POST['tourneyIsCards'];
	$tourneyObj->tourneyIsLeagues = $_POST['tourneyIsLeagues'];
	if(($tourneyObj->tourneyNumLeagues = $_POST['tourneyNumLeagues']) == '') {
		$tourneyObj->tourneyNumLeagues = 1;
	}
	$i=0;
	foreach($_POST['tourneyLeagueName'] as $name) {
		$tourneyObj->tourneyLeagueNames[$i] = $name;
		$i++;
	}
	$i=0;
	foreach($_POST['tourneyLeaguePrices'] as $prices) {
		$tourneyObj->tourneyLeaguePrices[$i] = $prices;
		$i++;
	}
	$tourneyObj->tourneyIsTeams = $_POST['tourneyIsTeams'];
	$i=0;
	foreach($_POST['tourneyNumTeams'] as $teams) {
		$tourneyObj->tourneyNumTeams[$i] = $teams;
		$i++;
	}
	$tourneyObj->tourneyIsPlayers = $_POST['tourneyIsPlayers'];
	$i=0;
	foreach($_POST['tourneyNumPlayers'] as $players) {
		$tourneyObj->tourneyNumPlayers[$i] = $players;
		$i++;
	}
	$tourneyObj->tourneyIsExtraField = $_POST['tourneyIsExtraField'];
	$tourneyObj->tourneyExtraFieldName = $_POST['tourneyExtraFieldName'];
	$i=0;
	foreach($_POST['tourneyNumRedCards'] as $redCards) {
		$tourneyObj->tourneyNumRedCards[$i] = $redCards;
		$i++;
	}
	$i=0;
	foreach($_POST['tourneyNumBlackCards'] as $blackCards) {
		$tourneyObj->tourneyNumBlackCards[$i] = $blackCards;
		$i++;
	}
		$i=0;
		if($tourneyObj->tourneyIsPlayers == 1 || $tourneyObj->tourneyIsCards == 1) {
		foreach($_POST['tourneyIsFullMale'] as $isFull) {
			$tourneyObj->tourneyIsFullMale[$i++] = $isFull;
		}
		$i=0;
		foreach($_POST['tourneyIsFullFemale'] as $isFull) {
			$tourneyObj->tourneyIsFullFemale[$i++] = $isFull;
		}
	} else {
		$i=0;
		foreach($_POST['tourneyIsFull'] as $isFull) {
			$tourneyObj->tourneyIsFull[$i++] = $isFull;
		}
	}
	$tourneyObj->tourneyDateOpen = date('Y-n-j', mktime(0, 0, 0, $_POST['dateOpenMonth'], $_POST['dateOpenDay'], $_POST['dateOpenYear']));
	$tourneyObj->tourneyDateClosed = date('Y-n-j', mktime(0, 0, 0, $_POST['dateClosedMonth'], $_POST['dateClosedDay'], $_POST['dateClosedYear']));
	$tourneyObj->tourneyDatePlayed = date('Y-n-j', mktime(0, 0, 0, $_POST['datePlayedMonth'], $_POST['datePlayedDay'], $_POST['datePlayedYear']));
	$tourneyObj->tourneyNumDays = $_POST['tourneyNumDays'];
	$tourneyObj->tourneyNumRunning = $_POST['tourneyNumRunning'];
	return $tourneyObj;
}

function getTournamentDD($tourneyID) {
	global $tournamentsTable;
	$tournamentsDropDown = '';
	
	$tournamentsQuery=mysql_query("SELECT * FROM $tournamentsTable ORDER BY tournament_id") or die("ERROR getting tournaments drop down ".mysql_error());
	while($tournament = mysql_fetch_array($tournamentsQuery)) {
		if($tournament['tournament_id']==$tourneyID){
			$tournamentsDropDown.="<option selected value= $tournament[tournament_id]>$tournament[tournament_name]</option><BR>";
		}else{
			$tournamentsDropDown.="<option value= $tournament[tournament_id]>$tournament[tournament_name]</option>";
		}
	}
	return $tournamentsDropDown;
}

function getAvailTourneyDD($tourneyID) {
	global $tournamentsTable, $tournamentsAvailableTable;
	$tournamentsDropDown = '';
	if($tourneyID != 0) {
		$tourneyFilter = "AND tournament_available_tournament_id = $tourneyID";
	} else {
		$tourneyFilter = '';
	}
	
	$tournamentsQuery=mysql_query("SELECT * FROM $tournamentsAvailableTable WHERE tournament_available_date > NOW() $tourneyFilter
		ORDER BY tournament_available_date") or die("ERROR getting tournaments available drop down ".mysql_error());
	while($tournament = mysql_fetch_array($tournamentsQuery)) {
		$tournamentsDropDown.="<option value= $tournament[tournament_available_id]>$tournament[tournament_available_date]</option>";
	}
	return $tournamentsDropDown;
	
}