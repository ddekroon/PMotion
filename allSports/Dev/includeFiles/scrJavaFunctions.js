function checkFields(matchnum, gamenum) {
	var errormessage = new String();
	
	var results = new Array(10);
	var spirit = new Array(10);
	var oppIDs = new Array(4);
	var resultString = new String();
	var spiritString = new String();
	var curGame = 0;
	var gameNumber = 0
	var matchNumber = 0;
	var isPlayoffs = document.getElementsByName('isPlayoffs')[0].value;
	
	var form = document.getElementById('scoreReporterID');
	var leagueID = form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID = form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	
	var oppIDObjs = document.getElementsByName('oppID[]');
	for(var i=0;i<matchnum;i++) {
		oppIDs[i] = oppIDObjs[i].options[oppIDObjs[i].options.selectedIndex].value;
	}
	var comments = document.getElementsByName('matchComments[]');
	for(var i = 0; i<matchnum;i++) {
		spiritString = 'spiritScore['+i+']';
		spirit[i] = document.getElementsByName(spiritString);	
	}
	
	for(var k=0; k<matchnum*gamenum;k++) {
		resultString = 'results[' + k + ']';
		results[k] = document.getElementsByName(resultString);
	}
	
	if(leagueID == 0) { 
		errormessage += "\n\nPlease select a league from the dropdown list."; 
	}
	if(teamID == 0) { 
		errormessage += "\n\nPlease select your team from the dropdown list."; 
	}
	if(isPlayoffs == 0) {
		for (var i = 0; i < matchnum; i++){
			matchNumber = i+1
			if(oppIDs[i] != 1){
				for(var j = 0; j < gamenum; j++){
					gameNumber = j +1;
					if(NoneWithCheck(results[curGame])){
						if(matchnum == 1){
							errormessage += "\n\nPlease select your results in game "+gameNumber;
						}else{
							errormessage += "\n\nPlease select your results in game "+gameNumber+" of match "+matchNumber;
						}
					}
					if(getCheckedValue(results[curGame])==4){
						if(comments[i].value==""){
							errormessage += "\n\nPlease submit a comment explaining the default/cancellation in game "+gameNumber+" of match "+matchNumber;
						}
					}
			    }
				if (checkRadio(spirit[i]) == true ){
					if (comments[i].value=="")
					{
						//errormessage += "\n\nA spirit score less than 4 is considered a low score.";
						errormessage += "\n\nPlease submit a comment explaining the low spirit score in match "+ matchNumber;
					}
				}
			}
			curGame = curGame + 1;
		}
	
		if(matchnum==1){
			if(teamID == oppIDs[0]){
				errormessage += "\n\nSubmitting team cannot be the same as its opponent.";
			}
		}
		
		if(matchnum==2){
			if(teamID==oppIDs[0] || teamID == oppIDs[1]) {
				errormessage += "\n\nSubmitting team cannot be the same as either of its opponents";
			}
			if(oppIDs[0] == oppIDs[1]){
				errormessage += "\n\nOpponent cannot be the same as another opponent.";
			}
		}
			
		for (var k=0; k < matchnum; k++){
			gameNumber = k+1;
			if(oppIDs[k] == 0) { 
				errormessage += "\n\nPlease select your opponent in game "+gameNumber+" from the dropdown list."; 
			}
		}
	}
	
	var submitterName = document.getElementsByName('submitterName')[0].value;
	if(submitterName == ''){ 
		errormessage += "\n\nPlease type something in the \"submitted by (name)\" text field."; 
	}
	
	if(errormessage.length > 2) {
	  alert('NOTE:' + errormessage);
	  return false;
	}
	return true;
        
} 

function checkRadio(radioObj) {
	for(var i=0; i < 6; i++) { 
		if(radioObj[i].checked) { 
			return true;
		}
	}
	return false;
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return 0;
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return 0;
		for(var i = 0; i < radioLength; i++) {
			if(radioObj[i].checked) {
				return radioObj[i].value;
			}
		}
		return 0;
}


function NoneWithCheck(buttons) {
	for(var h = 0; h < buttons.length; h++) {
		if(buttons[h].checked) { 
			return false; 
		}
	}
	return true;
}

function WithoutSelectionValue(ss) {
	for(var i = 0; i < ss.length; i++) {
		if(ss[i].selected) {
			if(ss[i].value.length) { 
				return false; 
			}
		}
	}
	return true;
}

function reloadPage() {
	var form = document.getElementById('scoreReporterID');
	var sportID = form.elements['sportID'].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	self.location='scoreReporter.php?sportID=' + sportID + '&leagueID=' + leagueID;
}

function secondReload() {
	var form = document.getElementById('scoreReporterID');
	var sportID= form.elements['sportID'].value;
	var leagueID= form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var teamID= form.elements['teamID'].options[form.elements['teamID'].options.selectedIndex].value;
	self.location='scoreReporter.php?sportID=' + sportID +'&leagueID=' + leagueID +'&teamID=' + teamID ;
}