<?php date_default_timezone_set('America/New_York');

	if (get_magic_quotes_gpc()) {
    	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    	while (list($key, $val) = each($process)) {
        	foreach ($val as $k => $v) {
            	unset($process[$key][$k]);
            	if (is_array($v)) {
                	$process[$key][stripslashes($k)] = $v;
                	$process[] = &$process[$key][stripslashes($k)];
            	} else {
            	    $process[$key][stripslashes($k)] = stripslashes($v);
            	}
        	}
    	}
    	unset($process);
	}

    $name = $_POST['name'];
	$email = $_POST['email'];
	$guardName = $_POST['guardName'];
	$guardEmail = $_POST['guardEmail'];
	$errorString = "";
	$sportID = $_GET['sportID'];

//---------------------------------------------------------------------------------------
//							START OF FUNCTIONS
//---------------------------------------------------------------------------------------

function postForm() { //This function posts the form with any input the user may have inputted?>

	<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Waiver</title>
			<style type="text/css">
				body{
					font-family:Arial, Helvetica, sans-serif;
				}
				h1{
					font-size:18px;
				}
				h2{
					font-size:16px;
					font-weight:bold;
				}
				h3{
					font-size:13px;
					text-decoration:underline;
				}
				p,ol, table td{
					font-size:13px;
				}
			
				.Table
				{
					display: table;
					width:600px;
					text-align:justify;
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
					width:300px;
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
	</head>
    <?php global $name, $email, $guardName, $guardEmail, $errorString, $sportID ?>
    <form name="waiver" method="post" action="<?php print $_SERVER['PHP_SELF'].'?sportID='.$sportID ?>">

    <div class="Table">
        <div class="Row" align="center">
            <h1>RELEASE OF LIABILITY, WAIVER OF CLAIMS</h1>
        </div>
        <div class="Cell">
            <h2>ASSUMPTION OF RISKS AND INDEMNITY AGREEMENT</h2>
              <p><b>By signing this document you will waive certain legal rights, including the right to sue.</b></p>
                        <h2>PLEASE READ CAREFULLY</h2>
        </div>
        <div class="Row">
                <h3>AWARENESS AND ASSUMPTION OF RISK</h3>
                <p>I am aware that sports involves risks including risk of personal injury, death, property damage, expense and related loss, including loss of income. Included in these risks are negligence on the part of Perpetual Motion Sports & Entertainment Inc. (known as "Perpetual Motion"), its directors, officers, officials, shareholders, employees and volunteers, other participants and owners of the facilities where the activities occur (referred to in the rest of this agreement as PERPETUAL MOTION and OTHERS). I freely accept and fully assume all such risks and the possibility of personal injury, death, property damage, expense and related loss, including loss of income.</p>
                
                <h3>RELEASE OF LIABILITY, WAIVER OF CLAIMS AND INDEMNITY AGREEMENT</h3>
                <p>In consideration of PERPETUAL MOTION accepting my application to participate in this activity, I agree:</p>
                
                <ol>
                    <li>This is a continuous waiver (current and future years) and I waive any and all claims that I may have in future against PERPETUAL MOTION and OTHERS.</li>
                    <li>To release PERPETUAL MOTION and OTHERS from any and all liability for any personal injury, death, property damage, expense and related loss, including loss of income that I or my next of kin may suffer as a result of my participation in this activity, due to any cause whatsoever, including negligence, breach of contract or breach of any statutory duty of care.</li>
                    <li>To hold harmless and indemnify PERPETUAL MOTION and OTHERS from any and all liability for any damage to property of, or personal injury to, any third party, resulting from my participation in this activity.</li>
                    <li>That this agreement is binding on not only myself but my next if kin, heirs, executors, administrators and assigns.</li>
                </ol>
                <h2>I HAVE READ THIS AGREEMENT AND UNDERSTAND IT. I AM AWARE THAT BY SIGNING THIS DOCUMENT I AM WAIVING CERTAIN RIGHTS WHICH I OR MY NEXT OF KIN, HEIRS, EXECUTORS, ADMINISTRATORS AND ASSIGNS MAY HAVE AGAINST PERPETUAL MOTION AND OTHERS.  I WARRANT THAT AT THE TIME OF SIGNING, I AM PHYSICALLY FIT TO PARTICIPATE.</h2>
            </div>
            <div class="colourRow" align="center">
            <input type="checkbox" name="consent" /><b><i><font size=2>* By checking this box, I agree to all of the terms and conditions listed above.</i></b></font>
            </div>
            <div class="Row" align="center">
            	<font color="#FF0000"><?php print $errorString ?></font>
            </div>
        <div class="Row">
            <div class="noBorderCell">
                <div class="Column" align="left">
                <font size=2> * Name: <br /></font>
                <input name="name" type="text" value="<?php print $name?>"/>
                </div>
                <div class="Column" align="right">
                <font size=2> Parent/Guardian's Name (if under 18): <br /></font>
                <input name="guardName" type="text" value="<?php print $guardName ?>"/>
                </div>
            </div>
        </div>
        <div class="Row">
            <div class="noBorderCell">
            	<div class="Column" align="left">
            		<font size=2> * Email Address: <br /></font>
					<input name="email" type="text" value="<?php print $email?>"/>
            	</div>
            	<div class="Column" align="right">
            		<font size=2> Parent/Guardian's Email Address: <br /></font>
					<input name="guardEmail" type="text" value="<?php print $guardEmail?>"/>
            	</div>
        	</div>
        </div>
        <div class="colourRow" align="center">
            <b><i><font size=2> Dated this </i><u><?php print date(jS)."</u><i> day of </i><u>".date("F")."</u><i> in the year </i><u>".date("Y")."</u>.";?></i></b></font>
        </div>
        <div class="Row">
        	<div class="noBorderCell">
            	<div class="Column" align="left">
			  		<input name="Submit" type="Submit" value="Submit" /></TD>
                </div>
                <div class="Column" align="right">
					<input type='Button' name='printit' value='Print Form' onclick='javascript:window.print();'>
                </div>
            </div>
        </div>
    </div>
        
    </form>
	</html> 
<?php } //end of printForm function
	
	
	//This function checks if the user entered a name, an email, and pressed the 'I agree' checkbox
	//It also checks if either both guardian fields were submitted, or neither
	//Finally it checks for valid email addresses using some built in PHP functions
	function checkValues() {
		global $errorString;
		global $name, $email, $guardName, $guardEmail;
		
		$errorString = "";
		
		if(!isset($_POST['consent'])) {
			$errorString.='*Please press the checkbox<br />';
		}
		if($name == "") {
			$errorString.='*Please enter your name<br />';
		} else {
			if (!isValid($name)) {
				$errorString.='Please enter a valid name composed of: letters, numbers, and the caharacters \' and -<br />';
			}
		}
		if($_POST['email'] == "") {
			$errorString.='*Please enter your email<br />';
		} else { 
			if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  				$errorString.="*Please enter a valid email<br />";
			}
		}
		if($guardName != "") {
			if (!isValid($guardName)) {
				$errorString.='Please enter a valid guardian name composed of: letters, numbers, and the caharacters \' and -<br />';
			}
		}
		if($_POST['guardEmail'] != "" && !filter_var($_POST['guardEmail'], FILTER_VALIDATE_EMAIL)) {
  			$errorString.='*Please enter a valid guardian\'s email<br />';
		}
		if($_POST['guardEmail'] != "" && $_POST['guardName'] == "") {
			$errorString.='*Please enter your guardian\'s name<br />';
		}
		if($_POST['guardName'] != "" && $_POST['guardEmail'] == "") {
			$errorString.='*Please enter your guardian\'s email<br />';
		}
		if($email == $guardEmail && $email != "") {
			$errorString.='*Please enter two separate email\'s<br />';
		}
	}//end of checkValues function
	
	function isValid($str) {
    	return !preg_match("/[^A-Za-z0-9\'\- @\.]/", $str);
	} //end of is Valid function
	
	//This function expedites the process of querying a database
	function query($query_string){
		$quer_line=mysql_query($query_string) or die("TEST ".mysql_error());
		$array_line=mysql_fetch_array($quer_line);
		return ($array_line);
	}
	
	//<!--------------------------------------------------------------------------------------
    //									START OF THE LOGIC
    //----------------------------------------------------------------------------------------
	 
    if(!isset($_POST['Submit'])) { //check if the submit button was pressed
    	postForm(); //if it wasn't, post the form
    } else { 		//If submit was pressed
		checkValues();
		if($errorString == "") { //Check if the data is good
			require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
			
			//If the data is good
			$row = query("SELECT MAX(waiver_id) AS maxID FROM waivers"); //get new max waiverID number
			$maxID = $row["maxID"];
			$newID = $maxID+1;
			
			$dateString = date("M d, Y; H:i:s");
		
			$escapedName = mysql_real_escape_string($name);
			$escapedEmail = mysql_real_escape_string($email);
			$escapedGuardName = mysql_real_escape_string($guardName);
			$escapedGuardEmail = mysql_real_escape_string($guardEmail);
			
			//Insert the new waiver #, form name, email, guardian info, and timestamp (as a string)
			$insertQuery = "INSERT INTO waivers (waiver_id, waiver_name, waiver_email, waiver_guard_name, waiver_guard_email, waiver_date, waiver_sport_id)
				VALUES ('$newID', '$escapedName', '$escapedEmail', '$escapedGuardName', '$escapedGuardEmail', '$dateString', $sportID)";
			mysql_query($insertQuery) or die(mysql_error());
		
			//tell the user their input was successful
			// redirect back to url visitor came from
			header("Location: /ThankYouWaiver.htm");
			//print '<iframe width="1000" height="1000" src="../ThankYouNote.htm" />';
			
		} else { //if the users input is invalid print the form and errors
			if (!isValid($name)){
				$name = "";
			}
			if (!isValid($guardName)){
				$guardName = "";
			}
			if (!isValid($email)){
				$email = "";
			}
			if (!isValid($guardEmail)){
				$guardEmail = "";
			}
			postForm();
		}
	}?>