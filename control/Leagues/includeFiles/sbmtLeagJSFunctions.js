function reloadCreatePage() {
	var resultForm = document.getElementById('results');
	var sportID= resultForm.elements['sportID'].options[resultForm.elements['sportID'].options.selectedIndex].value;
	var seasonID= resultForm.elements['seasonID'].options[resultForm.elements['seasonID'].options.selectedIndex].value;
	self.location='submitLeague.php?sportID=' + sportID +'&seasonID=' + seasonID;
}

function reloadUpdatePage() {
	var resultForm = document.getElementById('results');
	var sportID= resultForm.elements['sportID'].options[resultForm.elements['sportID'].options.selectedIndex].value;
	var seasonID= resultForm.elements['seasonID'].options[resultForm.elements['seasonID'].options.selectedIndex].value;
	var leagueID= resultForm.elements['leagueID'].options[resultForm.elements['leagueID'].options.selectedIndex].value;
	self.location='updateLeague.php?sportID=' + sportID +'&seasonID=' + seasonID +'&leagueID=' + leagueID + '&update=1';
	
}