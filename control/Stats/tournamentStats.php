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
$container = new Container('Tournament Stats', 'includeFiles/statsStyle.css', $javaScript);

require_once('includeFiles'.DIRECTORY_SEPARATOR.'class_tournament.php');
require_once('includeFiles'.DIRECTORY_SEPARATOR.'staticTournamentData.php');

$yearsArray = array(2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011);
declareTournamentVariables($tourneyData);
for($i = 1; $i <= count($tourneyData); $i++) {
	if($tourneyData[$i]->tourneyIsTeams == 1) {
		getTeams($i, $tourneyData);
	} else if ($tourneyData[$i]->tourneyIsPlayers == 1 || $tourneyData[$i]->tourneyIsCards == 1) {
		getPlayers($i, $tourneyData);
	}
} ?>

<h1>Tournament Stats</h1>
<div class="getIDs">
	<input type="button" id="togglePercentButton" value="Toggle Percent" />
</div>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=<?php print count($yearsArray) + 1 ?>>
				Teams Per Tournament
			</th>
		</tr><tr>
			<td>
				Tourney
			</td>
			<?php $counter = 0;
			for($i = 0; $i < count($yearsArray); $i++) { ?>
				<td>
					<?php $totalYear[$i] = 0;
					print $yearsArray[$i]; ?>
				</td>
			<?php }  ?>
		</tr>
		<?php for($j = 1; $j <= count($tourneyData); $j++) { 
			if($tourneyData[$j]->numTeams[0] != '') { ?>
				<tr>
					<td>
						<?php print $tourneyData[$j]->tourneyName; ?>
					</td>
				<?php $startYear = $tourneyData[$j]->startYear;
				$count = 0;
				for($i = 0; $i < count($yearsArray); $i++) { ?>
					<td>
						<?php if($i >= $startYear - 2000) {
							$curNumTeams = $tourneyData[$j]->numTeams[$count];
							$lastNumTeams = $tourneyData[$j]->numTeams[$count++ - 1];
							if($tourneyData[$j]->tourneyIsTeams == 1) {
								$totalYear[$i] += $curNumTeams;
							}
							printCell($curNumTeams, $lastNumTeams);
						} ?>
					</td>
				<?php } ?>
				</tr>
			<?php } ?>
		<?php } ?>
		<tr>
			<td>
				Total
			</td>
			<?php for($k = 0; $k < count($yearsArray); $k++) { ?>
				<td>
					<?php printCell($totalYear[$k], $totalYear[$k-1]); ?>
				</td>
			<?php } ?>
		</tr>
	</table>
</div>

<?php $container->printFooter(); 

function declareTournamentVariables(&$tourneyData) {
	global $tournamentsTable;

	$tourneyQuery = mysql_query("SELECT * FROM $tournamentsTable ORDER BY tournament_id ASC") 
		or die('ERROR getting tourney info - '.mysql_error());
	$i = 1;
	while($tourneyArray = mysql_fetch_array($tourneyQuery)) {
		$tourneyData[$i]->tourneyID = $tourneyArray['tournament_id'];
		$tourneyData[$i]->tourneyName = $tourneyArray['tournament_name'];
		$tourneyData[$i]->tourneyIsCards = $tourneyArray['tournament_is_cards'];
		$tourneyData[$i]->tourneyIsLeagues = $tourneyArray['tournament_is_leagues'];
		$tourneyData[$i]->tourneyIsTeams = $tourneyArray['tournament_is_teams'];
		$tourneyData[$i]->tourneyIsPlayers = $tourneyArray['tournament_is_players'];
		$i++;
	}
}

function getTeams($tourneyID, &$tourneyData) {
	global $tournamentTeamsTable, $yearsArray;
	
	if($tourneyData[$tourneyID]->tourneyIsLeagues == 1) {
		$orderFilter = 'tournament_team_num_in_league';
		$teamNumFilter = ' AND tournament_team_num_in_league > 0';
	} else {
		$orderFilter = 'tournament_team_num_in_tournament';
		$teamNumFilter = ' AND tournament_team_num_in_tournament > 0';
	}
	
	$teamsQuery = mysql_query("SELECT * FROM $tournamentTeamsTable WHERE tournament_team_tournament_id = $tourneyID 
		$teamNumFilter AND tournament_team_is_waiting = 0 ORDER BY tournament_team_tournament_num_running
		ASC, $orderFilter ASC") or die('ERROR getting tourney teams - '.mysql_error());
	$lastYear = 0;
	while($teamArray = mysql_fetch_array($teamsQuery)) {
		$year = preg_replace("/\.[0-9]*/", '', $teamArray['tournament_team_tournament_num_running']);
		if(!in_array($year, $yearsArray) && is_numeric($year) && $year > 2010) {
			$yearsArray[] = $year;
		}
		$numRunning = $year - $tourneyData[$tourneyID]->startYear;
		if($year != $lastYear && $year > 2000) {
			$lastYear = $year;
			$tourneyData[$tourneyID]->numTeams[$numRunning] = 0;
		}
		if($numRunning > 0) { //I added a team in 1999 for dodgeball, any old contacts from Dave's email get added to that team. That way they can be pulled up in the email past tournament program
			$tourneyData[$tourneyID]->numTeams[$numRunning]++;
		}
	}
}

function getPlayers($tourneyID, &$tourneyData) {
	global $tournamentTeamsTable, $tournamentPlayersTable, $yearsArray;
	
	
	if($tourneyData[$tourneyID]->tourneyIsLeagues == 1 && $tourneyData[$tourneyID]->tourneyIsCards == 0) {
		$orderFilter = 'tournament_player_num_in_league ASC, ';
		$playerNumFilter = ' AND tournament_player_num_in_league > 0';
	} else if($tourneyData[$tourneyID]->tourneyIsLeagues == 0 && $tourneyData[$tourneyID]->tourneyIsCards == 0) {
		$orderFilter = 'tournament_player_num_in_tournament ASC, ';
		$playerNumFilter = ' AND tournament_player_num_in_tournament > 0';
	} else if($tourneyData[$tourneyID]->tourneyIsCards == 1) { 
		$playerNumFilter = ' AND tournament_player_card > 0';
	}
	
	$playersQuery = mysql_query("SELECT * FROM $tournamentPlayersTable WHERE tournament_player_tournament_id = $tourneyID
		$playerNumFilter AND tournament_player_is_waiting = 0 ORDER BY tournament_player_tournament_num_running ASC, 
		$orderFilter tournament_player_id ASC") or die('ERROR getting tourney players - '.mysql_error());
	
	while($playerArray = mysql_fetch_array($playersQuery)) {
		$year = preg_replace("/\.[0-9]*/", '',$playerArray['tournament_player_tournament_num_running']);
		if(!in_array($year, $yearsArray) && is_numeric($year) && $year > 2010) {
			$yearsArray[] = $year;
		}
		$numRunning = $year - $tourneyData[$tourneyID]->startYear;
		if($year != $lastYear) {
			$lastYear = $year;
			$tourneyData[$tourneyID]->numTeams[$numRunning] = 0;
		}
		$tourneyData[$tourneyID]->numTeams[$numRunning]++;
	}
}

function printCell($curNumTeams, $lastNumTeams) {
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