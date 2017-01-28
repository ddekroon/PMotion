<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

if(($submissionID = $_GET['submissionID']) == '' ) {
	$submissionID = 0;
}

$answerOne[0] = 'Never';
$answerOne[1] = 'Once';
$answerOne[2] = 'A Few Times';
$answerOne[3] = 'Most Weeks';
$answerOne[4] = 'Every Week';

$answerTwo[0] = 'Ranch';
$answerTwo[1] = 'Franks';
$answerTwo[2] = 'The Albion';
$answerTwo[3] = 'Bobby O\'Brien\'s';
$answerTwo[4] = 'McCabes';
$answerTwo[5] = 'Montanas';
$answerTwo[6] = 'Kelseys';
$answerTwo[7] = 'Shoeless Joes';
$answerTwo[8] = 'Fifty West';
$answerTwo[9] = 'Squirrelies';
$answerTwo[10] = 'Boston Pizza';
$answerTwo[11] = 'Woolys';
$answerTwo[12] = 'Borealis';
$answerTwo[13] = 'None';
$answerTwo[14] = 'Other';

$submissionQuery = mysql_query("SELECT * FROM $surveysTable WHERE survey_submission_id = $submissionID") or die('ERROR getting submission - '.mysql_error());
$submissionArray = mysql_fetch_array($submissionQuery);

$teamName = $submissionArray['survey_submitter_team_name'];
$playerName = $submissionArray['survey_submitter_player_name'];
$aOne = $submissionArray['survey_answer_one'];
$aTwo = $submissionArray['survey_answer_two'];
$aTwoText = $submissionArray['survey_answer_two_other'];
$aThreeArray = explode('%', $submissionArray['survey_answer_three']);
$aThreeText = $submissionArray['survey_answer_three_other'];
$surveyComment = $submissionArray['survey_comments'];



?>
<html>
	<head>
    	<link rel='stylesheet' type='text/css' href="includeFiles/surveyStyle.css" />
        <title>Team Survey Page</title>
    </head>
    <body>
    	<table class='master'>
        	<tr>
            	<td>
                	<table class="surveyInfo">
                    	<tr>
                        	<td>
                            	<?php print $teamName ?>
                            </td><td>
                            	<?php print $playerName ?>
                            </td>
                        </tr>
                        <tr>
                        	<th colspan="2">
                            	How often did your team go out to a restaurant or bar after your games?
                            </th>
                        </tr>
                        <tr>
                        	<td colspan="2">
                            	Answer: <?php print $answerOne[$aOne - 1] ?>
                            </td>
                        </tr>
                        <tr>
                        	<th colspan="2">
                            	Which restaurant or bar did you most regularly go to?
                            </th>
                        </tr>
                        <tr>
                        	<td>
                            	Answer: <?php print $answerTwo[$aTwo - 1] ?>
                            </td><td>
                            	Other: <?php print $answerTwoText ?>
                            </td>
                        </tr>
                        <tr>
                        	<th colspan="2">
                            	Which restaurants and bars did you attend at least once during the year?
                            </th>
                        </tr>
                        <tr>
                        	<td>
                            	Answer: <?php foreach($aThreeArray as $aThree) {
									print $answerTwo[$aThree - 1].'<br />';
								}?>
                            </td><td>
                            	Other: <?php print $answerThreeText ?>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan=2>
                            	Comment: <?php print $surveyComment; ?>
                            </td>
                        </tr>
        			</table>
                </td>
            </tr>
        </table>
    </body>
</html>