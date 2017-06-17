<?php 


//Function to populate the previous teams list
//How it works:
//Queries database
//Loops through query pulling out teams registered to the user

function createPreviousTeamsList(){
	global $userID, $teamsTable, $sportID, $sportsTable, $leaguesTable;
	$query = "SELECT * FROM $teamsTable 
		Inner Join $leaguesTable ON $teamsTable.team_league_id = $leaguesTable.league_id
		Inner Join $sportsTable ON $leaguesTable.league_sport_id = $sportsTable.sport_id
		WHERE $leaguesTable.league_sport_id = $sportID AND team_managed_by_user_id = $userID";
	$oldTeamsArray=mysql_query($query);
	$list="";

	while ($oldTeam = mysql_fetch_array($oldTeamsArray)){
		$teamNameFromDB=stripslashes($oldTeam['team_name']);
		$leagueFromDB=$oldTeam['league_name'];
		$teamIDFromDB=$oldTeam['team_id'];

		$list.="<tr>
			<td align=center>
				<font face='Verdana'>
					<a href='http://perpetualmotion.org/Registration/signupNew.php?sportID=$sportID&teamLeagueID=$teamIDFromDB'>
						$teamNameFromDB
					</a>
				</font>
			</td><td>
				<font face='Verdana'>$leagueFromDB</font>
			</td></tr>";
	}
	return $list;
}// End function

