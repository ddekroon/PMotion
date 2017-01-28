<?php require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'Login'.DIRECTORY_SEPARATOR.'config.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

if(($siteDirect = $_GET['siteDirect']) == '') {
	$siteDirect = 'Reg';
}

if(isset($_SESSION['username'])) {
	if($siteDirect=='control'){
		header('Location: /control/');
	} else {
		header('Location: /Registration/membersPage.php');
	}
} else {
	
	// Check if the cookie exists
	if(isSet($_COOKIE[$cookie_name])) {
		parse_str($_COOKIE[$cookie_name]);
	
		// Make a verification
	
		foreach($admin_usernames as $user) {
			if($usr == $user) {
				// Register the session
				$_SESSION['username'] = $user;
				$_SESSION['userID'] = $userID;
				if($siteDirect=='control'){
					header('Location: /control/');
				} else {
					header('Location: /Registration/membersPage.php');
				}
			}
		}
	} 
} 

if (strlen($_POST['login']) > 0) {
	require_once('do_login.php');
} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<title>Login - Perpetual Motion</title>
		<link rel="stylesheet" type="text/css" href="loginStyle.css" />
    </head>
    
     <style>
		.Table
		{
			display: table;
			width:600px;
			text-align:justify;
			margin:0px auto;
		}
		.Heading
		{
			display: table-row;
			font-weight: bold;
			text-align: center;
			width:900px;
			margin:0px auto;
		}
		.Row
		{
			display: table-row;
			width:500px;
			margin:0px auto;
			padding:5px;
		}
		.colourRow
		{
			display: table-row;
			width:500px;
			height:15px;
			margin:0px auto;
			padding:5px;
			background:#CCCCCC;
		}
		.Column
		{
			width:65px;
			display: table-cell;
			margin:0px auto;
			padding:5px;
	
		}
		.Cell
		{
			width:500px;
			height:25px;
			display: table-cell;
			text-align:center;
			border: solid;
			border-width: medium;
			margin:0px auto;
		}
		.noBorderCell
		{
			width:500px;
			display: table-cell;
			margin:0px auto;
		}
	</style>
    
    <body>
    <form id="loginForm" method="post" action="<?php print $_SERVER['PHP_SELF']."?siteDirect=$siteDirect" ?>" >
      	<div class="Table" align="center">
            <div class="Row" align="center">
                <img <?php print $siteDirect == 'Reg'?'src="/Logos/Perpetualmotionlogo2.jpg" width="300"':'src="/Logos/PerpetualMotionLeaf.jpg" width="170" height="150"' ?>>
            </div>
            <div class="Row" align="center">
            <?php if($siteDirect == 'control'){
                print "<tr><td><h2>Control Panel Login</h2></td></tr>";
            }else{
                print "<tr><td><h2>Online Registration System</h2></td></tr>";
            }?>
            </div>
            <div class="Row" align="center">
           		Please Login or <a tabindex=4 href='/Login/createAccount.php'>Create a New Account</a></td>
            </div>
            <div class="Row" align="center">
				<div class="noBorderCell">
                	<div class="Row">
                    	<div class="Column" align="left">
							Username
						</div>
                        <div class="Column" align="right" id="forgot">
							<a tabindex=5 href='/Login/resetAccount.php?type=Username'>I forgot</a>
						</div>
					</div>
                    <div class="Row" id='textBoxes'>
						<input tabindex=1 type="text" name="username" id='userName' />
					</div>
                    <div class="Row">
                       	<div class="Column" align="left">
                    		Password
						</div>
                        <div class="Column" align="right" id="forgot">
							<a tabindex=6 href='/Login/resetAccount.php?type=Password'>I forgot</a>
						</div>
                    </div>
                    <div class="Row" id='textBoxes'>
						<div class="Column" align="left">
							<input tabindex=2 type="password" name="userPassword" id='password' value='' />
                        </div>
						<div class="Column" align="right" id='show'>
							<input tabindex=7 id='showBox' type="checkbox" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'" /> Show
						</div>
					</div>
                </div>
			</div>	
            <br  />
            <div class="Row" id='bottomButton'>
                <?php if($siteDirect == 'control'){ ?>
                <span id='rememberMeSpan'>
                    <input type="checkbox" checked name="autologin" tabindex=8 value=1/>Remember Me
                </span><span id='loginSpan'>
                    <input id='submitButton' tabindex=3 type="submit" name="login" value="Login" />
                </span>
                <?php } else { ?>
                    <input id='submitButton' tabindex=3 type="submit" name="login" value="Login" />
                <?php } ?>
            </div>
            
        </div>
        
    </form>
    </body>
</html>
