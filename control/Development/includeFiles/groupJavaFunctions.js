function checkError() {
	var errormessage = new String();
	var leagueIDs = document.getElementsByName('leagueID[]');
	var playerFirst = document.getElementsByName('playerFirst[]');
	var playerLast = document.getElementsByName('playerLast[]');
	var playerGender = document.getElementsByName('playerGender[]');
	var playerEmail = document.getElementsByName('playerEmail[]');
	var playerPhone = document.getElementsByName('playerPhone[]');
	var capPhone = playerPhone[0].value;
	capPhone = capPhone.replace(/[^0-9.]/g, "");
	var comments = document.getElementsByName('groupComments')[0].value;
	var form = document.getElementById('signupGroup');
	var payMethod = form.elements['payMethod'].options[form.elements['payMethod'].options.selectedIndex].value;
	
	if(leagueIDs[0].options[leagueIDs[0].options.selectedIndex].value == 0) { 
		errormessage += "\n\nPlease select a preffered league 1 from the dropdown list."; 
	}
	if(!(playerFirst[0].value.length > 2 && playerFirst[0].value.length < 21)) { 
		errormessage += "\n\nPlease enter your first name between 3 and 20 characters in length."; 
	}
	if(!(playerLast[0].value.length > 2 && playerLast[0].value.length < 31)) { 
		errormessage += "\n\nPlease enter your last name between 3 and 30 characters in length."; 
	}
	if(!validateEmail(playerEmail[0].value)) { 
		errormessage += "\n\nPlease enter your valid email."; 
	}
	if(!(capPhone.length == 11 && capPhone[0] == 1 || capPhone.length == 10)) { 
		errormessage += "\n\nPlease enter your valid phone number."; 
	}
	if(!(comments.length > 2 && comments.length < 1001) && comments.length != 0) { 
		errormessage += "\n\nPlease enter a comment between 3 and 1000 characters."; 
	}
	if(playerFirst[1].value.length > 0 && !validateEmail(playerEmail[1].value)) {
		errormessage += "\n\nPlease enter a valid email address for player 2."; 
	}
	if(payMethod == 0) {
		errormessage += "\n\nPlease enter a payment method."; 
	}
		
	if(errormessage.length > 2) {
	  alert('NOTE:' + errormessage);
	  return false;
	}
	return true;
}

function addRows() {
	var numRows = document.getElementById('numMoreRows').value;
	var rows = document.getElementsByClassName('repeatingRow');
	
	
	var numShown = 0;
	for (var i = 0; i< 50;i++) {
		if(numShown < numRows) {
			if(rows[i].style.display == 'none') {
				rows[i].style.display = 'table-row';
				numShown++;
			}
		} else {
			break;
		}
	}
	return false;
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function showTextbox(self) {
	var textRow = document.getElementById('hearTextRow');
	if(self.value == 8) {
		textRow.style.display = 'table-row';
	} else {
		textRow.style.display = 'none';
	}
}

function reloadPageSport() {
	var form = document.getElementById('signupGroup');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location = 'signupGroup.php?sportID=' + sportID;
}