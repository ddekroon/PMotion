<?php /*****************************************
File: recentSentEmails.php
Creator: Derek Dekroon
Created: May 7/2013
Kind've a failsafe for the emails control panel. Adds an email to a list in a txt file whenever an email gets sent.
This way we can track who got emailed last pending a php error.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function CheckAll() {
				with (document.email) {
					for (var i=0; i < elements.length; i++) {
						if (elements[i].type == 'checkbox' && elements[i].name == 'emailNode[]')
							elements[i].checked = true;
					}
				}
				return false;
			}
			function UnCheckAll() {
				with (document.email) {
					for (var i=0; i < elements.length; i++) {
						if (elements[i].type == 'checkbox' && elements[i].name == 'emailNode[]')
							elements[i].checked = false;
					}
				}
				return false;
			}
		</script>";
$container = new Container('Emails Recently Sent', 'includeFiles/emailStyle.css', $javaScript);

$fileString = 'whoGotEmailed.txt';

function deleteRows() {
	global $fileString;
	$toDelete = array();
	$linesArray = array();
	foreach($_POST['emailNode'] as $delRow) {
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

function getEmails() {
	global $fileString;
	$optionsArray = array();
	$file = fopen($fileString, "r");
	if($file) {
		$counter = 0;
		while (($buffer = fgets($file, 4096)) !== false) {
			$optionsArray[$counter] = $buffer;
			$counter++;
		}
		if (!feof($file)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($file);
	}
	return $optionsArray;
}


if(isset($_POST['delEmails'])) {
	deleteRows();
}

$emailsArray = getEmails(); ?>

<form name="email" action='recentSentEmails.php' method="post">
<h1>Recent Emails</h1>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=2>
				Sent Emails
			</th>
		</tr><tr>
			<td>
				Email
			</td><td>
				Del
			</td>
		</tr>
		<?php for($i=0;$i<count($emailsArray);$i++) { ?>
			<tr>
				<td>
					<?php print $emailsArray[$i] ?>
				</td><td>
					<input type="checkbox" name="emailNode[]" value="<?php print $i ?>" />
				</td>
			</tr>
		<?php }?>
			<tr>
				<td colspan=2>
					<input type="submit" name="delEmails" value="Remove Emails" onClick="return confirm('Are you sure?')"/>
					<input type="submit" name="selAll" value="Check All" onClick="return CheckAll()"/>
					<input type="submit" name="deselAll" value="Uncheck All" onClick="return UnCheckAll()" />
				</td>
			</tr>
		</table>
	</div>	
</form>

<?php $container->printFooter(); ?>
