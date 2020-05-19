<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->post('/api/score-submission', function (Request $request, Response $response) {

		$scoreReporter = new Controllers_ScoreReporterController($this->db, $this->logger);
		$leaguesController = new Controllers_LeaguesController($this->db, $this->logger);
	
		$returnObj = array();
		
		$submissionFromRequest = $request->getParsedBody();
		
		try {

			$league = Models_League::withID($this->db, $this->logger, $submissionFromRequest['leagueId']);
			$team = Models_Team::withID($this->db, $this->logger, $submissionFromRequest['teamId']);

			if(isset($submissionFromRequest['dateId']) && $submissionFromRequest['dateId'] > 0) {
				$activeDate = Models_Date::withID($this->db, $this->logger, $submissionFromRequest['dateId']);
			} else {
				$activeDate = $leaguesController->getActiveDate($league);
			}
			
			$ignoreSubmission = $scoreReporter->checkSubmissionExists($team, $activeDate);

			$submissions = $scoreReporter->parseScoreSubmissionFromJson($submissionFromRequest, $league, $team, $ignoreSubmission, $activeDate);

			$this->logger->debug("Logging submissions: " . print_r($submissions, true));

			$scoreReporter->saveSubmissions($submissions, $league, $team, $ignoreSubmission);

			$returnObj["status"] = 1;

			return $response->withStatus(200)
				->withHeader('Content-Type', 'application/json')
				->write(json_encode($returnObj));
		} catch(Exception $e) {

			$this->logger->debug("Caught Exception: " . $e . "\n");
			
			$returnObj["status"] = 0;
			$returnObj["errorMessage"] = $e->getMessage();

			return $response->withStatus(400)
				->withHeader('Content-Type', 'application/json')
				->write(json_encode($returnObj));
		}
	});
	
?>