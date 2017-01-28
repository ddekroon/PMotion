<?php date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'class_container.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/includeFiles/teamClass.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/includeFiles/stndVariableDeclarations.php');

if(($dayNum = $_GET['dayNum']) == '') {
	$dayNum = 0;
}

if($dayNum == 0) {
	$javaScript = "<script>
		function reloadDay(dayNum) {
			self.location = 'printFinalStandings.php?dayNum=' + dayNum.value;
		}
		</script>";
	$container = new Container('Print Final Standings', '', $javaScript); ?>
	<h1>Print Final Standings</h1>
	<div class='getIDs'>
		Day <select name='dayNum' id='userInput' onChange="reloadDay(this)">
			<option value=0>Choose</option>
			<?php for($i = 1; $i <= 7; $i++) {
				print "<option value=$i>".dayString($i).'</option>';
			} ?>
		</select>
	</div>

<?php } else { ?>
	<html>
		<head>
			<title>Print League Standings</title>
			<style>
				.league {
					page-break-after:always;
				}
				
				.league:last-of-type {
					page-break-after:avoid;
				}
				
				.bottomData {
					margin-top:30px;
					width:100%;
				}
				
				.bottomData td {
					width:50%;
					padding:4px 5px 0px 0px;
				}
				
				.standings {
					border-collapse:collapse;
					margin-left:auto;
					margin-right:auto;
				}
				
				.standings tr:nth-of-type(2n) {
					background-color:#CCC;
				}
				
				.standings td {
					padding-right:7px;
				}
			</style>
		</head>
		<body>
		
<?php // VARIABLE DECLARATIONS
	$leagueQuery = "SELECT league_name, sport_name, league_has_ties, league_sort_by_win_pct, league_day_number, league_id FROM $leaguesTable 
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id 
		WHERE season_available_score_reporter = 1 AND league_day_number = $dayNum ORDER BY sport_id ASC, league_name ASC";
	if(!($result = $dbConnection->query($leagueQuery))) print 'Error getting teams - '.$dbConnection->error;
	while($leagueObj = $result->fetch_object()) {
		$league['name'] = $leagueObj->league_name;
		$league['sportName'] = $leagueObj->sport_name;
		$league['hasTies'] = $leagueObj->league_has_ties;
		$league['sortByPercent'] = $leagueObj->league_sort_by_win_pct;
		$league['dayString'] = dayString($leagueObj->league_day_number);
		$curLeagueID = $leagueObj->league_id;

		$teamsQuery= "SELECT team_name, team_dropped_out, team_wins, team_losses, team_ties, team_id FROM $teamsTable WHERE team_league_id = $curLeagueID AND team_num_in_league > 0
			AND team_dropped_out = 0";
		if(!($teamResult = $dbConnection->query($teamsQuery))) print 'Error getting teams - '.$dbConnection->error;
		
		$i=0;
		while($teamObj = $teamResult->fetch_object()) {
			$team[$i] = new Team();
			$team[$i]->teamName = $teamObj->team_name;
			$team[$i]->teamDroppedOut = $teamObj->team_dropped_out;
			$team[$i]->teamWins = $teamObj->team_wins;
			$team[$i]->teamLosses = $teamObj->team_losses;
			$team[$i]->teamTies = $teamObj->team_ties;
			$team[$i]->teamPoints = $team[$i]->getPoints();
			$team[$i]->teamID= $teamObj->team_id;
			$teamID = $teamObj->team_id;
			
			$spiritQuery= "SELECT spirit_score_edited_value FROM $spiritScoresTable INNER JOIN $scoreSubmissionsTable ON 
				$scoreSubmissionsTable.score_submission_id = $spiritScoresTable.spirit_score_score_submission_id
				WHERE score_submission_opp_team_id = $teamID AND spirit_score_ignored = 0 
				AND (spirit_score_edited_value > 3.5 OR spirit_score_dont_show = 1 OR spirit_score_is_admin_addition = 1) 
				AND spirit_score_edited_value > 0";
				
			if(!($spiritResult = $dbConnection->query($spiritQuery))) print 'Error getting spirits - '.$dbConnection->error;
			while($spiritObj = $spiritResult->fetch_object()) {
				$team[$i]->teamSpiritNumbers++;
				$team[$i]->teamSpiritTotal = $team[$i]->addSpirit($spiritObj->spirit_score_edited_value);
			}
			$team[$i]->teamSpiritAverage = $team[$i]->getSpiritAverage();

			
			$team[$i]->teamPointsAvailable = $team[$i]->getAvailablePoints();
			$team[$i]->teamWinPercent = $team[$i]->getWinPercent();
			$team[$i]->setTitleSize();
			$i++;
		}
		if(count($team) > 0) {
			if ($league['sortByPercent'] == 0) {
				usort($team, "comparePoints");
			} else {
				usort($team, "comparePercent");
			} ?>
	
			<div class='league'>
				<table class='standings'>
					<?php printLeagueHeader($league);
					for($i = 0 ; $i < count($team) ; $i++) {
						printLeagueNode($team[$i], $i);
					} 
					$teams=$team; ?>
				</table><table class='bottomData'>
					<?php usort($team, "compareSpirit");
					$spiritTeam = printBottomData($league, $team); ?>
				</table>
                <br />
                <p><table align="center" style="width:75%;">
                <?php 
					printContactInfo($teams,$spiritTeam);
				?>
                </table></p>
			</div>
			<?php $team = array();
			$league = array();
		}
	} //end While ?>
		</body>
	</html>
<?php } //end if dayNum != 0

