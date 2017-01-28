<?php

function hideSpirit($leagueObj){

	if($leagueObj->league_available_score_reporter = 0) { //if not in score reporter show spirits, at that point who cares
		return false;
	}

	$dayOfWeekNum = date('N'); //Number representing day of week... Mon=1, Tues=2..Sun=7
	$timeOfDay = date('G');   //24 Hour representation of time: 0-23

	$dateHide = $leagueObj->league_day_number;
	$dateShow = $dateHide + $leagueObj->league_num_days_spirit_hidden;
	if($dateShow > 7) {
		$dateShow = $dateShow % 7; //if games are sunday show date is gonna be greater than 7.
	}

	if($dayOfWeekNum == $dateHide){      //If it's the day of the game, hide spirit
		if($timeOfDay >= $leagueObj->league_hide_spirit_hour){
			return true;
		}else{
			return false;
		}
	}//end if

	if(($dayOfWeekNum == $dateShow)){      //If it's 2 days after the game, show spirit
		print($leagueObj->league_show_spirit_hour);
		if($timeOfDay >= $leagueObj->league_show_spirit_hour){
			print(0);
			return false;
		}else{
			print(1);
			return true;
		}
	}//end if
print("hi");
	if(($dayOfWeekNum > $dateHide && $dayOfWeekNum < $dateShow) || ($dayOfWeekNum == 1 && $dateHide == 7)){
		//If it's anytime inbetween, hide spirit
		//also included for sunday... if it's supposed to be hidden on sunday and it's monday, hide spirit
		return true;
	}else{
		return false;
	}

}//end function

//naturally uncommented... sorts whether the teams tied
function checkTied($teams, $curNum, $numTeams, $index) {
	if($curNum == 0 && $curNum == $numTeams - 1) { //if there is only one team for some reason
		$isTied = 0;
	} else if($curNum == 0 && $curNum != $numTeams - 1) { //first team in multi team league
		if($teams[$curNum]->$index == $teams[$curNum + 1]->$index) {
			$isTied = 1;
		} else {
			$isTied = 0;
		}
	} else if($curNum > 0 && $curNum != $numTeams - 1) { //some middle team in multi team league
		if($teams[$curNum - 1]->$index == $teams[$curNum]->$index || $teams[$curNum]->$index == $teams[$curNum + 1]->$index) {
			$isTied = 1;
		} else {
			$isTied = 0;
		}
	} else { //by default last team in multi team league
		if($teams[$curNum - 1]->$index == $teams[$curNum]->$index) {
			$isTied = 1;
		} else {
			$isTied = 0;
		}
	}
	return $isTied;
}