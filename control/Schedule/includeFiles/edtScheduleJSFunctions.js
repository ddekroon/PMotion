window.checkAuto = 0;

$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

function reloadCreatePage() {
	var resultForm = document.getElementById('Schedule');
	var sportID= resultForm.elements['sportID'].options[resultForm.elements['sportID'].options.selectedIndex].value;
	var seasonID= resultForm.elements['seasonID'].options[resultForm.elements['seasonID'].options.selectedIndex].value;
	var leagueID= resultForm.elements['leagueID'].options[resultForm.elements['leagueID'].options.selectedIndex].value;
	self.location='editSchedule.php?sportID=' + sportID +'&seasonID=' + seasonID+'&leagueID=' + leagueID;
}

function changeNumWeeks(self) {
	var masterTimesNum = document.getElementById('numTimesMaster').value;
	var timeRows = document.getElementsByName('weekTimes[]');
	var venueRows = document.getElementsByName('weekVenues[]');
	var sameTimes = document.getElementById('timesSame').value;
	var maxWeeks = document.getElementById('maxWeeks').value;
	var numWeeks = document.getElementById('numWeeks').value;
	var maxVenues = document.getElementById('maxVenues').value;
	var sameVenues = document.getElementById('venuesSame').value;
	var numVenues = document.getElementById('numVenuesMaster').value;
	var weekHeads = document.getElementsByName('weekHeads[]');
	var weekRows = document.getElementsByName('weekTables[]');
	var venues = new Array(); 
	
	for(var i = 0; i < maxWeeks; i++) {
		if(((sameVenues == 0 || sameTimes == 0) && i < numWeeks) || ((sameVenues == 1 ||sameTimes == 1) && i == 0)) {
			weekRows[i].style.display = 'inline';
			weekRows[i].style.borderBottom = 'thin #000 dashed';
			weekHeads[i].style.display = 'inline';
		} else {
			weekRows[i].style.display = 'none';
			weekHeads[i].style.display = 'none';
		}
		
		if((sameTimes == 0 && i < numWeeks) || (sameTimes == 1 && i == 0) ){
			timeRows[i].style.display = 'inline';
			timeRows[i].style.width = '100%';
		} else {
			timeRows[i].style.display = 'none';	
			
		}
		for(var j = 0; j < maxVenues; j++) {
			if((i < numWeeks && sameVenues == 0) || (i == 0 && sameVenues == 1)) {
				venueRows[i].style.display = 'inline';
				venueRows[i].style.width = '100%';
			} else {
				venueRows[i].style.display = 'none';	
			}
		}
	}
	
	
}

function changeSameTimes(self) {
	var numTimes = document.getElementById('numTimesMaster').value;
	var numVenues = document.getElementById('numVenuesMaster').value;
	var timeRows = document.getElementsByName('weekTimes[]');
	var sameTimes = self.value;
	var maxWeeks = document.getElementById('maxWeeks').value;
	var numWeeks = document.getElementById('numWeeks').value;
	var maxTimes = document.getElementById('maxTimes').value;
	var weekHeads = document.getElementsByName('weekHeads[]');
	var weekRows = document.getElementsByName('weekTables[]');
	var sameVenues = document.getElementById('venuesSame').value;
	var timeColumns = new Array;
	
	if(sameTimes == 0) {
		for(var i = 0; i < maxWeeks; i++) {
			if(i < numWeeks) {
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
				weekHeads[i].style.display = 'inline';
			} else {
				weekHeads[i].style.display = 'none';
			}
			timeColumns = document.getElementsByName('timeColumn['+i+'][]');
			for(var j = 0; j < maxTimes; j++) {
			  if(i < numWeeks && j < numTimes) {
					timeColumns[j].style.display = 'inline';
				} else {
					timeColumns[j].style.display = 'none';	
				}
			}
			if(i < parseInt(numWeeks)) {
				timeRows[i].style.display = 'inline';
			} else {
				timeRows[i].style.display = 'none';
			}
		}
	} else {
		for(var i = 0; i < maxWeeks; i++) {
			timeColumns = document.getElementsByName('timeColumn['+i+'][]');
			for(var j = 0; j < maxTimes; j++) {
			  if(i == 0 && j < numTimes) {
					timeColumns[j].style.display = 'inline';
				} else{
					timeColumns[j].style.display = 'none';	
				}
			}
			if(i == 0) {
				timeRows[i].style.display = 'inline';
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
				weekHeads[i].style.display = 'inline';
			} else {
				timeRows[i].style.display = 'none';
				if(sameVenues == 1) {
					weekHeads[i].style.display = 'none';
				}
			}
		}

	}
	
	
}

function setNumTimes(self) {
	var maxTimes = document.getElementById('maxTimes').value;
	var sameTimes = document.getElementById('timesSame').value;
	var maxWeeks = document.getElementById('maxWeeks').value;
	var numWeeks = document.getElementById('numWeeks').value;
	var weekHeads = document.getElementsByName('weekHeads[]');
	var weekRows = document.getElementsByName('weekTables[]');
	
	var timeColumns = new Array;
	var numTimes = self.value;
	if(sameTimes == 0) {
		for(var i = 0; i < maxWeeks; i++) {
			if(i < numWeeks) {
				
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
weekHeads[i].style.display = 'inline';
			} else {
				weekHeads[i].style.display = 'none';
			}
			timeColumns = document.getElementsByName('timeColumn['+i+'][]');
			for(var j = 0; j < maxTimes; j++) {
				if(j < numTimes) {
					timeColumns[j].style.display = 'inline';
				} else {
					timeColumns[j].style.display = 'none';	
				}
			}
		}
	} else {
		for(var i = 0; i < maxWeeks; i++) {
			timeColumns = document.getElementsByName('timeColumn['+i+'][]');
			if(i == 0) {
				
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
weekHeads[i].style.display = 'inline';
				for(var j = 0; j < maxTimes; j++) {
					if(j < numTimes) {
						timeColumns[j].style.display = 'inline';
					} else {
						timeColumns[j].style.display = 'none';
					}
				}
			} else {
				weekHeads[i].style.display = 'none';
			}
		}
	}
}

