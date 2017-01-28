<?php

// Comments and edits by: Bradley Connolly

function printJavaScript() { ?>
	<table>
		<tr>
			<td>
				<noscript>
					<font face='verdana' size=2 color=red><b>
						For full functionality of this site it is necessary to enable JavaScript.
					</b></font><br />
					Here are the <a href='http://www.enable-javascript.com/' target='_blank'>
					instructions how to enable JavaScript in your web browser</a>.
				</noscript>
			</td>
		</tr>
	</table>
<?php }


//prints the top info of the score reporter, ie the game info and title
function printTopInfo($sportID) {
  global  $formTitle, $leagueDropDown, $teamDropDown, $teamID, $actualWeekDate, $dayOfYear, $dateID,  $logo;
  ?>
  <tr>
	  <td colspan=20 align=center>
		  <FONT SIZE=2 FACE=VERDANA><B>
			  <img src=<?php print $logo;?> /><br /><br />
		  </B></FONT>
	  </td>
  </tr>
	<tr>
	  <td colspan=20 align=center>
		  <TABLE class='infoBox' align=center>
			  <tr>
				  <th colspan=2>
					  Game Information
				  </th>
			  </tr>
			  <tr>
				  <td>
					  <select name='leagueID' style="width:250px;" onchange='reloadPage()'>
						  <option value=0>Select League</option>
						  <?php print $leagueDropDown; ?>
					  </select>
				  </td>
			  </tr>
			  <tr>
				  <td>
					  <select name='teamID' style="width:250px;" onchange='secondReload()'>
						  <option value=0>Select Your Team's Name</option>
						  <?php print $teamDropDown;?>
					  </select>
					  <INPUT TYPE='hidden' NAME='actualWeekDate' VALUE='<?php print $actualWeekDate?>'>
					  <INPUT TYPE='hidden' NAME='dayOfYear' VALUE=<?php print $dayOfYear?>>
					  <INPUT TYPE='hidden' NAME='dateID' VALUE=<?php print $dateID?>>
				  </td>
			  </tr>
			  <tr>
				  <th align='center'>
					  <?php print $actualWeekDate?>
				  </th>
			  </tr>   
		  </TABLE>
	  </td>
  </tr>
<?php }

function printError($error) {?>
	 <tr>
		<td colspan=20 align=center>
			<table class='infoBox' align=center>
				<tr>
					<td>
						<font color="#FF0000" size=3>
							<?php print $error?>
						</font>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php }


