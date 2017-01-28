<?php function printImage($logoString) { ?>
<HTML>
    <BODY>
        <p align="center">&nbsp;</p>
        <p align="center"><b><font face="Verdana" size="2">Click on a team name below to 
        see their team picture</font></b></p>
        <p align="center">&nbsp;</p>
        <p align="center">
        <img border="0" src="<?php print $logoString ?>"></p>
    </BODY>
</HTML>
<?php }

if(($sport = $_GET['sport']) != '') {
	if($sport == 'ultimate') {
		$logoString = '/Logos/ultimate_0.png';
	} else if($sport == 'beach') {
		$logoString = '/Logos/volleyball_0.png';
	} else if($sport == 'football') {
		$logoString = '/Logos/football_0.png';
	} else if($sport == 'soccer') {
		$logoString = '/Logos/soccer_0.png';
	}
	printImage($logoString);
} else {
	$sportID = $_GET['sportID'];
	if($sportID == 1) {
		$logoString = '/Logos/ultimate_0.png';
	} else if($sportID == 2) {
		$logoString = '/Logos/volleyball_0.png';
	} else if($sportID == 3) {
		$logoString = '/Logos/football_0.png';
	} else if($sportID == 4) {
		$logoString = '/Logos/soccer_0.png';
	}
	printImage($logoString);
}