function changeSameVenues(self) {
	var masterVenuesNum = document.getElementById('numVenuesMaster').value;
	var venueRows = document.getElementsByName('weekVenues[]');
	var sameVenues = self.value;
	var maxWeeks = document.getElementById('maxWeeks').value;
	var numWeeks = document.getElementById('numWeeks').value;
	var maxVenues = document.getElementById('maxVenues').value;
	var numVenues = document.getElementById('numVenuesMaster').value;
	var weekHeads = document.getElementsByName('weekHeads[]');
	var weekRows = document.getElementsByName('weekTables[]');
	var sameTimes = document.getElementById('timesSame').value;
	
	if(parseInt(numWeeks) > parseInt(maxWeeks)) {
		alert('ERROR, numWeeks > maxWeeks, maxWeeks = ' + maxWeeks);
		exit(0);
	}
	if(sameVenues == 0) {
		for(var i = 0; i < maxWeeks; i++) {
			if(i < numWeeks) {
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
				weekHeads[i].style.display = 'inline';
			} else {
				weekHeads[i].style.display = 'none';
			}
			venues = document.getElementsByName('venueRows['+i+'][]');
			if(i < numWeeks) {
				venueRows[i].style.display = 'inline';
			} else {
				venueRows[i].style.display = 'none';	
			}
			for(var j = 0; j < maxVenues; j++) {
				if(i < numWeeks && j < numVenues) {
					venues[j].style.display = 'inline';
				} else {
					venues[j].style.display = 'none';	
				}
			}
		}
	} else {
		for(var i = 0; i < maxWeeks; i++) {
			venues = document.getElementsByName('venueRows['+i+'][]');
			if(i == 0) {
				venueRows[i].style.display = 'inline';
				weekRows[i].style.display = 'inline';
				weekRows[i].style.border = "thin #000 dashed";
				weekHeads[i].style.display = 'inline';
			} else {
				venueRows[i].style.display = 'none';
				alert(sameTimes);
				if(sameTimes == 1) {
					weekHeads[i].style.display = 'none';
				}
			}
			for(var j = 0; j < maxVenues; j++) {
				if(i == 0 && j < numVenues) {
					venues[j].style.display = 'inline';
				} else {
					venues[j].style.display = 'none';	
				}
			}
		}
	}
	return false;
}

function setNumVenues(self) {
	var maxVenues = document.getElementById('maxVenues').value;
	var maxWeeks = document.getElementById('maxWeeks').value;
	var sameVenues = document.getElementById('venuesSame').value;
	var numWeeks = document.getElementById('numWeeks').value;
	var masterVenuesNum = document.getElementById('numVenuesMaster').value;
	var weekHeads = document.getElementsByName('weekHeads[]');
	var weekRows = document.getElementsByName('weekTables[]');
	var venues = new Array(); 
	var numVenues = self.value;
	
	if(sameVenues == 0) {
		for(var i = 0; i < maxWeeks; i++) {
			if(i < numWeeks) {
				weekRows[i].style.display = 'inline';
				weekRows[i].style.borderBottom = 'thin #000 dashed';
				weekHeads[i].style.display = 'inline';
			} else {
				weekHeads[i].style.display = 'none';
			}
			venues = document.getElementsByName('venueRows['+i+'][]');
			for(var j = 0; j < maxVenues; j++) {
				if(i < numWeeks && j < numVenues) {
					venues[j].style.display = 'inline';
				} else {
					venues[j].style.display = 'none';	
				}
			}
		}
	} else {
		for(var i = 0; i < maxWeeks; i++) {
			venues = document.getElementsByName('venueRows['+i+'][]');
			for(var j = 0; j < maxVenues; j++) {
				if(i == 0 && j < numVenues) {
					venues[j].style.display = 'inline';
					weekRows[i].style.display = 'inline';
					weekRows[i].style.borderBottom = 'thin #000 dashed';
					weekHeads[i].style.display = 'inline';
				} else {
					venues[j].style.display = 'none';
					weekHeads[i].style.display = 'none';	
				}
			}
		}
	}
}

function setVenuesMaster(self, fieldNum) {
	var maxWeeks = document.getElementById('maxWeeks').value;
	var weekVenues = new Array();
	var toSelect = self.options.selectedIndex;
	for(var i = 0; i < maxWeeks; i++) {
		weekVenues = document.getElementsByName("venuesDD[" + i + "][]");
		weekVenues[fieldNum].options[toSelect].selected = true;
	}
}

function setAuto(self) {
	if(self.checked == true) {
		window.checkAuto = 1;
	} else {
		window.checkAuto = 0;
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

function checkDelete() {
    return confirm("Are you sure you want to delete these teams?");
}

function reloadSport(self) {
			this.location = 'pendingSchedule.php?sportID=' + self.value;	
		}
		function reloadLeague(self) {
			var sportID = document.getElementsByName('sportID')[0].value;
			
			this.location = 'pendingSchedule.php?sportID=' + sportID + '&leagueID=' + self.value;
		}