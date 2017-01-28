function reloadPage(emailTarget) {
	var sportID = document.getElementsByName('sportID')[0].value;
	var leagueID = document.getElementsByName('leagueID')[0].value;	
	var dayNumber = document.getElementsByName('dayNumber')[0].value;	
	var seasonID = document.getElementsByName('seasonID')[0].value;

	var lateEmail = 'removeLateEmail.php';
	
	top.location.href=lateEmail+'?sportID='+sportID+'&leagueID='+leagueID+'&dayNumber='+dayNumber+'&seasonID='+seasonID;
}

function CheckAll() {
	with (document.email) {
		for (var i=0; i < elements.length; i++) {
			if (elements[i].type == 'checkbox' && elements[i].name == 'checkBox[]')
				elements[i].checked = true;
		}
	}
}
function UnCheckAll() {
	with (document.email) {
		for (var i=0; i < elements.length; i++) {
			if (elements[i].type == 'checkbox' && elements[i].name == 'checkBox[]')
				elements[i].checked = false;
		}
	}
}

function checkTeams(teamID, self) {
	var checkBoxes = document.getElementsByName('checkBox[]');
	var teamIDs = document.getElementsByName('teamID[]');
	for(i = 0; i < checkBoxes.length; i++) {
		if(parseInt(teamIDs[i].value) == parseInt(teamID)) {
			if(self.checked == true) {
				checkBoxes[i].checked = true;
			} else {
				checkBoxes[i].checked = false;
			}
		}
	}
}

function reloadPageTournament() {
	var tourneyID = document.getElementsByName('tourneyID')[0].value;
	var year = document.getElementsByName('year')[0].value;
	var textareaval = document.email.message.value;
	
	top.location.href = 'emailPastTournaments.php?tourneyID='+tourneyID+'&year='+year;
	document.email.message.value=textareaval;
}

function loadDeletePage() {
	var delPlayerIDs = document.getElementsByName('deleteBox[]');
	var url = '/control/Email/deleteEmails.php?';
	var numToDelete = 0;
	for(i = 0; i < delPlayerIDs.length; i++) {
		if(delPlayerIDs[i].checked == true) {
			if( numToDelete ==0) {
				url += 'delArray[]=' + delPlayerIDs[i].value
			} else {
				url += '&delArray[]=' + delPlayerIDs[i].value
			}
		numToDelete++;
		}
	}
	window.open(url);
	return false;
}