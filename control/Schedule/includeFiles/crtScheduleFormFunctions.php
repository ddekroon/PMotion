<?php

function printGetIDs($seasonID, $sportID, $leagueID) { 
	global $sportsDropDown, $seasonsDropDown, $leaguesDropDown ?>
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
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printNumWeeks() { 
	global $numWeeks ?>
	<tr>
        <td>
            <table class='getIDs'>
                <tr>
                    <td>
                        Weeks
                    </td><td>
                    	<select id="numWeeks" name='weeksNum' onChange="return changeNumWeeks(this)">
                        	<?php for($i = 1; $i < 10; $i++) {
								print '<option ';
								print $i == $numWeeks?'selected':'';
								print " value='$i'>$i</option>";
							} ?>
                        </select>
                    </td>
                </tr><tr>
                    <td>
                        Start Date
                    </td><td>
                    	<?php print date_picker('start', date('Y'), date('Y')); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php }

function printTimesAndVenues($numTimesMaster, $sameTimes, $timesDD, $numVenuesMaster, $sameVenues, $fieldVenuesDD) {
	global $maxTimes, $maxVenues?>
	<tr>
        <td>
            <table class='subTable' id="timesAndVenues">
                <tr>
                    <td>
                        Times:
                    </td><td>
                        <select id="numTimesMaster" name='numTimes' onChange="setNumTimes(this)">
                        <?php for($i = 1; $i <= $maxTimes; $i++) {
							print '<option ';
								print $i == $numTimesMaster?'selected':'';
								print " value='$i'>$i</option>";
						} ?>
                        </select>
                    </td><td>
                        Same times all season?
                    </td><td>
                        <select id="timesSame" name='sameTimes' onChange="return changeSameTimes(this)">
                            <option <?php print $sameTimes==1?'selected':'' ?> value="1">Yes</option>
                            <option <?php print $sameTimes==0?'selected':'' ?> value="0">No</option>
                        </select>
                    </td>
                </tr><tr>
                    <td>
                        Venues:
                    </td><td>
                        <select id="numVenuesMaster" name='numVenues' onChange="setNumVenues(this)">
                        <?php for($i = 1; $i <= $maxVenues; $i++) {
							print '<option ';
								print $i == $numVenuesMaster?'selected':'';
								print " value='$i'>$i</option>";
						} ?>
                        </select>
                    </td><td>
                        Same venues all season?
                    </td><td>
                        <select id="venuesSame" name='sameVenues' onChange="return changeSameVenues(this)">
                            <option <?php print $sameVenues==1?'selected':'' ?> value="1">Yes</option>
                            <option <?php print $sameVenues==0?'selected':'' ?> value="0">No</option>
                        </select>
                    </td>
                </tr>	
            </table>
        </td>
    </tr>
<?php }

function printSchedule() {
	global $numWeeks, $maxWeeks, $maxVenues, $sameVenues, $numVenuesMaster, $fieldVenuesDD;
	global $timesDD, $sameTimes, $maxTimes, $numTimesMaster; 
	
	for($i = 0; $i < $maxWeeks; $i++) { ?>
    <tr name='weekTables[]' <?php print ($i<$numWeeks && $sameTimes==0) || ($i<1 && $sameTimes==1)
        || ($i<$numWeeks && $sameVenues==0) || ($i<1 && $sameVenues==1)?'':'style="display:none;"';?>>
        <td>
            <table class='subTable' id='weekRow'>
                <tr name='weekHeads[]' id='weekHead' <?php print ($i<$numWeeks && $sameTimes==0) || ($i<1 && $sameTimes==1)
                    || ($i<$numWeeks && $sameVenues==0) || ($i<1 && $sameVenues==1)?'':'style="display:none;"';?>>
                    <td>
                        <?php print 'Week ';
                            print $i+1;?>
                    </td>
                </tr>
                <tr name='weekTimes[]' <?php print ($i<$numWeeks && $sameTimes==0) || ($i<1 && $sameTimes==1)?'':'style="display:none;"';?>>
                    <?php for($j = 0; $j < $maxTimes; $j++) { ?>
                        <td name='timeColumn[<?php print $i?>][]' <?php print $j < $numTimesMaster?'':'style="display:none;"';?>>
                            <select name="timesDD[<?php print $i?>][]" <?php print $j == 0?"onChange='setTimesMaster(this, $j)'":''?> >
                                <?php print $timesDD[$i][$j];?>
                            </select>
                        </td>
                    <?php } ?>
                </tr>
                <tr name='weekVenues[]' <?php print ($i<$numWeeks && $sameVenues==0) || ($i<1 && $sameVenues==1)?'':'style="display:none;"';?>>
                    <td colspan=5>
                        <table class="leagueWeek">
                            <?php for($j = 0, $k = 1; $j < $maxVenues; $j++, $k++) { ?>
                                <tr name='venueRows[<?php print $i?>][]' <?php print $j < $numVenuesMaster?'':'style="display:none;"';?>>
                                    <td>
                                        <?php print 'Field '.$k ?>
                                    </td><td>
                                        <select <?php print $i == 0?"onChange='setVenuesMaster(this, $j)'":''?> 
                                            name="venuesDD[<?php print $i?>][]">
                                            <?php print $fieldVenuesDD[$i][$j]; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
			</table>
        </td>
    </tr>
    <?php } ?> 
<?php }

function date_picker($name, $startyear=NULL, $endyear=NULL)
{
    if($startyear==NULL) $startyear = date("Y")-100;
    if($endyear==NULL) $endyear=date("Y")+50;
	$curMonth = date('m');
	$curDay = date('j');

    $months=array('','January','February','March','April','May',
    'June','July','August', 'September','October','November','December');

    // Month dropdown
    $html="<select name=\"".$name."Month\">";

    for($i=1;$i<=12;$i++)
    {
       $html .= "<option ";
	   $html .= $i==$curMonth?'selected':'';
	   $html .= " value='$i'>$months[$i]</option>";
    }
    $html.="</select> ";
   
    // Day dropdown
    $html.="<select name=\"".$name."Day\">";
    for($i=1;$i<=31;$i++)
    {
       $html .= "<option ";
	   $html .= $i==$curDay?'selected':'';
	   $html .= " value='$i'>$i</option>";
    }
    $html.="</select> ";

    // Year dropdown
    $html.="<select name=\"".$name."Year\">";

    for($i=$startyear;$i<=$endyear;$i++)
    {      
      $html.="<option value='$i'>$i</option>";
    }
    $html.="</select> ";

    return $html;
}

