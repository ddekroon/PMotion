function reloadPageTourney() {
	var form = document.getElementById('teamForm');
	var tourneyID=form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	self.location='editPlayer.php?tournamentID=' + tourneyID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='editPlayer.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID;
}

function reloadPageTeam() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='editPlayer.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID+ '&teamID=' + teamID;
}

function reloadPagePlayer() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	var playerID=form.elements['playerID'].options[form.elements['playerID'].options.selectedIndex].value;
	self.location='editPlayer.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID+ '&teamID=' + teamID+ '&playerID=' + playerID;
}