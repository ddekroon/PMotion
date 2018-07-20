<?php
date_default_timezone_set('America/New_York');
$temp = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'allSports'.DIRECTORY_SEPARATOR;

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once($temp.'includeFiles/matchClass.php');
require_once($temp.'includeFiles/tmpgVariableDeclarations2.php');
require_once($temp.'includeFiles/tmpgFormFunctions2.php');
require_once($temp.'includeFiles/teamClass.php');
require_once($temp.'includeFiles/stndVariableDeclarations.php');
require_once($temp.'includeFiles/stndOtherFunctions.php'); 
require_once($temp.'includeFiles/stndFormFunctions.php');

if(($teamID = $_GET['teamID']) == '') 
{
	$teamID = 0;
}

if($teamID == 0) 
{
	print 'Invalid URL<br />';
	exit();
}

?>
<html>
    <head>
		<link rel="stylesheet" type="text/css" href="includeFiles/teamPageStyles.css" />
		<link rel="stylesheet" type="text/css" href="includeFiles/standingsStyle.css" />
        <title>Team Pages</title>
    </head>

<?php

$teamObj = query("SELECT team_league_id FROM $teamsTable WHERE team_id = $teamID");
$leagueID = $teamObj->team_league_id;

$leagueObj = query("SELECT league_week_in_standings, league_sport_id FROM $leaguesTable WHERE league_id = $leagueID");
$sportID = $leagueObj->league_sport_id;

if($leagueObj->league_week_in_standings < 3) 
{ // check this because much faster to just search current teams, but if league has not started yet, must search all
	$temp = getTeamsData($teamID, 1);
}
else 
{
	$temp = getTeamsData($teamID, 0);
}

$teamObjs = $temp['teamObjs'];
$leagueID = $temp['leagueID'];
$matchObj = getMatchData($teamObjs, $teamID);

$picExists = 0;
if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamObjs[$teamID]->league_pic_link.DIRECTORY_SEPARATOR.$teamObjs[$teamID]->team_pic_name.'.JPG')) {
	$picLink = '/'.$teamObjs[$teamID]->league_pic_link.'/'.$teamObjs[$teamID]->team_pic_name.'.JPG';
	$picExists = 1;
} else if(file_exists(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamObjs[$teamID]->league_pic_link.DIRECTORY_SEPARATOR.$teamObjs[$teamID]->team_pic_name.'.jpg')) {
	$picLink = '/'.$teamObjs[$teamID]->league_pic_link.'/'.$teamObjs[$teamID]->team_pic_name.'.jpg';
	$picExists = 1;
} ?>

    <body>
    
		<div class='container'>
			<h2><?php print $teamObjs[$teamID]->team_name.' - '.$teamObjs[$teamID]->league_name.' - '.dayString($teamObjs[$teamID]->league_day_number) ?></h2>
			<h3><?php print $teamObjs[$teamID]->getFormattedStandings(); ?></h3>
			
			<div class="matchInfo" align="center">
				<?php $rowNum = 0; ?>
				<div class="Table" style="width:90%; max-width:775px;">
				<?php printMatchHeader();
				if ($matchObj != null) 
				{
					foreach($matchObj as $matchDay) 
					{
						$rowNum++;
						$rowNum = $rowNum % 2;
						
						foreach($matchDay as $match) 
						{
							printMatchNode($match, $rowNum, $teamObjs[$teamID]->league_id, $teamObjs);
						}
					} 
				}
				?>
                </div>
                
                
                
                
                
			</div>
            
            <!--<div class="matchInfo" align="center">
				<?php $rowNum = 0; ?>
				<div class="Table" style="width:90%; max-width:775px;">
                	<p><b>Playoff Week</b>
                    <?php $rowNum++;
					$rowNum = $rowNum % 2; ?>
           			Hello</p>
                </div>
            </div>-->
            
            
            
			<p style="height:20px"></p>
            <?php if($picExists == 1) { ?>
                <div class='centerImg' align="center" style="width:90%; max-width:550px;height:auto;">
                	<img src="<?php print $picLink ?>" border="2" style="width:90%; max-width:550px;height:auto;" />
                </div>
				<p style="height:20px"></p>
			<?php }
			if ($teamObjs[$teamID]->league_sort_by_win_pct == 0) 
			{
				usort($teamObjs, "comparePoints");
			} 
			else 
			{
				usort($teamObjs, "comparePercent");
			}
			
			printStandingsTable($teamObjs[0], $teamObjs, $leagueID); ?>
            
		</div>
	</body>
</html>
<?php $dbConnection->close(); ?>