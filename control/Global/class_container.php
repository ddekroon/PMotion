<?php session_start();
$control = 1; //used for security, brings them to the control panel login instead of the members login
date_default_timezone_set('America/New_York');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'dbConnect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'whoGetsEmailed.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'globalDeclarations.php');

class Container {
	
	public function __construct($title = 'PMotion Control', $stylePage = '', $javaScript = '') {
		$this->printHeader($title, $stylePage, $javaScript);
	}
	
	private function printHeader($title, $stylePage, $javaScript) {
global $jQueryPage, $styleRoot, $activeTabIndex; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
	<head runat="server" >
		<title><?php print $title ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<!-- <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.0/build/reset/reset-min.css">
		<link rel="stylesheet" href="http://data.perpetualmotion.org/control/css/style.css" type="text/css" media="all" charset="utf-8" /> -->
		<link rel='stylesheet' type='text/css' href='<?php print $stylePage ?>'/>
		
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/reset.css" />
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/main.css" />
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/2col.css" title="2col" />
		<link rel="alternate stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/1col.css" title="1col" />
		<!--[if lte IE 6]><link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/main-ie6.css" /><![endif]-->
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/jquery-ui.css" />
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/style.css" />
		<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php print $styleRoot ?>css/mystyle.css" />
		
		<script type="text/javascript" src="/GlobalFiles/jquery2.0.2.js"></script>
		<script type="text/javascript" src="/control/Global/Style/js/toggle.js"></script>
		
		
		<!--<script type="text/javascript" src="/control/Global/Style/js/switcher.js"></script>
		<script type="text/javascript" src="/control/Global/Style/js/ui.core.js"></script>
		<script type="text/javascript" src="/control/Global/Style/js/ui.tabs.js"></script> -->
		<script type="text/javascript" src="/control/Global/Style/js/jquery-ui.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#tabContainer").tabs({active: $("#tabIndex").val()});
				$('#searchBox').focus();
			});
		</script>
		
		<?php print $javaScript ?>
	</head>
	<body>
		<div id="main">
			<!-- Tray -->
			<div id="tray" class="box">
				<p class="f-left box">
					<!-- Switcher -->
					<span class="f-left" id="switcher">
						<a href="javascript:void(0);" rel="1col" class="styleswitch ico-col1" title="Display one column">
							<img src="/control/Global/Style/design/switcher-1col.gif" alt="1 Column" />
						</a>
						<a href="javascript:void(0)" rel="2col" class="styleswitch ico-col2" title="Display two columns">
							<img src="/control/Global/Style/design/switcher-2col.gif" alt="" />
						</a>
					</span> Project: <strong>Control Panel</strong>
				</p>
				<p class="f-right">
					User: <strong><a href="/control/"><?php print $_SESSION['username']; ?></a></strong>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<strong><a href="/Login/logout.php?siteDirect=control" id="logout">Log out</a></strong>
				</p>
			</div>
			<!--  /tray -->
			<hr class="noscreen" />
			<!-- Menu
			<div id="menu" class="box">
			<ul class="box f-right">
			<li><a href="http://www.free-css.com/"><span><strong>Visit Site &raquo;</strong></span></a></li>
			</ul>
			<ul class="box">
			<li id="menu-active"><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			<li><a href="http://www.free-css.com/"><span>Lorem ipsum</span></a></li>
			</ul>
			</div> -->
			<!-- /header -->
			<hr class="noscreen" />
			<!-- Columns -->
			<div id="cols" class="box">
			<!-- Aside (Left Column) -->
				<div id="aside" class="box">
					<div class="padding box">
					<!-- Logo (Max. width = 200px) -->
						<p id="logo">
							<a href="http://perpetualmotion.org"><img src="/Logos/Perpetualmotionlogo2.jpg" alt="PMotion" /></a>
						</p>
						<!-- Search -->
					<?php require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/searchBar.php'); ?>
					
						<!-- Create a new project -->
						<!--<p id="btn-create" class="box"><a href="http://www.free-css.com/"><span>Create a new project</span></a></p> -->
					</div>
					<div class="padding box">
					<?php require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'menu.php');?>
					</div>
				</div>
				<!-- /aside -->
				<hr class="noscreen" />
				<!-- Content (Right Column) -->
				<div id="content" class="box">
					<!--<div id="search">
					</div><div id="container">
					<div id="content"> -->
				<?php }
	
	public function printFooter() { 
		global $dbConnection; ?>
				</div>
			</div>
			<!-- /cols -->
			<hr class="noscreen" />
			<!-- Footer -->
			<div id="footer" class="box">
				<p class="f-left">&copy; 2015 <a href="http://perpetualmotion.org/">Perpetual Motion</a>, All Rights Reserved &reg;</p>
				<p class="f-right">Templates by <a href="http://www.adminizio.com/">Adminizio</a></p>
			</div>
		<!-- /footer -->
		</div>
		<!-- /main -->
	</body>
</html>
		<?php if(isset($dbConnection)) {
			$dbConnection->close();
		}
	}
	
	public function printWarning($string) { ?>
		<p class="msg warning"><?php print $string ?></p>
	<?php }
	
	public function printInfo($string) { ?>
		<p class="msg info"><?php print $string ?></p>
	<?php }
	
	public function printSuccess($string) { ?>
		<p class="msg done"><?php print $string ?></p>
	<?php }
	
	public function printError($string) { ?>
		<p class="msg error"><?php print $string ?></p>
	<?php }
}