//prints the matches sections of the score reporter
function printMatches() 
{
	global $oppDropDown, $games, $matches, $sportID, $maxPoints, $showCancelOption, $hasTies, $scoresAvailable;
	global $oppTeamID, $gameResults, $spiritScores, $matchComments, $isPlayoffs;
	global $usScore, $themScore;
	
	$gameNum = 0;	

	// THIS IS THE MATCH TABLE
	//for ($i = 0; $i < $matches; $i++){ ?>
	<tr>
		<td colspan=20 align=center>
			<TABLE align=center class='game'>
				<tr>
					<th align="center" colspan=2>
						Game <?php print $i+1?> Tracker<br>
					</th>
				</tr>
                <tr>
                    <td align='center'>
                        <select name='oppID[]'>
                            <option value=0>Select Opponent # <?php print $i+1?></option>
                            <?php print $oppDropDown[0]; ?>
                        </select>
                    </td>
                </tr>
                
                
                <?php if ($oppTeamID[0] != 1) {
                    for($j = 0; $j < $games; $j++){ ?>
                    <tr>
                        <td align=center>
                        <TABLE class='score'>
                                <tr>
                                    <td rowspan=2>
                                        Scores <br>
                                    </td>
                                </tr>
                         </TABLE>
                         <TABLE align=center>
                        	 <tr>
                             	Us
                             </tr>
                             	<td align=center>
                                	<INPUT TYPE='Submit' Value='-' name='subUs' id='subUs' onclick="subOneUs()" style="width:75px; height:75px;" />
                                    <font size="6">
                                    <INPUT TYPE='text' VALUE=<?php print $usScore;?> NAME='usScore' size="2" style="height:75px;"/>
                                    </font>
                                    <INPUT TYPE='Submit' Value='+' name='addUs' id='addUs' onclick="addOneUs()" style="width:75px; height:75px;"/>			                                 
								</td>
                             <tr>
								Them
                             </tr>
								<td align=center>
                                    <INPUT TYPE='Submit' Value='-' name='subThem' id='subThem' onclick="subOneThem()" style="width:75px; height:75px;" />
                                    <input type="text" name="themScore" value="<?php print $themScore?>" size="2" style="height:75px;"/>
                                    <INPUT TYPE='Submit' Value='+' name='addThem' id='addThem' onclick="addOneThem()" style="width:75px; height:75px;"/>	
								</td>
							</TABLE>
						</td>
					</tr>
					<?php //$gameNum++; 
					}/* ?>
                    
                    
					<tr>
					   <td align='center'>
					   Spirit Score<br>
						<?php 
						
						// ****************************************************************************
						// So the ID values are k*(i+20) because of the loop duplication
						// This way each match has a seperate radio id button for each spirit score
						// *****************************************************************************
						
						for($k = 1; $k <= 5; $k+=.5) 
						{
							if ($k == $spiritScores[$i])
							{ ?>
								<INPUT TYPE='radio' checked name='spiritScore[<?php print $i?>]' VALUE=<?php print $k?> id=<?php print $k*($i+20)?> >
								<label for="<?php print $k*($i+20)?>" > <?php print $k?></label>
                                
							<?php } 
							
							else 
							{ ?>
								<INPUT TYPE='radio' name='spiritScore[<?php print $i ?>]' VALUE=<?php print $k ?> id=<?php print $k*($i+20)?> >
								<label for="<?php print $k*($i+20)?>" > <?php print $k?></label>
							<?php }
						} ?>
						</td>
					</tr>
				<?php } else { 
					//I put thesee here so that if a team practices first it won't offset all the other variables from not showing up once.
					for($j = 0; $j < $games; $j++){ ?>
					<input type="hidden" checked="checked" name="results[<?php print $gameNum?>]" value=5 />
					<input type="hidden" name="scoreus[]" value=0 />
					<input type="hidden" name='scorethem[]' value=0 />
					<?php $gameNum++;
					}
				} */
	}?>
				<tr>
					<td align='center'>
					Comments<br>
						<TEXTAREA NAME='matchComments[]' COLS=35 ROWS=6><?php print $matchComments[$i]?></TEXTAREA>
					</td>
				</tr>
			</TABLE>
		</td>
	</tr>
	<?php }
//}

//Prints the contact info on the bottom of the score reporter page
function contactInfo() {
	global $submitName, $submitEmail; ?>
	<tr>
		<td colspan = 20 align=center>
			<TABLE align=center class='contactInfo'>
				<tr>
					<td colspan = 20 align = center>
						<B>Send Email Reminder</B>
					</td>
				</tr>
				<tr>
					<td align = center>
						<B>*</B> Name:
					</td><td>
					<?php if(isValid($submitName)) { ?> 
						<INPUT TYPE='text' NAME='submitterName' value='<?php print $submitName?>' SIZE=45>
					<?php } else { ?>
						<INPUT TYPE='text' NAME='submitterName' value='' SIZE=45>
					<?php } ?>
					</td>
				</tr>
				<tr>
					<td align = center>
						<B>*</B> Email:
					</td><td>
					<?php if(isValid($submitEmail)) { ?> 
						<INPUT TYPE='text' NAME='submitemail' value='<?php print $submitEmail?>' SIZE=45>
					<?php } else { ?>
						<INPUT TYPE='text' NAME='submitemail' value='' SIZE=45>
					<?php } ?>                   
					</td>
				</tr>
				<tr>
					<td colspan=20 ALIGN='center'>
						<INPUT TYPE='Submit' Value='Send Email' name='Submit'>
					</td>
				</tr>
			</TABLE>
		</td>
	</tr>
<?php } ?>