<?php /*****************************************
File: searchResults.php
Creator: Derek Dekroon
Created: July 16/2013
Program used to show the data for a searched FAQ.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
	
$_GET['faqID'] == ''?$faqID = 0: $faqID = $_GET['faqID'];

if (isset($_POST['delFAQ'])) {
	mysql_query("DELETE FROM $faqTable WHERE faq_id = $faqID") or die('ERROR deleting FAQ - '.mysql_error());
	header('Location: createFAQ.php');
}

$container = new Container('FAQ Node', 'howToStyle.css');

if($faqID == 0) {
	print 'Invalid FAQ item selected<br />';
	exit(0);
}

if(isset($_POST['updateFAQ'])) {
	$title = mysql_escape_string($_POST['title']);
	$body = mysql_escape_string($_POST['body']);
	
	if(strlen($title) < 5 || strlen($body) < 5) {
		print 'String length for title or body too short, must be over 5 characters<br />';
	} else {
		mysql_query("UPDATE $faqTable SET faq_title = '$title', faq_body = '$body' WHERE faq_id = $faqID")
			or die('ERROR updating FAQ - '.mysql_error());
		print 'FAQ updated<br />';
	}
}

$sql = "SELECT * FROM $faqTable WHERE faq_id = $faqID";
$sql_query = mysql_query($sql) or die('error getting query - '.mysql_error());
$faqArray = mysql_fetch_array($sql_query); ?>

<form name="createForm" method="post" action="<?php print $_SERVER['PHP_SELF'].'?faqID='.$faqID ?>">
	<h1><?php print htmlentities($faqArray['faq_title'], ENT_QUOTES) ?></h1>
	<div class='tableData'>
		<table>
			<tr>
				<th colspan=2>
					FAQ Entry
				</th>
			</tr><tr>
				<td>
					Title
				</td><td>
					<input type='text' style="width:800px" name="title" 
						value="<?php print htmlentities($faqArray['faq_title'], ENT_QUOTES) ?>" />
				</td>
			</tr><tr>
				<td >
					Body
				</td><td>
					<textarea style='width:800px; height:400px;' name="body"><?php print htmlentities($faqArray['faq_body'], ENT_QUOTES); ?></textarea>
				</td>
			</tr><tr>
				<td colspan=2>
					<input type="submit" name="updateFAQ" value="Update FAQ" />
					<input type="submit" name="delFAQ" value="Delete FAQ" onclick="return confirm('Are you sure you want to delete this FAQ?')"/>
				</td>
			 </tr>
		</table>
	</div>
</form>