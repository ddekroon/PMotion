<?php /*****************************************
File: createYearLeagues.php
Creator: Derek Dekroon
Created: June 20/2013
FUNCTIONALITY TAKEN OVER BY LEAGUES CONtrOL PANEL. Allows Dave to create a year's leagues based on last year's data
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
			function checkAll(toCheck) {
				var leagues = document.getElementsByName('leagueID[]');
				for(i = 0; i < leagues.length; i++) {
					if(toCheck == 1) {
						leagues[i].checked = true;
					} else {
						leagues[i].checked = false;
					}
				}
				return false;
			}
		</script>";
$container = new Container('Create a Years Leagues', 'includeFiles/registrationStyle.css', $javaScript);

require_once('includeFiles/createYearSQLFunctions.php');
require_once('includeFiles/leagueClass.php');

$createYear = $_POST['createYear'];
$copyYear = $_POST['copyYear'];

if(!isset($copyYear) || !isset($createYear)) {
	$seasonQuery = mysql_query("SELECT * FROM $seasonsTable ORDER BY season_year DESC") 
		or die('ERROR getting season data - '.mysql_error());
	$seasonArray = mysql_fetch_array($seasonQuery);
	$copyYear = $seasonArray['season_year'];
	$createYear = $copyYear + 1;
}

$toCreateFilter = array('','','',);
if($_POST['toCreate'] == 1) {
	$toSelect[0] = 'checked';
	$readOnly = 'readonly';
} else if($_POST['toCreate'] == 2) {
	$toSelect[1] = 'checked';
	$readOnly = 'readonly';
} else if($_POST['toCreate'] == 3) {
	$toSelect[2] = 'checked';
	$readOnly = 'readonly';
}
$sqlStrings = array();
$sqlStrings[1] = '';
$sqlStrings[2] = "AND season_name LIKE '%Spring%'";
$sqlStrings[3] = "AND season_name LIKE '%Winter 1%'";


if(isset($_POST['showLeagues']) || isset($_POST['makeYear']) || isset($_POST['makeSummer']) || isset($_POST['makeWin2'])) {
	$oldLeagues = getLeagues($copyYear, $_POST['toCreate']);
}

if(isset($_POST['makeYear'])) {
	if(isset($_POST['leagueID'])) {
		$createLeagueIDs = $_POST['leagueID'];
		createNewYear($oldLeagues, $createYear);	
	} else {
		print 'No leagues selected<br/>';
	}
}

if(isset($_POST['makeSummer'])) {
	if(isset($_POST['leagueID'])) {
		$createLeagueIDs = $_POST['leagueID'];
		createSummer($oldLeagues, $createYear);	
	} else {
		print 'No leagues selected<br/>';
	}
}?>

<form action=<?php print $_SERVER['PHP_SELF'] ?> method='post' id='createYearLeagues'>
<table class='master'>
	<tr>
		<td>
			<table class='titleBox'>
				<tr>
					<th>
						Create a Year
					</th>
				</tr>
			</table>
		</td>
	</tr><tr>
		<td>
			<table class='getIDs'>
				<tr>
					<td colspan=3>
						Year to create:
						<input type='text' name='createYear' <?php print $readOnly?> style='width:100px' value='<?php print $createYear ?>'>
						Year to copy:
						<input type='text' name='copyYear' <?php print $readOnly?> style='width:100px' value='<?php print $copyYear ?>'>
					</td>
				</tr><tr>
					<td>
						Create Seasons from last year:
						<?php if(strlen($readOnly ) > 2) { ?>
						<input type='radio' <?php print $toSelect[0]?> disabled name='toCreate' value=1 />
						<?php $toSelect[0] == 'checked'? print "<input type='hidden' name='toCreate' value=1 />":'';
						} else { ?>
							<input type='radio' <?php print $toSelect[0]?> name='toCreate' value=1 />
						<?php } ?>
					</td><td>
						Create Summer From Spring:
						<?php if(strlen($readOnly ) > 2) { ?>
						<input type='radio' <?php print $toSelect[1]?> disabled name='toCreate' value=2 />
						<?php $toSelect[1] == 'checked'? print "<input type='hidden' name='toCreate' value=2 />":'';
						} else { ?>
							<input type='radio' <?php print $toSelect[1]?> name='toCreate' value=2 />
						<?php } ?>
					</td><td>
						Create Winter 2 From Winter 1:
						<?php if(strlen($readOnly ) > 2) { ?>
						<input type='radio' <?php print $toSelect[2]?> disabled name='toCreate' value=3 />
						<?php $toSelect[2] == 'checked'? print "<input type='hidden' name='toCreate' value=3 />":'';
						} else { ?>
							<input type='radio' <?php print $toSelect[2]?> name='toCreate' value=3 />
						<?php } ?>
					</td>
				</tr>
				<?php if (isset($_POST['showLeagues'])) { ?>
				<tr>
					<td colspan="3">
						<input type="button" name="check" value="Check All" onClick="return checkAll(1);" />
						<input type="button" name="uncheck" value="Uncheck All" onClick="return checkAll(0);" />
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
	<?php if (isset($_POST['showLeagues'])) { ?>
	<tr>
		<td>
			<table class='leagues'>
				<tr>
					<th>
						League Name
					</th><th>
						Season
					</th><th>
						Make League
					</th>
				</tr>
				<?php foreach($oldLeagues as $league) {
					printLeague($league);
				} ?>
			</table>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td>
			<table class='bottomButton'>
				<tr>
					<td>
						<?php if(isset($_POST['showLeagues']) && $_POST['toCreate'] == 1) { ?>
						<button style='font-size:30px;' name='makeYear' value=1>Create Year</button>
						<?php } else if(isset($_POST['showLeagues']) && $_POST['toCreate'] == 2) { ?>
						<button style='font-size:30px;' name='makeSummer' value=1>Create Summer</button>
						<?php } else if(isset($_POST['showLeagues']) && $_POST['toCreate'] == 3) { ?>
						<button style='font-size:30px;' name='makeWin2' value=1>Create Winter 2</button>
						<?php } else { ?>
						<button style='font-size:30px;' name='showLeagues' value=1>Show Leagues to Create</button>
						<?php } ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>


<?php function printLeague($league) { ?>
	<tr>
    	<td>
        	<?php print $league->leagueName ?>
        </td><td>
        	<?php print $league->seasonObj->seasonName ?>
        </td><td>
        	<input type="checkbox" checked name="leagueID[]" value="<?php print $league->leagueID ?>" />
        </td>
    </tr>
<?php }

$container->printFooter(); ?>