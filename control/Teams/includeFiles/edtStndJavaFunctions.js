function checkYesNo() {
	return confirm("Are you sure you want to change this team data?");
}

function reloadPageSport() {
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='editTeamStandings.php?sportID=' + sportID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='editTeamStandings.php?sportID=' + sportID + '&leagueID=' + leagueID;
}
