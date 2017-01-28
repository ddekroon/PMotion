<?php session_start();
$control = 1; //used for security, brings them to the control panel login instead of the members login
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
    <head runat="server" >
        <title>View a Schedule</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.0/build/reset/reset-min.css">
        <link rel="stylesheet" href="../css/style.css" type="text/css" media="all" charset="utf-8" />
        <script type="text/javascript" src='/GlobalFiles/jquery2.0.2.js'></script>
		
        <!-- begin google tracking code -->
         <script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            var pageTracker = _gat._getTracker("UA-2180518-1");
            pageTracker._initData();
            pageTracker._trackPageview();
        </script>
        <!-- end google tracking code -->
    </head>
    <body>
        <div id="search">
            <?php require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control/Search/searchBar.php'); ?>
        </div>      
        <div id="container" >
            <?php require_once('../menu.php');?>
            <div id="content">	    	 
                <? require_once('pendingScheduleSource.php'); ?>
            </div>	  
        </div>
    </body>
</html>
