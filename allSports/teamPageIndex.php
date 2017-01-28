<?php require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'globalDeclarations.php');
require_once('includeFiles'.DIRECTORY_SEPARATOR.'teamClass.php');

if(($leagueID = $_GET['leagueID']) == '' || $_GET['leagueID'] == 0) {
	exit(0);
}

if(!($leagueQuery = $dbConnection->query("SELECT league_name, league_day_number, league_sport_id, team_num_in_league, team_id, team_name FROM $teamsTable 
	INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
	WHERE team_league_id = $leagueID AND team_num_in_league > 0 
	AND team_finalized = 1 AND team_dropped_out = 0 ORDER BY team_num_in_league ASC"))) {
	print 'error getting league data - '.$dbConnection->error;
	exit(0);
}
if($leagueQuery->num_rows == 0) {
	print 'ERROR no teams';
	exit(0);
}
while($teamObj = $leagueQuery->fetch_object()) {
	$team[] = new Team($teamObj);
} 
$leagueQuery->close();

$leagueName = $team[0]->league_name.' - '.dayString($team[0]->league_day_number);
if($team[0]->league_sport_id == 1) {
	$sportName = 'ultimate';
} else if($team[0]->league_sport_id == 2) {
	$sportName = 'volleyball';
} else if($team[0]->league_sport_id == 3) {
	$sportName = 'football';
} else if($team[0]->league_sport_id == 4) {
	$sportName = 'soccer';
}
$dbConnection->close();?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Teams Index</title>
	</head>
	
    <style>
		.Table
		{
			display: table;
			width:600px;
			text-align:justify;
			margin:0px auto;
		}
		.Heading
		{
			display: table-row;
			font-weight: bold;
			text-align: center;
			width:900px;
			margin:0px auto;
		}
		.Row
		{
			display: table-row;
			width:500px;
			margin:0px auto;
			padding:5px;
		}
		.colourRow
		{
			display: table-row;
			width:500px;
			height:15px;
			margin:0px auto;
			padding:5px;
			background:#CCCCCC;
		}
		.Column
		{
			width:65px;
			display: table-cell;
			margin:0px auto;
			padding:5px;
	
		}
		.Cell
		{
			width:500px;
			height:25px;
			display: table-cell;
			text-align:center;
			border: solid;
			border-width: medium;
			margin:0px auto;
		}
		.noBorderCell
		{
			width:500px;
			display: table-cell;
			margin:0px auto;
		}
	</style>
    
	<body>
		<div class="Table" style="width:90%; max-width:600px; min-width:250px;">
			<div class="Heading">
					<span style="font-size:20px; text-decoration:underline;"><?php print $leagueName ?></span>
			</div>
			<div class="Row" align="center">
					** Click on a team name to see their team page **
			</div>
			<div class="Row" align="center">
				<div class="noBorderCell">
						<?php for($i = 0; $i < count($team); $i++) 
						{ 
							if ($team[$i]->team_dropped_out == 0)
							{?>
                        
                        	<div class="Row">
								<div class="Column" align="left">
									Team <?php print $team[$i]->team_num_in_league ?>
                             	</div>
								<div class="Column" align="left" style="width:auto">
									<?php print "<a target='_top' href='http://perpetualmotion.org/$sportName/teamPage?teamID=".$team[$i]->team_id."'>".$team[$i]->team_name.'</a>'; ?>
								</div>
                            	</div>
						<?php }
						} ?>
					</div>
                    </div>
			</div>
		
	</body>
</html>