function printLeagueHeader($league) {  ?>
    <tr>
		<th colspan=9>
			<?php print $league['sportName'].' - '.$league['name'].' - '.$league['dayString'];?>
		</th>
	</tr><tr>
        <td colspan=2>
            Team
        </td><td>
            W
        </td><td>
            L
        </td><td>
			T
		</td><td>
            Pts
        </td><td>
			Win Pct
		</td><td>
            Spirit
        </td>
    </tr>
<?php }

function printLeagueNode($curTeam, $i) { 
	global $league ?>
	<tr>
        <td>
            <?php print $i+1?>
        </td><td style='width:200px;'>
            <?php print $curTeam->teamName;
			if ($curTeam->teamDroppedOut == 1) {
				print '(Dropped)';
			}?>
        </td><td>
            <?php print $curTeam->teamWins; ?>
        </td><td>
            <?php print $curTeam->teamLosses; ?>
        </td><td>
			<?php print $curTeam->teamTies; ?>
		</td><td>
            <?php print $curTeam->teamPoints?>
        </td><td>
			<?php print number_format($curTeam->teamWinPercent, 3, '.', '')?>
		</td><td>
            <?php if($curTeam->teamSpiritAverage > 0) {
				print number_format($curTeam->teamSpiritAverage, 2, '.', '');
			} else {
				print 'N/A';
			}?>
        </td>
    </tr>
<?php }

function printBottomData($league, $team) { 

	global $playersTable;	?>

	<tr>
		<th>
			Spirit Winners
		</th><th>
			Champions
		</th>
	</tr>
	<?php $spiritPosition= 0;
	for($position = 1; $position <= 3; $position++) { ?>
		<tr>
			<td>
				<?php print $position.') '; 
				$spiritValue = round($team[$spiritPosition]->teamSpiritAverage, 2);
				$toPrint = array();
				$spiritTeam[$spiritPosition] = $team[$spiritPosition];
				for($i = $spiritPosition; $i < count($team); $i++) {
					if(round($team[$i]->teamSpiritAverage, 2) == $spiritValue) {
						$toPrint[] = $team[$i]->teamName;
						$spirit = $team[$i]->teamSpiritAverage;
						$spiritTeam[$spiritPosition] = $team[$spiritPosition];
						$spiritPosition++;
					} else if (round($team[$i]->teamSpiritAverage, 2) < $spiritValue) {
						break;
					}
				} 
				print join(', ', $toPrint).' <strong>'.$spirit.'</strong>';?>
			</td><td >
				<?php print $position.')'; ?>
			</td>
		</tr>
	<?php }
	return $spiritTeam;
}

function printContactInfo($teams, $team) {
	
	global $leaguesTable, $playersTable; ?>
    
    <tr>
    	<th>
        	Team
        </th><th>
    		Captain Name
        </th><th>
        	Phone Number
        </th>
    </tr>
		
	<?php for($i = 0 ; $i < 4; $i++) { // print contact info for top 4 teams in standings

    $teamID=$teams[$i]->teamID;
	$captainQuery = mysql_query("SELECT player_firstname, player_lastname, player_phone FROM $playersTable WHERE player_is_captain = 1 AND player_team_id = $teamID;");
	$captain = mysql_fetch_array($captainQuery); ?>
    
    <tr>
    	<td>
        	<?php print ($teams[$i]->teamName); ?>
        </td><td>
        	<?php print($captain['player_firstname']." ".$captain['player_lastname']); ?>
        </td><td>
        	<?php print ($captain['player_phone']); ?>
        </td>
    </tr>
    
    <?php } 
    $i=0;
	do {
		$teamID=$team[$i]->teamID;
		$captainQuery = mysql_query("SELECT player_firstname, player_lastname, player_phone FROM $playersTable WHERE player_is_captain = 1 AND player_team_id = $teamID;");
		$captain = mysql_fetch_array($captainQuery); ?>
		
        <tr>
            <td>
                <?php print ("Spirit - " . $team[$i]->teamName); ?>
            </td><td>
                <?php print($captain['player_firstname']." ".$captain['player_lastname']); ?>
            </td><td>
                <?php print ($captain['player_phone']); ?>
            </td>
        </tr>
        
	<?php 
		$i=$i+1;
	} while ($team[$i-1]->teamSpiritAverage == $team[$i]->teamSpiritAverage);

 } ?>