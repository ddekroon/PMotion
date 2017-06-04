<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/score-reporter[/{sportID}[/{leagueID}[/{teamID}]]]', function(Request $request, Response $response) {

		$sport = Models_Sport::withID($this->db, $request->getAttribute('sportID'));
		$league = Models_League::withID($this->db, $request->getAttribute('leagueID'));
		$team = Models_Team::withID($this->db, $request->getAttribute('teamID'));

		if($sport->getId() == 1) {
			$logo='/Logos/ultimate_0.png';
		} elseif($sport->getId() == 2) {
			$logo='/Logos/volleyball_0.png';
		} elseif($sport->getId() == 3) {
			$logo='/Logos/football_0.png';
		} elseif($sport->getId() == 4) {
			$logo= '/Logos/soccer_0.png';
		} else  {
			$logo = '/Logos/Perpetualmotionlogo.jpg';
		}

		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
		
		if($league != null) {
			$leaguesController->setLeagueWeek($league);
		}

		return $this->view->render($response, "score-reporter.phtml", [
				"request", $request,
				"sport" => $sport,
				"league" => $league,
				"team" => $team,
				"logo" => $logo,
				"leagues" => $leaguesController->getLeaguesInScoreReporter($sport->getId())
			]
		);

		//return $response;
	});

	$app->get('/score-reporter-matches/{teamID}', function(Request $request, Response $response) {
		$teamID = (int)$request->getAttribute('teamID');

		if($teamID > 0) {
			$scoreReporter = new Controllers_ScoreReporterController($this->db, $this->logger);

			$team = Models_Team::withID($this->db, $teamID);

			if(isset($team) && $team->getId() != null) {

				if($team->getLeague() !== null) {

					$matches = $scoreReporter->getMatches($team, $team->getLeague());

					return $this->view->render($response, "score-reporter-matches.phtml", [
							"request", $request,
							"teamID" => $teamID,
							"team" => $team,
							"matches" => $matches
						]
					);	
				}
			}
		}

		$response->getBody()->write("<div class='error'>Error: invalid Team ID.</div>");

		return $response;
	});

	$app->post('/score-reporter/report-score', function(Request $request, Response $response) {

		$scoreReporter = new Controllers_ScoreReporterController($this->db, $this->logger);
		
		$returnObj = array();
		
		$allPostPutVars = $request->getParsedBody();
		$sportID = $allPostPutVars['sportID'];
		$leagueID = $allPostPutVars['leagueID'];
		$teamID = $allPostPutVars['teamID'];
		
		try {
			$scoreReporter->saveFromRequest($request);
			$returnObj["status"] = 1;
			$returnObj["html"] = $this->view->render($response, "score-reporter-reported-score.phtml", [
					"request", $request,
					"sportID" => $sportID,
					"leagueID" => $leagueID,
					"teamID" => $teamID
				]
			);
		} catch(Exception $e) {
			$this->logger->debug("Caught Exception: " . $e . "\n");
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();
		}

		$response->getBody()->write(json_encode($returnObj));

		return $response;
	});
	
?>