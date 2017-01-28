<?php /*****************************************
File: deleteEmails.php
Creator: Derek Dekroon
Created: June 23/2013
Deletes an email from the addressdb
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function checkYesNo() {
				return confirm('Are you sure you want to delete this email?');
			}
		</script>";
$container = new Container('Delete Email Addresses', '', $javaScript);

function deleteEmail($deleteEmail) {
	global $addressesTable, $container, $dbConnection;	
	$deleteEmail = $dbConnection->real_escape_string($deleteEmail);
	
	$count = mysql_query("SELECT count(*) as count FROM $addressesTable WHERE EmailAddress = '$deleteEmail'") or die(mysql_error()); //get new max waiverID number
	$countCheck = mysql_fetch_assoc($count);
	
	if ($countCheck['count'] != 0) {
	
		$delQuery = "DELETE FROM $addressesTable WHERE EmailAddress = '$deleteEmail'";
		
		if(!$dbConnection->query($delQuery)) {
			print 'Error deleting email - '.$dbConnection->error;
		}
		else {
			$container->printSuccess('Job done, '.$deleteEmail.' deleted');
		}
    }
	else {
		$container->printError('No email found matching '.$deleteEmail);
	}
}
if(isset($_POST['submitButton'])) {
	deleteEmail($_POST['deleteEmail']);
}?>

<form action=<?php print $_SERVER['PHP_SELF']?> method='post' id='Schedule'>
<h1>Delete Emails from Address Database</h1>
<div class='tableData'>
	<div>
		<label>Email address to delete:</label>
		<input type='text' size='40' name='deleteEmail' value='<?php print $deleteEmail ?>' class='input-text'>
	</div>
	<div>
		<input type='submit' class='input-submit' name='submitButton' value='Submit' onClick='return checkYesNo()' />
	</div>
</div>
</form>

<?php $container->printFooter(); ?>