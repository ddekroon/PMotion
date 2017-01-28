<?php //Variable declarations based on what sport is being registered for:

//$type= Common name (proper capitalization) of the sport "../register.PHP?sport='soccer'" .... really it's "6 vs 6 Soccer" etc
//$logo= Image path (and sometimes resizing information) for the logos
//$sportHeader= Title seen on the form
//$titleHeader= Title seen in the browser window
//$people= Number of player spots allotted on the registration form
//$filter= For the purpose of the query, you should never have to touch this
function declareSportVariables() {
	global $sportID, $logo, $sportHeader, $titleHeader, $people, $update, $leagueID;
	
	if($sportID==1){
		$logo="/Logos/GuelphUltimate.jpg";
		$titleHeader="Register - Guelph Ultimate";
		$sportHeader="Register for Guelph Ultimate";
		$people=15;
	}elseif($sportID==2){
		$logo="/Logos/WheresTheBeach.jpg";
		$titleHeader="Register - Where's The Beach Volleyball";
		$sportHeader="Register for Where's The Beach Volleyball";
		$people=14;
	}elseif($sportID==3){
		$logo="/Logos/GuelphFlagFootball.jpg";
		$titleHeader="Register - Guelph Flag Football";
		$sportHeader="Register for Guelph Flag Football";
		$people=12;
	}elseif($sportID==4){
		$logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
		$titleHeader="Register - Guelph Soccer";
		$sportHeader="Register for Guelph Soccer";
		$people=15;
	}
}

//Gets all the leagues to print based on the season and sport ID
function getLeagueData($seasonID, $sportID) {
	global $leaguesTable;
        
        $leagueNames = array(); //going to store all the leagues to display on the print page
        
        $leagueQuery = mysql_query("SELECT * FROM $leaguesTable WHERE league_sport_id = $sportID AND league_season_id = $seasonID")
            or die('ERROR getting leagues Data - '.mysql_error());
        $i = 0;
        while($leagueArray = mysql_fetch_array($leagueQuery)) {
            $leagueNames[$i] = $leagueArray['league_name'].' - '.dayString($leagueArray['league_day_number']).' ($'.number_format($leagueArray['league_registration_fee'], 2).'/team)'.' ($'.number_format($leagueArray['league_individual_registration_fee'], 2).'/person)';
            $i++;
        }
        return $leagueNames;
}

function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
}