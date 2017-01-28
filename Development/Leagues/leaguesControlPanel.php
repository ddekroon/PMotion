<?php /*****************************************
File: leaguesControlPanel.php
Creator: Derek Dekroon
Created: July 4/2013
Program to let a user do anything and everything with leagues. Lets them make new leagues based on past years data, 
update what's full and what isn't, and gives links to edit the rest of a league's data.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "
<script type='text/javascript'>
	function reloadSeason(self) {
		document.location = 'leaguesControlPanel.php?seasonID=' + self.value;
	}
	
	function checkAll() {
		var field = document.getElementsByName('pastLeagueID[]');
		for (i = 0; i < field.length; i++) {
			field[i].checked = true;
		}
		return false;
	}

	function uncheckAll() {
		var field = document.getElementsByName('pastLeagueID[]');
		for (i = 0; i < field.length; i++) {
			field[i].checked = false;
		}
		return false;
	}
	</script><script>
$(document).ready(function(){
  $('#togglePastButton').click(function(){
    $('#pastLeaguesRow').fadeToggle(250);
  });
  
  $('#toggleSeasonButton').click(function(){
    $('#editSeasonRow').fadeToggle(250);
  });
  $('#regBySport').change(function(){
		$('.sportRow').fadeToggle(250);
	  });
  
});
</script>";
?>
<script>
	
  </script>
<?php
$container = new Container('Leagues Control Panel', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/leagControlPanelFormFunctions.php');
require_once('includeFiles/leagControlPanelSqlFunctions.php');
require_once('includeFiles/leagueClass.php');
require_once('includeFiles/seasonClass.php');

checkCreateSeasons(date('Y'));

if(($seasonID = $_GET['seasonID']) == '') {
	$seasonArray = mysql_fetch_array(mysql_query("SELECT season_id FROM $seasonsTable WHERE season_available_registration = 1"));
	$seasonID = $seasonArray['season_id'];
}
$seasonArray = mysql_fetch_array(mysql_query("SELECT season_name, season_year FROM $seasonsTable WHERE season_id = $seasonID"));
$seasonName = $seasonArray['season_name'];
$curYear = $seasonArray['season_year'];
$pastSeasonYear = $curYear - 1;
$pastSeasonArray = mysql_fetch_array(mysql_query("SELECT season_id FROM $seasonsTable WHERE season_name LIKE '$seasonName'
	AND season_year = $pastSeasonYear"));
$pastSeasonID = $pastSeasonArray['season_id'];

if(isset($_POST['updateLeagues'])) {
	updateData();
}

$seasonsDropDown = getSeasonDD($seasonID);
$leagueObjArray = getLeaguesData($seasonID);
$pastLeagueObjArray = getLeaguesData($pastSeasonID);

$curSeasonObj = getSeasonData($seasonID);
$sports = getSportData($curSeasonObj);?>

<form id='results' method='POST' action='<?php print $_SERVER['PHP_SELF'].'?seasonID='.$seasonID ?>'>
	<h1>Leagues Control Panel</h1>
	<?php $container->printInfo('The league names have had \'Division, Recreational, Competitive, and Intermediate\' shortened to \'Div, Rec, Comp, and Inter\' respectively so all the sports would fit on one row. The actual league names are still the long form.'); ?>
	<div class='getIDs'>
	<?php printIDs($seasonsDropDown); ?>
	</div><div class='tableData' id="editSeasonRow">
		<table id='innerTable'>
			<?php printSeasonEditor($curSeasonObj, $sports); ?>
		</table>
	</div><div class='tableData'>
		<?php printLeagues($leagueObjArray, 0); ?>
	</div><div class='tableData'>
		<input type='button' id="togglePastButton" value='Toggle Past Leagues' />
	</div><div class='tableData' id="pastLeaguesRow" style="display:none;">
		<h5><?php print $seasonName.' '.$pastSeasonYear; ?></h5>
		<?php printLeagues($pastLeagueObjArray, 1); ?>
		<?php printCheckAllButtons(); ?>
	</div><div class='tableData'>
		<?php printBottomButton(); ?>
	</div>
</form>

<?php $container->printFooter(); ?>