<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
	require_once('includeFiles/surveyFunctions.php');

	if(isset($_POST['submitInfo'])) {
		if(errorCheck() == 1) {
			submitData();
			header("Location:thankYouSurvey.php");
		} else {
			print $error; //error is a global from errorCheck()
		}
	} ?>
<html>
    <head>
        <title>Perpetual Motion Survey</title>
        <link type="text/css" rel="stylesheet" href="includeFiles/surveyStyle.css">
        <script type="text/javascript">
		function showOther(qString, self) {
			var row = document.getElementById(qString + 'Row');
			if(qString == 'Two') {
				if(self.value == 15) {
					row.style.display = 'inline';
				} else {
					row.style.display = 'none';
				}
			} else if(qString == 'Three') {
				if(self.value == 15) {
					if (self.checked == true) {
						row.style.display = 'inline';
					} else {
						row.style.display = 'none';
					}
				}
			}
		}
		
		function checkForm() {
			var teamName = document.getElementsByName('teamName')[0].value;
			var playerName = document.getElementsByName('playerName')[0].value;
			var questionOne = document.getElementsByName('questionOne[]');
			var questionTwo = document.getElementsByName('questionTwo[]');
			var questionThree = document.getElementsByName('questionThree[]');
			var errorString = '';
			
			if(teamName.length < 3 || teamName.length > 30) {
				errorString+="\n\n Please enter a team name between 3 and 30 characters";
			}
			if(playerName.length < 3 || playerName.length > 50) {
				errorString+='\n\n Please enter a player name between 3 and 50 characters';
			}
			if(noneWithCheck(questionOne)) {
				errorString+='\n\n Please answer question one';
			}
			if(noneWithCheck(questionTwo)) {
				errorString+='\n\n Please answer question two';
			}
			if(noneWithCheck(questionThree)) {
				errorString+='\n\n Please answer question three';
			}
			if(errorString.length > 2) {
				alert('ERROR'+ errorString);
				return false
			} else {
				return true;
			}
		}
		
		function noneWithCheck(buttons) {
			for(var h = 0; h < buttons.length; h++) {
				if(buttons[h].checked) { 
					return false; 
				}
			}
			return true;
		}
		
		</script>
    </head>
    <body>
    	<form id="survey" action="<?php $_SERVER['PHP_SELF']?>" method="post">
     	<table class='master' align="center">
        	<tr>
            	<td>
                	<table class="header">
                    	<tr>
                        	<th>
                            	<img src="/Logos/PerpetualMotionLeaf.jpg" align="left" width="70px" height="50px">
                            	Post Game Social Survey
                            </th>
                        </tr>
                        <tr>
                        	<td>
                            	<br><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="height:10px">
            	<td>
                	<br />
                </td>
            </tr>
        	<tr>
                <td align="center">
                    In an effort to improve our post-game social experience for all of the players in our leagues, we are
                    asking a few quick questions that will take only a minute to complete. We will be handing out prizes, including gift
                    certificates, t-shirts, and hats to random captains that complete the survey to thank you for your time.
                </td>
            </tr>
            <tr style="height:10px">
            	<td>
                	<br />
                </td>
            </tr>
            <tr>
                <td>
                    <table class="question">
						<?php printQuestionOne();  ?>
            		</table>
                </td>
            </tr><tr>
                <td>
                    <table class="question">
            			<?php printQuestion(2); ?>
            		</table>
                </td>
            </tr><tr>
                <td>
                    <table class="question">
            			<?php printQuestion(3); ?>
                    </table>
                </td>
            </tr><tr>
            	<td>
                	<table class="footer">
						<?php printFooter();?>
            		</table>
                </td>
            </tr>
        </table>
    	</form>
    </body>
</html>