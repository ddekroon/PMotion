function checkAllScores() {
	var field = document.getElementsByName('deleteScore[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAllScores() {
	var field = document.getElementsByName('deleteScore[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkYesNo() {
	return confirm("Are you sure you want to delete these scores from the database?");
}

function reloadPageSport() {
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='addScore.php?sportID=' + sportID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='addScore.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function reloadPageTeam() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='addScore.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID;
}