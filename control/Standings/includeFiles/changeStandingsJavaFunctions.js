function checkYesNoSave() {
	return confirm("Are you sure you want to change these standings?");
}

function reloadSport(self) {
	var seasonID = document.getElementsByName('seasonID')[0].value;
	document.location = 'manuallyChangeStandings.php?sportID=' + self.value + '&seasonID=' + seasonID;
}

function reloadSeason(self) {
	var sportID = document.getElementsByName('sportID')[0].value;
	document.location = 'manuallyChangeStandings.php?sportID=' + sportID + '&seasonID=' + self.value;
}

function reloadLeague(self) {
	var sportID = document.getElementsByName('sportID')[0].value;
	var seasonID = document.getElementsByName('seasonID')[0].value;
	document.location= 'manuallyChangeStandings.php?sportID=' + sportID + '&seasonID=' + seasonID + '&leagueID=' + self.value;
}