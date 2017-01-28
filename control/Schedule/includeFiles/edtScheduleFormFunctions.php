<?php function printGetIDs($seasonID, $sportID, $leagueID) { 
	global $sportsDropDown, $seasonsDropDown, $leaguesDropDown, $weeksDropDown ?>
    <tr>
        <td>
            <table class="getIDs">
                <tr>
                    <td>
                        Sport
                    </td><td>
                        <select id='idDropDown' name='sportID' onchange='reloadCreatePage()'>
                            <?php print $sportsDropDown ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Season
                    </td><td>
                        <select id='idDropDown' name='seasonID' onchange='reloadCreatePage()'>
                            <?php print $seasonsDropDown ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        League
                    </td><td>
                        <select id='idDropDown' name='leagueID' onchange='reloadCreatePage()'>
                            <?php print $leaguesDropDown ?>
                        </select>
                    </td>
                </tr><tr>
                    <td style='width:40%;'>
                        Playoff Start Week
					</td><td>
                        <select id='idDropDown' name='playoffStartWeek'>
                            <?php print $weeksDropDown ?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printBottomButton() { ?>
	<tr>
		<td>
			<table class='bottomButton'>
				<tr>
					<td>
						<input type='submit' name="submitSchedule" value="Submit Changes" />
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php }


