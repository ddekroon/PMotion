<?php 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'globalDeclarations.php');

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($leagueID = $_GET['leagueID']) == '') {
	$leagueID = 0;
}
if(($teamID = $_GET['teamID']) == '') {
	$teamID = 0;
} 

if($sportID == 1) {
	$sportName = 'ultimate';
} else if($sportID == 2) {
	$sportName = 'volleyball';
} else if($sportID == 3) {
	$sportName = 'football';
} else if($sportID == 4) {
	$sportName = 'soccer';
} else {
	$sportName = '';
}

$query = mysql_query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id WHERE team_id = $teamID");
$array = mysql_fetch_array($query) or die('ERROR getting data - '.mysql_error());

$leagueName = htmlentities($array['league_name'], ENT_QUOTES).' - '.dayString($array['league_day_number']);
$teamName = htmlentities($array['team_name'], ENT_QUOTES);
?>
<html>

<head>
	<title>Score Report Submitted</title>
	<link rel="stylesheet" href="includeFiles/thankYouRegStyle.css" />

</head>

<body>
	<p class="head">Thank you for submitting your team's score<br /><br />
	Good luck with your games next week!</p>
	<div class="image"><img border="0" src="/Logos/Perpetualmotionlogo2.jpg"></div>
	<div class="container">
		<div class="link"><a target='_top' href='<?php print "http://perpetualmotion.org/$sportName/$sportName-schedule?leagueID=$leagueID" ?>'><?php print $leagueName.' Schedule' ?></a></div>
		<div class="link"><a target='_top' href='<?php print "http://perpetualmotion.org/$sportName/$sportName-standings?leagueID=$leagueID" ?>'><?php print $leagueName.' Standings' ?></a></div>
		<div class="link"><a target='_top' href='<?php print "http://perpetualmotion.org/$sportName/teamPage?teamID=$teamID" ?>'><?php print $teamName.' Team Page' ?></a></div>
</div>
</body>

</html>
