<?php

date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/stndFormFunctions.php');
require_once('includeFiles/stndVariableDeclarations.php');
require_once('includeFiles/stndOtherFunctions.php');

if(($leagueID = $_GET['leagueID']) == '' || $_GET['leagueID'] == 0) {
		$leagueID = 0;
		print 'No League Specified<br />';
		exit(0);
	}

// VARIABLE DECLARATIONS
$leagueObj = query("SELECT league_week_in_standings,league_show_spirit_hour, season_name, date_description,league_num_days_spirit_hidden, league_day_number, 
					league_hide_spirit_hour, league_season_id, league_has_ties, league_day_number,	league_sport_id, sport_name, league_name, league_sort_by_win_pct
					FROM $leaguesTable  
					INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id
					LEFT JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
					LEFT JOIN $datesTable ON ($datesTable.date_day_number = league_day_number AND league_season_id = date_season_id AND date_sport_id = league_sport_id
						AND date_week_number = league_week_in_standings)
					WHERE league_id = $leagueID");
if($leagueObj == NULL) { //mostly if season = 0, exit nicely
	print 'Error, league does not exist';
	exit(0);
}
$totalWins = 0;
$totalLosses = 0;
$totalTies = 0;

if($leagueObj->league_week_in_standings < 50) {
	$dateDescription = $leagueObj->date_description;

	if ($leagueObj->league_week_in_standings > 1) {
		$teams = getActiveTeams($leagueID);
	}
	else {
		$teams = getFutureTeams($leagueID);
	}

	if ($leagueObj->league_sort_by_win_pct == 0) {
		usort($teams, "comparePoints");
	} else {
		usort($teams, "comparePercent");
	}
	if ($teamMaxWeek > $leagueObj->league_week_in_standings) {
		query("UPDATE $leaguesTable SET league_week_in_standings = $teamMaxWeek WHERE league_id = $leagueID");
		$leagueObj->league_week_in_standings = $teamMaxWeek;
		$query = "SELECT date_description FROM $datesTable 
			WHERE date_day_number = ".$leagueObj->league_day_number.' AND date_week_number = '.$teamMaxWeek.' 
			AND date_season_id = '.$leagueObj->league_season_id.' AND date_sport_id = '.$leagueObj->league_sport_id;
		$dateArray = query($query);
		$dateDescription = $dateArray->date_description;
	}
} else {
	$teams = getFinishedTeams($leagueID);
	usort($teams, "comparePosition");
}
$dbConnection->close(); ?>


<html>
    <head>
		<link rel="stylesheet" type="text/css" href="includeFiles/standingsStyle.css" />
    </head>
    <body>
		<div class='container'>
		<?php
		 if($leagueObj->league_week_in_standings < 50) {
			printLeagueTopInfo($leagueObj, $dateDescription); 
			printStandingsTable($leagueObj, $teams, $leagueObj->team_league_id);
			printActiveFooter();
		} else {
			printFinalTopInfo($leagueObj); ?>
			<table class="finalInfo">
				<?php $lastPosition = 0;
				for($i = 0 ; $i < count($teams) ; $i++) {
					$isTied = checkTied($teams, $i, count($teams), 'team_final_position');
					printFinalStandings($teams[$i], $isTied, 'team_final_position');
				} ?>
			</table>
			<?php printSpiritTopInfo(); ?>
			<table class="finalInfo">
				<?php usort($teams, "compareSpirit");
				$curPosition = 0;
				for($i = 0 ; $i < count($teams) ; $i++) {
					$isTied = checkTied($teams, $i, count($teams), 'team_final_spirit_position');
					if($teams[$i]->team_final_spirit_position <= 3) {
						printFinalStandings($teams[$i], $isTied, 'team_final_spirit_position');
					} else {
						break;
					}
				}?>
			</table>
		<?php } ?>
		</div>
	</body>
</html>