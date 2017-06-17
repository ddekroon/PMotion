<?php

//Variable declarations based on what sport is being registered for:

//$type= Common name (proper capitalization) of the sport "../register.PHP?sport='soccer'" .... really it's "6 vs 6 Soccer" etc
//$logo= Image path (and sometimes resizing information) for the logos
//$sportHeader= Title seen on the form
//$titleHeader= Title seen in the browser window
//$people= Number of player spots allotted on the registration form
//$filter= For the purpose of the query, you should never have to touch this

if($tournament=='wheresthebeach'){
        $logo="http://www.perpetualmotion.org/WherestheBeach/Logos/Where%27sThe%20BeachTournamentlogo2.jpg";
        $logoHeight=104;
		$logoWidth=216;
		$sportHeader="<br>Register for the Where's the Beach Volleyball Tournament";
        $titleHeader="Register - Where's The Beach Volleyball Tournament";
        $filter = "WHERE divisionID < 100";
}elseif($tournament=='stallfall'){
        $logo="http://www.perpetualmotion.org/StallFall/Logos/FallStallClassic.jpg";
        $logoHeight=76;
		$logoWidth=207;
		$sportHeader="<br>Register for Guelph Ultimate's Stall Fall Classic Tournament";
        $titleHeader="Register - Stall Fall Classic";
        $people=12;
        $filter = "WHERE divisionID > 100 AND divisionID < 200";
}elseif($tournament=='beaverbowl'){
        $logo="http://www.perpetualmotion.org/BeaverBowl/Logos/BeaverBowllogo.jpg";
        $logoHeight=68;
		$logoWidth=216;
		$sportHeader="<br>Register for Perpetual Motion's Beaver Bowl Flag Football Tournament";
        $titleHeader="Register - Beaver Bowl";
        $filter = "WHERE divisionID > 200 AND divisionID < 300";
}elseif($tournament=='worldcup'){
        $logo="http://www.perpetualmotion.org/WorldCup/Logos/WorldCup.jpg";
		$logoHeight=139;
		$logoWidth=360;
        $sportHeader="<br>Register for Perpetual Motion's World Cup Challenge";
        $titleHeader="Register - World Cup Challenge";
        $filter = "WHERE divisionID > 300 AND divisionID < 400";
}elseif($tournament=='dodgeball'){
        $logo="http://www.perpetualmotion.org/DodgeBall/Logos/Dodgeballlogo.jpg";
		$logoHeight=65;
		$logoWidth=252;
        $sportHeader="<br>Register for Perpetual Motion's Dodgeball Tournament";
        $titleHeader="Register - Guelph Dodgeball Tournament";
        $filter = "WHERE divisionID > 400 AND divisionID < 500";
}elseif($tournament=='discgolf'){
        $logo="http://www.perpetualmotion.org/DiscGolf/Logos/guelphdiscgolflogo.jpg";
		$logoHeight=56;
		$logoWidth=216;
        $sportHeader="<br>Register for the Spring Fling Disc Golf Tournament";
        $titleHeader="Register - Spring Fling Disc Golf Tournament";
        $filter = "WHERE divisionID > 500 AND divisionID<600";
}

//end declarations

?>
