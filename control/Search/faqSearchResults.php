<?php /*****************************************
File: searchResults.php
Creator: Derek Dekroon
Created: July 16/2013
Program used to check the FAQ versus a string.
******************************************/

require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.'Global'.
	DIRECTORY_SEPARATOR.'class_container.php'); 
$container = new Container('FAQ Search');

function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//mysql_query("ALTER TABLE $faqTable ADD FULLTEXT(faq_title, faq_body)") or die('ERROR - '.mysql_error());

$RESULTS_LIMIT=10;

if(isset($_POST['searchString']) || isset($_GET['search_term'])) {
    if(($search_term = $_POST['searchString']) == '') {
		$search_term = $_GET['search_term'];
	}
	$search_term = mysql_escape_string($search_term);
	
    if(!isset($_GET['first_pos'])) {
        $first_pos = "0";
    } else {
		$first_pos = $_GET['first_pos'];
	}
	
    $start_search = getmicrotime();
	if(strcasecmp($search_term, 'all') != 0) {
		$sql_query = mysql_query("SELECT * FROM $faqTable WHERE MATCH(faq_title, faq_body) AGAINST('$search_term')") 
			or die('ERROR getting data - '.mysql_error());
		if($results = mysql_num_rows($sql_query) != 0) {
			$sql =  "SELECT * FROM $faqTable WHERE MATCH(faq_title, faq_body) AGAINST('$search_term') LIMIT $first_pos, 
				$RESULTS_LIMIT";
			$sql_result_query = mysql_query($sql);   
		} else {
			$sql = "SELECT * FROM $faqTable WHERE (faq_title LIKE '%".mysql_real_escape_string($search_term)."%' 
				OR faq_body LIKE '%".mysql_real_escape_string($search_term)."%') ";
			$sql_query = mysql_query($sql) or die('error getting query - '.mysql_error());
			$results = mysql_num_rows($sql_query);
			$sql_result_query = mysql_query("SELECT * FROM $faqTable WHERE (faq_title LIKE '%".$search_term."%' 
				OR faq_body LIKE '%".$search_term."%') LIMIT $first_pos, $RESULTS_LIMIT ");
		}
	} else {
		$sql_query = mysql_query("SELECT * FROM $faqTable") 
			or die('ERROR getting all FAQ - '.mysql_error());
		$results = mysql_num_rows($sql_query);
		$sql_result_query = mysql_query("SELECT * FROM $faqTable LIMIT $first_pos, $RESULTS_LIMIT") 
			or die('ERROR getting all FAQ - '.mysql_error());
	}
	
    $stop_search = getmicrotime();
    $time_search = ($stop_search - $start_search);
}?>
<input type='hidden' name="faqSearchString" value="<?php print htmlentities($search_term, ENT_QUOTES) ?>" />
<h1>FAQ Search Results</h1>

<?php if($results != 0) { ?> 
<div class='tableData'></div>
<div class='col50 t-left'>
	Results for <?php echo "<i><b><font color=#000000>".htmlentities($search_term, ENT_QUOTES)."</font></b></i> "; ?>
</div><div class='col50 t-right'>Results <b>
	<?php echo ($first_pos+1)." - ";
	if(($RESULTS_LIMIT + $first_pos) < $results) echo ($RESULTS_LIMIT + $first_pos);
	else echo $results ; ?>
	</b>
	out of <b><?php echo $results; ?></b>
	for(<b><?php echo sprintf("%01.2f", $time_search); ?></b>)
	seconds 
</div><div class='tableData'></div>
<div class='tableData'>
			<table>
				<tr>
					<th colspan=3>
						Results
					</th>
				</tr><tr>
					<td>
						#
					</td><td>
						Title
					</td><td>
						Description
					</td>
				</tr>
	<?php $counter = 1;
	while($row = mysql_fetch_array($sql_result_query)) { 
		$faqID = $row['faq_id'];
		$faqTitle = $row['faq_title'];?>
		<tr>
			<td>
				<?php print $counter++ ?>
			</td><td>
				<?php print "<a href='faqNode.php?faqID=$faqID'>".htmlentities($faqTitle, ENT_QUOTES).'</a>'; ?>
			</td><td style="width:65%;">
				<?php print substr(htmlentities($row['faq_body'], ENT_QUOTES), 0, 80).' ...'; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div><div class='tableData'>
			
	<?php //displaying the number of pages where the results are sittuated
	
	if($results > $RESULTS_LIMIT) { ?>
		<tr>
			<td style="text-align:left;">
	<?php if($first_pos > 0) {
		$back=$first_pos-$RESULTS_LIMIT;
		if($back < 0) {
			$back = 0;
		}
		echo "<a href='search.php?search_term=".stripslashes($search_term)."&first_pos=$back' ></a>";
	}
	if($results > $RESULTS_LIMIT) {
		$sites=intval($results/$RESULTS_LIMIT);
		if($results % $RESULTS_LIMIT) {
			$sites++;
		}
	}
	for ($i=1;$i<=$sites;$i++) {
		$fwd = ($i-1) * $RESULTS_LIMIT;
		if($fwd == $first_pos) {
		  echo "<a href='faqSearchResults.php?search_term=".stripslashes($search_term)."&first_pos=$fwd '><b>$i</b></a> | ";
		} else {
		  echo "<a href='faqSearchResults.php?search_term=".stripslashes($search_term)."&first_pos=$fwd '>$i</a> | ";   
		}
	}
	if(isset($first_pos) && $first_pos < $results-$RESULTS_LIMIT) {
		$fwd=$first_pos+$RESULTS_LIMIT;
		echo "<a href='faqSearchResults.php?search_term=".stripslashes($search_term)."&first_pos=$fwd ' > >></a>";
		$fwd=$results-$RESULTS_LIMIT;
	} ?>
	<?php } ?>
</div>
<?php } elseif($sql_query) { ?>
	<div class='tableData'>
		No results for <?php echo "<i><b><font color=#000000>".htmlentities($search_term, ENT_QUOTES)."</font></b></i> "; ?>
	</div>
<?php } ?>
