function reloadPageSport() {
	var sportIDArray = document.getElementsByName('sportID');
	var sportID = sportIDArray[0].value;
	self.location='splitLeague.php?sportID=' + sportID;
}

function reloadPageLeague() {
	var leagueID = document.getElementsByName('leagueID')[0].value;
	var sportID = document.getElementsByName('sportID')[0].value;

	self.location='splitLeague.php?sportID=' + sportID + '&leagueID=' + leagueID;
}