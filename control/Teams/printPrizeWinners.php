<?php 
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.DIRECTORY_SEPARATOR.'globalDeclarations.php');
require_once('includeFiles/przFormFunctions.php');
require_once('includeFiles/przSQLFunctions.php');
require_once('includeFiles/przVariableDeclarations.php');
require_once('includeFiles/teamClass.php');
require_once('includeFiles/playerClass.php'); 

$curWinners = getCurrentWinners($sortBy, $prizeTime);
$prizeTimesDD = getPrizeTimesDD($prizeTime);?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="includeFiles/showAllTeamsStyle.css"/>
        <script type="text/javascript">
        	function reloadPagePrize() {
				var form = document.getElementById('prizeWinners');
				var prizeTime=form.elements['prizeTime'].options[form.elements['prizeTime'].options.selectedIndex].value;
				
				self.location='printPrizeWinners.php?prizeTime=' + prizeTime;
			}
		</script>
    </head>
    <body>
    <form id='prizeWinners' METHOD='POST' action='<?php print "prizeWinners.php?sportID=$sportID&leagueID=$leagueID"?>'>
		<table class='master'>
        	<tr>
				<?php printPrintableWinnersHeader($prizeTimesDD, $prizeTime);?>
            </tr><tr>
                <td>
                	<table class="teamInfo">
                    	<?php printPrintableWinnersTop($prizeTime);
						$count = 1;
						if(count($curWinners) > 0) {
							foreach($curWinners as $team) {
								if($team->teamID != 0) {
									printPrintableWinnerNode($count, $team);
									$count++;
								}
							} 
						}?>
                    </table>
                </td>
            </tr>
        </table>
    </form>
	</body>
</html>