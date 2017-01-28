window.checkAuto = 1;

function setAuto(self) {
	if(self.checked == true) {
		window.checkAuto = 1;
	} else {
		window.checkAuto = 0;
	}
}

function checkGroups(groupID, self) {
	var agents = document.getElementsByName('agent[]');
	var groupIDs = document.getElementsByName('groupID[]');
	if(window.checkAuto == 1 && groupID != 0) {
		for(i = 0; i < agents.length; i++) {
			if(parseInt(groupIDs[i].value) == groupID) {
				if(self.checked == true) {
					agents[i].checked = true;
				} else {
					agents[i].checked = false;
				}
			}
		}
	}
}

function checkAllPlayers() {
	var field = document.getElementsByName('player[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAllPlayers() {
	var field = document.getElementsByName('player[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkAllAgents() {
	var field = document.getElementsByName('agent[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAllAgents() {
	var field = document.getElementsByName('agent[]');
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function checkYesNo() {
	return confirm("Are you sure you want to change this information?");
}

function checkDelete() {
	return confirm("Are you sure you want to delete these players/teams?");
}

function checkTeamName() {
	var oldNameArray = document.getElementsByName("teamID");
	var oldName = oldNameArray[0].options[oldNameArray[0].selectedIndex].text;
	var newNameArray = document.getElementsByName('newTeamName');
	var newName = newNameArray[0].value;
	return confirm("Are you sure you want to change the team name " + oldName + ' to ' + newName);
}

function checkLeague() {
	var oldLeagueArray = document.getElementsByName("leagueID");
	var oldLeague = oldLeagueArray[0].options[oldLeagueArray[0].selectedIndex].text;
	var newLeagueArray = document.getElementsByName('newLeagueID');
	var newLeague = newLeagueArray[0].options[newLeagueArray[0].selectedIndex].text;
	return confirm("Are you sure you want to change the team's league from " + oldLeague + ' to ' + newLeague);
}

function reloadPageTourney() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	self.location='editByLeague.php?tournamentID=' + tourneyID;
}

function reloadPageLeague() {
	var form = document.getElementById('teamForm');
	var tourneyID = form.elements['tourneyID'].options[form.elements['tourneyID'].options.selectedIndex].value;
	var leagueID = form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value
	self.location='editByLeague.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID;
}

function showPrintPage(tourneyID) {
	window.open('regPrintPage.php?tournamentID='+tourneyID, '_blank');
	return false;	
}