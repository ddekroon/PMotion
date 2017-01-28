<?php 

// Prevent crashing if nothing is selected

if(($sportID = $_GET['sportID']) == '') 
{
	$sportID = 0;
}

if(($seasonID = $_GET['seasonID']) == '')
{
	$seasonID = 0;
}

if(($leagueID = $_GET['leagueID']) == '') 
{
	$leagueID = 0;
}

if(($team1ID = $_GET['team1ID']) == '') 
{
	$team1ID = 0;
}

if(($team2ID = $_GET['team2ID']) == '') 
{
	$team2ID = 0;
}

// Function for the team drop down (don't touch)
function getTeamDD($leagueID, $teamID) 
{
	global $teamsTable, $dbConnection, $container;
	$teamsDropDown = '<option value=0>-- Teams --</option>';
    
    if($leagueID != 0)
    {
		$teamsQuery = "SELECT * FROM $teamsTable WHERE team_league_id = $leagueID AND team_num_in_league > 0 AND team_dropped_out < 1 ORDER BY team_num_in_league ASC, team_id DESC";
		
		if(!($result = $dbConnection->query($teamsQuery)))
		{
			$container->printError('ERROR getting teams drop down - '.$dbConnection->error);
		}
		
		while($team = $result->fetch_object()) 
		{
			if($team->team_id == $teamID)
			{
				$teamsDropDown.='<option selected value= '.$team->team_id.'>'.$team->team_name.'</option>';
			} 
			else 
			{
				$teamsDropDown.='<option value= '.$team->team_id.'>'.$team->team_name.'</option>';
			}
		}
	}
	return $teamsDropDown;
}
