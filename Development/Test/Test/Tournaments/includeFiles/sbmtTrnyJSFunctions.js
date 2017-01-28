function reloadCreatePage() {
	var resultForm = document.getElementById('results');
	var tourneyID= resultForm.elements['tourneyID'].options[resultForm.elements['tourneyID'].options.selectedIndex].value;
	var tourneyAvailableID= resultForm.elements['tourneyAvailableID'].value;
	self.location='submitTournament.php?tourneyID=' + tourneyID;
}

function changeNumLeagues(isLeagues) {
	var numLeagues = document.getElementsByName('tourneyNumLeagues')[0];
	var leagueNames = document.getElementsByName('tourneyLeagueName[]');
	var isTeams = document.getElementsByName('tourneyIsTeams')[0].value;
	var numTeams = document.getElementsByName('tourneyNumTeams[]');
	var isPlayers = document.getElementsByName('tourneyIsPlayers')[0].value;
	var numPlayers = document.getElementsByName('tourneyNumPlayers[]');
	var teamsLabel = document.getElementById('teamsTitle');
	var leaguesLabel = document.getElementById('leaguesTitle');
	var playersLabel = document.getElementById('playersTitle');
	var isCards = document.getElementsByName('tourneyIsCards')[0].value;
	var numBlack = document.getElementsByName('tourneyNumBlackCards[]');
	var numRed = document.getElementsByName('tourneyNumRedCards[]');
	var isFullMale = document.getElementsByName('tourneyIsFullMale[]');
	var isFullFemale = document.getElementsByName('tourneyIsFullFemale[]');
	var isFull = document.getElementsByName('tourneyIsFull[]');
	var cardsLabel = document.getElementById('cardsTitle');
	var leaguePrices = document.getElementsByName('tourneyLeaguePrices[]');
	
	if(isLeagues.value == 1) {
		leaguesLabel.innerHTML = 'League Names';
		if(isTeams == 1) {
			teamsLabel.innerHTML = 'Num Teams Per League';
			for(var i=0;i<numLeagues.value;i++) {
				numTeams[i].style.display = 'inline';
				isFull[i].style.display = 'inline';
			}
		}
		if(isPlayers == 1) {
			playersLabel.innerHTML = 'Num Players Per League';
			for(var i=0;i<numLeagues.value;i++) {
				numPlayers[i].style.display = 'inline';
				isFullMale[i].style.display = 'inline';
				isFullFemale[i].style.display = 'inline';
			}
		}
		if(isCards == 1) {
			cardsLabel.innerHTML = 'Num Cards Per League(B-R)';
			for(var i=0;i<numLeagues.value;i++) {
				numBlack[i].style.display = 'inline';
				numRed[i].style.display = 'inline';
				isFullMale[i].style.display = 'inline';
				isFullFemale[i].style.display = 'inline';
			}
		}
		numLeagues.disabled = false;
		for(var i=0;i<numLeagues.value;i++) {
			leagueNames[i].style.display = 'inline';
			leaguePrices[i].style.display = 'inline';
		}
	} else {
		leaguesLabel.innerHTML = '';
		if(isTeams == 1) {
			teamsLabel.innerHTML = 'Num Teams';
			numTeams[0].style.display = 'inline';
			for(var i=1;i<numLeagues.value;i++) {
				numTeams[i].style.display = 'none';
				isFull[i].style.display = 'none';
			}
		}
		if(isPlayers == 1) {
			playersLabel.innerHTML = 'Num Players';
			numPlayers[0].style.display = 'inline';
			for(var i=1;i<numLeagues.value;i++) {
				numPlayers[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		if(isCards == 1) {
			cardsLabel.innerHTML = 'Num Cards(B-R)';
			numBlack[0].style.display = 'inline';
			numRed[0].style.display = 'inline';
			for(var i=1;i<numLeagues.value;i++) {
				numBlack[i].style.display = 'none';
				numRed[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		numLeagues.disabled = true;
		for(var i=0;i<numLeagues.value;i++) {
			leagueNames[i].style.display = 'none';
			if(i == 0) {
				leaguePrices[i].style.display = 'inline';
			} else {
				leaguePrices[i].style.display = 'none';
			}
		}
	}
}

function showLeagueNames(numLeagues) {
	var leagueNames = document.getElementsByName('tourneyLeagueName[]');
	var numTeams = document.getElementsByName('tourneyNumTeams[]');
	var isTeams = document.getElementsByName('tourneyIsTeams')[0].value;
	var isPlayers = document.getElementsByName('tourneyIsPlayers')[0].value;
	var numPlayers = document.getElementsByName('tourneyNumPlayers[]');
	var isCards = document.getElementsByName('tourneyIsCards')[0].value;
	var numBlack = document.getElementsByName('tourneyNumBlackCards[]');
	var numRed = document.getElementsByName('tourneyNumRedCards[]');
	var isFullMale = document.getElementsByName('tourneyIsFullMale[]');
	var isFullFemale = document.getElementsByName('tourneyIsFullFemale[]');
	var isFull = document.getElementsByName('tourneyIsFull[]');
	var leaguePrices = document.getElementsByName('tourneyLeaguePrices[]');
	
	for(var i=0;i<8;i++) {
		if(i <numLeagues.value) {
			leagueNames[i].style.display = 'inline';
			if(isTeams == 1) {
				numTeams[i].style.display = 'inline';
				isFull[i].style.display = 'inline';
			}
			if(isPlayers == 1) {
				numPlayers[i].style.display = 'inline';
				isFullMale[i].style.display = 'inline';
				isFullFemale[i].style.display = 'inline';
			}
			if(isCards == 1) {
				numBlack[i].style.display = 'inline';
				numRed[i].style.display = 'inline';
				isFullMale[i].style.display = 'inline';
				isFullFemale[i].style.display = 'inline';
			}
			leaguePrices[i].style.display = 'inline';
		} else {
			leagueNames[i].style.display = 'none';
			numTeams[i].style.display = 'none';
			numPlayers[i].style.display = 'none';
			numBlack[i].style.display = 'none';
			numRed[i].style.display = 'none';
			leaguePrices[i].style.display = 'none';
			isFullMale[i].style.display = 'none';
			isFullFemale[i].style.display = 'none';
			isFull[i].style.display = 'none';
		}
	}
}

function changeNumTeams(isTeams) {
	var isCards = document.getElementsByName('tourneyIsCards')[0];
	var numBlack = document.getElementsByName('tourneyNumBlackCards[]');
	var numRed = document.getElementsByName('tourneyNumRedCards[]');
	var cardsLabel = document.getElementById('cardsTitle');
	var numTeams = document.getElementsByName('tourneyNumTeams[]');
	var isPlayers = document.getElementsByName('tourneyIsPlayers')[0];
	var numPlayers = document.getElementsByName('tourneyNumPlayers[]');
	var isLeagues = document.getElementsByName('tourneyIsLeagues')[0].value;
	var numLeagues = document.getElementsByName('tourneyNumLeagues')[0];
	var teamsLabel = document.getElementById('teamsTitle');
	var playersLabel = document.getElementById('playersTitle');
	var isFullMale = document.getElementsByName('tourneyIsFullMale[]');
	var isFullFemale = document.getElementsByName('tourneyIsFullFemale[]');
	var isFull = document.getElementsByName('tourneyIsFull[]');
	
	if(isTeams.value == 1) {
		if(isPlayers.value == 1) {
			isPlayers.value = 0;
			playersLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numPlayers[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		if(isCards.value == 1) {
			isCards.value = 0;
			cardsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numBlack[i].style.display = 'none';
				numRed[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		if(isLeagues == 1) {
			teamsLabel.innerHTML = 'Num Teams Per League';
			for(var i =0;i<8;i++) {
				if(i <numLeagues.value) {
					numTeams[i].style.display = 'inline';
					isFull[i].style.display = 'inline';
				} else {
					numTeams[i].style.display = 'none';
					isFull[i].style.display = 'none';
				}
			}
		} else {
			teamsLabel.innerHTML = 'Num Teams';
			numTeams[0].style.display = 'inline';
		}
	} else {
		if(isLeagues == 1) {
			teamsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numTeams[i].style.display = 'none';
			}
		} else {
			teamsLabel.innerHTML = '';
			numTeams[0].style.display = 'none';
		}
	}
}

function changeNumPlayers(isPlayers) {
	var isCards = document.getElementsByName('tourneyIsCards')[0];
	var numBlack = document.getElementsByName('tourneyNumBlackCards[]');
	var numRed = document.getElementsByName('tourneyNumRedCards[]');
	var cardsLabel = document.getElementById('cardsTitle');
	var numPlayers = document.getElementsByName('tourneyNumPlayers[]');
	var isTeams = document.getElementsByName('tourneyIsTeams')[0];
	var numTeams = document.getElementsByName('tourneyNumTeams[]');
	var isLeagues = document.getElementsByName('tourneyIsLeagues')[0].value;
	var numLeagues = document.getElementsByName('tourneyNumLeagues')[0];
	var teamsLabel = document.getElementById('teamsTitle');
	var playersLabel = document.getElementById('playersTitle');
	var isFullMale = document.getElementsByName('tourneyIsFullMale[]');
	var isFullFemale = document.getElementsByName('tourneyIsFullFemale[]');
	var isFull = document.getElementsByName('tourneyIsFull[]');
	
	if(isPlayers.value == 1) {
		if(isTeams.value == 1) {
			isTeams.value = 0;
			teamsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numTeams[i].style.display = 'none';
				isFull[i].style.display = 'none';
			}
		}
		if(isCards.value == 1) {
			isCards.value = 0;
			cardsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numBlack[i].style.display = 'none';
				numRed[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		if(isLeagues == 1) {
			playersLabel.innerHTML = 'Num Players Per League';
			for(var i =0;i<8;i++) {
				if(i <numLeagues.value) {
					numPlayers[i].style.display = 'inline';
					isFullMale[i].style.display = 'inline';
					isFullFemale[i].style.display = 'inline';
				} else {
					numPlayers[i].style.display = 'none';
					isFullMale[i].style.display = 'none';
					isFullFemale[i].style.display = 'none';
				}
			}
		} else {
			playersLabel.innerHTML = 'Num Players';
			numPlayers[0].style.display = 'inline';
			isFullMale[0].style.display = 'inline';
			isFullFemale[0].style.display = 'inline';
		}
	} else {
		if(isLeagues == 1) {
			playersLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numPlayers[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		} else {
			playersLabel.innerHTML = '';
			numPlayers[0].style.display = 'none';
			isFullMale[0].style.display = 'none';
			isFullFemale[0].style.display = 'none';
		}
	}
}

function changeNumCards(isCards) {
	var numBlack = document.getElementsByName('tourneyNumBlackCards[]');
	var numRed = document.getElementsByName('tourneyNumRedCards[]');
	var cardsLabel = document.getElementById('cardsTitle');
	var isPlayers = document.getElementsByName('tourneyIsPlayers')[0];
	var numPlayers = document.getElementsByName('tourneyNumPlayers[]');
	var isTeams = document.getElementsByName('tourneyIsTeams')[0];
	var numTeams = document.getElementsByName('tourneyNumTeams[]');
	var isLeagues = document.getElementsByName('tourneyIsLeagues')[0].value;
	var numLeagues = document.getElementsByName('tourneyNumLeagues')[0];
	var teamsLabel = document.getElementById('teamsTitle');
	var playersLabel = document.getElementById('playersTitle');
	var isFullMale = document.getElementsByName('tourneyIsFullMale[]');
	var isFullFemale = document.getElementsByName('tourneyIsFullFemale[]');
	var isFull = document.getElementsByName('tourneyIsFull[]');
	
	
	if(isCards.value == 1) {
		if(isPlayers.value == 1) {
			isPlayers.value = 0;
			playersLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numPlayers[i].style.display = 'none';
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
			}
		}
		if(isTeams.value == 1) {
			isTeams.value = 0;
			teamsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				numTeams[i].style.display = 'none';
				isFull[i].style.display = 'none';
			}
		}
		if(isLeagues == 1) {
			cardsLabel.innerHTML = 'Num Cards Per League(B-R)';
			for(var i =0;i<8;i++) {
				if(i <numLeagues.value) {
					numBlack[i].style.display = 'inline';
					numRed[i].style.display = 'inline';
					isFullMale[i].style.display = 'inline';
					isFullFemale[i].style.display = 'inline';
				} else {
					numBlack[i].style.display = 'none';
					numRed[i].style.display = 'none';
					isFullMale[i].style.display = 'none';
					isFullFemale[i].style.display = 'none';
				}
			}
		} else {
			cardsLabel.innerHTML = 'Num Cards';
			numBlack[0].style.display = 'inline';
			numRed[0].style.display = 'inline';
			isFullMale[0].style.display = 'inline';
			isFullFemale[0].style.display = 'inline';
		}
	} else {
		if(isLeagues == 1) {
			cardsLabel.innerHTML = '';
			for(var i =0;i<numLeagues.value;i++) {
				isFullMale[i].style.display = 'none';
				isFullFemale[i].style.display = 'none';
				numBlack[i].style.display = 'none';
				numRed[i].style.display = 'none';
				
			}
		} else {
			cardsLabel.innerHTML = '';
			numRed[0].style.display = 'none';
			numBlack[0].style.display = 'none';
			isFullMale[0].style.display = 'none';
			isFullFemale[0].style.display = 'none';
		}
	}
}

function changeExtraField(isExtra) {
	var extraField = document.getElementsByName('tourneyExtraFieldName')[0];
	if(isExtra.value == 1) {
		extraField.disabled = false;
	} else {
		extraField.disabled = true;
	}
}