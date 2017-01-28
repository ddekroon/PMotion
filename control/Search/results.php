<?php /*****************************************
File: results.php
Creator: Derek Dekroon
Created: June 20/2012
Shows search results
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php');
require_once('includeFiles/srchVariableDeclarations.php');
require_once('includeFiles/srchFormFunctions.php');

$error = 0;
if ($_POST['searchType'] == 1 && strlen($_POST['searchString']) > 2) {
	$isActive = 1;
} else if ($_POST['searchType'] == 2 && strlen($_POST['searchString']) > 2) {
	$isActive = 0;
} else if($_POST['searchType'] == 3 && strlen($_POST['searchString']) > 2) {
	require_once('faqSearchResults.php');
	exit(0);
} else {
	$error = 1;
}
$searchString = $_POST['searchString'];

$container = new Container('Search Results', 'includeFiles/searchStyle.css');
if($error == 1) {
	print '<h1>Search Results</h1>';
	$container->printError('Search string < 2 characters');
	exit(0);
}

filter_var($searchString, FILTER_VALIDATE_EMAIL)?$isEmail = 1:$isEmail = 0;

$searchString = '%'.$searchString.'%'; //makes it so the matching in mysql statememnts uses all the characters of searchString

$numTeams = getTeamsData($searchString, $isActive);
$numPlayers = getPlayersData($searchString, $isActive);
if($isEmail == 1) {
	$user = getUserData($searchString);
}?>

<h1>Search Results</h1>
<?php if($numTeams == 0 && $numPlayers == 0) {
	$container->printError('No teams or players found for string - '.htmlentities($_POST['searchString'], ENT_QUOTES));
} else { ?>
	<div class='tableData'>
		<table>
			<?php printTeamsHeader(); 
			for($i=0;$i<$numTeams;$i++) {
				printTeamInfo($team[$i]);
			} ?>
		</table>
	</div>
	<?php if($isEmail == 1 && count($user) > 0) { ?>
		<div class='tableData'>
			<table>
				<?php printUserHeader(); 
				foreach($user as $userNode) {
					printUserInfo($userNode);
				} ?>
			</table>
		</div>
	<?php } ?>
	<div class='tableData'>
		<table>
			<?php printPlayersHeader(); 
			for($i=0;$i<$numPlayers;$i++) {
				printPlayerInfo($player[$i]);
			} ?>
		</table>
	</div>
<?php } ?>
<?php $container->printFooter(); ?>