<?php /*****************************************
File: index.php
Creator: Darryl Moyers
Edited By: Derek Dekroon
Created: ???
Main login screen
EDITS: July 24/2013
	Changed the page to work with the global class_container. Also changed up how the site checks to see if the username
	is a registered account. Now when security is brought up the session needs to be one of the employees for every page, 
	not just this one.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$container = new Container('PMotion Control Panel'); ?>
<h1>Perpetual Motion's Control Panel</h1>
<?php $container->printFooter(); ?>