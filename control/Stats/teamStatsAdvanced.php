<?php /*****************************************
File: teamStatsAdvanced.php
Creator: Derek Dekroon
Created: July 22/2013
Gives more detailed data, splits leagues by division.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
	function reloadSport(self) {
		document.location = 'teamStatsAdvanced.php?sportID=' + self.value;
	}
	
	function loadTeamPage() {
		document.location = 'teamStats.php';
	}
$(document).ready(function(){
  $('#togglePercentButton').click(function(){
    $('span[name=\"cellTeams\"]').fadeToggle(0);
	$('span[name=\"cellPercent\"]').fadeToggle(0);
  });
});
</script>";
$container = new Container('Advanced Team Stats', 'includeFiles/statsStyle.css', $javaScript);
require_once('includeFiles'.DIRECTORY_SEPARATOR.'class_season.php');
require_once('includeFiles'.DIRECTORY_SEPARATOR.'staticTeamData.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if($sportID == 1) {
	$sportData = $ultimateData;
} else if($sportID == 2) {
	$sportData = $volleyballData;
} else if($sportID == 3) {
	$sportData = $footballData;
} else if($sportID == 4) {
	$sportData = $soccerData;
} else if($sportID == 0) {
	print 'ERROR invalid sport ID<br />';
	exit(0);
}

$startSeasonSport[1] = 2002;
$startSeasonSport[2] = 2003;
$startSeasonSport[3] = 2004;
$startSeasonSport[4] = 2006;
$leagueNames[1] = array('A1', 'A2', 'B7', 'B1', 'B2', 'B', 'C1', 'C2', 'C');
$leagueNames[2] = array('Open 2', 'Comp 4', 'Inter/Comp 4', 'Inter 4', 'Inter 6', 'Rec 6');
$leagueNames[3] = array('A', 'B1', 'B2', 'B');
$leagueNames[4] = array('Inter', 'Rec I', 'Rec II', 'Rec');
$seasonNames = array('Spring', 'Summer'); //Every sport runs in spring and summer...realistically this doesn't need to be here it's kind've asking for problems

$sportQuery = mysql_query("SELECT * FROM $sportsTable WHERE sport_id = $sportID") 
	or die('ERROR getting seasons - '.mysql_error());
$sportArray = mysql_fetch_array($sportQuery);
$sportName = $sportArray['sport_name'];

$dataQuery = mysql_query("SELECT * FROM $seasonsTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = season_id 
	INNER JOIN $teamsTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
	WHERE team_num_in_league > 0 AND team_dropped_out = 0 AND league_sport_id = $sportID
	ORDER BY season_id ASC, league_id ASC, team_num_in_league ASC") 
	or die('ERROR getting data - '.mysql_error());
	$lastSeasonID = 0;
	$lastYear = 0;
	$lastSportID = 0;
	$lastLeagueID = 0;
while($array = mysql_fetch_array($dataQuery)) {
	$seasonID = $array['season_id'];
	$seasonYear = $array['season_year'];
	$leagueID = $array['league_id'];
	
	if($seasonID != $lastSeasonID) {
		if(!in_array($array['season_name'], $seasonNames)) {
			$seasonNum = count($seasonNames);
			$seasonNames[] = $array['season_name'];
		} else {
			reset($seasonNames);
			while($season = current($seasonNames)) {
				if(strcasecmp($season, $array['season_name']) == 0) {
					$seasonNum = key($seasonNames);
				}
				next($seasonNames);
			}
		}
		$sportData[$seasonYear][$seasonNum] = new Season($seasonID, $array['season_name'], $seasonYear);
		$lastSeasonID = $seasonID;
		foreach($leagueNames[$sportID] as $league) {
			$sportData[$seasonYear][$seasonNum]->numTeamsLeague[] = 0;
		}
	}
	
	if($leagueID != $lastLeagueID) {
		$lastLeagueID = $leagueID;
		$leagueName = filterLeagueName($sportID, $array['league_name']);
		if(!in_array($leagueName, $leagueNames[$sportID])) {
			$leagueNum = count($leagueNames[$sportID]);
			$leagueNames[] = $leagueName;
		} else {
			reset($leagueNames[$sportID]);
			while($league = current($leagueNames[$sportID])) {
				if(strcasecmp($league, $leagueName) == 0) {
					$leagueNum = key($leagueNames[$sportID]);
				}
				next($leagueNames[$sportID]);
			}
		}
	}
	$sportData[$seasonYear][$seasonNum]->numTeamsLeague[$leagueNum]++;
	
} 

$startSeason = $startSeasonSport[$sportID]; ?>

<h1>Team Stats</h1>
<div class='getIDs'>
	Sport
	<select name="sportID" id='userInput' onchange='reloadSport(this)'>
		<?php print getSportDD($sportID); ?>
	</select><br /><br />			
	<input type="button" id="togglePercentButton" value="Toggle Percent" />
	<input type="button" value="Back to General Teams Page" onClick="loadTeamPage()" />
</div>
<div class='tableData'>
	<?php for($j = 0; $j < count($seasonNames); $j++) {
		if($j % 3 == 0 && $j != 0) { ?>
			</div><div class='tableData'>
		<?php } ?>			
			<table>
				<tr>
					<th colspan=<?php print count($sportData[2012][$j]->numTeamsLeague) + 2; ?>>
						<?php print $seasonNames[$j]; ?>
					</th>
				</tr><tr>
					<td>
						Year
					</td>
					<?php foreach($leagueNames[$sportID] as $league) { ?>
						<td>
							<?php print $league ?>
						</td>
					<?php } ?>
					<td>
						Total
					</td>
				</tr>
				<?php for($i = 0; $i < count($sportData); $i++) { //Every Year 
					$isData = 0;
					$lastYearTotal = $yearTotal;
					$yearTotal = 0;
					for($k=0;$k<count($sportData[$startSeason + $i][$j]->numTeamsLeague);$k++) {
						if($sportData[$startSeason + $i][$j]->numTeamsLeague[$k] > 0) {
							$isData = 1;
							break;
						}
					}
					if($isData == 0) {
						continue;
					} ?>
					<tr>
						<td>
							<?php print $sportData[$startSeason + $i][$j]->seasonYear ?>
						</td>
						<?php for($k=0;$k<count($sportData[$startSeason + $i][$j]->numTeamsLeague);$k++) {?>
						<td>
							<?php 
								$curNumTeams = $sportData[$startSeason + $i][$j]->numTeamsLeague[$k];
								$lastNumTeams = $sportData[$startSeason + $i - 1][$j]->numTeamsLeague[$k];
								printCell($curNumTeams, $lastNumTeams);
								$yearTotal += $curNumTeams; ?>
						</td>
					<?php } ?>
					<td>
						<?php printCell($yearTotal, $lastYearTotal);?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>
	<?php } ?>
</div>

<?php $container->printFooter(); 

function filterLeagueName($sportID, $leagueName) {
	$searchArray = array();
	$replaceArray = array();
	if($sportID == 1) {
		$searchArray = array('A1/A2', 'A2/B1', 'B1/B2', 'B2/C', 'B2/C1', 'B/C', 'C1/C2', 'Division', 'Indoor', '(no lights)');
		$replaceArray = array('A', 'A2', 'B', 'C', 'C', 'C', 'C', '', '', '');
	} else if($sportID == 2) {
		$searchArray = array('Recreational', 'Intermediate', 'Competitive', '\'s');
		$replaceArray = array('Rec', 'Inter', 'Comp', '');
	} else if($sportID == 3) {
		$searchArray = array('B1/B2', 'A/B1', 'A/B', ' Division');
		$replaceArray = array('B', 'A', 'B', '');
	}else if($sportID == 4) {
		$searchArray = array('Recreational', 'Intermediate', '1', '2', ' 6 vs 6', ' 5 vs 5', 'Indoor');
		$replaceArray = array('Rec', 'Inter', 'I', 'II', '', '', '');
	}
	$leagueName = str_replace($searchArray, $replaceArray, $leagueName);
	$leagueName = trim(preg_replace("/[^A-Za-z0-9 ]/", '', $leagueName));
	if($sportID == 2) {
		if(strcasecmp(substr($leagueName, 0, 9), 'InterComp') == 0 || strcasecmp(substr($leagueName, 0, 9), 'CompInter') == 0) {
			return 'Inter/Comp 4';
		} else if(strstr($leagueName, '2') != FALSE) {
			return 'Open 2';
		} else {
			return $leagueName;
		}
	} else if($sportID == 3) {
		if(strstr($leagueName, 'Fall') != FALSE) {
			return 'B';
		} else {
			return $leagueName;
		}
	} else {
		return $leagueName;
	}
}

function printCell($curNumTeams, $lastNumTeams) {
	if($curNumTeams == 0) {
		$curNumTeams = '';
	}
	if($curNumTeams > $lastNumTeams && $lastNumTeams > 0) {
		$fontFilter = 'color:green;';
	} else if($curNumTeams < $lastNumTeams && $lastNumTeams > 0) {
		$fontFilter = 'color:red;';
	} else {
		$fontFilter = '';
	}
	if($lastNumTeams > 0) {
		$percent = round(($curNumTeams / $lastNumTeams * 100), 1) - 100;
		if($percent > 0) {
			$percentString = "+$percent%";
		} else if($percent != -100) {
			$percentString = "$percent%";
		} else {
			$percentString = '';
		}
	} else if($curNumTeams > 0) {
		$percent = 100;
		$percentString = $curNumTeams; 
	} else {
		$percent = 0;
		$percentString = '';
	}?>
	<?php print "<span name='cellTeams' style='display:inline;$fontFilter'>$curNumTeams</span>";
	print "<span name='cellPercent' style='display:none;$fontFilter'>$percentString</span>";
}?>