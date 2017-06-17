<?php
//function that displays a check mark or x and hyper links the image to the registration page for each sport
function hyperlinkReg($sportNumber,$leagueFullCheck){
	if($leagueFullCheck == 0){
		switch ($sportNumber){
			case 1:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/ultimate/registration/register-individual'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 2:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/volleyball/registration/register-individual'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 3:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/football/registration/register-individual'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 4:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/soccer/registration/register-individual'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
		}
	}else{
		echo "<img src='images/close-button.jpg' style='width:20px;height:20px;'>";
	}
	return;
	
}

function hyperlinkTeam($sportNumber, $leagueFullCheck){
	if($leagueFullCheck == 0){
		switch ($sportNumber){
			case 1:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/ultimate/registration/register-team'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 2:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/volleyball/registration/register-team'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 3:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/football/registration/register-team'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
			case 4:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/soccer/registration/register-team'><img src='images/mark.jpg' style='width:20px;height:20px;'>";
				break;
		}
	}else{
		echo "<img src='images/close-button.jpg' style='width:20px;height:20px;'>";
	}
	return;
	
}

function printTableRows($sportNumber,$leagueName) {
	switch ($sportNumber){
			case 1:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/ultimate/registration'>",$leagueName;
				break;
			case 2:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/volleyball/registration'>",$leagueName;
				break;
			case 3:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/football/registration'>",$leagueName;
				break;
			case 4:
				echo "<a target='_parent' href='http://www.perpetualmotion.org/soccer/registration'>",$leagueName;
				break;
		}
	return;
}
?>