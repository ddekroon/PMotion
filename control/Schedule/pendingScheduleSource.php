<?php 
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
	require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'globalDeclarations.php');
	require_once('class_lib.php');
	require_once('class_week.php');
	require_once('includeFiles/edtScheduleDeclarations.php');
	require_once('includeFiles/edtScheduleSQLFunctions.php');
	require_once('includeFiles/edtScheduleFileFunctions.php');
	require_once('includeFiles/crtScheduleFormFunctions.php');
	require_once('includeFiles/dateClass.php');
	require_once('includeFiles/class_schedule_variables.php'); 
	
	$_GET['sportID'] != ''?$sportID = $_GET['sportID']:$sportID = 0;
	$_GET['leagueID'] != ''?$leagueID = $_GET['leagueID']:$leagueID = 0;
	$sportsDropDown = getSportDD($sportID);
	$leaguesDropDown = getLeaguesDD($sportID, -1, $leagueID); ?>

    <script type='text/javascript' src='includeFiles/edtScheduleJSFunctions.js'/></script>
	<link rel='stylesheet' type='text/css' href='includeFiles/scheduleStyle.css'/>
    <form name="scheduleForm">
	<table class="master">
    	<tr>
        	<td>
            	<table class="titleBox">
                	<tr>
                    	<th>
                        	Dynamic Schedules
                        </th>
                    </tr>
                </table>
            </td>
        </tr><tr>
        	<td>
            	<table class="getIDs">
                	<tr>
                    	<td>
                        	Sport
                        </td><td>
                        	<select id="userInput" name="sportID" onchange="reloadSport(this)">
                            	<?php print $sportsDropDown ?>
                            </select>
                        </td>
                    </tr><tr>
                    	<td>
                        	League
                        </td><td>
                        	<select id="userInput" name="leagueID" onchange="reloadLeague(this)">
                            	<?php print $leaguesDropDown ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		<?php if($leagueID > 0) { ?>
		<tr>
        	<td style="height:20px; border:0px 1px;"></td>
        </tr><tr>
        	<td>
				<?php $league_schedule = new Schedule($leagueID, 'show'); ?>
			</td>
        </tr>
		<?php }?>
    </table>
    </form>
