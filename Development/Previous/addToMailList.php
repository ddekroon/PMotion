<?php /*****************************************
File: addToMailList.php
Creator: Alex Eckensweiler
Created: May 23rd 2014
Adds an email to the addressdb
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
			function checkYesNo() {
				return confirm('Are you sure you want to add this email?');
			}
		</script>";
$container = new Container('Add Email Addresses', '', $javaScript);

function addEmail($addEmail) {
	global $addressesTable, $container, $dbConnection;	
	$addEmail = $dbConnection->real_escape_string($addEmail);
	$addQuery = "INSERT INTO $addressesTable (EmailAddress)  VALUES ('$addEmail');";
	if(!$dbConnection->query($addQuery)) $container->printError( 'Error adding email - '.$dbConnection->error);
	else {
	$container->printSuccess('Job done, '.$addEmail.' added');
	}
}
if(isset($_POST['submitButton'])) {
	addEmail($_POST['addEmail']);
}?>

<form action=<?php print $_SERVER['PHP_SELF']?> method='post' id='Schedule'>
<h1>Add Emails to Address Database</h1>
<div class='tableData'>
	<table class='nostyle'>
		<tr>
			<td>Email address to add:</td>
			<td>
				<input type='text' size='40' name='addEmail' value='<?php print $addEmail ?>' class='input-text'>
			</td>
		</tr><tr>
			<td colspan='2' class='t-right'>
				<input type='submit' class='input-submit' name='submitButton' value='Submit' onClick='return checkYesNo()' />
			</td>
		</tr>
	</table>
</div>
</form>

<?php $container->printFooter(); ?>