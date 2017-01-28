<?php 

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($prizeTime = $_GET['prizeTime']) == '') {
	$prizeTime = 0;
}
if(($sortBy = $_GET['sortBy']) == '') {
	$sortBy = '';
}

function getPrizeTimesDD($prizeTime) {
	$prizeTimeDD = '';
	$file = fopen('prizeTimes.txt', "r");
	if($file) {
		while (($buffer = fgets($file, 4096)) !== false) {
			$lineTokens = explode('-', trim($buffer));
			if($lineTokens[0] == $prizeTime) {
				$prizeTimeDD.="<option selected value='$lineTokens[0]'>$lineTokens[1]</option>";
			} else {
				$prizeTimeDD.="<option value='$lineTokens[0]'>$lineTokens[1]</option>";
			}
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	return $prizeTimeDD;
}

function getPrizeDD($teamID) {
	global $prizesTable;
	$optionsArray = array();
	$linesArray = array();
	$optionsDD = array();
	$teamQuery = mysql_query("SELECT * FROM $prizesTable WHERE prize_team_id = $teamID") or die(mysql_error());
	$teamArray = mysql_fetch_array($teamQuery);
	$prizeDescription = $teamArray['prize_description'];
	$file = fopen('prizesAvailable.txt', "r");
	if($file) {
		$counter = 0;
		while (($buffer = fgets($file, 4096)) !== false) {
			$linesArray[$counter] = trim($buffer);
			$lineTokens = explode('-', trim($buffer));
			$optionsArray[$counter] = $lineTokens[0].' - $'.$lineTokens[1];
			$counter++;
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	for($i=0;$i < count($optionsArray); $i++) {
		if($linesArray[$i] == $prizeDescription) {
			$optionsDD.='<option selected value="'.$linesArray[$i].'">'.$optionsArray[$i].'</option>';
		} else {
			$optionsDD.='<option value="'.$linesArray[$i].'">'.$optionsArray[$i].'</option>';
		}
	}
	return $optionsDD;
}

function getPrizeString($teamID) {
	global $prizesTable;
	$teamQuery = mysql_query("SELECT * FROM $prizesTable WHERE prize_team_id = $teamID") or die(mysql_error());
	$teamArray = mysql_fetch_array($teamQuery);
	$lineTokens = explode('-', $teamArray['prize_description']);
	$prizeDescription = $lineTokens[0].' - $'.$lineTokens[1];
	return $prizeDescription;
}