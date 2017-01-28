<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/score-reporter[/{sportID}[/{leagueID}[/{teamID}]]]', function (Request $request, Response $response) {

		$sportID = (int)$request->getAttribute('sportID');
		$leagueID = (int)$request->getAttribute('leagueID');
		$teamID = (int)$request->getAttribute('teamID');

		if($sportID == 1) {
			$logo='/Logos/ultimate_0.png';
		} elseif($sportID == 2) {
			$logo='/Logos/volleyball_0.png';
		} elseif($sportID == 3) {
			$logo='/Logos/football_0.png';
		} elseif($sportID == 4) {
			$logo= '/Logos/soccer_0.png';
		} else  {
			$logo = '/Logos/Perpetualmotionlogo.jpg';
		}

		$scoreReporter = new Controllers_ScoreReporterController($this->db, $this->logger);

		return $this->view->render($response, "score-reporter.phtml", [
				"request", $request,
				"sportID" => $sportID,
				"leagueID" => $leagueID,
				"teamID" => $teamID,
				"logo" => $logo,
				"leagues" => $scoreReporter->getLeagues($sportID)
			]
		);

		//return $response;
	});

	$app->get('/score-reporter-matches/{teamID}', function (Request $request, Response $response) {
		$teamID = (int)$request->getAttribute('teamID');

		if($teamID > 0) {
			$scoreReporter = new Controllers_ScoreReporterController($this->db, $this->logger);

			$team = $scoreReporter->getTeamById($teamID);

			if(isset($team) && $team->getId() != null) {
				$league = $scoreReporter->getLeagueById($team->getLeagueId());

				if(isset($league) && $league->getId() != null) {

					$matches = $scoreReporter->getMatches($team, $league);

					return $this->view->render($response, "score-reporter-matches.phtml", [
							"request", $request,
							"teamID" => $teamID,
							"team" => $team,
							"league" => $league,
							"matches" => $matches
						]
					);	
				}
			}
		}

		$response->getBody()->write("<div class='error'>Error: invalid Team ID.</div>");

		return $response;
	});

?>