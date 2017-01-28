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
    <div class="Table" align="center" style="width:90%; max-width:425px; min-width:250px;">
		<div class="Row" align="center">
			<FONT SIZE=2 FACE=VERDANA><B>
				<img src=<?php print $logo;?> /><br /><br />
				<?php print $formTitle; ?><BR><BR>
            </B></FONT>
		</div>
		<div class="infoBox" align="center">
				<div class="Row" align="center" style="text-align:center;">
                	<div class="Cell">
					Game Information
                    </div>
				</div>
                <div class="noBorderCell">
				<div class="Row" align="center">
                	<div class="Cell">
						<B>*</B> Please Select a League:
                    </div>
                    <div class="Cell">
						<select name='leagueID'  style="width:90%;" onchange='reloadPage()'>
							<option value=0>Select League</option>
							<?php print $leagueDropDown; ?>
						</select>
                    </div>
                </div>
				<div class="Row">
					<div class="Cell">
						<B>*</B> Team Name:
					</div>
                    <div class="Cell">
						<select name='teamID'  style="width:90%;" onchange='secondReload()'>
							<option value=0>Select Your Team's Name</option>
							<?php print $teamDropDown;?>
						</select>
						<INPUT TYPE='hidden' NAME='actualWeekDate' VALUE='<?php print $actualWeekDate?>'>
                        <INPUT TYPE='hidden' NAME='dayOfYear' VALUE=<?php print $dayOfYear?>>
                        <INPUT TYPE='hidden' NAME='dateID' VALUE=<?php print $dateID?>>
					</div>
                </div>
				<div class="Row">
					<div class="Cell">
						<B>*</B> Date the Game was Played:
					</div>
                    <div class="Cell">
						<?php print $actualWeekDate?>
					</div>
				</div>
			</div>
            </div>
		</div>
        <br  />
<?php }

function printError($error) {?>
	 <div class="Table" align="center" style="width:90%; max-width:425px; min-width:250px;">
        <div class='infoBox' align=center>
            <div class="Row">
                    <font color="#FF0000" size=3>
                        <?php print $error?>
                    </font>
            	</div>
            </div>
        </div>
<?php }


