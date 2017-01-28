<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'class_container.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/leagVariableDeclarations.php');

$leagueQuery = "SELECT league_name, sport_name FROM $leaguesTable INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
    WHERE league_id = $leagueID";
if(!($result = $dbConnection->query($leagueQuery))) print 'ERROR - '.mysql_error();
$leagueObj = $result->fetch_object();
$leagueName = $leagueObj->league_name;
$sportName = $leagueObj->sport_name;

$teamNum = getDatabaseTeams($leagueID)
?>

<html>
    <head>
        <link rel='stylesheet' type='text/css' href='includeFiles/leagueStyle.css'/>
        <title>Teams Page</title>
    </head>
    <body>
		<table>
			<tr>
				<th colspan="2" align=center>
					<?php print $sportName.' - '.$leagueName ?>
				</th>
			</tr>
			<tr>
				<td colspan="2" align="center">
					Team Name
				</td>
			</tr>
			<?php
			for($i=0;$i<$teamNum;$i++) { ?>
				<tr>
					<td>
						<?php print $i + 1; ?>
					</td>
					<td align="left">
						<?php print $team[$i]->teamName; ?>
					</td>
				</tr>
			<?php } ?>
		</table>
    </body>
</html>
