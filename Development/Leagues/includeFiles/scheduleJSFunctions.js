function reloadUpdatePage() {
	var resultForm = document.getElementById('Schedule');
	var sportID= resultForm.elements['sportID'].options[resultForm.elements['sportID'].options.selectedIndex].value;
	var seasonID= resultForm.elements['seasonID'].options[resultForm.elements['seasonID'].options.selectedIndex].value;
	var leagueID= resultForm.elements['leagueID'].options[resultForm.elements['leagueID'].options.selectedIndex].value;
	self.location='storeScheduleData.php?sportID=' + sportID +'&seasonID=' + seasonID+'&leagueID=' + leagueID;
}

function openAddVenue() {
	var resultForm = document.getElementById('Schedule');
	var sportID= resultForm.elements['sportID'].options[resultForm.elements['sportID'].options.selectedIndex].value;
	var leagueID= resultForm.elements['leagueID'].options[resultForm.elements['leagueID'].options.selectedIndex].value;
	
	window.open('addVenue.php?sportID=' + sportID+'&leagueID=' + leagueID,'_blank');
	return false;
}