function reloadPageSport() {
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='updatePlayer.php?sportID=' + sportID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='updatePlayer.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function reloadPageTeam() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='updatePlayer.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID;
}

function reloadPagePlayer() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	var playerID=form.elements['playerID'].options[form.elements['playerID'].options.selectedIndex].value;
	self.location='updatePlayer.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID+ '&playerID=' + playerID;
}

function reloadPageNewLeague() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	var playerID=form.elements['playerID'].options[form.elements['playerID'].options.selectedIndex].value;
	var newLeagueID=form.elements['newLeagueID'].options[form.elements['newLeagueID'].options.selectedIndex].value;
	self.location='updatePlayer.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID+ '&playerID=' + playerID+ '&newLeagueID=' + newLeagueID;
}