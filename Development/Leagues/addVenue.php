<?php /*****************************************
File: closeLeagueWeek.php
Creator: Derek Dekroon
Created: June 5/2013
File to close a league week with one button. Compares all the scheduled matches and score submissions to figure out
what the probable outcomes of any unsubmitted matches would be. User needs only click submit and the values are 
automatically added to the database.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
	function reloadSport(self) {
		window.location.href = 'addVenue.php?sportID=' + self.value;
	}
	function reloadVenue(self) {
		var sportID = document.getElementsByName('sportID')[0].value;
		window.location.href = 'addVenue.php?sportID=' + sportID + '&edtVenueID=' + self.value;
	}
	</script>";
$container = new Container('Venues Control Panel', 'includeFiles/leagueStyle.css', $javaScript);

require_once('includeFiles/venueFunctions.php');

if(!isset($_POST['sportID'])) {
	if(($sportID = $_GET['sportID']) == '') {
		$sportID = 0;
	}
} else {
	$sportID = $_POST['sportID'];
}
if(($edtVenueID = $_GET['edtVenueID']) == '') {
	$edtVenueID = 0;
}
$venuesDropDown = getVenueDD($sportID, $edtVenueID);

getVenueData($edtVenueID);

if(isset($_POST['addVenue'])) {
	updateVenue($_POST['venueName'], $_POST['venueShortMatchName'], $sportID, $_POST['venueLink']);
} else if(isset($_POST['editVenue'])) {
	updateVenue($_POST['edtVenueName'], $_POST['edtVenueShortMatchName'], $sportID, $_POST['edtVenueLink'], $edtVenueID);
}

$sportsDropDown = getSportDD($sportID);?>

<form action='<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID.'&edtVenueID='.$edtVenueID ?>' method='post'>
<h1>Venues Control Panel</h1>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=2>
				Add a Venue
			</th><th colspan=2>
				Edit a Venue
			</th>
		</tr><tr>
			<td colspan=4>
				Sport
				<select id='userInput' name='sportID' onchange='reloadSport(this)'>
					<?php print $sportsDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				Venue Name
			</td><td>
				<input type='text' id="userInput" name="venueName" title='name shown all over the website'
					value="<?php print htmlentities($_POST['venueName'], ENT_QUOTES) ?>" />
			</td><td>
				Venue To Edit
			</td><td>
				<select name="edtVenueID" id="userInput" onChange="reloadVenue(this)">
					<?php print $venuesDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td>
				Venue Short Name
			</td><td>
				<input type='text' id="userInput" name="venueShortMatchName"  title='For schedule searching'
					value="<?php print htmlentities($_POST['venueShortMatchName'], ENT_QUOTES) ?>" />
			</td><td>
				Venue Name
			</td><td>
				<input type='text' id="userInput" name="edtVenueName" title='name shown all over the website'
					value="<?php print htmlentities($venueName, ENT_QUOTES) ?>" />
			</td>
		</tr><tr>
			<td>
				Venue Map Link
			</td><td>
				<input type='text' id="userInput" name="venueLink" title='eg "/maps/venuename"'
					value="<?php print htmlentities($_POST['venueLink'], ENT_QUOTES) ?>" />
			</td><td>
				Venue Short Name
			</td><td>
				<input type='text' id="userInput" name="edtVenueShortMatchName"  title='For schedule searching'
					value="<?php print htmlentities($venueShortName, ENT_QUOTES) ?>" />
			</td>
		</tr><tr>
			<td></td><td></td>
			<td>
				Venue Map Link
			</td><td>
				<input type='text' id="userInput" name="edtVenueLink" title='eg "/maps/venuename"'
					value="<?php print htmlentities($venueLink, ENT_QUOTES) ?>" />
			</td>
		</tr><tr>
			<td colspan=2>
				<input type='submit' name='addVenue' value='Create Venue' />
			</td><td colspan=2>
				<input type='submit' name='editVenue' value='Edit Venue' />
			</td>
		</tr>
	</table>
</div>
</form>
<?php $container->printFooter(); ?>
