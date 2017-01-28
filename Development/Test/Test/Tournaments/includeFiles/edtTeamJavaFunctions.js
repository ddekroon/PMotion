
function checkAllPlayers() {
	var field = document.getElementsByName('playerCheck[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAllPlayers() {
	var field = document.getElementsByName('playerCheck[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkYesNo() {
	return confirm("Are you sure you want to delete these players from the database?");
}

function checkTeamName() {
	var oldNameArray = document.getElementsByName("teamID");
	var oldName = oldNameArray[0].options[oldNameArray[0].selectedIndex].text;
	var newNameArray = document.getElementsByName('newTeamName');
	var newName = newNameArray[0].value;
	return confirm("Are you sure you want to change the team name " + oldName + ' to ' + newName);
}

function checkLeague() {
	var oldLeagueArray = document.getElementsByName("leagueID");
	var oldLeague = oldLeagueArray[0].options[oldLeagueArray[0].selectedIndex].text;
	var newLeagueArray = document.getElementsByName('newLeagueID');
	var newLeague = newLeagueArray[0].options[newLeagueArray[0].selectedIndex].text;
	return confirm("Are you sure you want to change the team's league from " + oldLeague + ' to ' + newLeague);
}

function reloadPageTourney() {
	var form = document.getElementById('teamForm');
	var tourneyID=form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	self.location='editTeam.php?tournamentID=' + tourneyID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='editTeam.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID;
}

function reloadPageTeam() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='editTeam.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID+ '&teamID=' + teamID;
}