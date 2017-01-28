<?php /*****************************************
File: teamStats.php
Creator: Derek Dekroon
Created: July 22/2013
Program that spits out stats about teams over the years in a nice organized table.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
	function loadAdvanced(sportID) {
		document.location = 'teamStatsAdvanced.php?sportID=' + sportID;
	}
$(document).ready(function(){
  $('#togglePercentButton').click(function(){
    $('span[name=\"cellTeams\"]').fadeToggle(0);
	$('span[name=\"cellPercent\"]').fadeToggle(0);
  });
});
</script>";
$container = new Container('Team Stats', 'includeFiles/statsStyle.css', $javaScript);

require_once('includeFiles'.DIRECTORY_SEPARATOR.'class_season.php');
require_once('includeFiles'.DIRECTORY_SEPARATOR.'staticTeamData.php');


$sports = array();
$seasonsBySport = array();
$startSeasonSport[1] = 2002;
$startSeasonSport[2] = 2003;
$startSeasonSport[3] = 2004;
$startSeasonSport[4] = 2006;


$sportQuery = mysql_query("SELECT * FROM $sportsTable WHERE sport_id > 0 ORDER BY sport_id") 
	or die('ERROR getting seasons - '.mysql_error());
$numSports = 1;
while($sportArray = mysql_fetch_array($sportQuery)) {
	$seasonsBySport[$numSports] = $seasonsArray = array('Spring', 'Summer');
	$sports[$numSports++] = array($sportArray['sport_id'], $sportArray['sport_name']);
}


$dataQuery = mysql_query("SELECT * FROM $seasonsTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_season_id = season_id 
	INNER JOIN $teamsTable ON $teamsTable.team_league_id = $leaguesTable.league_id 
	WHERE team_num_in_league > 0 AND team_dropped_out = 0 
	ORDER BY season_id ASC, league_sport_id ASC, team_num_in_league ASC") 
	or die('ERROR getting data - '.mysql_error());
	$lastSeasonID = 0;
	$lastYear = 0;
	$lastSportID = 0;
while($array = mysql_fetch_array($dataQuery)) {
	$seasonID = $array['season_id'];
	$seasonYear = $array['season_year'];
	$sportID = $array['league_sport_id'];
	if($seasonYear != $lastYear) {
		$numYears++;
		for($i = 1; $i <= count($sports); $i++) {
			$yearsArrayBySport[$i][$numYears] = $seasonYear;
		}
		$lastYear = $seasonYear;
	}
	
	if($sportID != $lastSportID) {
		$lastSportID = $sportID;
		if(!in_array($array['season_name'], $seasonsBySport[$array['league_sport_id']])) {
			$seasonNum = count($seasonsBySport[$array['league_sport_id']]);
			$seasonsBySport[$sportID][] = $array['season_name'];
		} else {
			reset($seasonsBySport[$sportID]);
			while($season = current($seasonsBySport[$sportID])) {
				if(strcasecmp($season, $array['season_name']) == 0) {
					$seasonNum = key($seasonsBySport[$sportID]);
				}
				next($seasonsBySport[$sportID]);
			}
		}
	}
	
	if($seasonID != $lastSeasonID) {
		if($sportID == $lastSportID) { //new Season, same sport. This means the num year was never changed
			if(!in_array($array['season_name'], $seasonsBySport[$array['league_sport_id']])) {
				$seasonNum = count($seasonsBySport[$array['league_sport_id']]);
				$seasonsBySport[$sportID][] = $array['season_name'];
			} else {
				reset($seasonsBySport[$sportID]);
				while($season = current($seasonsBySport[$sportID])) {
					if(strcasecmp($season, $array['season_name']) == 0) {
						$seasonNum = key($seasonsBySport[$sportID]);
					}
					next($seasonsBySport[$sportID]);
				}
			}
		}
		$seasonObj[$seasonYear][$seasonNum] = new Season($seasonID, $array['season_name'], $seasonYear);
		$lastSeasonID = $seasonID;
	}
	$seasonObj[$seasonYear][$seasonNum]->numTeamsSport[$sportID]++;
	
} 

for($j = 1; $j <= count($sports); $j++) {
	for($k = 0; $k < count($yearsArrayBySport[$j]); $k++) {
		$totalYearSport[$j][$startSeasonSport[$j]+$k] = 0;
		$totalYear[$startSeasonSport[$j]+$k] = array();
		$grandTotal[$k] = 0;
		for($i = 0; $i < count($seasonsBySport[$j]); $i++) {
			$totalYear[$startSeasonSport[$j]+$k][$i] = 0;
		}
	}
}?>

<h1>Team Stats</h1>
<div class='tableData'>
	<input type="button" id="togglePercentButton" value="Toggle Percent" />
</div>
<?php for($j = 1; $j <= count($sports); $j++) { 
	$curPrintSeason = $startSeasonSport[$j]; ?>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=<?php print count($yearsArrayBySport[$j]) + 1; ?>>
					<?php print $sports[$j][1]; ?>
				</th>
			</tr><tr>
				<td>
					Season
				</td>
				<?php $counter = 0;
					foreach($yearsArrayBySport[$j] as $year) { ?>
					<td>
						<?php print $year ?>
					</td>
				<?php } ?>
			</tr>
			<?php for($i = 0; $i < count($seasonsBySport[$j]); $i++) { ?>
				<tr>
					<td>
						<?php print $seasonsBySport[$j][$i]; ?>
					</td>
					<?php for($k = 0; $k < count($yearsArrayBySport[$j]); $k++) { ?>
					<td>
						<?php $curNumTeams = $seasonObj[$curPrintSeason + $k][$i]->numTeamsSport[$j];
						$lastNumTeams = $seasonObj[$curPrintSeason + $k - 1][$i]->numTeamsSport[$j];
						$totalYearSport[$j][$startSeasonSport[$j]+$k] += $curNumTeams;
						$totalYear[$startSeasonSport[$j]+$k][$i] += $curNumTeams;
						printCell($curNumTeams, $lastNumTeams); ?>
					</td>
				<?php } ?>
				</tr>
			<?php } ?>
			<tr>
				<td>
					Total
				</td>
				<?php for($k = 0; $k < count($yearsArrayBySport[$j]); $k++) { ?>
					<td>
						<?php printCell($totalYearSport[$j][$startSeasonSport[$j]+$k], $totalYearSport[$j][$startSeasonSport[$j]+$k-1]); ?>
					</td>
				<?php } ?>
			</tr>
		</table>
	</div><div class='tableData'>
		<input type="button" value="Advanced <?php print $sports[$j][1] ?> Stats" 
			onclick="loadAdvanced(<?php print $j ?>)"/>
	</div>
<?php } ?>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=<?php print count($yearsArrayBySport[1]) + 1; ?>>
				Yearly Totals
			</th>
		</tr><tr>
			<td>
				Total
			</td>
			<?php foreach($yearsArrayBySport[1] as $year) { ?>
				<td>
					<?php print $year ?>
				</td>
			<?php } ?>
		</tr>
		<?php for($i = 0; $i < count($seasonsBySport[1]); $i++) { ?>
		<tr>
			<td>
				<?php print $seasonsBySport[1][$i]; ?>
			</td>
			<?php for($k = 0; $k < count($yearsArrayBySport[1]); $k++) { ?>
				<td>
					<?php $grandTotal[$k] += $totalYear[$startSeasonSport[1]+$k][$i];
					printCell($totalYear[$startSeasonSport[1]+$k][$i], $totalYear[$startSeasonSport[1]+$k-1][$i]); ?>
				</td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td>
				Grand
			</td>
			<?php for($k = 0; $k < count($yearsArrayBySport[1]); $k++) { ?>
				<td>
					<?php printCell($grandTotal[$k], $grandTotal[$k - 1]); ?>
				</td>
			<?php } ?>
		</tr>
	</table>
</div>
<?php $container->printFooter(); 

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
		$percent = number_format(round(($curNumTeams / $lastNumTeams * 100), 1) - 100, 1);
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