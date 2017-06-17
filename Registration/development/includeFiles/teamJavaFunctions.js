var regCheck = 0;

function errorCheck() {
	var errormessage = new String();
	
	var form = document.getElementById('update');
	var leagueID = form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamName = document.getElementsByName('teamName')[0].value;
	var capFirst = document.getElementsByName('capFirst')[0].value;
	var capLast = document.getElementsByName('capLast')[0].value;
	var capGender = form.elements['capSex'].options[form.elements['capSex'].options.selectedIndex].value;
	var capEmail = document.getElementsByName('capEmail')[0].value;
	var capPhone = document.getElementsByName('capPhone')[0].value;
	capPhone = capPhone.replace(/[^0-9.]/g, "");
	var playerEmails = document.getElementsByName('playerEmail[]');
	var comments = document.getElementsByName('teamComments')[0].value;
	
	if(leagueID == 0) { 
		errormessage += "\n\nPlease select a league from the dropdown list."; 
	}
	if(!(teamName.length > 2 && teamName.length < 41)) { 
		errormessage += "\n\nPlease enter a team name between 3 and 40 characters in length."; 
	}
	if(!(capFirst.length > 2 && capFirst.length < 21)) { 
		errormessage += "\n\nPlease enter a captain first name between 3 and 20 characters in length."; 
	}
	if(!(capLast.length > 2 && capLast.length < 31)) { 
		errormessage += "\n\nPlease enter a captain last name between 3 and 30 characters in length."; 
	}
	if(!validateEmail(capEmail)) { 
		errormessage += "\n\nPlease enter a valid captains email."; 
	}
	if(!(capPhone.length == 11 && capPhone[0] == 1 || capPhone.length == 10)) { 
		errormessage += "\n\nPlease enter a valid phone number."; 
	}
	if(!(comments.length > 2 && comments.length < 1001) && comments.length != 0) { 
		errormessage += "\n\nPlease enter a comment between 3 and 1000 characters."; 
	}
	var isAnotherEmail = 0;
	for(var i = 0 ; i < playerEmails.length; i++) {
		if(validateEmail(playerEmails[i].value)) {
			isAnotherEmail = 1;
		}
	}
	if(isAnotherEmail == 0) {
		errormessage += "\n\nPlease enter a second email to be used as an alternate contact."; 
	}
	
	if(errormessage.length > 2) {
	  alert('NOTE:' + errormessage);
	  return false;
	}
	return true;   
} 

function errorCheckSecond() {
	var errormessage = new String();
	var form = document.getElementById('update');
	var payMethod = form.elements['payMethod'].options[form.elements['payMethod'].options.selectedIndex].value;
	var isReg = document.getElementsByName('isRegistered')[0].value;
	if(payMethod == 0 && isReg != 0) {
		errormessage += "\n\nPlease enter a payment method."; 
	}
	
	if(errormessage.length > 2) {
	  alert('NOTE:' + errormessage);
	  return 1;
	}
	return 0;
}

function checkUpdate() {
	if(errorCheck() == true) { //No errors
		if(errorCheckSecond() == 0) { //no errors again, reload so data can be submitted
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function showPayment() {
	var errorVar = errorCheck();
	var errorVar2 = 0;
	if(errorVar == true) { //No errors
		if(regCheck == 1) { //second time clicked
			errorVar2 = errorCheckSecond();
			if(errorVar2 == 0) { //no errors again, reload so data can be submitted
				return true;
			} else {
				return false;
			}
		} else {
			document.getElementById('payInfo').style.display = "table-row";
			regCheck++;
			return false;
		}
	} else {
		return false;
	}
}
function showTextbox(self) {
	var textRow = document.getElementById('hearTextRow');
	if(self.value == 8) {
		textRow.style.display = 'table-row';
	} else {
		textRow.style.display = 'none';
	}
}

function updtShowPayMethod(self) {
	var payMethod = document.getElementsByName('payMethod')[0];
	if(self.value == 1) { //show it
		document.getElementById('updtPayMethod').style.display = "table-row";
	} else {
		document.getElementById('updtPayMethod').style.display = "none";
		payMethod.value = 0;
	}
}

function setBlanks() {
	var playerFirsts = document.getElementsByName('playerFirst[]');
	var playerLasts = document.getElementsByName('playerLast[]');
	var playerEmails = document.getElementsByName('playerEmail[]');
	for(var i = 0; i < playerFirsts.length; i++) {
		if(playerFirsts[i].length == 0) {
			playerFirsts[i].value = '-';
		}
		if(playerLasts[i].length == 0) {
			playerLasts[i].value = '-';
		}
		if(playerEmails[i].length == 0) {
			playerEmails[i].value = '-';
		}
	}
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 