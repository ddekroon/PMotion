<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* May 7, 2013
* TimeWinnersSource.php
*
* This program uses a txt file and gives users a way to edit what Times are available.
********************************/ 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$container = new Container('Manage Prize Times');

$fileString = 'prizeTimes.txt';

function getTimes() {
	global $fileString;
	$optionsArray = array();
	$file = fopen($fileString, "r");
	if($file) {
		$counter = 0;
		while (($buffer = fgets($file, 4096)) !== false) {
			$lineTokens = explode('-', trim($buffer));
			$optionsArray[$counter] = $lineTokens[0].') '.$lineTokens[1];
			$counter++;
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	return $optionsArray;
}

function resetWinners(){
	$file = fopen("prizeTime.txt", "w");
	/*Getting the current year*/
	$currentYear = date(o);
	while (($buffer = fgets($file, 4096)) !== false) {
			/*This grabs the line from the file and trims it and puts it in an array*/
			/*lineTokens[0] is the number and lineTokens[1] is the text*/
			$lineTokens = explode('-', trim($buffer));
			/*breaking up the string into separate variables*/
			list($league, $year, $extra) = sscanf($lineTokens[1], "%s %d %s");
			/*Seeing if the year to be resetted is 3 years old*/
			if($year < ($currentYear - 2)){
				/*Deleteing the info from the database*/
				$sql = "DELETE FROM prizes WHERE prize_time_frame = ".$lineTokens[0].";";
			}
		
			
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}

	
	
	fclose($file);
	
}


function addTime() {
	global $fileString;
	$timeName = $_POST['TimeName'];
	$file = fopen($fileString, "r");
	if($file) {
		$max = 0;
		while (($buffer = fgets($file, 4096)) !== false) {
			$fileTokens = explode('-', $buffer);
			if($fileTokens[0] > $max) {
				$max = $fileTokens[0];
			}
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	$max = $max + 1;
	$file = fopen($fileString, "a");
	if($file) {
		fwrite($file, $max.'-'.$timeName."\n");
		fclose($file);
	}
}

function deleteTimes() {
	global $fileString;
	$toDelete = array();
	$linesArray = array();
	foreach($_POST['delTime'] as $delRow) {
		array_push($toDelete, $delRow);
	}
	$file = fopen($fileString, "r");
	if($file) {
		while (($buffer = fgets($file, 4096)) !== false) {
			array_push($linesArray, $buffer);
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	$file = fopen($fileString, "w");
	if($file) {
		for($i=0;$i<count($linesArray);$i++) {
			if(!in_array($i, $toDelete)) {
				fwrite($file, $linesArray[$i]);
			}
		}
		fclose($file);
	}
}

if(isset($_POST['addTimes'])) {
	addTime();
} else if(isset($_POST['delTimes'])) {
	deleteTimes();
}
$timesArray = getTimes();
/*resetWinners();*/ ?>


<form id="manageTimes" action='managePrizeTimes.php' method="post">
	<h1>Available Prizes</h1>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					Prize List
				</th>
			</tr>
			<?php for($i=0;$i<count($timesArray);$i++) { ?>
				<tr>
					<td>
						<?php print $timesArray[$i] ?>
					</td><td>
						<input type="checkbox" name="delTime[]" value="<?php print $i?>" />
					</td>
				</tr>
			<?php }?>
			<tr>
				<td colspan="2">
					New Time - <input type="text" name="TimeName" style="width:300px; ;"/>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="addTimes" value="Add Times" />
				</td><td>
					<input type="submit" name="delTimes" value="Delete Times" />
				</td>
			</tr>
		</table>
	</div>
</form>



<?php	$container->printFooter(); ?>
