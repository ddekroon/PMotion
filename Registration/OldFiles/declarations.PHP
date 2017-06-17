<?php

//Variable declarations based on what sport is being registered for:

//$type= Common name (proper capitalization) of the sport "../register.PHP?sport='soccer'" .... really it's "6 vs 6 Soccer" etc
//$logo= Image path (and sometimes resizing information) for the logos
//$sportHeader= Title seen on the form
//$titleHeader= Title seen in the browser window
//$people= Number of player spots allotted on the registration form
//$filter= For the purpose of the query, you should never have to touch this

if($sport=='ultimate'){
        $type="Ultimate";
        $logo="/Logos/GuelphUltimate.jpg";
        $sportHeader="<br>Register as a team for Guelph Ultimate";
        $titleHeader="Register - Guelph Ultimate";
        $people=15;
        $filter = "WHERE cat_id > 100 AND cat_id < 200";
}elseif($sport=='beach'){
        $type="Beach Volleyball";
        $logo="/Logos/WheresTheBeach.jpg";
        $sportHeader="<br>Register as a team for Where's The Beach Volleyball";
        $titleHeader="Register - Where's The Beach Volleyball";
        $people=14;
        $filter = "WHERE cat_id >0 AND cat_id < 100";
}elseif($sport=='football'){
        $type="Flag Football";
        $logo="/Logos/GuelphFlagFootball.jpg";
        $sportHeader="<br>Register as a team for Guelph Flag Football";
        $titleHeader="Register - Guelph Flag Football";
        $people=12;
        $filter = "WHERE cat_id > 200 AND cat_id < 300";
}elseif($sport=='soccer'){
        $type="Soccer";
        $logo="'/Soccer/Logos/6vs6 SoccerFinal1.jpg' width=170 height=88";
        $sportHeader="<br>Register as a team for Guelph Soccer";
        $titleHeader="Register - Guelph Soccer";
        $people=15;
        $filter = "WHERE cat_id > 300";
}

//end declarations

?>