//prints the matches sections of the score reporter
function printMatches() {
	global $oppDropDown, $games, $matches, $sportID, $maxPoints, $showCancelOption, $hasTies, $scoresAvailable;
	global $oppTeamID, $scoreThem, $scoreUs, $gameResults, $spiritScores, $matchComments, $isPlayoffs;
	$gameNum = 0;
	
	// THIS IS THE MATCH TABLE
	for ($i = 0; $i < $matches; $i++){ ?>
    <div class="Table" style="width:90%; max-width:525px; min-width:250px;" align="center">
		<div align=center class="game">
			<div class="Row" align="center" style="width:30%;">
            	<div class="Cell" style="width:30%;">
					Match <?php print $i+1?><br>
                </div>
			</div>
            <div class="noBorderCell" style="width:30%;">
			<div class="Row" style="width:30%;">
				<div class="Cell" style="width:30%;">
					<B>*</B> Opponent Name:
				</div>
                <div class="Cell" style="width:70%;">
					<select name='oppID[]' style="width:90%;">
						<option value=0>Select Opponent # <?php print $i+1?></option>
						<?php print $oppDropDown[$i]; ?>
					</select>
				</div>
			</div>
			<?php if ($oppTeamID[$i] != 1) { // otherwise it's a practise game
				for($j = 0; $j < $games; $j++){ ?>
					<div class="Row" align="center" style="width:30%;">
						<div class="Cell" style="width:30%;">
                        	Game <?php print $j + 1?> Results <B>*</B>:
						</div>
                        <div class="Cell" align="center" style="width:30%;">
							<?php if ($gameResults[$gameNum] == 1) {?>
                                <INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=1>We Won
                            <?php } else { ?>
                                <INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=1>We Won
                            <?php } 
                             if ($gameResults[$gameNum] == 2) {?>
                                <INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=2>We Lost
                            <?php } else { ?>
                                <INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=2>We Lost
                            <?php } 
                             if($hasTies == 1){
                                if ($gameResults[$gameNum] == 3) {?>
                                    <INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=3>We Tied
                                <?php } else { ?>
                                    <INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=3>We Tied
                                <?php } 
                             }
                            if($showCancelOption == 1){ ?>
                                <?php if ($gameResults[$gameNum] == 4) {?>
                                    <INPUT TYPE='radio' checked name='results[<?php print $gameNum?>]' VALUE=4>Cancelled**
                                <?php } else { ?>
                                    <INPUT TYPE='radio' name='results[<?php print $gameNum?>]' VALUE=4>Cancelled**
                                <?php }
                            }
                              
							if($scoresAvailable == 1 || $isPlayoffs == 1){ ?>
                                <div class="Row" align="center" style="width:30%;">
                                	<div class="noBorderCell" style="width:30%;">
                                        <div class="noBorderCell" align="right" style="width:30%;">
                                            Scores
                                        </div>
                                        <div class="noBorderCell" style="width:30%;">
                                            <div class="Row" align="center" style="width:30%;">
                                                <div class="Column" style="width:30%;">
                                                Us
                                                </div>
                                                <div class="Column" style="width:30%;">
                                                <SELECT NAME='scoreus[]' style="width:100%;">
                                                <?php //Runs a for loop to load the max number of points based on the sport
                                                for ($z=0; $z <= $maxPoints; $z++){ 
                                                    if($z == $scoreUs[$gameNum]) { ?>
                                                        <OPTION selected='selected' VALUE=<?php print $z?>><?php print $z?></OPTION><?php
                                                    } else { ?>
                                                        <OPTION VALUE=<?php print $z?>><?php print $z?></OPTION><?php
                                                    }
                                                } ?>
                                                </SELECT>
                                                </div>
                                            </div>
                                            <div class="Row" align="center" style="width:30%;">
                                                <div class="Column" style="width:30%;">
                                                    Them
                                                </div>
                                                <div class="Column" style="width:30%;">
                                                    <SELECT NAME='scorethem[]' style="width:100%;">
                                                    <?php //for loop, same as above
                                                    for ($x=0; $x <= $maxPoints; $x++){
                                                        if ($x == $scoreThem[$gameNum]) { ?>
                                                            <OPTION selected='selected' VALUE=<?php print $x ?>><?php print $x?></OPTION>
                                                        <?php } else {
                                                            ?><OPTION VALUE=<?php print $x ?>><?php print $x?></OPTION><?php
                                                        }
                                                    }?>
                                                    </SELECT>
                                                </div>
                                            </div>
                                        </div>
                                	</div>
                                </div>
                            <?php }?>
                    	</div>
					</div>
					<?php $gameNum++; 
					} ?>
					<div class="Row" style="width:30%;">
						<div class="Cell" style="width:30%;">Spirit Score:
						</div><div class="Cell" style="width:30%;">
						<?php for($k = 1; $k <= 5; $k+=.5) {
							if ($k == $spiritScores[$i]) { ?>
								<INPUT TYPE='radio' checked name='spiritScore[<?php print $i?>]' VALUE=<?php print $k?> ><?php print $k ?>
							<?php } else { ?>
								<INPUT TYPE='radio' name='spiritScore[<?php print $i ?>]' VALUE=<?php print $k ?> ><?php print $k ?>
							<?php }
						} ?>
						</div>
					</div>
                <?php } else { 
					//I put these here so that if a team practices first it won't offset all the other variables from not showing up once.
					for($j = 0; $j < $games; $j++){ ?>
                	<input type="hidden" checked="checked" name="results[<?php print $gameNum?>]" value=5 />
                    <input type="hidden" name="scoreus[]" value=0 />
                    <input type="hidden" name='scorethem[]' value=0 />
                    <?php $gameNum++;
                    }
                } ?>
				<div class="Row" style="width:30%;">
					<div class="Cell" style="width:30%;">Comments (optional):
					</div><div class="Cell">
						<TEXTAREA NAME='matchComments[]'  style="width:90%;" ROWS=6><?php print $matchComments[$i]?></TEXTAREA>
					</div>
                </div>
			</div>
            </div>
       	</div>
        <p></p>
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
					<td>
						<B>*</B> Opponent Name:
					</td><td align='center'>
						<select name='oppID[]'>
							<option value=0>Select Opponent # <?php print $i+1?></option>
							<?php print $oppDropDown[$i]; ?>
						</select>
					</td>
				</tr>
				<?php if ($oppTeamID[$i] != 1) { // otherwise it's a practise game
					for($j = 0; $j < $games; $j++){ ?>
					<tr>
						<td>
                        	Game <?php print $j + 1?> Results <B>*</B>:
						</td><td align=center>
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
						<td>Spirit Score:
						</td><td>
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


//Prints the contact info on the bottom of the score reporter page
function contactInfo() {
	global $submitName, $submitEmail; ?>
	<div class="Table" style="width:90%; max-width:525px; min-width:250px;" align="center">
        <div class="Row" align="center" style="width:30%;">
            <B><font size=4>Contact Information</B></font>
        </div>
        <div class="Row" align="center" style="width:30%;">
        	<div class="noBorderCell" align="center" style="width:30%;">
            	<div class="Row" align="center" style="width:30%;">
                	<div class="Column" style="width:30%;">
                		<B>*</B> Submitted by (Name):
                	</div>
                	<div class="Column" style="width:30%;">
						<?php if(isValid($submitName)) { ?> 
                            <INPUT TYPE='text' NAME='submitterName' style="width:100%;" value='<?php print $submitName?>' SIZE=45>
                        <?php } else { ?>
                            <INPUT TYPE='text' NAME='submitterName' style="width:100%;" value='' SIZE=45>
                        <?php } ?>
                    </div>
        		</div>
        		<div class="Row" align="center" style="width:30%;">
                	<div class="Column" style="width:30%;">
                		Submitted by (Email):
                    </div>
                    <div class="Column" style="width:30%;">
						<?php if(isValid($submitEmail)) { ?> 
                            <INPUT TYPE='text' NAME='submitemail' style="width:100%;" value='<?php print $submitEmail?>' SIZE=45>
                        <?php } else { ?>
                            <INPUT TYPE='text' NAME='submitemail' style="width:100%;" value='' SIZE=45>
                        <?php } ?>   
                    </div>
                </div>
            </div>  
        </div>            
        <div class="Row" align="center" style="width:30%;">
            <INPUT TYPE='Submit' Value='Submit' name='Submit'>
        </div>
    </div>
<?php } ?>