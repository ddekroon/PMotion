
function checkYesNoSave() 
{
	return confirm("Are you sure you want to change these standings?");
}

function reloadSport() 
{
	var form = document.getElementById('teamForm');
	var sportID=form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	self.location = 'tieBreak.php?sportID=' + sportID;
}

// When a drop down is changed it changes the URL and updates variables
function reloadPage() 
{
	var form = document.getElementById('teamForm');
	var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
	var seasonID = form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
	var leagueID=form.elements['leagueID'].options[form.elements['leagueID'].options.selectedIndex].value;
	var team1ID=form.elements['team1ID'].options[form.elements['team1ID'].options.selectedIndex].value;
	var team2ID=form.elements['team2ID'].options[form.elements['team2ID'].options.selectedIndex].value;

	self.location = 'tieBreak.php?sportID=' + sportID + '&seasonID=' + seasonID + '&leagueID=' + leagueID+ '&team1ID=' + team1ID + '&team2ID=' + team2ID;
}
