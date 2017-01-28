<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

if(($id=$_GET['id']) != '') {
	$query= "SELECT * FROM teampicturearchive WHERE teampicturearchive.identifier = '$id'";
	
	$result=mysql_query($query) or die(mysql_error());
	while($array=mysql_fetch_array($result)){
	
	$path = '/'.$array['picLink']."/".$array['identifier'].".JPG";
	$pathCheck = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$array['picLink']."/".$array['identifier'].".JPG";
	$pathAlt = '/'.$array['picLink']."/".$array['identifier'].".jpg";
	$pathAltCheck = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$array['picLink']."/".$array['identifier'].".jpg";
	$subcat = $array['subcategory'];
	
	print "
	<TABLE align=center><TR><td style='width:50px'><br /></td><TD align=center><font face=arial size=4><b><u>".$subcat."</u></b></td></tr>";
	if(file_exists($pathCheck)){print '<tr><td style="vertical-align:top">
            	<!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_floating_style addthis_32x32_style" style="vertical-align:top">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                </div>
                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-5023df88037ce294"></script>
                <!-- AddThis Button END -->
            </td><td><img src="'.$path.'"></td></tr>';}
	  elseif(file_exists($pathAltCheck)){print '<tr><td style="vertical-align:top">
            	<!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_floating_style addthis_32x32_style" style="vertical-align:top">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                </div>
                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-5023df88037ce294"></script>
                <!-- AddThis Button END -->
            </td><td align=center><img src="'.$pathAlt.'"></td><tr>';}
	print "</TABLE>";
	} 
} else {
	if(($teamID = $_GET['teamID']) == '') {
		$teamID = 0;
	}
	$teamQuery = mysql_query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id WHERE team_id = $teamID") or die('ERROR getting team picture - '.mysql_error());
		
	$teamsArray = mysql_fetch_array($teamQuery);
	$teamName = $teamsArray['team_name'];
	$sportName = $teamsArray['sport_name'];

	$path = '/'.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".JPG";
	$pathCheck = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".JPG";
	$pathAlt = '/'.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".jpg";
	$pathAltCheck = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".jpg"; ?>
		
    <font face=arial size=4>
	<TABLE align=center style="width:90%; max-width:700px;height:auto;">
    	<TR>
        	<td style="width:50px">
            	<br />
            </td>
        	<TD align=center>
            	<b><u>
                	<?php print $teamName ?>
                </u></b>
            </td>
        </tr>
        <tr>
        	<td style="vertical-align:top;height:auto;width:10%;">
            	<!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_floating_style addthis_32x32_style" style="vertical-align:top;width:5%;">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                </div>
                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-5023df88037ce294"></script>
                <!-- AddThis Button END -->
            </td>
        	<td align=center style="width:90%;height:auto;">
				<?php if(file_exists($pathCheck)) {
					print "<img style='width:90%;height:auto;' src='$path'>";
				} elseif(file_exists($pathAltCheck)) {
					print "<img style='width:90%;height:auto;' src='$pathAlt'>";
				} ?>
            </td>
        </tr>
    </TABLE>
    </font>
<?php } ?>
