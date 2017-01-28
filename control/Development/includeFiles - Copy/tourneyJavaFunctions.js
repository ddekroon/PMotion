function showDivisionCards(self) {
	var division = self.value;
	var cardsDropDown = document.getElementsByName('cardsDropDown')[0];
	var numBlack = document.getElementsByName('numBlack[]');
	var numRed = document.getElementsByName('numRed[]');
	var cardValue = 0;
	
	for(var i = 0; i < cardsDropDown.length; i++) {
		for(var j = 1; j < 5; j++) {
			cardValue = parseInt(cardsDropDown[i].value) - (100 * j);
			if(cardValue < 100) {
				break;
			}
		}
		if(cardsDropDown[i].value < 300) {
			if(cardValue <= numBlack[division].value || cardsDropDown[i].value == 0) {
				cardsDropDown[i].style.display = 'inline';
			} else {
				cardsDropDown[i].style.display = 'none';
			} 
		} else {	
			if(cardValue <= numRed[division].value || cardsDropDown[i].value == 0) {
				cardsDropDown[i].style.display = 'inline';
			} else {
				cardsDropDown[i].style.display = 'none';
			}
		}
	}
	
	
}

function showGenderCards(self) {
	var gender = self.value;
	var cardsDropDown = document.getElementsByName('cardsDropDown')[0];
	var startValue = 0;
	var endValue = 0;

	if(gender == 'M') {
		startValue = 100;
		endValue = 300
	} else if (gender == 'F') {
		startValue = 300;
		endValue = 500;
	} else {
		startValue = 100;
		endValue = 500;
	}
	for(var i = 0; i < cardsDropDown.length; i++) {
		if((cardsDropDown[i].value > startValue && cardsDropDown[i].value < endValue) || cardsDropDown[i].value == 0) {
			cardsDropDown[i].disabled = false;
		} else {
			cardsDropDown[i].disabled = true;
		}
	}
}

function errorCheckTeam() {
	var teamName = document.getElementsByName('teamName')[0].value;
	var errorMessage = new String();

	if(!(teamName.length > 2 && teamName.length < 41)) { 
		errorMessage += "\n\nPlease enter a team name between 3 and 40 characters in length."; 
	}
	errorMessage += errorCheck();
	
	if(errorMessage.length > 2) {
	  alert('NOTE:' + errorMessage);
	  return false;
	}
	return true; 
}


function errorCheckPlayer() {
	var errorMessage = new String();

	errorMessage += errorCheck();
	if(errorMessage.length > 2) {
	  alert('NOTE:' + errorMessage);
	  return false;
	}
	return true; 
	
}

function errorCheckCard() {
	var division = document.getElementsByName('leagueID')[0].value;
	var cardDropDown = document.getElementsByName('cardsDropDown')[0];
	var numBlack = document.getElementsByName('numBlack[]');
	var numRed = document.getElementsByName('numRed[]');
	var errorMessage = new String();
	var cardValue = 0;
	var cardNum = cardDropDown.options[cardDropDown.selectedIndex].value;
	
	if(cardNum == 0) { 
		errorMessage += "\n\nPlease choose a card."; 
	}
	for(var j = 1; j < 5; j++) {
		cardValue = parseInt(cardNum) - (100 * j);
		if(cardValue < 100) {
			break;
		}
	}
	if(cardNum < 300 && cardValue > numBlack[division]) { 
		errorMessage += "\n\nCard not allowed for current division, please choose another."; 
	} else if(cardNum >= 300 && cardValue > numRed[division]) { 
		errorMessage += "\n\nCard not allowed for current division, please choose another."; 
	}
	errorMessage += errorCheck();
	
	if(errorMessage.length > 2) {
	  alert('NOTE:' + errorMessage);
	  return false;
	}
	return true; 
	
}

function errorCheck() {
	var errormessage = new String();
	
	var form = document.getElementById('tourneyReg');
	var leagueID = form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var capFirst = document.getElementsByName('capFirst')[0].value;
	var capLast = document.getElementsByName('capLast')[0].value;
	var capGender = form.elements['capSex'].options[form.elements['capSex'].options.selectedIndex].value;
	var capEmail = document.getElementsByName('capEmail')[0].value;
	var comments = document.getElementsByName('teamComments')[0].value;
	var payMethod = form.elements['payMethod'].options[form.elements['payMethod'].options.selectedIndex].value;
	
	if(leagueID == 10000) { 
		errormessage += "\n\nPlease select a league from the dropdown list."; 
	}
	if(!(capFirst.length > 2 && capFirst.length < 21)) { 
		errormessage += "\n\nPlease enter a captain first name between 3 and 20 characters in length."; 
	}
	if(!(capLast.length > 2 && capLast.length < 31)) { 
		errormessage += "\n\nPlease enter a captain last name between 3 and 30 characters in length."; 
	}
	if(capGender == '') { 
		errormessage += "\n\nPlease enter a captain gender."; 
	}
	if(!validateEmail(capEmail)) { 
		errormessage += "\n\nPlease enter a valid captains email."; 
	}
	if(!(comments.length > 2 && comments.length < 1001) && comments.length != 0) { 
		errormessage += "\n\nPlease enter a comment between 3 and 1000 characters."; 
	}
	if(payMethod == 0) {
		errormessage += "\n\nPlease enter a payment method."; 
	}
	return errormessage; 
} 

function showTextbox(self) {
	var textRow = document.getElementById('hearTextRow');
	if(self.value == 9) {
		textRow.style.display = 'table-row';
	} else {
		textRow.style.display = 'none';
	}
}

function reloadPageCard() {
	var form = document.getElementById('tourneyReg');
	var tourneyID = form.elements['tourneyID'].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='signupTournament.php?tournamentID=' + tourneyID + '&leagueID=' + leagueID;
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 