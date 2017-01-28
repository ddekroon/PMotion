<?php /*****************************************
File: createFAQ.php
Creator: Derek Dekroon
Created: July 16/2013
Creates a new FAQ database entry.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$container = new Container('Create FAQ', 'faqStyle.css'); 

if(isset($_POST['addFAQ'])) {
	$title = mysql_escape_string($_POST['title']);
	$body = mysql_escape_string($_POST['body']);
	
	if(strlen($title) < 5 || strlen($body) < 5) {
		print 'String length for title or body too short, must be over 5 characters<br />';
	} else {
		mysql_query("INSERT INTO $faqTable (faq_title, faq_body) VALUES ('$title', '$body')")
			or die('ERROR adding FAQ - '.mysql_error());
		print 'FAQ added<br />';
	}
}?>

<form name="createForm" method="post" action="<?php print $_SERVER['PHP_SELF'] ?>">
<h1>Create FAQ</h1>
<div class='tableData'>
	<table>
		<tr>
			<th colspan=2>
				Submission Form
			</th>
		</tr><tr>
			<td>
				Title
			</td><td>
				<input type='text' style="width:800px" name="title" 
					value="<?php print htmlentities($_POST['title'], ENT_QUOTES) ?>" />
			</td>
		</tr><tr>
			<td>
				Body
			</td><td>
				<textarea style='width:800px; height:400px;' name="body"><?php print htmlentities($_POST['body'], ENT_QUOTES); ?></textarea>
			</td>
		</tr><tr>
			<td colspan=2>
				<input type="submit" name="addFAQ" value="Create FAQ" />
			</td>
		</tr>
	</table>				
</div>
</form>