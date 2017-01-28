<?php 
function removeComments() {
	global $surveysTable;
	$numAffected = 0;
	
	if($_POST['playerID'] != '') {
		foreach($_POST['playerID'] as $submissionID) {
			mysql_query("UPDATE $surveysTable SET survey_dont_show = 1 WHERE survey_submission_id = $submissionID") or die('ERROR removing comments - '.mysql_error());
			$numAffected++;
		}
		print 'Comments removed, '.$numAffected.' rows affected';
	} else {
		print 'No comments selected';
	}
}

function printPlayerHeader() { ?>
	<tr>
    	<td>
        	Name
        </td><td>
        	Team
        </td><td>
        	Comment
        </td><td></td>
    </tr>
<?php }

function printPlayerNode($playerObj) { ?>
	<tr>
    	<td>
        	<?php print $playerObj->playerFirstName; ?>
        </td><td>
        	<?php print "<a target='_blank' href='pollOneTeamPage.php?submissionID=".$playerObj->playerID."'>".$playerObj->playerTeamName.'</a>'; ?>
        </td><td>
        	<?php print $playerObj->playerComment; ?>
        </td><td style="vertical-align:middle">
        	<input type="checkbox" name="playerID[]" value="<?php print $playerObj->playerID ?>" />
        </td>
    </tr>
<?php }

//gets all the survey IDs... names are inherited from the surveyNames array in viewPollSource.
function getSurveysDD($surveyID, $surveyNames) {
	global $surveysTable;
	$lastSurveyID = 0;
	$surveysDropDown = '<option value=0>Pick a Survey</option>';
	
	$surveysQuery=mysql_query("SELECT * FROM $surveysTable ORDER BY survey_id DESC") or die("ERROR getting tournaments drop down ".mysql_error());
	while($survey = mysql_fetch_array($surveysQuery)) {
		if($survey['survey_id'] != $lastSurveyID) {
			if($survey['survey_id']==$surveyID){
				$surveysDropDown.="<option selected value=$survey[survey_id]>".$surveyNames[$survey['survey_id']].'</option>';
			}else{
				$surveysDropDown.="<option value=$survey[survey_id]>".$surveyNames[$survey['survey_id']].'</option>';
			}
			$lastSurveyID = $survey['survey_id'];
		}
	}
	return $surveysDropDown;
}

function printHiddenVariables($surveyNames, $surveyQuestions) { ?>
	<input type="hidden" name="surveyTitleNames[]" value="" /> <?php //needs to be here because surveyIDs start at 1 not 0
	foreach($surveyNames as $surveyName) { ?>
		<input type="hidden" name="surveyTitleNames[]" value="<?php print $surveyName ?>" />
	<?php }
	for($i = 1; $i <= count($surveyQuestions); $i++) {
		for($j=1; $j<= count($surveyQuestions[$i]); $j++) {?>
        	<input type="hidden" name="surveyQuestions[<?php print $i?>][]" value="<?php print $surveyQuestions[$i][$j] ?>" />
        <?php }
	}
}