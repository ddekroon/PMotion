window.checkAuto = 0;

function updateNumbers(numChanged) {
	var oldNumbers = document.getElementsByName('curTeamNum[]');
	var numbers = document.getElementsByName('teamNum[]');
	if(window.checkAuto == 1) {
		for(var i = 0; i < numbers.length; i++) {
			if(parseInt(numbers[numChanged].value) < parseInt(oldNumbers[numChanged].value)) {
				if(parseInt(numbers[i].value) >= parseInt(numbers[numChanged].value) && parseInt(numbers[i].value)  < parseInt(oldNumbers[numChanged].value) && i != parseInt(numChanged)) {
					newNumber = parseInt(numbers[i].value) + 1;
					numbers[i].value = newNumber;
					oldNumbers[i].value = newNumber;
				}
			} else {
				if(parseInt(numbers[i].value) <= parseInt(numbers[numChanged].value) && parseInt(numbers[i].value)  > parseInt(oldNumbers[numChanged].value) && i != parseInt(numChanged)) {
					newNumber = parseInt(numbers[i].value) - 1;
					numbers[i].value = newNumber;
					oldNumbers[i].value = newNumber;
				}
			}
		}
		oldNumbers[numChanged].value = numbers[numChanged].value;
	}
}

function setAuto(self) {
	if(self.checked == true) {
		window.checkAuto = 1;
	} else {
		window.checkAuto = 0;
	}
}

function revert() {
	var numbers = document.getElementsByName('teamNum[]');
	for(i = 0; i < window.teamNumsInLeague.length; i++) {
		numbers[i].value = window.teamNumsInLeague[i];
	}
}
	

function checkAll() {
	var field = document.getElementsByName('delete[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAll() {
	var field = document.getElementsByName('delete[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkYesNo() {
	return confirm("Are you sure you want to submit this data?");
}

function reloadPageSport() {
	var form = document.getElementById('leagueForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location='editByLeague.php?sportID=' + sportID;
}

function reloadPage() {
	var form = document.getElementById('leagueForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='editByLeague.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function loadTeamsPage(leagueID) {
	window.open('leagueTeamsPage.php?leagueID=' + leagueID, '_blank');
	return false;
}

function loadIndividualTeams(leagueID) {
	window.open('leagueIndividualTeams.php?leagueID=' + leagueID, '_blank');
	return false;
}

function checkAdd() {
    return confirm("Are you sure you want to add these teams to the league?");
}

function checkDelete() {
    return confirm("Are you sure you want to delete these teams?");
}