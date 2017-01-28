<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

function dayString($dayNum) {
	if($dayNum ==1) {
		return 'Monday';
	} else if($dayNum ==2) {
		return 'Tuesday';
	} else if($dayNum ==3) {
		return 'Wednesday';
	} else if($dayNum ==4) {
		return 'Thursday';
	} else if($dayNum ==5) {
		return 'Friday';
	} else if($dayNum ==6) {
		return 'Saturday';
	} else if($dayNum ==7) {
		return 'Sunday';
	}
}

if(($sport = $_GET['sport']) != '') {
	$year=$_GET['year'];
	$seas=$_GET['season'];
	
	if($sport=='beach'){$operator="<=99";}
	elseif($sport=='ultimate'){$operator=">=100 AND teampicturearchive.cat_id <=199";}
	elseif($sport=='football'){$operator=">=200 AND teampicturearchive.cat_id <=299";}
	if($sport=='soccer'){$operator=">=300 AND teampicturearchive.cat_id <=399";}
		
	$catIDQuery="SELECT distinct cat_id FROM teampicturearchive WHERE cat_id ".$operator." ORDER BY identifier";
	$catIDResult=mysql_query($catIDQuery);
	$totalcount=1;
	
	while($catIDArray=mysql_fetch_array($catIDResult)){
		$leagueCatID = $catIDArray['cat_id']; 
		$teamsQuery = mysql_query("SELECT * FROM teampicturearchive WHERE teampicturearchive.cat_id = $leagueCatID AND teampicturearchive.inorder < 98 
			ORDER BY teampicturearchive.identifier, teampicturearchive.inorder") or die(mysql_error());
			
		while($array=mysql_fetch_array($teamsQuery)){
			$identifier = explode('-', $array['identifier']);
	
			if(($identifier[1]=="$seas") AND ($identifier[2]=="$year")){
	
				if($identifier[1]==1){$season="Spring";}
				elseif($identifier[1]==2){$season="Summer";}
				elseif($identifier[1]==3){$season="Fall";}
				elseif($identifier[1]==4){$season="Winter";}
	
				$header=$season." ".$identifier[2];
				?>
			<TABLE>
				<?php if($totalcount==1){?>
					<TR><th>
						<U><font face=verdana size=2><?php print $header?></font></U>
					</th></TR>
				<?php }?>
	
				<?php if($array['inorder']==0){?>
					<TR><th>
						<font face=verdana size=2><?php print $array['subcategory']?></font>
					<?php $catID=$array['cat_id'];?>
					</th></TR>
				<?php }else{
					$num = $array['inorder'];
					$subcat=$array['subcategory'];
					$path=realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$array['picLink']."/".$array['identifier'].".JPG";
					$pathAlt=realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$array['picLink']."/".$array['identifier'].".jpg";
					$link="archivePicturePage.php?id=".$array['identifier'];
					if(file_exists($path)){$name='<a href="'.$link.'" target="PicFrame">'.$subcat.'</a>';}
					elseif(file_exists($pathAlt)){$name='<a href="'.$link.'" target="PicFrame">'.$subcat.'</a>';}
					else{$name=$subcat;}
					?>
					<TR><th style="font-size:12px; font-family:Verdana, Geneva, sans-serif;">
						<?php print "Team $num - ".$name; ?>
					</TD></TR>
				<?php }?>
			<?php
			}//end if
			$totalcount+=1;
		} ?>
		</table>
	<?php }
} else {
	$sportID = $_GET['sportID'];
	$seasonID = $_GET['seasonID'];
		
	$teamsQuery=mysql_query("SELECT * FROM $teamsTable INNER JOIN $leaguesTable ON $leaguesTable.league_id = $teamsTable.team_league_id 
		INNER JOIN $seasonsTable ON $seasonsTable.season_id = $leaguesTable.league_season_id
		INNER JOIN $sportsTable ON $sportsTable.sport_id = $leaguesTable.league_sport_id 
		WHERE season_id = $seasonID AND sport_id = $sportID AND team_num_in_league > 0 ORDER BY league_day_number ASC, league_name ASC, team_num_in_league ASC")
		or die('ERROR gettings teams - '.mysql_error());

	$curLeague = 0; ?>
	
    <font face=verdana>
	<table>
	<?php while($teamsArray = mysql_fetch_array($teamsQuery)){
		if($teamsArray['league_id'] != $curLeague) {
			$curLeague = $teamsArray['league_id']; ?>
			<TR>
            	<TD style="font-size:14px">
					<B><?php print $teamsArray['league_name'].' - '.dayString($teamsArray['league_day_number']) ?></B>
				</TD>
            </TR>
        <?php } 
		$teamName = $teamsArray['team_name'];
		$teamNum = $teamsArray['team_num_in_league'];
		$path = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".JPG";
		$pathAlt = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.$teamsArray['league_pic_link']."/".$teamsArray['team_pic_name'].".jpg";
		$link = "archivePicturePage.php?teamID=".$teamsArray['team_id'];
		
		if(file_exists($path)) {
			$name = '<a href="'.$link.'" target="PicFrame">'.$teamName.'</a>';
		} elseif(file_exists($pathAlt)) {
			$name = '<a href="'.$link.'" target="PicFrame">'.$teamName.'</a>';
		} else {
			$name = $teamName;
		} 
        if($teamsArray['team_dropped_out'] != 1) { ?>
		<TR>
        	<TD style="font-size:14px">
				<?php print "Team $teamNum - ".$name; ?>
			</TD>
        </TR>
        <?php }
	} ?>
	</table>
    </font>
<?php }