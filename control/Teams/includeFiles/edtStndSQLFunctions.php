<?php
function updateTeams() {
	global $teamsTable;
    if($_POST['teamID'] == '') {
        return;
    }
	for($i=0;$i<count($_POST['teamID']);$i++) {
        $teamID = $_POST['teamID'][$i];
        $teamWins = $_POST['teamWins'][$i];
        $teamLosses = $_POST['teamLosses'][$i];
        $teamTies = $_POST['teamTies'][$i];
        mysql_query("UPDATE $teamsTable SET team_wins = $teamWins, team_losses=$teamLosses, team_ties = $teamTies WHERE team_id = $teamID")
		or die('ERROR updating team '.$teamID.mysql_error());
    }
} ?>