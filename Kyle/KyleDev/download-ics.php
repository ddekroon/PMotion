
<?php 
	date_default_timezone_set ("America/Toronto");

	/* For accessing functions to get team and match info - remove the temp later when moved to allSports folder */
	$temp = realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'allSports'.DIRECTORY_SEPARATOR;
	require_once($temp.'includeFiles/tmpgFormFunctions2.php');

	header('Content-type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename=pmotion-scheduled-matches.ics');
	
	include 'ics.php'; // Don't know if this is needed, or if semicolon is required either
	echo Includes_ICS::header_to_string();

	$data = getTeamsData($teamID, 0);

	$teamObjs = $data['teamObjs'];
	// $leagueID = $data['leagueID']; // Don't know if this is needed
	$matchObj = getMatchData($teamObjs, $teamID);

	if($matchObj != null) {
		foreach($matchObj as $matchDay) {
			foreach($matchDay as $match) {
				$teamName = '';
				$oppTeam = '';
				$date = '';
				$startTime = '';
				$endTime = '';
				if($sportID!=2) {$shirtColour = $matchObj->matchShirtColour};
			}
		}
	}

	foreach(/*match - find variable/class for this*/) {
		$ics = new ics(array(
			'location' => '',
			'summary' => '',
			'dtstart' => '',
			'dtend' => '',
			'url' => '',
			'uid' => '' . '@pmotionmatches', // I think this needs to be set
			'description' => '' // TODO: Put link to venue in here (for map)
		));

		echo $ics->to_string();
	}

	echo Includes_ICS::footer_to_string();

	/* ----------------------------- */ 
	// All below is for web-app teams
	foreach($teams as $curTeam) {
		foreach($curTeam->getScheduledMatches() as $curMatch) {
			$matchDate = $curMatch->getDate();
			
			$oppTeam = $curMatch->getOppTeam($curTeam);
			
			$basePath = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
			
			$startDateObj = DateTime::createFromFormat("l, F jS g:i a", $matchDate->getDescription() . ' ' . $curMatch->getMatchTimeFormatted());
			$startDateObj->setTimezone(new DateTimeZone("UTC"));
			
			$endDateObj = clone $startDateObj;
			$endDateObj->add(new DateInterval('PT' . "60" . "M"));
			
			$ics = new Includes_ICS(array(
				'location' => $curMatch->getVenue()->getName(),
				'summary' => $curMatch->getLeague()->getSport()->getName() . (isset($oppTeam) && $oppTeam != null ? ' vs ' . $oppTeam->getName() . ' ' . $oppTeam->getFormattedStandings() : ''),
				'dtstart' => $startDateObj,
				'dtend' => $endDateObj,
				'url' => $basePath . $router->pathFor('schedule', ['leagueID' => $curTeam->getLeagueId()]),
				'uid' => $curMatch->getId() . '@pmotionmatches', //so that our uid's don't match up with someone elses by accident. // WILL NOT BE USED? - KYLE
				'description' => '' //TODO put link to venue in here.
			));

			echo $ics->to_string();
		}
	}
	
	echo Includes_ICS::footer_to_string();
?>
