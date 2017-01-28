<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* August 2, 2012
* editTeamCaptains.php
*
* Program that figures out which teams in score reporter have no captain and gives you a chance to add one/gives
* links to team page and team editor page so you can more easily figure it out.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$container = new Container('Teams No Captain', 'includeFiles/registrationStyle.css');

require_once('includeFiles/edtCptnFunctions.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');

if(isset($_POST['submitCaptains'])) {
	$editedTeams = getPostData();
	updateTeams($playerObj, $editedTeams);
}
$numTeams = getTeamsData();
 ?>

<form id='teamForm' METHOD='POST' action=<?php print $_SERVER['PHP_SELF'] ?>>
<table class="master">
	<tr>
		<td>
			<table class="titleBox">
				<tr>
					<th>
						Teams With No Captain
					</th>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table class="leagueInfo"> 
				<?php printCaptainHeader();
				for($i=0;$i<$numTeams;$i++) {
					printCaptainNode($team[$i], $i, $editedTeams, $playerObj);
				} ?>
				<input type='hidden' name='numTeams' value=<?php print $numTeams?> />
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table class="bottomButton">
				<?php printButtons() ?>
			</table>
		</td>
	</tr>
</table>
</form>
	
	<?php $container->printFooter(); ?>