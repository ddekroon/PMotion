<?php 

function printPlayerHeader() { ?>
	<tr>
    	<td>
        	Name
        </td><td>
        	League
        </td><td>
        	Comment
        </td>
    </tr>
<?php }

function printPlayerNode($playerObj) { ?>
	<tr>
    	<td>
        	<?php print "<a target='_blank' href='mailto:$playerObj->playerEmail'>".$playerObj->playerFirstName.' '.$playerObj->playerLastName.'</a>'; ?>
        </td><td>
        	<?php print $playerObj->playerLeagueName; ?>
        </td><td>
        	<?php print $playerObj->playerHearText; ?>
        </td>
    </tr>
<?php }