<?php /*****************************************
File: viewPolls.php
Creator: Derek Dekroon
Created: August 15/2012
Program to view a poll. Didn't do a very good job of this, not very adaptable to new polls.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
			function reloadPage(surveyID) {
				self.location = 'viewPolls.php?surveyID=' + surveyID.value;
			}
		
        	function loadValues(surveyID, self) {
				var imgBox = document.getElementById('imageBox');
				var questionBox = document.getElementById('questionBox');
				var questionString = 'surveyQuestions[' + surveyID + '][]'
				var questions = document.getElementsByName(questionString);
				
				if(surveyID == 1) {
					imgBox.src = 'includeFiles/postGameSurveyOne.php?questionID=' + self.value;
				}
				questionBox.innerHTML = questions[self.value].value;
			}
			function checkSubmit() {
				return confirm('Are you sure you want to remove these rows?');	
			}
        </script>";
$container = new Container('Tournament Stats', 'includeFiles/statsStyle.css', $javaScript); 

require_once('includeFiles'.DIRECTORY_SEPARATOR.'viewPollsFunctions.php');
require_once('includeFiles'.DIRECTORY_SEPARATOR.'playerClass.php');

//This array is key for storing and displaying all surveys in the surveys database
$surveyName[1] = 'Post Game Social One';

$surveyQuestion[1][1] = 'How often did your team go out to a restaurant or bar after your games';
$surveyQuestion[1][2] = 'Which restaurant or bar did you most regularly go to';
$surveyQuestion[1][3] = 'Which restaurants and bars did you attend at least once during the year';

$playerObj = array();

if(($surveyID = $_GET['surveyID']) == '') {
	$surveyID = 0;
}

if(isset($_POST['removeComments'])) {
	removeComments();
}

$surveysDropDown = getSurveysDD($surveyID, $surveyName);
printHiddenVariables($surveyName, $surveyQuestion); //prints all survey names as hidden variables so data changes with the surveys dropdown (javascript)

$numPlayers = 0;
$playerQuery = mysql_query("SELECT * FROM $surveysTable WHERE survey_id = $surveyID AND survey_comments != '' AND survey_dont_show = 0 ORDER BY survey_submission_id DESC") 
	or die('ERROR getting survey players - '.mysql_error());
	
while($personArray = mysql_fetch_array($playerQuery)) {
	$playerObj[$numPlayers] = new Player();
	$playerObj[$numPlayers]->playerFirstName = $personArray['survey_submitter_player_name'];
	$playerObj[$numPlayers]->playerTeamName = $personArray['survey_submitter_team_name'];
	$playerObj[$numPlayers]->playerComment = $personArray['survey_comments'];
	$playerObj[$numPlayers]->playerID = $personArray['survey_submission_id'];
	$numPlayers++;
} ?>

<form name="viewPoll" action="<?php print $_SERVER['PHP_SELF'].'?surveyID='.$surveyID ?>" method="post">
	<h1>Surveys</h1>
	<div class='getIDs'>
		<select id='userInput' name="surveyID" onChange="reloadPage(this)">
			<?php print $surveysDropDown ?>
		</select><br /><br />
		<select id='userInput' name="questionID" onChange="loadValues('<?php print $surveyID ?>', this)">
			<option value=0>Choose</option>
			<option value=0>Question 1</option>
			<option value=1>Question 2</option>
			<option value=2>Question 3</option>
		</select>
	</div>
	<div class='tableData'>
		<img id="imageBox" width="800px" height="300px">
	</div><div class='tableData'>
		<table>
			<tr>
				<th colspan=4>
					Player Testamonies
				</th>
			</tr>
			<?php printPlayerHeader();
			foreach($playerObj as $player) {
				printPlayerNode($player);
			} ?>
			<tr>
				<td colspan=4>
					<input type="submit" name="removeComments" value="Remove Comments" onClick="return checkSubmit();">
				</td>
			</tr>
		</table>
	</div>
</form>
<?php $container->printFooter(); ?>