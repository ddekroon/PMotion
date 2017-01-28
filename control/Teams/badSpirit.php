<?php /*************************
* Derek Dekroon
* derek@perpetualmotion.org
* June 13, 2012
* badSpirit.php
*
* This program is used to show, and update all spirit scores of 3.5 or lower.
********************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php');
$javaScript = "<script type='text/javascript'>
	function checkAll() {
		var field = document.getElementsByName('approve[]');
		for (i = 0; i < field.length; i++)
			field[i].checked = true ;
	}
	
	function checkYesNo() {
		return confirm('Are you sure you want to change these spirit scores?');
	}
</script>";
$container = new Container('Bad Spirit', 'includeFiles/teamStyle.css', $javaScript);

require_once('includeFiles/sptFunctions.php');
require_once('includeFiles/class_spirit.php');

if(isset($_POST['ApproveSpirit'])) {
	if (($spiritCount = $_POST['spiritCount']) == '') {
		$spiritCount = 0;
	}
	updateDatabase($spiritCount);
} else if(isset($_POST['DeleteButton'])) {
	if (($spiritCount = $_POST['spiritCount']) == '') {
		$spiritCount = 0;
	}
	deleteSpirits($spiritCount);
}





$badSpiritSubmissions = getBadSpiritSubmissions();?>

<form NAME='badspirit' METHOD='POST' action='badSpirit.php' onSubmit="return checkYesNo()">
<?php storeHiddenVariables($badSpiritSubmissions); ?>
<h1>Spirit Score Screener</h1>
<div class='tableData'>
	<table>
		<?php printBadSpiritHeader();
		for($i=0;$i<count($badSpiritSubmissions);$i++) {
			printBadSpiritNode($i, $badSpiritSubmissions[$i]);
		}
		printBadSpiritFooter(count($badSpiritSubmissions)); ?>
	</table>
</div>
</form>
		
<?php $container->printFooter(); ?>