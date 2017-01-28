function reloadPageSport() {
	var form = document.getElementById('results');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='editSubmissions.php?sportID=' + sportID;
}

function reloadPage() {
	var form = document.getElementById('results');
	var sportID = form.elements['sportID'].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='editSubmissions.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function secondReload() {
	var form = document.getElementById('results');
	var sportID= form.elements['sportID'].value;
	var leagueID= form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var dateID= form.elements['dateID'].options[form.elements['dateID'].options.selectedIndex].value;
	self.location='editSubmissions.php?sportID=' + sportID +'&leagueID=' + leagueID +'&dateID=' + dateID;
}