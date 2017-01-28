<?php
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('includeFiles/matchClass.php');
require_once('includeFiles/tmpgVariableDeclarations.php');
require_once('includeFiles/tmpgFormFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/stndVariableDeclarations.php');
require_once('includeFiles/stndOtherFunctions.php'); 
require_once('includeFiles/stndFormFunctions.php');


if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
}
$teamID=2378;
if($teamID == 0) {
	print 'Invalid URL<br />';
	exit();
}

$temp = getTeamsData($teamID);
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
<html>
    <head>
		<link rel="stylesheet" type="text/css" href="includeFiles/teamPageStyle.css" />
		<link rel="stylesheet" type="text/css" href="includeFiles/standingsStyle.css" />
        <title><?php print $teamObjs[$teamID]->team_name ?> Team Page</title>
    </head>
    <body>
		<div class='container'>
			<h2><?php print $teamObjs[$teamID]->team_name.' - '.$teamObjs[$teamID]->league_name.' - '.dayString($teamObjs[$teamID]->league_day_number) ?></h2>
			<h3><?php print $teamObjs[$teamID]->getFormattedStandings(); ?></h3>
			
			<div class="matchInfo" align="center">
				<?php $rowNum = 0; ?>
				<div class="Table" style="width:90%; max-width:750px;">
				<?php printMatchHeader();
				foreach($matchObj as $matchDay) {
					$rowNum++;
					$rowNum = $rowNum % 2;
					foreach($matchDay as $match) {
						printMatchNode($match, $rowNum, $teamObjs[$teamID]->league_id, $teamObjs);
					}
				} ?>
                </div>
			</div>
			<p style="height:20px"></p>
            <?php if($picExists == 1) { ?>
                <div class='centerImg' align="center" style="width:90%; max-width:550px;height:auto;">
                	<img src="<?php print $picLink ?>" border="2" style="width:90%; max-width:550px;height:auto;" />
                </div>
				<p style="height:20px"></p>
			<?php }
			if ($teamObjs[$teamID]->league_sort_by_win_pct == 0) {
				usort($teamObjs, "comparePoints");
			} else {
				usort($teamObjs, "comparePercent");
			}
			printStandingsTable($teamObjs[0], $teamObjs, $leagueID); ?>
		</div>
	</body>
</html>
<?php $dbConnection->close(); ?>