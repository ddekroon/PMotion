<?php

function updateFinalStandings($leagueID) {
	global $teamsTable, $leaguesTable, $container;
	if(($teamCount = $_POST['teamCount']) == '') {
		$teamCount = 0;
	}
	
	for($i=0;$i<$teamCount;$i++) {
		$teamNum = $_POST['teamNum'][$i];
		$teamID = $_POST['teamID'][$i];
		$spiritNum = $_POST['spiritNum'][$i];
		mysql_query("UPDATE $teamsTable SET team_final_position = $teamNum, team_final_spirit_position = $spiritNum WHERE team_id = $teamID") 
			or die('ERROR updating teams - '.mysql_error());
	}
	$container->printSuccess('Standings successfully closed');
} ?>