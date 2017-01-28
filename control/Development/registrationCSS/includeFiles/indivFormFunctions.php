<?php

function printFormHeader($logo, $sportHeader) { ?>
	<tr>
        <td>
       		<table class="title">
            	<tr>
                    <td>
                        <hr style="height:5px;background-color:#000;color:#000;" />
                        <img src=<?php print $logo?>><br />
                        Team List
                        <hr style="height:5px;background-color:#000;color:#000;" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printLeagueAndTeam($curTeamNum, $leagueName) { 
	global $team; ?>
	<tr>
		<td colspan=3 align='Center'>
        	Team Name: <?php print $team[$curTeamNum]->teamName; ?>
        </td>
    </tr>
    <tr>
    	<td colspan=3 align='center'>
        	Division: <?php print $leagueName ?>
        </td>
    </tr>
<?php }

function printPlayerForm($playerObj, $people, $curTeamNum) { ?>
    <tr>
    	<td>
        	<table class="players">
            	<tr>
                    <th style="width:30%; text-align:center;">
                        Players Name
                    </th><th style="width:50%; text-align:center;">
                        Email
                    </th><th style="width:20%; text-align:center;">
                        Phone Number
                    </th>
                </tr>
                <?php for($v=1; $v<=$people; $v++){?>
                    <tr>
                        <td>
                            <?php print $v.') '.$playerObj[$curTeamNum][$v-1]->playerName;?>
                        </td><td>
                        </td><td>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
<?php }