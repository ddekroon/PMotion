<?php

function errorCheck() {
	global $error;
	
	$error = '';
	
	if(strlen($_POST['teamName']) < 3 || strlen($_POST['teamName']) > 30) {
		$error.='<br />Please enter a team name between 3 and 30 characters';
	}
	
	if(strlen($_POST['playerName']) < 3 || strlen($_POST['playerName']) > 30) {
		$error.='<br />Please enter a player name between 3 and 30 characters';
	}
	
	if($_POST['questionOne'] == '') {
		$error.='<br />Please answer question one';
	}
	if($_POST['questionTwo'] == '') {
		$error.='<br />Please answer question two';
	}
	if($_POST['questionThree'] == '') {
		$error.='<br />Please answer question three';
	}
	
	if(strlen($error) > 2) {
		return 0;
	} else {
		return 1;
	}
}

function submitData() {
	global $surveysTable;
	
	$teamName = $_POST['teamName'];
	$playerName = $_POST['playerName'];
	$answerOne = $_POST['questionOne'][0];
	$answerTwo = $_POST['questionTwo'][0];
	$answerTwoOther = $_POST['questionTwoOther'];
	$answerThree = '';
	foreach($_POST['questionThree'] as $questionThree) {
		$answerThree.=$questionThree.'%';
	}
	$answerThreeOther = $_POST['questionThreeOther'];
	$teamComment = $_POST['comments'];

	mysql_query("INSERT into $surveysTable (survey_id, survey_submitter_team_name, survey_submitter_player_name, survey_answer_one, survey_answer_two, survey_answer_two_other, 
		survey_answer_three, survey_answer_three_other, survey_comments) VALUES (1, '$teamName', '$playerName', '$answerOne', '$answerTwo', '$answerTwoOther', '$answerThree',
		'$answerThreeOther', '$teamComment')") or die('ERROR putting in submission - '.mysql_error());
}

function printQuestionOne() { ?>
	<tr>
        <td>
            <b>Question 1: How often did your team go out to a restaurant or bar after your games?</b>
        </td>
	</tr>
    <tr>
        <td>
            <input type="radio" name="questionOne[]" value="1" />
            Never
        </td>
    </tr><tr>
        <td>
            <input type="radio" name="questionOne[]" value="2" />
            Once
        </td>
    </tr><tr>
        <td>
            <input type="radio" name="questionOne[]" value="3" />
            A few Times
        </td>
    </tr><tr>
        <td>
            <input type="radio" name="questionOne[]" value="4" />
            Most Weeks
        </td>
    </tr><tr>
        <td>
            <input type="radio" name="questionOne[]" value="5" />
            Every Week
        </td>
    </tr>
    <tr style="height:10px">
        <td colspan="2">
            <br />
        </td>
    </tr>
<?php }

function printQuestion($qNum) {
	if($qNum == 2) {
		$questionString = 'Question 2: Which restaurant or bar did you most regularly go to?';
		$type = 'radio';
		$qString = 'Two';
	} else if ($qNum == 3) {
		$questionString = 'Question 3: Which restaurants and bars did you attend at least once during the year?';
		$type = 'checkbox';
		$qString = 'Three';	
	}
    ?>
    <tr>
        <td>
            <b><?php print $questionString ?></b>
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="1" onClick="showOther('<?php print $qString ?>', this)"/>
            Stampede Ranch
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="2" onClick="showOther('<?php print $qString ?>', this)"/>
            Frank and Steins
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="3" onClick="showOther('<?php print $qString ?>', this)"/>
            The Albion Hotel
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="4" onClick="showOther('<?php print $qString ?>', this)"/>
            Bobby O'Brien's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="5" onClick="showOther('<?php print $qString ?>', this)"/>
            McCabe's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="6" onClick="showOther('<?php print $qString ?>', this)"/>
            Montana's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="7" onClick="showOther('<?php print $qString ?>', this)"/>
            Kelsey's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="8" onClick="showOther('<?php print $qString ?>', this)"/>
            Shoeless Joe's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="9" onClick="showOther('<?php print $qString ?>', this)"/>
            Fifty West
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="10" onClick="showOther('<?php print $qString ?>', this)"/>
            Squirrel Tooth Alice's
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="11" onClick="showOther('<?php print $qString ?>', this)"/>
            Boston Pizza
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="12" onClick="showOther('<?php print $qString ?>', this)"/>
            Woolwich Arrow
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="13"onClick="showOther('<?php print $qString ?>', this)" />
            Borealis
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="14" onClick="showOther('<?php print $qString ?>', this)"/>
            None
        </td>
    </tr><tr>
        <td>
            <input type="<?php print $type ?>" name="question<?php print $qString ?>[]" value="15" onClick="showOther('<?php print $qString ?>', this)" />
            Other (Specify)
        </td>
    </tr>
    </tr><tr id="<?php print $qString.'Row'?>" style="display:none">
        <td colspan="2">
            <input type="text" name="question<?php print $qString ?>Other" style="width:300px"> 
        </td>
    </tr>
    <tr style="height:10px">
        <td colspan="2">
            <br />
        </td>
    </tr>
<?php }

function printFooter() { ?>
	<tr>	
		<td colspan="2">
        	<b>If you have any comments or suggestions for next year, please type them in here:</b>
        </td>
    </tr>
    <tr>
    	<td colspan="2">
        	<textarea  name="comments" rows="7" style="width:700px;"></textarea>
        </td>
    </tr>
    <tr>
    	<td>
        	Team Name 
        </td><td>
        	<input type="text" name="teamName">
        </td>
    </tr>
    <tr>
    	<td>
        	Player Name
        </td><td>
        	<input type="text" name="playerName">
        </td>
    </tr>
    <tr>
    	<td colspan="2">
        	<input type="submit" name="submitInfo" value='Submit' onclick="return checkForm()">
        </td>
    </tr>
<?php }