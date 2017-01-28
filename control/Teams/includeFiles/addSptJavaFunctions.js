function checkAllSpirits() {
	var field = document.getElementsByName('deleteOldSpirit[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAllSpirits() {
	var field = document.getElementsByName('deleteOldSpirit[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkYesNo() {
	return confirm("Are you sure you want to delete these spirits from the database?");
}

function reloadPageSport() {
	var form = document.getElementById('oldSpiritForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='addSpirit.php?sportID=' + sportID;
}

function reloadPageLeague() {
	var form = document.getElementById('oldSpiritForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='addSpirit.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function reloadPageTeam() {
	var form = document.getElementById('oldSpiritForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='addSpirit.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID;
}