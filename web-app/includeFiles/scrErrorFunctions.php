<?php
function checkForErrors() {
	global $oppTeamID, $scoreUs, $scoreThem, $gameResults, $spiritScores, $matchComments, $submitName, $submitEmail;
	global $matches, $games, $teamID, $leagueID, $isPlayoffs;
	$error = '';
	$gameNum = 0; 
	$matchNum = 0;
	if ($leagueID == 0) {
		$error.='* Please enter a league';
	}
	if ($teamID == 0) {
		$error.='* Please enter a team';
	}
	
	if($isPlayoffs == 0) {
		
		for ($i=0;$i<$matches;$i++) {
			$matchNum++;
			if ($oppTeamID[$i] == 0) {
				$error.='* Please enter a team for match '.$matchNum.'<br />';
			}
			if ($spiritScores[$i] && $spiritScores[$i] < 3.5 && strlen($matchComments[$i]) < 2 && $oppTeamID[$i] != 1) {
				$error.='* Please enter the reason for spirit being so low in match '.$matchNum.'<br />';
			}
			if ($oppTeamID[$i] == $teamID) {
				$error.='* Please select a different opposing team for game '.$matchNum.'<br />';
			}
			if ($oppTeamID[$i] > 2) {
				for ($j=0;$j<$games;$j++) {
					if (!$gameResults[$gameNum]) {
						$error.='* Please enter a result for game '.$gameNum.'<br />';
					}
					$gameNum++;
				}
			}
		}
		
		if ($matches == 2) {
			if ($oppTeamID[0] == $oppTeamID[1]) {
				$error.='* Please select two different opposing teams<br />';
			}
		}
	}

	if (strlen($submitName) < 2) {
    	$error.='* Please enter your name<br />';
    } else {
		if (!isValid($submitName)) {
			$error.='* Please enter a valid name<br />';
		}
	}
	
	return $error;
}

function isValid($str) {
   	return !preg_match("/[^A-Za-z0-9\'\- @\.]/", $str);
} //end of is Valid function
	
?>