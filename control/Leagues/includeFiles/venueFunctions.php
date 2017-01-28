<?php function updateVenue($venueName, $venueShortMatchName, $sportID, $venueLink, $venueID = 0) {
	global $venuesTable, $container;
	
	if(strlen($venueName) < 5) {
		print 'Venue name must be over 5 characters<br />';
		return 0;
	} else {
		$venueName = mysql_escape_string($venueName);
	}
	
	if(strlen($venueShortMatchName) < 5) {
		print 'Short venue matching name must be over 5 characters<br />';
		return 0;
	} else {
		$venueShortMatchName = mysql_escape_string($venueShortMatchName);
	}
	
	if($sportID <= 0 || $sportID >= 10) {
		print 'Invalid sport id<br />';
		return 0;
	}
	if(strlen($venueLink) < 5) {
		print 'Venue link name must be over 5 characters<br />';
		return 0;
	} else {
		$venueLink = mysql_escape_string($venueLink);
	}
	if($venueID == 0) {
		$queryString = "INSERT INTO $venuesTable (venue_name, venue_short_show_name, venue_short_match_name, 
			venue_sport_id, venue_link) VALUES ('$venueName', '$venueName', '$venueShortMatchName', $sportID, 
			'$venueLink')";
	} else {
		$queryString = "UPDATE $venuesTable SET venue_name = '$venueName', venue_short_show_name = '$venueName', 
			venue_short_match_name = '$venueShortMatchName', venue_sport_id = $sportID, venue_link = '$venueLink' 
			WHERE venue_id = $venueID";
	}
	//print $queryString;
	mysql_query($queryString) or die('Error updating venue - '.mysql_error());
	if($venueID == 0) {
		$container->printSuccess('Successfully added venue');
	} else {
		$container->printSuccess('Venue successfully updated');
	}
}

function getVenueData($venueID) {
	global $venuesTable, $venueName, $venueShortName, $venueLink;
	$venueQuery = mysql_query("SELECT * FROM $venuesTable WHERE venue_id = $venueID")
		or die('ERROR getting edit venue data - '.mysql_error());
	$venueArray = mysql_fetch_array($venueQuery);
	$venueName = $venueArray['venue_name'];
	$venueShortName = $venueArray['venue_short_match_name'];
	$venueLink = $venueArray['venue_link'];
}

function getVenueDD($sportID, $edtVenueID) {
	global $venuesTable;
	$venuesDropDown = '<option value=0>-- Venue --</option>';
	
	$venuesQuery=mysql_query("SELECT * FROM $venuesTable WHERE venue_sport_id = $sportID ORDER BY venue_name ASC") 
		or die("ERROR getting venues drop down ".mysql_error());
	while($venue = mysql_fetch_array($venuesQuery)) {
		if($venue['venue_id'] == $edtVenueID){
			$venuesDropDown.="<option selected value= $venue[venue_id]>$venue[venue_name]</option><BR>";
		}else{
			$venuesDropDown.="<option value= $venue[venue_id]>$venue[venue_name]</option>";
		}
	}
	return $venuesDropDown;
}