function printForm() {
	global $league, $teamName, $capFirst, $capLast, $capSex, $capEmail, $capPhone, $capMaleSelected, $capFemaleSelected;
	global $people, $playerFirst, $playerLast, $playerSex, $playerEmail, $comments, $footer;
	global $playerMaleSelected, $playerFemaleSelected, $playerNoneSelected;
	global $sport, $user, $title, $sportHeader, $userTeamsNum, $secondaryError, $seas_name, $registration_due; 
	global $leaguesArray, $error, $logo, $quer2, $semi, $method; ?>

    <form NAME='update' METHOD='POST' action=<?php print $_SERVER['PHP_SELF']?>?sport=<?php print $sport?>>
        <INPUT TYPE='hidden' NAME='sport' VALUE='$sport'>
        <INPUT TYPE='hidden' NAME='user' VALUE='$user'>
        <head><title><?php print $title?></title>
            <link rel="stylesheet" type="text/css" href="design.css"/>
        </head>
        <font face='arial' size=2>User: <B><?php print $user;?></B></font>
        <font face='verdana' size=1>(<a href=logout.PHP style='text-decoration: none;'>Logout</a>)</font>
    
        <table class="master" ALIGN="CENTER">
            <tr>
                <td colspan=4>
                    <TABLE class="logo" align="center">
                        <tr>
                            <td><img src=<?php print $logo?>></td>
                        </tr>
                    </TABLE>
                </td>
            </tr>
            <tr>
                <td colspan=4><font face='verdana' size=4>
                    <B><?php print $sportHeader?></B></font>
                </td>
            </tr><?php
                    
//##############################################################################################################################################
//                                          END OF TITLE, TEAM, AND HEADER SECTION
//#############################################################################################################################################    
   
//##############################################################################################################################################
//                                                 PREVIOUS TEAMS LIST
//##############################################################################################################################################
	
             // IF THERE'S ALREADY A TEAM REGISTERED BY THE USER IN THE DATABASE, OFFER THE OPTION TO UPDATE THAT REGISTRATION INSTEAD
            if($userTeamsNum >=1 and (!isset($_POST['register']))){
                if(strlen($secondaryError)<5){ ?>
                
                    <tr>
                        <td colspan=3 align=center>
                            <table class="previousTeams" border="3" >
                                <tr>
                                    <td colspan=5 align=center>
                                        <BR />
                                        <font face='verdana' size="3" color=red><B>Do you want to register one of these previous teams for <? print $seas_name?>?
                                            </B></font>
                                    </td>
                                <tr>
                                    <td align="center">
                                        <font face='verdana' size="2"><b>Team Name</b></font>
                                    </td><td align="center">
                                        <font face='verdana' size="2"><b>League</b></font>
                                    </td>
                                </tr>
                                    
                                <?php  //Calls function to populate the previous teams list, see above
                                $list=createPreviousTeamsList();
                                print $list; ?>
                            </table>
                        </td>
                    </tr><?php
                }
            } ?>
            <tr>
                <td colspan=5 align=center>
                    <font face='verdana' color=red><b>Registration due date: <?php print $registration_due; ?></b></font>
                </td>
            </tr><?php
            if ($error!=''){ ?>
                <tr>
                    <td colspan=4 align="center">
                        <font face='verdana' color="red"><?php print $error?></font>
                    </td>
                </tr>        
            <?php }
            
            if ($secondaryError!=''){ ?>
                <tr>
                    <td colspan=4 align="center">
                        <font face='verdana' color="red"><?php print $secondaryError?></font>
                    </td>
                </tr>
            <?php }
            
            if ((isset($_POST['register']) or isset($_POST['confirm']) or isset ($_POST['save']) or strlen($secondaryError)>1) and $error==""){
                //Print footer confirmation page if one of the buttons was pressed and there were no errors ?>
                
                <tr>
                    <td>
                        <?php print $footer?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <BR>
                    </td>
                </tr><?php
            }
        
            if ((!isset($_POST['register']) and !isset($_POST['confirm']) and !isset($_POST['save'])) or $error!=""){
                //If a button is not pressed or there is an error, show the form ////Derek asks, is this possible? ?>
        
                <tr BGCOLOR="#CCCCCC">
                    <td colspan=4 align="center">
                        <b>1. Select Your Division and Choose Team Name</b>
                        <br>
                        <font face='verdana' size=1>Please choose a division.</font>
                    </td>
                </tr>
                <tr>
                    <td colspan=4>
                        <TABLE class="leagueTeamName" align=center>
                            <tr BGCOLOR="white">
                                <td align="center" colspan=2>
                                    <B>Preferred League:</B>
                                </td><td align="left" colspan=2>
                                    <select name='semi'><option value='nullVal'>League</option>
                                    <?php $leagueDD='';
                                    while($leagueNode = mysql_fetch_array($quer2)) {
                                        if($leagueNode['semi_identifier'] == $semi){
                                            $leagueDD.= "<option value selected='$leagueNode[semi_identifier]'>$leagueNode[category]</option>"."<BR>";
                                        } else {
                                            $leagueDD.= "<option value='$leagueNode[semi_identifier]'>$leagueNode[category]</option>";
                                        }
                                    }//end while ?>
                                    <?php print $leagueDD.'<BR />';?>
                                    </select>
                                </td>
                            </tr><tr>
                                <td align='center' colspan=2>
                                    <b>Team Name: </b>
                                </td><td colspan=2>
                                    <input type='text' name='teamName' VALUE="<?php print htmlentities($teamName, ENT_QUOTES);?>" size=30>
                                </td>
                            </tr>
                        </TABLE>
                    </td>
                </tr>        
                <INPUT TYPE="hidden" NAME="holderVariable" VALUE='<?php print htmlentities($semi, ENT_QUOTES);?>'>
                
<?php //#########################################################################################################################################
//                              THE FORM - CAPTAIN INFORMATION SECTION
//#############################################################################################################################################?>
    
                <tr BGCOLOR='#CCCCCC'>
                    <td COLSPAN=4 align='center'>
                        <B>2. Captain Information</B>
                        <BR />
                        <font face='verdana' SIZE=1>
                            The captain is the first person we'll contact with team inquiries and is responsible for submitting scores.
                        </FONT>
                    </td>
                </tr>
                <tr>
                    <td colspan=4>
                        <TABLE class="playerTable" align=center cellspacing=10 cellpadding=1>
                            <tr BGCOLOR='white'>
                                <td align='center'>
                                    <B>First Name:</B>
                                </td><td align='center'>
                                    <B>Last Name:</B>
                                </td><td align='center'>
                                    <B>Sex:</B>
                                </td>   
                            </tr>
                            <tr BGCOLOR="white">
                                <td>
                                    <INPUT TYPE="text" NAME="capFirst" VALUE="<?php print htmlentities($capFirst, ENT_QUOTES);?>" SIZE=40>
                                </td><td>
                                    <INPUT TYPE="text" NAME="capLast" VALUE="<?php print htmlentities($capLast, ENT_QUOTES);?>" SIZE=40>
                                </td><td align=center>
                                    <SELECT NAME="capSex">
                                        <OPTION VALUE="">Select One</OPTION>
                                        <OPTION VALUE="Male" <?php print $capMaleSelected;?>>Male</OPTION>
                                        <OPTION VALUE="Female" <?php print $capFemaleSelected;?>>Female</OPTION>
                                    </SELECT>
                                </td>
                            </tr>
                            <tr BGCOLOR="white">
                                <td align="center">
                                    <B>Email:</B>
                                </td><td align="center">
                                    <B>Phone Number:</B>
                                </td>
                            </tr>
                            <tr BGCOLOR="white">
                                <td>
                                    <INPUT TYPE="text" NAME="capEmail" VALUE="<?php print htmlentities($capEmail, ENT_QUOTES);?>" SIZE=40>
                                </td><td align="center">
                                    <INPUT TYPE="text" NAME="capPhone" VALUE="<?php print htmlentities($capPhone, ENT_QUOTES);?>" SIZE=15>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
<?php //########################################################################################################################################
//                              THE FORM - PLAYER INFORMATION SECTION
//############################################################################################################################################?>
                            
                <tr>
                    <td colspan=4>
                        <TABLE>
                            <TR BGCOLOR='#CCCCCC'>
                                <TD COLSPAN=5 align='center'>
                                    <font FACE='verdana' SIZE=2><B>3. Player Information</B></FONT>
                                    <BR /><font face='verdana' SIZE=1><font face='verdana' COLOR='red'>*</FONT>
                                        The first player listed should be the captain, 
                                            the second will be an alternate contact if the captain is unavailable.</FONT>
                                </TD>
                            </TR>
                            <tr BGCOLOR='white'>
                                <td align='center'>
                                    <br />
                                </td><td align='center'>
                                    <font face='verdana' SIZE='2'><B>First Name:</B></FONT>
                                </td><td align='center'>
                                    <font face='verdana' SIZE='2'><B>Last Name:</B></FONT>
                                </td><td align='center'>
                                    <font face='verdana' SIZE='2'><B>Email:</B></FONT>
                                </td><td align='center'>
                                    <font face='verdana' SIZE='2'><B>Sex:</B></FONT>
                                </td>
                            </tr>
                            <?php 
                            for($v=1; $v<=$people; $v++){?>
                                <tr BGCOLOR="white">
                                    <td>
                                        <?php 
                                        if($v<=2){
                                            print $v.'.'?>
                                            <font face='verdana' COLOR="red" size=1>*</FONT>
                                        <?php 
                                        } else { //adds the asterisk
                                            print $v.'.';
                                        }?>
                                    </td><td>
                                        <INPUT TYPE="text" NAME="playerFirst<?php print $v?>" VALUE="<?php print 
                                            htmlentities($playerFirst[$v], ENT_QUOTES);?>" SIZE=27>
                                    </td><td>
                                        <INPUT TYPE="text" NAME="playerLast<?php print $v?>" VALUE="<?php print 
                                            htmlentities($playerLast[$v], ENT_QUOTES);?>" SIZE=30>
                                    </td><td>
                                        <INPUT TYPE="text" NAME="playerEmail<?php print $v?>" VALUE="<?php 
                                            print htmlentities($playerEmail[$v], ENT_QUOTES);?>" SIZE=30>
                                    </td><td align=center>
                                        <SELECT NAME="playerSex<?php print $v?>"><font face='verdana' SIZE=2>
                                            <OPTION VALUE="" <?php print $playerNoneSelected[$v]?>>Select One</OPTION></Font>
                                            <font face='verdana' SIZE=2><OPTION VALUE="Male" <?php print $playerMaleSelected[$v]?>>Male</OPTION>
                                            <OPTION VALUE="Female"<?php print $playerFemaleSelected[$v]?>>Female</OPTION></FONT>
                                        </SELECT>
                                    </td>
                                </tr>
                            <?php 
                            }//END FOR
                            ?>
                        </TABLE>
                    </td>
                </tr>     
                       
<?php //########################################################################################################################################
//                                                       THE FORM - COMMENT BOX AND ACTION BUTTONS
//############################################################################################################################################?>
                    
                <TR BGCOLOR='#CCCCCC'>
                    <TD COLSPAN=4 align='center'>
                        <B>4. Comments</B><BR>
                        <font face='verdana' SIZE=1>Comments, notes, player needs, etc. (limit 1000 characters)</font>
                    </TD>
                </TR>
                <TR>
                    <TD colspan=4 align='center'>
                        <TEXTAREA NAME='comments' COLS= 80 ROWS=6><?php print $comments ?></TEXTAREA>
                    </TD>
                </TR>
            <?php }else{
                //A BUTTON WAS PRESSED AND THERE WERE NO ERRORS, STORE ALL FORM INFORMATION IN HIDDEN VARIABLES
    
                ?>
                <INPUT TYPE='hidden' NAME='teamName' VALUE='<?php print htmlentities($teamName, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='capFirst' VALUE='<?php print htmlentities($capFirst, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='capLast' VALUE='<?php print htmlentities($capLast, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='capSex' VALUE='<?php print htmlentities($capSex, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='capEmail' VALUE='<?php print htmlentities($capEmail, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='capPhone' VALUE='<?php print htmlentities($capPhone, ENT_QUOTES);?>'>
                <INPUT TYPE='hidden' NAME='semi' VALUE='<?php print htmlentities($semi, ENT_QUOTES);?>'>
    
                <?php
                for($u=1; $u<=$people; $u++){
                ?>
                    <INPUT TYPE='hidden' NAME="playerFirst<?php print $u?>" VALUE='<?php print htmlentities($playerFirst[$u], ENT_QUOTES);?>'>
                    <INPUT TYPE='hidden' NAME="playerLast<?php print $u?>" VALUE='<?php print htmlentities($playerLast[$u], ENT_QUOTES);?>'>
                    <INPUT TYPE='hidden' NAME="playerSex<?php print $u?>" VALUE='<?php print htmlentities($playerSex[$u], ENT_QUOTES);?>'>
                    <INPUT TYPE='hidden' NAME="playerEmail<?php print $u?>" VALUE='<?php print htmlentities($playerEmail[$u], ENT_QUOTES);?>'>
                <?php
                }
                ?>
                <INPUT TYPE='hidden' NAME='comments' VALUE='<?php print htmlentities($comments, ENT_QUOTES);?>'>
                <?php
    
            }
            //if error is set to anything and no button was pressed
            if( $error!='' || (!isset($_POST['register']) and !isset($_POST['save']) and !isset($_POST['confirm']))) {
                print $footer;
            }?>
        </table>
    </form>
<?php } ?>