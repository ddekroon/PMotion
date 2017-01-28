<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* May 7, 2013
* managePrizes.php
*
* This program uses a txt file and gives users a way to edit what prizes are available.
********************************/ 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$container = new Container('Manage Prizes');

$fileString = 'prizesAvailable.txt';

function getPrizes() {
	global $fileString;
	$optionsArray = array();
	$file = fopen($fileString, "r");
	if($file) {
		$counter = 0;
		while (($buffer = fgets($file, 4096)) !== false) {
			$lineTokens = explode('-', trim($buffer));
			$optionsArray[$counter] = $lineTokens[0].' - $'.$lineTokens[1];
			$counter++;
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	return $optionsArray;
}

function addPrizes() {
	global $fileString;
	$prizeName = $_POST['prizeName'];
	$prizeValue = $_POST['prizeValue'];
	$file = fopen($fileString, "a");
	if($file) {
		fwrite($file, $prizeName.'-'.$prizeValue."\n");
		fclose($file);
	}
}

function deletePrizes() {
	global $fileString;
	$toDelete = array();
	$linesArray = array();
	foreach($_POST['delPrize'] as $delRow) {
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

if(isset($_POST['addPrizes'])) {
	addPrizes();
} else if(isset($_POST['delPrizes'])) {
	deletePrizes();
}
$prizesArray = getPrizes();?>

<h1>Manage Available Prizes</h1>
<form id="managePrizes" action='managePrizes.php' method="post">
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					Prizes List
				</th>
			</tr>
			<?php for($i=0;$i<count($prizesArray);$i++) { ?>
				<tr>
					<td>
						<?php print $prizesArray[$i] ?>
					</td><td>
						<input type="checkbox" name="delPrize[]" value="<?php print $i?>" />
					</td>
				</tr>
			<?php }?>
			<tr>
				<td>
					Prize - <input type="text" name="prizeName" style="width:100px; ;"/>
				</td><td>
					$<input type="text" name="prizeValue" style="width:40px;" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="addPrizes" value="Add Prizes" />
				</td><td>
					<input type="submit" name="delPrizes" value="Delete Prizes" />
				</td>
			</tr>
		</table>
	</div>
</form>
		
<?php $container->printFooter(); ?>
