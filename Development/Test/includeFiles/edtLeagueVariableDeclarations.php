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
        
if(($tourneyID = $_GET['tournamentID']) == '') {
	$tourneyID = 0;
}

function getDefaultInfo($tourneyID) {
	global $tournamentsTable, $sportsTable;
	
	if($tourneyID == 0) {
		return;
	}
	$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable 
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $tournamentsTable.tournament_sport_id 
		WHERE tournament_id = $tourneyID") or die('ERROR getting tourney info - '.mysql_error());
	$tourneyArray = mysql_fetch_array($tourneyQuery);
	$tourneyObj = new Tournament();
	$tourneyObj->tourneyID = $tourneyArray['tournament_id'];
	$tourneyObj->tourneyName = $tourneyArray['tournament_name'];
	$tourneyObj->tourneyIsCards = $tourneyArray['tournament_is_cards'];
	$tourneyObj->tourneyIsLeagues = $tourneyArray['tournament_is_leagues'];
	$tourneyObj->tourneyNumLeagues = $tourneyArray['tournament_num_leagues'];
	$tourneyObj->tourneyLeagueNames = explode('%', $tourneyArray['tournament_league_names']);
	$tourneyObj->tourneyIsTeams = $tourneyArray['tournament_is_teams'];
	$tourneyObj->tourneyNumTeams = explode('%', $tourneyArray['tournament_num_teams']);
	$tourneyObj->tourneyIsPlayers = $tourneyArray['tournament_is_players'];
	$tourneyObj->tourneyNumPlayers = explode('%', $tourneyArray['tournament_num_players']);
	$tourneyObj->tourneyIsExtraField = $tourneyArray['tournament_is_extra_field'];
	$tourneyObj->tourneyExtraFieldName = $tourneyArray['tournament_extra_field_name'];
	$tourneyObj->tourneyNumRedCards = explode('%', $tourneyArray['tournament_num_red_cards']);
	$tourneyObj->tourneyNumBlackCards = explode('%', $tourneyArray['tournament_num_black_cards']);
	$tourneyObj->tourneyDateOpen = $tourneyArray['tournament_registration_open'];
	$tourneyObj->tourneyDateClosed = $tourneyArray['tournament_registration_closed'];
	$tourneyObj->tourneyDatePlayed = $tourneyArray['tournament_game_day'];
	$tourneyObj->tourneyNumDays = $tourneyArray['tournament_num_days'];
	$tourneyObj->tourneyNumRunning = $tourneyArray['tournament_num_running'];
	if($tourneyArray['tournament_sport_id'] != 0) {
		$tourneyObj->tourneySportName = $tourneyArray['sport_name'];
	} else {
		$tourneyObj->tourneySportName = '';
	}
	return $tourneyObj;
	
}

