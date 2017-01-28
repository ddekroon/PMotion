<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="STYLESHEET" type="text/css" href="style.css">
<title>Untitled Document</title>
	<?php include ("db-connect.php");?>
</head>

<body>
	<?php
	
	$league_id = 1;
	
	
	
	
    $query = "SELECT
					DISTINCT
					dates_dbtable.date_week_number AS date_week_number
					FROM
					dates_dbtable
					INNER JOIN scheduled_matches_dbtable ON scheduled_matches_dbtable.scheduled_match_date_id = dates_dbtable.date_id
					INNER JOIN teams_dbtable ON teams_dbtable.team_id = scheduled_matches_dbtable.scheduled_match_team_id_1
					WHERE
					teams_dbtable.team_league_id = ".$league_id."
					ORDER BY
					dates_dbtable.date_week_number";
		$result = mysql_query($query);
		$dates = array();
		while($key = mysql_fetch_assoc($result)){
			$dates[] = $key['date_week_number'];
		}
		$number_of_weeks_in_league = count($dates);
		print "The number of weeks in the league is :".$number_of_weeks_in_league."<BR>";
	//end setNumberOfWeeksLeague
	
	$query = "SELECT
					seasons_dbtable.season_name AS season_name,
					leagues_dbtable.league_name AS league_name,
					leagues_dbtable.league_day_number As day_number,
					sports_dbtable.sport_name AS sport_name
					FROM
					leagues_dbtable
					Inner Join seasons_dbtable ON seasons_dbtable.season_id = leagues_dbtable.league_season_id
					Inner Join sports_dbtable ON sports_dbtable.sport_id = leagues_dbtable.league_sport_id
					WHERE
					leagues_dbtable.league_id = ".$league_id;
		
		$result = mysql_query($query);
		$array = mysql_fetch_assoc($result);
		$day_number = $array['day_number'];
		
		$day_number == 1 ? $day = "Monday" 
			: $day_number == 2 ? $day = "Tuesday" 
			: $day_number == 3 ? $day = "Wednesday" 
			: $day_number == 4 ? $day = "Thursday" 
			: $day_number == 5 ? $day = "Friday" 
			: $day_number == 6 ? $day = "Saturday" 
			: $day_number == 7 ? $day = "Sunday" 
			: "";
				
		$schedule_title = $array['sport_name']." ".$day." ".$array['league_name']." ".$array['season_name'];
		print "The title is :".$schedule_title."<BR>";
	
		$query = "SELECT
					*
					FROM
					teams_dbtable
					WHERE
					teams_dbtable.team_league_id =  ".$league_id."
					ORDER BY
					teams_dbtable.team_num_in_league";
		$result = mysql_query($query);
		while($key = mysql_fetch_assoc($result)){
			if(substr($key['team_name'],0,2)!="00"){
				$team_array[] = "Team ".$key['team_num_in_league']." - ".$key['team_name'];
			}
		}
		$break_team_list_point = round(count($team_array)/2);
		
		$team_list_table = "<TABLE>";
		
		for($x = 0; $x < $break_team_list_point; $x++){
			$y = $x + $break_team_list_point;
			$team_list_table .= "<TR>
									<TD>$team_array[$x]</TD>
									<TD>$team_array[$y]</TD>
								</TR>";
		}//end for
		
		$team_list_table .= "</TABLE>";
	
	print "Team List is:<BR>".$team_list_table;
	
	?>
</body>
</html>