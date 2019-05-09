<?php

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/api/lookups', function (Request $request, Response $response) {

		$sportsController = new Controllers_SportsController($this->db, $this->logger);
		$leaguesConteroller = new Controllers_LeaguesController($this->db, $this->logger);
		$seasonsController = new Controllers_SeasonsController($this->db, $this->logger);

		$returnObj = array();

		$returnObj["sports"] = $sportsController->getSports();
		$returnObj["seasonsAvailableRegistration"] = $seasonsController->getSeasonsAvailableForRegistration();
		$seasonsAvailableScoreReporter = $seasonsController->getSeasonsAvailableForScoreReporter();

		foreach($seasonsAvailableScoreReporter as $curSeason)
		{
			$curSeason->getLeagues();
		}

		$returnObj["seasonsAvailableScoreReporter"] = $seasonsAvailableScoreReporter;

		return $response->withStatus(200)
        	->withHeader('Content-Type', 'application/json')
			->write(json_encode($returnObj));
	});
?>