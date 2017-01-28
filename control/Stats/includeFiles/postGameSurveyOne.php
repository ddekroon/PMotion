<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php'); 

	if(($questionID = $_GET['questionID']) == '') {
		$questionID = 0;
	} else {
		$questionID++;
	}
	$answerOne[0] = 'Never';
	$answerOne[1] = 'Once';
	$answerOne[2] = 'Few';
	$answerOne[3] = 'Most Weeks';
	$answerOne[4] = 'Every Week';

	$answerTwo[0] = 'Ranch';
	$answerTwo[1] = 'Franks';
	$answerTwo[2] = 'Albion';
	$answerTwo[3] = 'Bobbys';
	$answerTwo[4] = 'McCabes';
	$answerTwo[5] = 'Montanas';
	$answerTwo[6] = 'Kelseys';
	$answerTwo[7] = 'Shoeless';
	$answerTwo[8] = 'Fifty';
	$answerTwo[9] = 'Squirrels';
	$answerTwo[10] = 'Boston';
	$answerTwo[11] = 'Woolys';
	$answerTwo[12] = 'Borealis';
	$answerTwo[13] = 'None';
	$answerTwo[14] = 'Other';
	
	if($questionID == 1) {
		$answerString = 'survey_answer_one';
		$arrayNum = 0;
		$numPeople = array(0, 0, 0, 0, 0, );
	} else if($questionID == 2) {
		$answerString = 'survey_answer_two';
		$arrayNum = 1;
		$numPeople = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, );
	} else if($questionID == 3) {
		$answerString = 'survey_answer_three';
		$arrayNum = 1;
		$numPeople = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, );
	}
	
	
	$personQuery = mysql_query("SELECT * FROM $surveysTable WHERE survey_id = 1") or die('ERROR getting hear methods - '.mysql_error());
	$totalPeople = mysql_num_rows($personQuery);
	
	$surveyQuery = mysql_query("SELECT * FROM $surveysTable WHERE survey_id = 1") or die('ERROR getting hear methods - '.mysql_error());
	while($answer = mysql_fetch_array($surveyQuery)) {
		$answerArray = explode('%', $answer[$answerString]);
		foreach($answerArray as $curAnswer) {
			if($curAnswer != '') {
				$numPeople[$curAnswer - 1]++;
			}
		}
	}
	
	$img_width=850;
	$img_height=250; 
	$margins=28;

	# ---- Find the size of graph by substracting the size of borders
	$graph_width=$img_width - $margins * 2;
	$graph_height=$img_height - $margins * 2; 
	$img=imagecreate($img_width,$img_height);

 
	$bar_width=25;
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
	if(($max_value=max($numPeople)) == '') {
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
		imagestring($img,2,$x1+8,$y1-12,$peopleValue,$bar_color);
		if($questionID == 1) {
			imagestring($img,2,$x1,$img_height-25,$answerOne[$key],$bar_color);
		} else {
			imagestring($img,2,$x1,$img_height-25,$answerTwo[$key],$bar_color);
		}
		imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_color);
	}
	header("Content-type:image/png");
	imagepng($img);

?>