<?php 

function printFormHeader($logo, $sportHeader) { ?>
	<tr>
        <td>
            <table class="logo" align="center">
                <tr>
                    <td><img src=<?php print $logo?>></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <font face='verdana' size=4><B><?php print $sportHeader?></B></font>
        </td>
    </tr><tr style="height:15px;">
		<td></td>
	</tr>
<?php }

function printLeagues($leagueNames) { ?>
    <tr>
        <th id='greyBar'>
            1. Select Your Preferred Leagues<BR />
			<span style="font-family:Verdana, Geneva, sans-serif; font-size:9px;">In order of importance</span>
        </th>
    </tr>
    <tr>
        <td align="center">
            <table class="leagues">
                    <?php foreach($leagueNames as $name) { ?>
                    <tr>
                        <td align=right>
                            <?php print $name;?>
                        </td><td align="left">
                            <input type="checkbox" name="check[]" style="border-collapse:collapse; height:10px; width:10px;" />
                        </td><td style="border-bottom:thin; border-bottom-color:#000; border-bottom-style:solid; width:150px">
							<br />
                        </td>    
                    <?php } ?>
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printPlayerForm() { ?>
    <tr>
		<th id='greyBar'>
			2. Player(s) Information<br />
			<span style="font-family:Verdana, Geneva, sans-serif; font-size:9px;">1st additional player will be the alternate contact for the group</span>
		</th>
	</tr><tr>
        <td>
            <table class="players">
                <tr>
                    <th>
                    </th><th>
                        First Name
                    </th><th>
                        Last Name
                    </th><th>
                        Email
                    </th><th>
                        Phone
                    </th><th>
                        Gender
                    </th>
                </tr>
                <?php for($v=0, $b=1; $v < 7; $v++, $b++){?>
                    <tr>
                        <td>
                            <?php print $v==0?"<font color='#FF0000'>*</font>You.":"$b ." ?>
                        </td><td style="width:130px">
                        </td><td style="width:130px">
                        </td><td style="width:250px">
                        </td><td style="width:150px">
                        </td><td style="width:40px">
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>     
<?php }

function printFormFooter() { ?>
    <tr style="height:30px;">
		<td></td>
	</tr><tr>
    	<td>
        	<img style="height:75%;" src="/Logos/Perpetualmotionlogo.jpg" /><br />
            78 Kathleen St. Guelph On, N1H 4Y3<br />
            (519)823-4502
        </td>
    </tr>
<?php }

