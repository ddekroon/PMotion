<?php /*****************************************
File:printFormSource.php
Creator: Derek Dekroon
Created: April 12/2013
User chooses the season and sport, clicks a button to show the print page on another tab.
 * Useful so Dave can get people to register on paper at his registration night.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$javaScript = "<script type='text/javascript'>
            function loadPrintPage() {
                var form = document.getElementById('printForm');
                var seasonID = form.elements['seasonID'].options[form.elements['seasonID'].options.selectedIndex].value;
                var sportID = form.elements['sportID'].options[form.elements['sportID'].options.selectedIndex].value;
                window.open('regPrintPage.php?seasonID=' + seasonID + '&sportID=' + sportID, '_blank');
                return false;
            }
        </script>";
$container = new Container('Print Form', 'includeFiles/registrationStyle.css', $javaScript);

if(($sportID = $_GET['sportID']) == '') {
	$sportID = 0;
}
if(($seasonID = $_GET['seasonID']) == '') {
	$seasonID = 0;
}

$sportsDropDown = getSportDD($sportID);
$seasonsDropDown = getSeasonDD($seasonID); ?>

<form id="printForm">
<h1>Print Registration Form</h1>
<div class='getIDs'>
	<table>
		<tr>
			<th colspan=2>
				Choose Sport/Season
			</th>
		</tr><tr>
			<td>
				Sport
			</td><td>
				<select id='userInput' name='sportID' onchange='reloadUpdatePage()'>
					<?php print $sportsDropDown ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Season
			</td><td>
				<select id='userInput' name='seasonID' onchange='reloadUpdatePage()'>
					<?php print $seasonsDropDown ?>
				</select>
			</td>
		</tr><tr>
			<td colspan=2>
				<input type="submit" name="Show Print Form" value='Show Print Form' onClick=" return loadPrintPage()">
			</td>
		</tr>
	</table>
</div>
				

</form>
		
<?php $container->printFooter() ?>