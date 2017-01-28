function reloadPageSeason() {
	var seasonIDArray = document.getElementsByName('seasonID');
	var seasonID = seasonIDArray[0].value;
	self.location='submitTeam.php?seasonID=' + seasonID;
}

function reloadPageSport() {
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;	
	var seasonID=form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
	self.location='submitTeam.php?seasonID=' + seasonID + '&sportID=' + sportID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var seasonID=form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='submitTeam.php?seasonID=' + seasonID + '&sportID=' + sportID + '&leagueID=' + leagueID;
}