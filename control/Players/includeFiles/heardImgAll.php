<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php'); 

	if(($yearNum = $_GET['yearNum']) == '') {
		$yearNum = 0;
	}
	$method[1] = 'Internet Search';
	$method[2] = 'Facebook';
	$method[3] = 'Kijiji';
	$method[4] = 'Returning Player';                       
	$method[5] = 'From a Friend';
	$method[6] = 'Restraunt Ad';
	$method[7] = 'Comunity Guide';
	$method[8] = 'Other';
	
	for($i=1;$i<9;$i++) {
		$teamQuery = mysql_query("SELECT * FROM $playersTable INNER JOIN $teamsTable ON $teamsTable.team_id = $playersTable.player_team_id 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
			WHERE player_hear_method = $i  AND player_is_captain = 1 AND season_year = $yearNum") 
			or die('ERROR getting hear methods - '.mysql_error());
						
		$individualQuery = mysql_query("SELECT * FROM $playersTable INNER JOIN $individualsTable ON $individualsTable.individual_player_id = $playersTable.player_id 
			INNER JOIN $leaguesTable ON $leaguesTable.league_id = $individualsTable.individual_preferred_league_id 
			INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
			WHERE player_hear_method = $i  AND player_is_individual = 1 AND season_year = $yearNum") 
			or die('ERROR getting hear methods - '.mysql_error());	
		if($i != 4) {
			$numPeople[$i] = mysql_num_rows($teamQuery) + mysql_num_rows($individualQuery);
		} else {
			$numPeopleReturning = mysql_num_rows($teamQuery) + mysql_num_rows($individualQuery);
		}
	}  
	$img_width=800;
	$img_height=250; 
	$margins=30;

	# ---- Find the size of graph by substracting the size of borders
	$graph_width=$img_width - $margins * 2;
	$graph_height=$img_height - $margins * 2; 
	$img=imagecreate($img_width,$img_height);

 
	$bar_width=50;
	$total_bars=count($numPeople);
	$gap= ($graph_width- $total_bars * $bar_width ) / ($total_bars +1);

 
	# -------  Define Colors ----------------
	$bar_color=imagecolorallocate($img,0,64,128);
	$background_color=imagecolorallocate($img,240,240,255);
	$border_color=imagecolorallocate($img,200,200,200);
	$line_color=imagecolorallocate($img,220,220,220);
 
	# ------ Create the border around the graph ------

	imagefilledrectangle($img,1,1,$img_width-2,$img_height-2,$border_color);
	imagefilledrectangle($img,$margins,$margins,$img_width-1-$margins,$img_height-1-$margins,$background_color);

 
	# ------- Max value is required to adjust the scale	-------
	if(($max_value=max($numPeople)) == 0) {
		$max_value = 1;
	}
	$ratio= $graph_height/$max_value;

 
	# -------- Create scale and draw horizontal lines  --------
	$horizontal_lines=20;
	$horizontal_gap=$graph_height/$horizontal_lines;

	for($i=1;$i<=$horizontal_lines;$i++){
		$y=$img_height - $margins - $horizontal_gap * $i ;
		imageline($img,$margins,$y,$img_width-$margins,$y,$line_color);
		$v=intval($horizontal_gap * $i /$ratio);
		imagestring($img,0,5,$y-5,$v,$bar_color);

	}
 
 
	# ----------- Draw the bars here ------
	for($i=0;$i< $total_bars; $i++){ 
		# ------ Extract key and value pair from the current pointer position
		list($key,$peopleValue)=each($numPeople); 
		$x1= $margins + $gap + $i * ($gap+$bar_width) ;
		$x2= $x1 + $bar_width; 
		$y1=$margins +$graph_height- intval($peopleValue * $ratio) ;
		$y2=$img_height-$margins;
		imagestring($img,2,$x1+18,$y1-12,$peopleValue,$bar_color);
		imagestring($img,2,$x1,$img_height-25,$method[$key],$bar_color);		
		imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_color);
	}
	header("Content-type:image/png");
	imagepng($img);

?>