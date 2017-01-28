<?php

function updateLeague($tourneyObj) {
	global $tournamentsTable;
	
	$leagueNames = '';
	$teamNums = '';
	$playerNums = '';
	$blackCardNums = '';
	$redCardNums = '';
	$leaguePrices = '';
	$isFull = '';
	
	$tourneyID = $tourneyObj->tourneyID;
	$tourneyName = $tourneyObj->tourneyName;
	$tourneyIsCards = $tourneyObj->tourneyIsCards;
	$tourneyShow = $tourneyObj->tourneyShow;
	$tourneyIsLeagues = $tourneyObj->tourneyIsLeagues;
	if($tourneyIsLeagues == 1) {
		$tourneyNumLeagues = $tourneyObj->tourneyNumLeagues;
	} else {
		$tourneyNumLeagues = 1;
	}
	
	$tourneyIsTeams = $tourneyObj->tourneyIsTeams;
	$tourneyIsPlayers = $tourneyObj->tourneyIsPlayers;
	$tourneyIsExtraField = $tourneyObj->tourneyIsExtraField;
	$tourneyExtraFieldName = mysql_escape_string($tourneyObj->tourneyExtraFieldName);
	
	for($i=0; $i < $tourneyNumLeagues; $i++) {
		$leagueNames.= mysql_escape_string($tourneyObj->tourneyLeagueNames[$i]);
		$leagueNames.=$i<$tourneyNumLeagues-1?'%':'';
		
		$teamNums.=$tourneyObj->tourneyNumTeams[$i];
		$teamNums.=$i<$tourneyNumLeagues-1?'%':'';
		
		$playerNums.=$tourneyObj->tourneyNumPlayers[$i];
		$playerNums.=$i<$tourneyNumLeagues-1?'%':'';
		
		$redCardNums.=$tourneyObj->tourneyNumRedCards[$i];
		$redCardNums.=$i<$tourneyNumLeagues-1?'%':'';
		$blackCardNums.=$tourneyObj->tourneyNumBlackCards[$i];
		$blackCardNums.=$i<$tourneyNumLeagues-1?'%':'';
		
		$leaguePrices.=mysql_escape_string($tourneyObj->tourneyLeaguePrices[$i]);
		$leaguePrices.=$i<$tourneyNumLeagues-1?'%':'';
		if($tourneyIsCards == 1 || $tourneyIsPlayers == 1) {
			$isFull .= $tourneyObj->tourneyIsFullMale[$i].'-'.$tourneyObj->tourneyIsFullFemale[$i];
		} else { 
			$isFull .= $tourneyObj->tourneyIsFull[$i];
		}
		$isFull .= $i<$tourneyNumLeagues-1?'%':'';
	}
	$tourneyDateOpen = $tourneyObj->tourneyDateOpen;
	$tourneyDateClosed = $tourneyObj->tourneyDateClosed;
	$tourneyDatePlayed = $tourneyObj->tourneyDatePlayed;
	$tourneyNumDays = $tourneyObj->tourneyNumDays;
	$tourneyNumRunning = $tourneyObj->tourneyNumRunning;
		
	mysql_query("UPDATE $tournamentsTable SET  tournament_registration_fee = '$leaguePrices', tournament_is_leagues = $tourneyIsLeagues, tournament_num_leagues = $tourneyNumLeagues, 
		tournament_league_names = '$leagueNames', 
		tournament_is_teams = $tourneyIsTeams, tournament_num_teams = '$teamNums', tournament_is_players = $tourneyIsPlayers, tournament_num_players = '$playerNums', 
		tournament_is_extra_field = $tourneyIsExtraField, tournament_extra_field_name = '$tourneyExtraFieldName', tournament_is_cards = $tourneyIsCards,
		tournament_num_red_cards = '$redCardNums', tournament_num_black_cards = '$blackCardNums', 
		tournament_registration_closed = '$tourneyDateClosed', tournament_game_day = '$tourneyDatePlayed', tournament_num_days = $tourneyNumDays, 
		tournament_num_running = $tourneyNumRunning, tournament_is_full = '$isFull' WHERE tournament_id = $tourneyID") or die ('Error updating tournament - '.mysql_error());	
}