function checkAll() {
	var field = document.getElementsByName('approveBadSpirit[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function checkYesNo() {
	return confirm('Are you sure you want to change these spirit scores?');
}


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

function checkYesNoApproveSpirit() {
	return confirm("Are you sure you want to approve these spirits from the database?");
}

function checkYesNoDeleteSpirit() {
	return confirm("Are you sure you want to delete these spirits from the database?");
}

function reloadPageSport() {
	var form = document.getElementById('oldSpiritForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='spiritControlPanel.php?sportID=' + sportID + '&activeTabIndex=2';
}

function reloadPageLeague() {
	var form = document.getElementById('oldSpiritForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='spiritControlPanel.php?sportID=' + sportID + '&leagueID=' + leagueID + '&activeTabIndex=2';
}

function reloadPageTeam() {
	var form = document.getElementById('oldSpiritForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='spiritControlPanel.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID + '&activeTabIndex=2';
}

function reloadPage() {
	var form = document.getElementById('teamsBadSpiritForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var spiritValue = form.elements['spiritValue'].options[form.elements['spiritValue'].options.selectedIndex].value;
	self.location='spiritControlPanel.php?sportID=' + sportID + '&spiritValue=' + spiritValue + '&activeTabIndex=1';
}

function reloadPageSeason() {
	var form = document.getElementById('spiritsByLeague');
	var seasonID = form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
	self.location='spiritControlPanel.php?seasonID=' + seasonID + '&activeTabIndex=3';
}