<?php

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
function printTopInfo() {
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
function printMatches() {
	global $oppDropDown, $games, $matches, $sportID, $maxPoints, $showCancelOption, $hasTies, $scoresAvailable;
	global $oppTeamID, $scoreThem, $scoreUs, $gameResults, $spiritScores, $matchComments, $isPlayoffs;
	$gameNum = 0;
	
	// THIS IS THE MATCH TABLE
	for ($i = 0; $i < $matches; $i++){ ?>
    <tr>
		<td colspan=20 align=center>
			<TABLE align=center class='game'>
				<tr>
					<th align="center" colspan=2>
						Match <?php print $i+1?><br>
					</th>
				</tr>
				<tr>
                    <td align='center'>
						<select name='oppID[]'>
							<option value=0>Select Opponent # <?php print $i+1?></option>
							<?php print $oppDropDown[$i]; ?>
						</select>
					</td>
				</tr>
				<?php if ($oppTeamID[$i] != 1) { // otherwise it's a practise game
					for($j = 0; $j < $games; $j++){ ?>
					<tr>
                       <td align=center>
							<TABLE class='score'>
                            	<tr>
									<td>
										<?php if ($gameResults[$gameNum] == 1) {?>
											We Won<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=1>
										<?php } else { ?>
											We Won<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=1>
										<?php } ?>
									</td><td>
										<?php if ($gameResults[$gameNum] == 2) {?>
											We Lost<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=2>
										<?php } else { ?>
											We Lost<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=2>
										<?php } ?>
									</td>
								<?php if($hasTies == 1){?>
									<td>
										<?php if ($gameResults[$gameNum] == 3) {?>
											We Tied<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=3>
										<?php } else { ?>
											We Tied<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=3>
										<?php } ?>
									</td>
								<?php }
								if($showCancelOption == 1){ ?>
                                	<td>
									<?php if ($gameResults[$gameNum] == 4) {?>
											Cancelled**<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=4>
										<?php } else { ?>
											Cancelled**<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=4>
										<?php } ?>
                                    </td>
								<?php }?>
                                </tr>
								<?php if($scoresAvailable == 1 || $isPlayoffs == 1){ ?>
								<tr>
									<td rowspan=2>
                                    	Scores
									</td><td>
                                    	Us
									</td><td>
										<SELECT NAME='scoreus[]'>
										<?php //Runs a for loop to load the max number of points based on the sport
										for ($z=0; $z <= $maxPoints; $z++){ 
											if($z == $scoreUs[$gameNum]) { ?>
												<OPTION selected='selected' VALUE=<?php print $z?>><?php print $z?></OPTION><?php
											} else { ?>
												<OPTION VALUE=<?php print $z?>><?php print $z?></OPTION><?php
											}
										} ?>
										</SELECT>
									</td>
								</tr><tr>
									<td>
                                    	Them
									</td><td>
										<SELECT NAME='scorethem[]'>
										<?php //for loop, same as above
										for ($x=0; $x <= $maxPoints; $x++){
											if ($x == $scoreThem[$gameNum]) { ?>
												<OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x?></OPTION>
											<?php } else {
												?><OPTION VALUE=<?php print $x ?>><?php print $x?></OPTION><?php
											}
										}?>
										</SELECT>
									</td>
								</tr>
								<?php }?>
							</TABLE>
						</td>
					</tr>
					<?php $gameNum++; 
					} ?>
					<tr>
					   <td align='center'>
						<?php for($k = 1; $k <= 5; $k+=.5) {
							if ($k == $spiritScores[$i]) { ?>
								<INPUT TYPE='radio' checked name='spiritScore[<?php print $i?>]' VALUE=<?php print $k?> ><?php print $k ?>
							<?php } else { ?>
								<INPUT TYPE='radio' name='spiritScore[<?php print $i ?>]' VALUE=<?php print $k ?> ><?php print $k ?>
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
                } ?>
				<tr>
					<td>Comments (optional):
					</td><td align='center'>
						<TEXTAREA NAME='matchComments[]' COLS=40 ROWS=6><?php print $matchComments[$i]?></TEXTAREA>
					</td>
                </tr>
			</TABLE>
       	</td>
	</tr>
	<?php }
}

//prints the matches sections of the score reporter
function printMatchesBeta() {
	global $oppDropDown, $games, $matches, $sportID, $maxPoints, $showCancelOption, $hasTies, $scoresAvailable;
	global $oppTeamID, $scoreThem, $scoreUs, $gameResults, $spiritScores, $matchComments, $isPlayoffs;
	$gameNum = 0;
	
	// THIS IS THE MATCH TABLE
	for ($i = 0; $i < $matches; $i++){ ?>
    <tr>
		<td colspan=20 align=center>
			<TABLE align=center class='game'>
				<tr>
					<th align="center" colspan=2>
						Match <?php print $i+1?><br>
					</th>
				</tr>
				<tr>
                   <td align='center'>
						<select name='oppID[]'>
							<option value=0>Select Opponent # <?php print $i+1?></option>
							<?php print $oppDropDown[$i]; ?>
						</select>
					</td>
				</tr>
				<?php if ($oppTeamID[$i] != 1) { // otherwise it's a practise game
					for($j = 0; $j < $games; $j++){ ?>
					<tr>
						<td align=center>
							<TABLE class='score'>
                            	<tr>
									<td>
										<?php if ($gameResults[$gameNum] == 1) {?>
											<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=1>We Won
										<?php } else { ?>
											<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=1>We Won
										<?php } ?>
									</td><td>
										<?php if ($gameResults[$gameNum] == 2) {?>
											<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=2>We Lost
										<?php } else { ?>
											<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=2>We Lost
										<?php } ?>
									</td>
								<?php if($hasTies == 1){?>
									<td>
										<?php if ($gameResults[$gameNum] == 3) {?>
											<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=3>We Tied
										<?php } else { ?>
											<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=3>We Tied
										<?php } ?>
									</td>
								<?php }
								if($showCancelOption == 1){ ?>
                                	<td>
									<?php if ($gameResults[$gameNum] == 4) {?>
											<INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=4>Cancelled**
										<?php } else { ?>
											<INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=4>Cancelled**
										<?php } ?>
                                    </td>
								<?php }?>
                                </tr>
								<?php if($scoresAvailable == 1 || $isPlayoffs == 1){ ?>
								<tr>
									<td>
                                    	Us:
									</td><td>
                                    	Score:
									</td><td>
										<SELECT NAME='scoreus[]'>
										<?php //Runs a for loop to load the max number of points based on the sport
										for ($z=0; $z <= $maxPoints; $z++){ 
											if($z == $scoreUs[$gameNum]) { ?>
												<OPTION selected='selected' VALUE=<?php print $z?>><?php print $z?></OPTION><?php
											} else { ?>
												<OPTION VALUE=<?php print $z?>><?php print $z?></OPTION><?php
											}
										} ?>
										</SELECT>
									</td>
								</tr><tr>
									<td>
                                    	Them:
									</td><td>
                                    	Score:
									</td><td>
										<SELECT NAME='scorethem[]'>
										<?php //for loop, same as above
										for ($x=0; $x <= $maxPoints; $x++){
											if ($x == $scoreThem[$gameNum]) { ?>
												<OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x?></OPTION>
											<?php } else {
												?><OPTION VALUE=<?php print $x ?>><?php print $x?></OPTION><?php
											}
										}?>
										</SELECT>
									</td>
								</tr>
								<?php }?>
							</TABLE>
						</td>
					</tr>
					<?php $gameNum++; 
					} ?>
					<tr>
						<td>
						<div id="star<?php print $i+1 ?>"></div>
						<div id="hint<?php print $i+1 ?>"></div>
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
                } ?>
				<tr>
					<td align='center'>
						<TEXTAREA NAME='matchComments[]' COLS=40 ROWS=6><?php print $matchComments[$i]?></TEXTAREA>
					</td>
                </tr>
			</TABLE>
       	</td>
	</tr>
	<?php }
}


//Prints the contact info on the bottom of the score reporter page
function contactInfo() {
	global $submitName, $submitEmail; ?>
	<tr>
        <td colspan = 20 align=center>
        	<TABLE align=center class='contactInfo'>
            	<tr>
            		<td colspan = 20 align = center>
                        <B>Contact Information</B>
                    </td>
                </tr>
                <tr>
                    <td align = center>
                        <B>*</B> Submitted by (Name):
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
                        Submitted by (Email):
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
                        <INPUT TYPE='Submit' Value='Submit' name='Submit'>
                    </td>
                </tr>
        	</TABLE>
    	</td>
    </tr>
<?php } ?>