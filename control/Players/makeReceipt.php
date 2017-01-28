<?php /*****************************************
File: updatePlayer.php
Creator: Derek Dekroon
Created: July 17/2012
Gives options to input data for the receipt, when submit is pressed the receipt should be created and downloaded.
******************************************/


require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).$calendarPage);
$javaScript = "<script type='text/javascript' src='$calendarJS'></script>";
$container = new Container('Make a Receipt', 'includeFiles/playerStyle.css', $javaScript); ?>
<link href="<?php print $calendarCSS ?>" rel="stylesheet" type="text/css" />

<?php $sportID = 0;

if(isset($_POST['inputData'])) {
	$receiptCreated = 1;
	$dateTokens = explode('-', $_POST['date']);
	$selectedDay = $dateTokens[2];
	$selectedMonth = $dateTokens[1];
	$selectedYear = $dateTokens[0];
	$sportID = $_POST['sportID'];
	
	if($sportID == 1) {
		$sportName = 'Ultimate Frisbee';
	} else if($sportID == 2) {
		$sportName = 'Beach Volleyball';
	} else if($sportID == 3) {
		$sportName = 'Flag Football';
	} else if($sportID == 4) {
		$sportName = 'Soccer';
	}
	if(isset($_POST['season'])) {
		$seasonString = $_POST['season'][0];
		for($i = 1; $i < count($_POST['season']); $i++) {
			$seasonString .= ' and '.$_POST['season'][$i];
		}
		$seasonString .= ' Fees';
	}
	print $_POST['date'].' '.$sportName.' '.$seasonString.'<br />';
} else {
	$receiptCreated = 0;
}

$sportsDropDown = getSportDD($sportID);

$minYear = date('Y') - 3;
$maxYear = date('Y') + 3;
$myCalendar = new tc_calendar("date", true);
$myCalendar->setIcon("$calendarRoot/images/iconCalendar.gif");
if($receiptCreated == 1) {
	$myCalendar->setDate($selectedDay, $selectedMonth, $selectedYear);
} else {
	$myCalendar->setDate(date('d'), date('m'), date('Y'));
}
$myCalendar->setPath("$calendarRoot/");
$myCalendar->setYearInterval(1960, $maxYear);
$myCalendar->dateAllow($minYear.'-01-01', $maxYear.'-03-01');
$myCalendar->setOnChange("myChanged('test')"); ?>

<form name="makeReceipt" action="downloadReceipt.php" method="post">
	<h1>Make a Receipt</h1>
	<div class='tableData'>
		<table>
			<tr>
				<td>
					To
				</td><td>
					<input type='text' name="toName" style="width:400px" value="" />
				</td>
			</tr><tr>
				<td colspan="2">
					<?php $myCalendar->writeScript(); ?>
				</td>
			</tr><tr>
				<td colspan="2">
					<b>Description</b><br /><br />
					<select style='width:120px;' name="sportID">
						<?php print $sportsDropDown ?>
					</select>
					<input type="checkbox" name="season[]" value='Spring' /> Spring
					<input type="checkbox" name="season[]" value='Summer' /> Summer
					<input type="checkbox" name="season[]" value='Fall' /> Fall
					<input type="checkbox" name="season[]" value='Winter I' /> Winter I
					<input type="checkbox" name="season[]" value='Winter II' /> Winter II<br /><br />OR<br /><br />
					Other <input type='text' name="miscFees" style="width:400px;" />
				</td>
			</tr><tr>
				<td>
					Paid
				</td><td>
					$<input type='text' name="totalPaid" value="" style="width:385px"  />
				</td>
			</tr><tr>
				<td colspan=2>
					<input type="submit" name="inputData" value="Submit Data" />
				</td>
			</tr>
		</table>
	</div>
</form>
<?php $container->printFooter(); ?>