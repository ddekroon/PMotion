<?php

//naturally uncommented... sorts whether the teams tied
function sortNextPosition($curTeamNum, $teamArray, $curPosition, $numTeams) {
	global $isTied;
	
	if($curTeamNum == 0) {
		if($teamArray[$curTeamNum]->teamFinalPosition==$teamArray[$curTeamNum+1]->teamFinalPosition) {
			$isTied = 1;
			return 1;
		} else {
			$isTied = 0;
			return 1;
		}
	} else if($curTeamNum != $numTeams) {
		if($teamArray[$curTeamNum]->teamFinalPosition == $teamArray[$curTeamNum-1]->teamFinalPosition) { 
			$isTied = 1;
			return $curPosition;
		} else if ($teamArray[$curTeamNum]->teamFinalPosition == $teamArray[$curTeamNum+1]->teamFinalPosition) {
			$isTied = 1;
			$i=0;
			do {
				$i++;
			} while($teamArray[$curTeamNum-$i]->teamFinalPosition == $teamArray[$curTeamNum-$i-1]->teamFinalPosition && $teamArray[$curTeamNum-$i-1]->teamFinalPosition != '');
			return $curPosition + $i;
		} else {
			$isTied = 0;
			$i=0;
			do {
				$i++;
			} while($teamArray[$curTeamNum-$i]->teamFinalPosition == $teamArray[$curTeamNum-$i-1]->teamFinalPosition && $teamArray[$curTeamNum-$i-1]->teamFinalPosition != '');
			return $curPosition + $i;
		}
	} else {
		if($teamArray[$curTeamNum]->teamFinalPosition == $teamArray[$curTeamNum-1]->teamFinalPosition) {
			$isTied = 1;
			return $curPosition;
		} else {
			$isTied = 0;
			return $curPosition +1;
		}
	}
}



function sortNextSpiritPosition($curTeamNum, $teamArray, $curPosition, $numTeams) {
	global $isTied;
	
	if($curTeamNum == 0) {
		if($teamArray[$curTeamNum]->teamFinalSpirit==$teamArray[$curTeamNum+1]->teamFinalSpirit) {
			$isTied = 1;
			return 1;
		} else {
			$isTied = 0;
			return 1;
		}
	} else if($curTeamNum != $numTeams) {
		if($teamArray[$curTeamNum]->teamFinalSpirit == $teamArray[$curTeamNum-1]->teamFinalSpirit || $teamArray[$curTeamNum]->teamFinalSpirit == $teamArray[$curTeamNum+1]->teamFinalSpirit) {
			$isTied = 1;
			if($teamArray[$curTeamNum]->teamFinalSpirit == $teamArray[$curTeamNum-1]->teamFinalSpirit) {
				return $curPosition;
			} else {
				return $curPosition+1;
			}
		} else {
			$isTied = 0;
			return $curPosition +1;
		}
	} else {
		if($teamArray[$curTeamNum]->teamFinalSpirit == $teamArray[$curTeamNum-1]->teamFinalSpirit) {
			$isTied = 1;
			return $curPosition;
		} else {
			$isTied = 0;
			return $curPosition +1;
		}
	}
}

function getHeadToHead($teamOne, $teamTwo) {
	global $scoreSubmissionsTable;
	$submissionsQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamOne 
		AND score_submission_opp_team_id = $teamTwo AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$winLoss = 0;
	while($submission = mysql_fetch_array($submissionsQuery)) {
		if($submission['score_submission_result'] == 2) {
			$winLoss++;
		} else if ($submission['score_submission_result'] == 1) {
			$winLoss--;
		} 
	}
	return $winLoss;
}

function getCommonPlusMinus($teamOne, $teamTwo) {
	global $scoreSubmissionsTable;
	$oppTeamOne = array();
	$teamOneFor = array();
	$teamOneAgainst = array();
	$plusMinusOne = 0;
	$plusMinueTwo = 0;
	$submissionsQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamOne 
		AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	$count = 0;
	while($submission = mysql_fetch_array($submissionsQuery)) {
		$oppTeamOne[$count] = $submission['score_submission_opp_team_id'];
		$teamOneFor[$count] = $submission['score_submission_score_us'];
		$teamOneAgainst[$count++] = $submission['score_submission_score_them'];
	}
		
	$submissionsQuery = mysql_query("SELECT * FROM $scoreSubmissionsTable WHERE score_submission_team_id = $teamTwo
		AND score_submission_ignored = 0 ORDER BY score_submission_id ASC") 
		or die('ERROR getting score submissions - '.mysql_error());
	
	while($submission = mysql_fetch_array($submissionsQuery)) {
		for($i = 0; $i < count($oppTeamOne); $i++) {
			if($submission['score_submission_opp_team_id'] == $oppTeamOne[$i]) {
				$plusMinusOne += $submission['score_submission_score_us'] - $submission['score_submission_score_them'];
				$plusMinusTwo += $teamOneFor[$i] - $teamOneAgainst[$i];
			}
		}
	}
	return $plusMinusOne - $plusMinusTwo;
}

function comparePoints($a, $b) {
    if ($a->teamPoints == $b->teamPoints) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			$headToHead = getHeadToHead($a->teamID, $b->teamID);
			if($headToHead == 0) {
				return getCommonPlusMinus($a->teamID, $b->teamID);
			} else {
				return $headToHead;
			}
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->teamPoints > $b->teamPoints) ? -1 : 1;
}

function comparePercent($a, $b) {
	global $scoreSubmissionsTable;
	if ($a->teamWinPercent == $b->teamWinPercent) {
        if($a->teamSpiritAverage == $b->teamSpiritAverage) {
			$headToHead = getHeadToHead($a->teamID, $b->teamID);
			if($headToHead == 0) {
				return getCommonPlusMinus($a->teamID, $b->teamID);
			} else {
				return $headToHead;
			}
		} else {
			return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
		}	
    }
    return ($a->teamWinPercent > $b->teamWinPercent) ? -1 : 1;
}

function comparePosition($a, $b) {
    if ($a->teamFinalPosition == $b->teamFinalPosition) {
        return 0;
    }
    return ($a->teamFinalPosition < $b->teamFinalPosition) ? -1 : 1;
}

function compareSpirit($a, $b) {
    if ($a->teamSpiritAverage == $b->teamSpiritAverage) {
        return 0;
    }
    return ($a->teamSpiritAverage > $b->teamSpiritAverage) ? -1 : 1;
}