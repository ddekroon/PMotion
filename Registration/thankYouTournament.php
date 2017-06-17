<?php 

if(($tourneyID = $_GET['tourneyID']) == '') {
	$tourneyID = 0;
}

if (($leagueID = $_GET['leagueID']) == '') {
	$leagueID = '';
}

if($tourneyID == 1) {
	$URL = 'wheres-beach';
} else if($tourneyID == 2) {
	$URL = 'king-of-the-castle';
	if ($leagueID == 0) {
		$ext = "competitive-4s-players";
	}
	else if ($leagueID == 1) {
		$ext = "recinter-6s-players";
	}
} else if($tourneyID == 3) {
	$URL = 'dodgeball';
} else if($tourneyID == 4) {
	$URL = 'disc-golf';
} else if($tourneyID == 5) {
	$URL = 'stall-fall';
} else if($tourneyID == 6) {
	$URL = 'world-cup';
} else if($tourneyID == 7) {
	$URL = 'beaver-bowl';
} else if($tourneyID == 8) {
	$URL = 'multi-sport-tournament';
}

?>

<html>
	<head>
		<meta http-equiv="Content-Language" content="en-us">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
		<meta name="ProgId" content="FrontPage.Editor.Document">
		<title>Team/Player Registered</title>
	</head>
	<body>
		<p align="center">&nbsp;</p>
		<p align="center"><b><font face="Verdana">Thank you for registering for Perpetual Motion's Tournament.</font></b></p>
		<p align="center">&nbsp;</p>
		<p align="center">&nbsp;</p>
		<p align="center">
		<img border="0" src="../Logos/pmotionLogo.jpg" width="432" height="130"></p>
		<?php if($tourneyID < 9 && $tourneyID > 0) { ?>
		<p align="center">&nbsp;</p>
		<p align="center">
			<?php if ($tourneyID == 2) { ?>
            	<a target='_top' href='<?php print "http://perpetualmotion.org/tournaments/$URL/teams-entered/$ext" ?>'>
					Click here for a list of players entered
			    </a>
            <?php }else { ?>
                <a target='_top' href='<?php print "http://perpetualmotion.org/tournaments/$URL/teams-entered/$ext" ?>'>
                    Click here for a list of teams entered
                </a>
            <?php } ?>
		</p>
		<?php } ?>
	</body>
</html>