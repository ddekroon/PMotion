
<?php function printOldFrame($sport, $year, $season) { ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
    <HTML>
    <HEAD>
    </HEAD>
    <FRAMESET rows="500,*">
            <frame name="PicFrame" src="topframe.php?sport=<?php print $sport?>" scrolling="auto">
            <frame name="NavFrame" src="archiveTeamPictures.php?<?php print "sport=$sport&year=$year&season=$season"?>" scrolling="auto">
            <noframes>
            </noframes>
    </FRAMESET>
    <frameset>
    </frameset>
    </HTML>
<?php } 

function printNewFrame($sportID, $seasonID) { ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
    <HTML>
    <HEAD>
    </HEAD>
    <FRAMESET rows="500,*">
            <frame name="PicFrame" src="topframe.php?sportID=<?php print $sportID ?>" scrolling="auto">
            <frame name="NavFrame" src='archiveTeamPictures.php?<?php print "sportID=$sportID&seasonID=$seasonID"?>' scrolling="auto">
            <noframes>
            </noframes>
    </FRAMESET>
    <frameset>
    </frameset>
    </HTML>
<?php }

if(($sport = $_GET['sport']) != '') {
	$year = $_GET['year'];
	$season = $_GET['season'];
	printOldFrame($sport, $year, $season);
} else {
	$sportID = $_GET['sportID'];
	$seasonID = $_GET['seasonID'];
	printNewFrame($sportID, $seasonID);
}
