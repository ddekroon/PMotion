<?php
session_start();
$control = 1;
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'security.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'connect.php');
require_once(realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'GlobalFiles'.DIRECTORY_SEPARATOR.'tableNames.php');

$queryString = $_POST['queryString'];
$dbName = mysql_escape_string($_POST['dbName']);
print($queryString."\n"); 


if(strlen($queryString) > 10) {
    $query = mysql_query("$queryString") or die('ERROR - '.mysql_error());
    if(isset($_POST['selectTrue'])) {
        $dbFieldQuery = mysql_query("SHOW COLUMNS FROM $dbName");
        if (!$dbFieldQuery) {
            echo 'Could not run query: ' . mysql_error();
        }
        $fieldnames=array();
        if (mysql_num_rows($dbFieldQuery) > 0) {
            $i=0;
            while ($row = mysql_fetch_assoc($dbFieldQuery)) {
                $fieldnames[$i] =  $row['Field'];
                $i++;
            }
        } else {
            print 'No available columns';
            exit(0);
        }
        $i=0;
        print '<table style="text-align:center;"><tr>';
		foreach($fieldnames as $field) {
			print '<td>'.$field.'</td>';
		}
		print '</tr>';
        while($queryString = mysql_fetch_array($query)) {
            print '<tr>';
            foreach($fieldnames as $field) {
                print '<td>'.$queryString["$field"].'</td>';
            }
            print '</tr>';
        }
        print '</table>';
    } else {
        print 'Deed done';
    }
}
?>

<form id="queryForm" method="post" action=<?php print $_SERVER['PHP_SELF']?>>
<TABLE ALIGN=CENTER class='master'>
    <TR>
        <TD ALIGN=CENTER colspan="4">
            <input type="text" name="dbName" style="width:800px"/>
        </TD>
    </tr>
    <tr>
    	<TD ALIGN=CENTER>
            <input type="text" name="queryString" style="width:800px"/>
        </TD><td>
            <input type="checkbox" name="selectTrue" value="1">
        </td><td>
            <button style="font-size:30px;" name="queryButton" value=1>Submit</button>
        </td>
    </TR>
</TABLE>
</form>
