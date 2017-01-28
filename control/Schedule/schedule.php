<?php session_start();
$control = 1; //used for security, brings them to the control panel login instead of the members login
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once('class_lib.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel='stylesheet' type='text/css' href='includeFiles/scheduleStyle.css'/>
		<title>Schedule</title>
	</head>
	<body>
		<?php if($leagueID == '') {
				if(($leagueID = $_GET['leagueID']) == '') {
				$leagueID = 0;
			}
		}
		if($type == '') {
			if(($type = $_GET['type']) == '') {
				$type = 'show';
			}
		}
		
		$league_schedule = new Schedule($leagueID, $type); ?>
	</body>
</html>