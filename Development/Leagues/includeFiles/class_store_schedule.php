<?php

class StoreScheduleVars {
	public $venueTeamNumber, $matchTimes, $newGameTime, $teamInMatchNum, $timeSlotNum, $nodeIsTeam, $nodeTeamNum,
		$teamNamesArray, $teamIDArray, $practiseRow, $curVenueID, $usedVenues, $numTeamsFound, $noteTop, $noteBottom,
		$potentialTeamNum, $numColumns, $venueStars, $numVenuesByDate, $inlineNotes, $numVenues, $fileName,
		$nodeFullText, $nodeString, $weekNum, $curDateID, $venueFound, $getGames,
		$leaguePlayoffWeek, $isTeam, $teamToBeStored, $gamesThisWeek;
		
	public function __construct() {
		$curDateID = 0;
		$curVenueID = 0;
		$fileName = '';
		$gamesThisWeek = array();
		$getGames = 0;
		$inlineNotes = array();
		$inlineNotes[0] = '';
		$isTeam = 0;
		$lastNode = '';
		$leagueID = 0;
		$leaguePlayoffWeek = 0;
		$matchTimes = array(0, 0, 0,);
		$newGameTime = 0;
		$nodeFullText = '';
		$nodeIsTeam = 0;
		$nodeString = '';
		$nodeTeamNum = 0;
		$noteTop = '';
		$noteBottom = '';
		$numColumns = 0;
		$numTeamsFound = 0;
		$numVenues = 0;
		$numVenuesByDate = 0;
		$potentialTeamNum = '';
		$practiseRow = 0;
		$seasonID = 0;
		$sportID = 0;
		$teamIDArray = array();
		$teamInMatchNum = 0;
		$teamNamesArray = array();
		$teamNumInLeague = 0;
		$teamNums = array();
		$teamOpps = array();
		$teamToBeStored = array();
		$timeSlotNum = 0;
		$usedVenueIDs = array();
		$usedVenues = array(array(0, 0));
		$venueFound = 0;
		$venueID = array();
		$venueName = array();
		$venueRow = 0;
		$venueShortName = array();
		$venueStars = array('', '', '', '', '', '',);
		$venueTeamNumber = 0;
		$weekNum = 0;
	}
	
	public function declareTeamOpps() {
		$count = 1;
		foreach($this->teamNums as $team) {
			$this->teamOpps[$count] = array();
			for($i = 1; $i <= count($this->teamNums); $i++) {
				$this->teamOpps[$count][$i] = 0;
			}
			$count++;
		}	
	}
	
	public function printDistribution() {
		print '<table class="showDistribution"><tr><th>Tm</th>';
		for($i = 1; $i <= count($this->teamOpps[1]) + 1; $i++) {
			if($i < count($this->teamOpps[1])) {
				print '<th>'.$i.'</th>';
			} else if($i == count($this->teamOpps[1])) {
				print $this->isPractise == 1?'<th>P</th>':"<th>$i</th>";
			} else if($i > count($this->teamOpps[1])) {
				print '<th>X</th>';
			}
		}
		print '</tr>';
		$i = 1;
		for($i = 1; $i <= count($this->teamOpps[1]); $i++) {
			$i % 2 == 0?$colourFilter= 'style="background-color:#aaa;"':$colourFilter = '';
			print '<tr><th '.$colourFilter.'>';
			if($i < count($this->teamOpps[1])) {
				print $i;
			} else if($i == count($this->teamOpps[1])){
				print $this->isPractise == 1?'P':"$i";
			}
			print '</th>';
			for($j = 1; $j <= count($this->teamOpps[1]) + 1; $j++) {
				if($i % 2 != 0 && $j <= count($this->teamOpps[1])) {
					$j % 2 == 0?$colourFilter= 'style="background-color:#ddd;"':$colourFilter = '';
				}
				if($i == $j) {
					print '<td '.$colourFilter.'>x</td>';
				} else if($j <= count($this->teamOpps[1])) {
					print '<td '.$colourFilter.'>'.$this->teamOpps[$i][$j].'</td>';
				} else if($j > count($this->teamOpps[1])) {
					if($i == count($this->teamOpps[1])) {
						print $this->isPractise == 1?'<th>P</th>':"<th>$i</th>";
					} else {
						print '<th '.$colourFilter.'>'.$i.'</th>';
					}
				}
			}
			print '</tr>';
		}
		print '<tr><th>X</th>';
		for($i = 1; $i <= count($this->teamOpps[1]) + 1; $i++) {
			if($i < count($this->teamOpps[1])) {
				print '<th>'.$i.'</th>';
			} else if($i == count($this->teamOpps[1])) {
				print $this->isPractise == 1?'<th>P</th>':"<th>$i</th>";
			} else if($i > count($this->teamOpps[1])) {
				print '<th>X</th>';
			}
		}
		print '</tr></table>';
	}
}