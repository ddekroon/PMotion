
<?php 
	date_default_timezone_set ("America/Toronto");

	header('Content-type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename=pmotion-scheduled-matches.ics');
	
	include 'ics.php'; // Don't know if this is needed, or if semicolon is required either
	echo Includes_ICS::header_to_string();

	foreach(/*match*/) {
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
				'uid' => $curMatch->getId() . '@pmotionmatches', //so that our uid's don't match up with someone elses by accident. WILL NOT BE USED? - KYLE
				'description' => '' //TODO put link to venue in here.
			));

			echo $ics->to_string();
		}
	}
	
	echo Includes_ICS::footer_to_string();
?>
