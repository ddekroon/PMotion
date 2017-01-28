var valuesArray = new Array;
function checkYesNoClose() {
	return confirm("Are you sure you want to close these standings?");
}

function checkYesNoSave() {
	return confirm("Are you sure you want to save these standings?");
}

function checkYesNoUndo() {
	return confirm("Are you sure you want to undo closing these standings?");
}

function reloadPageSport() {
	var form = document.getElementById('leagueForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='closeLeagueWeek.php?sportID=' + sportID;
}

function reloadPage() {
	var form = document.getElementById('leagueForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='closeLeagueWeek.php?sportID=' + sportID + '&leagueID=' + leagueID;
}
function reloadPageWeek(dateID) {
	var form = document.getElementById('leagueForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='closeLeagueWeek.php?sportID=' + sportID + '&leagueID=' + leagueID + '&dateID=' + dateID.value;
}

function loadValue(submission, index) {
	window.valuesArray[index] = submission;
}

function changeClosed(self, dropDownIndex) {
	var closedWins = parseInt(document.getElementById('closedWins').innerHTML);
	var closedLosses = parseInt(document.getElementById('closedLosses').innerHTML);
	var closedTies = parseInt(document.getElementById('closedTies').innerHTML);
	var closedTotal = parseInt(document.getElementById('closedTotal').innerHTML);
	var toChange = self.value;
	var oldValue = window.valuesArray[dropDownIndex];
	
	if(toChange == 1) { //going to a win
		closedWins++;
	} else if (toChange == 2) {//going to a loss
		closedLosses++;
	} else if(toChange == 3) {//going to a tie
		closedTies++;
	}
	
	if(oldValue == 1) { //going to a win
		closedWins--;
	} else if (oldValue == 2) {//going to a loss
		closedLosses--;
	} else if(oldValue == 3) {//going to a tie
		closedTies--;
	}
	if((toChange == 1 || toChange == 2 || toChange == 3) && (oldValue != 1 && oldValue != 2 && oldValue != 3)) {
		closedTotal++;
	} else if((toChange != 1 && toChange != 2 && toChange != 3) && (oldValue == 1 || oldValue == 2 || oldValue == 3)) {
		closedTotal--;
	}
	
	window.valuesArray[dropDownIndex] = self.value;
	
	document.getElementById('closedWins').innerHTML = closedWins;
	document.getElementById('closedLosses').innerHTML = closedLosses;
	document.getElementById('closedTies').innerHTML = closedTies;
	document.getElementById('closedTotal').innerHTML = closedTotal;
}