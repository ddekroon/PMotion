function reloadPageSport() {
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var numPlayers=form.elements['groupSize'].value;
	if (numPlayers == 1) {
		self.location='addAgent.php?sportID=' + sportID;
	} else {
		self.location='addAgentGroup.php?sportID=' + sportID;
	}
}

function reloadPage() {
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID=form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	var isIndividual=form.elements['isIndividual'].value;
	var numPlayers=form.elements['groupSize'].value;
	if (numPlayers == 1) {
		self.location='addAgent.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID+ '&isIndividual=' + isIndividual+ '&numPlayers=' + numPlayers;
	} else {
		self.location='addAgentGroup.php?sportID=' + sportID + '&leagueID=' + leagueID+ '&teamID=' + teamID+ '&isIndividual=' + isIndividual+ '&numPlayers=' + numPlayers;
	}
}