function getTeamsInfo($tourneyID, $isLeagues, $numLeagues, $numTeamsPerLeague) {
	global $tournamentTeamsTable, $tournamentsTable, $tournamentPlayersTable;
	if($tourneyID == 0) {
		return;
	}
	$isLeagues == 1?$numLeagues = $numLeagues:$numLeagues = 1;
	for($j=0 ; $j < $numLeagues;$j++) {
		if($isLeagues == 1) {
			$orderClause = 'tournament_team_num_in_league ASC, tournament_team_id ASC';
			$leagueFilter = "AND tournament_team_num_in_league > 0 AND tournament_team_league_id = $j";
		} else {
			$orderClause = 'tournament_team_num_in_tournament ASC, tournament_team_id ASC';
			$leagueFilter = "AND tournament_team_num_in_tournament > 0";
		}
		for($i=1;$i<=$numTeamsPerLeague[$j];$i++) {
			$teams[$j][$i] = new Team();
			$teams[$j][$i]->teamRatingDropDown = getRatingDD(0);
			$teams[$j][$i]->teamNumsDropDown = getTeamNumDD($i, $numTeamsPerLeague[$j]);
			$teams[$j][$i]->teamPaid = 0;
		}
		
		$teamsQuery = mysql_query("SELECT * FROM $tournamentTeamsTable 
			INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = $tournamentTeamsTable.tournament_team_tournament_id
			INNER JOIN $tournamentPlayersTable ON $tournamentPlayersTable.tournament_player_team_id = $tournamentTeamsTable.tournament_team_id
			WHERE tournament_team_tournament_id = $tourneyID AND tournament_team_tournament_num_running = tournament_num_running $leagueFilter ORDER BY $orderClause") 
			or die('ERROR getting tourney teams - '.mysql_error());
		$lastTeamID = 0;
		$teamNum = 0;
		while($teamArray = mysql_fetch_array($teamsQuery)) {
			if($teamArray['tournament_team_id'] != $lastTeamID) {
				$teamNum++;
				$teams[$j][$teamNum]->teamID = $teamArray['tournament_team_id'];
				$teams[$j][$teamNum]->teamTournamentID = $teamArray['tournament_team_tournament_id'];
				$teams[$j][$teamNum]->teamName = $teamArray['tournament_team_name'];
				$teams[$j][$teamNum]->teamRating = $teamArray['tournament_team_rating'];
				$teams[$j][$teamNum]->teamRatingDropDown = getRatingDD($teams[$j][$teamNum]->teamRating);
				$teams[$j][$teamNum]->teamPaid = $teamArray['tournament_team_paid'];
				$teams[$j][$teamNum]->teamLeagueID = $teamArray['tournament_team_league_id'];
				$teams[$j][$teamNum]->teamNumInLeague = $teamArray['tournament_team_num_in_league'];
				$teams[$j][$teamNum]->teamNumInTournament = $teamArray['tournament_team_num_in_tournament'];
				$teams[$j][$teamNum]->teamNote = $teamArray['tournament_team_note'];
				$teams[$j][$teamNum]->teamCaptainName = $teamArray['tournament_player_firstname'].' '.$teamArray['tournament_player_lastname'];
				$teams[$j][$teamNum]->teamCaptainPhone = format_phone($teamArray['tournament_player_phone']);
				$teams[$j][$teamNum]->teamCaptainEmail = $teamArray['tournament_player_email'];
				$teams[$j][$teamNum]->teamNumsDropDown = getTeamNumDD($teamNum, $numTeamsPerLeague[$j]);
				$teams[$j][$teamNum]->teamExtraField = $teamArray['tournament_team_extra_field'];
				$teams[$j][$teamNum]->teamIsWaiting = $teamArray['tournament_team_is_waiting'];
				$lastTeamID = $teamArray['tournament_team_id'];
				
			}
		}
	}
	return $teams;
}

function format_phone($number) {
	return sprintf("%s-%s-%s",
		  substr($number, 0, 3),
		  substr($number, 3, 3),
		  substr($number, 6));	
}

function getPlayerCardsInfo($tourneyID, $isLeagues, $numLeagues, $numCardsBlack, $numCardsRed) {
	global $tournamentPlayersTable, $tournamentsTable;

	if($tourneyID == 0) {
		return;
	}
	
	$isLeagues == 1?$numLeagues = $numLeagues:$numLeagues = 1;
	for($j=0 ; $j < $numLeagues;$j++) {
		$players[$j] = array();
		if($isLeagues == 1) {
			$orderClause = 'tournament_player_card ASC';
			$leagueFilter = "AND tournament_player_league_id = $j";
		} else {
			$orderClause = 'tournament_player_card ASC';
			$leagueFilter = '';
		}
		
		for($k=0;$k<2;$k++) {
			$players[$j][$k] = array();
			if($k == 0) {
				$cardColourFilter='AND tournament_player_card > 100 && tournament_player_card < 300';
			} else {
				$cardColourFilter='AND tournament_player_card > 300 && tournament_player_card < 500';
			}
			
			$playersQuery = mysql_query("SELECT * FROM $tournamentPlayersTable 
				INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id 
				WHERE tournament_id = $tourneyID AND tournament_player_tournament_num_running = tournament_num_running $leagueFilter $cardColourFilter ORDER BY $orderClause") 
				or die('ERROR getting tourney players - '.mysql_error());
			while($playerArray = mysql_fetch_array($playersQuery)) {
				$playerNum = $playerArray['tournament_player_card'];
				$players[$j][$k][$playerNum]->playerID = $playerArray['tournament_player_id'];
				$players[$j][$k][$playerNum]->playerTournamentID = $playerArray['tournament_player_tournament_id'];
				$players[$j][$k][$playerNum]->playerFirstName = $playerArray['tournament_player_firstname'];
				$players[$j][$k][$playerNum]->playerLastName = $playerArray['tournament_player_lastname'];
				$players[$j][$k][$playerNum]->playerEmail = $playerArray['tournament_player_email'];
				$players[$j][$k][$playerNum]->playerPaid = $playerArray['tournament_player_paid'];
				$players[$j][$k][$playerNum]->playerLeagueID = $playerArray['tournament_player_league_id'];
				$players[$j][$k][$playerNum]->playerNumInLeague = $playerArray['tournament_player_num_in_league'];
				$players[$j][$k][$playerNum]->playerNumInTournament = $playerArray['tournament_player_num_in_tournament'];
				$players[$j][$k][$playerNum]->playerNote = $playerArray['tournament_player_note'];
				$players[$j][$k][$playerNum]->playerCard = $playerArray['tournament_player_card'];
				$players[$j][$k][$playerNum]->playerPhone = format_phone($playerArray['tournament_player_phone']);
				$players[$j][$k][$playerNum]->playerCardsDropDown = getPlayerCardDD($players[$j][$k][$playerNum]->playerCard, $k==0?$numCardsBlack[$j]:$numCardsRed[$j]);
				$players[$j][$k][$playerNum]->playerIsWaiting = $playerArray['tournament_player_is_waiting'];
			}
		}
	}
	return $players;
}

function getPlayersInfo($tourneyID, $isLeagues, $numLeagues, $numPlayersPerLeague) {
	global $tournamentPlayersTable, $tournamentsTable;
	
	if($tourneyID == 0) {
		return;
	}
	
	$isLeagues == 1?$numLeagues = $numLeagues:$numLeagues = 1;
	for($j=0 ; $j < $numLeagues;$j++) {
		if($isLeagues == 1) {
			$orderClause = 'tournament_player_num_in_league ASC';
			$leagueFilter = "AND tournament_player_num_in_league > 0 AND tournament_player_league_id = $j";
		} else {
			$orderClause = 'tournament_player_num_in_tournament ASC';
			$leagueFilter = 'AND tournament_player_num_in_tournament > 0';
		}
		for($i=1;$i<=$numPlayersDivision;$i++) {
			$players[$j][$i] = new Player();
			$players[$j][$i]->playerNumDropDown = getPlayerNumDD($i, $numPlayersDivision);
			$players[$j][$i]->teamPaid = 0;
		}
			
		$playersQuery = mysql_query("SELECT * FROM $tournamentPlayersTable 
			INNER JOIN $tournamentsTable ON $tournamentsTable.tournament_id = $tournamentPlayersTable.tournament_player_tournament_id 
			WHERE tournament_id = $tourneyID AND tournament_player_tournament_num_running = tournament_num_running $cardColourFilter ORDER BY $orderClause") 
			or die('ERROR getting tourney players - '.mysql_error());
		while($playerArray = mysql_fetch_array($playersQuery)) {
			$playerNum = $playerArray['tournament_player_card'];
			$players[$j][$playerNum]->playerID = $playerArray['tournament_player_id'];
			$players[$j][$playerNum]->playerTournamentID = $playerArray['tournament_player_tournamnet_id'];
			$players[$j][$playerNum]->playerFirstName = $playerArray['tournament_player_firstname'];
			$players[$j][$playerNum]->playerLastName = $playerArray['tournament_player_lastname'];
			$players[$j][$playerNum]->playerEmail = $playerArray['tournament_player_email'];
			$players[$j][$playerNum]->playerPaid = $playerArray['tournament_player_paid'];
			$players[$j][$playerNum]->playerLeagueID = $playerArray['tournament_player_league_id'];
			$players[$j][$playerNum]->playerNumInLeague = $playerArray['tournament_player_num_in_league'];
			$players[$j][$playerNum]->playerNumInTournament = $playerArray['tournament_player_num_in_tournament'];
			$players[$j][$playerNum]->playerNote = $playerArray['tournament_player_note'];
			$players[$j][$playerNum]->playerCard = $playerArray['tournament_player_card'];
			$players[$j][$playerNum]->playerPhone = format_phone($playerArray['tournament_player_phone']);
			$players[$j][$playerNum]->playerNumDropDown = getPlayerNumDD($isLeagues==1?$players[$j][$playerNum]->playerNumInLeague:$players[$j][$playerNum]->playerNumInTournament, $numPlayersPerLeague[$j]);
			$players[$j][$playerNum]->playerIsWaiting = $playerArray['tournament_player_is_waiting'];
		}
	}
	return $players;
}

function getNewPlayerInfo() {
	$player = new Player();
	$player->playerFirstName = $_POST['addPlayerFirstName'];
	$player->playerLastName = $_POST['addPlayerLastName'];
	$player->playerPaid = $_POST['addPlayerPaid'];
	$player->playerNote = $_POST['addPlayerNote'];
	$player->playerCard = $_POST['addPlayerCardDD'];
	$player->playerLeagueID = $_POST['addPlayerLeagueID']; 
	return $player;
}

function getTourneyLeaguesDD($tourneyObj) {
	$leaguesDropDown ='<option value=10000>--League--</option>';
	//leagues in dropdown
	$tourneyObj->tourneyIsLeagues == 1?$numLeagues = $tourneyObj->tourneyNumLeagues:1; 
	for($i=0;$i<$numLeagues;$i++) {
		if($tourneyObj->tourneyIsLeagues == 0){
			$leaguesDropDown.="<option value=$i>".$tourneyObj->tourneyName.'</option>';
		}else{
			$leaguesDropDown.="<option value=$i>".$tourneyObj->tourneyLeagueNames[$i].'</option>';
		}
	}
	return $leaguesDropDown;
}

function getTournamentDD($tourneyID) {
	global $tournamentsTable;
	$tournamentDropDown ='';
	//leagues in dropdown
	$tournamentsQuery=mysql_query("SELECT * FROM $tournamentsTable ORDER BY tournament_id ASC") or die("ERROR getting tourneys DD ".mysql_error());
	while($tournament = mysql_fetch_array($tournamentsQuery)) {
		if($tournament['tournament_id'] == $tourneyID){
			$tournamentDropDown.="<option selected value= $tournament[tournament_id]>$tournament[tournament_name]</option><BR>";
		}else{
			$tournamentDropDown.="<option value= $tournament[tournament_id]>$tournament[tournament_name]</option>";
		}
	}
	return $tournamentDropDown;
}

function getRatingDD($teamRating) {
	$ratingDD = '<select name="teamRatingDD[]"><option value=0>N/A</option>';
	for($i=10;$i > 0; $i--) {
		if($i==$teamRating){
			$ratingDD.="<option selected value=$i>$i</option>";
		}else{
			$ratingDD.="<option value=$i>$i</option>";
		}
	}
	$ratingDD .= '</select>';
	
	return $ratingDD;	
	
}

function getTeamNumDD($teamNumInLeague, $numTeams) {
	
	$teamNumDD = '<select name="teamNumDD[]"><option value=0>N\A</option>';
	for($i=1;$i <= $numTeams;$i++) {
		if($i==$teamNumInLeague){
			$teamNumDD.="<option selected value=$i>$i</option>";
		}else{
			$teamNumDD.="<option value=$i>$i</option>";
		}
	}
	$teamNumDD .= '</select>';
	
	return $teamNumDD;	
} 

function getPlayerCardDD($playerCard, $numCards) {

	if($playerCard - 300 < 0) {
		$firstCardValue = 101;
		$secondCardValue = 201;
	} else {
		$firstCardValue = 301;
		$secondCardValue = 401;
	}
	
	$playerCardDD = '';
	for($j=$firstCardValue;$j <= $firstCardValue + $numCards;$j++) {
		if($j == $playerCard){
			$playerCardDD.="<option selected value=$j>".getCardString($j).'</option>';
		}else{
			$playerCardDD.="<option value=$j>".getCardString($j).'</option>';
		}
	}
	for($j=$secondCardValue;$j <= $secondCardValue + $numCards;$j++) {
		if($j == $playerCard){
			$playerCardDD.="<option selected value=$j>".getCardString($j).'</option>';
		}else{
			$playerCardDD.="<option value=$j>".getCardString($j).'</option>';
		}
	}
	return $playerCardDD;
}

function getplayerNumDD($playerNum, $numPlayers) {
	
	$playerNumDD = '<select name="playerNumDD[]"><option value=0>N\A</option>';
	for($j = 1; $j <= $numPlayers; $j++) {
		if($j == $playerNum){
			$playerNumDD.="<option selected value=$j>$j</option>";
		}else{
			$playerNumDD.="<option value=$j>$j</option>";
		}
	}
	$playerNumDD .= '</select>';
	return $playerNumDD;
}

function getCardString($playerCard) {
	for($i=0;$i<4;$i++) {
		$playerCard = $playerCard - 100;
		if($playerCard < 100) {
			if($i == 0) {
				$cardSuit = 'S';
			} else if($i == 1) {
				$cardSuit = 'C';
			} else if($i == 2) {
				$cardSuit = 'H';
			} else if($i == 3) {
				$cardSuit = 'D';
			}
			$i=500;
		}
	}
	if($playerCard < 11) {
		return $playerCard.$cardSuit;
	} else if($playerCard == 11) {
		return 'J'.$cardSuit;
	} else if($playerCard == 12) {
		return 'Q'.$cardSuit;
	} else if($playerCard == 13) {
		return 'K'.$cardSuit;
	} else {
		$cardValue = $playerCard - 3;
		return $cardValue.$cardSuit;
	}
}?>